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

class SupplementalBidBulletinController extends Controller
{
  public function getSupplementalBids()
  {
    $year=date('Y');
    $supplemental_bids=DB::table('supplemental_bid')
    ->select('supplemental_bid_procacts.*','procacts.*','project_plans.*','supplemental_bid.*')
    ->where('supplemental_bid.date_issued','like',$year.'%')
    ->leftJoin('supplemental_bid_procacts','supplemental_bid.supplemental_bid_id','supplemental_bid_procacts.supplemental_bid_id')
    ->leftJoin('procacts','procacts.procact_id','supplemental_bid_procacts.procact_id')
    ->leftJoin('project_plans','procacts.procact_id','project_plans.latest_procact_id')
    ->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view('admin.supplemental_bid',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>'Supplemental Bid Bulletin','year'=>$year,'supplemental_bids'=>$supplemental_bids]);
  }

  public function filterSupplementalBids(Request $request)
  {
    $data=$request->validate([
      "year"=>'required|digits:4|integer|min:2020|max:'.(date('Y')),
    ]);
    $year=$request->year;
    $supplemental_bids=DB::table('supplemental_bid')
    ->select('supplemental_bid_procacts.*','procacts.*','project_plans.*','supplemental_bid.*')
    ->where('supplemental_bid.date_issued','like',$year.'%')
    ->leftJoin('supplemental_bid_procacts','supplemental_bid.supplemental_bid_id','supplemental_bid_procacts.supplemental_bid_id')
    ->leftJoin('procacts','procacts.procact_id','supplemental_bid_procacts.procact_id')
    ->leftJoin('project_plans','procacts.procact_id','project_plans.latest_procact_id')
    ->get();

    return back()->withInput()->with('supplemental_bids',$supplemental_bids);
  }

  public function submitSupplementalBid(Request $request)
  {
    $data=$request->validate([
      "title"=>"required",
      // "plan_ids"=>"required",
      "opening_date"=>"required",
      "date_issued"=>"required | before:opening_date",
    ]);

    if($request->plan_ids===null){
      $schedule=DB::table('project_timelines')->where('bid_submission_start',date('Y-m-d',strtotime($request->opening_date)))->count();
      if($schedule==0){
        return back()->with("message","opening_error");
      }
    }

    $supplemental_bid_id=$request->input('supplemental_bid_id');
    $title=$request->input('title');
    $date_opened=null;
    if($request->input('opening_date')!=null){
      $date_opened=date("Y-m-d",strtotime($request->input('opening_date')));
    }
    $date_issued=date("Y-m-d",strtotime($request->input('date_issued')));
    $plan_ids=$request->input('plan_ids');
    $plan_ids_array=null;
    if($plan_ids!=null){
      $plan_ids_array=explode(",",$plan_ids);
    }
    $message="success";
    $new_name=null;
    $attachment_count=0;

    if($supplemental_bid_id!=null){
      $attachment_count=DB::table('supplemental_bid_attachments')->where("supplemental_bid_id",$supplemental_bid_id)->count();
    }

    if ($request->file('attachments')==null&&$attachment_count===0){
      return back()->with('message','missing_attachment');
    }
    else{
      // Add
      if($supplemental_bid_id===null){
        $duplicate=DB::table('supplemental_bid')->where('title',$title)->count();
        if($duplicate>0){
          $message="duplicate";
        }
        else{
          $insert=DB::table('supplemental_bid')->insert([
            "title"=>$title,
            "date_opened"=>$date_opened,
            "date_issued"=>$date_issued,
            "created_at"=>now(),
            "updated_at"=>now()
          ]);

          if($insert){
            $supplemental_bid=DB::table('supplemental_bid')->where('title',$title)->first();

            $attachments=$request->file('attachments');

            // save attachments to folder and database
            foreach ($attachments as $attachment) {
              $filename=$attachment->getClientOriginalName();
              $pieces = explode(".", $filename);
              $last_index=count($pieces)-1;
              $new_name="SB".uniqid().".pdf";
              if($pieces[$last_index]=="pdf")
              {

                Storage::disk('drive-d')->putFileAs('Archives/Supplemental Bids',$attachment,$new_name);
                DB::table('supplemental_bid_attachments')->insert([
                  "supplemental_bid_id"=>$supplemental_bid->supplemental_bid_id,
                  "file_name"=>$new_name,
                  "created_at"=>now(),
                  "updated_at"=>now()
                ]);
              }
            }

            if($plan_ids_array!=null){
              $project_plans=DB::table("project_plans")->whereIn('plan_id',$plan_ids_array)->get();

              foreach ($project_plans as $project_plan) {
                DB::table('supplemental_bid_procacts')->insert([
                  "supplemental_bid_id"=>$supplemental_bid->supplemental_bid_id,
                  "procact_id"=>$project_plan->latest_procact_id,
                  "created_at"=>now(),
                  "updated_at"=>now()
                ]);
              }
            }
          }
        }
      }
      // Edit
      else{
        $duplicate=DB::table('supplemental_bid')->where([['title',$title],['supplemental_bid_id','<>',$supplemental_bid_id]])->count();
        if($duplicate>0){
          $message="duplicate";
        }
        else{

          $update=DB::table('supplemental_bid')
          ->where("supplemental_bid_id",$supplemental_bid_id)
          ->update([
            "title"=>$title,
            "date_opened"=>$date_opened,
            "date_issued"=>$date_issued,
            "updated_at"=>now()
          ]);


          $attachments=$request->file('attachments');
          // save attachments to folder and database

          if($attachments!=null){
            foreach ($attachments as $attachment) {
              $filename=$attachment->getClientOriginalName();
              $pieces = explode(".", $filename);
              $last_index=count($pieces)-1;
              $new_name="SB".uniqid().".pdf";
              if($pieces[$last_index]=="pdf")
              {
                Storage::disk('drive-d')->putFileAs('Archives/Supplemental Bids',$attachment,$new_name);
                DB::table('supplemental_bid_attachments')->insert([
                  "supplemental_bid_id"=>$supplemental_bid_id,
                  "file_name"=>$new_name,
                  "created_at"=>now(),
                  "updated_at"=>now()
                ]);
              }
            }
          }

          if($plan_ids_array!=null){

            $procacts=[];
            $project_plans=DB::table("project_plans")->whereIn('plan_id',$plan_ids_array)->get();

            foreach ($project_plans as $project_plan) {
              array_push($procacts,$project_plan->latest_procact_id);
              $count=DB::table("supplemental_bid_procacts")->where([["procact_id",$project_plan->latest_procact_id],[  "supplemental_bid_id",$supplemental_bid_id]])->count();
              if($count===0){
                DB::table('supplemental_bid_procacts')->insert([
                  "supplemental_bid_id"=>$supplemental_bid_id,
                  "procact_id"=>$project_plan->latest_procact_id,
                  "created_at"=>now(),
                  "updated_at"=>now()
                ]);
              }
            }
            // delete excess procact data
            DB::table('supplemental_bid_procacts')->whereNotIn("procact_id",$procacts)->where("supplemental_bid_id",$supplemental_bid_id)->delete();
          }
        }
      }
    }

    return back()->with("message",$message);
  }

  public function viewSupplementalBid($id)
  {
    // Merge PDFS and show
    $initial=0;
    $pdfMerger = PDFMerger::init();
    $name="Supplemental Bid".$id;
    $attachments=DB::table('supplemental_bid')->where("supplemental_bid.supplemental_bid_id",$id)
    ->join("supplemental_bid_attachments","supplemental_bid.supplemental_bid_id","supplemental_bid_attachments.supplemental_bid_id")->get();
    if(count($attachments)==0){
      return abort(404);
    }
    else{
      foreach ($attachments as $attachment) {
        $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/Supplemental Bids/'.$attachment->file_name),'all');
      }
      $pdfMerger->merge();
      $pdfMerger->save(public_path("Merged PDF/".$name.".pdf"));
      return  response()->file(public_path("Merged PDF/".$name.".pdf"))->deleteFileAfterSend(true);
    }
  }


  public function getSBAttachments(Request $request)
  {
    $supplemental_bid_id=$request->supplemental_bid_id;
    $attachments=DB::table("supplemental_bid_attachments")->where("supplemental_bid_id",$supplemental_bid_id)->orderBy('supplemental_bid_attachment_id','asc')->get();
    return $attachments;
  }

  public function deleteSupplementalBid($id)
  {
    $supplemental_bid=DB::table('supplemental_bid')->where("supplemental_bid.supplemental_bid_id",$id)
    ->join("supplemental_bid_attachments","supplemental_bid.supplemental_bid_id","supplemental_bid_attachments.supplemental_bid_id")->first();
    Storage::disk('drive-d')->delete('Archives/Supplemental Bids/'.$supplemental_bid->file_name);
    // delete
    DB::table('supplemental_bid_attachments')->where("supplemental_bid_id",$id)->delete();
    DB::table('supplemental_bid_procacts')->where("supplemental_bid_id",$id)->delete();
    DB::table('supplemental_bid')->where("supplemental_bid_id",$id)->delete();

    return back()->with("message","delete_success");

  }

  public function deleteSBAttachment(Request $request){
    $data=DB::table('supplemental_bid_attachments')->where('supplemental_bid_attachment_id',$request->id)->first();
    if($data!=null){
      Storage::disk('drive-d')->delete('Archives/Supplemental Bids/'.$data->file_name);
      $data=DB::table('supplemental_bid_attachments')->where('supplemental_bid_attachment_id',$request->id)->delete();
    }
    return "success";

  }

  public function viewSBAttachment(Request $request){
    $data=DB::table('supplemental_bid_attachments')->where('supplemental_bid_attachment_id',$request->id)->first();
    if($data!=null){
      return  response()->file(Storage::disk('drive-d')->path('Archives/Supplemental Bids/'.$data->file_name));
    }
    else{
      return abort(404);
    }
  }
}
