<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use App\Notice;
use App\Contract;
use App\Contractors;
use App\Procact;
use App\NoticeOfAward;
use App\NoticeToProceed;
use Validator;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\AdditionalDocumentController;


class NoticeController extends Controller
{

  public function editNotice($id)
  {
    $bidder_notice=Notice::where('project_bidder_notice_id',$id)->first();
    if($bidder_notice==""||$bidder_notice==null){
      return abort(403,"Undefined Notice");
    }
    else{
      return  response()->download(public_path("notice_documents/".$bidder_notice->file_name));
    }
  }

  public function getNotices()
  {
    $year=date('Y');

    $project_plans=DB::table('project_plans')
    ->where([['project_timelines.timeline_status','set'],['project_activity_status.advertisement','finished'],['project_year',$year]])
    ->join('municipalities', 'project_plans.municipality_id','=','municipalities.municipality_id')
    ->leftJoin('barangays', 'project_plans.barangay_id','=','barangays.barangay_id')
    ->join('projtypes', 'project_plans.projtype_id','=','projtypes.projtype_id')
    ->join('procurement_modes', 'project_plans.mode_id','=','procurement_modes.mode_id')
    ->join('funds', 'project_plans.fund_id','=','funds.fund_id')
    ->leftJoin('sectors', 'project_plans.sector_id','=','sectors.sector_id')
    ->join('fund_category', 'fund_category.fund_category_id','=','funds.fund_category_id')
    ->join('account_classifications', 'project_plans.account_id','=','account_classifications.account_id')
    ->join('procacts', 'project_plans.plan_id','=','procacts.plan_id')
    ->join('project_timelines', 'procacts.procact_id','=','project_timelines.procact_id')
    ->join('project_activity_status', 'project_activity_status.procact_id','=','procacts.procact_id')
    ->get();
    $title="Generate Notices";
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view("admin.project_notice",['links'=>$links,'user_privilege'=>$user_privilege,'project_plans'=>$project_plans,"title"=>$title,"year"=>$year]);
  }


  public function filterNoticePerProject(Request $request)
  {
    $data=$request->validate([
      "project_year"=>'required|digits:4|integer|min:2020|max:'.(date('Y')),
    ]);
    $year=$request->project_year;

    $project_plans=DB::table('project_plans')
    ->where([['project_timelines.timeline_status','set'],['project_activity_status.advertisement','finished'],['project_year',$year]])
    ->join('municipalities', 'project_plans.municipality_id','=','municipalities.municipality_id')
    ->leftJoin('barangays', 'project_plans.barangay_id','=','barangays.barangay_id')
    ->join('projtypes', 'project_plans.projtype_id','=','projtypes.projtype_id')
    ->join('procurement_modes', 'project_plans.mode_id','=','procurement_modes.mode_id')
    ->join('funds', 'project_plans.fund_id','=','funds.fund_id')
    ->leftJoin('sectors', 'project_plans.sector_id','=','sectors.sector_id')
    ->join('fund_category', 'fund_category.fund_category_id','=','funds.fund_category_id')
    ->join('account_classifications', 'project_plans.account_id','=','account_classifications.account_id')
    ->join('procacts', 'project_plans.latest_procact_id','=','procacts.procact_id')
    ->join('project_timelines', 'procacts.procact_id','=','project_timelines.procact_id')
    ->join('project_activity_status', 'project_activity_status.procact_id','=','procacts.procact_id')
    ->get();

    return back()->withInput()->with('project_plans',$project_plans);
  }

  public function getNoticeBidders($id)
  {
    $APP = new APP;
    $year=date('Y');
    $project_plan=DB::table('project_plans')->where('project_plans.plan_id',$id)
    ->join('procacts','procacts.procact_id','project_plans.latest_procact_id')
    ->first();
    $responsive_count=count($APP->getBiddersData($project_plan->latest_procact_id,'responsive'));
    $project_bidders=(array)$APP->getAllBidders($id);


    // $project_bidders=array_filter($project_bidders, function($value,$key) {
    //   return !is_null($value->bid_status);
    // }, ARRAY_FILTER_USE_BOTH);

    $links=getUserLinks();
    $user_privilege=getUserPrivilegeByLink('notices');
    $access=checkUserAccess('view',$user_privilege);
    return view("admin.project_notice_bidders",['links'=>$links,'user_privilege'=>$user_privilege,"project_number"=>$project_plan->project_no,"title"=>$project_plan->project_title,"open_bid"=>$project_plan->open_bid,"project_cost"=>$project_plan->project_cost,"project_bidders"=>$project_bidders,"responsive_count"=>$responsive_count]);
  }

  public function submitNotice(Request $request){

    $data=$request->validate([
      "date_generated"=>"required",
      "date_released"=>"nullable|after_or_equal:date_generated|required_with:date_received_by_contractor",
      "date_received_by_contractor"=>"nullable|after_or_equal:date_released|required_with:date_received_by_bac",
      "date_received_by_bac"=>"nullable|after_or_equal:date_received_by_contractor|required_with:date_received_by_contractor",
    ]);

    $notice_type=$request->input('notice_type');

    $project_bid=$request->input('project_bid');

    $notice_id=$request->input('notice_id');

    $date_generated=date("Y-m-d", strtotime($request->input('date_generated')));

    $bac=DB::table('bids_and_awards_committee')->orderBy('bac_id','desc')->first();

    if($request->input('date_released')==null){
      $date_released=null;
    }
    else{
      $date_released=date("Y-m-d", strtotime($request->input('date_released')));
    }

    if($request->input('date_received_by_contractor')==null){
      $date_received_by_contractor=null;
    }
    else{
      $date_received_by_contractor=date("Y-m-d", strtotime($request->input('date_received_by_contractor')));
    }

    if($request->input('date_received_by_contractor')==null){
      $date_received_by_bac=null;
    }
    else{
      $date_received_by_bac=date("Y-m-d", strtotime($request->input('date_received_by_bac')));
    }

    $last_received=null;
    $remarks=$request->input('remarks');
    $filename=md5(date('Y-m-d H:i:s:u')).".docx";
    $APP=new APP;

    $message="success";

    $project_plan=DB::table('project_plans')
    ->where('project_bidders.project_bid',$project_bid)
    ->select('project_plans.project_bid_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','project_plans.mode_id')
    ->join('procacts','procacts.plan_id','project_plans.plan_id')
    ->join('rfq_projects','rfq_projects.procact_id','procacts.procact_id')
    ->join('project_bidders','project_bidders.rfq_project_id','rfq_projects.rfq_project_id')
    ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
    ->join('contractors','contractors.contractor_id','rfqs.contractor_id')
    ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
    ->orderBy('procacts.itb_arrangement','asc')
    ->first();


    if($project_plan===null){
      $project_plan=DB::table('project_plans')
      ->where('project_bidders.project_bid',$project_bid)
      ->select('project_plans.project_bid_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','project_plans.mode_id')
      ->join('procacts','procacts.plan_id','project_plans.plan_id')
      ->join('bid_doc_projects','bid_doc_projects.procact_id','procacts.procact_id')
      ->join('project_bidders','project_bidders.bid_doc_project_id','bid_doc_projects.bid_doc_project_id')
      ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
      ->join('contractors','contractors.contractor_id','bid_docs.contractor_id')
      ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
      ->orderBy('procacts.itb_arrangement','asc')
      ->first();
    }


    $cluster_bids=$APP->getClusterBids($project_bid);

    if($notice_type==="NOA"){
      $procact=Procact::where('procact_id',$cluster_bids[0]->procact_id)->first();
      $post_qualification_end=date('m/d/Y', strtotime("+3 day", strtotime($procact->post_qual)));
      $data=$request->validate([
        "date_generated"=>"required",
        "date_released"=>"nullable|after_or_equal:".$post_qualification_end,
        "date_received_by_contractor"=>"nullable|after_or_equal:".$post_qualification_end,
        "date_received_by_bac"=>"nullable|after_or_equal:".$post_qualification_end,
      ]);

      //Check NOPD and NTLB
      $ProcurementController=new ProcurementController;
      $non_responsive=$APP->getBiddersData($project_plan->procact_id,'non-responsive');
      $losing_bidder=$APP->getBiddersData($project_plan->procact_id,'active');
      $losing_bidder_array=(array)json_decode($losing_bidder);
      $non_responsive_array=(array)json_decode($non_responsive);
      $non_responsive_project_bids = array_column($non_responsive_array, 'project_bid');
      $losing_bidder_project_bids = array_column($losing_bidder_array, 'project_bid');
      $resolution_projects=DB::table('resolution_projects')->where([['resolution_projects.procact_id',$project_plan->procact_id],['resolutions.type','RRA']])
      ->join('resolutions','resolutions.resolution_id','resolution_projects.resolution_id')
      ->first();

      //
      // if(count($non_responsive)>0){
      //
      //   // Check matching bidders and disqualifications
      //   $nopd_cnt=Notice::where('notice_type',"NOPD")->whereIn('project_bid',$non_responsive_project_bids)->count();
      //   if(count($non_responsive)>$nopd_cnt){
      //     $message="nopd_mismatch";
      //   }
      //   else{
      //     $nopd_cnt=Notice::where([['notice_type',"NOPD"],['date_received',null]])->whereIn('project_bid',$non_responsive_project_bids)->count();
      //
      //     if($nopd_cnt>=1){
      //       $message="nopd_null_release";
      //     }
      //     else{
      //       $nopd=Notice::where([['notice_type',"NOPD"]])
      //       ->whereIn('project_bid',$non_responsive_project_bids)
      //       ->orderBy('date_received','desc')
      //       ->first();
      //
      //       $date1=date_create(date("Y-m-d"));
      //       $date2=date_create($nopd->date_received);
      //       $noa_date_release=date_create($date_released);
      //       $diff=date_diff($date1,$date2);
      //       $last_received=$nopd->date_received;
      //       $diff2=date_diff($date2,$noa_date_release);
      //
      //       if($diff2->invert===1){
      //         $message="nopd_release";
      //       }
      //       else if($diff->d<3){
      //         $message="nopd_less_release";
      //       }
      //       else{
      //
      //       }
      //     }
      //   }
      // }
      //
      // if(count($losing_bidder)>0){
      //   $ntlb_cnt=Notice::where('notice_type',"NTLB")->whereIn('project_bid',$losing_bidder_project_bids)->count();
      //   if(count($losing_bidder)!=$ntlb_cnt){
      //     $message="ntlb_mismatch";
      //   }
      //   else{
      //     $ntlb_cnt=Notice::where([['notice_type',"NTLB"],['date_received',null]])->whereIn('project_bid',$losing_bidder_project_bids)->count();
      //     if($ntlb_cnt>=1){
      //       $message="ntlb_null_release";
      //     }
      //     else{
      //       $ntlb=Notice::where([['notice_type',"ntlb"]])
      //       ->whereIn('project_bid',$losing_bidder_project_bids)
      //       ->orderBy('date_received','desc')
      //       ->first();
      //
      //       $date1=date_create(date("Y-m-d"));
      //       $date2=date_create($ntlb->date_received);
      //       $diff=date_diff($date1,$date2);
      //       $last_received=$ntlb->date_received;
      //
      //       if($diff->d<3){
      //         $message="ntlb_less_release";
      //       }
      //     }
      //   }
      // }
      //
      // if($date_received!=null){
      //   foreach ($cluster_bids as $cluster_bid) {
      //     $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$cluster_bid->plan_id)->orderBy('pro_act_stat_id','desc')->first();
      //     $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$cluster_bid->plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      //     $latest_timeline=DB::table("project_timelines")->where("plan_id",$cluster_bid->plan_id)->orderBy('timeline_id','desc')->first();
      //
      //     $date_received_cmp=date("m/d/Y", strtotime($date_received));
      //     $award_notice_start=date("m/d/Y", strtotime($latest_timeline->award_notice_start));
      //     $award_notice_end=date("m/d/Y", strtotime($latest_timeline->award_notice_end));
      //
      //     if(strtotime($date_received_cmp)<strtotime($award_notice_start)||strtotime($date_received_cmp)>strtotime($award_notice_end)){
      //       $message="range_error";
      //     }
      //   }
      // }
      //
      //
      //
      // if($resolution_projects==null){
      //   $message="resolution_error";
      // }
      //
      // if($message!="range_error"){
      //   $message="success";
      // }

      if($message==="success"){
        if($notice_id==null){
          foreach ($cluster_bids as $cluster_bid) {
            $duplicate=DB::table('notice_of_awards')
            ->where('project_bid_id',$cluster_bid->project_bid)
            ->first();
            if($duplicate!=null){
              $message="duplicate";
            }
          }


          if($message==="success"){
            foreach ($cluster_bids as $cluster_bid) {
              NoticeOfAward::create([
                "project_bid_id"=>$cluster_bid->project_bid,
                "date_generated"=>$date_generated,
                "date_released"=>$date_released,
                "date_received_by_contractor"=>$date_received_by_contractor,
                "date_received"=>$date_received_by_bac,
                "noa_remarks"=>$remarks
              ]);
            }
          }
        }
        else{
          foreach ($cluster_bids as $cluster_bid) {
            $noa=NoticeOfAward::where('project_bid_id',$cluster_bid->project_bid)->first();
            if($noa===null){
              NoticeOfAward::create([
                "project_bid_id"=>$cluster_bid->project_bid,
                "date_generated"=>$date_generated,
                "date_released"=>$date_released,
                "date_received_by_contractor"=>$date_received_by_contractor,
                "date_received"=>$date_received_by_bac,
                "noa_remarks"=>$remarks
              ]);
              $noa=NoticeOfAward::where('project_bid_id',$cluster_bid->project_bid)->first();
            }
            $noa->date_generated=$date_generated;
            $noa->date_released=$date_released;
            $noa->date_received_by_contractor=$date_received_by_contractor;
            $noa->date_received=$date_received_by_bac;
            $noa->noa_remarks=$remarks;
            $noa->save();
          }
        }

        if($date_received_by_bac!==null){
          // check project_timeline
          // $procact=Procact::where('procact_id',$cluster_bids[0]->procact_id)->first();
          // if($procact->award_notice!=date("Y-m-d", strtotime($request->input("date_received_by_bac")))&&$procact->contract_signing==null){
          //   $plan_ids_array = array_column((array)json_decode($cluster_bids), 'plan_id');
          //   $plan_ids= implode(",", $plan_ids_array);
          //   $extend=$APP->extendSpecificProcess($plan_ids,"notice_of_award",$date_received_by_bac,"Automatic Extension");
          //   $parameters=["plan_id"=>$plan_ids,"award_notice_date"=>date("m/d/Y", strtotime($date_received_by_bac)),"bypass"=>true];
          //   $request = new \Illuminate\Http\Request();
          //   $request->replace($parameters);
          //   $test=$ProcurementController->submitAwardNotice($request);
          // }
        }
      }


    }

    else if($notice_type==="NTP"){
      $contract=DB::table("contracts")->where('project_bid_id',$project_bid)->first();
      $ProcurementController=new ProcurementController;
      $old_receive=null;
      // $non_responsive=$APP->getBiddersData($project_plan->procact_id,'non-responsive');
      // $losing_bidder=$APP->getBiddersData($project_plan->procact_id,'active');
      // $array=(array)json_decode($non_responsive);
      // $project_bids = array_column($array, 'project_bid');
      $resolution_projects=DB::table('resolution_projects')->where([['resolution_projects.procact_id',$project_plan->procact_id],['resolutions.type','RRA']])
      ->join('resolutions','resolutions.resolution_id','resolution_projects.resolution_id')
      ->first();

      if($date_received_by_bac!=null){
        foreach ($cluster_bids as $cluster_bid) {
          $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$cluster_bid->plan_id)->orderBy('pro_act_stat_id','desc')->first();
          $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$cluster_bid->plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
          $latest_timeline=DB::table("project_timelines")->where("plan_id",$cluster_bid->plan_id)->orderBy('timeline_id','desc')->first();

          $date_received_cmp=date("m/d/Y", strtotime($date_received_by_bac));
          $proceed_notice_start=date("m/d/Y", strtotime($latest_timeline->proceed_notice_start));
          $proceed_notice_end=date("m/d/Y", strtotime($latest_timeline->proceed_notice_end));
          $contract=Contract::where('project_bid_id',$project_bid)->first();

          if($contract===null){
            $message="contract_error";
          }
          else{
            if($contract->contract_receive_date===null){
              $message="contract_error";
            }
          }

          // if(strtotime($date_received_cmp)<strtotime($proceed_notice_start)||strtotime($date_received_cmp)>strtotime($proceed_notice_end)){
          //   $message="range_error";
          // }

        }
        if($message=="success"){
          $noa=NoticeOfAward::where('project_bid_id',$cluster_bid->project_bid)->first();
          $noa_end=date('m/d/Y',strtotime($noa->date_received_by_contractor));
          $data=$request->validate([
            "date_released"=>"after_or_equal:".$noa_end,
            "date_received_by_contractor"=>"after_or_equal:date_released",
            "date_received_by_bac"=>"after_or_equal:date_received_by_contractor",
          ]);

        }
      }

      if($message==="success"){
        if($notice_id==null){
          foreach ($cluster_bids as $cluster_bid) {
            $duplicate=DB::table('notice_to_proceeds')
            ->where('project_bid_id',$cluster_bid->project_bid)
            ->first();
            if($duplicate!=null){
              $message="duplicate";
            }
          }
          if($message==="success"){
            foreach ($cluster_bids as $cluster_bid) {
              NoticeToProceed::insert([
                "project_bid_id"=>$cluster_bid->project_bid,
                "ntp_date_generated"=>$date_generated,
                "ntp_date_released"=>$date_released,
                "ntp_date_received_by_contractor"=>$date_received_by_contractor,
                "ntp_date_received"=>$date_received_by_bac,
                "ntp_remarks"=>$remarks,
                "created_at"=>now(),
                "updated_at"=>now()
              ]);
            }
          }
        }
        else{
          foreach ($cluster_bids as $cluster_bid) {
            DB::table('notice_to_proceeds')
            ->where('project_bid_id',$cluster_bid->project_bid)
            ->update([
              "ntp_date_generated"=>$date_generated,
              "ntp_date_released"=>$date_released,
              "ntp_date_received_by_contractor"=>$date_received_by_contractor,
              "ntp_date_received"=>$date_received_by_bac,
              "ntp_remarks"=>$remarks,
              "updated_at"=>now()
            ]);
          }
        }

        // if($date_received_by_bac!==null && $date_received_by_bac!=$old_receive){
        //   $plan_ids_array = array_column((array)json_decode($cluster_bids), 'plan_id');
        //   $plan_ids= implode(",", $plan_ids_array);
        //   $extend=$APP->extendSpecificProcess($plan_ids,"notice_to_proceed",$request->input("date_received_by_bac"),"Automatic Extension");
        //   $parameters=["plan_id"=>$plan_ids,"notice_to_proceed_date"=>date("m/d/Y", strtotime($date_received_by_bac)),"bypass"=>true];
        //   $request = new \Illuminate\Http\Request();
        //   $request->replace($parameters);
        //   $ProcurementController->submitNoticeToProceed($request);
        // }
      }
    }
    else{
      $mr_due_date=$request->input('mr_due_date');
      if(isset($mr_due_date)){
        $mr_due_date=Date('Y-m-d',strtotime($mr_due_date));
      }
      foreach ($cluster_bids as $cluster_bid) {

        $temp=Notice::firstOrCreate([
          "project_bid"=>$cluster_bid->project_bid,
          "notice_type"=>$notice_type
        ]);

        $notice=Notice::find($temp->project_bidder_notice_id);
        $notice->mr_due_date=$mr_due_date;
        $notice->notice_type=$notice_type;
        $notice->date_generated=$date_generated;
        $notice->date_released=$date_released;
        $notice->date_received_by_contractor=$date_received_by_contractor;
        $notice->date_received=$date_received_by_bac;
        $notice->notice_remarks=$remarks;
        $notice->bac_id=$bac->bac_id;
        $notice->save();
      }
    }


    if($message==="success"){
      return back()->with(['message'=>$message,'last_received'=>$date_received_by_bac]);
    }
    else{
      return back()->withInput()->with(['message'=>$message,'last_received'=>$date_received_by_bac]);
    }
  }

  public function prepareNoticeOfAward()
  {
    $year=date('Y');
    $title="PREPARE NOTICE OF AWARD";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity('for_notice_of_award',$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.prepare_notice_of_award',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>$title,'project_plans'=>$project_plans,'year'=>$year]);
  }

  public function filterNoticeOfAwards(Request $request)
  {
    $year=$request->project_year;
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity('for_notice_of_award',$year);
    return back()->withInput()->with("project_plans",$project_plans);
  }

  public function generateNotice ($id){

    $bidder_notice=Notice::where('project_bidder_notice_id',$id)->first();

    if($bidder_notice==""||$bidder_notice==null){
      return abort(403,"Undefined Notice");
    }
    else{
      $notice_type = $bidder_notice->notice_type;
      $bac=DB::table('bids_and_awards_committee')->where('bac_id',$bidder_notice->bac_id)->first();
      $bac_chairman=DB::table('member')->select(DB::RAW('upper(LOWER(CONCAT(member_prefix," ",member_fname," ",member_minitial," ",member_lname))) as name'))->where('member_id',$bac->bac_chairman)->first();

      $filename=md5(date('Y-m-d H:i:s:u')).".docx";
      $APP=new APP;

      $cluster_bids=$APP->getClusterBids($bidder_notice->project_bid);
      $contractor=Contractors::find($cluster_bids[0]->contractor_id);
      $title="";
      $letter="A";
      if(count($cluster_bids)>1){
        foreach ($cluster_bids as $cluster_bid) {
          if($title==""){
            $title=$letter.".) ".$cluster_bid->project_title;
          }
          else{
            $title=$title." ".$letter.".) ".$cluster_bid->project_title;
          }
          ++$letter;
        }
      }
      else{
        $title=$cluster_bids[0]->project_title;
      }

      if(count($cluster_bids)>1){
        $project_label="projects";
      }
      else{
        $project_label="project";
      }

      if($cluster_bids[0]->procact_mode_id===1){
        $label="bid";
        $lowest="Lowest Calculated Responsive Bid";
        $rank_label="LCRB";
        $days="five (5)";
      }
      else{
        $label="quotation";
        $lowest="Lowest Calculated Responsive Price Quotation";
        $rank_label="LCRPQ";
        $days="three (3)";
      }

      $name_array=explode(' ',$contractor->owner);
      if(strpos(strtolower(end($name_array)),'jr')===false&&strpos(strtolower(end($name_array)),'sr')===false){
        $last_name=end($name_array);
      }
      else{
        $last_name=$name_array[count($name_array)-2];
      }
      $last_name=ucwords(strtolower($last_name));

      $title=strtoupper(strtolower($title));
      $title= htmlspecialchars($title);
      $business_name=htmlspecialchars($contractor->business_name);

      if($notice_type==="NOPQ"){
        $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/NOPQ.docx");
        // select missing requirements
        if($days==="three (3)"){

          $pbard=DB::table('project_bidder_additional_required_documents')->where('project_bid_id',$cluster_bids[0]->project_bid_id)->first();
          if($pbard==null){
            $missing_docs=DB::table('additional_required_documents')->join('document_types','additional_required_documents.document_type_id','document_types.id')
            ->where('document_types.project_type',"svp")
            ->get();
            $templateProcessor->cloneBlock('svp', 1, true, true);
            $missing_docs_count=count($missing_docs);
            $templateProcessor->cloneBlock('block#1', $missing_docs_count, true, true);
            $count=1;
            foreach ($missing_docs as $missing_doc) {
              $templateProcessor->setValue('days#1',$days);
              if($count===($missing_docs_count-1) && $missing_docs_count>1){
                $templateProcessor->setValue('missing_docs#1#'.$count,$missing_doc->document_type."; and");
              }
              else{
                $templateProcessor->setValue('missing_docs#1#'.$count,$missing_doc->document_type.";");
              }
              $count=$count+1;
            }
          }
          else{

            if($pbard->missing_docs!=null){
              $templateProcessor->cloneBlock('svp', 1, true, true);
              $missing_docs=explode(",",$pbard->missing_docs);
              $missing_docs_count=count($missing_docs);
              $templateProcessor->cloneBlock('block#1', $missing_docs_count, true, true);
              $count=1;
              foreach ($missing_docs as $missing_doc) {
                $templateProcessor->setValue('days#1',$days);
                if($count==($missing_docs_count-1) && $missing_docs_count>1){
                  $templateProcessor->setValue('missing_docs#1#'.$count,$missing_doc."; and");
                }
                else{
                  $templateProcessor->setValue('missing_docs#1#'.$count,$missing_doc.";");
                }
                $count=$count+1;
              }
              // $AdditionalDocumentController=new AdditionalDocumentController;
              // $parameters=["pbard_id"=>$pbard->pbard_id,"project_bid_id"=>$pbard->project_bid_id,"opening_date"=>$date_released,"date_released"=>date("m/d/Y", strtotime($date_released))];
              // $request = new \Illuminate\Http\Request();
              // $request->replace($parameters);
              // $AdditionalDocumentController->submitReleaseNoticeToSubmitDocuments($request);
            }
            else{
              $templateProcessor->cloneBlock('svp', 0, true, true);
            }
          }
        }
        else{
          $templateProcessor->cloneBlock('svp', 0, true, true);
        }
        $templateProcessor->setValue('date_generated', date("F d, Y", strtotime($bidder_notice->date_generated)));
        $templateProcessor->setValue('owner',$contractor->owner);
        $templateProcessor->setValue('last_name',$last_name);
        $templateProcessor->setValue('business_name',$business_name);
        $templateProcessor->setValue('address',$contractor->address);
        $templateProcessor->setValue('project_title',$title);
        $templateProcessor->setValue('position',$contractor->position);
        $templateProcessor->setValue('bid_or_quotation',$label);
        $templateProcessor->setValue('project_label',$project_label);
        $templateProcessor->setValue('lowest',$lowest);
        $templateProcessor->setValue('bac_chairman',$bac_chairman->name);
        $templateProcessor->saveAs(public_path().'\\'.'notice_documents/NOPQ'.$filename);
      }

      if($notice_type==="NOPD"){
        $disqualification=DB::table('disqualification_records')->where([['project_bid',$bidder_notice->project_bid],['remarks','like','Non-responsive:%']])->first();
        $reason=str_replace(  'Non-responsive:','', $disqualification->remarks);
        $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/NOPD.docx");
        $templateProcessor->setValue('date_generated', date("F d, Y", strtotime($bidder_notice->date_generated)));
        $templateProcessor->setValue('owner',$contractor->owner);
        $templateProcessor->setValue('reason',$reason);
        $templateProcessor->setValue('last_name',$last_name);
        $templateProcessor->setValue('business_name',$business_name);
        $templateProcessor->setValue('address',$contractor->address);
        $templateProcessor->setValue('municipality',$cluster_bids[0]->municipality_name);
        $templateProcessor->setValue('project_title',$title);
        $templateProcessor->setValue('position',$contractor->position);
        $templateProcessor->setValue('bid_or_quotation',$label);
        $templateProcessor->setValue('project_label',$project_label);
        $templateProcessor->setValue('bac_chairman',$bac_chairman->name);
        $templateProcessor->saveAs(public_path().'\\'.'notice_documents/NOPD'.$filename);
      }

      if($notice_type==="NOI"){
        $disqualification=DB::table('disqualification_records')->where([['project_bid',$bidder_notice->project_bid],['remarks','like','Ineligible:%']])->orderBy('record_id','desc')->first();
        if($disqualification==null){
          $reason="Please verify reason";
        }
        else{
          $reason=str_replace(  'Disqualified:','', $disqualification->remarks);
        }
        $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/NOI.docx");
        $templateProcessor->setValue('date_generated', date("F d, Y", strtotime($bidder_notice->date_generated)));
        $templateProcessor->setValue('owner',$contractor->owner);
        $templateProcessor->setValue('position',$contractor->position);
        $templateProcessor->setValue('last_name',$last_name);
        $templateProcessor->setValue('business_name',$business_name);
        $templateProcessor->setValue('address',$contractor->address);
        $templateProcessor->setValue('project_title',$title);
        $templateProcessor->setValue('bid_or_quotation',$label);
        $templateProcessor->setValue('project_label',$project_label);
        $templateProcessor->setValue('reason',$reason);
        $templateProcessor->setValue('bac_chairman',$bac_chairman->name);
        $templateProcessor->saveAs(public_path().'\\'.'notice_documents/NOI'.$filename);

      }

      if($notice_type==="NOD"){
        $disqualification=DB::table('disqualification_records')->where([['project_bid',$bidder_notice->project_bid],['remarks','like','Disqualified:%']])->orderBy('record_id','desc')->first();
        if($disqualification==null){
          $reason="Please verify reason";
        }
        else{
          $reason=str_replace(  'Disqualified:','', $disqualification->remarks);
        }




        $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/NOD.docx");
        $templateProcessor->setValue('date_generated', date("F d, Y", strtotime($bidder_notice->date_generated)));
        $templateProcessor->setValue('owner',$contractor->owner);
        $templateProcessor->setValue('position',$contractor->position);
        $templateProcessor->setValue('last_name',$last_name);
        $templateProcessor->setValue('business_name',$business_name);
        $templateProcessor->setValue('address',$contractor->address);
        $templateProcessor->setValue('project_title',$title);
        $templateProcessor->setValue('bid_or_quotation',$label);
        $templateProcessor->setValue('project_label',$project_label);
        $templateProcessor->setValue('reason',$reason);
        $templateProcessor->setValue('bac_chairman',$bac_chairman->name);
        if($contractor->contact_number!=null){
          $templateProcessor->setValue('contact',$contractor->contact_number." \n");
        }
        else{
          $templateProcessor->setValue('contact','');
        }
        $templateProcessor->saveAs(public_path().'\\'.'notice_documents/NOD'.$filename);

      }

      if($notice_type==="NTLB"){

        $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/NTLB.docx");
        $responsive_bidder=$APP->getBiddersData($cluster_bids[0]->procact_id,'responsive');
        $bidders=$APP->getBiddersData($cluster_bids[0]->procact_id,'responsive,active,non-responsive,disapproved,withdrawn');
        $rank=1;
        foreach ($bidders as $bidder) {
          if($bidder->project_bid===$responsive_bidder[0]->project_bid){
            break;
          }
          else{
            $rank=$rank+1;
          }
        }


        $resolution=DB::table('resolutions')->join('resolution_projects','resolutions.resolution_id','resolution_projects.resolution_id')
        ->where([['resolution_projects.procact_id',$cluster_bids[0]->procact_id],['type','RRA']])->first();
        if($resolution!=null){
          $templateProcessor->setValue('resolution_number',$resolution->resolution_number);
          $templateProcessor->setValue('resolution_date', date("F d, Y", strtotime(($resolution->resolution_date))));
        }
        else{
          $templateProcessor->setValue('resolution_number','');
          $templateProcessor->setValue('resolution_date', '');
        }

        $templateProcessor->setValue('responsive',htmlspecialchars($responsive_bidder[0]->business_name));
        $templateProcessor->setValue('bac_chairman',$bac_chairman->name);
        $templateProcessor->setValue('rank', $rank.date("S", mktime(0, 0, 0, 0, $rank, 0)));
        $templateProcessor->setValue('date_generated', date("F d, Y", strtotime($bidder_notice->date_generated)));
        $templateProcessor->setValue('owner',$contractor->owner);
        $templateProcessor->setValue('last_name',$last_name);
        $templateProcessor->setValue('business_name',$business_name);
        $templateProcessor->setValue('address',$contractor->address);
        $templateProcessor->setValue('position',$contractor->position);
        $templateProcessor->setValue('bid_or_quotation',$label);
        $templateProcessor->setValue('project_label',$project_label);
        $templateProcessor->setValue('rank_label',$rank_label);
        $templateProcessor->setValue('project_title',$title);
        $templateProcessor->saveAs(public_path().'\\'.'notice_documents/NTLB'.$filename);
      }

      return  response()->download(public_path().'\\'.'notice_documents/'.$notice_type.$filename)->deleteFileAfterSend(true);

    }

  }


  public function generateNOA($id)
  {
    $APP = new APP;
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $noa=DB::table('notice_of_awards')->where('notice_award_id',$id)->first();
    if($noa==null){
      return abort(403,"Unknown Notice of Award");
    }
    else{
      $cluster_bids=$APP->getClusterBids($noa->project_bid_id);
      $project_plan=DB::table('project_plans')
      ->where('project_bidders.project_bid',$noa->project_bid_id)
      ->select('project_plans.project_bid_id','project_plans.mode_id','governors.name as governor_name','governors.governor_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','barangays.*')
      ->join('procacts','procacts.plan_id','project_plans.plan_id')
      ->join('rfq_projects','rfq_projects.procact_id','procacts.procact_id')
      ->join('project_bidders','project_bidders.rfq_project_id','rfq_projects.rfq_project_id')
      ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
      ->join('contractors','contractors.contractor_id','rfqs.contractor_id')
      ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
      ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
      ->leftJoin('governors','governors.governor_id','project_plans.governor_id')
      ->orderBy('procacts.itb_arrangement','asc')
      ->first();

      if($project_plan==null){
        $project_plan=DB::table('project_plans')
        ->where('project_bidders.project_bid',$noa->project_bid_id)
        ->select('project_plans.project_bid_id','governors.name as governor_name','project_plans.mode_id','governors.governor_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','barangays.*')
        ->join('procacts','procacts.plan_id','project_plans.plan_id')
        ->join('bid_doc_projects','bid_doc_projects.procact_id','procacts.procact_id')
        ->join('project_bidders','project_bidders.bid_doc_project_id','bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
        ->join('contractors','contractors.contractor_id','bid_docs.contractor_id')
        ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
        ->leftJoin('governors','governors.governor_id','project_plans.governor_id')
        ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
        ->orderBy('procacts.itb_arrangement','asc')
        ->first();
      }

      if($project_plan->mode_id===1){
        $label="bid";
        $lowest="Lowest Calculated Responsive Bid";
        $rank_label="LCRB";
        $bid_or_quoted="bid";
        $and_bid=" and forfeiture of your Bid Security";
      }
      else{
        $label="quotation";
        $lowest="Lowest Calculated Responsive Price Quotation";
        $rank_label="LCRPQ";
        $bid_or_quoted="quoted";
        $and_bid="";
      }

      $bidders=$APP->getBiddersData($project_plan->procact_id,"responsive,active,withdrawn,non-responsive");
      if(count($bidders)===1){
        $lone_or_lowest="Lone/".$lowest;
      }
      else{
        $lone_or_lowest=$lowest;
      }




      if($project_plan->governor_id=null){
        $governor_name=$project_plan->governor_name;
      }
      else{
        $governor=DB::table('governors')->orderBy('governor_id','desc')->first();
        $governor_name=$governor->name;
        foreach ($cluster_bids as $cluster_bid) {
          DB::table('project_plans')->where('plan_id',$cluster_bid->plan_id)->update([
            "governor_id"=>$governor->governor_id
          ]);
        }

      }

      $barangay="";

      $barangay_ids = array_column((array)json_decode($cluster_bids), 'barangay_id');
      $title="";
      $cluster_bids_string="";
      $letter="A";
      if(count($cluster_bids)>1){
        foreach ($cluster_bids as $cluster_bid) {
          if($title==""){
            $title=$letter.".) ".$cluster_bid->project_title;
          }
          else{
            $title=$title." ".$letter.".) ".$cluster_bid->project_title;
          }
          if($cluster_bid->minimum_detailed_cost>0){
            $bid_in_words=strtoupper($formatter->format((int)$cluster_bid->minimum_detailed_cost))." PESOS";
            $decimal=$cluster_bid->minimum_detailed_cost-(int)$cluster_bid->minimum_detailed_cost;
            $decimal=number_format($decimal,2,'.',',');
            $converted_to_decimal=$decimal;
            $decimal=str_replace('0.','',$decimal);
            if((int)$decimal>=1){
              $bid_in_words=$bid_in_words." AND ".strtoupper($formatter->format((int)$decimal))." CENTAVOS";
            }

            if($cluster_bids_string==""){
              $cluster_bids_string=$letter.".) ".$bid_in_words." (Php ".number_format((float)$cluster_bid->minimum_detailed_cost,2,'.',',').")";
            }
            else{
              $cluster_bids_string=$cluster_bids_string." ".$letter.".) ".$bid_in_words." ( Php ".number_format((float)$cluster_bid->minimum_detailed_cost,2,'.',',').")";
            }
          }
          ++$letter;
        }
      }
      else{
        $title=$cluster_bids[0]->project_title;
      }

      if(count($cluster_bids)>1){
        $project_label="projects";
      }
      else{
        $project_label="project";
      }

      $name_array=explode(' ',$project_plan->owner);
      if(strpos(strtolower(end($name_array)),'jr')===false&&strpos(strtolower(end($name_array)),'sr')===false){
        $last_name=end($name_array);
      }
      else{
        $last_name=$name_array[count($name_array)-2];
      }
      if($project_plan->barangay_name!=null){
        if(count(array_unique($barangay_ids))==1){
          $barangay=$project_plan->barangay_name.',';
        }
        else{
          $barangay="";
        }
      }

      $responsive_bidder=$APP->getBiddersData($project_plan->procact_id,'responsive');
      $bid=$responsive_bidder[0]->final_minimum_cost;
      $bid_in_words=strtoupper($formatter->format((int)$responsive_bidder[0]->final_minimum_cost))." PESOS";
      $decimal=$responsive_bidder[0]->final_minimum_cost-(int)$responsive_bidder[0]->final_minimum_cost;
      $decimal=number_format($decimal,2,'.',',');
      $decimal=str_replace('0.','',$decimal);
      if((int)$decimal>=1){
        $bid_in_words=$bid_in_words." AND ".strtoupper($formatter->format((int)$decimal))." CENTAVOS";
      }


      $title=strtoupper(strtolower($title));
      $title= htmlspecialchars($title);
      $business_name=htmlspecialchars($project_plan->business_name);


      $filename='NOA'.md5(date('Y-m-d H:i:s:u')).".docx";
      $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/NOA.docx");
      $templateProcessor->setValue('date_generated', date("F d, Y", strtotime($noa->date_generated)));
      $templateProcessor->setValue('position',$project_plan->position);
      $templateProcessor->setValue('project_label',$project_label);
      $templateProcessor->setValue('project_title',$title);
      $templateProcessor->setValue('bid_or_quoted',$bid_or_quoted);
      $templateProcessor->setValue('and_bid',$and_bid);
      $templateProcessor->setValue('owner',strtoupper(strtolower($project_plan->owner)));
      $templateProcessor->setValue('last_name',$last_name);
      $templateProcessor->setValue('business_name',$business_name);
      $templateProcessor->setValue('barangay',$barangay);
      $templateProcessor->setValue('municipality',$project_plan->municipality_name);
      $templateProcessor->setValue('address',$project_plan->address);
      $templateProcessor->setValue('project_title',strtoupper(strtolower($title)));
      if($cluster_bids_string!=""){
        $templateProcessor->setValue('bid',$cluster_bids_string);
        $templateProcessor->setValue('bid_in_words','');
      }
      else{
        $templateProcessor->setValue('bid',"(Php".number_format((float)$bid,2,'.',',').")");
        $templateProcessor->setValue('bid_in_words',$bid_in_words);
      }
      $templateProcessor->setValue('lowest',$lone_or_lowest);
      $templateProcessor->setValue('governor',strtoupper(strtolower($governor_name)));
      $templateProcessor->saveAs(public_path().'\\'.'word_results/'.$filename);
      return  response()->download(public_path().'\\'.'word_results/'.$filename)->deleteFileAfterSend(true);

    }
  }

  public function prepareNoticeToProceed(Request $request)
  {
    $year=$request->project_year;
    $APP = new APP;
    if($year!=null){
      $project_plans=$APP->getSpecificProcurementActivity('for_notice_to_proceed',$year);
      return back()->withInput()->with("project_plans",$project_plans);
    }
    else{
      $year=date('Y');
      $title="PREPARE NOTICE TO PROCEED";
      $project_plans=$APP->getSpecificProcurementActivity('for_notice_to_proceed',$year);
      $links=getUserLinks();
      $user_privilege=getUserPrivilege();
      return view('admin.prepare_notice_to_proceed',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>$title,'project_plans'=>$project_plans,'year'=>$year]);
    }


  }

  public function generateNTP($id)
  {
    $APP = new APP;
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $ntp=NoticeToProceed::find($id);
    if($ntp==null){
      return abort(403,"Unknown Notice to Proceed");
    }
    else{
      $cluster_bids=$APP->getClusterBids($ntp->project_bid_id);
      $project_plan=DB::table('project_plans')
      ->where('project_bidders.project_bid',$ntp->project_bid_id)
      ->select('project_plans.project_bid_id','governors.name as governor_name','governors.governor_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','barangays.*')
      ->join('procacts','procacts.plan_id','project_plans.plan_id')
      ->join('rfq_projects','rfq_projects.procact_id','procacts.procact_id')
      ->join('project_bidders','project_bidders.rfq_project_id','rfq_projects.rfq_project_id')
      ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
      ->join('contractors','contractors.contractor_id','rfqs.contractor_id')
      ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
      ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
      ->leftJoin('governors','governors.governor_id','project_plans.governor_id')
      ->orderBy('procacts.itb_arrangement','asc')
      ->first();

      if($project_plan==null){
        $project_plan=DB::table('project_plans')
        ->where('project_bidders.project_bid',$ntp->project_bid_id)
        ->select('project_plans.project_bid_id','governors.name as governor_name','governors.governor_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','barangays.*')
        ->join('procacts','procacts.plan_id','project_plans.plan_id')
        ->join('bid_doc_projects','bid_doc_projects.procact_id','procacts.procact_id')
        ->join('project_bidders','project_bidders.bid_doc_project_id','bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
        ->join('contractors','contractors.contractor_id','bid_docs.contractor_id')
        ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
        ->leftJoin('governors','governors.governor_id','project_plans.governor_id')
        ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
        ->orderBy('procacts.itb_arrangement','asc')
        ->first();
      }


      $governor_name=$project_plan->governor_name;
      $barangay="";

      $barangay_ids = array_column((array)json_decode($cluster_bids), 'barangay_id');
      $title="";
      $duration=0;
      $source="";
      $letter="A";
      $cluster_bids_string="";

      if(count($cluster_bids)>1){
        foreach ($cluster_bids as $cluster_bid) {
          $duration=$duration+$cluster_bid->duration;
          if($title==""){
            $title=$letter.".) ".$cluster_bid->project_title;
          }
          else{
            $title=$title." ".$letter.".) ".$cluster_bid->project_title;
          }
          if($source==""){
            $source=$letter.".) ".$cluster_bid->source;
          }
          else{
            $source=$source." ".$letter.".) ".$cluster_bid->source;
          }

          if($cluster_bid->minimum_detailed_cost>0){
            if($cluster_bids_string==""){
              $cluster_bids_string=$letter.".) Php ".number_format((float)$cluster_bid->minimum_detailed_cost,2,'.',',');
            }
            else{
              $cluster_bids_string=$cluster_bids_string." ".$letter.".) Php ".number_format((float)$cluster_bid->minimum_detailed_cost,2,'.',',');
            }
          }
          ++$letter;
        }
      }
      else{
        $title=$cluster_bids[0]->project_title;
        $source=$cluster_bids[0]->source;
        $duration=$cluster_bids[0]->duration;
      }


      if(count($cluster_bids)>1){
        $project_label="projects";
      }
      else{
        $project_label="project";
      }

      $name_array=explode(' ',$project_plan->owner);
      if(strpos(strtolower(end($name_array)),'jr')===false&&strpos(strtolower(end($name_array)),'sr')===false){
        $last_name=end($name_array);
      }
      else{
        $last_name=$name_array[count($name_array)-2];
      }
      if($project_plan->barangay_name!=null){
        if(count(array_unique($barangay_ids))==1){
          $barangay=$project_plan->barangay_name.',';
        }
        else{
          $barangay="";
        }
      }

      $responsive_bidder=$APP->getBiddersData($project_plan->procact_id,'responsive');
      $bid=$responsive_bidder[0]->final_minimum_cost;
      $bid_in_words=strtoupper($formatter->format((int)$responsive_bidder[0]->final_minimum_cost))." PESOS";
      $decimal=$responsive_bidder[0]->final_minimum_cost-(int)$responsive_bidder[0]->final_minimum_cost;
      $decimal=number_format($decimal,2,'.',',');
      $decimal=str_replace('0.','',$decimal);
      if((int)$decimal>=1){
        $bid_in_words=$bid_in_words." AND ".strtoupper($formatter->format((int)$decimal))." CENTAVOS";
      }

      $title=strtoupper(strtolower($title));
      $title= htmlspecialchars($title);
      $business_name=htmlspecialchars($project_plan->business_name);

      $filename='NTP'.md5(date('Y-m-d H:i:s:u')).".docx";
      $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/NTP.docx");
      $templateProcessor->setValue('date_released', date("F d, Y", strtotime($ntp->ntp_date_generated)));
      $templateProcessor->setValue('position',$project_plan->position);
      $templateProcessor->setValue('project_label',$project_label);
      $templateProcessor->setValue('project_title',$title);
      $templateProcessor->setValue('owner',strtoupper(strtolower($project_plan->owner)));
      $templateProcessor->setValue('last_name',$last_name);
      $templateProcessor->setValue('duration',$duration);
      $templateProcessor->setValue('source',$source);
      $templateProcessor->setValue('business_name',$business_name);
      $templateProcessor->setValue('barangay',$barangay);
      $templateProcessor->setValue('municipality',$project_plan->municipality_name);
      $templateProcessor->setValue('address',$project_plan->address);
      $templateProcessor->setValue('project_title',strtoupper(strtolower($title)));
      if($cluster_bids_string!=""){
        $templateProcessor->setValue('bid',$cluster_bids_string);
      }
      else{
        $templateProcessor->setValue('bid',"Php".number_format((float)$bid,2,'.',','));
      }

      $templateProcessor->setValue('bid_in_words',$bid_in_words);
      $templateProcessor->setValue('governor',strtoupper(strtolower($governor_name)));
      if($ntp->ntp_receive_date!=null){
        $templateProcessor->setValue('release_date',date("F d, Y", strtotime($ntp->ntp_receive_date)));
        $templateProcessor->setValue('project_start',date("F d, Y", strtotime($ntp->ntp_receive_date."+6 days")));
        $templateProcessor->setValue('target_date',date("F d, Y", strtotime($ntp->ntp_receive_date."+".($duration+6)." days")));
      }
      else{
        $templateProcessor->setValue('project_start',"_____");
        $templateProcessor->setValue('target_date',"______");
        $templateProcessor->setValue('release_date',"______");
      }

      $templateProcessor->saveAs(public_path().'\\'.'word_results/'.$filename);
      return  response()->download(public_path().'\\'.'word_results/'.$filename)->deleteFileAfterSend(true);

    }
  }


  public function getNoticeToSubmitPostQualDocs()
  {
    $title="Post Qual Docs Checklist";
    $year=date('Y');
    $APP=new App;
    $project_plans=$APP->getNoticeToSubmitPostQualDocs(null,$year,false);

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('twg.post_qual_docs_checklist',['links'=>$links,'user_privilege'=>$user_privilege,'project_plans'=>$project_plans,"title"=>$title,"year"=>$year]);
  }

  public function getAdminNTSPQD()
  {
    $title="Notice To Submit Post Qualification Documents";
    $year=date('Y');
    $APP=new App;
    $project_plans=$APP->getNoticeToSubmitPostQualDocs(null,$year,false);

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view('admin.post_qual_docs_checklist',['links'=>$links,'user_privilege'=>$user_privilege,'project_plans'=>$project_plans,"title"=>$title,"year"=>$year]);
  }

  public function submitNoticeToSubmitPostQualDocs(Request $request)
  {
    $APP=new APP;

    $ntspqd_id=$request->input('ntspqd_id');
    $project_bid=$request->input('project_bid');
    $date_received=$request->input('date_received');
    if($date_received!=null){
      $date_received=date("Y-m-d",strtotime($request->input('date_received')));
    }
    $ntspqd_status="complete";
    $message="success";
    // validate checklists
    if($request->has('latest_income_and_business_tax')){
      $latest_income_and_business_tax=true;
    }
    else{
      $latest_income_and_business_tax=false;
      $ntspqd_status="incomplete";
    }

    if($request->has('provincial_permit')){
      $provincial_permit=true;
    }
    else{
      $provincial_permit=false;
      $ntspqd_status="incomplete";
    }
    if($request->has('printed_copy_of_the_itb')){
      $printed_copy_of_the_itb=true;
    }
    else{
      $printed_copy_of_the_itb=false;
      $ntspqd_status="incomplete";
    }
    if($request->has('construction_of_schedule_and_s_curve')){
      $construction_of_schedule_and_s_curve=true;
    }
    else{
      $construction_of_schedule_and_s_curve=false;
      $ntspqd_status="incomplete";
    }
    if($request->has('manpower_schedule')){
      $manpower_schedule=true;
    }
    else{
      $manpower_schedule=false;
      $ntspqd_status="incomplete";
    }
    if($request->has('construction_methods')){
      $construction_methods=true;
    }
    else{
      $construction_methods=false;
      $ntspqd_status="incomplete";
    }
    if($request->has('equipment_utilization_schedule')){
      $equipment_utilization_schedule=true;
    }
    else{
      $equipment_utilization_schedule=false;
      $ntspqd_status="incomplete";
    }
    if($request->has('construction_safety_and_health_programs')){
      $construction_safety_and_health_programs=true;
    }
    else{
      $construction_safety_and_health_programs=false;
      $ntspqd_status="incomplete";
    }

    $cluster_bids=$APP->getClusterBids($project_bid);

    if($ntspqd_id==null){
      $duplicate=DB::table('notice_to_submit_post_qual_docs')->where('project_bid_id',$project_bid)->first();
      if($duplicate!=null){
        $message="duplicate";
      }
      else{
        foreach ($cluster_bids as $cluster_bid) {
          $insert=DB::table('notice_to_submit_post_qual_docs')
          ->insert([
            "project_bid_id"=>$cluster_bid->project_bid,
            "ntspqd_status"=>$ntspqd_status,
            "latest_income_business_tax"=>$latest_income_and_business_tax,
            "provincial_permit"=>$provincial_permit,
            "itb_copy"=>$printed_copy_of_the_itb,
            "schedule_and_scurve"=>$construction_of_schedule_and_s_curve,
            "manpower_schedule"=>$manpower_schedule,
            "construction_methods"=>$construction_methods,
            "equipment_utilization_schedule"=>$equipment_utilization_schedule,
            "construction_safety_health_programs"=>$construction_safety_and_health_programs,
            "date_released"=>date("Y-m-d",strtotime($request->date_of_opening)),
            "created_at"=>now(),
            "updated_at"=>now()
          ]);
        }
      }

    }
    else{
      $duplicate=DB::table('notice_to_submit_post_qual_docs')->where([['project_bid_id',$project_bid],['ntspqd_id','<>',$ntspqd_id]])->first();
      if($duplicate!=null){
        $message="duplicate";
      }
      else{
        foreach ($cluster_bids as $cluster_bid) {
          // code...

          $insert=DB::table('notice_to_submit_post_qual_docs')
          ->where('project_bid_id',$cluster_bid->project_bid)
          ->update([
            "ntspqd_status"=>$ntspqd_status,
            "latest_income_business_tax"=>$latest_income_and_business_tax,
            "provincial_permit"=>$provincial_permit,
            "itb_copy"=>$printed_copy_of_the_itb,
            "schedule_and_scurve"=>$construction_of_schedule_and_s_curve,
            "manpower_schedule"=>$manpower_schedule,
            "construction_methods"=>$construction_methods,
            "equipment_utilization_schedule"=>$equipment_utilization_schedule,
            "construction_safety_health_programs"=>$construction_safety_and_health_programs,
            "date_released"=>date("Y-m-d",strtotime($request->date_of_opening)),
            "date_received"=>$date_received,
            "updated_at"=>now()
          ]);
        }
      }
    }

    return back()->with("message",$message);
  }


  public function generateNTSPQD($id)
  {
    $APP=new APP;
    $ntspqd=DB::table('notice_to_submit_post_qual_docs')->where('ntspqd_id',$id)->first();
    if($ntspqd==null){
      return abort(403,"Unknown Notice to Submit Post Qualification Documents");
    }
    else{
      $missing_docs=[];
      $title="";
      $cluster_bids=$APP->getClusterBids($ntspqd->project_bid_id);
      $project_plan=DB::table('project_plans')
      ->where('project_bidders.project_bid',$ntspqd->project_bid_id)
      ->select('project_plans.project_bid_id','project_timelines.bid_submission_start','governors.name as governor_name','governors.governor_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','barangays.*')
      ->join('procacts','procacts.plan_id','project_plans.plan_id')
      ->join('rfq_projects','rfq_projects.procact_id','procacts.procact_id')
      ->join('project_bidders','project_bidders.rfq_project_id','rfq_projects.rfq_project_id')
      ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
      ->join('contractors','contractors.contractor_id','rfqs.contractor_id')
      ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
      ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
      ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
      ->leftJoin('governors','governors.governor_id','project_plans.governor_id')
      ->orderBy('procacts.itb_arrangement','asc')
      ->first();

      if($project_plan==null){
        $project_plan=DB::table('project_plans')
        ->where('project_bidders.project_bid',$ntspqd->project_bid_id)
        ->select('project_plans.project_bid_id','project_timelines.bid_submission_start','governors.name as governor_name','governors.governor_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','barangays.*')
        ->join('procacts','procacts.plan_id','project_plans.plan_id')
        ->join('bid_doc_projects','bid_doc_projects.procact_id','procacts.procact_id')
        ->join('project_bidders','project_bidders.bid_doc_project_id','bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
        ->join('contractors','contractors.contractor_id','bid_docs.contractor_id')
        ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
        ->leftJoin('governors','governors.governor_id','project_plans.governor_id')
        ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
        ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
        ->orderBy('procacts.itb_arrangement','asc')
        ->first();
      }


      foreach ($cluster_bids as $cluster_bid) {
        if($title==""){
          $title=$cluster_bid->project_title;
        }
        else{
          $title=$title.' And '.$cluster_bid->project_title;
        }
      }

      if(count($cluster_bids)>1){
        $project_label="projects";
      }
      else{
        $project_label="project";
      }

      $name_array=explode(' ',$project_plan->owner);
      if(strpos(strtolower(end($name_array)),'jr')===false&&strpos(strtolower(end($name_array)),'sr')===false){
        $last_name=end($name_array);
      }
      else{
        $last_name=$name_array[count($name_array)-2];
      }

      $bidders=$APP->getBiddersData($project_plan->procact_id,'responsive,active,non-responsive,disapproved,withdrawn');
      $rank=1;
      foreach ($bidders as $bidder) {
        if($bidder->project_bid===$ntspqd->project_bid_id){
          break;
        }
        else{
          $rank=$rank+1;
        }
      }

      if(count($bidders)>1){
        $rank="the ".$rank.date("S", mktime(0, 0, 0, 0, $rank, 0))." Lowest Calculated Bid (LCB)";
      }
      else{
        $rank="the Lone Bidder";
      }

      if($cluster_bids[0]->mode_id==1||$cluster_bids[0]->mode_id==3){
        $days="five (5)";
      }
      else{
        $days="three (3)";
      }

      if($ntspqd->latest_income_business_tax===0){
        array_push($missing_docs,'Latest Income and Business Tax Return');
      }
      if($ntspqd->provincial_permit===0){
        array_push($missing_docs,'Provincial Permit');
      }
      if($ntspqd->itb_copy===0){
        array_push($missing_docs,'Printed Copy of the Invitation to Bid @Philgeps');
      }
      if($ntspqd->schedule_and_scurve===0){
        array_push($missing_docs,'Construction of Schedule and S-curve');
      }
      if($ntspqd->manpower_schedule===0){
        array_push($missing_docs,'Manpower Schedule');
      }
      if($ntspqd->construction_methods===0){
        array_push($missing_docs,'Construction Methods');
      }
      if($ntspqd->equipment_utilization_schedule===0){
        array_push($missing_docs,'Equipment Utilization Schedule');
      }
      if($ntspqd->construction_safety_health_programs===0){
        array_push($missing_docs,'Construction Safety and Health Program');
      }

      $title=strtoupper(strtolower($title));
      $title= htmlspecialchars($title);
      $business_name=htmlspecialchars($project_plan->business_name);
      $filename='NTSPQD'.md5(date('Y-m-d H:i:s:u')).".docx";
      $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/NTSPQD.docx");
      $templateProcessor->cloneBlock('block', count($missing_docs), true, true);
      $templateProcessor->setValue('days',$days);
      $templateProcessor->setValue('position',$project_plan->position);
      $templateProcessor->setValue('address',$project_plan->address);
      $templateProcessor->setValue('project_title',$title);
      $templateProcessor->setValue('rank',$rank);
      $templateProcessor->setValue('owner',strtoupper(strtolower($project_plan->owner)));
      $templateProcessor->setValue('last_name',$last_name);
      $templateProcessor->setValue('business_name',$business_name);
      $templateProcessor->setValue('date_now',date("F d, Y",strtotime($ntspqd->date_released)));
      $missing_docs_count=count($missing_docs);

      $count=1;
      foreach ($missing_docs as $missing_doc) {
        $templateProcessor->setValue('missing_docs#'.$count,$missing_doc);
        $count=$count+1;
      }

      $templateProcessor->saveAs(public_path().'\\'.'word_results/'.$filename);
      return  response()->download(public_path().'\\'.'word_results/'.$filename)->deleteFileAfterSend(true);
    }
  }

  public function generateProjectBiddersAdditionalRequiredDocuments($id)
  {
    $APP=new APP;
    $pbard=DB::table('project_bidder_additional_required_documents')->where('pbard_id',$id)->first();
    if($pbard==null){
      return abort(403,"Unknown Notice ");
    }
    else{
      $bac = DB::table('bids_and_awards_committee')
        ->select(
          'bids_and_awards_committee.*',
          DB::raw("CONCAT(bac_ch.member_prefix,' ',bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name_prefix"),
          DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
          DB::raw("CONCAT(bac_vice_ch.member_prefix,' ',bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name_prefix"),
          DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
          DB::raw("CONCAT(bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
          DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
          DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
          DB::raw("CONCAT(bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname) AS bac_twg_chairman_name"),
          DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
        )
        ->join('member as bac_ch', 'bac_ch.member_id', '=', 'bids_and_awards_committee.bac_chairman')
        ->join('member as bac_vice_ch', 'bac_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_vice_chairman')
        ->leftJoin('member as bac_alternate_vice_ch', 'bac_alternate_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_alternate_vice_chairman')
        ->join('member as bac_sec_ch', 'bac_sec_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_chairman')
        ->join('member as bac_sec_vice_ch', 'bac_sec_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_vice_chairman')
        ->join('member as bac_twg_ch', 'bac_twg_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_chairman')
        ->join('member as bac_twg_vice_ch', 'bac_twg_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_vice_chairman')
        ->orderBy('bac_id', 'desc')
        ->first();

      $missing_docs=[];
      $title="";
      $cluster_bids=$APP->getClusterBids($pbard->project_bid_id);
      $project_plan=DB::table('project_plans')
      ->where('project_bidders.project_bid',$pbard->project_bid_id)
      ->select('project_plans.mode_id','project_plans.project_bid_id','project_timelines.bid_submission_start','governors.name as governor_name','governors.governor_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','barangays.*')
      ->join('procacts','procacts.plan_id','project_plans.plan_id')
      ->join('rfq_projects','rfq_projects.procact_id','procacts.procact_id')
      ->join('project_bidders','project_bidders.rfq_project_id','rfq_projects.rfq_project_id')
      ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
      ->join('contractors','contractors.contractor_id','rfqs.contractor_id')
      ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
      ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
      ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
      ->leftJoin('governors','governors.governor_id','project_plans.governor_id')
      ->orderBy('procacts.itb_arrangement','asc')
      ->first();

      if($project_plan==null){
        $project_plan=DB::table('project_plans')
        ->where('project_bidders.project_bid',$pbard->project_bid_id)
        ->select('project_plans.mode_id','project_plans.project_bid_id','project_timelines.bid_submission_start','governors.name as governor_name','governors.governor_id','procacts.plan_id','procacts.procact_id','procacts.plan_cluster_id','municipalities.municipality_name','project_plans.project_title','contractors.*','barangays.*')
        ->join('procacts','procacts.plan_id','project_plans.plan_id')
        ->join('bid_doc_projects','bid_doc_projects.procact_id','procacts.procact_id')
        ->join('project_bidders','project_bidders.bid_doc_project_id','bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
        ->join('contractors','contractors.contractor_id','bid_docs.contractor_id')
        ->leftJoin('barangays','project_plans.barangay_id','barangays.barangay_id')
        ->leftJoin('governors','governors.governor_id','project_plans.governor_id')
        ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
        ->join('municipalities','municipalities.municipality_id','project_plans.municipality_id')
        ->orderBy('procacts.itb_arrangement','asc')
        ->first();
      }

      if($project_plan->mode_id==1){
        $rank_label="Lowest Calculated Bid (LCB)";
        $bid_or_quotation="bid";
      }
      else{
        $rank_label="Lowest Calculated Price Quotation (LCPQ)";
        $bid_or_quotation="price quotation";
      }


      foreach ($cluster_bids as $cluster_bid) {
        if($title==""){
          $title=$cluster_bid->project_title;
        }
        else{
          $title=$title.' And '.$cluster_bid->project_title;
        }
      }

      if(count($cluster_bids)>1){
        $project_label="projects";
      }
      else{
        $project_label="project";
      }

      $name_array=explode(' ',$project_plan->owner);
      if(strpos(strtolower(end($name_array)),'jr')===false&&strpos(strtolower(end($name_array)),'sr')===false){
        $last_name=end($name_array);
      }
      else{
        $last_name=$name_array[count($name_array)-2];
      }

      $bidders=$APP->getBiddersData($project_plan->procact_id,'responsive,active,non-responsive,disapproved,withdrawn');
      $rank=1;
      foreach ($bidders as $bidder) {
        if($bidder->project_bid===$pbard->project_bid_id){
          break;
        }
        else{
          $rank=$rank+1;
        }
      }

      if(count($bidders)>1){
        $rank="the ".$rank.date("S", mktime(0, 0, 0, 0, $rank, 0)).$rank_label;
      }
      else{
        if($project_plan->mode_id==1){
          $rank="the Lone Bidder";
        }
        else{
          $rank="the Lone Price Quotation";
        }
      }

      if($cluster_bids[0]->mode_id==1||$cluster_bids[0]->mode_id==3){
        $days="five (5)";
      }
      else{
        $days="three (3)";
      }

      $missing_docs=explode(",",$pbard->missing_docs);

      
      $title=strtoupper(strtolower($title));
      $title= htmlspecialchars($title);
      $business_name=htmlspecialchars($project_plan->business_name);
      $filename='NoticeToSubmitDocs'.md5(date('Y-m-d H:i:s:u')).".docx";
      $templateProcessor = new TemplateProcessor(public_path().'\\'."word_templates/pbard.docx");
      $missing_docs_count = count($missing_docs);
      
      if($pbard->missing_docs===null){
        $templateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/pbard2.docx");
      }
      $templateProcessor->setValue('bac_sec', strtoupper(strtolower($bac->bac_sec_chairman_name)));
      $templateProcessor->cloneBlock('block', count($missing_docs), true, true);
      $templateProcessor->setValue('days',$days);
      $templateProcessor->setValue('position',$project_plan->position);
      $templateProcessor->setValue('address',$project_plan->address);
      $templateProcessor->setValue('project_title',$title);
      $templateProcessor->setValue('bid_or_quotation',$bid_or_quotation);
      $templateProcessor->setValue('rank',$rank);
      $templateProcessor->setValue('owner',strtoupper(strtolower($project_plan->owner)));
      $templateProcessor->setValue('last_name',$last_name);
      $templateProcessor->setValue('business_name',$business_name);
      $templateProcessor->setValue('date_now',date("F d, Y",strtotime($pbard->date_created)));
      
      $count=1;
      foreach ($missing_docs as $missing_doc) {
        $templateProcessor->setValue('missing_docs#'.$count,$missing_doc);
        $count=$count+1;
      }

      $templateProcessor->saveAs(public_path().'\\'.'word_results/'.$filename);
      return  response()->download(public_path().'\\'.'word_results/'.$filename)->deleteFileAfterSend(true);
    }
  }

  public function releaseNoticeToSubmit()
  {

  }

  public function prepareNoticeOfDisqualification(Request $request)
  {
    $year=date('Y');
    $notice_type="NOD";
    $project_bidders=prepare_notice($notice_type,"all",$year);
    $title="Prepare Notice of Disqualification";

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view("admin.project_bidder_notice",['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_bidders"=>$project_bidders,"notice_type"=>$notice_type,"year"=>$year]);

  }


  public function prepareNoticeOfIneligibility(Request $request)
  {
    $year=date('Y');
    $notice_type="NOI";
    $project_bidders=prepare_notice($notice_type,"all",$year);
    $title="Prepare Notice of Ineligibility";

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view("admin.project_bidder_notice",['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_bidders"=>$project_bidders,"notice_type"=>$notice_type,"year"=>$year]);

  }

  public function prepareNoticeOfPostDisqualification(Request $request)
  {

    $year=date('Y');
    $notice_type="NOPD";
    $project_bidders=prepare_notice($notice_type,"all",$year);
    $title="Prepare Notice of Post Disqualification";

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view("admin.project_bidder_notice",['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_bidders"=>$project_bidders,"notice_type"=>$notice_type,"year"=>$year]);

  }

  public function prepareNoticeOfPostQualification(Request $request)
  {
    $year=date('Y');
    $notice_type="NOPQ";
    $project_bidders=prepare_notice($notice_type,"all",$year);
    $title="Prepare Notice of Post Qualification";

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view("admin.project_bidder_notice",['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_bidders"=>$project_bidders,"notice_type"=>$notice_type,"year"=>$year]);

  }

  public function prepareNoticeToLosingBidder(Request $request)
  {
    $year=date('Y');
    $notice_type="NTLB";
    $project_bidders=prepare_notice($notice_type,"all",$year);
    $title="Prepare Notice to Losing Bidder";

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();
    return view("admin.project_bidder_notice",['links'=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_bidders"=>$project_bidders,"notice_type"=>$notice_type,"year"=>$year]);

  }


  public function filterBidderNotice(Request $request)
  {
    $project_bidders=prepare_notice($request->filter_notice_type,$request->notice_status,$request->year);
    return back()->withInput()->with('project_bidders',$project_bidders);
  }

}
