<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\{APP,Procact,Rfq,ProjectBidder};
use Validator;

class RFQController extends Controller
{
  public function getReleaseRFQ()
  {

    $year=date('Y');
    $released_rfqs=DB::table('rfq_projects')
    ->where('rfqs.date_released','like',$year.'%')
    ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
    ->join('procacts','rfq_projects.procact_id','procacts.procact_id')
    ->join('project_plans','procacts.procact_id','project_plans.latest_procact_id')
    ->join('project_timelines','procacts.procact_id','project_timelines.procact_id')
    ->join('contractors','contractors.contractor_id','rfqs.contractor_id')
    ->join('funds','funds.fund_id','project_plans.fund_id')
    ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
    ->join('municipalities','project_plans.municipality_id','municipalities.municipality_id')
    ->get();

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view('admin.release_rfq',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>'Release RFQ','year'=>$year,'released_rfqs'=>$released_rfqs]);
  }

  public function getRecieveRFQ()
  {
    $year=date('Y');
    $released_rfqs=DB::table('rfq_projects')
    ->where('rfqs.date_released','like',$year.'%')
    ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
    ->join('procacts','rfq_projects.procact_id','procacts.procact_id')
    ->join('project_plans','procacts.procact_id','project_plans.latest_procact_id')
    ->join('project_timelines','procacts.procact_id','project_timelines.procact_id')
    ->join('contractors','contractors.contractor_id','rfqs.contractor_id')
    ->join('funds','funds.fund_id','project_plans.fund_id')
    ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
    ->join('municipalities','project_plans.municipality_id','municipalities.municipality_id')
    ->get();

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view('admin.receive_rfq',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>'Receive RFQ','year'=>$year,'released_rfqs'=>$released_rfqs]);
  }
  public function submitReleaseRFQ(Request $request)
  {
    $data=$request->validate([
      "plan_title"=>"required",
      "contractor"=>"required",
      "date_released"=>"required|before:tomorrow|before_or_equal:opening_date"
    ]);

    $plan_id=$request->input('plan_id');
    $contractor_id=$request->input('contractor_id');
    $latest_procact_ids=[];

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
      $latest_activity_statuses=DB::table('project_plans')->where('project_plans.current_cluster',$cluster_id)
      ->select('project_activity_status.open_bid')
      ->join('procacts','project_plans.latest_procact_id','procacts.procact_id')
      ->join('project_activity_status','project_activity_status.procact_id','procacts.procact_id')
      ->distinct()->get();
      $total_project_cost=DB::table('project_plans')->where('current_cluster',$plan->current_cluster)->sum('project_cost');

    }

    if(count($latest_activity_statuses)==1 && $latest_activity_statuses[0]->open_bid=='pending'){
      $bid_opened=false;
    }
    else{
      $bid_opened=true;
    }

    foreach ($latest_procacts as $latest_procact) {
      array_push($latest_procact_ids,$latest_procact->procact_id);
    }

    if($request->input('rfq_id')!=null){

      $rfq_id=$request->input('rfq_id');

      $duplicate=DB::table('rfqs')->where([['rfqs.contractor_id',$contractor_id],['rfqs.rfq_id','<>',$rfq_id]])->whereIn('rfq_projects.procact_id',$latest_procact_ids)
      ->join('rfq_projects','rfq_projects.rfq_id','rfqs.rfq_id')->count();

      if($duplicate>0){
        $message="duplicate";
      }

      // else if($bid_opened){
      //   $message="bid_opening_done";
      // }
      else{
        $update=DB::table('rfqs')->where('rfq_id',$rfq_id)->update([
          'contractor_id'=>$contractor_id,
          'date_released'=>date("Y-m-d", strtotime($request->input('date_released'))),
          'updated_at' => now()
        ]);

        $message="success";
      }
    }
    else{
      // check duplicate
      $duplicate=DB::table('rfqs')->where([['rfqs.contractor_id',$contractor_id]])->whereIn('rfq_projects.procact_id',$latest_procact_ids)
      ->join('rfq_projects','rfq_projects.rfq_id','rfqs.rfq_id')->count();

      if($duplicate>0){
        $message="duplicate";
      }
      else{
        $insert=DB::table('rfqs')->insert([
          'contractor_id'=>$contractor_id,
          'date_released'=>date("Y-m-d", strtotime($request->input('date_released'))),
          'created_at' => now(),
          'updated_at' => now()
        ]);

        $latest_rfq=DB::table('rfqs')->latest()->first();
        foreach ($latest_procacts as $latest_procact) {
          DB::table('rfq_projects')->insert([
            'rfq_id'=>$latest_rfq->rfq_id,
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


  public function deleteRFQ($id)
  {
    $rfq=DB::table('rfqs')->where('rfq_id',$id)->first();
    if($rfq->date_received!=null){
      $message="delete_error";
    }
    else{
      DB::table('rfq_projects')->where('rfq_id',$id)->delete();
      DB::table('rfqs')->where('rfq_id',$id)->delete();
      $message='delete_success';
    }
    return redirect()->back()->with('message',$message);

  }



  public function filterRFQ(Request $request)
  {
    $data=$request->validate([
      "year"=>'nullable|digits:4|integer|min:2020|max:'.(date('Y')),
    ]);

    $year=$request->input('year');

    $released_rfqs=DB::table('rfq_projects')
    ->where('rfqs.date_released','like',$year.'%')
    ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
    ->join('procacts','rfq_projects.procact_id','procacts.procact_id')
    ->join('project_plans','procacts.procact_id','project_plans.latest_procact_id')
    ->join('project_timelines','procacts.procact_id','project_timelines.procact_id')
    ->join('contractors','contractors.contractor_id','rfqs.contractor_id')
    ->join('funds','funds.fund_id','project_plans.fund_id')
    ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
    ->join('municipalities','project_plans.municipality_id','municipalities.municipality_id')
    ->get();

    return back()->withInput()->with('released_rfqs',$released_rfqs);

  }


  public function submitReceiveRFQ(Request $request)
  {

    $data=$request->validate([
      "date_received"=>"required|before:tomorrow|after_or_equal:date_released",
      "time_received"=>"required"
    ]);

    $status='active';
    $rfq_id=$request->input('rfq_id');
    $latest_procact_ids=[];


    $rfq=DB::table('rfqs')->where('rfq_id',$rfq_id)->first();
    $latest_procacts=DB::table('rfq_projects')->where('rfqs.rfq_id',$rfq_id)->join('rfqs','rfq_projects.rfq_id','rfqs.rfq_id')->get();
    foreach ($latest_procacts as $latest_procact) {
      array_push($latest_procact_ids,$latest_procact->procact_id);
    }

    $latest_activity_statuses=DB::table('rfq_projects')->where('rfq_projects.rfq_id',$rfq_id)
    ->select('project_activity_status.bid_evaluation')
    ->join('procacts','rfq_projects.procact_id','procacts.procact_id')
    ->join('project_activity_status','project_activity_status.procact_id','procacts.procact_id')
    ->distinct()->get();

    $latest_activity_statuses2=DB::table('rfq_projects')->where('rfq_projects.rfq_id',$rfq_id)
    ->select('project_activity_status.post_qual')
    ->join('procacts','rfq_projects.procact_id','procacts.procact_id')
    ->join('project_activity_status','project_activity_status.procact_id','procacts.procact_id')
    ->distinct()->get();


    if(count($latest_activity_statuses)==1 && $latest_activity_statuses[0]->bid_evaluation=='bid_evaluation'){
      $bid_opened=false;
    }
    else{
      $bid_opened=true;
      // $status='late';
    }

    if(count($latest_activity_statuses2)==1 && $latest_activity_statuses2[0]->post_qual=='pending'){
      $bid_done=false;
    }
    else{
      $bid_done=true;
    }

    $existing=DB::table('project_bidders')->where('rfq_projects.rfq_id',$rfq_id)->join('rfq_projects','rfq_projects.rfq_project_id','project_bidders.rfq_project_id')->first();
    $proposed_bid=$request->input('proposed_bid');
    $date_received=$request->input('date_received');
    $time_received=$request->input('time_received');
    $rfq_id=$request->input('rfq_id');

    if($existing!=null){
      $status=$existing->bid_status;
      if($bid_done===true){
        $message="update_error";
      }
      else{

        $update=DB::table('rfqs')->where('rfq_id',$rfq_id)->update([
          'date_received'=>date("Y-m-d",strtotime($date_received)),
          'time_received'=>$time_received,
          'updated_at'=> now()
        ]);

        $update=DB::table('project_bidders')->where('rfq_projects.rfq_id',$rfq_id)->join('rfq_projects','project_bidders.rfq_project_id','rfq_projects.rfq_project_id')->update([
          "bid_status"=>$status,
          'project_bidders.updated_at' => now()
        ]);

        $message="success";

      }

    }
    else{

      $update=DB::table('rfqs')->where('rfq_id',$rfq_id)->update([
        'date_received'=>date("Y-m-d",strtotime($date_received)),
        'time_received'=>$time_received,
        'updated_at' => now()
      ]);



      foreach ($latest_procact_ids as $latest_procact_id) {
        $temp=DB::table('rfq_projects')->where([['rfq_projects.procact_id',$latest_procact_id],['rfqs.rfq_id',$rfq_id]])
        ->join('rfqs','rfq_projects.rfq_id','rfqs.rfq_id')->first();
        $insert=DB::table('project_bidders')->insert([
          'rfq_project_id'=>$temp->rfq_project_id,
          'bid_status'=>$status,
          'created_at' => now(),
          'updated_at' => now()
        ]);
      }

      $message="success";

    }

    // Update if Project Bidder is late

    $strtime_received=date("Y-m-d",strtotime($date_received))." ".$time_received;

    foreach ($latest_procact_ids as $latest_procact_id) {
      $Procact=Procact::find($latest_procact_id);
      $opening=$Procact->open_bid." 08:30";
      $temp=DB::table('rfq_projects')->where([['rfq_projects.procact_id',$latest_procact_id],['rfqs.rfq_id',$rfq_id]])
      ->join('rfqs','rfq_projects.rfq_id','rfqs.rfq_id')->first();
      $project_bidder=DB::table('project_bidders')->where('rfq_project_id',$temp->rfq_project_id)->first();

      if(strtotime($strtime_received)>strtotime($opening)){
        if($project_bidder->bid_status==="active"){
          $edit=DB::table('project_bidders')->where('rfq_project_id',$temp->rfq_project_id)->update(["bid_status"=>"disqualified"]);
          DB::table('disqualification_records')->insert([
            'project_bid'	=>$project_bidder->project_bid,
            'remarks'	=>'Disqualified: The bidder submitted their RFQ beyond the prescribed time (System Generated).',
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
            $edit=DB::table('project_bidders')->where('rfq_project_id',$temp->rfq_project_id)->update(["bid_status"=>"active"]);
          }
        }
      }
    }

    return back()->with('message',$message);
  }



}
