<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\{APP,Termination,Procact,Project,Reschedule,RescheduleProjects,ProjectBidder};
use Validator;


class SettingsController extends Controller
{
  public function getFundCategory()
  {
    $fund_categories=DB::table('fund_category')->orderBy('title','asc')->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.fund_category',['links'=>$links,'user_privilege'=>$user_privilege,"fund_categories"=>$fund_categories]);
  }

  public function submitFundCategory(Request $request)
  {
    $data=$request->validate([
      "title"=>'required',
      "status"=>'required'
    ]);

    $message="success";

    $fund_category_id=$request->input('fund_category_id');
    $fund_category=$request->input('title');
    $status=$request->input('status');
    if($fund_category_id==null){
      $duplicate=DB::table('fund_category')->where('title',$fund_category)->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{

        DB::table('fund_category')
        ->insert([
          "title"=>$fund_category,
          "status"=>$status,
          "created_at"=>now(),
          "updated_at"=>now()
        ]);

      }
    }
    else{
      $duplicate=DB::table('fund_category')->where([['title',$fund_category],['fund_category_id','<>',$fund_category_id]])->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{

        DB::table('fund_category')
        ->where("fund_category_id",$fund_category_id)
        ->update([
          "title"=>$fund_category,
          "status"=>$status,
          "updated_at"=>now()
        ]);

      }
    }

    return back()->with("message",$message);
  }

  public function deleteFundCategory($id)
  {
    $data=DB::table('funds')
    ->where("funds.fund_category_id",$id)
    ->count();

    if($data>0){
      $message="delete_error";
    }
    else{
      DB::table('fund_category')->where("fund_category_id",$id)->delete();
      $message="delete_success";
    }
    return redirect()->back()->with('message',$message);
  }

  public function getSourceOfFund()
  {
    $source_of_funds=DB::table('funds')->select('*','funds.status as fund_status')->orderBy('source','asc')->leftJoin('fund_category','fund_category.fund_category_id','funds.fund_category_id')->get();
    $fund_categories=DB::table('fund_category')->orderBy('title','asc')->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.source_of_fund',['links'=>$links,'user_privilege'=>$user_privilege,"source_of_funds"=>$source_of_funds,"fund_categories"=>$fund_categories]);
  }


  public function submitSourceOfFund(Request $request)
  {
    $data=$request->validate([
      "source"=>'required',
      "status"=>'required',
      "fund_category"=>'required'
    ]);

    $message="success";

    $source_id=$request->input('source_id');
    $source=$request->input('source');
    $status=$request->input('status');
    $fund_category=$request->input('fund_category');
    if($source_id==null){
      $duplicate=DB::table('funds')->where('source',$source)->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{

        DB::table('funds')
        ->insert([
          "source"=>$source,
          "status"=>$status,
          "fund_category_id"=>$fund_category,
          "created_at"=>now(),
          "updated_at"=>now()
        ]);

      }
    }
    else{
      $duplicate=DB::table('funds')->where([['source',$source],['fund_id','<>',$source_id]])->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{

        DB::table('funds')
        ->where("fund_id",$source_id)
        ->update([
          "source"=>$source,
          "status"=>$status,
          "fund_category_id"=>$fund_category,
          "updated_at"=>now()
        ]);

      }
    }


    return back()->with("message",$message);
  }

  public function deleteSource($id)
  {
    $data=DB::table('funds')
    ->where("funds.fund_id",$id)
    ->join('project_plans','project_plans.fund_id','funds.fund_id')
    ->count();

    if($data>0){
      $message="delete_error";
    }
    else{
      DB::table('funds')->where("fund_id",$id)->delete();
      $message="delete_success";
    }
    return redirect()->back()->with('message',$message);
  }

  public function getProjectTypes()
  {
    $project_types=DB::table('projtypes')->orderBy('type','asc')->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.project_types',['links'=>$links,'user_privilege'=>$user_privilege,"project_types"=>$project_types]);
  }

  public function submitProjectType(Request $request)
  {
    $data=$request->validate([
      "type"=>'required',
      "status"=>'required'
    ]);

    $message="success";

    $projtype_id=$request->input('projtype_id');
    $type=$request->input('type');
    $status=$request->input('status');
    if($projtype_id==null){
      $duplicate=DB::table('projtypes')->where('type',$type)->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{

        DB::table('projtypes')
        ->insert([
          "type"=>$type,
          "status"=>$status,
          "created_at"=>now(),
          "updated_at"=>now()
        ]);

      }
    }
    else{
      $duplicate=DB::table('projtypes')->where([['type',$type],['projtype_id','<>',$projtype_id]])->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{

        DB::table('projtypes')
        ->where('projtype_id',$projtype_id)
        ->update([
          "type"=>$type,
          "status"=>$status,
          "created_at"=>now(),
          "updated_at"=>now()
        ]);

      }
    }


    return back()->with("message",$message);
  }

  public function deleteProjectType($id)
  {
    $data=DB::table('projtypes')
    ->where("projtypes.projtype_id",$id)
    ->join('project_plans','project_plans.projtype_id','projtypes.projtype_id')
    ->count();

    if($data>0){
      $message="delete_error";
    }
    else{
      DB::table('projtypes')->where("projtype_id",$id)->delete();
      $message="delete_success";
    }
    return redirect()->back()->with('message',$message);
  }


  public function getSectors()
  {
    $sectors=DB::table('sectors')->orderBy('sector_name','asc')->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.sectors',['links'=>$links,'user_privilege'=>$user_privilege,"sectors"=>$sectors]);
  }


  public function submitSector(Request $request)
  {
    $data=$request->validate([
      "sector_type"=>'required',
      "sector_name"=>'required',
      "status"=>'required'
    ]);

    $message="success";


    $sector_id=$request->input('sector_id');
    $sector_name=$request->input('sector_name');
    $sector_type=$request->input('sector_type');
    $status=$request->input('status');

    if($sector_id==null){
      $duplicate=DB::table('sectors')->where('sector_name',$sector_name)->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{

        DB::table('sectors')
        ->insert([
          "sector_name"=>$sector_name,
          "sector_type"=>$sector_type,
          "status"=>$status,
          "created_at"=>now(),
          "updated_at"=>now()
        ]);

      }
    }
    else{
      $duplicate=DB::table('sectors')->where([['sector_name',$sector_name],['sector_id','<>',$sector_id]])->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{

        DB::table('sectors')
        ->where('sector_id',$sector_id)
        ->update([
          "sector_name"=>$sector_name,
          "sector_type"=>$sector_type,
          "status"=>$status,
          "updated_at"=>now()
        ]);

      }
    }


    return back()->with("message",$message);
  }

  public function deleteSector($id)
  {
    $data=DB::table('sectors')
    ->where("sectors.sector_id",$id)
    ->join('project_plans','project_plans.sector_id','sectors.sector_id')
    ->count();

    if($data>0){
      $message="delete_error";
    }
    else{
      DB::table('sectors')->where("sector_id",$id)->delete();
      $message="delete_success";
    }
    return redirect()->back()->with('message',$message);
  }

  public function unreceiveBiddersDocuments()
  {
    $title="Unreceive Bidders Documents";
    $modes=DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.unreceive_bidders_documents',['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"modes"=>$modes]);
  }

  public function submitUnreceiveBiddersDocuments(Request $request){
    $data=$request->validate([
      "password"=>'required',
      "project_title"=>'required',
      "contractor"=>'required',
      "remarks"=>'required'
    ]);
    $APP=new APP;
    $password=$request->input('password');
    $remarks=$request->input('remarks');
    $user_id=Auth::user()->id;
    $checkPassword=checkPassword($user_id,$password);
    if($checkPassword){
      $opening_count=$APP->checkOngoingSpecificProject($request->input('plan_id'),"opening");
      if($opening_count===0){
        return back()->withInput()->with("message","opening_error");
      }
      else{
        $plan=DB::table('project_plans')->where('plan_id',$request->plan_id)->first();

        if($plan->mode_id===1){
          // delete project bidder and clear bid doc receive date

          $bid_doc=DB::table('bid_doc_projects')->where([['procact_id',$plan->latest_procact_id],['bid_docs.contractor_id',$request->input('contractor_id')]])
          ->join('bid_docs','bid_doc_projects.bid_doc_id','bid_docs.bid_doc_id')
          ->join('contractors','bid_docs.contractor_id','contractors.contractor_id')
          ->first();

          DB::table('bid_docs')->where('bid_doc_id',$bid_doc->bid_doc_id)->update([
            "date_received"=>null,
            "time_received"=>null,
            "proposed_bid"=>null,
            "bid_in_words"=>null,
            "initial_bid_as_evaluated"=>null,
            "bid_as_evaluated"=>null,
            "discount"=>null,
            "amount_of_discount"=>null,
            "discount_type"=>null,
            "discount_source"=>null,
          ]);

          $log="Unreceived Bid Docs of ".$bid_doc->business_name;

          DB::table('project_logs')->insert([
            'plan_id'=>	$plan->plan_id,
            'user_id'=>Auth::user()->id,
            'project_log_type'=>$log,
            'project_log_remarks'=>$remarks,
            'log_date'=>date("Y-m-d"),
            'created_at'=> now(),
            'updated_at'=> now()
          ]);

          DB::table('project_bidders')->where("bid_doc_project_id",$bid_doc->bid_doc_project_id)->delete();

          return back()->with("message","success");
        }
        else{
          // delete project bidder and clear bid doc receive date

          $rfq=DB::table('rfq_projects')->where([['procact_id',$plan->latest_procact_id],['rfqs.contractor_id',$request->input('contractor_id')]])
          ->join('rfqs','rfq_projects.rfq_id','rfqs.rfq_id')
          ->join('contractors','rfqs.contractor_id','contractors.contractor_id')
          ->first();

          DB::table('rfqs')->where('rfq_id',$rfq->rfq_id)->update([
            "date_received"=>null,
            "time_received"=>null,
            "proposed_bid"=>null,
            "bid_in_words"=>null,
            "initial_bid_as_evaluated"=>null,
            "bid_as_evaluated"=>null,
            "discount"=>null,
            "amount_of_discount"=>null,
            "discount_type"=>null,
            "discount_source"=>null,
          ]);

          $log="Unreceived RFQ of ".$rfq->business_name;


          DB::table('project_logs')->insert([
            'plan_id'=>	$plan->plan_id,
            'user_id'=>Auth::user()->id,
            'project_log_type'=>$log,
            'project_log_remarks'=>$remarks,
            'log_date'=>date("Y-m-d"),
            'created_at'=> now(),
            'updated_at'=> now()
          ]);


          DB::table('project_bidders')->where("rfq_project_id",$rfq->rfq_project_id)->delete();
          return back()->with("message","success");
        }

      }
    }
    else{
      return back()->withInput()->with("message","password_error");
    }


  }


  public function withdrawBidderDocuments()
  {
    $title="Withdraw Bidders Documents";
    $modes=DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    $user_privilege=["view"];
    return view('admin.withdraw_bidders_documents',['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"modes"=>$modes]);
  }

  public function submitwithdrawBidderDocuments(Request $request){
    $data=$request->validate([
      "password"=>'required',
      "project_title"=>'required',
      "contractor"=>'required',
      "remarks"=>'required',
      "letter_date"=>'required',
      "date_received"=>'required|after_or_equal:letter_date'
    ]);
    $APP=new APP;
    $password=$request->input('password');
    $remarks=$request->input('remarks');
    $user_id=Auth::user()->id;
    $checkPassword=checkPassword($user_id,$password);
    if($checkPassword){

      $plan=DB::table('project_plans')->where('plan_id',$request->plan_id)->first();

      if($plan->mode_id===1){

        $bid_doc=DB::table('bid_doc_projects')->where([['procact_id',$plan->latest_procact_id],
        ['bid_docs.contractor_id',$request->input('contractor_id')]])
        ->join('bid_docs','bid_doc_projects.bid_doc_id','bid_docs.bid_doc_id')
        ->join('contractors','bid_docs.contractor_id','contractors.contractor_id')
        ->join('project_bidders','bid_doc_projects.bid_doc_project_id','project_bidders.bid_doc_project_id')
        ->first();

        ProjectBidder::where('project_bid',$bid_doc->project_bid)->update([
          "bid_status"=>"withdrawn",
          "withdrawal_letter_date"=>Date('Y-m-d',strtotime($request->letter_date)),
          "withdrawal_receive_date"=>Date('Y-m-d',strtotime($request->date_received)),
        ]);

        $log="Withdrawn Bidding Documents:".$bid_doc->business_name;

        DB::table('project_logs')->insert([
          'plan_id'=>	$plan->plan_id,
          'user_id'=>Auth::user()->id,
          'project_log_type'=>$log,
          'project_log_remarks'=>$remarks,
          'log_date'=>date("Y-m-d"),
          'created_at'=> now(),
          'updated_at'=> now()
        ]);


        return back()->with("message","success");
      }
      else{

        $rfq=DB::table('rfq_projects')->where([['procact_id',$plan->latest_procact_id],
        ['rfqs.contractor_id',$request->input('contractor_id')]])
        ->join('rfqs','rfq_projects.rfq_id','rfqs.rfq_id')
        ->join('contractors','rfqs.contractor_id','contractors.contractor_id')
        ->join('project_bidders','rfq_projects.rfq_project_id','project_bidders.rfq_project_id')
        ->first();

        ProjectBidder::where('project_bid',$rfq->project_bid)->update([
          "bid_status"=>"withdrawn",
          "withdrawal_letter_date"=>Date('Y-m-d',strtotime($request->letter_date)),
          "withdrawal_receive_date"=>Date('Y-m-d',strtotime($request->date_received)),
        ]);

        $log="Withdrawn RFQ:".$rfq->business_name;

        DB::table('project_logs')->insert([
          'plan_id'=>	$plan->plan_id,
          'user_id'=>Auth::user()->id,
          'project_log_type'=>$log,
          'project_log_remarks'=>$remarks,
          'log_date'=>date("Y-m-d"),
          'created_at'=> now(),
          'updated_at'=> now()
        ]);

        return back()->with("message","success");
      }

    }

    else{
      return back()->withInput()->with("message","password_error");
    }


  }

  public function autoCompleteAwardedProjects(Request $request){
    $term=$request->term;
    $APP=new APP;
    $project_plans = DB::table('project_plans')
    ->select('project_plans.plan_id','project_plans.project_bid_id', 'project_plans.project_title','project_plans.project_type','project_plans.project_no','procacts.*','project_bidders.*')
    ->where([
      // ['project_plans.is_old',false]
      ['project_plans.project_bid_id','<>',null],
      ['project_plans.status', '<>', 'complete'],
      ['project_plans.status', '<>', 'for_review'],['project_plans.project_title', 'LIKE', '%'.$term.'%'],
      ['project_timelines.timeline_status','set']
    ]);

    if($request->opening_date!=null){
      $project_plans=$project_plans->where('open_bid',date('Y-m-d',strtotime($request->opening_date)));
    }
    $project_plans=$project_plans->join('procacts','project_plans.latest_procact_id','procacts.procact_id')
    ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
    ->join('project_bidders','project_plans.project_bid_id','project_bidders.project_bid')
    ->orderBy('project_bid_id','desc')
    ->distinct()
    ->take(10)
    ->get();

    if(sizeOf($project_plans) != 0){
      foreach($project_plans as $project_plan){
        $bid_details=$APP->getBid($project_plan->project_bid_id);
        $results[] = [
          'id' => $project_plan->plan_id,
          'procact_id' =>  $bid_details->procact_id,
          'procact' =>  $bid_details->procact_id,
          'value' => $project_plan->project_title,
          'project_number' => $project_plan->project_no,
          'project_bid_id' =>  $project_plan->project_bid_id,
          'contractor' =>  $bid_details->business_name,
        ];
      }
    }
    else{
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }
    return response()->json($results);

  }

  public function clearTermination (){
    $title="Clear Termination of Contract";
    $modes=DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.clear_termination',['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"modes"=>$modes]);
  }

  public function reschedule (){
    $title="Reschedule Project Opening";
    $modes=DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
    $links=getUserLinks();
    $user_privilege=['view'];
    return view('admin.reschedule',['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"modes"=>$modes]);
  }

  public function submitClearTermination (Request $request){
    $data=$request->validate([
      "termination_id"=>"required",
      "project_title"=>'required',
      "contractor"=>'required',
      "remarks"=>'required',
      "password"=>"required"
    ]);

    $password=$request->input('password');
    $remarks=$request->input('remarks');

    $user_id=Auth::user()->id;
    $checkPassword=checkPassword($user_id,$password);
    if($checkPassword){
      $termination=Termination::find($request->termination_id);
      if($termination->with_attachment===1){
        return back()->withInput()->with("message","termination_error");
      }
      else{
        $procact=Procact::find($termination->procact_id);
        $status="completed";
        if(is_null($procact->proceed_notice)===true){
          $status="pending";
        }

        DB::table('project_activity_status')->where('procact_id',$termination->procact_id)->update([
          "main_status"=>$status
        ]);

        DB::table('project_plans')->where('procacts.procact_id',$termination->procact_id)
        ->join('procacts','procacts.plan_id','project_plans.plan_id')->update([
          'project_plans.project_bid_id'=>$termination->project_bid
        ]);

        DB::table('project_logs')->insert([
          'plan_id'=>	$procact->plan_id,
          'user_id'=>Auth::user()->id,
          'project_log_type'=>"Clear Termination of Contract",
          'project_log_remarks'=>$request->contractor.":".$remarks,
          'log_date'=>date("Y-m-d"),
          'created_at'=> now(),
          'updated_at'=> now()
        ]);

        return back()->with("message","success");
      }
    }
    else{
      return back()->withInput()->with("message","password_error");
    }
  }


  public function autoCompleteTerminatedProjects(Request $request){
    $term=$request->term;
    $APP=new APP;
    $project_plans = DB::table('termination')
    ->select('project_plans.plan_id','project_plans.project_bid_id', 'project_plans.project_title','project_plans.project_type','project_plans.project_no','procacts.*','project_bidders.*','termination.*','project_activity_status.*')
    ->where([
      ['project_activity_status.main_status','terminated'],
      ['project_plans.status', '<>', 'for_review'],['project_plans.project_title', 'LIKE', '%'.$term.'%'],
      ['project_timelines.timeline_status','set']
    ]);

    if($request->opening_date!=null){
      $project_plans=$project_plans->where('open_bid',date('Y-m-d',strtotime($request->opening_date)));
    }

    $project_plans=$project_plans
    ->join('procacts','procacts.procact_id','termination.procact_id')
    ->join('project_plans','project_plans.plan_id','procacts.plan_id')
    ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
    ->join('project_bidders','termination.project_bid','project_bidders.project_bid')
    ->join('project_activity_status','procacts.procact_id','project_activity_status.procact_id')
    ->orderBy('project_bid_id','desc')
    ->distinct()
    ->take(10)
    ->get();

    if(sizeOf($project_plans) != 0){
      foreach($project_plans as $project_plan){
        $bid_details=$APP->getBid($project_plan->project_bid);
        $results[] = [
          'termination_id' => $project_plan->termination_id,
          'procact_id' =>  $bid_details->procact_id,
          'procact' =>  $bid_details->procact_id,
          'value' => $project_plan->project_title,
          'project_number' => $project_plan->project_no,
          'project_bid' =>  $project_plan->project_bid,
          'contractor' =>  $bid_details->business_name,
        ];
      }
    }
    else{
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }
    return response()->json($results);

  }


  public function clearReversion (){
    $title="Clear Project Reversion";
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.clear_reversion',['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title]);
  }

  public function autoCompleteRevertedProjects(Request $request){
    $term=$request->term;
    $APP=new APP;
    $project_plans = DB::table('project_plans')
    ->select('project_plans.plan_id','project_plans.project_bid_id', 'project_plans.project_title','project_plans.project_type','project_plans.project_no','procacts.*','project_activity_status.*')
    ->where([
      ['project_plans.status','reverted'],
      ['project_activity_status.main_status','reverted'],
      ['project_plans.project_title', 'LIKE', '%'.$term.'%']
    ])
    ->join('procacts','project_plans.plan_id','procacts.plan_id')
    ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
    ->join('project_activity_status','procacts.procact_id','project_activity_status.procact_id')
    ->orderBy('project_title','asc')
    ->distinct()
    ->take(10)
    ->get();


    if(sizeOf($project_plans) != 0){
      foreach($project_plans as $project_plan){
        $results[] = [
          'plan_id' => $project_plan->plan_id,
          'value' => $project_plan->project_title,
          'project_number' => $project_plan->project_no
        ];
      }
    }
    else{
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }
    return response()->json($results);

  }


  public function submitClearReversion (Request $request){
    $data=$request->validate([
      "plan_id"=>"required",
      "project_title"=>'required',
      "remarks"=>'required',
      "password"=>"required"
    ]);

    $password=$request->input('password');
    $remarks=$request->input('remarks');

    $user_id=Auth::user()->id;
    $checkPassword=checkPassword($user_id,$password);


    if($checkPassword){
      $project=Project::find($request->plan_id);
      if($project->status!="reverted"){
        return back()->withInput()->with("message","reverted_error");
      }
      else{
        $procact=Procact::find($project->latest_procact_id);
        $status="completed";
        if(is_null($procact->proceed_notice)===true){
          $status="pending";
        }

        DB::table('project_activity_status')->where('procact_id',$project->latest_procact_id)->update([
          "main_status"=>$status
        ]);

        if($procact->proceed_notice!=null){
          $project->status="completed";

        }
        else if($procact->open_bid!=null){
          $project->status="onprocess";

        }
        else{
          $project->status="pending";
        }
        $project->save();

        DB::table('project_logs')->insert([
          'plan_id'=>	$procact->plan_id,
          'user_id'=>Auth::user()->id,
          'project_log_type'=>"Clear Project Reversion",
          'project_log_remarks'=>$remarks,
          'log_date'=>date("Y-m-d"),
          'created_at'=> now(),
          'updated_at'=> now()
        ]);

        return back()->with("message","success");
      }
    }
    else{
      return back()->withInput()->with("message","password_error");
    }
  }

  public function autoCompletePostQualifiedContractors(Request $request){
    $APP=new APP;
    if($request->type==="TWG"){
      $bidders=$APP->getTWGBiddersData($request->procact_id,'responsive,non-responsive');
    }
    else{
      $bidders=$APP->getBiddersData($request->procact_id,'responsive,non-responsive');
    }

    if(sizeOf($bidders) == 0){
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }
    else{
      foreach($bidders as $bidder){
        $results[] = [
          'id' => $bidder->project_bid,
          'value' => $bidder->business_name,
        ];
      }
    }

    return response()->json($results);
  }

  public function clearPostQualification (){
    $title="Clear Post Qualification";
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.clear_post_qualification',['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"type"=>"BACSEC"]);
  }

  public function clearPostTWGQualification (){
    $title="Clear TWG Post Qualification";
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.clear_post_qualification',['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"type"=>"TWG"]);
  }

  public function clearTWGQualification (){
    $title="Clear TWG Post Qualification";
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.clear_post_qualification',['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"type"=>"TWG"]);
  }

  public function submitReschedule (Request $request){
    $data=$request->validate([
      "opening_date"=>"required",
      "new_opening_date"=>'required|after:opening_date',
      "remarks"=>'required',
      "password"=>"required"
    ]);

    $password=$request->input('password');
    $remarks=$request->input('remarks');
    $APP=new APP;

    $user_id=Auth::user()->id;
    $checkPassword=checkPassword($user_id,$password);
    if($checkPassword){
      // check projects with the opening_date
      $projects=DB::table('project_timelines')->selectRaw("GROUP_CONCAT(procact_id SEPARATOR ',') as procacts,GROUP_CONCAT(plan_id SEPARATOR ',') as plan_ids")->where('bid_submission_start',Date('Y-m-d',strtotime($request->opening_date)))->groupBy('bid_submission_start')->first();

      // check if there are scheduled projects with the opening_date
      if($projects===null){
        return back()->with('message',"project_error");
      }
      else{
        $existing_resched=Reschedule::where([['opening_date',Date('Y-m-d',strtotime($request->opening_date))],['new_opening_date',Date('Y-m-d',strtotime($request->new_opening_date))]])->first();
        if($existing_resched===null){
          $resched=Reschedule::create([
            'opening_date'=>Date('Y-m-d',strtotime($request->opening_date)),
            'new_opening_date'=>Date('Y-m-d',strtotime($request->new_opening_date)),
            'reschedule_remarks'=>$request->remarks
          ]);

          $procacts=explode(',',$projects->procacts);

          foreach($procacts as $procact){
            RescheduleProjects::create([
              'procact_id'=>$procact,
              'reschedule_id'=>$resched->id
            ]);
          }

          // extend and edit procacts
          $remarks="Recheduled Project From ".Date('m/d/Y',strtotime($request->opening_date)).'-'.Date('m/d/Y',strtotime($request->new_opening_date)).": ".$request->remarks;
          $extend=$APP->extendSpecificProcess($projects->plan_ids,"submission_opening",$request->new_opening_date,$remarks);
          DB::table('procacts')->whereIn('procact_id',$procacts)->update([
            "open_bid"=>Date('Y-m-d',strtotime($request->new_opening_date))
          ]);

        }
      }
      return back()->withInput()->with("message","success");
    }
    else{
      return back()->withInput()->with("message","password_error");
    }
  }



}
