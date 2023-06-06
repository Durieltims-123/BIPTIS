<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use App\APP;
use Validator;
use Exception;

class MotionForReconsiderationController extends Controller
{

  public function getMotionForReconsiderations(Request $request)
  {
    $year=date('Y');
    $motion_for_reconsiderations_rfqs=DB::table("motion_for_reconsideration")
    ->where('motion_for_reconsideration.mr_date_received','like',$year.'%')
    ->whereIn('project_bidder_notices.notice_type',['NOI','NOPD','NOD','NOPQ'])
    ->select("*","motion_for_reconsideration_project_bid.project_bid_id as mr_project_bid_id")
    ->join('motion_for_reconsideration_project_bid','motion_for_reconsideration_project_bid.mr_id','motion_for_reconsideration.mr_id')
    ->join('project_bidders','motion_for_reconsideration_project_bid.project_bid_id','project_bidders.project_bid')
    ->join('project_bidder_notices','project_bidders.project_bid','project_bidder_notices.project_bid')
    ->join('rfq_projects','project_bidders.rfq_project_id','rfq_projects.rfq_project_id')
    ->join('procacts','procacts.procact_id','rfq_projects.procact_id')
    ->join('project_plans','project_plans.plan_id','procacts.plan_id')
    ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
    ->join('contractors','rfqs.contractor_id','contractors.contractor_id')
    // ->join("motion_for_reconsideration_attachments","motion_for_reconsideration.mr_id","motion_for_reconsideration_attachments.mr_id")
    ->get();

    $motion_for_reconsiderations_bid_docs=DB::table("motion_for_reconsideration")
    ->where('motion_for_reconsideration.mr_date_received','like',$year.'%')
    ->whereIn('project_bidder_notices.notice_type',['NOI','NOPD','NOD','NOPQ'])
    ->select("*","motion_for_reconsideration_project_bid.project_bid_id as mr_project_bid_id")
    ->join('motion_for_reconsideration_project_bid','motion_for_reconsideration_project_bid.mr_id','motion_for_reconsideration.mr_id')
    ->join('project_bidders','motion_for_reconsideration_project_bid.project_bid_id','project_bidders.project_bid')
    ->join('project_bidder_notices','project_bidders.project_bid','project_bidder_notices.project_bid')
    ->join('bid_doc_projects','project_bidders.bid_doc_project_id','bid_doc_projects.bid_doc_project_id')
    ->join('procacts','procacts.procact_id','bid_doc_projects.procact_id')
    ->join('project_plans','project_plans.plan_id','procacts.plan_id')
    ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
    ->join('contractors','bid_docs.contractor_id','contractors.contractor_id')
    // ->join("motion_for_reconsideration_attachments","motion_for_reconsideration.mr_id","motion_for_reconsideration_attachments.mr_id")
    ->get();

    $motion_for_reconsiderations=[];

    if(count($motion_for_reconsiderations_rfqs)>=1 && count($motion_for_reconsiderations_bid_docs)>=1){
      $motion_for_reconsiderations=array_merge((array) json_decode($motion_for_reconsiderations_rfqs), (array) json_decode($motion_for_reconsiderations_bid_docs));
    }
    else if(count($motion_for_reconsiderations_rfqs)===0 && count($motion_for_reconsiderations_bid_docs)>=1){
      $motion_for_reconsiderations=$motion_for_reconsiderations_bid_docs;
    }
    else if(count($motion_for_reconsiderations_rfqs)>=1 && count($motion_for_reconsiderations_bid_docs)===0){
      $motion_for_reconsiderations=$motion_for_reconsiderations_rfqs;
    }
    else{

    }
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view('admin.motion_for_reconsideration',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>'Motion For Reconsideration','year'=>$year,'motion_for_reconsiderations'=>$motion_for_reconsiderations]);
  }

  public function filterMotionForReconsiderations(Request $request)
  {
    $data=$request->validate([
      "year"=>'required|digits:4|integer|min:2020|max:'.(date('Y')),
    ]);
    $year=$request->year;
    $motion_for_reconsiderations_rfqs=DB::table("motion_for_reconsideration")
    ->where('motion_for_reconsideration.mr_date_received','like',$year.'%')
    ->whereIn('project_bidder_notices.notice_type',['NOI','NOPD','NOD'])
    ->select("*","motion_for_reconsideration_project_bid.project_bid_id as mr_project_bid_id")
    ->join('motion_for_reconsideration_project_bid','motion_for_reconsideration_project_bid.mr_id','motion_for_reconsideration.mr_id')
    ->join('project_bidders','motion_for_reconsideration_project_bid.project_bid_id','project_bidders.project_bid')
    ->join('project_bidder_notices','project_bidders.project_bid','project_bidder_notices.project_bid')
    ->join('rfq_projects','project_bidders.rfq_project_id','rfq_projects.rfq_project_id')
    ->join('procacts','procacts.procact_id','rfq_projects.procact_id')
    ->join('project_plans','project_plans.plan_id','procacts.plan_id')
    ->join('rfqs','rfqs.rfq_id','rfq_projects.rfq_id')
    ->join('contractors','rfqs.contractor_id','contractors.contractor_id')
    // ->join("motion_for_reconsideration_attachments","motion_for_reconsideration.mr_id","motion_for_reconsideration_attachments.mr_id")
    ->get();

    $motion_for_reconsiderations_bid_docs=DB::table("motion_for_reconsideration")
    ->where('motion_for_reconsideration.mr_date_received','like',$year.'%')
    ->whereIn('project_bidder_notices.notice_type',['NOI','NOPD','NOD'])
    ->select("*","motion_for_reconsideration_project_bid.project_bid_id as mr_project_bid_id")
    ->join('motion_for_reconsideration_project_bid','motion_for_reconsideration_project_bid.mr_id','motion_for_reconsideration.mr_id')
    ->join('project_bidders','motion_for_reconsideration_project_bid.project_bid_id','project_bidders.project_bid')
    ->join('project_bidder_notices','project_bidders.project_bid','project_bidder_notices.project_bid')
    ->join('bid_doc_projects','project_bidders.bid_doc_project_id','bid_doc_projects.bid_doc_project_id')
    ->join('procacts','procacts.procact_id','bid_doc_projects.procact_id')
    ->join('project_plans','project_plans.plan_id','procacts.plan_id')
    ->join('bid_docs','bid_docs.bid_doc_id','bid_doc_projects.bid_doc_id')
    ->join('contractors','bid_docs.contractor_id','contractors.contractor_id')
    // ->join("motion_for_reconsideration_attachments","motion_for_reconsideration.mr_id","motion_for_reconsideration_attachments.mr_id")
    ->get();

    $motion_for_reconsiderations=[];

    if(count($motion_for_reconsiderations_rfqs)>=1 && count($motion_for_reconsiderations_bid_docs)>=1){
      $motion_for_reconsiderations=array_merge((array) json_decode($motion_for_reconsiderations_rfqs), (array) json_decode($motion_for_reconsiderations_bid_docs));
    }
    else if(count($motion_for_reconsiderations_rfqs)===0 && count($motion_for_reconsiderations_bid_docs)>=1){
      $motion_for_reconsiderations=$motion_for_reconsiderations_bid_docs;
    }
    else if(count($motion_for_reconsiderations_rfqs)>=1 && count($motion_for_reconsiderations_bid_docs)===0){
      $motion_for_reconsiderations=$motion_for_reconsiderations_rfqs;
    }
    else{

    }
    return back()->withInput()->with('motion_for_reconsiderations',$motion_for_reconsiderations);
  }

  public function getMRAttachments(Request $request){

    $mr_id=$request->mr_id;
    $attachments=DB::table("motion_for_reconsideration_attachments")->where("mr_id",$mr_id)->orderBy('mr_attachment_id','asc')->get();
    return $attachments;
  }


  public function submitMotionForReconsideration(Request $request)
  {
    $data=$request->validate([
      "plan_id"=>"required",
      "opening_date"=>"required",
      "contractor"=>"required",
      "mr_type"=>"required",
      "date_received"=>"required|after_or_equal:opening_date",
    ]);

    $APP=new APP;
    $mr_id=$request->mr_id;
    $contractor_id=$request->input('contractor_id');
    $date_received=date("Y-m-d",strtotime($request->input('date_received')));
    $date_opened=date("Y-m-d",strtotime($request->input('opening_date')));
    $mr_status=$request->status;
    $resolution_due_date=$request->resolution_due_date;
    if($resolution_due_date!=null){
      $resolution_due_date=Date('Y-m-d',strtotime($resolution_due_date));
    }
    $remarks=$request->remarks;
    $plan_ids=$request->plan_id;
    $plan_ids_array=explode(",",$plan_ids);
    $message="success";
    $cmp_array=[];
    $mr_type=$request->input('mr_type');

    // ADD
    if($mr_id===null){
      $duplicate_error=false;
      $project_bids=[];
      foreach ($plan_ids_array as $plan_id) {
        $project_bid=$APP->getSpecificBiddersData($date_opened,$plan_id,$contractor_id,"active,responsive,non-responsive,disqualified,ineligible,disapproved,withdrawn");
        $cluster_bids=$APP->getClusterBids($project_bid->project_bid);
        foreach ($cluster_bids as $cluster_bid) {
          $duplicate=DB::table("motion_for_reconsideration_project_bid")
          ->where([['motion_for_reconsideration_project_bid.project_bid_id',$cluster_bid->project_bid],["motion_for_reconsideration.mr_date_received",$date_received],['motion_for_reconsideration.mr_type',$mr_type]])
          ->join('motion_for_reconsideration','motion_for_reconsideration.mr_id',"motion_for_reconsideration_project_bid.mr_id")
          ->count();

          if($duplicate>0){
            $duplicate_error=true;
          }
          else{
            if(in_array($cluster_bid->project_bid,$project_bids)===false){
              array_push($project_bids,$cluster_bid->project_bid);
            }
          }
        }
      }

      if($duplicate_error===true){
        $message="duplicate";
      }
      else{
        if ($request->file('attachments')!=null){
          DB::table('motion_for_reconsideration')->insert([
            "mr_date_received"=>$date_received,
            "resolution_due_date"=>$resolution_due_date,
            "mr_status"=>"pending",
            "mr_remarks"=>$remarks,
            "mr_type"=>$mr_type,
            "created_at"=>now(),
            "updated_at"=>now()
          ]);

          $mr=DB::table('motion_for_reconsideration')->latest('mr_id')->first();
          $attachments=$request->file('attachments');
          // save attachments to folder and database
          foreach ($attachments as $attachment) {
            $filename=$attachment->getClientOriginalName();
            $pieces = explode(".", $filename);
            $last_index=count($pieces)-1;
            $new_name="MR".uniqid().".pdf";
            if($pieces[$last_index]=="pdf")
            {
              Storage::disk('drive-d')->putFileAs('Archives/Motion For Reconsideration/', $attachment,$new_name);

              DB::table("motion_for_reconsideration_attachments")->insert([
                "mr_id"=>$mr->mr_id,
                "mr_attachment_file_name"=>$new_name,
                "created_at"=>now(),
                "updated_at"=>now()
              ]);
            }
          }
          // Insert Bids to Motion for Reconsideration project bids
          foreach ($project_bids as $bid) {
            DB::table('motion_for_reconsideration_project_bid')->insert([
              "mr_id"=>$mr->mr_id,
              "project_bid_id"=>$bid,
              "created_at"=>now(),
              "updated_at"=>now()
            ]);
          }
        }
        else{
          $message="missing_attachment";
        }
      }
    }
    // update
    else{
      $duplicate_error=false;
      $project_bids=[];
      foreach ($plan_ids_array as $plan_id) {
        $project_bid=$APP->getSpecificBiddersData($date_opened,$plan_id,$contractor_id,"active,responsive,non-responsive,disqualified,ineligible");
        $cluster_bids=$APP->getClusterBids($project_bid->project_bid);
        foreach ($cluster_bids as $cluster_bid) {
          $duplicate=DB::table("motion_for_reconsideration_project_bid")
          ->where([['motion_for_reconsideration_project_bid.project_bid_id',$cluster_bid->project_bid],["motion_for_reconsideration.mr_date_received",$date_received],["motion_for_reconsideration.mr_id","<>",$mr_id],['motion_for_reconsideration.mr_type',$mr_type]])
          ->join('motion_for_reconsideration','motion_for_reconsideration.mr_id',"motion_for_reconsideration_project_bid.mr_id")
          ->count();

          if($duplicate>0){
            $duplicate_error=true;
          }
          else{
            if(in_array($cluster_bid->project_bid,$project_bids)===false){
              array_push($project_bids,$cluster_bid->project_bid);
            }
          }
        }
      }

      if($duplicate_error===true){
        $message="duplicate";
      }
      else{

        $bids=DB::table('motion_for_reconsideration_project_bid')->where('mr_id',$mr_id)->get();
        foreach($bids as $bid){
          try{
            DB::table('motion_for_reconsideration_project_bid')->where([['mr_id',$bid->mr_id],['project_bid_id',$bid-> project_bid_id]])->delete();
          }catch(Exception $e){
            if(in_array($bid->project_bid_id,$project_bids)==false){
              return back()->with("message","update_delete_error");
            }
          }
        }

        DB::table('motion_for_reconsideration')
        ->where('mr_id',$mr_id)
        ->update([
          "mr_date_received"=>$date_received,
          "resolution_due_date"=>$resolution_due_date,
          "mr_remarks"=>$remarks,
          "mr_type"=>$mr_type,
          "updated_at"=>now()
        ]);

        foreach ($project_bids as $bid) {
          $duplicate_bid=DB::table('motion_for_reconsideration_project_bid')->where([["mr_id",$mr_id],["project_bid_id",$bid]])->count();
          if($duplicate_bid==0){
            DB::table('motion_for_reconsideration_project_bid')->insert([
              "mr_id"=>$mr_id,
              "project_bid_id"=>$bid,
              "created_at"=>now(),
              "updated_at"=>now()
            ]);
          }
        }

        $old_attachments=DB::table('motion_for_reconsideration_attachments')->where('mr_id',$mr_id)->count();

        if ($request->file('attachments')==null && $old_attachments==0){
          return back()->with("message","missing_attachment");
        }
        else{
          $attachments=$request->file('attachments');
          // save attachments to folder and database
          if($attachments!=null){
            foreach ($attachments as $attachment) {
              $filename=$attachment->getClientOriginalName();
              $pieces = explode(".", $filename);
              $last_index=count($pieces)-1;
              $new_name="MR".uniqid().".pdf";
              if($pieces[$last_index]=="pdf")
              {
                Storage::disk('drive-d')->putFileAs('Archives/Motion For Reconsideration/', $attachment,$new_name);
                DB::table("motion_for_reconsideration_attachments")->insert([
                  "mr_id"=>$mr_id,
                  "mr_attachment_file_name"=>$new_name,
                  "created_at"=>now(),
                  "updated_at"=>now()
                ]);
              }
            }
          }
        }
      }
    }
    return back()->with("message",$message);
  }


  public function viewMRAttachment(Request $request){
    $data=DB::table('motion_for_reconsideration_attachments')->where('mr_attachment_id',$request->id)->first();
    if($data!=null){
      return  response()->file(Storage::disk('drive-d')->path('Archives/Motion For Reconsideration/'.$data->mr_attachment_file_name));
    }
    else{
      return abort(404);
    }
  }

  public function viewMotionFOrConsideration($mr_id)
  {
    // Merge PDFS and show
    $initial=0;
    $pdfMerger = PDFMerger::init();
    $name="Motion for Reconsideration".$mr_id;
    $attachments=DB::table("motion_for_reconsideration_attachments")->where("mr_id",$mr_id)->orderBy('mr_attachment_id','asc')->get();
    if(count($attachments)==0){
      return abort(404);
    }
    else{
      foreach ($attachments as $attachment) {
        $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/Motion For Reconsideration/'.$attachment->mr_attachment_file_name,'all'));
      }
      $pdfMerger->merge();
      $pdfMerger->save(public_path().'\\'."Merged PDF/".$name.".pdf");
      return  response()->file(public_path().'\\'."Merged PDF/".$name.".pdf")->deleteFileAfterSend(true);
    }
  }

  public function deleteMotionForReconsideration($mr_id)
  {
    $motion_for_reconsiderations=DB::table('motion_for_reconsideration')
    ->where("motion_for_reconsideration.mr_id",$mr_id)
    ->join("motion_for_reconsideration_attachments","motion_for_reconsideration.mr_id","motion_for_reconsideration_attachments.mr_id")->get();


    try{
      DB::table('motion_for_reconsideration_project_bid')->where('mr_id',$mr_id)->delete();
    }catch(Exception $e){
      return back()->with("message","update_delete_error");
    }

    foreach ($motion_for_reconsiderations as $motion_for_reconsideration) {
      Storage::disk('drive-d')->delete('Archives/Motion for Reconsideration/'.$motion_for_reconsideration->mr_attachment_file_name);
    }

    // delete
    DB::table('motion_for_reconsideration_attachments')->where("mr_id",$mr_id)->delete();
    DB::table('motion_for_reconsideration_project_bid')->where("mr_id",$mr_id)->delete();
    DB::table('motion_for_reconsideration')->where("mr_id",$mr_id)->delete();

    return back()->with("message","delete_success");
  }

  public function deleteMRAttachment(Request $request){
    $data=DB::table('motion_for_reconsideration_attachments')->where('mr_attachment_id',$request->id)->first();
    if($data!=null){
      Storage::disk('drive-d')->delete('Archives/Motion for Reconsideration/'.$data->mr_attachment_file_name);
      $data=DB::table('motion_for_reconsideration_attachments')->where('mr_attachment_id',$request->id)->delete();
    }
    return "success";

  }

}
