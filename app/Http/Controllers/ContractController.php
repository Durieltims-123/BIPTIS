<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\{APP, Contract, Procact, NoticeOfAward};
use Validator;
use App\Http\Controllers\ProcurementController;
use PhpOffice\PhpWord\TemplateProcessor;

class ContractController extends Controller
{
  public function getPerformanceBond(Request $request)
  {
    $APP = new APP;
    if ($request->project_year != null) {
      $year = $request->project_year;
      $project_plans = $APP->getSpecificProcurementActivity('performance_bond', $year);
      return back()->withInput()->with("project_plans", $project_plans);
    } else {
      $year = date('Y');
      $title = "PERFORMANCE BOND";
      $project_plans = $APP->getSpecificProcurementActivity('performance_bond', $year);
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();


      return view('admin.performance_bond', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
    }
  }

  public function submitPerformanceBond(Request $request)
  {
    // $contracts = Contract::all();
    // foreach ($contracts as $contract) {
    //   if ($contract->performance_bond_issuance == null) {
    //     $ct = Contract::find($contract->contract_id);
    //     $ct->performance_bond_issuance = $contract->performance_bond_posted;
    //     $ct->save();
    //   }
    //   if ($contract->performance_bond_posted == null) {
    //     $ct = Contract::find($contract->contract_id);
    //     $ct->performance_bond_posted = $contract->performance_bond_issuance;
    //     $ct->save();
    //   }
    // }

    $APP = new APP;
    $project_bid = $request->input('project_bid');
    $contract_id = $request->input('contract_id');
    $message = "success";
    $noa = DB::table('notice_of_awards')->where("project_bid_id", $project_bid)->first();
    $existing_contract = Contract::where('project_bid_id', $project_bid)->first();
    $cluster_bids = $APP->getClusterBids($project_bid);
    $data = $request->validate([
      "performance_bond_issuance" => "required|before:tomorrow|after_or_equal:" . $noa->date_received_by_contractor,
      "performance_bond_expiration" => "required|after:performance_bond_issuance",
      "date_received_by_bac" => "required|before:tomorrow|after_or_equal:request_to_submit_performance_bond"
    ]);
    $date_received_by_bac = date("Y-m-d", strtotime($request->input('date_received_by_bac')));
    $performance_bond_expiration = date("Y-m-d", strtotime($request->input('performance_bond_expiration')));
    $performance_bond_issuance = date("Y-m-d", strtotime($request->input('performance_bond_issuance')));
    $performance_bond_duration = $request->input('performance_bond_duration');
    $performance_bond_remarks = $request->input('performance_bond_remarks');

    if ($existing_contract === null) {
      // Duplicate Checker
      foreach ($cluster_bids as $cluster_bid) {
        $duplicate = Contract::where('project_bid_id', $cluster_bid->project_bid)->count();
        if ($duplicate === 0) {

          $contractor = $APP->getBid($cluster_bid->project_bid);
          // insert into contracts
          $insert = Contract::create([
            "project_bid_id" => $cluster_bid->project_bid,
            "contractor_id" => $contractor->contractor_id,
            "performance_bond_receive_date" => $date_received_by_bac,
            "performance_bond_duration" => $performance_bond_duration,
            "performance_bond_remarks" => $performance_bond_remarks,
            "performance_bond_expiration" => $performance_bond_expiration,
            "performance_bond_issuance" => $performance_bond_issuance,
            "performance_bond_posted" => $performance_bond_issuance,
          ]);
        }
      }
      // else{
      //   $message="duplicate";
      // }
    } else {
      // Duplicate Checker
      foreach ($cluster_bids as $cluster_bid) {
        $contract = Contract::where('project_bid_id', $cluster_bid->project_bid)->first();
        $duplicate = Contract::where([['project_bid_id', $cluster_bid->project_bid], ['contract_id', "<>", $contract->contract_id]])->count();
        if ($duplicate === 0) {
          //  update in contracts
          $contractor = $APP->getBid($cluster_bid->project_bid);
          $update = Contract::where("contract_id", $contract->contract_id)
            ->update([
              "project_bid_id" => $cluster_bid->project_bid,
              "contractor_id" => $contractor->contractor_id,
              "performance_bond_receive_date" => $date_received_by_bac,
              "performance_bond_duration" => $performance_bond_duration,
              "performance_bond_remarks" => $performance_bond_remarks,
              "performance_bond_expiration" => $performance_bond_expiration,
              "performance_bond_issuance" => $performance_bond_issuance,
              "performance_bond_posted" => $performance_bond_issuance,
              "updated_at" => now(),
            ]);
        }
      }
    }
    return back()->with("message", $message);
  }

  public function prepareContract(Request $request)
  {
    $APP = new APP;
    if ($request->project_year != null) {
      $year = $request->project_year;
      $project_plans = $APP->getSpecificProcurementActivity('for_contract_generation', $year);
      return back()->withInput()->with("project_plans", $project_plans);
    } else {
      $year = date('Y');
      $title = "PREPARE CONTRACTS";
      $project_plans = $APP->getSpecificProcurementActivity('for_contract_generation', $year);
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.prepare_contracts', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
    }
  }

  public function submitContract(Request $request)
  {
    $data = $request->validate([
      "date_generated" => "required",
      "date_released" => "nullable|after_or_equal:date_generated",
      "date_received_contractor" => "nullable|after_or_equal:date_released",
      "date_of_notarization" => "nullable|after_or_equal:date_received_contractor|required_with:date_released",
      "date_received" => "nullable|after_or_equal:date_of_notarization|required_if:date_received_contractor,!=,null|required_if:date_of_notarization,!=,null"
    ]);

    $APP = new APP;

    $contract_id = $request->input('contract_id');
    $project_bid = $request->input('project_bid');
    $date_generated = date("Y-m-d", strtotime($request->input('date_generated')));
    $date_released = null;
    $date_received_contractor = null;
    $date_received = null;
    $date_of_notarization = null;

    if ($request->input('date_released')) {
      $date_released = date("Y-m-d", strtotime($request->input('date_released')));
    }
    if ($request->input('date_received_contractor')) {
      $date_received_contractor = date("Y-m-d", strtotime($request->input('date_received_contractor')));
    }
    if ($request->input('date_of_notarization')) {
      $date_of_notarization = date("Y-m-d", strtotime($request->input('date_of_notarization')));
    }
    if ($request->input('date_received')) {
      $date_received = date("Y-m-d", strtotime($request->input('date_received')));
    }
    $remarks = $request->input('remarks');

    $cluster_bids = $APP->getClusterBids($project_bid);
    $plan_ids_array = array_column((array)json_decode($cluster_bids), 'plan_id');
    $plan_ids = implode(",", $plan_ids_array);
    $message = "success";


    $noa = DB::table('notice_of_awards')->where("project_bid_id", $project_bid)->first();
    $existing_contract = Contract::where('project_bid_id', $project_bid)->first();
    // $old_receive=$existing_contract->contract_receive_date;

    if ($existing_contract === null) {
      foreach ($cluster_bids as $cluster_bid) {
        $create_contract = Contract::insert([
          "project_bid_id" => $cluster_bid->project_bid,
          "performance_bond_posted" => null,
          "performance_bond_duration" => null,
          "performance_bond_remarks" => null,
        ]);
      }
    }


    if ($date_received != null) {
      foreach ($cluster_bids as $cluster_bid) {
        $contract = Contract::where('project_bid_id', $cluster_bid->project_bid)->first();
        if ($contract->performance_bond_posted === null) {
          $message = "performance_bond_error";
        }
      }

      if ($message === "success") {
        $noa = NoticeOfAward::where('project_bid_id', $cluster_bid->project_bid)->first();
        $noa_end = date('m/d/Y', strtotime($noa->date_received_by_contractor));
        $data = $request->validate([
          "date_released" => "after_or_equal:" . $noa_end,
          "date_received_contractor" => "after_or_equal:date_released",
          "date_of_notarization" => "after_or_equal:date_received_contractor",
          "date_received" => "after_or_equal:date_of_notarization"
        ]);
      }
    }

    if ($message === "success") {
      foreach ($cluster_bids as $cluster_bid) {
        $contract_id = Contract::where('project_bid_id', $cluster_bid->project_bid)->first();
        $contract = Contract::find($contract_id->contract_id);
        $contract->contract_date_generated = $date_generated;
        $contract->contract_release_date = $date_released;
        $contract->contract_date_received_contractor = $date_received_contractor;
        $contract->contract_receive_date = $date_received;
        $contract->contract_date_of_notarization = $date_of_notarization;
        $contract->contract_remarks = $remarks;
        $contract->save();
      }

      // if($date_received!=null && $date_received!=$old_receive){
      //   if($procact->award_notice!=date("Y-m-d", strtotime($request->input("date_received_by_bac")))&&$procact->proceed_notice===null){
      //     $extend=$APP->extendSpecificProcess($plan_ids,"contract_preparation_signing",$request->input("date_received"),"Automatic Extension");
      //     $ProcurementController=new ProcurementController;
      //     $parameters=["plan_id"=>$plan_ids,"contract_preparation_and_signing_date"=>date("m/d/Y", strtotime($date_received)),"bypass"=>true];
      //     $request = new \Illuminate\Http\Request();
      //     $request->replace($parameters);
      //     $ProcurementController->submitContractPreparationAndSigning($request);
      //   }
      // }
      return back()->with("message", $message);
    } else {
      return back()->with("message", $message)->withInput();
    }
  }

  public function generateContract($id)
  {
    $APP = new APP;
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $contract = Contract::where('contract_id', $id)->first();
    if ($contract === null) {
      return abort(403, "Unknown Contract");
    } else {
      $cluster_bids = $APP->getClusterBids($contract->project_bid_id);
      $project_plan = DB::table('project_plans')
        ->where('project_bidders.project_bid', $contract->project_bid_id)
        ->select('project_plans.project_bid_id', 'governors.name as governor_name', 'governors.governor_id', 'procacts.plan_id', 'procacts.procact_id', 'procacts.plan_cluster_id', 'municipalities.municipality_name', 'project_plans.project_title', 'contractors.*', 'barangays.*')
        ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('rfq_projects', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'contractors.contractor_id', 'rfqs.contractor_id')
        ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
        ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
        ->leftJoin('governors', 'governors.governor_id', 'project_plans.governor_id')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->first();

      if ($project_plan === null) {
        $project_plan = DB::table('project_plans')
          ->where('project_bidders.project_bid', $contract->project_bid_id)
          ->select('project_plans.project_bid_id', 'governors.name as governor_name', 'governors.governor_id', 'procacts.plan_id', 'procacts.procact_id', 'procacts.plan_cluster_id', 'municipalities.municipality_name', 'project_plans.project_title', 'contractors.*', 'barangays.*')
          ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
          ->join('bid_doc_projects', 'bid_doc_projects.procact_id', 'procacts.procact_id')
          ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
          ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
          ->join('contractors', 'contractors.contractor_id', 'bid_docs.contractor_id')
          ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
          ->leftJoin('governors', 'governors.governor_id', 'project_plans.governor_id')
          ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
          ->orderBy('procacts.itb_arrangement', 'asc')
          ->first();
      }

      $governor_name = $project_plan->governor_name;
      if ($governor_name === null) {
        $governor = DB::table('governors')->orderBy('governor_id', 'desc')->first();
        $governor_name = $governor->name;
      }
      $barangay = "";
      $barangay_ids = array_column((array)json_decode($cluster_bids), 'barangay_id');
      $title = "";
      $duration = 0;
      $source = "";
      $letter = "A";
      $cluster_bids_string = "";

      if (count($cluster_bids) > 1) {
        foreach ($cluster_bids as $cluster_bid) {
          $duration = $duration + $cluster_bid->duration;
          if ($title === "") {
            $title = $letter . ".) " . $cluster_bid->project_title;
          } else {
            $title = $title . " " . $letter . ".) " . $cluster_bid->project_title;
          }
          if ($source === "") {
            $source = $letter . ".) " . $cluster_bid->source;
          } else {
            $source = $source . " " . $letter . ".) " . $cluster_bid->source;
          }
          if ($cluster_bid->minimum_detailed_cost > 0) {
            $bid_in_words = strtoupper($formatter->format((int)$cluster_bid->minimum_detailed_cost)) . " PESOS";
            $decimal = $cluster_bid->minimum_detailed_cost - (int)$cluster_bid->minimum_detailed_cost;
            $decimal = number_format($decimal, 2, '.', ',');
            $decimal = str_replace('0.', '', $decimal);
            if ((int)$decimal >= 1) {
              $bid_in_words = $bid_in_words . " AND " . strtoupper($formatter->format((int)$decimal)) . " CENTAVOS";
            }

            if ($cluster_bids_string === "") {
              $cluster_bids_string = $letter . ".) " . $bid_in_words . " (Php " . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . ")";
            } else {
              $cluster_bids_string = $cluster_bids_string . " " . $letter . ".)" . $bid_in_words . " ( Php " . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . ")";
            }
          }


          ++$letter;
        }
      } else {
        $title = $cluster_bids[0]->project_title;
        $source = $cluster_bids[0]->source;
        $duration = $cluster_bids[0]->duration;
      }
      if (count($cluster_bids) > 1) {
        $project_label = "projects";
      } else {
        $project_label = "project";
      }

      $name_array = explode(' ', $project_plan->owner);
      if (strpos(strtolower(end($name_array)), 'jr') === false && strpos(strtolower(end($name_array)), 'sr') === false) {
        $last_name = end($name_array);
      } else {
        $last_name = $name_array[count($name_array) - 2];
      }
      if ($project_plan->barangay_name != null) {
        if (count(array_unique($barangay_ids)) === 1) {
          $barangay = $project_plan->barangay_name . ',';
        } else {
          $barangay = "";
        }
      }
      $title = strtoupper(strtolower($title));
      $title = htmlspecialchars($title);
      $business_name = htmlspecialchars($project_plan->business_name);

      $responsive_bidder = $APP->getBiddersData($project_plan->procact_id, 'responsive');
      $bid = $responsive_bidder[0]->final_minimum_cost;
      $bid_in_words = strtoupper($formatter->format((int)$responsive_bidder[0]->final_minimum_cost)) . " PESOS";
      $decimal = $responsive_bidder[0]->final_minimum_cost - (int)$responsive_bidder[0]->final_minimum_cost;
      $decimal = number_format($decimal, 2, '.', ',');
      $decimal = str_replace('0.', '', $decimal);
      if ((int)$decimal >= 1) {
        $bid_in_words = $bid_in_words . " AND " . strtoupper($formatter->format((int)$decimal)) . " CENTAVOS";
      }

      $day = date('jS', strtotime($contract->contract_date_generated));
      $month_year = date('F Y', strtotime($contract->contract_date_generated));

      $filename = 'Contract' . md5(date('Y-m-d H:i:s:u')) . ".docx";
      $templateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/Contract.docx");
      $templateProcessor->setValue('day', $day);
      $templateProcessor->setValue('month_year', $month_year);
      $templateProcessor->setValue('position', $project_plan->position);
      $templateProcessor->setValue('project_label', $project_label);
      $templateProcessor->setValue('project_title', $title);
      $templateProcessor->setValue('owner', strtoupper(strtolower($project_plan->owner)));
      $templateProcessor->setValue('last_name', $last_name);
      $templateProcessor->setValue('business_name', $business_name);
      $templateProcessor->setValue('barangay', $barangay);
      $templateProcessor->setValue('duration', $duration);
      $templateProcessor->setValue('municipality', $project_plan->municipality_name);
      $templateProcessor->setValue('address', $project_plan->address);
      $templateProcessor->setValue('project_title', $title);
      if ($cluster_bids_string != "") {
        $templateProcessor->setValue('bid', $cluster_bids_string);
        $templateProcessor->setValue('bid_in_words', "");
      } else {
        $templateProcessor->setValue('bid', "Php" . number_format((float)$bid, 2, '.', ','));
        $templateProcessor->setValue('bid_in_words', "(" . $bid_in_words . ")");
      }
      $templateProcessor->setValue('bid_in_words', $bid_in_words);
      $templateProcessor->setValue('governor', strtoupper(strtolower($governor_name)));
      $templateProcessor->saveAs(public_path() . '\\' . 'word_results/' . $filename);
      return  response()->download(public_path() . '\\' . 'word_results/' . $filename)->deleteFileAfterSend(true);
    }
  }
}
