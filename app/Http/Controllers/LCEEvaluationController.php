<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use App\{LCEEvaluation,LCEEvaluationAttachment,APP};
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LCEEvaluationController extends Controller
{
	public function index(Request $request){
		$links=getUserLinks();
		$user_privilege=getUserPrivilege();
		$user_privilege=['view','add','update','delete'];

		if($request->project_year===null){
			$data=LCEEvaluation::where('lce_evaluation_date','like',"%".Date('Y')."%")
			->join('procacts','procacts.procact_id','lce_evaluation.procact_id')
			->join('project_plans','procacts.plan_id','project_plans.plan_id')
			->join('contractors','lce_evaluation.contractor_id','contractors.contractor_id')
			->get();

			return view('archives.lce_evaluation',['title'=>"Local Government Executive Evaluation","data"=>$data,"year"=>Date('Y'),'links'=>$links,'user_privilege'=>$user_privilege]);
		}
		else{
			$data=LCEEvaluation::where('lce_evaluation_date','like','%'.$request->project_year.'%')
			->join('procacts','procacts.procact_id','lce_evaluation.procact_id')
			->join('project_plans','procacts.plan_id','project_plans.plan_id')
			->join('contractors','lce_evaluation.contractor_id','contractors.contractor_id')
			->get();

			return back()->withInput()->with('data',$data);
		}
	}

	public function submitLCEEvaluation(Request $request)
	{

		$data=$request->validate([
			"opening_date"=>"required",
			"project_title"=>"required",
			"contractor"=>"required",
			"opening_date"=>"required",
			"evaluation_date"=>"required|after:opening_dateI",
			"status"=>"required",
			"contractor_date_received"=>"required",
			"reason"=>"required",
		]);
		$with_attachment=true;
		$id=$request->id;
		$message="success";
		$attachments=$request->file('attachments');
		$evaluation=null;

		$APP=new APP;
		$clusters=$APP->getClusterBids($request->project_bid);

		$folder="LCE Evaluation";

		$governor=DB::table('governors')->orderBy('governor_id','desc')->first();

		if($id===null){
			if(!isset($attachments)){
				$message="missing_attachment";
			}
			else{
				// Duplicate
				$duplicate=LCEEvaluation::where([
					["project_bid",$request->project_bid],
					["contractor_id",$request->contractor_id],
					["procact_id",$clusters[0]->procact_id],
				])
				->count();

				if($duplicate>0){
					$message="duplicate";
				}
				else{
					foreach($clusters as $cluster){

						$term="Disapproved";
						$resolution_project=DB::table('resolution_projects')->where('procact_id',$cluster->procact_id)->first();
						if($resolution_project===null){
							$resolution_id=null;
						}
						else{
							$resolution_id=$resolution_project->resolution_id;
							DB::table('resolution_projects')->where('procact_id',$cluster->procact_id)->delete();
						}

						DB::table("project_plans")->where('plan_id',$cluster->plan_id)->update(['project_bid_id'=>null]);
						DB::table('project_bidders')->where('project_bid',$cluster->project_bid_id)->update(['bid_status'=>"disqualified"]);
						DB::table('disqualification_records')->insert([
							'project_bid'	=>$cluster->project_bid,
							'remarks'	=>$term.': '.$request->reason,
							'user_id'	=>Auth::user()->id,
							'created_at'	=>now(),
							'updated_at' =>now()
						]);

						$evaluation=LCEEvaluation::create([
							"project_bid"=>$cluster->project_bid,
							"contractor_id"=>$request->contractor_id,
							"procact_id"=>$cluster->procact_id,
							"governor_id"=>$governor->governor_id,
							'lce_evaluation_date'=>Date('Y-m-d',strtotime($request->evaluation_date)),
							"lce_evaluation_status"=>$request->status,
							"lce_evaluation_remarks"=>$request->remarks,
							"lce_evaluation_reason"=>$request->reason,
							"lce_contractor_date_received"=>$request->contractor_date_received,
							"resolution_id"=>$resolution_id
						]);
					}
				}

			}

		}
		else{

			foreach($clusters as $cluster){
				$evaluation=LCEEvaluation::where("project_bid",$cluster->project_bid)->first();
				$evaluation=LCEEvaluation::find($evaluation->id);
				$evaluation->lce_evaluation_date=Date('Y-m-d',strtotime($request->evaluation_date));
				$evaluation->lce_evaluation_status=$request->status;
				$evaluation->lce_evaluation_remarks=$request->remarks;
				$evaluation->lce_evaluation_reason=$request->reason;
				$evaluation->lce_contractor_date_received=Date('Y-m-d',strtotime($request->contractor_date_received));
				$evaluation->save();
			}

			$attachmentCount=LCEEvaluationAttachment::where('lce_evaluation_id',$id)->count();
			if($attachmentCount===0 && !isset($attachments)){
				$message="missing_attachment";
			}
		}


		if(isset($attachments) && $message=="success" && $evaluation!=null){
			// save attachments to folder and database
			foreach ($attachments as $attachment) {
				$filename=$attachment->getClientOriginalName();
				$pieces = explode(".", $filename);
				$last_index=count($pieces)-1;
				$new_name="lce_evaluation"."-".uniqid().".pdf";
				if($pieces[$last_index]==="pdf"){
					Storage::disk('drive-d')->putFileAs('Archives/'.$folder.'/',$attachment,$new_name);

					foreach($clusters as $cluster){
						$evaluation=LCEEvaluation::where("project_bid",$cluster->project_bid)->first();
						LCEEvaluationAttachment::create([
							"lce_evaluation_id"=>$evaluation->id,
							"attachment_name"=>$new_name,
						]);
					}
				}
			}
		}
		if($message==="success"){
			return back()->with("message",$message);
		}
		else{
			return back()->withInput()->with("message",$message);
		}


	}

	public function getLCEEvaluationAttachments(Request $request){
		$attachments=LCEEvaluationAttachment::where('lce_evaluation_id',$request->id)->orderBy('created_at','asc')->get();
		return $attachments;
	}


	public function viewLCEEvaluationAttachment(Request $request){
		$data=LCEEvaluationAttachment::where('id',$request->id)->first();
		if($data!=null){
			return  response()->file(Storage::disk('drive-d')->path('Archives/LCE Evaluation/'.$data->attachment_name));
		}
		else{
			return abort(404);
		}

	}

	public function viewLCEEvaluationAttachments(Request $request){

		$lce_evaluation=LCEEvaluation::find($request->id);
		if($lce_evaluation!=null){
			// Merge PDFS and show
			$initial=0;
			$pdfMerger = PDFMerger::init();
			$name="LCE Evaluation-".$request->id;
			$attachments=LCEEvaluationAttachment::where("lce_evaluation_id",$request->id)->get();

			if(count($attachments)>0){
				foreach ($attachments as $attachment) {
					$pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/LCE Evaluation/'.$attachment->attachment_name),'all');
				}
				$pdfMerger->merge();
				$pdfMerger->save(storage_path("app/public/temp_archive/".$name.".pdf"));
				return  response()->file(storage_path("app/public/temp_archive/".$name.".pdf"))->deleteFileAfterSend(true);
			}
			else{
				abort(403,"No attached files");
			}
		}
		else{
			abort(404);
		}
	}


	public function deleteLCEEvaluationAttachment(Request $request){
		$data=LCEEvaluationAttachment::where('id',$request->id)->first();
		$lce_evaluation=LCEEvaluation::find($data->lce_evaluation_id);
		Storage::disk('drive-d')->delete('Archives/LCE Evaluation/'.$data->attachment_name);
		LCEEvaluationAttachment::where('attachment_name',$data->attachment_name)->delete();

		$existing_attachments=LCEEvaluationAttachment::where('lce_evaluation_id',$data->lce_evaluation_id)->count();

		return "success";
	}

	public function deleteLCEEvaluation(Request $request){
		$data=LCEEvaluation::find($request->id);
		$APP=new APP;
		$clusters=$APP->getClusterBids($data->project_bid);

		foreach($clusters as $cluster){
			$evaluation=LCEEvaluation::where("project_bid",$cluster->project_bid)->first();
			$term="LCE Disqualified:";
			$resolution_project=DB::table('resolution_projects')->where('procact_id',$cluster->procact_id)->first();
			if($evaluation->resolution_id!=null && 	$resolution_project===null){
				DB::table('resolution_projects')->insert([
					"resolution_id"=>$evaluation->resolution_id,
					"procact_id"=>$evaluation->procact_id,
					"created_at"=>now(),
					"updated_at"=>now()
				]);
			}

			DB::table("project_plans")->where('plan_id',$cluster->plan_id)->update(['project_bid_id'=>$evaluation->project_bid]);
			DB::table('project_bidders')->where('project_bid',$cluster->project_bid_id)->update(['bid_status'=>"responsive"]);
			DB::table('disqualification_records')->where([
				['project_bid',$cluster->project_bid],
				['remarks','like','%'.$term.'%']
				])->delete();

				LCEEvaluationAttachment::where('lce_evaluation_id',$evaluation->id)->delete();
				LCEEvaluation::where("project_bid",$cluster->project_bid)->delete();
			}
			return "success";
		}

	}
