<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\{APP, Meeting, NoticeToProceed};
use Validator;
use PhpOffice\PhpWord\Element\Field;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\ProcurementController;

class ReportController2 extends Controller
{

  public function generateProjectMonitoringReport()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("admin.generate_pmr", ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function downloadPMR($date_start, $date_end)
  {
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $date_start = $date_start;
    $date_end = $date_end;
    $ids_array = [];
    $APP = new APP;
    $count = 1;
    $plans = DB::table('project_plans')
      ->select('*', 'notice_of_awards.date_released AS noa_date_released', 'procacts.open_bid as bidding_date', DB::raw('DATE_FORMAT(resolutions.resolution_date, "%Y-%m") AS month_group'))
      ->whereRaw('resolutions.resolution_date BETWEEN CAST( "' . $date_start . '" AS DATE) AND CAST( "' . $date_end . '" AS DATE) AND (resolutions.type="RRA" OR resolutions.type IS NULL)')
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->join('project_bidders', 'project_plans.project_bid_id', 'project_bidders.project_bid')
      ->join('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->leftJoin('resolution_projects', 'procacts.procact_id', 'resolution_projects.procact_id')
      ->leftJoin('resolutions', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
      ->orderBy('municipalities.municipality_name', 'asc')
      ->orderBy('month_group', 'asc')
      ->orderBy('procacts.open_bid', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();

    if (count($plans) > 0) {



      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/PMR.xlsx");
      $worksheet = $spreadsheet->getActiveSheet();
      $municipality_group = null;
      $sub_total_rows = [];
      $row = 9;
      $municipality_style = [
        'font' => ['bold'  =>  true, 'size'  =>  12, 'name'  =>  'Arial', 'color' => array('rgb' => '000000'),],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D3D3D3']]
      ];

      $borderedStyleArray = [
        'borders' => [
          'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
          ]
        ]
      ];

      $right_align = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

      foreach ($plans as $plan) {
        if (in_array($plan->plan_id, $ids_array) === false) {
          $observer_names = "";
          $meeting = Meeting::where([['meeting_date', $plan->open_bid]])->first();
          $prebid_meeting = Meeting::where([['meeting_date', $plan->pre_bid], ['date_received', '<>', null]])->join('meeting_observer', 'meeting_observer.meeting_id', 'meeting.meeting_id')->first();
          $opening_meeting = Meeting::where([['meeting_date', $plan->open_bid]])->join('meeting_observer', 'meeting_observer.meeting_id', 'meeting.meeting_id')->first();
          if ($meeting != null) {
            $observer_names = "";
            $observers = DB::table('bac_observer')
              ->where([['bac_id', $meeting->bac_id]])
              ->select(DB::raw("CONCAT(if(observer.observer_prefix is null ,'',CONCAT(observer.observer_prefix,' ')),observer.observer_fname,' ',if(observer.observer_minitial is null ,'',observer.observer_minitial),' ',observer.observer_lname) AS observer_name"), 'observer.*')
              ->join('observer', 'observer.observer_id', '=', 'bac_observer.observer_id')
              ->get();

            foreach ($observers as $data) {
              if ($data->observer_name != null) {
                if ($observer_names === "") {
                  $observer_names = $data->observer_name;
                } else {
                  $observer_names = $observer_names . ' , ' . $data->observer_name;
                }
              } else {
                if ($observer_names === "") {
                  $observer_names = $data->observer_office . " Representative";
                } else {
                  $observer_names = $observer_names . ' , ' . $data->observer_office . "Representative";
                }
              }
            }
          }

          if ($plan->municipality_name != $municipality_group) {
            $worksheet->insertNewRowBefore($row + 1, 1);
            $worksheet->getCell('B' . $row)->setValue($plan->municipality_name);
            $worksheet->getStyle("B" . $row . ":" . "BA" . $row)->applyFromArray($borderedStyleArray);
            $worksheet->getStyle("B" . $row . ":" . "BA" . $row)->applyFromArray($municipality_style);
            $row++;
            $municipality_group = $plan->municipality_name;
          }

          if ($plan->plan_cluster_id != null) {

            $letter = 'A';
            $total = 0;
            $title = "";
            $source_of_fund = "";
            $project_number = "";
            $project_cost = "";
            $duration = 0;
            $duration_count = 0;
            $start_row = $row;
            $detailed_cost = 0;
            $is_same_duration = true;
            $initial_duration = $plan->duration;
            $has_zero_duration = false;
            $non_zero_duration = null;
            $clusters = DB::table('project_plans')
              ->where([['project_timelines.bid_submission_start', $plan->bid_submission_start], ['procacts.plan_cluster_id', $plan->plan_cluster_id]])
              ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
              ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
              ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
              ->leftJoin('resolution_projects', 'procacts.procact_id', 'resolution_projects.procact_id')
              ->leftJoin('resolutions', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
              ->orderBy('procacts.itb_arrangement', 'asc')
              ->get();


            if (count($clusters) <= 1) {
              // dd("Please Recheck Clustering of ".$plan->project_title);
            }


            foreach ($clusters as $cluster) {
              $worksheet->getStyle("B" . $row . ":" . "BA" . $row)->applyFromArray($borderedStyleArray);
              array_push($ids_array, $cluster->plan_id);
              $temp_title = $letter . ') ' . htmlspecialchars($cluster->project_title);
              $temp_source = $letter . ') ' . htmlspecialchars($cluster->source);
              $temp_project_number = htmlspecialchars($cluster->project_no);
              $worksheet->getCell('B' . $row)->setValue($temp_project_number);
              $worksheet->getCell('C' . $row)->setValue($temp_title);
              $worksheet->getCell('AM' . $row)->setValue($temp_source);
              $letter = ++$letter;
              $worksheet->getCell('AK' . $row)->setValue($cluster->duration . "CD");
              $worksheet->insertNewRowBefore($row + 1, 1);
              $worksheet->getCell('AN' . $row)->setValue($cluster->project_cost);
              $winner = $APP->getBiddersData($cluster->latest_procact_id, 'responsive');

              if ($initial_duration != $cluster->duration) {
                $is_same_duration = false;
              } else {
                if ($cluster->duration > 0) {
                  $non_zero_duration = $cluster->duration;
                }
              }

              if ($winner[0]->minimum_detailed_cost != null) {
                $detailed_cost++;
              }
              $worksheet->getCell('AQ' . $row)->setValue($winner[0]->minimum_detailed_cost);
              if ($cluster->account_id == 1) {
                $worksheet->getCell('AO' . $row)->setValue($cluster->project_cost);
                $worksheet->getCell('AR' . $row)->setValue($winner[0]->minimum_detailed_cost);
              } else {
                $worksheet->getCell('AP' . $row)->setValue($cluster->project_cost);
                $worksheet->getCell('AS' . $row)->setValue($winner[0]->minimum_detailed_cost);
              }
              $worksheet->getCell('BA' . $row)->setValue($cluster->remarks);

              $worksheet->getCell('AT' . $row)->setValue($observer_names);

              if ($prebid_meeting != null) {
                $worksheet->getCell('AU' . $row)->setValue(date("m/d/Y", strtotime($prebid_meeting->date_received)));
              }
              if ($opening_meeting != null) {
                $worksheet->getCell('AV' . $row)->setValue(date("m/d/Y", strtotime($opening_meeting->date_received)));
                $worksheet->getCell('AW' . $row)->setValue(date("m/d/Y", strtotime($opening_meeting->date_received)));
                $worksheet->getCell('AX' . $row)->setValue(date("m/d/Y", strtotime($opening_meeting->date_received)));
              }

              $row++;
            }

            $worksheet->mergeCells("W" . $start_row . ":" . "W" . ($row - 1));
            $worksheet->mergeCells("X" . $start_row . ":" . "X" . ($row - 1));
            $worksheet->mergeCells("Y" . $start_row . ":" . "Y" . ($row - 1));
            $worksheet->mergeCells("Z" . $start_row . ":" . "Z" . ($row - 1));
            $worksheet->mergeCells("AA" . $start_row . ":" . "AA" . ($row - 1));
            $worksheet->mergeCells("AB" . $start_row . ":" . "AB" . ($row - 1));
            $worksheet->mergeCells("AC" . $start_row . ":" . "AC" . ($row - 1));
            $worksheet->mergeCells("AD" . $start_row . ":" . "AD" . ($row - 1));
            $worksheet->mergeCells("AE" . $start_row . ":" . "AE" . ($row - 1));
            $worksheet->mergeCells("AF" . $start_row . ":" . "AF" . ($row - 1));
            $worksheet->mergeCells("AG" . $start_row . ":" . "AG" . ($row - 1));
            $worksheet->mergeCells("AH" . $start_row . ":" . "AH" . ($row - 1));
            $worksheet->mergeCells("AI" . $start_row . ":" . "AI" . ($row - 1));
            $worksheet->mergeCells("AJ" . $start_row . ":" . "AJ" . ($row - 1));
            $worksheet->mergeCells("AL" . $start_row . ":" . "AL" . ($row - 1));
            $worksheet->mergeCells("AT" . $start_row . ":" . "AT" . ($row - 1));
            $worksheet->mergeCells("AU" . $start_row . ":" . "AU" . ($row - 1));
            $worksheet->mergeCells("AV" . $start_row . ":" . "AV" . ($row - 1));
            $worksheet->mergeCells("AW" . $start_row . ":" . "AW" . ($row - 1));
            $worksheet->mergeCells("AX" . $start_row . ":" . "AX" . ($row - 1));



            $worksheet->getCell('W' . $start_row)->setValue("PLGU-Benguet");
            $worksheet->getCell('X' . $start_row)->setValue("No");
            if ($plan->mode == "Bidding") {
              $worksheet->getCell('Y' . $start_row)->setValue("Public " . $plan->mode);
            } else {
              $worksheet->getCell('Y' . $start_row)->setValue($plan->mode);
            }
            if ($plan->pre_proc_date != null) {
              $worksheet->getCell('Z' . $start_row)->setValue($plan->pre_proc_date);
            } else {
              $worksheet->getCell('Z' . $start_row)->setValue("N/A");
            }
            $worksheet->getCell('AA' . $start_row)->setValue(date("m/d/Y", strtotime($plan->advertisement_start)));
            if ($plan->pre_bid_start != null) {
              $worksheet->getCell('AB' . $start_row)->setValue(date("m/d/Y", strtotime($plan->pre_bid_start)));
            } else {
              $worksheet->getCell('AB' . $start_row)->setValue("N/A");
            }
            $worksheet->getCell('AC' . $start_row)->setValue(date("m/d/Y", strtotime($plan->bid_submission_start)));
            $worksheet->getCell('AD' . $start_row)->setValue(date("m/d/Y", strtotime($plan->bid_submission_start)));
            $worksheet->getCell('AE' . $start_row)->setValue(date("m/d/Y", strtotime($plan->bid_evaluation_start)));
            $worksheet->getCell('AF' . $start_row)->setValue(date("m/d/Y", strtotime($plan->bid_evaluation_start)));
            if ($plan->resolution_date != null) {
              $worksheet->getCell('AG' . $start_row)->setValue(date("m/d/Y", strtotime($plan->resolution_date)));
            }
            $worksheet->getCell('AH' . $start_row)->setValue(date("m/d/Y", strtotime($plan->resolution_date)));
            if ($plan->contract_signing != null) {
              $worksheet->getCell('AI' . $start_row)->setValue(date("m/d/Y", strtotime($plan->contract_signing)));
            }
            if ($plan->proceed_notice != null) {
              $worksheet->getCell('AJ' . $start_row)->setValue(date("m/d/Y", strtotime($plan->proceed_notice)));
            }
            if ($is_same_duration == true || $has_zero_duration == true) {
              $worksheet->mergeCells("AK" . $start_row . ":" . "AK" . ($row - 1));
              $worksheet->getCell('AK' . $row)->setValue($non_zero_duration . "CD");
            }
            if ($detailed_cost != count($clusters)) {
              $worksheet->mergeCells("AQ" . $start_row . ":" . "AQ" . ($row - 1));
              $worksheet->mergeCells("AR" . $start_row . ":" . "AR" . ($row - 1));
              $worksheet->mergeCells("AS" . $start_row . ":" . "AS" . ($row - 1));

              $winner = $APP->getBiddersData($plan->latest_procact_id, 'responsive');
              $worksheet->getCell('AQ' . $start_row)->setValue($winner[0]->minimum_cost);
              if ($plan->account_id == 1) {
                $worksheet->getCell('AO' . $start_row)->setValue($plan->project_cost);
                $worksheet->getCell('AR' . $start_row)->setValue($winner[0]->minimum_cost);
              } else {
                $worksheet->getCell('AP' . $start_row)->setValue($plan->project_cost);
                $worksheet->getCell('AS' . $start_row)->setValue($winner[0]->minimum_cost);
              }
            }
          } else {
            array_push($ids_array, $plan->plan_id);
            $worksheet->getStyle("B" . $row . ":" . "BA" . $row)->applyFromArray($borderedStyleArray);
            $worksheet->insertNewRowBefore($row + 1, 1);
            $worksheet->getCell('B' . $row)->setValue($plan->project_no);
            $worksheet->getCell('C' . $row)->setValue($plan->project_title);
            $worksheet->getCell('W' . $row)->setValue("PLGU-Benguet");
            $worksheet->getCell('X' . $row)->setValue("No");

            if ($plan->mode == "Bidding") {
              $worksheet->getCell('Y' . $row)->setValue("Public " . $plan->mode);
            } else {
              $worksheet->getCell('Y' . $row)->setValue($plan->mode);
            }

            if ($plan->pre_proc_date != null) {
              $worksheet->getCell('Z' . $row)->setValue($plan->pre_proc_date);
            } else {
              $worksheet->getCell('Z' . $row)->setValue("N/A");
            }
            $worksheet->getCell('AA' . $row)->setValue(date("m/d/Y", strtotime($plan->advertisement_start)));
            if ($plan->pre_bid_start != null) {
              $worksheet->getCell('AB' . $row)->setValue(date("m/d/Y", strtotime($plan->pre_bid_start)));
            } else {
              $worksheet->getCell('AB' . $row)->setValue("N/A");
            }
            $worksheet->getCell('AC' . $row)->setValue(date("m/d/Y", strtotime($plan->bid_submission_start)));
            $worksheet->getCell('AD' . $row)->setValue(date("m/d/Y", strtotime($plan->bid_submission_start)));
            $worksheet->getCell('AE' . $row)->setValue(date("m/d/Y", strtotime($plan->bid_evaluation_start)));
            $worksheet->getCell('AF' . $row)->setValue(date("m/d/Y", strtotime($plan->bid_evaluation_start)));
            if ($plan->resolution_date != null) {
              $worksheet->getCell('AG' . $row)->setValue(date("m/d/Y", strtotime($plan->resolution_date)));
            }
            $worksheet->getCell('AH' . $row)->setValue(date("m/d/Y", strtotime($plan->resolution_date)));
            if ($plan->contract_signing != null) {
              $worksheet->getCell('AI' . $row)->setValue(date("m/d/Y", strtotime($plan->contract_signing)));
            }
            if ($plan->proceed_notice != null) {
              $worksheet->getCell('AJ' . $row)->setValue(date("m/d/Y", strtotime($plan->proceed_notice)));
            }

            $worksheet->getCell('AK' . $row)->setValue($plan->duration . " CD");
            $worksheet->getCell('AM' . $row)->setValue($plan->source);
            $worksheet->getCell('AN' . $row)->setValue($plan->project_cost);
            $winner = $APP->getBiddersData($plan->latest_procact_id, 'responsive');
            $worksheet->getCell('AQ' . $row)->setValue($winner[0]->minimum_cost);
            if ($plan->account_id == 1) {
              $worksheet->getCell('AO' . $row)->setValue($plan->project_cost);
              $worksheet->getCell('AR' . $row)->setValue($winner[0]->minimum_cost);
            } else {
              $worksheet->getCell('AP' . $row)->setValue($plan->project_cost);
              $worksheet->getCell('AS' . $row)->setValue($winner[0]->minimum_cost);
            }

            $worksheet->getCell('AT' . $row)->setValue($observer_names);

            if ($prebid_meeting != null) {
              $worksheet->getCell('AU' . $row)->setValue(date("m/d/Y", strtotime($prebid_meeting->date_received)));
            }
            if ($opening_meeting != null) {
              $worksheet->getCell('AV' . $row)->setValue(date("m/d/Y", strtotime($opening_meeting->date_received)));
              $worksheet->getCell('AW' . $row)->setValue(date("m/d/Y", strtotime($opening_meeting->date_received)));
              $worksheet->getCell('AX' . $row)->setValue(date("m/d/Y", strtotime($opening_meeting->date_received)));
            }

            $worksheet->getCell('BA' . $row)->setValue($plan->remarks);
            $row++;
          }
        }
      }
      $worksheet->getStyle('AT10:AT' . ($row - 1))->getAlignment()->setWrapText(true);
      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save(public_path() . '\\' . "excel_templates/PMR-" . $date_start . "-" . $date_end . ".xlsx");
      return  response()->download(public_path() . '\\' . "excel_templates/PMR-" . $date_start . "-" . $date_end . ".xlsx")->deleteFileAfterSend(true);
    }
  }

  public function generateAwardedProjectsForTransmittal()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view("admin.generate_awarded_projects_for_transmittal", ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function submitGenerateAwardedForTransmittal(Request $request)
  {
    $data = $request->validate([
      "date_start" => 'required',
      "date_end" => 'required|after_or_equal:date_start'
    ]);
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $desired_plan_array = [];
    $desired_plan_format = [];
    $date_start = date("Y-m-d", strtotime($request->input('date_start')));
    $date_end = date("Y-m-d", strtotime($request->input('date_end')));
    $ids_array = [];
    $APP = new APP;
    $count = 1;
    $plans = DB::table('project_plans')
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->join('project_bidders', 'project_plans.project_bid_id', 'project_bidders.project_bid')
      ->leftJoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
      ->leftJoin('contracts', 'project_bidders.project_bid', 'contracts.project_bid_id')
      ->leftJoin('notice_to_proceeds', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
      ->leftJoin('chsp', 'project_bidders.project_bid', 'chsp.chsp_project_bid')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->join('resolution_projects', 'procacts.procact_id', 'resolution_projects.procact_id')
      ->join('resolutions', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
      ->select('*', 'resolutions.resolution_date AS noa_date_released', 'procacts.open_bid as bidding_date')
      ->whereRaw('resolutions.resolution_date BETWEEN CAST( "' . $date_start . '" AS DATE) AND CAST( "' . $date_end . '" AS DATE) AND resolutions.type="RRA"')
      ->orderBy('resolutions.resolution_date')
      ->orderBy('procacts.open_bid', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();
    if (count($plans) > 0) {

      foreach ($plans as $plan) {
        if (in_array($plan->plan_id, $ids_array) == false) {
          array_push($ids_array, $plan->plan_id);
          $initial_barangay = $plan->barangay_id;
          $initial_duration = $plan->duration;
          $duration = $initial_duration;
          $same_location = false;
          if ($plan->plan_cluster_id != null) {
            $same_location = true;
            $letter = 'A';
            $total = 0;
            $title = "";
            $source_of_fund = "";
            $project_number = "";
            $project_cost = "";
            $clusters = DB::table('project_plans')
              ->where([['procacts.plan_cluster_id', $plan->plan_cluster_id]])
              ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
              ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
              ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
              ->orderBy('procacts.itb_arrangement', 'asc')
              ->get();
            foreach ($clusters as $cluster) {
              if ($initial_barangay != $cluster->barangay_id || $initial_barangay == null) {
                $same_location = false;
              }
              array_push($ids_array, $cluster->plan_id);
              $temp = $letter . '. ' . $cluster->project_title . ";";
              $temp_source = $letter . '. ' . $cluster->source . ";";
              $temp_project_number = $letter . '. ' . $cluster->project_no . ";";
              $title = $title . "   " . $temp;
              $source_of_fund = $source_of_fund . "   " . $temp_source;
              $project_number = $project_number . "   " . $temp_project_number;
              // $temp2=$letter.'. '.$cluster->project_cost;
              $total = $total + $cluster->project_cost;
              $project_cost = $project_cost . "PHP" . number_format((float)$cluster->project_cost, 2, '.', ',') . " + ";
              $letter = ++$letter;
              if ($cluster->duration != $initial_duration) {
                $duration = $duration + $cluster->duration;
              }
              if ($cluster->special_case_1 == 1) {
                $title = $cluster->project_title;
              }
            }
            $project_cost = rtrim($project_cost, ' + ');
            $project_cost = $project_cost . "= PHP" . number_format((float)$total, 2, '.', ',');
          } else {
            $title = $plan->project_title;
            $project_cost = "PHP" . number_format((float)$plan->project_cost, 2, '.', ',');
            $project_number = $plan->project_no;
            $source_of_fund = $plan->source;
            $duration = $plan->duration;
            $total = $plan->project_cost;
          }

          $winner = $APP->getBiddersData($plan->latest_procact_id, 'responsive');
          $detailed_bids = "";
          $isZero = false;

          if (count($winner) === 0) {
            $procacts = DB::table('procacts')->where('project_plans.plan_id', $plan->plan_id)->join('project_plans', 'project_plans.plan_id', 'procacts.plan_id')->first();
            //   dump($plan->latest_procact_id);
            $winner = $APP->getBiddersData($plan->latest_procact_id, 'responsive');
          }

          if (count($winner) === 0) {
            dump($plan);
          }

          $cluster_bids = $APP->getClusterBids($winner[0]->project_bid);
          $counter = 1;
          $cluster_bids_count = count($cluster_bids);
          $total_minimum_cost = 0;
          foreach ($cluster_bids as $cluster_bid) {
            if ($cluster_bid->minimum_detailed_cost <= 0) {
              $isZero = true;
            } else {
              if ($counter === $cluster_bids_count) {
                $total_minimum_cost = $total_minimum_cost + $cluster_bid->minimum_detailed_cost;
                $detailed_bids = $detailed_bids . "PHP" . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . " = PHP" . number_format((float)$total_minimum_cost, 2, '.', ',');
              } else {
                $total_minimum_cost = $total_minimum_cost + $cluster_bid->minimum_detailed_cost;
                $detailed_bids = $detailed_bids . "PHP" . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . " + ";
              }
            }
            $counter = $counter + 1;
          }


          $temp_plan["project_title"] = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($title))));
          $temp_plan["project_cost"] = $project_cost;
          $temp_plan["total_project_cost"] = $total;
          $temp_plan["project_no"] = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($project_number))));
          $temp_plan["source_of_fund"] = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($source_of_fund))));
          if ($same_location === true) {
            if ($plan->barangay_id != null) {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_name)));
            } else {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->barangay_name . ", " . $plan->municipality_name)));
            }
          } else {
            $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_name)));
          }

          $temp_plan["winning_bidder"] = htmlspecialchars(strtoupper(strtolower($winner[0]->business_name)));
          $temp_plan["name_address"] = htmlspecialchars(strtoupper(strtolower($winner[0]->owner))) . " , " . $winner[0]->address;
          $temp_plan["total_bid"] = $winner[0]->final_minimum_cost;
          if ($isZero === false && count($cluster_bids) > 1) {
            $temp_plan["bid_amount"] = $detailed_bids;
          } else {
            $temp_plan["bid_amount"] = "PHP" . number_format((float)$winner[0]->final_minimum_cost, 2, '.', ',');
          }
          $temp_plan["bidding_date"] = date("m/d/Y", strtotime($plan->bidding_date));
          $temp_plan["group"] = date("F Y", strtotime($plan->noa_date_released));
          $temp_plan["duration"] = $duration;
          if ($plan->date_received_by_contractor != null) {
            $temp_plan["noa_preparation"] = calculateDate($plan->resolution_date, 3, "Working Days");
            $temp_plan["date_received_by_contractor"] = Date('m/d/Y', strtotime($plan->date_received_by_contractor));
            $temp_plan["noa_to_date"] = calculateDateDiff($plan->date_received_by_contractor, Date('Y-m-d', strtotime($request->input('date_end'))));
          } else {
            $temp_plan["noa_preparation"] = null;
            $temp_plan["date_received_by_contractor"] = null;
            $temp_plan["noa_to_date"] = null;
          }
          $pb = "";
          if ($plan->performance_bond_posted) {
            $pb = Date('m/d/Y', strtotime($plan->performance_bond_posted));
          }
          if ($plan->performance_bond_receive_date) {
            $pb = $pb . " " . Date('m/d/Y', strtotime($plan->performance_bond_receive_date));
          }
          $chsp = "";
          if ($plan->chsp_date_issuance) {
            $chsp = Date('m/d/Y', strtotime($plan->chsp_date_issuance));
          }
          if ($plan->chsp_received_date) {
            $chsp = $chsp . " " . Date('m/d/Y', strtotime($plan->chsp_received_date));
          }

          $temp_plan["pb"] = $pb;
          $temp_plan["chsp"] = $chsp;
          $temp_plan["resolution_date"] = Date('m/d/Y', strtotime($plan->resolution_date));
          $temp_plan["award_notice"] = $plan->award_notice;
          $temp_plan["municipality_name"] = $plan->municipality_name;
          $temp_plan["remarks"] = null;
          $temp_plan["count"] = $count;
          $count = $count + 1;
          array_push($desired_plan_array, (object) $temp_plan);
        }
      }

      return back()->withInput()->with("project_plans", (array)$desired_plan_array);
    } else {
      return abort(403, 'No Projects  Were Awarded Selected Dates');
    }
  }

  public function downloadAwardedForTransmittal($date_start, $date_end)
  {
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $date_start = $date_start;
    $date_end = $date_end;
    $ids_array = [];
    $APP = new APP;
    $count = 1;
    $plans = DB::table('project_plans')
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->join('project_bidders', 'project_plans.project_bid_id', 'project_bidders.project_bid')
      ->leftJoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
      ->leftJoin('contracts', 'project_bidders.project_bid', 'contracts.project_bid_id')
      ->leftJoin('notice_to_proceeds', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
      ->leftJoin('chsp', 'project_bidders.project_bid', 'chsp.chsp_project_bid')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->join('resolution_projects', 'procacts.procact_id', 'resolution_projects.procact_id')
      ->join('resolutions', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
      ->select('*', 'resolutions.resolution_date AS noa_date_released', 'procacts.open_bid as bidding_date')
      ->whereRaw('resolutions.resolution_date BETWEEN CAST( "' . $date_start . '" AS DATE) AND CAST( "' . $date_end . '" AS DATE) AND resolutions.type="RRA"')
      ->orderBy('resolutions.resolution_date')
      ->orderBy('procacts.open_bid', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();

    if (count($plans) > 0) {

      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/awarded_for_transmittal.xlsx");
      $worksheet = $spreadsheet->setActiveSheetIndexByName("Sheet1");
      $grouping = null;
      $sub_total_rows = [];
      $row = 6;
      $month_style = [
        'font' => ['bold'  =>  true, 'size'  =>  12, 'name'  =>  'Arial', 'color' => array('rgb' => '000000'),],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFA500']]
      ];
      $sub_total_style = [
        'font' => ['bold'  =>  true, 'size'  =>  11, 'name'  =>  'Arial', 'color' => array('rgb' => '000000'),],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF8000']]
      ];

      $total_style = [
        'font' => ['bold'  =>  true, 'size'  =>  12, 'name'  =>  'Arial', 'color' => array('rgb' => '000000'),],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF8000']]
      ];

      $borderedStyleArray = [
        'borders' => [
          'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
          ]
        ]
      ];

      $right_align = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

      foreach ($plans as $plan) {
        if (in_array($plan->plan_id, $ids_array) == false) {
          array_push($ids_array, $plan->plan_id);
          $initial_barangay = $plan->barangay_id;
          $initial_duration = $plan->duration;
          $duration = $initial_duration;
          $same_location = false;
          if ($plan->plan_cluster_id != null) {
            $same_location = true;
            $letter = 'A';
            $total = 0;
            $title = "";
            $source_of_fund = "";
            $project_number = "";
            $project_cost = "";
            $clusters = DB::table('project_plans')
              ->where([['procacts.plan_cluster_id', $plan->plan_cluster_id]])
              ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
              ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
              ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
              ->orderBy('procacts.itb_arrangement', 'asc')
              ->get();
            foreach ($clusters as $cluster) {
              if ($initial_barangay != $cluster->barangay_id || $initial_barangay == null) {
                $same_location = false;
              }
              array_push($ids_array, $cluster->plan_id);
              $temp = $letter . '. ' . $cluster->project_title . ";";
              $temp_source = $letter . '. ' . $cluster->source . ";";
              $temp_project_number = $letter . '. ' . $cluster->project_no . ";";
              $title = $title . "   " . $temp;
              $source_of_fund = $source_of_fund . "   " . $temp_source;
              $project_number = $project_number . "   " . $temp_project_number;
              // $temp2=$letter.'. '.$cluster->project_cost;
              $total = $total + $cluster->project_cost;
              $project_cost = $project_cost . "PHP" . number_format((float)$cluster->project_cost, 2, '.', ',') . " + ";
              $letter = ++$letter;
              if ($cluster->duration != $initial_duration) {
                $duration = $duration + $cluster->duration;
              }
              if ($cluster->special_case_1 == 1) {
                $title = $cluster->project_title;
              }
            }
            $project_cost = rtrim($project_cost, ' + ');
            $project_cost = $project_cost . "= PHP" . number_format((float)$total, 2, '.', ',');
          } else {
            $title = $plan->project_title;
            $project_cost = "PHP" . number_format((float)$plan->project_cost, 2, '.', ',');
            $project_number = $plan->project_no;
            $source_of_fund = $plan->source;
            $duration = $plan->duration;
            $total = $plan->project_cost;
          }

          $winner = $APP->getBiddersData($plan->latest_procact_id, 'responsive');
          $detailed_bids = "";
          $isZero = false;

          if (count($winner) === 0) {
            $procacts = DB::table('procacts')->where('project_plans.plan_id', $plan->plan_id)->join('project_plans', 'project_plans.plan_id', 'procacts.plan_id')->first();
            $winner = $APP->getBiddersData($plan->latest_procact_id, 'responsive');
          }

          $cluster_bids = $APP->getClusterBids($winner[0]->project_bid);
          $counter = 1;
          $cluster_bids_count = count($cluster_bids);
          $total_minimum_cost = 0;
          foreach ($cluster_bids as $cluster_bid) {
            if ($cluster_bid->minimum_detailed_cost <= 0) {
              $isZero = true;
            } else {
              if ($counter === $cluster_bids_count) {
                $total_minimum_cost = $total_minimum_cost + $cluster_bid->minimum_detailed_cost;
                $detailed_bids = $detailed_bids . "PHP" . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . " = PHP" . number_format((float)$total_minimum_cost, 2, '.', ',');
              } else {
                $total_minimum_cost = $total_minimum_cost + $cluster_bid->minimum_detailed_cost;
                $detailed_bids = $detailed_bids . "PHP" . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . " + ";
              }
            }
            $counter = $counter + 1;
          }

          if ($isZero === false && count($cluster_bids) > 1) {
            $bid_amount = $detailed_bids;
          } else {
            $bid_amount = "PHP" . number_format((float)$winner[0]->final_minimum_cost, 2, '.', ',');
          }

          if ($plan->date_received_by_contractor != null) {
            $noa_preparation = calculateDate($plan->resolution_date, 3, "Working Days");
            $date_received_by_contractor = Date('m/d/Y', strtotime($plan->date_received_by_contractor));
            $noa_to_date = calculateDateDiff($plan->date_received_by_contractor, Date('Y-m-d', strtotime($date_end)));
          } else {
            $noa_preparation = null;
            $date_received_by_contractor = null;
            $noa_to_date = null;
          }

          $pb = "";
          $chsp = "";

          if ($plan->performance_bond_posted) {
            $pb = Date('m/d/Y', strtotime($plan->performance_bond_posted));
          }
          if ($plan->performance_bond_receive_date) {
            $pb = $pb . " " . Date('m/d/Y', strtotime($plan->performance_bond_receive_date));
          }
          if ($plan->chsp_date_issuance) {
            $chsp = Date('m/d/Y', strtotime($plan->chsp_date_issuance));
          }
          if ($plan->chsp_received_date) {
            $chsp = $chsp . " " . Date('m/d/Y', strtotime($plan->chsp_received_date));
          }

          $temp_plan["project_cost"] = $project_cost;
          $temp_plan["total_project_cost"] = $total;
          $temp_plan["source_of_fund"] = htmlspecialchars(strtoupper(strtolower($source_of_fund)));
          $worksheet->setCellValue("A" . $row, $count);
          $worksheet->setCellValue("B" . $row, Date('m/d/Y', strtotime($plan->resolution_date)));
          $worksheet->setCellValue("C" . $row, strtoupper(strtolower($project_number)));
          $worksheet->setCellValue("D" . $row, strtoupper(strtolower($title)));
          $worksheet->setCellValue("E" . $row, strtoupper(strtolower($plan->municipality_name)));
          $worksheet->setCellValue("F" . $row, strtoupper(strtolower($source_of_fund)));
          $worksheet->setCellValue("G" . $row, Date("m/d/Y", strtotime($plan->bidding_date)));
          $worksheet->setCellValue("H" . $row, $bid_amount);
          $worksheet->setCellValue("I" . $row, strtoupper(strtolower($winner[0]->business_name)));
          $worksheet->setCellValue("J" . $row, $noa_preparation);
          $worksheet->setCellValue("K" . $row, $date_received_by_contractor);
          $worksheet->setCellValue("L" . $row, $noa_to_date);
          $worksheet->setCellValue("M" . $row, $pb);
          $worksheet->setCellValue("N" . $row, $chsp);
          $row = $row + 1;
          $count = $count + 1;
        }
      }
      $worksheet->getStyle("A5" . ":" . "O" . ($row - 1))->applyFromArray($borderedStyleArray);
      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save(public_path() . '\\' . "excel_templates/AwardedProjectsSuspendedForTransmittal-" . $date_start . "-" . $date_end . ".xlsx");
      return  response()->download(public_path() . '\\' . "excel_templates/AwardedProjectsSuspendedForTransmittal-" . $date_start . "-" . $date_end . ".xlsx")->deleteFileAfterSend(true);
    } else {
      return abort(403, 'No Projects  Were Awarded Selected Dates');
    }
  }

  public function getSummaryOfBiddingDocuments()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    $user_privilege = ['view'];
    return view("admin.generate_summary_of_bidding_documents", ['links' => $links, 'user_privilege' => $user_privilege]);
  }
  public function submitSummaryOfBiddingDocuments(Request $request)
  {
    $data = $request->validate([
      "date_start" => 'required',
      "date_end" => 'required|after_or_equal:date_start'
    ]);

    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $desired_non_responsive_array = [];
    $desired_non_responsive_format = [];
    $date_start = date("Y-m-d", strtotime($request->input('date_start')));
    $date_end = date("Y-m-d", strtotime($request->input('date_end')));
    $ids_array = [];
    $APP = new APP;
    $count = 1;
    $bidder_status = $request->bidder_status;
    $string_modes = "(1)";

    // Bidder Status
    if ($bidder_status == 0) {
      $bid_doc_bidders = DB::table('bid_doc_projects')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->where('project_bidders.project_bid', null)
        ->where('procact_mode_id', 1)
        ->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 1) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->whereRaw('procact_mode_id="1" AND project_bidders.bid_status in ("disqualified","ineligible") AND notice_of_awards.date_received_by_contractor BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  project_bidders.bid_status in ("disapproved") AND notice_of_awards.date_received_by_contractor BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("lce_evaluation", "lce_evaluation.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 2) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->where('project_bidders.bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("project_bidder_notices", "project_bidders.project_bid", "project_bidder_notices.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 3) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(contractors.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND notice_of_awards.date_received_by_contractor BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        // ->where('project_title','like','%orengao%')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->orderBy('project_bidders.project_bid', 'asc')
        ->get();
    } else if ($bidder_status == 4) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND  project_plans.project_bid_id IS NOT NULL AND notice_of_awards.date_received_by_contractor >" . "'" . $date_end . "'" . "AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND notice_of_awards.date_received_by_contractor > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND notice_of_awards.date_received_by_contractor IS NULL AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND notice_of_awards.date_received_by_contractor  > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND notice_of_awards.date_received_by_contractor IS NULL AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidder_notices.date_received_by_contractor > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.open_bid <= '" . $date_end . "'AND notice_of_awards.date_received_by_contractor > '" . $date_end . "'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->leftJoin('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->leftJoin("project_bidder_notices", "project_bidders.project_bid", "project_bidder_notices.project_bid")
        ->get();
    } else if ($bidder_status == 5) {

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(contractors.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND notice_of_awards.date_received_by_contractor BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 6) {

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='withdrawn' AND project_bidders.withdrawal_receive_date BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 7) {

      $bid_doc_bidders1 = DB::table('bid_doc_projects')->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->where('project_bidders.project_bid', null)
        ->where('procact_mode_id', 1)
        ->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $bid_doc_bidders2 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->whereRaw('procact_mode_id="1" AND project_bidders.bid_status in ("disqualified","ineligible") AND notice_of_awards.date_received_by_contractor BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  project_bidders.bid_status in ("disapproved") AND notice_of_awards.date_received_by_contractor BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("lce_evaluation", "lce_evaluation.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders3 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->where('project_bidders.bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("project_bidder_notices", "project_bidders.project_bid", "project_bidder_notices.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders4 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(contractors.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND notice_of_awards.date_received_by_contractor BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        // ->where('project_title','like','%orengao%')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->orderBy('project_bidders.project_bid', 'asc')
        ->get();

      $bid_doc_bidders5 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND  project_plans.project_bid_id IS NOT NULL AND notice_of_awards.date_received_by_contractor >" . "'" . $date_end . "'" . "AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND notice_of_awards.date_received_by_contractor > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND notice_of_awards.date_received_by_contractor IS NULL AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND notice_of_awards.date_received_by_contractor  > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND notice_of_awards.date_received_by_contractor IS NULL AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidder_notices.date_received_by_contractor > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.open_bid <= '" . $date_end . "'AND notice_of_awards.date_received_by_contractor > '" . $date_end . "'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->leftJoin('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->leftJoin("project_bidder_notices", "project_bidders.project_bid", "project_bidder_notices.project_bid")
        ->get();


      $bid_doc_bidders6 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(contractors.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND notice_of_awards.date_received_by_contractor BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders7 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='withdrawn' AND project_bidders.withdrawal_receive_date BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else {
      return abort(403, 'Uknown Bidder Status!');
    }

    if ($bidder_status == 7) {
      $custom_bidders = [];
      if (count($bid_doc_bidders1) > 0) {
        $custom_bidders = (array)json_decode($bid_doc_bidders1);
      }
      if (count($bid_doc_bidders2) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders2));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders2);
        }
      }
      if (count($bid_doc_bidders3) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders3));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders3);
        }
      }
      if (count($bid_doc_bidders4) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders4));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders4);
        }
      }
      if (count($bid_doc_bidders5) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders5));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders5);
        }
      }
      if (count($bid_doc_bidders6) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders6));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders6);
        }
      }
      if (count($bid_doc_bidders7) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders7));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders7);
        }
      }
    } else {
      $custom_bidders = (array)json_decode($bid_doc_bidders);
    }
    if (count($custom_bidders) > 0) {
      $custom_bidders = $APP->sortObject($custom_bidders, array('open_bid' => 'asc', 'itb_arrangement' => 'asc', 'post_qual_start' => 'asc', 'post_qual_end' => 'asc'));
      foreach ($custom_bidders as $not_responsive_bidder) {
        if (in_array($not_responsive_bidder->init_id, $ids_array) === false) {

          array_push($ids_array, $not_responsive_bidder->init_id);
          $initial_barangay = $not_responsive_bidder->barangay_id;
          $initial_duration = $not_responsive_bidder->duration;
          $duration = $initial_duration;
          $same_location = false;
          if ($not_responsive_bidder->plan_cluster_id != null) {
            $same_location = true;
            $letter = 'A';
            $total = 0;
            $title = "";
            $source_of_fund = "";
            $project_number = "";
            $project_cost = "";
            $clusters = DB::table('procacts')
              ->where([['procacts.plan_cluster_id', $not_responsive_bidder->plan_cluster_id]])
              ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
              ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
              ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
              ->orderBy('procacts.itb_arrangement', 'asc')
              ->get();
            foreach ($clusters as $cluster) {
              $temp_not_responsive_bidder = [];
              if ($initial_barangay != $cluster->barangay_id || $initial_barangay == null) {
                $same_location = false;
              }
              $temp = $letter . '. ' . $cluster->project_title . ";";
              $temp_source = $letter . '. ' . $cluster->source . ";";
              $temp_project_number = $letter . '. ' . $cluster->project_no . ";";
              $title = $title . "   " . $temp;
              $source_of_fund = $source_of_fund . "   " . $temp_source;
              $project_number = $project_number . "   " . $temp_project_number;
              // $temp2=$letter.'. '.$cluster->project_cost;
              $total = $total + $cluster->project_cost;
              $project_cost = $project_cost . "PHP" . number_format((float)$cluster->project_cost, 2, '.', ',') . " + ";
              $letter = ++$letter;
              if ($cluster->duration != $initial_duration) {
                $duration = $duration + $cluster->duration;
              }
              if ($cluster->special_case_1 == 1) {
                $title = $cluster->project_title;
              }
            }
            $project_cost = rtrim($project_cost, ' + ');
            $project_cost = $project_cost . "= PHP" . number_format((float)$total, 2, '.', ',');
          } else {
            $title = $not_responsive_bidder->project_title;
            $project_cost = "PHP" . number_format((float)$not_responsive_bidder->project_cost, 2, '.', ',');
            $project_number = $not_responsive_bidder->project_no;
            $source_of_fund = $not_responsive_bidder->source;
            $duration = $not_responsive_bidder->duration;
            $total = $not_responsive_bidder->project_cost;
          }

          if ($not_responsive_bidder->bidders_bid != null) {
            $detailed_bids = "";
            $isZero = false;
            $cluster_bids = $APP->getClusterBids($not_responsive_bidder->bidders_bid);
            $counter = 1;
            $cluster_bids_count = count($cluster_bids);
            $total_minimum_cost = 0;
            foreach ($cluster_bids as $cluster_bid) {
              array_push($ids_array, $cluster_bid->init_id);
              if ($not_responsive_bidder->minimum_detailed_cost <= 0) {
                $isZero = true;
              } else {
                if ($counter === $cluster_bids_count) {
                  $total_minimum_cost = $total_minimum_cost + $cluster_bid->minimum_detailed_cost;
                  $detailed_bids = $detailed_bids . "PHP" . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . " = PHP" . number_format((float)$total_minimum_cost, 2, '.', ',');
                } else {
                  $total_minimum_cost = $total_minimum_cost + $cluster_bid->minimum_detailed_cost;
                  $detailed_bids = $detailed_bids . "PHP" . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . " + ";
                }
              }
              $counter = $counter + 1;
            }
          } else {
            $total_minimum_cost = "N/A";
            $detailed_bids = "N/A";
            $isZero = true;
            $cluster_bids = [];
          }
          $temp_not_responsive_bidder["project_no"] = htmlspecialchars(strtoupper(strtolower($project_number)));
          $temp_not_responsive_bidder["project_title"] = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($title))));
          $temp_not_responsive_bidder["project_cost"] = $project_cost;
          $temp_not_responsive_bidder["total_project_cost"] = $total;
          $temp_not_responsive_bidder["source_of_fund"] = htmlspecialchars(strtoupper(strtolower($source_of_fund)));
          if ($same_location === true) {
            if ($not_responsive_bidder->barangay_id != null) {
              $temp_not_responsive_bidder["location"] = htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->municipality_name)));
            } else {
              $temp_not_responsive_bidder["location"] = htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->barangay_name . ", " . $not_responsive_bidder->municipality_name)));
            }
          } else {
            $temp_not_responsive_bidder["location"] = htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->municipality_name)));
          }

          $temp_not_responsive_bidder["bidder"] = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->business_name))));
          $temp_not_responsive_bidder["name_address"] = htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->owner))) . " , " . htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->address)));
          $temp_not_responsive_bidder["total_bid"] = $not_responsive_bidder->final_minimum_cost;
          if ($isZero === false && count($cluster_bids) > 1) {
            $temp_not_responsive_bidder["bid_amount"] = $detailed_bids;
          } else if ($not_responsive_bidder->bidders_bid == null) {
            $temp_not_responsive_bidder["bid_amount"] = "N/A";
          } else if ($not_responsive_bidder->final_minimum_cost != null) {
            $temp_not_responsive_bidder["bid_amount"] = "PHP" . number_format((float)$not_responsive_bidder->final_minimum_cost, 2, '.', ',');
          } else {
            $temp_not_responsive_bidder["bid_amount"] = "PHP" . number_format((float)$not_responsive_bidder->minimum_cost, 2, '.', ',');
          }
          $temp_not_responsive_bidder["bidding_date"] = date("F d,Y", strtotime($not_responsive_bidder->open_bid));

          if ($not_responsive_bidder->post_qual_start != null) {
            $temp_not_responsive_bidder["post_qual_start"] = date("F d, Y", strtotime($not_responsive_bidder->post_qual_start));
          } else {
            $temp_not_responsive_bidder["post_qual_start"] = "N/A";
          }
          if ($not_responsive_bidder->post_qual_end != null) {
            $temp_not_responsive_bidder["post_qual_end"] = date("F d, Y", strtotime($not_responsive_bidder->post_qual_end));
          } else {
            $temp_not_responsive_bidder["post_qual_end"] = "N/A";
          }
          $temp_not_responsive_bidder["award_date"] = date("F d, Y", strtotime($not_responsive_bidder->award_notice));
          $temp_not_responsive_bidder["duration"] = $duration;
          $temp_not_responsive_bidder["count"] = $count;
          $temp_not_responsive_bidder["group"] = date("F d, Y", strtotime($not_responsive_bidder->open_bid));
          $temp_not_responsive_bidder["mode"] = $not_responsive_bidder->mode;




          if ($not_responsive_bidder->bid_status == null) {
            $temp_not_responsive_bidder["bid_status"] = "Did Not Submit";
          } else if ($not_responsive_bidder->bid_status == "active" && ($not_responsive_bidder->award_notice != null || $not_responsive_bidder->post_qual != null)) {
            $temp_not_responsive_bidder["bid_status"] = "Loosing Bid";
          } else {
            $temp_not_responsive_bidder["bid_status"] = $not_responsive_bidder->bid_status;
          }


          // remarks

          if ($not_responsive_bidder->bid_status === "Loosing Bid" || $not_responsive_bidder->bid_status === "ongoing") {
            $rank = getRank($not_responsive_bidder->procact_id, $not_responsive_bidder->bidders_bid);
            $temp_not_responsive_bidder["remarks"] = $rank;
          } else if ($not_responsive_bidder->bid_status === "disqualified" || $not_responsive_bidder->bid_status === "ineligible" || $not_responsive_bidder->bid_status === "disapproved") {
            $disqualification_records = DB::table('disqualification_records')->where('project_bid', $not_responsive_bidder->bidders_bid)->orderBy('record_id', 'desc')->first();
            $temp_not_responsive_bidder["remarks"] = $disqualification_records->remarks;
          } else if ($not_responsive_bidder->bid_status == null) {
            $temp_not_responsive_bidder["remarks"] = "Did Not Submit Bidding Document";
          } else if ($not_responsive_bidder->bid_status === "non-responsive") {
            $temp_not_responsive_bidder["remarks"] = "Non-responsive";
            $noa = DB::table("notice_of_awards")->where('project_bid_id', $not_responsive_bidder->project_bid_id)->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])->first();
            if ($noa === null) {
              $temp_not_responsive_bidder["bid_status"] = "ongoing";
            }
          } else if ($not_responsive_bidder->bid_status === "active" && ($not_responsive_bidder->award_notice != null || $not_responsive_bidder->post_qual != null)) {
            $rank = getRank($not_responsive_bidder->procact_id, $not_responsive_bidder->project_bid);
            $temp_not_responsive_bidder["remarks"] = $rank;
          } else if ($not_responsive_bidder->bid_status === "responsive") {
            $noa = DB::table("notice_of_awards")->where('project_bid_id', $not_responsive_bidder->project_bid_id)->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])->first();
            if ($noa != null) {
              if ($noa->date_received_by_contractor != null) {
                $temp_not_responsive_bidder["remarks"] = "NOA:" . Date('m/d/Y', strtotime($noa->date_generated)) . " Received:" . Date('m/d/Y', strtotime($noa->date_received_by_contractor));
              } else if ($noa->date_generated != null) {
                $temp_not_responsive_bidder["remarks"] = "NOA:" . Date('m/d/Y', strtotime($noa->date_generated));
              } else {
                $rank = getRank($not_responsive_bidder->procact_id, $not_responsive_bidder->project_bid);
                $temp_not_responsive_bidder["remarks"] = $rank;
              }
            } else {
              $temp_not_responsive_bidder["remarks"] = "For Notice of Award";
              $temp_not_responsive_bidder["bid_status"] = "ongoing";
            }
          } else {
            $temp_not_responsive_bidder["remarks"] = $not_responsive_bidder->twg_evaluation_remarks;
          }

          if ($not_responsive_bidder->procact_mode_id === 1) {
            $temp_not_responsive_bidder["fees"] = "PHP" . number_format((float)$not_responsive_bidder->fees, 2, '.', ',');
            $temp_not_responsive_bidder["total_fees"] = (float)$not_responsive_bidder->fees;
          } else {
            $temp_not_responsive_bidder["fees"] = "N/A";
            $temp_not_responsive_bidder["total_fees"] = "0.00";
          }
          $temp_not_responsive_bidder["control_number"] = $not_responsive_bidder->control_number;
          $temp_not_responsive_bidder["bid_doc_release_date"] = Date("F d, Y", strtotime($not_responsive_bidder->bid_doc_release_date));
          $temp_not_responsive_bidder["winning_bidder"] = $not_responsive_bidder->winning_bidder;

          $count = $count + 1;
          array_push($desired_non_responsive_array, (object) $temp_not_responsive_bidder);
        }
      }
      return back()->withInput()->with("project_bidders", (array)$desired_non_responsive_array);
    } else {
      return abort(403, 'No Specific Bidders data found on the selected dates');
    }
  }


  public function downloadSummaryOfBiddingDocuments($date_start, $date_end, $bidder_status)
  {

    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $desired_non_responsive_format = [];
    $date_start = date("Y-m-d", strtotime($date_start));
    $date_end = date("Y-m-d", strtotime($date_end));
    $ids_array = [];
    $APP = new APP;
    $count = 1;
    $bidder_status = $bidder_status;
    $string_modes = "(1)";

    // Bidder Status
    if ($bidder_status == 0) {
      $bid_doc_bidders = DB::table('bid_doc_projects')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->where('project_bidders.project_bid', null)
        ->where('procact_mode_id', 1)
        ->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 1) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->whereRaw('procact_mode_id="1" AND project_bidders.bid_status in ("disqualified","ineligible") AND notice_of_awards.date_received_by_contractor BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  project_bidders.bid_status in ("disapproved") AND notice_of_awards.date_received_by_contractor BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("lce_evaluation", "lce_evaluation.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 2) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->where('project_bidders.bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("project_bidder_notices", "project_bidders.project_bid", "project_bidder_notices.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 3) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(contractors.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND notice_of_awards.date_received_by_contractor BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        // ->where('project_title','like','%orengao%')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->orderBy('project_bidders.project_bid', 'asc')
        ->get();
    } else if ($bidder_status == 4) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND  project_plans.project_bid_id IS NOT NULL AND notice_of_awards.date_received_by_contractor >" . "'" . $date_end . "'" . "AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND notice_of_awards.date_received_by_contractor > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND notice_of_awards.date_received_by_contractor IS NULL AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND notice_of_awards.date_received_by_contractor  > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND notice_of_awards.date_received_by_contractor IS NULL AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidder_notices.date_received_by_contractor > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.open_bid <= '" . $date_end . "'AND notice_of_awards.date_received_by_contractor > '" . $date_end . "'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->leftJoin('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->leftJoin("project_bidder_notices", "project_bidders.project_bid", "project_bidder_notices.project_bid")
        ->get();
    } else if ($bidder_status == 5) {

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(contractors.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND notice_of_awards.date_received_by_contractor BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 6) {

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='withdrawn' AND project_bidders.withdrawal_receive_date BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 7) {

      $bid_doc_bidders1 = DB::table('bid_doc_projects')->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->where('project_bidders.project_bid', null)
        ->where('procact_mode_id', 1)
        ->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $bid_doc_bidders2 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->whereRaw('procact_mode_id="1" AND project_bidders.bid_status in ("disqualified","ineligible") AND notice_of_awards.date_received_by_contractor BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  project_bidders.bid_status in ("disapproved") AND notice_of_awards.date_received_by_contractor BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("lce_evaluation", "lce_evaluation.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders3 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->where('project_bidders.bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("project_bidder_notices", "project_bidders.project_bid", "project_bidder_notices.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders4 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(contractors.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND notice_of_awards.date_received_by_contractor BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        // ->where('project_title','like','%orengao%')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->orderBy('project_bidders.project_bid', 'asc')
        ->get();

      $bid_doc_bidders5 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(winning_contractor.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'), 'project_bidders.*')
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND  project_plans.project_bid_id IS NOT NULL AND notice_of_awards.date_received_by_contractor >" . "'" . $date_end . "'" . "AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND notice_of_awards.date_received_by_contractor > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND notice_of_awards.date_received_by_contractor IS NULL AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND notice_of_awards.date_received_by_contractor  > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND notice_of_awards.date_received_by_contractor IS NULL AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidder_notices.date_received_by_contractor > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.open_bid <= '" . $date_end . "'AND notice_of_awards.date_received_by_contractor > '" . $date_end . "'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->leftJoin('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->leftJoin("project_bidder_notices", "project_bidders.project_bid", "project_bidder_notices.project_bid")
        ->get();


      $bid_doc_bidders6 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::RAW('CONCAT(UPPER(contractors.business_name)," - ",notice_of_awards.date_received_by_contractor) as winning_bidder'))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND notice_of_awards.date_received_by_contractor BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->leftjoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_plans.project_bid_id')
        ->leftJoin('project_bidders as winning_bidder', 'winning_bidder.project_bid', 'project_plans.project_bid_id')
        ->leftJoin('bid_doc_projects as winning_bid_doc_projects', 'winning_bid_doc_projects.bid_doc_project_id', 'winning_bidder.bid_doc_project_id')
        ->leftJoin('bid_docs as winning_bid_doc', 'winning_bid_doc_projects.bid_doc_id', 'winning_bid_doc.bid_doc_id')
        ->join('contractors as winning_contractor', 'winning_contractor.contractor_id', 'winning_bid_doc.contractor_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders7 = DB::table('project_bidders')
        ->select('*', 'bid_docs.date_released as bid_doc_release_date', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='withdrawn' AND project_bidders.withdrawal_receive_date BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else {
      return abort(403, 'Uknown Bidder Status!');
    }

    if ($bidder_status == 7) {
      $custom_bidders = [];
      if (count($bid_doc_bidders1) > 0) {
        $custom_bidders = (array)json_decode($bid_doc_bidders1);
      }
      if (count($bid_doc_bidders2) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders2));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders2);
        }
      }
      if (count($bid_doc_bidders3) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders3));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders3);
        }
      }
      if (count($bid_doc_bidders4) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders4));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders4);
        }
      }
      if (count($bid_doc_bidders5) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders5));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders5);
        }
      }
      if (count($bid_doc_bidders6) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders6));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders6);
        }
      }
    } else {
      $custom_bidders = (array)json_decode($bid_doc_bidders);
    }

    if (count($custom_bidders) > 0) {
      $custom_bidders = $APP->sortObject($custom_bidders, array('open_bid' => 'asc', 'itb_arrangement' => 'asc', 'post_qual_start' => 'asc', 'post_qual_end' => 'asc'));
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/Summary of Bidding Documents.xlsx");
      $worksheet = $spreadsheet->setActiveSheetIndexByName("Sheet1");
      $processed_array = [];
      $row = 10;
      $cmp_date = "";
      $cmp_date_row = 10;
      $cmp_count = "";
      $cmp_count_row = 10;
      foreach ($custom_bidders as $key => $not_responsive_bidder) {
        if (in_array($not_responsive_bidder->init_id, $ids_array) === false) {
          array_push($ids_array, $not_responsive_bidder->init_id);
          $initial_barangay = $not_responsive_bidder->barangay_id;
          $initial_duration = $not_responsive_bidder->duration;
          $duration = $initial_duration;
          $same_location = false;
          if ($not_responsive_bidder->plan_cluster_id != null) {
            $same_location = true;
            $letter = 'A';
            $total = 0;
            $title = "";
            $source_of_fund = "";
            $project_number = "";
            $project_cost = "";
            $clusters = DB::table('procacts')
              ->where([['procacts.plan_cluster_id', $not_responsive_bidder->plan_cluster_id]])
              ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
              ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
              ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
              ->orderBy('procacts.itb_arrangement', 'asc')
              ->get();
            foreach ($clusters as $cluster) {
              $temp_not_responsive_bidder = [];
              if ($initial_barangay != $cluster->barangay_id || $initial_barangay == null) {
                $same_location = false;
              }
              $temp = $letter . '. ' . $cluster->project_title . ";";
              $temp_source = $letter . '. ' . $cluster->source . ";";
              $temp_project_number = $letter . '. ' . $cluster->project_no . ";";
              $title = $title . "   " . $temp;
              $source_of_fund = $source_of_fund . "   " . $temp_source;
              $project_number = $project_number . "   " . $temp_project_number;
              // $temp2=$letter.'. '.$cluster->project_cost;
              $total = $total + $cluster->project_cost;
              $project_cost = $project_cost . "PHP" . number_format((float)$cluster->project_cost, 2, '.', ',') . " + ";
              $letter = ++$letter;
              if ($cluster->duration != $initial_duration) {
                $duration = $duration + $cluster->duration;
              }
              if ($cluster->special_case_1 == 1) {
                $title = $cluster->project_title;
              }
            }
            $project_cost = rtrim($project_cost, ' + ');
            $project_cost = $project_cost . "= PHP" . number_format((float)$total, 2, '.', ',');
          } else {
            $title = $not_responsive_bidder->project_title;
            $project_cost = "PHP" . number_format((float)$not_responsive_bidder->project_cost, 2, '.', ',');
            $project_number = $not_responsive_bidder->project_no;
            $source_of_fund = $not_responsive_bidder->source;
            $duration = $not_responsive_bidder->duration;
            $total = $not_responsive_bidder->project_cost;
          }

          if ($not_responsive_bidder->bidders_bid != null) {
            $detailed_bids = "";
            $isZero = false;
            $cluster_bids = $APP->getClusterBids($not_responsive_bidder->bidders_bid);
            $counter = 1;
            $cluster_bids_count = count($cluster_bids);
            $total_minimum_cost = 0;
            foreach ($cluster_bids as $cluster_bid) {
              array_push($ids_array, $cluster_bid->init_id);
              if ($not_responsive_bidder->minimum_detailed_cost <= 0) {
                $isZero = true;
              } else {
                if ($counter === $cluster_bids_count) {
                  $total_minimum_cost = $total_minimum_cost + $cluster_bid->minimum_detailed_cost;
                  $detailed_bids = $detailed_bids . "PHP" . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . " = PHP" . number_format((float)$total_minimum_cost, 2, '.', ',');
                } else {
                  $total_minimum_cost = $total_minimum_cost + $cluster_bid->minimum_detailed_cost;
                  $detailed_bids = $detailed_bids . "PHP" . number_format((float)$cluster_bid->minimum_detailed_cost, 2, '.', ',') . " + ";
                }
              }
              $counter = $counter + 1;
            }
          } else {
            $total_minimum_cost = "N/A";
            $detailed_bids = "N/A";
            $isZero = true;
            $cluster_bids = [];
          }
          $temp_not_responsive_bidder["project_no"] = strtoupper(strtolower($project_number));
          $temp_not_responsive_bidder["project_title"] = strtoupper(strtolower($title));
          $temp_not_responsive_bidder["project_cost"] = $project_cost;
          $temp_not_responsive_bidder["total_project_cost"] = $total;
          $temp_not_responsive_bidder["source_of_fund"] = strtoupper(strtolower($source_of_fund));
          if ($same_location === true) {
            if ($not_responsive_bidder->barangay_id != null) {
              $temp_not_responsive_bidder["location"] = strtoupper(strtolower($not_responsive_bidder->municipality_name));
            } else {
              $temp_not_responsive_bidder["location"] = strtoupper(strtolower($not_responsive_bidder->barangay_name . ", " . $not_responsive_bidder->municipality_name));
            }
          } else {
            $temp_not_responsive_bidder["location"] = strtoupper(strtolower($not_responsive_bidder->municipality_name));
          }

          $temp_not_responsive_bidder["bidder"] = strtoupper(strtolower($not_responsive_bidder->business_name));
          $temp_not_responsive_bidder["name_address"] = strtoupper(strtolower($not_responsive_bidder->owner)) . " , " . strtoupper(strtolower($not_responsive_bidder->address));
          $temp_not_responsive_bidder["total_bid"] = $not_responsive_bidder->final_minimum_cost;
          if ($isZero === false && count($cluster_bids) > 1) {
            $temp_not_responsive_bidder["bid_amount"] = $detailed_bids;
          } else if ($not_responsive_bidder->bidders_bid == null) {
            $temp_not_responsive_bidder["bid_amount"] = "N/A";
          } else if ($not_responsive_bidder->final_minimum_cost != null) {
            $temp_not_responsive_bidder["bid_amount"] = "PHP" . number_format((float)$not_responsive_bidder->final_minimum_cost, 2, '.', ',');
          } else {
            $temp_not_responsive_bidder["bid_amount"] = "PHP" . number_format((float)$not_responsive_bidder->minimum_cost, 2, '.', ',');
          }
          $temp_not_responsive_bidder["bidding_date"] = date("F d,Y", strtotime($not_responsive_bidder->open_bid));

          if ($not_responsive_bidder->post_qual_start != null) {
            $temp_not_responsive_bidder["post_qual_start"] = date("F d, Y", strtotime($not_responsive_bidder->post_qual_start));
          } else {
            $temp_not_responsive_bidder["post_qual_start"] = "N/A";
          }
          if ($not_responsive_bidder->post_qual_end != null) {
            $temp_not_responsive_bidder["post_qual_end"] = date("F d, Y", strtotime($not_responsive_bidder->post_qual_end));
          } else {
            $temp_not_responsive_bidder["post_qual_end"] = "N/A";
          }
          $temp_not_responsive_bidder["award_date"] = date("F d, Y", strtotime($not_responsive_bidder->award_notice));
          $temp_not_responsive_bidder["duration"] = $duration;
          $temp_not_responsive_bidder["group"] = date("F d, Y", strtotime($not_responsive_bidder->open_bid));
          $temp_not_responsive_bidder["mode"] = $not_responsive_bidder->mode;
          if ($not_responsive_bidder->bid_status == null) {
            $temp_not_responsive_bidder["bid_status"] = "Did Not Submit";
          } else if ($not_responsive_bidder->bid_status == "active" && ($not_responsive_bidder->award_notice != null || $not_responsive_bidder->post_qual != null)) {
            $temp_not_responsive_bidder["bid_status"] = "Loosing Bid";
          } else {
            $temp_not_responsive_bidder["bid_status"] = $not_responsive_bidder->bid_status;
          }
          $temp_not_responsive_bidder["control_number"] = $not_responsive_bidder->control_number;
          $temp_not_responsive_bidder["bid_doc_release_date"] = Date("F d, Y", strtotime($not_responsive_bidder->bid_doc_release_date));

          // remarks

          if ($not_responsive_bidder->bid_status === "Loosing Bid" || $not_responsive_bidder->bid_status === "ongoing") {
            $rank = getRank($not_responsive_bidder->procact_id, $not_responsive_bidder->bidders_bid);
            $temp_not_responsive_bidder["remarks"] = $rank;
          } else if ($not_responsive_bidder->bid_status === "disqualified" || $not_responsive_bidder->bid_status === "ineligible" || $not_responsive_bidder->bid_status === "disapproved") {
            $disqualification_records = DB::table('disqualification_records')->where('project_bid', $not_responsive_bidder->bidders_bid)->orderBy('record_id', 'desc')->first();
            $temp_not_responsive_bidder["remarks"] = $disqualification_records->remarks;
          } else if ($not_responsive_bidder->bid_status == null) {
            $temp_not_responsive_bidder["remarks"] = "Did Not Submit Bidding Document";
          } else if ($not_responsive_bidder->bid_status === "non-responsive") {
            $temp_not_responsive_bidder["remarks"] = "Non-responsive";
            $noa = DB::table("notice_of_awards")->where('project_bid_id', $not_responsive_bidder->project_bid_id)->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])->first();
            if ($noa === null) {
              $temp_not_responsive_bidder["bid_status"] = "ongoing";
            }
          } else if ($not_responsive_bidder->bid_status === "active" && ($not_responsive_bidder->award_notice != null || $not_responsive_bidder->post_qual != null)) {
            $rank = getRank($not_responsive_bidder->procact_id, $not_responsive_bidder->project_bid);
            $temp_not_responsive_bidder["remarks"] = $rank;
          } else if ($not_responsive_bidder->bid_status === "responsive") {
            $noa = DB::table("notice_of_awards")->where('project_bid_id', $not_responsive_bidder->project_bid_id)->whereBetween('notice_of_awards.date_received_by_contractor', [$date_start, $date_end])->first();
            if ($noa != null) {
              if ($noa->date_received_by_contractor != null) {
                $temp_not_responsive_bidder["remarks"] = "NOA:" . Date('m/d/Y', strtotime($noa->date_generated)) . " Received:" . Date('m/d/Y', strtotime($noa->date_received_by_contractor));
              } else if ($noa->date_generated != null) {
                $temp_not_responsive_bidder["remarks"] = "NOA:" . Date('m/d/Y', strtotime($noa->date_generated));
              } else {
                $rank = getRank($not_responsive_bidder->procact_id, $not_responsive_bidder->project_bid);
                $temp_not_responsive_bidder["remarks"] = $rank;
              }
            } else {
              $temp_not_responsive_bidder["remarks"] = "For Notice of Award";
              $temp_not_responsive_bidder["bid_status"] = "ongoing";
            }
          } else {
            $temp_not_responsive_bidder["remarks"] = $not_responsive_bidder->twg_evaluation_remarks;
          }

          if ($not_responsive_bidder->procact_mode_id === 1) {
            $temp_not_responsive_bidder["fees"] = "PHP" . number_format((float)$not_responsive_bidder->fees, 2, '.', ',');
            $temp_not_responsive_bidder["total_fees"] = (float)$not_responsive_bidder->fees;
          } else {
            $temp_not_responsive_bidder["fees"] = "N/A";
            $temp_not_responsive_bidder["total_fees"] = "0.00";
          }
          $temp_not_responsive_bidder["control_number"] = $not_responsive_bidder->control_number;
          $temp_not_responsive_bidder["bid_doc_release_date"] = Date("F d, Y", strtotime($not_responsive_bidder->bid_doc_release_date));
          $temp_not_responsive_bidder["winning_bidder"] = $not_responsive_bidder->winning_bidder;

          if ($count == 1) {
            // dump($count);
            $temp_not_responsive_bidder["count"] = $count;
            array_push($processed_array, $temp_not_responsive_bidder);
            $count = $count + 1;
          } else {
            $last_instance = $processed_array[(count($processed_array) - 1)];
            if ($last_instance["project_title"] === $temp_not_responsive_bidder["project_title"]) {
              // dump($count);
              $count = $last_instance["count"];
              $temp_not_responsive_bidder["count"] = $count;
            } else {
              // dump($count);
              $temp_not_responsive_bidder["count"] = $count;
              array_push($processed_array, $temp_not_responsive_bidder);
            }
            $count = $count + 1;
          }



          if ($temp_not_responsive_bidder["remarks"] == null) {
            // dd($not_responsive_bidder);
          }


          // Insert data to excel

          // Merging Initial Columns
          if ($temp_not_responsive_bidder["count"] != 1) {
            // Column A
            if ($cmp_date != $temp_not_responsive_bidder["bidding_date"]) {

              $last_instance = $processed_array[(count($processed_array) - 2)];
              if ($cmp_date_row != ($row - 1)) {
                $worksheet->mergeCells("A" . $cmp_date_row . ":" . "A" . ($row - 1));
              }
              $worksheet->setCellValue("A" . $cmp_date_row, $last_instance["bidding_date"]);
              $cmp_date = $temp_not_responsive_bidder["bidding_date"];
              $cmp_date_row = $row;
            }
            // Column B C D
            if ($cmp_count != $temp_not_responsive_bidder["count"]) {
              if ($cmp_count_row != ($row - 1)) {
                $worksheet->mergeCells("B" . $cmp_count_row . ":" . "B" . ($row - 1));
                $worksheet->mergeCells("C" . $cmp_count_row . ":" . "C" . ($row - 1));
                $worksheet->mergeCells("D" . $cmp_count_row . ":" . "D" . ($row - 1));
                $worksheet->mergeCells("K" . $cmp_count_row . ":" . "K" . ($row - 1));
              }
              $worksheet->setCellValue("B" . $cmp_count_row, $last_instance["count"]);
              $worksheet->setCellValue("C" . $cmp_count_row, $last_instance["project_title"]);
              $worksheet->setCellValue("D" . $cmp_count_row, $last_instance["project_cost"]);
              $worksheet->setCellValue("K" . $cmp_count_row, $last_instance["project_no"]);
              $cmp_count = $temp_not_responsive_bidder["count"];
              $cmp_count_row = $row;
            }
          } else {
            // Initial Column Values
            $cmp_date = $temp_not_responsive_bidder["bidding_date"];
            $cmp_date_row = $row;
            $cmp_count = $temp_not_responsive_bidder["count"];
            $cmp_count_row = 10;
          }

          $worksheet->setCellValue("E" . $row, $temp_not_responsive_bidder["bidder"]);
          if ($temp_not_responsive_bidder["bid_status"] === "responsive") {
            $worksheet->setCellValue("F" . $row, $temp_not_responsive_bidder["total_fees"]);
          } else if ($temp_not_responsive_bidder["bid_status"] == "Did Not Submit" || $temp_not_responsive_bidder["bid_status"] == "disqualified" || $temp_not_responsive_bidder["bid_status"] == "non-responsive" || $temp_not_responsive_bidder["bid_status"] == "Loosing Bid" || $temp_not_responsive_bidder["bid_status"] == "disapproved") {
            $worksheet->setCellValue("G" . $row, $temp_not_responsive_bidder["total_fees"]);
          } else {
            $worksheet->setCellValue("H" . $row, $temp_not_responsive_bidder["total_fees"]);
          }

          $worksheet->setCellValue("I" . $row, $temp_not_responsive_bidder["remarks"]);
          $worksheet->setCellValue("J" . $row, $temp_not_responsive_bidder["control_number"] . " - " . $temp_not_responsive_bidder["bid_doc_release_date"]);
          $row = $row + 1;
        }
      }

      $borderedStyleArray = [
        'borders' => [
          'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
          ]
        ]
      ];

      // Merge Column A

      $last_instance = $processed_array[(count($processed_array) - 1)];
      if ($cmp_date_row != ($row - 1)) {
        $worksheet->mergeCells("A" . $cmp_date_row . ":" . "A" . ($row - 1));
      }
      $worksheet->setCellValue("A" . $cmp_date_row, $last_instance["bidding_date"]);

      // Merge Column B C D
      if ($cmp_count_row != ($row - 1)) {
        $worksheet->mergeCells("B" . $cmp_count_row . ":" . "B" . ($row - 1));
        $worksheet->mergeCells("C" . $cmp_count_row . ":" . "C" . ($row - 1));
        $worksheet->mergeCells("D" . $cmp_count_row . ":" . "D" . ($row - 1));
        $worksheet->mergeCells("K" . $cmp_count_row . ":" . "K" . ($row - 1));
      }
      $worksheet->setCellValue("B" . $cmp_count_row, $last_instance["count"]);
      $worksheet->setCellValue("C" . $cmp_count_row, $last_instance["project_title"]);
      $worksheet->setCellValue("D" . $cmp_count_row, $last_instance["project_cost"]);
      $worksheet->setCellValue("K" . $cmp_count_row, $last_instance["project_no"]);

      $worksheet->setCellValue("F" . $row, "=SUM(F10:F" . ($row - 1) . ")");
      $worksheet->setCellValue("G" . $row, "=SUM(G10:G" . ($row - 1) . ")");
      $worksheet->setCellValue("H" . $row, "=SUM(H10:H" . ($row - 1) . ")");
      $worksheet->getStyle("A10:" . "I" . $row)->applyFromArray($borderedStyleArray);
      // dd("test");
      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save(public_path() . '\\' . "excel_templates/Summary of Bidding Documents-" . $date_start . ".xlsx");
      return  response()->download(public_path() . '\\' . "excel_templates/Summary of Bidding Documents-" . $date_start . ".xlsx")->deleteFileAfterSend(true);
    } else {
      return abort(403, 'No Specific Bidders data found on the selected dates');
    }
  }

  public function downloadCertification($id)
  {
    $APP = new APP;
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $ntp = NoticeToProceed::find($id);
    if ($ntp == null) {
      return abort(403, "Unknown Notice to Proceed");
    } else {
      $cluster_bids = $APP->getClusterBids($ntp->project_bid_id);
      $project_plan = DB::table('project_plans')
        ->where('project_bidders.project_bid', $ntp->project_bid_id)
        ->select('project_plans.project_bid_id', 'governors.name as governor_name', 'governors.governor_id', 'procacts.plan_id', 'procacts.procact_id', 'procacts.plan_cluster_id', 'municipalities.municipality_name', 'project_plans.project_title', 'contractors.*', 'barangays.*', 'project_plans.project_cost')
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

      if ($project_plan == null) {
        $project_plan = DB::table('project_plans')
          ->where('project_bidders.project_bid', $ntp->project_bid_id)
          ->select('project_plans.project_bid_id', 'governors.name as governor_name', 'governors.governor_id', 'procacts.plan_id', 'procacts.procact_id', 'procacts.plan_cluster_id', 'municipalities.municipality_name', 'project_plans.project_title', 'contractors.*', 'barangays.*', 'project_plans.project_cost')
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

      $bac = DB::table('bids_and_awards_committee')
        ->select(
          'bids_and_awards_committee.*',
          DB::raw("CONCAT(bac_ch.member_prefix,' ',bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
          DB::raw("CONCAT(bac_vice_ch.member_prefix,' ',bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
          DB::raw("CONCAT(bac_alternate_vice_ch.member_prefix,' ',bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
          DB::raw(" UPPER(CONCAT(bac_sec_ch.member_prefix,' ',bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname)) AS bac_sec_chairman_name"),
          DB::raw("CONCAT(bac_sec_vice_ch.member_prefix,' ',bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
          DB::raw("CONCAT(bac_twg_ch.member_prefix,' ',bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname) AS bac_twg_chairman_name"),
          DB::raw("CONCAT(bac_twg_vice_ch.member_prefix,' ',bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
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

      $supplemental = DB::table('supplemental_bid_procacts')->where('procact_id', $project_plan->procact_id)->first();
      $supplemental_bid = "";
      if ($supplemental != null) {
        $supplemental_bid = "Supplemental Bid Bulletin, ";
      }

      $title = "";
      $duration = 0;
      $source = "";
      $letter = "A";
      $cluster_cost = "";
      $total = 0;
      if (count($cluster_bids) > 1) {
        foreach ($cluster_bids as $cluster_bid) {
          $duration = $duration + $cluster_bid->duration;
          if ($title == "") {
            $title = $letter . ".) " . $cluster_bid->project_title;
          } else {
            $title = $title . " " . $letter . ".) " . $cluster_bid->project_title;
          }
          if ($cluster_cost == "") {
            $cluster_cost = $letter . ".) Php " . number_format((float)$cluster_bid->project_cost, 2, '.', ',');
          } else {
            $cluster_cost = $cluster_cost . " " . $letter . ".) Php " . number_format((float)$cluster_bid->project_cost, 2, '.', ',');
          }
          $total = $total + (float)$cluster_bid->project_cost;
          ++$letter;
        }
      } else {
        $title = $cluster_bids[0]->project_title;
      }
      $title = strtoupper(strtolower($title));
      $title = htmlspecialchars($title);


      $filename = 'Certification' . md5(date('Y-m-d H:i:s:u')) . ".docx";
      $templateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/Certification.docx");
      $templateProcessor->setValue('title', str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($title)))));
      $templateProcessor->setValue('supplemental', $supplemental_bid);
      if ($cluster_cost != "") {
        $templateProcessor->setValue('abc', $cluster_cost . " = Php " . number_format((float)$total, 2, '.', ','));
      } else {
        $templateProcessor->setValue('abc', "Php" . number_format((float)$project_plan->project_cost, 2, '.', ','));
      }
      $templateProcessor->setValue('ntp_date_received', date('F d,Y', strtotime($ntp->ntp_date_received_by_contractor)));
      $templateProcessor->setValue('day', date('jS'));
      $templateProcessor->setValue('month_year', date('F Y'));
      $templateProcessor->setValue('chairperson', $bac->bac_sec_chairman_name);
      $templateProcessor->saveAs(public_path() . '\\' . 'word_results/' . $filename);
      return  response()->download(public_path() . '\\' . 'word_results/' . $filename)->deleteFileAfterSend(true);
    }
  }
}
