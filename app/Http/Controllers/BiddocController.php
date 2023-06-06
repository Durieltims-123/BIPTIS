<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\{APP,Procact};
use Validator;
use PhpOffice\PhpWord\TemplateProcessor;

class BiddocController extends Controller
{

  public function getReleaseBidDocs()
  {
    $year=date('Y');
    $released_bid_docs=DB::table('bid_doc_projects')
    ->where('bid_docs.date_released','like',$year.'%')
    ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
    ->join('procacts','bid_doc_projects.procact_id','procacts.procact_id')
    ->join('project_plans','procacts.plan_id','project_plans.plan_id')
    ->join('project_timelines','procacts.procact_id','project_timelines.procact_id')
    ->join('contractors','contractors.contractor_id','bid_docs.contractor_id')
    ->join('funds','funds.fund_id','project_plans.fund_id')
    ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
    ->join('municipalities','project_plans.municipality_id','municipalities.municipality_id')->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();

    return view('admin.release_bid_docs',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>'Release Bid Docs','year'=>$year,'released_bid_docs'=>$released_bid_docs]);
  }

  public function getRecieveBidDocs()
  {
    $year=date('Y');
    $released_bid_docs=DB::table('bid_doc_projects')
    // ->where('project_plans.project_year',$year)
    ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
    ->join('procacts','bid_doc_projects.procact_id','procacts.procact_id')
    // ->join('project_plans','procacts.procact_id','project_plans.latest_procact_id')
    ->join('project_timelines','procacts.procact_id','project_timelines.procact_id')
    ->join('project_plans','procacts.plan_id','project_plans.plan_id')
    ->join('contractors','contractors.contractor_id','bid_docs.contractor_id')
    ->join('funds','funds.fund_id','project_plans.fund_id')
    ->leftJoin('project_bidders','project_bidders.bid_doc_project_id','bid_doc_projects.bid_doc_project_id')
    ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
    ->join('municipalities','project_plans.municipality_id','municipalities.municipality_id')->get();

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view('admin.receive_bid_docs',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>'Receive Bid Docs','year'=>$year,'released_bid_docs'=>$released_bid_docs]);
  }

  public function submitReleaseBidDoc(Request $request)
  {
    $data=$request->validate([
      "plan_title"=>"required",
      "contractor"=>"required",
      "date_released"=>"required|before:tomorrow",
      "control_number"=>"required"
    ]);

    $plan_id=$request->input('plan_id');
    $contractor_id=$request->input('contractor_id');
    $APP = new APP;
    $latest_procact_ids=[];
    $bid_opened=false;

    // Check if clustered
    $plan=DB::table('project_plans')->where('plan_id',$plan_id)->first();
    if($plan->current_cluster==null){
      $cluster_id=null;
      $latest_activity_statuses=DB::table("project_activity_status")->select('project_activity_status.open_bid')->where("procact_id",$plan->latest_procact_id)->orderBy('pro_act_stat_id','desc')->distinct()->get();
      $latest_procacts=DB::table("procacts")->where("procact_id",$plan->latest_procact_id)->join('project_plans','project_plans.plan_id','procacts.plan_id')->orderBy('procact_id','desc')->get();
      $total_project_cost=$plan->project_cost;
    }
    else{
      $cluster_id=$plan->current_cluster;
      $plans=DB::table('project_plans')->where('current_cluster',$plan->current_cluster)->get();
      $latest_procacts=DB::table('project_plans')->where('project_plans.current_cluster',$cluster_id)->join('procacts','project_plans.latest_procact_id','procacts.procact_id')->get();
      // $latest_activity_statuses=DB::table('project_plans')->where('project_plans.current_cluster',$cluster_id)
      // ->select('project_activity_status.open_bid')
      // ->join('procacts','project_plans.latest_procact_id','procacts.procact_id')
      // ->join('project_activity_status','project_activity_status.procact_id','procacts.procact_id')
      // ->distinct()->get();
      $total_project_cost=DB::table('project_plans')->where('current_cluster',$plan->current_cluster)->sum('project_cost');

    }

    // if(count($latest_activity_statuses)==1 && $latest_activity_statuses[0]->open_bid=='pending'){
    //   $bid_opened=false;
    // }
    // else{
    //   $bid_opened=false;
    // }

    $fees=$APP->computeFee($total_project_cost);

    foreach ($latest_procacts as $latest_procact) {
      array_push($latest_procact_ids,$latest_procact->procact_id);
    }

    if($request->input('bid_doc_id')!=null){

      $bid_doc_id=$request->input('bid_doc_id');

      $duplicate=DB::table('bid_docs')->where([['bid_docs.contractor_id',$contractor_id],['bid_docs.bid_doc_id','<>',$bid_doc_id]])->whereIn('bid_doc_projects.procact_id',$latest_procact_ids)
      ->join('bid_doc_projects','bid_doc_projects.bid_doc_id','bid_docs.bid_doc_id')->count();
      $duplicate_cn=DB::table('bid_docs')->where([['control_number',$request->input('control_number')],['bid_docs.bid_doc_id','<>',$bid_doc_id]])->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else if($duplicate_cn>0){
        $message="duplicate_control_number";
      }

      else if($bid_opened){
        $message="bid_opening_done";
      }
      else{
        $update=DB::table('bid_docs')->where('bid_doc_id',$bid_doc_id)->update([
          'contractor_id'=>$contractor_id,
          'date_released'=>date("Y-m-d", strtotime($request->input('date_released'))),
          'control_number'=>$request->input('control_number'),
          'fees'=>$fees,
          'updated_at' => now()
        ]);

        $message="success";
      }
    }
    else{
      // check duplicate
      $duplicate=DB::table('bid_docs')->where([['bid_docs.contractor_id',$contractor_id]])->whereIn('bid_doc_projects.procact_id',$latest_procact_ids)
      ->join('bid_doc_projects','bid_doc_projects.bid_doc_id','bid_docs.bid_doc_id')->count();
      $duplicate_cn=DB::table('bid_docs')->where('control_number',$request->input('control_number'))->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else if($duplicate_cn>0){
        $message="duplicate_control_number";
      }

      else if($bid_opened){
        $message="bid_opening_done";
      }
      else{
        $insert=DB::table('bid_docs')->insert([
          'contractor_id'=>$contractor_id,
          'date_released'=>date("Y-m-d", strtotime($request->input('date_released'))),
          'control_number'=>$request->input('control_number'),
          'fees'=>$fees,
          'created_at' => now(),
          'updated_at' => now()
        ]);

        $latest_bid_doc=DB::table('bid_docs')->latest()->first();
        foreach ($latest_procacts as $latest_procact) {
          DB::table('bid_doc_projects')->insert([
            'bid_doc_id'=>$latest_bid_doc->bid_doc_id,
            'procact_id'=>$latest_procact->procact_id,
            'created_at' => now(),
            'updated_at' => now()
          ]);
        }
        $message='success';
      }
    }
    return redirect()->back()->with('message',$message);
  }


  public function deleteBidDoc($id)
  {
    $bid_doc=DB::table('bid_docs')->where('bid_doc_id',$id)->first();

    if($bid_doc->date_received!=null){
      $message="delete_error";
    }
    else{
      DB::table('bid_doc_projects')->where('bid_doc_id',$id)->delete();
      DB::table('bid_docs')->where('bid_doc_id',$id)->delete();
      $message='delete_success';
    }
    return redirect()->back()->with('message',$message);

  }


  public function filterBidDoc(Request $request)
  {
    $data=$request->validate([
      "year"=>'nullable|digits:4|integer|min:2020|max:'.(date('Y')),
    ]);

    $year=$request->input('year');

    $released_bid_docs=DB::table('bid_doc_projects')
    ->where('bid_docs.date_released','like',$year.'%')
    ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
    ->join('procacts','bid_doc_projects.procact_id','procacts.procact_id')
    ->join('project_plans','procacts.plan_id','project_plans.plan_id')
    ->join('project_timelines','procacts.procact_id','project_timelines.procact_id')
    ->join('contractors','contractors.contractor_id','bid_docs.contractor_id')
    ->join('funds','funds.fund_id','project_plans.fund_id')
    ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
    ->join('municipalities','project_plans.municipality_id','municipalities.municipality_id')->get();

    return back()->withInput()->with('released_bid_docs',$released_bid_docs);

  }

  public function submitReceiveBidDoc(Request $request)
  {

    $data=$request->validate([
      "date_received"=>"required|before:tomorrow|after_or_equal:date_released",
      "time_received"=>"required"
    ]);

    $status='active';
    $bid_doc_id=$request->input('bid_doc_id');
    $latest_procact_ids=[];

    $bid_doc=DB::table('bid_docs')->where('bid_doc_id',$bid_doc_id)->first();
    $latest_procacts=DB::table('bid_doc_projects')->where('bid_docs.bid_doc_id',$bid_doc_id)->join('bid_docs','bid_doc_projects.bid_doc_id','bid_docs.bid_doc_id')->get();
    foreach ($latest_procacts as $latest_procact) {
      array_push($latest_procact_ids,$latest_procact->procact_id);
    }

    $proposed_bid=$request->input('proposed_bid');
    $date_received=$request->input('date_received');
    $time_received=$request->input('time_received');
    $bid_doc_id=$request->input('bid_doc_id');


    $latest_activity_statuses=DB::table('bid_doc_projects')->where('bid_doc_projects.bid_doc_id',$bid_doc_id)
    ->select('project_activity_status.bid_evaluation')
    ->join('procacts','bid_doc_projects.procact_id','procacts.procact_id')
    ->join('project_activity_status','project_activity_status.procact_id','procacts.procact_id')
    ->distinct()->get();

    $latest_timeline=DB::table('bid_doc_projects')->where('bid_doc_projects.bid_doc_id',$bid_doc_id)
    ->select('project_timelines.*')
    ->join('procacts','bid_doc_projects.procact_id','procacts.procact_id')
    ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
    ->first();

    // if(strtotime($latest_timeline->bid_submission_start." 08:30") < strtotime(date("Y-m-d",strtotime($date_received))." ".$time_received)){
    //   $status="late";
    // }

    $latest_activity_statuses2=DB::table('bid_doc_projects')->where('bid_doc_projects.bid_doc_id',$bid_doc_id)
    ->select('project_activity_status.post_qual')
    ->join('procacts','bid_doc_projects.procact_id','procacts.procact_id')
    ->join('project_activity_status','project_activity_status.procact_id','procacts.procact_id')
    ->distinct()->get();


    if(count($latest_activity_statuses)==1 && $latest_activity_statuses[0]->bid_evaluation=='pending'){
      $bid_opened=false;
    }
    else{
      $bid_opened=true;
    }

    if(count($latest_activity_statuses2)==1 && $latest_activity_statuses2[0]->post_qual=='pending'){
      $bid_done=false;
    }
    else{
      $bid_done=true;
    }


    $existing=DB::table('project_bidders')->where('bid_doc_projects.bid_doc_id',$bid_doc_id)
    ->join('bid_doc_projects','bid_doc_projects.bid_doc_project_id','project_bidders.bid_doc_project_id')->first();

    if($existing!=null){
      $status=$existing->bid_status;
      if($bid_done===true){
        $message="update_error";
      }
      else{
        $update=DB::table('bid_docs')->where('bid_doc_id',$bid_doc_id)->update([
          'date_received'=>date("Y-m-d",strtotime($date_received)),
          'time_received'=>$time_received,
          'updated_at'=> now()
        ]);

        $update=DB::table('project_bidders')->where('bid_doc_projects.bid_doc_id',$bid_doc_id)->join('bid_doc_projects','project_bidders.bid_doc_project_id','bid_doc_projects.bid_doc_project_id')->update([
          "bid_status"=>$status,
          'project_bidders.updated_at' => now()
        ]);

        $message="success";

      }

    }
    else{

      $update=DB::table('bid_docs')->where('bid_doc_id',$bid_doc_id)->update([
        'date_received'=>date("Y-m-d",strtotime($date_received)),
        'time_received'=>$time_received,
        'updated_at' => now()
      ]);

      foreach ($latest_procact_ids as $latest_procact_id) {
        $temp=DB::table('bid_doc_projects')->where([['bid_doc_projects.procact_id',$latest_procact_id],['bid_docs.bid_doc_id',$bid_doc_id]])
        ->join('bid_docs','bid_doc_projects.bid_doc_id','bid_docs.bid_doc_id')->first();
        $insert=DB::table('project_bidders')->insert([
          'bid_doc_project_id'=>$temp->bid_doc_project_id,
          'bid_status'=>$status,
          'created_at' => now(),
          'updated_at' => now()
        ]);
      }

      $message="success";

    }


    $strtime_received=date("Y-m-d",strtotime($date_received))." ".$time_received;

    foreach ($latest_procact_ids as $latest_procact_id) {
      $Procact=Procact::find($latest_procact_id);
      $opening=$Procact->open_bid." 08:30";
      $temp=DB::table('bid_doc_projects')->where([['bid_doc_projects.procact_id',$latest_procact_id],['bid_docs.bid_doc_id',$bid_doc_id]])
      ->join('bid_docs','bid_doc_projects.bid_doc_id','bid_docs.bid_doc_id')->first();
      $project_bidder=DB::table('project_bidders')->where('bid_doc_project_id',$temp->bid_doc_project_id)->first();

      if(strtotime($strtime_received)>strtotime($opening)){
        if($project_bidder->bid_status==="active"){
          $edit=DB::table('project_bidders')->where('bid_doc_project_id',$temp->bid_doc_project_id)->update(["bid_status"=>"disqualified"]);
          DB::table('disqualification_records')->insert([
            'project_bid'	=>$project_bidder->project_bid,
            'remarks'	=>'Disqualified: The bidder submitted their Bidding Documents beyond the prescribed time (System Generated).',
            'user_id'	=>Auth::user()->id,
            'created_at'	=>now(),
            'updated_at' =>now()
          ]);
          $message="success_automatic_disqual";
        }
      }
      else{
        if($project_bidder->bid_status==="disqualified"){
          $automatically_disqualified=DB::table('disqualification_records')->where([['project_bid',$project_bidder->project_bid],
          ['remarks','like','Disqualified:%'],
          ['remarks','like','%System Generated%'],
          ])->orderbY('record_id','desc')->first();
          $latest_records=DB::table('disqualification_records')->where('project_bid',$project_bidder->project_bid)->orderbY('record_id','desc')->first();
          if($automatically_disqualified->record_id==$latest_records->record_id){
            $edit=DB::table('project_bidders')->where('bid_doc_project_id',$temp->bid_doc_project_id)->update(["bid_status"=>"active"]);
          }
        }
      }
    }


    return redirect()->back()->withInput()->with('message',$message);
  }

  public function generateOrderOfPayment($id)
  {
    $bid_doc=DB::table('bid_docs')
    ->select(DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid) AS minimum_cost"),"bid_docs.*",'contractors.*')
    ->where('bid_doc_id',$id)
    ->join('contractors','contractors.contractor_id','bid_docs.contractor_id')
    ->first();


    if($bid_doc==null){
      return abort('403','Unknown Bid Document');
    }
    else{
      $project_plans=DB::table('bid_doc_projects')
      ->where('bid_doc_projects.bid_doc_id',$id)
      ->join('procacts','procacts.procact_id','bid_doc_projects.procact_id')
      ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
      ->join('project_plans','procacts.plan_id','project_plans.plan_id')
      ->join('funds','project_plans.fund_id','funds.fund_id')
      ->leftJoin('barangays','barangays.barangay_id','project_plans.barangay_id')
      ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
      ->orderBy('procacts.itb_arrangement','asc')
      ->get();

      $project_numbers="";
      $project_titles="";
      $location="";
      $sources="";
      $date_opening="";
      $total_fee=0.00;
      $release_person="";
      $project_cost="";
      $total_cost=0;
      $letter="A";
      $initial_location=$project_plans[0]->barangay_id;
      $is_same_location=true;

      $user_id=Auth::user()->id;
      $user=DB::table('users')->where("id",$user_id)->first();


      foreach ($project_plans as $project_plan) {
        if(count($project_plans)>1){
          $project_numbers=$project_numbers.$letter.") ".$project_plan->project_no."; ";
          $project_titles=$project_titles.$letter.") ".$project_plan->project_title."; ";
          $sources=$sources.$letter.") ".$project_plan->source."; ";
          $project_cost=$project_cost.$letter.") Php ".number_format($project_plan->project_cost,2,'.',',')." + ";
          $total_cost=$total_cost+(float)$project_plan->project_cost;
          $letter = ++$letter;
          if($project_plan->barangay_id!=$initial_location){
            $is_same_location=false;
          }
        }
        else{
          $project_numbers=$project_plan->project_no;
          $project_titles=$project_plan->project_title;
          $sources=$project_plan->source;
          $project_cost="Php ".number_format($project_plan->project_cost,2,'.',',');
        }
      }

      if($project_plans[0]->barangay_id===null||$is_same_location===false){
        $location=$project_plans[0]->municipality_name;
      }
      else{
        $location=$project_plans[0]->barangay_name.', '.$project_plans[0]->municipality_name;
      }

      if($total_cost>0){
        $project_cost=preg_replace('/.*\K' . preg_quote('+', '/') . '/i','=',$project_cost);
        $project_cost=$project_cost.'Php '.number_format($total_cost,2,'.',',');
      }
      $project_titles=strtoupper(strtolower($project_titles));
      $project_titles=htmlspecialchars($project_titles);
      $business_name= strtoupper(strtolower($bid_doc->business_name));
      $business_name=htmlspecialchars($business_name);
      $filename='OrderOfPayment'.md5(date('Y-m-d H:i:s:u')).".docx";
      $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/OOP.docx");
      $templateProcessor->setValue('project_number', strtoupper(strtolower($project_numbers)));
      $templateProcessor->setValue('contractor',$business_name);
      $templateProcessor->setValue('project_title', $project_titles);
      $templateProcessor->setValue('location', $location);
      $templateProcessor->setValue('project_cost', $project_cost);
      $templateProcessor->setValue('source', strtoupper(strtolower($sources)));
      $templateProcessor->setValue('date_of_opening', date("F d, Y", strtotime($project_plans[0]->bid_submission_start)));
      $templateProcessor->setValue('owner', strtoupper(strtolower($bid_doc->owner)));
      $templateProcessor->setValue('bid_fee', number_format($bid_doc->fees,2,'.',','));
      $templateProcessor->setValue('total_fee', number_format(($bid_doc->fees+50.00),2,'.',','));
      $templateProcessor->setValue('date_released', date("F d, Y", strtotime($bid_doc->date_released)));
      // $templateProcessor->setValue('release_person', strtoupper(strtolower($user->name)));


      $templateProcessor->saveAs(public_path().'\\'.'word_results/'.$filename);
      return  response()->download(public_path().'\\'.'word_results/'.$filename)->deleteFileAfterSend(true);
    }
  }





}
