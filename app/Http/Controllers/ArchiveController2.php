<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use App\APP;
use App\Project;
use App\ProjectPlans;
use App\Procact;
use App\Meeting;
use App\ArchiveAbstract;
use App\ArchiveAbstractAttachments;
use App\ArchiveCertificateOfPosting;
use App\ArchiveCertificateOfPostingAttachments;
use App\ArchiveMinute;
use App\ArchiveMinuteAttachments;
use App\ArchiveMeetingAttendance;
use App\ArchiveMeetingAttendanceAttachments;
use App\ArchiveNoticeOfMeetingAttachments;
use App\ArchiveResolutionAttachments;
use App\ArchiveNoticeOfAwardAttachments;
use App\ArchiveContractAttachments;
use App\ArchiveProjectAttachments;
use App\ArchiveNoticeToProceedAttachments;
use App\ArchiveNoticeAttachments;
use App\ArchiveITBAttachments;
use App\NoticeOfAward;
use App\ProjectTimeline;
use App\Resolution;
use App\Contract;
use App\NoticeToProceed;
use App\ProjectBidderNotice;
use App\ArchiveApp;
use App\Order;
use App\RequestForExtension;
use App\RequestForExtensionBids;
use App\ArchiveOrderAttachments;
use App\Termination;
use App\ArchiveTerminationAttachments;
use App\ArchiveTransmittal;
use App\ArchiveTransmittalAttachments;
use Validator;
use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\ProcurementController;


class ArchiveController2 extends Controller
{
	// Transmittals

	function autoCompleteProjectWithNTPs(Request $request){
		$term=$request->term;
		$APP=new APP;

		$project_plans=NoticeToProceed::where([
			["project_plans.project_bid_id","<>",null],
			["project_plans.project_bid_id","<>",null],
			["project_plans.project_title", "LIKE", "%".$term."%"],
			["project_timelines.timeline_status","set"],
			["notice_to_proceeds.ntp_date_received","<>",null],
			['project_plans.status','completed']
		])
		->join("project_bidders","project_bidders.project_bid","notice_to_proceeds.project_bid_id")
		->join("project_plans","project_plans.project_bid_id","project_bidders.project_bid")
		->join("procacts","project_plans.latest_procact_id","procacts.procact_id")
		->join("project_timelines","project_timelines.procact_id","procacts.procact_id")
		->orderBy("project_plans.project_title","desc")
		->distinct()
		->take(10)
		->get();

		if(sizeOf($project_plans) != 0){
			foreach($project_plans as $project_plan){
				$bid_details=$APP->getBid($project_plan->project_bid_id);
				$results[] = [
					"id" => $project_plan->plan_id,
					"procact_id" =>  $bid_details->procact_id,
					"procact" =>  $bid_details->procact_id,
					"value" => $project_plan->project_title,
					"project_number" => $project_plan->project_no,
					"project_bid_id" =>  $project_plan->project_bid_id,
					"contractor_id" =>  $bid_details->contractor_id,
					"contractor" =>  $bid_details->business_name,
					"contract_id" =>  $project_plan->contract_id,
				];
			}
		}
		else{
			$results[] = [
				"id" => "",
				"value" => "No Match Found"
			];
		}
		return response()->json($results);

	}



	public function getTransmittals(Request $request)
	{
		if(isset($request->year)){
			$year=$request->year;
		}
		else{
			$year=date('Y');
		}

		$data=ArchiveTransmittal::select('*','archive_transmittal.id as transmittal_id')
		->where([['archive_transmittal.date_received_by_coa','like',$year.'%'],['project_plans.status','completed'],['archive_transmittal.deleted',false]])
		->with('transmittal_attachments')
		->leftJoin('project_plans','project_plans.plan_id','archive_transmittal.plan_id')
		->join('project_bidders','project_bidders.project_bid','project_plans.project_bid_id')
		->join('rfq_projects','rfq_projects.rfq_project_id','project_bidders.rfq_project_id')
		->join('rfqs','rfq_projects.rfq_id','rfqs.rfq_id')
		->join('procacts','rfq_projects.procact_id','procacts.procact_id')
		->join('contractors','rfqs.contractor_id','contractors.contractor_id')
		->join('procurement_modes','project_plans.mode_id','procurement_modes.mode_id')
		->join('funds','project_plans.fund_id','funds.fund_id')
		->leftJoin('users', 'archive_transmittal.updated_by', 'users.id')
		->get();

		$data2=ArchiveTransmittal::select('*','archive_transmittal.id as transmittal_id')
		->where([['archive_transmittal.date_received_by_coa','like',$year.'%'],['project_plans.status','completed'],['archive_transmittal.deleted',false]])
		->with('transmittal_attachments')
		->leftJoin('project_plans','project_plans.plan_id','archive_transmittal.plan_id')
		->join('project_bidders','project_bidders.project_bid','project_plans.project_bid_id')
		->join('bid_doc_projects','bid_doc_projects.bid_doc_project_id','project_bidders.bid_doc_project_id')
		->join('bid_docs','bid_doc_projects.bid_doc_id','bid_docs.bid_doc_id')
		->join('procacts','bid_doc_projects.procact_id','procacts.procact_id')
		->join('contractors','bid_docs.contractor_id','contractors.contractor_id')
		->join('procurement_modes','project_plans.mode_id','procurement_modes.mode_id')
		->join('funds','project_plans.fund_id','funds.fund_id')
		->leftJoin('users', 'archive_transmittal.updated_by', 'users.id')
		->get();

		$data=json_decode(json_encode($data));
		$data2=json_decode(json_encode($data2));

		foreach ($data2 as $row) {
			array_push($data,$row);
		}

		$title="Transmittals";
		$links=getUserLinks();
		$user_privilege=getUserPrivilege();

		return view("archives.transmittal",['links'=>$links,'user_privilege'=>$user_privilege, 'data'=>$data,"title"=>$title,"year"=>$year]);
	}

	public function filterTransmittals(Request $request)
	{
		$data = $request->validate([
			"year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
		]);

		$year=$request->year;


		$data=ArchiveTransmittal::select('*','archive_transmittal.id as transmittal_id')
		->where([['archive_transmittal.date_received_by_coa','like',$year.'%'],['project_plans.status','completed']])
		->with('transmittal_attachments')
		->leftJoin('project_plans','project_plans.plan_id','archive_transmittal.plan_id')
		->join('project_bidders','project_bidders.project_bid','project_plans.project_bid_id')
		->join('rfq_projects','rfq_projects.rfq_project_id','project_bidders.rfq_project_id')
		->join('rfqs','rfq_projects.rfq_id','rfqs.rfq_id')
		->join('contractors','rfqs.contractor_id','contractors.contractor_id')
		->join('procacts','rfq_projects.procact_id','procacts.procact_id')
		->leftJoin('users', 'archive_transmittal.updated_by', 'users.id')
		->join('procurement_modes','project_plans.mode_id','procurement_modes.mode_id')
		->join('funds','project_plans.fund_id','funds.fund_id')
		->get();

		$data2=ArchiveTransmittal::select('*','archive_transmittal.id as transmittal_id')
		->where([['archive_transmittal.date_received_by_coa','like',$year.'%'],['project_plans.status','completed']])
		->with('transmittal_attachments')
		->leftJoin('project_plans','project_plans.plan_id','archive_transmittal.plan_id')
		->join('project_bidders','project_bidders.project_bid','project_plans.project_bid_id')
		->join('bid_doc_projects','bid_doc_projects.bid_doc_project_id','project_bidders.bid_doc_project_id')
		->join('bid_docs','bid_doc_projects.bid_doc_id','bid_docs.bid_doc_id')
		->join('contractors','bid_docs.contractor_id','contractors.contractor_id')
		->join('procacts','bid_doc_projects.procact_id','procacts.procact_id')
		->leftJoin('users', 'archive_transmittal.updated_by', 'users.id')
		->join('procurement_modes','project_plans.mode_id','procurement_modes.mode_id')
		->join('funds','project_plans.fund_id','funds.fund_id')
		->get();

		$data=json_decode(json_encode($data));
		$data2=json_decode(json_encode($data2));

		foreach ($data2 as $row) {
			array_push($data,$row);
		}

		return back()->withInput()->with("data",$data);
	}

	public function submitTransmittal(Request $request)
	{
		$data = $request->validate([
			"date_received_by_coa" => "required|before:tomorrow",
			"plan_id" => "required"
		]);
		$id=$request->transmittal_id;
		$message="success";
		$attachments=$request->file('attachments');
		$date_received_by_coa=date('Y-m-d',strtotime($request->date_received_by_coa));
		$APP=new APP;
		$cluster_bids=$APP->getClusterBids($request->project_bid);
		if($id===null){
			$duplicate=ArchiveTransmittal::where([['plan_id',$request->plan_id],['deleted','<>','1']])->count();

			if($duplicate===0){

				if(isset($attachments)){
					// save attachments to folder and database
					foreach ($attachments as $attachment) {
						$filename=$attachment->getClientOriginalName();
						$pieces = explode(".", $filename);
						$last_index=count($pieces)-1;
						$new_name=$request->plan_id."transmittal-".uniqid().".pdf";
						if($pieces[$last_index]==="pdf")
						{
							Storage::disk('drive-d')->putFileAs('Archives/Transmittal',$attachment,$new_name);
							foreach($cluster_bids as $item){
								$archive_transmittal=ArchiveTransmittal::create([
									"date_received_by_coa"=>$date_received_by_coa,
									"transmittal_remarks"=>$request->remarks,
									"plan_id"=>$item->plan_id,
									"updated_by"=>Auth::user()->id,
									"deleted"=>0,
								]);


								ArchiveTransmittalAttachments::create([
									"archive_transmittal_id"=>$archive_transmittal->id,
									"attachment_name"=>$new_name,
								]);
							}
						}
					}
				}
				else{
					$message="missing_attachment";
				}
			}
			else{
				$message="duplicate_error";
			}
		}
		else{


			if(isset($attachments)){

				// save attachments to folder and database
				foreach ($attachments as $attachment) {

					$filename=$attachment->getClientOriginalName();
					$pieces = explode(".", $filename);
					$last_index=count($pieces)-1;
					$new_name=$request->plan_id."transmittal-".uniqid().".pdf";
					if($pieces[$last_index]==="pdf"){
						Storage::disk('drive-d')->putFileAs('Archives/Transmittal',$attachment,$new_name);
						foreach($cluster_bids as $item){
							$archive_transmittal=ArchiveTransmittal::where([["plan_id",$item->plan_id],["deleted",false]])->first();
							$archive_transmittal=ArchiveTransmittal::find($archive_transmittal->id);
							$archive_transmittal->date_received_by_coa=$date_received_by_coa;
							$archive_transmittal->transmittal_remarks=$request->remarks;
							$archive_transmittal->updated_by=Auth::user()->id;
							$archive_transmittal->save();
							ArchiveTransmittalAttachments::create([
								"archive_transmittal_id"=>$archive_transmittal->id,
								"attachment_name"=>$new_name,
							]);
						}
					}
				}
			}
			else{
				$existing_attachments= ArchiveTransmittalAttachments::where("archive_transmittal_id",$archive_transmittal->id)->count();
				if($existing_attachments===0){
					$message="missing_attachment";
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

	public function getTransmittalAttachments(Request $request)
	{
		$attachments=ArchiveTransmittalAttachments::where('archive_transmittal_id',$request->archive_transmittal_id)->orderBy('created_at','asc')->get();

		return $attachments;
	}

	public function viewTransmittalAttachment(Request $request)
	{
		$data=ArchiveTransmittalAttachments::where('id',$request->id)->first();
		if($data!=null){
			return  response()->file(Storage::disk('drive-d')->path('Archives/Transmittal/'.$data->attachment_name));
		}
		else{
			return abort(404);
		}

	}

	public function viewTransmittalAttachments(Request $request)
	{
		$transmittal=ArchiveTransmittal::find($request->id);
		if($transmittal!=null){
			// Merge PDFS and show
			$initial=0;
			$pdfMerger = PDFMerger::init();
			$name="Archives_Transmittal-".$request->id;
			$attachments=ArchiveTransmittalAttachments::where("archive_transmittal_id",$request->id)->get();
			if(count($attachments)>0){
				foreach ($attachments as $attachment) {
					$pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/Transmittal/'.$attachment->attachment_name),'all');
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

	public function deleteTransmittalAttachment(Request $request)
	{
		$data=ArchiveTransmittalAttachments::find($request->id);
		$APP=new APP;
		if($data!=null){
			Storage::disk('drive-d')->delete('Archives/Transmittal/'.$data->attachment_name);
			ArchiveTransmittalAttachments::where('attachment_name',$data->attachment_name)->delete();
			$transmittal_attachments=ArchiveTransmittalAttachments::where('archive_transmittal_id',$data->archive_transmittal_id)->count();
			// if($transmittal_attachments===0){
			// 	$transmittal=ArchiveTransmittal::find($data->archive_transmittal_id);
			// 	$plan=ProjectPlans::where('plan_id',$transmittal->plan_id)->first();
			// 	$clusters=$APP->getClusterBids($plan->project_bid_id);
			// 	foreach ($clusters as $value) {
			// 		$id=ArchiveTransmittal::where([['plan_id',$value->plan_id],['deleted',false]])->first();
			// 		$transmittal=ArchiveTransmittal::find($id->id);
			// 		$transmittal->delete();
			// 	}
			// }
		}
		// $transmittal_attachments=ArchiveTransmittalAttachments::where('archive_transmittal_id',$data->archive_transmittal_id)->count();
		return "success";
	}

	public function deleteTransmittal(Request $request)
	{
		$APP=new APP;
		$archive_transmittal=ArchiveTransmittal::find($request->id);
		if($archive_transmittal!=null){
			$plan=ProjectPlans::where('plan_id',$archive_transmittal->plan_id)->first();
			$clusters=$APP->getClusterBids($plan->project_bid_id);
			foreach ($clusters as $value) {
				$archive_transmittal=ArchiveTransmittal::where([['plan_id',$value->plan_id],['deleted',false]])->first();
				if($archive_transmittal!=null){
					$archive_transmittal=ArchiveTransmittal::find($archive_transmittal->id);
					$archive_transmittal->deleted=1;
					$archive_transmittal->deleted_at=now();
					$archive_transmittal->deleted_by=Auth::user()->id;
					$archive_transmittal->save();
				}
			}
		}
		if(count($clusters)>=2){
			return "reload";
		}
		else{
			return "success";
		}

	}
}
