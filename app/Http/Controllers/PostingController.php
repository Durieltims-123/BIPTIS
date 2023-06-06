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
use App\Procact;
use App\ArchiveResolutionAttachments;
use App\ArchiveNoticeOfAwardAttachments;
use App\ArchiveContractAttachments;
use App\ArchiveNoticeToProceedAttachments;
use App\ArchiveITBAttachments;
use App\Resolution;
use App\NoticeOfAward;
use App\Contract;
use App\NoticeToProceed;
use App\ArchiveRFQAttachments;
use Validator;
use Exception;
use Illuminate\Database\Eloquent\Model;


class PostingController extends Controller
{

	// Notice of Awards
	public function getPostingNoticeOfAwards(Request $request)
	{
		if (isset($request->year)) {
			$year = $request->year;
		} else {
			$year = date('Y');
		}

		$data = NoticeOfAward::where([['notice_of_awards.date_generated', 'like', $year . '%'], ['with_attachment', true]])
			->select('municipalities.*', 'procacts.*', 'notice_of_awards.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*')
			->join('project_bidders', 'project_bidders.project_bid', 'notice_of_awards.project_bid_id')
			->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
			->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
			->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data2 = NoticeOfAward::where([['notice_of_awards.date_generated', 'like', $year . '%'], ['with_attachment', true]])
			->select('municipalities.*', 'procacts.*', 'notice_of_awards.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*')->join('project_bidders', 'project_bidders.project_bid', 'notice_of_awards.project_bid_id')
			->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
			->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
			->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data = json_decode(json_encode($data));
		$data2 = json_decode(json_encode($data2));

		foreach ($data2 as $row) {
			array_push($data, $row);
		}

		$title = "Posting Notice of Awards";
		$links = getUserLinks();
		// $user_privilege=getUserPrivilege();
		$user_privilege = ["add", "update", "delete"];

		return view("posting.noa", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year]);
	}

	public function filterPostingNoticeOfAwards(Request $request)
	{
		$data = $request->validate([
			"year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
		]);

		$year = $request->year;
		$data = NoticeOfAward::where([['notice_of_awards.date_generated', 'like', $year . '%'], ['with_attachment', true]]);
		if ($request->status != null) {
			$data = $data->where('notice_of_awards.posting_status', $request->status);
		}

		$data = $data
			->select('municipalities.*', 'procacts.*', 'notice_of_awards.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*')->join('project_bidders', 'project_bidders.project_bid', 'notice_of_awards.project_bid_id')
			->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
			->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
			->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data2 = NoticeOfAward::where([['notice_of_awards.date_generated', 'like', $year . '%'], ['with_attachment', true]]);
		if ($request->status != null) {
			$data2 = $data2->where('notice_of_awards.posting_status', $request->status);
		}

		$data2 = $data2
			->select('municipalities.*', 'procacts.*', 'notice_of_awards.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*')->join('project_bidders', 'project_bidders.project_bid', 'notice_of_awards.project_bid_id')
			->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
			->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
			->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data = json_decode(json_encode($data));
		$data2 = json_decode(json_encode($data2));

		foreach ($data2 as $row) {
			array_push($data, $row);
		}

		return back()->withInput()->with("data", $data);
	}

	public function submitPostingNoticeOfAward(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(",", $request->input('id'));
		$ids_array = [];

		foreach ($ids as $id) {
			$noa = NoticeOfAward::find($id);
			if (in_array($noa->notice_award_id, $ids_array) === false) {
				$cluster_bids = $APP->getClusterBids($noa->project_bid_id);
				if (count($cluster_bids) > 1) {
					foreach ($cluster_bids as $cluster) {
						$noa = NoticeOfAward::where('project_bid_id', $cluster->project_bid)->first();
						$noa = NoticeOfAward::find($noa->notice_award_id);
						$noa->posting_status = "posted";
						$noa->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$noa->save();
						array_push($ids_array, $noa->notice_award_id);
					}
				} else {
					$noa->posting_status = "posted";
					$noa->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$noa->save();
					array_push($ids_array, $noa->notice_award_id);
				}
			}
		}

		return back()->withInput()->with('message', 'success');
	}

	public function downloadNOAZip(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(",", $request->input('id'));
		$ids_array = [];
		$noa = NoticeOfAward::find($ids[0]);
		$name = 'NOA-' . Date("Y-m-d", strtotime($request->posting_date)) . md5(uniqid(rand(), true)) . '.zip';
		$zip_file = public_path() . '\\' . 'zips/' . $name;
		$zip = new \ZipArchive();
		$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		foreach ($ids as $id) {
			$letter = 'A';
			$title = "";
			$noa = NoticeOfAward::find($id);

			if (in_array($noa->notice_award_id, $ids_array) === false) {
				$cluster_bids = $APP->getClusterBids($noa->project_bid_id);
				if (count($cluster_bids) > 1) {
					foreach ($cluster_bids as $cluster) {
						$noa = NoticeOfAward::where('project_bid_id', $cluster->project_bid)->first();
						$noa = NoticeOfAward::find($noa->notice_award_id);
						$noa->posting_status = "posted";
						$noa->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$noa->save();

						$temp = $letter . '. ' . $cluster->project_title . ";";
						if ($letter == "A") {
							$title = $temp;
						} else {
							$title = $title . "   " . $temp;
						}
						array_push($ids_array, $noa->notice_award_id);
						$letter++;
					}
				} else {
					$noa->posting_status = "posted";
					$noa->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$noa->save();
					$title = $cluster_bids[0]->project_title;
					array_push($ids_array, $noa->notice_award_id);
					$attachments = ArchiveNoticeOfAwardAttachments::where("notice_award_id", $noa->notice_award_id)->get();
				}
				$attachments = ArchiveNoticeOfAwardAttachments::where("notice_award_id", $noa->notice_award_id)->get();
				if (count($attachments) > 0) {
					foreach ($attachments as $attachment) {
						// $title = str_replace(' ', '_', $title);
						if (strlen($title) > 200) {
							$title = substr($title, 0, 200);
						}
						$zip->addFile(Storage::disk('drive-d')->path('Archives/NoticeOfAwards/' . $attachment->attachment_name), "NOA-" . strtoupper(strtolower(str_replace('/', '_', $title))) . "-" . strtoupper(strtolower((str_replace('/', '_', $cluster_bids[0]->business_name)))) . "-OPENED ON " . Date("F d,Y", strtotime($cluster_bids[0]->open_bid)) . ".pdf");
					}
				}
			}
		}
		$zip->close();
		return response()->download($zip_file, $name)->deleteFileAfterSend(true);
	}


	// Contracts
	public function getPostingContracts(Request $request)
	{
		if (isset($request->year)) {
			$year = $request->year;
		} else {
			$year = date('Y');
		}

		$data = Contract::where([['contracts.contract_date_generated', 'like', $year . '%'], ['with_attachment', true]])
			->select('municipalities.*',  'procacts.*', 'contracts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'contracts.contract_date_generated as date_generated', 'contracts.contract_date_received_contractor as date_received_by_contractor', 'contracts.contract_release_date as date_released', 'contracts.contract_receive_date as date_received')
			->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')
			->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
			->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
			->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data2 = Contract::where([['contracts.contract_date_generated', 'like', $year . '%'], ['with_attachment', true]])
			->select('municipalities.*',  'procacts.*', 'contracts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'contracts.contract_date_generated as date_generated', 'contracts.contract_date_received_contractor as date_received_by_contractor', 'contracts.contract_release_date as date_released', 'contracts.contract_receive_date as date_received')
			->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')
			->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
			->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
			->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();



		$data = json_decode(json_encode($data));
		$data2 = json_decode(json_encode($data2));

		foreach ($data2 as $row) {
			array_push($data, $row);
		}

		$title = "Contract Posting";
		$links = getUserLinks();
		// $user_privilege=getUserPrivilege();
		$user_privilege = ["add", "update", "delete"];

		return view("posting.contract", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year]);
	}

	public function filterPostingContracts(Request $request)
	{
		$data = $request->validate([
			"year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
		]);

		$year = $request->year;
		$data = Contract::where([['contracts.contract_date_generated', 'like', $year . '%'], ['with_attachment', true]]);
		if ($request->status != null) {
			$data = $data->where('contracts.posting_status', $request->status);
		}

		$data = $data
			->select('municipalities.*',  'procacts.*', 'contracts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'contracts.contract_date_generated as date_generated', 'contracts.contract_date_received_contractor as date_received_by_contractor', 'contracts.contract_release_date as date_released', 'contracts.contract_receive_date as date_received')
			->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')
			->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
			->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
			->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data2 = Contract::where([['contracts.contract_date_generated', 'like', $year . '%'], ['with_attachment', true]]);
		if ($request->status != null) {
			$data2 = $data2->where('contracts.posting_status', $request->status);
		}

		$data2 = $data2
			->select('municipalities.*',  'procacts.*', 'contracts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'contracts.contract_date_generated as date_generated', 'contracts.contract_date_received_contractor as date_received_by_contractor', 'contracts.contract_release_date as date_released', 'contracts.contract_receive_date as date_received')
			->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')
			->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
			->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
			->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data = json_decode(json_encode($data));
		$data2 = json_decode(json_encode($data2));

		foreach ($data2 as $row) {
			array_push($data, $row);
		}

		return back()->withInput()->with("data", $data);
	}

	public function submitPostingContract(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(",", $request->input('id'));
		$ids_array = [];

		foreach ($ids as $id) {
			$contract = Contract::find($id);
			if (in_array($contract->contract_id, $ids_array) === false) {
				$cluster_bids = $APP->getClusterBids($contract->project_bid_id);
				if (count($cluster_bids) > 1) {
					foreach ($cluster_bids as $cluster) {
						$contract = Contract::where('project_bid_id', $cluster->project_bid)->first();
						$contract = Contract::find($contract->contract_id);
						$contract->posting_status = "posted";
						$contract->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$contract->save();
						array_push($ids_array, $contract->contract_id);
					}
				} else {
					$contract->posting_status = "posted";
					$contract->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$contract->save();
					array_push($ids_array, $contract->contract_id);
				}
			}
		}

		return back()->withInput()->with('message', 'success');
	}

	public function downloadContractZip(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(",", $request->input('id'));
		$ids_array = [];
		$contract = Contract::find($ids[0]);
		$name = 'CONTRACT-' . Date("Y-m-d", strtotime($request->posting_date)) . md5(uniqid(rand(), true)) . '.zip';
		$zip_file = public_path() . '\\' . 'zips/' . $name;
		$zip = new \ZipArchive();
		$zip->open(
			$zip_file,
			\ZipArchive::CREATE | \ZipArchive::OVERWRITE
		);
		foreach ($ids as $id) {
			$letter = 'A';
			$title = "";
			$contract = Contract::find($id);

			if (in_array($contract->contract_id, $ids_array) === false) {
				$cluster_bids = $APP->getClusterBids($contract->project_bid_id);
				if (count($cluster_bids) > 1) {
					foreach ($cluster_bids as $cluster) {
						$contract = Contract::where('project_bid_id', $cluster->project_bid)->first();
						$contract = Contract::find($contract->contract_id);
						$contract->posting_status = "posted";
						$contract->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$contract->save();

						$temp = $letter . '. ' . $cluster->project_title . ";";
						if ($letter == "A") {
							$title = $temp;
						} else {
							$title = $title . "   " . $temp;
						}
						array_push($ids_array, $contract->contract_id);
						$letter++;
					}
				} else {
					$contract->posting_status = "posted";
					$contract->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$contract->save();
					$title = $cluster_bids[0]->project_title;
					array_push($ids_array, $contract->contract_id);
					$attachments = ArchiveContractAttachments::where("contract_id", $contract->contract_id)->get();
				}
				$attachments = ArchiveContractAttachments::where("contract_id", $contract->contract_id)->get();
				if (count($attachments) > 0) {
					foreach ($attachments as $attachment) {
						// $title = str_replace(' ', '_', $title);
						if (strlen($title) > 200) {
							$title = substr($title, 0, 200);
						}
						$zip->addFile(Storage::disk('drive-d')->path('Archives/Contracts/' . $attachment->attachment_name), "CONTRACT-" . strtoupper(strtolower(str_replace('/', '_', $title))) . "-" . strtoupper(strtolower((str_replace('/', '_', $cluster_bids[0]->business_name)))) . "-OPENED ON " . Date("F d,Y", strtotime($cluster_bids[0]->open_bid)) . ".pdf");
					}
				}
			}
		}
		$zip->close();
		return response()->download($zip_file, $name)->deleteFileAfterSend(true);
	}

	// NoticeToProceeds
	public function getPostingNoticeToProceeds(Request $request)
	{
		if (isset($request->year)) {
			$year = $request->year;
		} else {
			$year = date('Y');
		}

		$data = NoticeToProceed::where([['notice_to_proceeds.ntp_date_generated', 'like', $year . '%'], ['with_attachment', true]])
			->select('municipalities.*', 'procacts.*', 'notice_to_proceeds.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'notice_to_proceeds.ntp_date_generated as date_generated', 'notice_to_proceeds.ntp_date_received_by_contractor as date_received_by_contractor', 'notice_to_proceeds.ntp_date_released as date_released', 'notice_to_proceeds.ntp_date_received as date_received')
			->join('project_bidders', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
			->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
			->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
			->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
			->join(
				'project_plans',
				'procacts.plan_id',
				'project_plans.plan_id'
			)
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data2 = NoticeToProceed::where([['notice_to_proceeds.ntp_date_generated', 'like', $year . '%'], ['with_attachment', true]])
			->select('municipalities.*', 'procacts.*', 'notice_to_proceeds.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'notice_to_proceeds.ntp_date_generated as date_generated', 'notice_to_proceeds.ntp_date_received_by_contractor as date_received_by_contractor', 'notice_to_proceeds.ntp_date_released as date_released', 'notice_to_proceeds.ntp_date_received as date_received')
			->join('project_bidders', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
			->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
			->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
			->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
			->join(
				'project_plans',
				'procacts.plan_id',
				'project_plans.plan_id'
			)
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();



		$data = json_decode(json_encode($data));
		$data2 = json_decode(json_encode($data2));

		foreach ($data2 as $row) {
			array_push($data, $row);
		}

		$title = "Notice To Proceed Posting";
		$links = getUserLinks();
		// $user_privilege=getUserPrivilege();
		$user_privilege = ["add", "update", "delete"];

		return view("posting.ntp", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year]);
	}

	public function filterPostingNoticeToProceeds(Request $request)
	{
		$data = $request->validate([
			"year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
		]);

		$year = $request->year;
		$data = NoticeToProceed::where([['notice_to_proceeds.ntp_date_generated', 'like', $year . '%'], ['with_attachment', true]]);
		if (
			$request->status != null
		) {
			$data = $data->where('notice_to_proceeds.posting_status', $request->status);
		}

		$data = $data
			->select('municipalities.*', 'procacts.*', 'notice_to_proceeds.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'notice_to_proceeds.ntp_date_generated as date_generated', 'notice_to_proceeds.ntp_date_received_by_contractor as date_received_by_contractor', 'notice_to_proceeds.ntp_date_released as date_released', 'notice_to_proceeds.ntp_date_received as date_received')
			->join('project_bidders', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
			->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
			->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
			->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
			->join(
				'project_plans',
				'procacts.plan_id',
				'project_plans.plan_id'
			)
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data2 = NoticeToProceed::where([['notice_to_proceeds.ntp_date_generated', 'like', $year . '%'], ['with_attachment', true]]);
		if (
			$request->status != null
		) {
			$data2 = $data2->where('notice_to_proceeds.posting_status', $request->status);
		}

		$data2 = $data2
			->select('municipalities.*', 'procacts.*', 'notice_to_proceeds.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'notice_to_proceeds.ntp_date_generated as date_generated', 'notice_to_proceeds.ntp_date_received_by_contractor as date_received_by_contractor', 'notice_to_proceeds.ntp_date_released as date_released', 'notice_to_proceeds.ntp_date_received as date_received')
			->join('project_bidders', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
			->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
			->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
			->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
			->join(
				'project_plans',
				'procacts.plan_id',
				'project_plans.plan_id'
			)
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$data = json_decode(json_encode($data));
		$data2 = json_decode(json_encode($data2));

		foreach ($data2 as $row) {
			array_push($data, $row);
		}

		return back()->withInput()->with("data", $data);
	}

	public function submitPostingNoticeToProceed(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(
			",",
			$request->input('id')
		);
		$ids_array = [];

		foreach ($ids as $id) {
			$ntp = NoticeToProceed::find($id);
			if (in_array($ntp->ntp_id, $ids_array) === false) {
				$cluster_bids = $APP->getClusterBids($ntp->project_bid_id);
				if (count($cluster_bids) > 1) {
					foreach ($cluster_bids as $cluster) {
						$ntp = NoticeToProceed::where('project_bid_id', $cluster->project_bid)->first();
						$ntp = NoticeToProceed::find($ntp->ntp_id);
						$ntp->posting_status = "posted";
						$ntp->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$ntp->save();
						array_push($ids_array, $ntp->ntp_id);
					}
				} else {
					$ntp->posting_status = "posted";
					$ntp->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$ntp->save();
					array_push($ids_array, $ntp->ntp_id);
				}
			}
		}

		return back()->withInput()->with('message', 'success');
	}

	public function downloadNoticeToProceedZip(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(",", $request->input('id'));
		$ids_array = [];
		$ntp = NoticeToProceed::find($ids[0]);
		$name = 'NTP-' . Date("Y-m-d", strtotime($request->posting_date)) . md5(uniqid(rand(), true)) . '.zip';
		$zip_file = public_path() . '\\' . 'zips/' . $name;
		$zip = new \ZipArchive();
		$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		foreach ($ids as $id) {
			$letter = 'A';
			$title = "";
			$ntp = NoticeToProceed::find($id);

			if (in_array($ntp->ntp_id, $ids_array) === false) {
				$cluster_bids = $APP->getClusterBids($ntp->project_bid_id);
				if (count($cluster_bids) > 1) {
					foreach ($cluster_bids as $cluster) {
						$ntp = NoticeToProceed::where('project_bid_id', $cluster->project_bid)->first();
						$ntp = NoticeToProceed::find($ntp->ntp_id);
						$ntp->posting_status = "posted";
						$ntp->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$ntp->save();

						$temp = $letter . '. ' . $cluster->project_title . ";";
						if ($letter == "A") {
							$title = $temp;
						} else {
							$title = $title . "   " . $temp;
						}
						array_push($ids_array, $ntp->ntp_id);
						$letter++;
					}
				} else {
					$ntp->posting_status = "posted";
					$ntp->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$ntp->save();
					$title = $cluster_bids[0]->project_title;
					array_push($ids_array, $ntp->ntp_id);
					$attachments = ArchiveNoticeToProceedAttachments::where("ntp_id", $ntp->ntp_id)->get();
				}
				$attachments = ArchiveNoticeToProceedAttachments::where("ntp_id", $ntp->ntp_id)->get();
				if (count($attachments) > 0) {
					foreach ($attachments as $attachment) {
						// $title = str_replace(' ', '_', $title);
						if (strlen($title) > 200) {
							$title = substr($title, 0, 200);
						}
						$zip->addFile(Storage::disk('drive-d')->path('Archives/NTPs/' . $attachment->attachment_name), "NTP-" . strtoupper(strtolower(str_replace('/', '_', $title))) . "-" . strtoupper(strtolower((str_replace('/', '_', $cluster_bids[0]->business_name)))) . "-OPENED ON " . Date("F d,Y", strtotime($cluster_bids[0]->open_bid)) . ".pdf");
					}
				}
			}
		}
		$zip->close();
		return response()->download($zip_file, $name)->deleteFileAfterSend(true);
	}

	// ITBs
	public function getPostingITBs(Request $request)
	{
		if (isset($request->year)) {
			$year = $request->year;
		} else {
			$year = date('Y');
		}

		$data = Procact::where([['project_plans.project_year', 'like', $year . '%'], ['itbrfq_attachment', true], ['project_plans.mode_id', 1]])
			->select('municipalities.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'funds.*')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$title = "ITB Posting";
		$links = getUserLinks();
		$user_privilege = getUserPrivilege();

		return view("posting.itb", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year]);
	}

	public function filterPostingITBs(Request $request)
	{
		$data = $request->validate([
			"year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
		]);


		$year = $request->year;
		$data = Procact::where([['project_plans.project_year', 'like', $year . '%'], ['itbrfq_attachment', true], ['project_plans.mode_id', 1]]);
		if ($request->status != null) {
			$data = $data->where('procacts.posting_status', $request->status);
		}
		$data = $data->select('municipalities.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'funds.*')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();


		return back()->withInput()->with("data", $data);
	}

	public function submitPostingITB(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(
			",",
			$request->input('id')
		);
		$ids_array = [];

		foreach ($ids as $id) {
			$procact = Procact::find($id);
			if (in_array($procact->procact_id, $ids_array) === false) {
				$clustered_projects = DB::table('procacts')->where([['plan_cluster_id', '<>', $procact->plan_cluster_id], ['open_bid', $procact->open_bid]])->get();
				if (count($clustered_projects) > 1) {
					foreach ($clustered_projects as $cluster) {
						$procact = Procact::find($cluster->procact_id);
						$procact->posting_status = "posted";
						$procact->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$procact->save();
						array_push($ids_array, $procact->procact_id);
					}
				} else {
					$procact->posting_status = "posted";
					$procact->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$procact->save();
					array_push($ids_array, $procact->procact_id);
				}
			}
		}

		return back()->withInput()->with('message', 'success');
	}

	public function downloadITBZip(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(
			",",
			$request->input('id')
		);
		$ids_array = [];
		$procact = Procact::find($ids[0]);
		$name = 'ITB-' . Date("Y-m-d", strtotime($request->posting_date)) . md5(uniqid(rand(), true)) . '.zip';
		$zip_file = public_path() . '\\' . 'zips/' . $name;
		$zip = new \ZipArchive();
		$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		foreach ($ids as $id) {
			$letter = 'A';
			$title = "";
			$procact = Procact::find($id);
			if (in_array($procact->procact_id, $ids_array) === false) {

				if ($procact->plan_cluster_id != null) {
					$clustered_projects = DB::table('procacts')->where([['plan_cluster_id', $procact->plan_cluster_id], ['procacts.open_bid', $procact->open_bid]])->join('project_plans', 'project_plans.plan_id', 'procacts.plan_id')->get();
					foreach ($clustered_projects as $cluster) {
						$procact = Procact::find($cluster->procact_id);
						$procact->posting_status = "posted";
						$procact->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$procact->save();

						$temp = $letter . '. ' . $cluster->project_title . ";";
						if ($letter == "A") {
							$title = $temp;
						} else {
							$title = $title . " " . $temp;
						}
						array_push($ids_array, $cluster->procact_id);
						$letter++;
					}
				} else {
					$plan = DB::table('project_plans')->where('plan_id', $procact->plan_id)->first();
					$procact->posting_status = "posted";
					$procact->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$procact->save();
					$title = $plan->project_title;
					array_push($ids_array, $procact->procact_id);
					$attachments = ArchiveITBAttachments::where("procact_id", $procact->procact_id)->get();
				}
				$attachments = ArchiveITBAttachments::where("procact_id", $procact->procact_id)->get();
				if (
					count($attachments) > 0
				) {
					foreach ($attachments as $attachment) {
						// $title = str_replace(' ', '_', $title);
						if (strlen($title) > 200) {
							$title = substr($title, 0, 200);
						}

						$zip->addFile(Storage::disk('drive-d')->path('Archives/Invitation To Bid/' . $attachment->attachment_name), "ITB-" . strtoupper(strtolower(str_replace('/', '_', $title))) . "-OPENED ON " . Date("F d,Y", strtotime($procact->open_bid)) . ".pdf");
					}
				}
			}
		}
		$zip->close();
		return response()->download($zip_file, $name)->deleteFileAfterSend(true);
	}

	// RFQs
	public function getPostingRFQs(Request $request)
	{
		if (isset($request->year)) {
			$year = $request->year;
		} else {
			$year = date('Y');
		}

		$data = Procact::where([['project_plans.project_year', 'like', $year . '%'], ['itbrfq_attachment', true]])
			->whereIn('project_plans.mode_id', [2, 3])
			->select('municipalities.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'funds.*')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();

		$title = "RFQ Posting";
		$links = getUserLinks();
		$user_privilege = getUserPrivilege();

		return view("posting.rfq", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year]);
	}

	public function filterPostingRFQs(Request $request)
	{
		$data = $request->validate([
			"year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
		]);


		$year = $request->year;
		$data = Procact::where([['project_plans.project_year', 'like', $year . '%'], ['itbrfq_attachment', true]])
			->whereIn('project_plans.mode_id', [2, 3]);
		if ($request->status != null) {
			$data = $data->where('procacts.posting_status', $request->status);
		}
		$data = $data->select('municipalities.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'funds.*')
			->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
			->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
			->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
			->join('funds', 'project_plans.fund_id', 'funds.fund_id')
			->get();


		return back()->withInput()->with("data", $data);
	}

	public function submitPostingRFQ(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(
			",",
			$request->input('id')
		);
		$ids_array = [];

		foreach ($ids as $id) {
			$procact = Procact::find($id);
			if (in_array($procact->procact_id, $ids_array) === false) {
				$clustered_projects = DB::table('procacts')->where([['plan_cluster_id', '<>', $procact->plan_cluster_id], ['open_bid', $procact->open_bid]])->get();
				if (count($clustered_projects) > 1) {
					foreach ($clustered_projects as $cluster) {
						$procact = Procact::find($cluster->procact_id);
						$procact->posting_status = "posted";
						$procact->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$procact->save();
						array_push($ids_array, $procact->procact_id);
					}
				} else {
					$procact->posting_status = "posted";
					$procact->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$procact->save();
					array_push($ids_array, $procact->procact_id);
				}
			}
		}

		return back()->withInput()->with('message', 'success');
	}

	public function downloadRFQZip(Request $request)
	{
		$APP = new APP;
		$data = $request->validate([
			"posting_date" => "required"
		]);
		$ids = explode(
			",",
			$request->input('id')
		);
		$ids_array = [];
		$procact = Procact::find($ids[0]);
		$name = 'RFQ-' . Date("Y-m-d", strtotime($request->posting_date)) . md5(uniqid(rand(), true)) . '.zip';
		$zip_file = public_path() . '\\' . 'zips/' . $name;
		$zip = new \ZipArchive();
		$zip->open(
			$zip_file,
			\ZipArchive::CREATE | \ZipArchive::OVERWRITE
		);
		foreach ($ids as $id) {
			$letter = 'A';
			$title = "";
			$procact = Procact::find($id);
			if (in_array($procact->procact_id, $ids_array) === false) {

				if ($procact->plan_cluster_id != null) {
					$clustered_projects = DB::table('procacts')->where([['plan_cluster_id', $procact->plan_cluster_id], ['procacts.open_bid', $procact->open_bid]])->join('project_plans', 'project_plans.plan_id', 'procacts.plan_id')->get();
					foreach ($clustered_projects as $cluster) {
						$procact = Procact::find($cluster->procact_id);
						$procact->posting_status = "posted";
						$procact->posting_date = Date("Y-m-d", strtotime($request->posting_date));
						$procact->save();

						$temp = $letter . '. ' . $cluster->project_title . ";";
						if ($letter == "A") {
							$title = $temp;
						} else {
							$title = $title . " " . $temp;
						}
						array_push($ids_array, $cluster->procact_id);
						$letter++;
					}
				} else {
					$plan = DB::table('project_plans')->where('plan_id', $procact->plan_id)->first();
					$procact->posting_status = "posted";
					$procact->posting_date = Date("Y-m-d", strtotime($request->posting_date));
					$procact->save();
					$title = $plan->project_title;
					array_push($ids_array, $procact->procact_id);
					$attachments = ArchiveRFQAttachments::where("procact_id", $procact->procact_id)->get();
				}
				$attachments = ArchiveRFQAttachments::where("procact_id", $procact->procact_id)->get();
				if (
					count($attachments) > 0
				) {
					foreach ($attachments as $attachment) {
						// $title = str_replace(' ', '_', $title);
						if (strlen($title) > 200) {
							$title = substr($title, 0, 200);
						}

						$zip->addFile(Storage::disk('drive-d')->path('Archives/Invitation To Bid/' . $attachment->attachment_name), "RFQ-" . strtoupper(strtolower(str_replace('/', '_', $title))) . "-OPENED ON " . Date("F d,Y", strtotime($procact->open_bid)) . ".pdf");
					}
				}
			}
		}
		$zip->close();
		return response()->download($zip_file, $name)->deleteFileAfterSend(true);
	}
}
