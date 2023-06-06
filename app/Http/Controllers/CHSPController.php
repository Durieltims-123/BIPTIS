<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use App\CHSP;
use App\Contract;


class CHSPController extends Controller
{
	public function getCHSP(Request $request)
	{
		$APP = new APP;

		if($request->project_year!=null){
			$year=$request->project_year;
			$project_plans=$APP->getSpecificProcurementActivity('chsp',$year);
			return back()->withInput()->with("project_plans",$project_plans);
		}
		else{
			$year=date('Y');
			$title="Construction Safety and Health Program";
			$project_plans=$APP->getSpecificProcurementActivity('chsp',$year);
			$links=getUserLinks();
			// $user_privilege=getUserPrivilege();
			$user_privilege=['view','add','update'];
			return view('admin.chsp',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>$title,'project_plans'=>$project_plans,'year'=>$year]);
		}
	}

	public function submitCHSP(Request $request)
	{
		$APP = new APP;
		$project_bid=$request->input('project_bid');
		$message="success";
		$noa=DB::table('notice_of_awards')->where("project_bid_id",$project_bid)->first();
		$existing_chsp=CHSP::where('chsp_project_bid',$project_bid)->first();
		$cluster_bids=$APP->getClusterBids($project_bid);
		$data=$request->validate([
			"chsp_issuance" => "required|before:tomorrow|after_or_equal:".$noa->date_received_by_contractor,
			"date_received_by_bac"=>"required|before:tomorrow|after_or_equal:chsp_issuance"
		]);
		$date_received_by_bac=date("Y-m-d", strtotime($request->input('date_received_by_bac')));
		$chsp_issuance=date("Y-m-d", strtotime($request->input('chsp_issuance')));
		$chsp_remarks=$request->input('chsp_remarks');

		if($existing_chsp===null){
			foreach($cluster_bids as $cluster_bid){
				$contractor=$APP->getBid($cluster_bid->project_bid);
				// insert into chsp
				$insert=CHSP::create([
					"chsp_project_bid"=>$cluster_bid->project_bid,
					"contractor_id"=>$contractor->contractor_id,
					"chsp_date_issuance"=>$chsp_issuance,
					"chsp_received_date"=>$date_received_by_bac,
					"chsp_remarks"=>$chsp_remarks
				]);
			}
		}
		else{
			foreach($cluster_bids as $cluster_bid){
				$contractor=$APP->getBid($cluster_bid->project_bid);
				$CHSP=chsp::where('chsp_project_bid',$cluster_bid->project_bid)->first();
				$CHSP=CHSP::find($CHSP->chsp_id);
				$CHSP->chsp_date_issuance=$chsp_issuance;
				$CHSP->chsp_received_date=$date_received_by_bac;
				$CHSP->chsp_remarks=$chsp_remarks;
				$CHSP->save();
			}
		}

		return back()->with("message", $message);

	}

}
