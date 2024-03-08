<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use Validator;
use PhpOffice\PhpWord\Element\Field;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\ProcurementController;


class ReportController extends Controller
{

  public function generateBidEvaluation()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("twg.generate_bid_eval", ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function submitGenerateBidEvaluation($date_opened)
  {

    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $date_opened = date("Y-m-d", strtotime($date_opened));
    $ids_array = [];
    $APP = new APP;
    $plans = DB::table('project_plans')
      ->where('project_timelines.bid_submission_start', $date_opened)
      ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->orderBy('project_plans.mode_id', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();


    $bac = DB::table('bids_and_awards_committee')
      ->select(
        'bids_and_awards_committee.*',
        DB::raw("CONCAT(bac_ch.member_prefix,' ',bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
        DB::raw("CONCAT(bac_vice_ch.member_prefix,' ',bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
        DB::raw("CONCAT(bac_alternate_vice_ch.member_prefix,' ',bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
        DB::raw("CONCAT(bac_sec_ch.member_prefix,' ',bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
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

    if (count($plans) > 0) {
      $due = "Post Qual Due Date: " . date("F j, Y", strtotime($date_opened . "+12 days"));
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/bid_evaluation.xlsx");
      $worksheet = $spreadsheet->setActiveSheetIndexByName("Sheet1");
      $worksheet->getCell('A1')->setValue(strtoupper(strtolower(date("F j, Y", strtotime($date_opened)) . " OPENING")));
      $worksheet->getCell('L1')->setValue($due);
      $row = 4;
      $count = 1;
      $same_location = true;
      $initial_mode = 0;
      $bidding_count = 1;

      foreach ($plans as $plan) {
        $initial_barangay = $plan->barangay_id;
        $initial_barangay = $plan->barangay_id;
        $initial_duration = $plan->duration;
        $duration = $initial_duration;
        if ($initial_barangay === null) {
          $same_location = false;
        }
        if ($plan->mode_id != $initial_mode) {
          $initial_mode = $plan->mode_id;
          if ($initial_mode == 1) {
            $group = "PUBLIC BIDDING";
            $rank_label = "LCB";
          } else if ($initial_mode == 2) {
            $group = "SMALL VALUE PROCUREMENT";
            $rank_label = "LCPQ";
          } else if ($initial_mode == 3) {
            $group = "NEGOTIATED PROCUREMENT";
            $rank_label = "LCPQ";
          } else {
            $group = "";
          }
          $worksheet->mergeCells('A' . $row . ':' . 'K' . $row);
          $worksheet->getStyle('A' . $row)->getFont()->setBold(true);
          $worksheet->getCell('A' . $row)->setValue($group);
          $row = $row + 1;
          $count = 1;
        }

        $start = $row;
        if (in_array($plan->plan_id, $ids_array) == false) {
          $title = $plan->project_title;
          array_push($ids_array, $plan->plan_id);
          $rank = 1;


          $title = $plan->project_title;
          $project_cost = $plan->project_cost;
          $total = $plan->project_cost;

          // Get cluster and append titles
          if ($plan->plan_cluster_id != null) {
            $letter = 'A';
            $total = 0;
            $title = "";
            $project_cost = "";
            $source_of_fund = "";
            $project_number = "";

            $clusters = DB::table('project_plans')
              ->where([['project_timelines.bid_submission_start', $date_opened], ['procacts.plan_cluster_id', $plan->plan_cluster_id]])
              ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
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
              $title = $title . "   " . $temp;
              $temp_source = $letter . '. ' . $cluster->source . ";";
              $temp_project_number = $letter . '. ' . $cluster->project_no . ";";
              // $temp2=$letter.'. '.$cluster->project_cost;
              $total = $total + $cluster->project_cost;

              if ($letter == "A") {
                $source_of_fund = $temp_source;
                $project_number = $temp_project_number;
              } else {
                $source_of_fund = $source_of_fund . "   " . $temp_source;
                $project_number = $project_number . "   " . $temp_project_number;
              }

              $project_cost = $project_cost . "PHP" . number_format((float)$cluster->project_cost, 2, '.', ',') . " + ";
              $letter = ++$letter;
              if ($cluster->special_case_1 == 1) {
                $title = $cluster->project_title;
              }
            }
            $project_cost = rtrim($project_cost, ' + ');
            $project_cost = $project_cost . "= PHP" . number_format((float)$total, 2, '.', ',');
            if ($cluster->duration != $initial_duration) {
              $duration = $duration + $cluster->duration;
            }
          } else {
            $title = $plan->project_title;
            $project_cost = "PHP" . number_format((float)$plan->project_cost, 2, '.', ',');
            $source_of_fund = $plan->source;
            $duration = $plan->duration;
            $project_number = $plan->project_no;
          }
          // get all bidders
          $bidders = $APP->getBidEvalBiddersData($plan->procact_id, 'responsive,active,non-responsive,disapproved,withdrawn,disqualified,ineligible');

          // BER for Bidding;
          if ($plan->procact_mode_id === 1) {
            $clonedWorksheet = clone $spreadsheet->getSheetByName('bid');
            $clonedWorksheet->setTitle((string)$bidding_count);
            $spreadsheet->addSheet($clonedWorksheet);
            $worksheet1 = $spreadsheet->setActiveSheetIndexByName((string)$bidding_count);
            // $worksheet1->getCell('D6')->setValue($due);
            $worksheet1->getCell('C9')->setValue(strtoupper(strtolower($title)));
            $worksheet1->getCell('D15')->setValue(strtoupper(strtolower($plan->municipality_display)) . ', BENGUET');
            if ($plan->plan_cluster_id == null) {
              $worksheet1->getCell('D16')->setValue($plan->project_cost);
            } else {
              $worksheet1->getCell('D16')->setValue(str_replace("₱", "PHP", $project_cost));
            }
            if ($plan->pre_proc_date != null) {
              $worksheet1->getCell('D25')->setValue(date("F d,Y", strtotime($plan->pre_proc_date)));
            }

            $worksheet1->getCell('D27')->setValue(date("F d,Y", strtotime($plan->advertisement_start)));
            $worksheet1->getCell('D29')->setValue(date("F d,Y", strtotime($plan->advertisement_end)));
            $worksheet1->getCell('D40')->setValue(date("F d,Y", strtotime($plan->advertisement_start)) . " - " . date("F d,Y", strtotime($plan->bid_submission_start . ' -1 day')));
            $worksheet1->getCell('D41')->setValue(getIssued($plan->procact_id));
            $worksheet1->getCell('D55')->setValue(getReceived($plan->procact_id));
            $worksheet1->getCell('D45')->setValue(date("F d,Y", strtotime($plan->pre_bid_start)) . " at 9:00 AM");
            $worksheet1->getCell('D51')->setValue(date("F d,Y", strtotime($plan->bid_submission_start)) . " at 8:30 AM");
            $worksheet1->getCell('D53')->setValue(date("F d,Y", strtotime($plan->bid_submission_start)) . " at 9:00 AM");
            $bidding_count++;

            if (count($bidders) > 0) {
              $array_bidders = (array)$bidders;
              $bid_as_read = $APP->sortObject($array_bidders, array('proposed_bid' => 'asc'));
              $bid_as_evaluated = $APP->sortObject($array_bidders, array('bid_as_evaluated' => 'asc'));
              $bid_as_read = $bid_as_read[0];
              $bid_as_evaluated = $bid_as_evaluated[0];
              $row1 = 62;
              $row2 = 62 + 9;
              if (count($bid_as_read) > 1) {
                $row2 = $row1 + 8 + count($bid_as_read);
                $worksheet1->insertNewRowBefore(63, (count($bid_as_read) - 1));
                $worksheet1->insertNewRowBefore(($row2 + 1), (count($bid_as_read) - 1));
              }



              foreach ($bid_as_read as $value) {
                $worksheet1->mergeCells('A' . $row1 . ':' . 'C' . $row1);
                $worksheet1->getCell('A' . $row1)->setValue($value->business_name);

                if ($value->bid_status === "disqualified") {
                  $disqualification = DB::table('disqualification_records')->where([['project_bid', $value->project_bid], ['remarks', 'like', 'Disqualified:%']])->orderBy('record_id', 'desc')->first();
                  $worksheet1->getCell('D' . $row1)->setValue($disqualification->remarks);
                } else if ($value->bid_status === "ineligible") {
                  $disqualification = DB::table('disqualification_records')->where([['project_bid', $value->project_bid], ['remarks', 'like', 'Ineligible:%']])->orderBy('record_id', 'desc')->first();
                  $worksheet1->getCell('D' . $row1)->setValue($disqualification->remarks);
                } else {
                  $worksheet1->getCell('D' . $row1)->setValue($value->proposed_bid);
                }

                $row1++;
              }

              foreach ($bid_as_evaluated as $value) {
                $worksheet1->mergeCells('A' . $row2 . ':' . 'C' . $row2);
                $worksheet1->getCell('A' . $row2)->setValue($value->business_name);
                $worksheet1->getCell('D' . $row2)->setValue($value->bid_as_evaluated);


                if ($value->bid_status === "disqualified") {
                  $disqualification = DB::table('disqualification_records')->where([['project_bid', $value->project_bid], ['remarks', 'like', 'Disqualified:%']])->orderBy('record_id', 'desc')->first();
                  // dd($disqualification);
                  $worksheet1->getCell('D' . $row2)->setValue($disqualification->remarks);
                } else if ($value->bid_status === "ineligible") {
                  $disqualification = DB::table('disqualification_records')->where([['project_bid', $value->project_bid], ['remarks', 'like', 'Ineligible:%']])->orderBy('record_id', 'desc')->first();
                  $worksheet1->getCell('D' . $row2)->setValue($disqualification->remarks);
                } else {
                  $worksheet1->getCell('D' . $row2)->setValue($value->bid_as_evaluated);
                }

                $row2++;
              }

              $worksheet1->getCell('C' . ($row2 + 4))->setValue(strtoupper(strtolower($bac->bac_twg_chairman_name)));
            } else {
              $worksheet1->getCell('C76')->setValue(strtoupper(strtolower($bac->bac_twg_chairman_name)));
              $worksheet1->getCell('A62')->setValue('No Bidders');
              $worksheet1->getCell('A71')->setValue('No Bidders');
            }

            $worksheet = $spreadsheet->setActiveSheetIndexByName("Sheet1");
          }


          // bidder_with_ranks
          $bidder_ranks = [];
          $rank = 1;
          foreach ($bidders as $bidder) {
            $temp_bidder = (array) $bidder;
            if ($bidder->bid_status != "disqualified" && $bidder->bid_status != "ineligible") {
              if ($plan->main_status === "deferred") {
                $place = "Deferred Opening";
                $temp_bidder["discount"] = null;
                $temp_bidder["proposed_bid"] = null;
                $temp_bidder["bid_as_evaluated"] = null;
                $temp_bidder["bid_in_words"] = null;
                $amount_of_discount = null;
                $lowest = null;
              } else {
                if ($rank === 1) {
                  $active_bidder = $APP->getBidEvalBiddersData($plan->procact_id, 'responsive,active,non-responsive,disapproved,withdrawn');
                  if (count($active_bidder) == 1) {
                    if ($initial_mode == 1) {
                      $place = "LONE BIDDER";
                    } else {
                      $place = "LONE QUOTATION";
                    }
                  } else {
                    $place = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . ' ' . $rank_label;
                  }
                } else {
                  $place = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . ' ' . $rank_label;
                }

                if ($bidder->proposed_bid <= $bidder->bid_in_words && $bidder->proposed_bid <= $bidder->bid_as_evaluated) {
                  $lowest = "I";
                } else if ($bidder->bid_in_words < $bidder->proposed_bid && $bidder->bid_in_words <= $bidder->bid_as_evaluated) {
                  $lowest = "J";
                } else if ($bidder->bid_as_evaluated < $bidder->proposed_bid && $bidder->bid_as_evaluated < $bidder->bid_in_words) {
                  $lowest = "M";
                } else {
                  $lowest = null;
                }

                // Check if detailed bid is not null
                $cluster_bids = $APP->getClusterBids($bidder->project_bid);
                $with_detailed_bids = 0;
                $detailed_proposed_bid = "";
                $detailed_bid_in_words = "";
                $detailed_bid_as_evaluated = "";
                $letter = 'A';
                foreach ($cluster_bids as $key => $project_bid) {
                  if ($project_bid->detailed_bid_as_read > 0) {
                    if ($detailed_proposed_bid == "") {
                      $detailed_proposed_bid = "₱" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                      $detailed_bid_in_words = "₱" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                      $detailed_bid_as_evaluated = "₱" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    } else {
                      $detailed_proposed_bid = $detailed_proposed_bid . " + ₱" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                      $detailed_bid_in_words = $detailed_bid_in_words . " + ₱" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                      $detailed_bid_as_evaluated = $detailed_bid_as_evaluated . " + ₱" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    }
                    ++$letter;
                    ++$with_detailed_bids;
                  }
                }

                if (count($cluster_bids) == $with_detailed_bids) {
                  $bidder->proposed_bid = $detailed_proposed_bid . " = ₱" . number_format($bidder->proposed_bid, 2, '.', ',');
                  $bidder->bid_in_words = $detailed_bid_in_words . " = ₱" . number_format($bidder->bid_in_words, 2, '.', ',');
                  $bidder->bid_as_evaluated = $detailed_bid_as_evaluated . " = ₱" . number_format($bidder->bid_as_evaluated, 2, '.', ',');
                }

                if ($bidder->proposed_bid == $bidder->bid_in_words) {
                  $bidder->bid_in_words = "SAME AS READ";
                }
                $temp_bidder = (array) $bidder;
                $rank = $rank + 1;
              }
              $temp_bidder = array_merge($temp_bidder, array("rank" => $place));
              $temp_bidder = array_merge($temp_bidder, array("lowest" => $lowest));
            } else {
              $place = null;
              // Check if detailed bid is not null
              $cluster_bids = $APP->getClusterBids($bidder->project_bid);
              $with_detailed_bids = 0;
              $detailed_proposed_bid = "";
              $detailed_bid_in_words = "";
              $detailed_bid_as_evaluated = "";
              $letter = 'A';
              foreach ($cluster_bids as $key => $project_bid) {
                if ($project_bid->detailed_bid_as_read > 0) {
                  if ($detailed_proposed_bid == "") {
                    $detailed_proposed_bid = "₱" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                    $detailed_bid_in_words = "₱" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                    $detailed_bid_as_evaluated = "₱" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                  } else {
                    $detailed_proposed_bid = $detailed_proposed_bid . " + ₱" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                    $detailed_bid_in_words = $detailed_bid_in_words . " + ₱" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                    $detailed_bid_as_evaluated = $detailed_bid_as_evaluated . " + ₱" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                  }
                  ++$letter;
                  ++$with_detailed_bids;
                }
              }

              if (count($cluster_bids) == $with_detailed_bids) {
                $bidder->proposed_bid = $detailed_proposed_bid . " = ₱" . number_format($bidder->proposed_bid, 2, '.', ',');
                $bidder->bid_in_words = $detailed_bid_in_words . " = ₱" . number_format($bidder->bid_in_words, 2, '.', ',');
                $bidder->bid_as_evaluated = $detailed_bid_as_evaluated . " = ₱" . number_format($bidder->bid_as_evaluated, 2, '.', ',');
              }

              if ($bidder->proposed_bid == $bidder->bid_in_words && $bidder->proposed_bid != null) {
                $bidder->bid_in_words = "SAME AS READ";
              }

              $temp_bidder = (array) $bidder;
              $temp_bidder = array_merge($temp_bidder, array("rank" => null));
              $temp_bidder = array_merge($temp_bidder, array("lowest" => null));
            }
            array_push($bidder_ranks, (object) $temp_bidder);
          }

          $bidders = $APP->sortObject($bidder_ranks, array('date_received' => 'asc', 'time_received' => 'asc'));

          if (count($bidders) === 0) {
            if ($initial_mode == 1) {
              $worksheet->getCell('H' . $row)->setValue('No Bidders');
            } else {
              $worksheet->getCell('H' . $row)->setValue('No Quotations');
            }

            $row = $row + 1;
          } else {

            foreach ($bidders as $bidder) {
              if ($bidder->bid_status == "disqualified") {
                $disqualification = DB::table('disqualification_records')->where([['project_bid', $bidder->project_bid], ['remarks', 'like', 'Disqualified:%']])->orderBy('record_id', 'desc')->first();
              } else if ($bidder->bid_status == "ineligible") {
                $disqualification = DB::table('disqualification_records')->where([['project_bid', $bidder->project_bid], ['remarks', 'like', 'Ineligible:%']])->orderBy('record_id', 'desc')->first();
              } else {
                $disqualification = null;
              }
              $worksheet->getCell('H' . $row)->setValue($bidder->business_name);
              $worksheet->getCell('I' . $row)->setValue($bidder->proposed_bid);
              if ($disqualification === null) {
                // mark lowest with yellow
                if ($bidder->bid_status != "disqualified" && $bidder->bid_status != "ineligible") {
                  $bidder->lowest = "M";
                  $lowest_style = [
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]
                  ];
                  if ($bidder->proposed_bid <= $bidder->bid_in_words) {
                    if ($bidder->proposed_bid <= $bidder->bid_as_evaluated) {
                      $bidder->lowest = "I";
                    }
                  } else if ($bidder->bid_in_words < $bidder->proposed_bid) {
                    if ($bidder->bid_in_words <= $bidder->bid_as_evaluated) {
                      $bidder->lowest = "J";
                    }
                  } else if ($bidder->bid_as_evaluated < $bidder->bid_in_words) {
                    if ($bidder->bid_as_evaluated < $bidder->proposed_bid) {
                      $bidder->lowest = "M";
                    }
                  }

                  $worksheet->getStyle($bidder->lowest . $row)->applyFromArray($lowest_style);
                }
              }

              if ($bidder->bid_as_evaluated != null) {
                if ((float)$bidder->bid_in_words == 0) {
                  $worksheet->getCell('J' . $row)->setValue($bidder->bid_in_words);
                } else {
                  $worksheet->getCell('J' . $row)->setValue(number_format((float)$bidder->bid_in_words, 2, '.', ','));
                }
              }


              if ($bidder->discount > 0) {
                $worksheet->getCell('K' . $row)->setValue('DISCOUNT (' . $bidder->discount . " %)");
              } else if ($bidder->amount_of_discount > 0 && $bidder->discount == 0) {
                $worksheet->getCell('L' . $row)->setValue("N/A");
              } else if ($bidder->discount == null) {
              } else {
                $worksheet->getCell('K' . $row)->setValue('NO DISCOUNT');
              }

              if ($bidder->proposed_bid == $bidder->bid_as_evaluated && $bidder->bid_as_evaluated != null) {
                $worksheet->getCell('M' . $row)->setValue($bidder->bid_as_evaluated);
              } else if ($bidder->bid_as_evaluated == null) {
              } else {
                $worksheet->getCell('M' . $row)->setValue($bidder->bid_as_evaluated);
              }


              if ($bidder->amount_of_discount > 0) {
                $amount_of_discount = $bidder->amount_of_discount;
              } else if ($bidder->amount_of_discount == null) {
                $amount_of_discount = null;
              } else {
                $amount_of_discount = "N/A";
              }

              $worksheet->getCell('L' . $row)->setValue($amount_of_discount);

              if (count($bidders) >= 1) {
                if ($bidder->bid_status === 'disqualified' || $bidder->bid_status === 'ineligible') {
                  $place = $disqualification->remarks;
                  $worksheet->getCell('N' . $row)->setValue($place);
                } else {
                  $worksheet->getCell('N' . $row)->setValue($bidder->rank);
                }
              }

              $worksheet->getStyle('B' . $row . ':' . 'N' . $row)
                ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('B' . $row . ':' . 'N' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('B' . $row . ':' . 'N' . $row)
                ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('B' . $row . ':' . 'N' . $row)
                ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('B' . $row)
                ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('C' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('D' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('E' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('F' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('G' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('H' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('I' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('J' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('K' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('L' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $worksheet->getStyle('M' . $row)
                ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
              $row = $row + 1;
            }
          }

          $end = $row - 1;
          $worksheet->getCell('A' . $start)->setValue($count);
          $worksheet->getCell('B' . $start)->setValue($title);
          $worksheet->getCell('C' . $start)->setValue($project_number);
          $worksheet->getCell('D' . $start)->setValue($source_of_fund);
          $worksheet->getCell('E' . $start)->setValue($duration);
          $worksheet->getCell('G' . $start)->setValue($project_cost);
          if ($start != $end) {
            $worksheet->mergeCells('A' . $start . ':' . 'A' . $end);
            $worksheet->mergeCells('B' . $start . ':' . 'B' . $end);
            $worksheet->mergeCells('C' . $start . ':' . 'C' . $end);
            $worksheet->mergeCells('D' . $start . ':' . 'D' . $end);
            $worksheet->mergeCells('E' . $start . ':' . 'E' . $end);
            $worksheet->mergeCells('F' . $start . ':' . 'F' . $end);
            $worksheet->mergeCells('G' . $start . ':' . 'G' . $end);
          }

          if ($same_location === true) {
            if ($plan->barangay_id != null) {
              $worksheet->getCell('F' . $start)->setValue($plan->barangay_name . ', ' . $plan->municipality_name . ', Benguet');
            } else {
              $worksheet->getCell('F' . $start)->setValue($plan->municipality_name . ', Benguet');
            }
          } else {
            $worksheet->getCell('F' . $start)->setValue($plan->municipality_name . ', Benguet');
          }

          $worksheet->getStyle('A' . $start . ':' . 'N' . $end)
            ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
          $worksheet->getStyle('A' . $start . ':' . 'N' . $end)
            ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
          $worksheet->getStyle('A' . $start . ':' . 'N' . $end)
            ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
          $worksheet->getStyle('A' . $start . ':' . 'N' . $end)
            ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

          $count = $count + 1;
          $row = $row + 1;
        }
      }
      $worksheet->getStyle('A4:' . "N" . $row)->getAlignment()->setWrapText(true);
      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save(public_path() . '\\' . "excel_templates/" . $date_opened . "-BER.xlsx");
      return  response()->download(public_path() . '\\' . "excel_templates/" . $date_opened . "-BER.xlsx")->deleteFileAfterSend(true);
    } else {
      return abort(403, 'No Projects Opened on Selected Date');
    }
  }

  public function generateBidEvaluationTable()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("admin.generate_bid_eval", ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function submitGenerateBidEvaluationTable(Request $request)
  {
    $data = $request->validate([
      "date_opened" => 'required'
    ]);

    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $desired_plan_array = [];
    $desired_plan_format = ["number" => null, "project_title" => null, "location" => null, "rows" => null, "bidders" => null];
    $date_opened = date("Y-m-d", strtotime($request->input('date_opened')));
    $ids_array = [];
    $APP = new APP;
    $plans = DB::table('project_plans')
      ->where('project_timelines.bid_submission_start', $date_opened)
      ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->orderBy('project_plans.mode_id', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();



    if (count($plans) > 0) {
      $count = 1;
      $initial_mode = 0;
      foreach ($plans as $plan) {
        $same_location = true;
        $initial_barangay = $plan->barangay_id;
        $initial_duration = $plan->duration;
        $duration = $initial_duration;
        if ($initial_barangay === null) {
          $same_location = false;
        }
        if ($plan->mode_id != $initial_mode) {
          $initial_mode = $plan->mode_id;
          if ($initial_mode == 1) {
            $group = "PUBLIC BIDDING";
            $rank_label = "LCB";
          } else if ($initial_mode == 2) {
            $group = "SMALL VALUE PROCUREMENT";
            $rank_label = "LCPQ";
          } else if ($initial_mode == 3) {
            $group = "NEGOTIATED PROCUREMENT";
            $rank_label = "LCPQ";
          } else {
            $group = "";
          }
          $count = 1;
        }
        $temp_plan = $desired_plan_format;
        if (in_array($plan->plan_id, $ids_array) == false) {
          $title = $plan->project_title;
          array_push($ids_array, $plan->plan_id);
          $rank = 1;
          $title = $plan->project_title;
          $project_cost = $plan->project_cost;
          $total = $plan->project_cost;

          // Get cluster and append titles
          if ($plan->plan_cluster_id != null) {
            $letter = 'A';
            $total = 0;
            $title = "";
            $project_cost = "";
            $source_of_fund = "";
            $project_number = "";

            $clusters = DB::table('project_plans')
              ->where([['project_timelines.bid_submission_start', $date_opened], ['procacts.plan_cluster_id', $plan->plan_cluster_id]])
              ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
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
              $title = $title . "   " . $temp;
              $temp_source = $letter . '. ' . $cluster->source . ";";
              $temp_project_number = $letter . '. ' . $cluster->project_no . ";";
              // $temp2=$letter.'. '.$cluster->project_cost;
              $total = $total + $cluster->project_cost;
              $source_of_fund = $source_of_fund . "   " . $temp_source;
              $project_number = $project_number . "   " . $temp_project_number;
              $project_cost = $project_cost . "PHP" . number_format((float)$cluster->project_cost, 2, '.', ',') . " + ";
              $letter = ++$letter;
              if ($cluster->special_case_1 == 1) {
                $title = $cluster->project_title;
              }
            }
            $project_cost = rtrim($project_cost, ' + ');
            $project_cost = $project_cost . "= PHP" . number_format((float)$total, 2, '.', ',');
          } else {
            $title = $plan->project_title;
            $project_cost = "PHP" . number_format((float)$plan->project_cost, 2, '.', ',');
            $source_of_fund = $plan->source;
            $duration = $plan->duration;
            $project_number = $plan->project_no;
          }


          // get all bidders
          $bidders = $APP->getBidEvalBiddersData($plan->procact_id, 'responsive,active,non-responsive,disapproved,withdrawn,disqualified,ineligible');
          // bidder_with_ranks
          $bidder_ranks = [];
          $rank = 1;
          foreach ($bidders as $bidder) {
            if ($bidder->bid_status == "disqualified") {
              $disqualification = DB::table('disqualification_records')->where([['project_bid', $bidder->project_bid], ['remarks', 'like', 'Disqualified:%']])->orderBy('record_id', 'desc')->first();
            } else if ($bidder->bid_status == "ineligible") {
              $disqualification = DB::table('disqualification_records')->where([['project_bid', $bidder->project_bid], ['remarks', 'like', 'Ineligible:%']])->orderBy('record_id', 'desc')->first();
            } else {
              $disqualification = null;
            }

            $temp_bidder = (array) $bidder;
            $temp_bidder["business_name"] = str_replace("&amp;", "&", htmlspecialchars(strtoupper(strtolower($temp_bidder["business_name"]))));

            if ($bidder->bid_status != "disqualified") {
              if ($plan->main_status === "deferred") {
                $temp_bidder["rank"] = "Deferred Opening";
                $temp_bidder["discount"] = null;
                $temp_bidder["proposed_bid"] = null;
                $temp_bidder["bid_as_evaluated"] = null;
                $temp_bidder["bid_in_words"] = null;
                $amount_of_discount = null;
              } else {
                if ($rank === 1) {
                  $active_bidder = $APP->getBidEvalBiddersData($plan->procact_id, 'responsive,active,non-responsive,disapproved,withdrawn');
                  if (count($active_bidder) == 1) {
                    if ($initial_mode == 1) {
                      $place = "LONE BIDDER";
                    } else {
                      $place = "LONE QUOTATION";
                    }
                  } else {
                    $place = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . ' ' . $rank_label;
                  }
                } else {
                  $place = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . ' ' . $rank_label;
                }
                $temp_bidder = array_merge($temp_bidder, array("rank" => $place));
                $rank = $rank + 1;
                if ($bidder->discount > 0) {
                  $temp_bidder["discount"] = $bidder->discount . "%";
                } else if ($bidder->amount_of_discount > 0 && $bidder->discount == 0) {
                  $temp_bidder["discount"] = "N/A";
                } else {
                  $temp_bidder["discount"] = "No Discount";
                }

                if ($bidder->amount_of_discount > 0) {
                  $amount_of_discount = "PHP" . number_format($bidder->amount_of_discount, 2, '.', ',');
                } else {
                  $amount_of_discount = "N/A";
                }

                $temp_bidder["proposed_bid"] = "PHP" . number_format((float)$temp_bidder["proposed_bid"], 2, '.', ',');
                if ($temp_bidder["proposed_bid"] === $temp_bidder["bid_as_evaluated"]) {
                  $temp_bidder["bid_as_evaluated"] =  $temp_bidder["proposed_bid"];
                } else {
                  $temp_bidder["bid_as_evaluated"] = "PHP" . number_format((float)$temp_bidder["bid_as_evaluated"], 2, '.', ',');
                }

                $temp_bidder["bid_in_words"] = "PHP" . number_format((float)$temp_bidder["bid_in_words"], 2, '.', ',');



                // Check if detailed bid is not null
                $cluster_bids = $APP->getClusterBids($bidder->project_bid);
                $with_detailed_bids = 0;
                $detailed_proposed_bid = "";
                $detailed_bid_in_words = "";
                $detailed_bid_as_evaluated = "";
                $letter = 'A';
                foreach ($cluster_bids as $key => $project_bid) {
                  if ($project_bid->detailed_bid_as_read > 0) {
                    if ($detailed_proposed_bid == "") {
                      $detailed_proposed_bid = "PHP" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                      $detailed_bid_in_words = "PHP" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                      $detailed_bid_as_evaluated = "PHP" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    } else {
                      $detailed_proposed_bid = $detailed_proposed_bid . " + PHP" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                      $detailed_bid_in_words = $detailed_bid_in_words . " + PHP" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                      $detailed_bid_as_evaluated = $detailed_bid_as_evaluated . " + PHP" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    }
                    ++$letter;
                    ++$with_detailed_bids;
                  }
                }

                if (count($cluster_bids) == $with_detailed_bids) {
                  $temp_bidder["proposed_bid"] = $detailed_proposed_bid . " = " . $temp_bidder["proposed_bid"];
                  $temp_bidder["bid_in_words"] = $detailed_bid_in_words . " = " . $temp_bidder["bid_in_words"];
                  $temp_bidder["bid_as_evaluated"] = $detailed_bid_as_evaluated . " = " . $temp_bidder["bid_as_evaluated"];
                }

                if ($temp_bidder["proposed_bid"] == $temp_bidder["bid_in_words"]) {
                  $temp_bidder["bid_in_words"] = "SAME AS READ";
                }
              }
            } else {
              if ($bidder->bid_as_evaluated > 0) {
                if ($bidder->discount > 0) {
                  $temp_bidder["discount"] = $bidder->discount . "%";
                } else if ($bidder->amount_of_discount > 0 && $bidder->discount == 0) {
                  $temp_bidder["discount"] = "N/A";
                } else {
                  $temp_bidder["discount"] = "No Discount";
                }

                if ($bidder->amount_of_discount > 0) {
                  $amount_of_discount = "PHP" . number_format($bidder->amount_of_discount, 2, '.', ',');
                } else {
                  $amount_of_discount = "N/A";
                }


                $temp_bidder["proposed_bid"] = "PHP" . number_format((float)$temp_bidder["proposed_bid"], 2, '.', ',');

                if ($temp_bidder["proposed_bid"] === $temp_bidder["bid_as_evaluated"]) {
                  $temp_bidder["bid_as_evaluated"] = "SAME AS READ";
                } else {
                  $temp_bidder["bid_as_evaluated"] = "PHP" . number_format((float)$temp_bidder["bid_as_evaluated"], 2, '.', ',');
                }

                if ($temp_bidder["proposed_bid"] === $temp_bidder["bid_in_words"]) {
                  $bid_in_words = "SAME AS READ";
                }
              } else {

                $temp_bidder["discount"] = "";
                $temp_bidder["proposed_bid"] = "";
                $bid_in_words = "";
                $temp_bidder["bid_as_evaluated"] = "";
                $amount_of_discount = null;
                $place = null;
                $temp_bidder = array_merge($temp_bidder, array("rank" => null));
              }
            }

            if ($temp_bidder["bid_status"] === "disqualified" || $temp_bidder["bid_status"] === "ineligible") {
              $temp_bidder["rank"] = $disqualification->remarks;
            }

            $temp_bidder = array_merge($temp_bidder, array("amount_of_discount" => $amount_of_discount));
            array_push($bidder_ranks, (object) $temp_bidder);
          }
          $bidders = $APP->sortObject($bidder_ranks, array('date_received' => 'asc', 'time_received' => 'asc'));
          $rows = count($bidders);
          if ($rows === 0) {
            $rows = 1;
          }
          $temp_plan["number"] = $count;
          $temp_plan["bidders"] = (array)$bidders;
          $temp_plan["bidder_count"] = count($bidders);
          $temp_plan["rows"] = $rows;
          $temp_plan["project_title"] = str_replace("&amp;", "&", htmlspecialchars(strtoupper(strtolower($title))));
          $temp_plan["project_cost"] = $project_cost;
          $temp_plan["group"] = $group;
          $temp_plan["mode_id"] = $initial_mode;
          $temp_plan["project_no"] = str_replace("&amp;", "&", htmlspecialchars(strtoupper(strtolower($project_number))));
          $temp_plan["source_of_fund"] = str_replace("&amp;", "&", htmlspecialchars(strtoupper(strtolower($source_of_fund))));
          $temp_plan["duration"] = $duration;

          if ($same_location === true) {
            if ($plan->barangay_id != null) {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
            } else {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->barangay_name . ", " . $plan->municipality_name)));
            }
          } else {
            $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
          }
          $count = $count + 1;
          array_push($desired_plan_array, (object) $temp_plan);
        }
      }
      return back()->withInput()->with("project_plans", (object)$desired_plan_array);
    } else {
      return back()->withInput()->with("project_plans", []);
    }
  }

  public function generateChecklist()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("admin.generate_checklist", ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function generateAwardedProjects()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("admin.generate_awarded", ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function generateBidderCustomReport()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("admin.generate_disqualified_non_responsive", ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function submitGenerateAwarded(Request $request)
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
      ->select('*', 'resolutions.resolution_date AS noa_date_released', 'procacts.open_bid as bidding_date', DB::raw('DATE_FORMAT(resolutions.resolution_date, "%Y-%m") AS month_group'))
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->join('project_bidders', 'project_plans.project_bid_id', 'project_bidders.project_bid')
      ->leftJoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
      ->leftJoin('contracts', 'project_bidders.project_bid', 'contracts.project_bid_id')
      ->leftJoin('chsp', 'project_bidders.project_bid', 'chsp.chsp_project_bid')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->join('resolution_projects', 'procacts.procact_id', 'resolution_projects.procact_id')
      ->join('resolutions', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
      ->whereRaw('resolutions.resolution_date BETWEEN CAST( "' . $date_start . '" AS DATE) AND CAST( "' . $date_end . '" AS DATE) AND resolutions.type="RRA"')
      ->orderBy('month_group', 'asc')
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

          $temp_plan["project_no"] = htmlspecialchars(strtoupper(strtolower($project_number)));
          $temp_plan["project_title"] = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($title))));
          $temp_plan["project_cost"] = $project_cost;
          $temp_plan["total_project_cost"] = $total;
          $temp_plan["source_of_fund"] = htmlspecialchars(strtoupper(strtolower($source_of_fund)));
          if ($same_location === true) {
            if ($plan->barangay_id != null) {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
            } else {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->barangay_name . ", " . $plan->municipality_name)));
            }
          } else {
            $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
          }

          $temp_plan["winning_bidder"] = htmlspecialchars(strtoupper(strtolower($winner[0]->business_name)));
          $temp_plan["name_address"] = htmlspecialchars(strtoupper(strtolower($winner[0]->owner))) . " , " . $winner[0]->address;
          $temp_plan["total_bid"] = $winner[0]->final_minimum_cost;
          if ($isZero === false && count($cluster_bids) > 1) {
            $temp_plan["bid_amount"] = $detailed_bids;
          } else {
            $temp_plan["bid_amount"] = "PHP" . number_format((float)$winner[0]->final_minimum_cost, 2, '.', ',');
          }
          $temp_plan["bidding_date"] = date("F d,Y", strtotime($plan->bidding_date));
          $temp_plan["group"] = date("F Y", strtotime($plan->noa_date_released));
          $temp_plan["duration"] = $duration;
          $temp_plan["resolution_date"] = $plan->resolution_date;
          $temp_plan["award_notice"] = $plan->award_notice;
          $temp_plan["municipality_name"] = $plan->municipality_name;
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


  public function SubmitGenerateBidderCustomReport(Request $request)
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
    $procurement_mode = $request->procurement_mode;
    $bidder_status = $request->bidder_status;
    // dd($bidder_status);
    if ($procurement_mode == 0) {
      $modes = [1, 2, 3];
    } else if ($procurement_mode == 1) {
      $modes = [1];
    } else if ($procurement_mode == 2) {
      $modes = [2];
    } else if ($procurement_mode == 3) {
      $modes = [3];
    } else if ($procurement_mode == 4) {
      $modes = [2, 3];
    } else {
      return abort(403, 'Uknown Procurement Mode!');
    }

    $string_modes = "(" . implode(",", $modes) . ")";

    // Bidder Status
    if ($bidder_status == 0) {
      $rfq_bidders = DB::table('rfq_projects')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('project_bidders.project_bid', null)
        ->whereIn('procact_mode_id', $modes)
        ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->leftJoin('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $bid_doc_bidders = DB::table('bid_doc_projects')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('project_bidders.project_bid', null)
        ->whereIn('procact_mode_id', $modes)
        ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 1) {


      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw('procact_mode_id="1" AND bid_status in ("disqualified","ineligible") AND procacts.open_bid BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  bid_status in ("disapproved") AND lce_evaluation.lce_evaluation_date BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("lce_evaluation", "lce_evaluation.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw('procact_mode_id IN ' . $string_modes . ' AND bid_status in ("disqualified","ineligible") AND procacts.open_bid BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  bid_status in ("disapproved") AND lce_evaluation.lce_evaluation_date BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
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
      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('twg_evaluations.post_qual_end', [$date_start, $date_end])
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('twg_evaluations.post_qual_end', [$date_start, $date_end])
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 3) {
      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        // ->where('project_title','like','%orengao%')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->orderBy('project_bidders.project_bid', 'asc')
        ->get();


      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
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
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->orderBy('project_bidders.project_bid', 'asc')
        ->get();
    } else if ($bidder_status == 4) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND twg_evaluations.post_qual_end > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND ISNULL(procacts.award_notice) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND procacts.award_notice > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice >= '" . $date_start . "' AND procacts.award_notice <= '" . $date_end . "'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND twg_evaluations.post_qual_end > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND ISNULL(procacts.award_notice) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND procacts.award_notice > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice >= '" . $date_start . "' AND procacts.award_notice <= '" . $date_end . "'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 5) {

      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
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
    } else if ($bidder_status == 6) {

      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("project_bidders.bid_status='withdrawn'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
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

      $rfq_bidders1 = DB::table('rfq_projects')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('project_bidders.project_bid', null)
        ->where('procact_mode_id', 1)
        ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->leftJoin('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();



      $rfq_bidders2 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw('procact_mode_id="1" AND bid_status in ("disqualified","ineligible") AND procacts.open_bid BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  bid_status in ("disapproved") AND lce_evaluation.lce_evaluation_date BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("lce_evaluation", "lce_evaluation.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders3 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('twg_evaluations.post_qual_end', [$date_start, $date_end])
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders4 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders5 = DB::table('project_bidders')
        ->select('*', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND twg_evaluations.post_qual_end > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND ISNULL(procacts.award_notice) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND procacts.award_notice > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice >= '" . $date_start . "' AND procacts.award_notice <= '" . $date_end . "'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $rfq_bidders6 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders7 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='withdrawn' AND project_bidders.withdrawal_receive_date BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders1 = DB::table('bid_doc_projects')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('project_bidders.project_bid', null)
        ->where('procact_mode_id', 1)
        ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $bid_doc_bidders2 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw('procact_mode_id="1" AND bid_status in ("disqualified","ineligible") AND procacts.open_bid BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  bid_status in ("disapproved") AND lce_evaluation.lce_evaluation_date BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
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
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('twg_evaluations.post_qual_end', [$date_start, $date_end])
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders4 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders5 = DB::table('project_bidders')
        ->select('*', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND twg_evaluations.post_qual_end > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND ISNULL(procacts.award_notice) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND procacts.award_notice > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice >= '" . $date_start . "' AND procacts.award_notice <= '" . $date_end . "'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $bid_doc_bidders6 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
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



      $bid_doc_bidders7 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
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
      if (count($rfq_bidders1) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders1));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders1);
        }
      }
      if (count($bid_doc_bidders2) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders2));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders2);
        }
      }
      if (count($rfq_bidders2) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders2));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders2);
        }
      }
      if (count($bid_doc_bidders3) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders3));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders3);
        }
      }
      if (count($rfq_bidders3) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders3));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders3);
        }
      }
      if (count($bid_doc_bidders4) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders4));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders4);
        }
      }
      if (count($rfq_bidders4) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders4));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders4);
        }
      }
      if (count($bid_doc_bidders5) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders5));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders5);
        }
      }
      if (count($rfq_bidders5) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders5));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders5);
        }
      }
      if (count($bid_doc_bidders6) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders6));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders6);
        }
      }
      if (count($rfq_bidders6) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders6));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders6);
        }
      }
      if (count($bid_doc_bidders7) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders7));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders7);
        }
      }
      if (count($rfq_bidders7) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders7));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders6);
        }
      }
    } else {
      if ($bid_doc_bidders != null && $rfq_bidders != null) {
        $custom_bidders = array_merge((array)json_decode($bid_doc_bidders), (array)json_decode($rfq_bidders));
      } else if ($bid_doc_bidders != null && $rfq_bidders == null) {
        $custom_bidders = (array)$bid_doc_bidders;
      } else if ($bid_doc_bidders == null && $rfq_bidders != null) {
        $custom_bidders = (array)$rfq_bidders;
      } else {
        $custom_bidders = [];
      }
    }

    if (count($custom_bidders) > 0) {
      $custom_bidders = $APP->sortObject($custom_bidders, array('open_bid' => 'asc', 'itb_arrangement' => 'asc', 'post_qual_start' => 'asc', 'post_qual_end' => 'asc'));
      foreach ($custom_bidders as $not_responsive_bidder) {
        if (in_array($not_responsive_bidder->init_id, $ids_array) == false) {
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

          if ($not_responsive_bidder->bid_status == "disqualified") {
            $disqualification_records = DB::table('disqualification_records')->where('project_bid', $not_responsive_bidder->bidders_bid)->orderBy('record_id', 'desc')->first();
            $temp_not_responsive_bidder["remarks"] = $disqualification_records->remarks;
          } else {
            $temp_not_responsive_bidder["remarks"] = $not_responsive_bidder->twg_evaluation_remarks;
          }

          if ($not_responsive_bidder->bid_status == null) {
            $temp_not_responsive_bidder["bid_status"] = "Did Not Submit";
          } else if ($not_responsive_bidder->bid_status == "active" && ($not_responsive_bidder->award_notice != null || $not_responsive_bidder->post_qual != null)) {
            $temp_not_responsive_bidder["bid_status"] = "Loosing Bid";
          } else {
            $temp_not_responsive_bidder["bid_status"] = $not_responsive_bidder->bid_status;
          }

          if ($not_responsive_bidder->procact_mode_id === 1) {
            $temp_not_responsive_bidder["fees"] = "PHP" . number_format((float)$not_responsive_bidder->fees, 2, '.', ',');
            $temp_not_responsive_bidder["total_fees"] = (float)$not_responsive_bidder->fees;
          } else {
            $temp_not_responsive_bidder["fees"] = "N/A";
            $temp_not_responsive_bidder["total_fees"] = "0.00";
          }

          $count = $count + 1;
          array_push($desired_non_responsive_array, (object) $temp_not_responsive_bidder);
        }
      }
      return back()->withInput()->with("project_bidders", (array)$desired_non_responsive_array);
    } else {
      return abort(403, 'No Specific Bidders data found on the selected dates');
    }
  }


  public function downloadGenerateBidderCustomReport($date_start, $date_end, $bidder_status, $procurement_mode)
  {
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $desired_non_responsive_array = [];
    $desired_non_responsive_format = [];
    $date_start = date("Y-m-d", strtotime($date_start));
    $date_end = date("Y-m-d", strtotime($date_end));
    $ids_array = [];
    $APP = new APP;
    $count = 1;
    if ($procurement_mode == 0) {
      $modes = [1, 2, 3];
    } else if ($procurement_mode == 1) {
      $modes = [1];
    } else if ($procurement_mode == 2) {
      $modes = [2];
    } else if ($procurement_mode == 3) {
      $modes = [3];
    } else if ($procurement_mode == 4) {
      $modes = [2, 3];
    } else {
      return abort(403, 'Uknown Procurement Mode!');
    }

    $string_modes = "(" . implode(",", $modes) . ")";

    if ($procurement_mode == 0) {
      $modes = [1, 2, 3];
    } else if ($procurement_mode == 1) {
      $modes = [1];
    } else if ($procurement_mode == 2) {
      $modes = [2];
    } else if ($procurement_mode == 3) {
      $modes = [3];
    } else if ($procurement_mode == 4) {
      $modes = [2, 3];
    } else {
      return abort(403, 'Uknown Procurement Mode!');
    }

    $string_modes = "(" . implode(",", $modes) . ")";

    // Bidder Status
    if ($bidder_status == 0) {
      $rfq_bidders = DB::table('rfq_projects')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('project_bidders.project_bid', null)
        ->whereIn('procact_mode_id', $modes)
        ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->leftJoin('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $bid_doc_bidders = DB::table('bid_doc_projects')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('project_bidders.project_bid', null)
        ->whereIn('procact_mode_id', $modes)
        ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 1) {


      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw('procact_mode_id="1" AND bid_status in ("disqualified","ineligible") AND procacts.open_bid BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  bid_status in ("disapproved") AND lce_evaluation.lce_evaluation_date BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("lce_evaluation", "lce_evaluation.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw('procact_mode_id IN ' . $string_modes . ' AND bid_status in ("disqualified","ineligible") AND procacts.open_bid BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  bid_status in ("disapproved") AND lce_evaluation.lce_evaluation_date BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
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
      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('twg_evaluations.post_qual_end', [$date_start, $date_end])
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('twg_evaluations.post_qual_end', [$date_start, $date_end])
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 3) {
      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        // ->where('project_title','like','%orengao%')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->orderBy('project_bidders.project_bid', 'asc')
        ->get();


      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
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
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->orderBy('project_bidders.project_bid', 'asc')
        ->get();
    } else if ($bidder_status == 4) {
      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND twg_evaluations.post_qual_end > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND ISNULL(procacts.award_notice) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND procacts.award_notice > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice >= '" . $date_start . "' AND procacts.award_notice <= '" . $date_end . "'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND twg_evaluations.post_qual_end > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND ISNULL(procacts.award_notice) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND procacts.award_notice > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice >= '" . $date_start . "' AND procacts.award_notice <= '" . $date_end . "'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();
    } else if ($bidder_status == 5) {

      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
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
    } else if ($bidder_status == 6) {

      $rfq_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("project_bidders.bid_status='withdrawn'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
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

      $rfq_bidders1 = DB::table('rfq_projects')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('project_bidders.project_bid', null)
        ->where('procact_mode_id', 1)
        ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->leftJoin('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();



      $rfq_bidders2 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw('procact_mode_id="1" AND bid_status in ("disqualified","ineligible") AND procacts.open_bid BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  bid_status in ("disapproved") AND lce_evaluation.lce_evaluation_date BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("lce_evaluation", "lce_evaluation.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders3 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('twg_evaluations.post_qual_end', [$date_start, $date_end])
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders4 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders5 = DB::table('project_bidders')
        ->select('*', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND twg_evaluations.post_qual_end > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND ISNULL(procacts.award_notice) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND procacts.award_notice > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice >= '" . $date_start . "' AND procacts.award_notice <= '" . $date_end . "'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $rfq_bidders6 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $rfq_bidders7 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='withdrawn' AND project_bidders.withdrawal_receive_date BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->orWhereRaw("procacts.procact_mode_id IN ".$string_modes." AND project_bidders.bid_status='active' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders1 = DB::table('bid_doc_projects')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('project_bidders.project_bid', null)
        ->where('procact_mode_id', 1)
        ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $bid_doc_bidders2 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw('procact_mode_id="1" AND bid_status in ("disqualified","ineligible") AND procacts.open_bid BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->orWhereRaw('lce_evaluation.id IS NOT NULL AND  bid_status in ("disapproved") AND lce_evaluation.lce_evaluation_date BETWEEN "' . $date_start . '" AND "' . $date_end . '"')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
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
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->where('bid_status', 'non-responsive')
        ->where('procact_mode_id', 1)
        // ->whereBetween('procacts.open_bid', [$date_start, $date_end])
        ->whereBetween('twg_evaluations.post_qual_end', [$date_start, $date_end])
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders4 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND bid_status='responsive' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
        // ->whereRaw("procacts.procact_mode_id IN ".$string_modes." AND bid_status='responsive' AND procacts.post_qual BETWEEN '".$date_start."' AND '".$date_end."'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();

      $bid_doc_bidders5 = DB::table('project_bidders')
        ->select('*', DB::raw('IF(project_bidders.bid_status != null ,"ongoing","ongoing") AS bid_status'), DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND ISNULL(project_plans.project_bid_id) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'non-responsive' AND twg_evaluations.post_qual_end > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND ISNULL(procacts.award_notice) AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status = 'responsive' AND procacts.award_notice > '" . $date_end . "' AND procacts.open_bid <= '" . $date_end . "'")
        ->orWhereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice >= '" . $date_start . "' AND procacts.award_notice <= '" . $date_end . "'")
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->get();


      $bid_doc_bidders6 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
        ->whereRaw("procacts.procact_mode_id IN " . $string_modes . " AND project_bidders.bid_status='active' AND procacts.award_notice BETWEEN '" . $date_start . "' AND '" . $date_end . "'")
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



      $bid_doc_bidders7 = DB::table('project_bidders')
        ->select('*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_bidders.project_bid as bidders_bid', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"))
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
      if (count($rfq_bidders1) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders1));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders1);
        }
      }
      if (count($bid_doc_bidders2) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders2));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders2);
        }
      }
      if (count($rfq_bidders2) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders2));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders2);
        }
      }
      if (count($bid_doc_bidders3) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders3));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders3);
        }
      }
      if (count($rfq_bidders3) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders3));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders3);
        }
      }
      if (count($bid_doc_bidders4) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders4));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders4);
        }
      }
      if (count($rfq_bidders4) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders4));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders4);
        }
      }
      if (count($bid_doc_bidders5) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders5));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders5);
        }
      }
      if (count($rfq_bidders5) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders5));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders5);
        }
      }
      if (count($bid_doc_bidders6) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders6));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders6);
        }
      }
      if (count($rfq_bidders6) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders6));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders6);
        }
      }
      if (count($bid_doc_bidders7) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($bid_doc_bidders7));
        } else {
          $custom_bidders = (array)json_decode($bid_doc_bidders7);
        }
      }
      if (count($rfq_bidders7) > 0) {
        if (count($custom_bidders) > 0) {
          $custom_bidders = array_merge($custom_bidders, (array)json_decode($rfq_bidders7));
        } else {
          $custom_bidders = (array)json_decode($rfq_bidders6);
        }
      }
    } else {
      if ($bid_doc_bidders != null && $rfq_bidders != null) {
        $custom_bidders = array_merge((array)json_decode($bid_doc_bidders), (array)json_decode($rfq_bidders));
      } else if ($bid_doc_bidders != null && $rfq_bidders == null) {
        $custom_bidders = (array)$bid_doc_bidders;
      } else if ($bid_doc_bidders == null && $rfq_bidders != null) {
        $custom_bidders = (array)$rfq_bidders;
      } else {
        $custom_bidders = [];
      }
    }

    if (count($custom_bidders) > 0) {
      $group_style = [
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
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFCC00']]
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

      $sub_total_rows = [];
      $group = null;
      $row = 11;
      $start_row = 12;
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/custom_bidders_report.xlsx");
      $worksheet = $spreadsheet->setActiveSheetIndexByName("Sheet1");
      $custom_bidders = $APP->sortObject($custom_bidders, array('open_bid' => 'asc', 'itb_arrangement' => 'asc', 'post_qual_start' => 'asc', 'post_qual_end' => 'asc'));
      foreach ($custom_bidders as $not_responsive_bidder) {
        if (in_array($not_responsive_bidder->init_id, $ids_array) == false) {
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

          if ($group != null && $not_responsive_bidder->open_bid != $group) {
            $worksheet->mergeCells("A" . $row . ":" . "B" . $row);
            $worksheet->getStyle("A" . $row . ":" . "Q" . $row)->applyFromArray($sub_total_style);
            $end_row = $row - 1;
            $worksheet->setCellValue("L" . $row, "=SUM(L" . $start_row . ":L" . $end_row . ")");
            $worksheet->setCellValue("M" . $row, "=SUM(M" . $start_row . ":M" . $end_row . ")");
            $worksheet->setCellValue("N" . $row, "=SUM(N" . $start_row . ":N" . $end_row . ")");
            $worksheet->setCellValue("A" . $row, "Subtotal:");
            $worksheet->getStyle("L" . $row)->applyFromArray($right_align);
            $worksheet->getStyle("M" . $row)->applyFromArray($right_align);
            $worksheet->getStyle("N" . $row)->applyFromArray($right_align);
            array_push($sub_total_rows, $row);
            $start_row = $row + 2;
            $row = $row + 1;
          }

          if ($not_responsive_bidder->open_bid != $group) {
            $group = $not_responsive_bidder->open_bid;
            $worksheet->mergeCells('A' . $row . ':' . 'Q' . $row);
            $worksheet->setCellValue('A' . $row, date('F d, Y', strtotime($not_responsive_bidder->open_bid)));
            $worksheet->getStyle('A' . $row)->applyFromArray($group_style);
            $row = $row + 1;
          }

          $worksheet->setCellValue("A" . $row, $count);
          $worksheet->setCellValue("B" . $row, htmlspecialchars(strtoupper(strtolower($project_number))));
          $worksheet->setCellValue("C" . $row, strtoupper(strtolower($title)));
          $worksheet->setCellValue("D" . $row, htmlspecialchars(strtoupper(strtolower($project_cost))));
          $worksheet->setCellValue("E" . $row, htmlspecialchars(strtoupper(strtolower($source_of_fund))));
          $worksheet->setCellValue("F" . $row, htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->mode))));
          if ($same_location === true) {
            if ($not_responsive_bidder->barangay_id != null) {
              $worksheet->setCellValue("G" . $row, htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->municipality_name))));
            } else {
              $worksheet->setCellValue("G" . $row, htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->barangay_name . ", " . $not_responsive_bidder->municipality_name))));
            }
          } else {
            $worksheet->setCellValue("G" . $row, htmlspecialchars(strtoupper(strtolower($not_responsive_bidder->municipality_name))));
          }

          $worksheet->setCellValue("H" . $row, strtoupper(strtolower($not_responsive_bidder->business_name)));
          $worksheet->setCellValue("I" . $row, strtoupper(strtolower($not_responsive_bidder->owner)) . " , " . strtoupper(strtolower($not_responsive_bidder->address)));
          if ($isZero === false && count($cluster_bids) > 1) {
            $worksheet->setCellValue("J" . $row, $detailed_bids);
          } else if ($not_responsive_bidder->bidders_bid == null) {
            $worksheet->setCellValue("J" . $row, "N/A");
          } else if ($not_responsive_bidder->final_minimum_cost != null) {
            $worksheet->setCellValue("J" . $row, "PHP" . number_format((float)$not_responsive_bidder->final_minimum_cost, 2, '.', ','));
          } else {
            $worksheet->setCellValue("J" . $row, "PHP" . number_format((float)$not_responsive_bidder->minimum_cost, 2, '.', ','));
          }
          $worksheet->setCellValue("K" . $row, date("F d,Y", strtotime($not_responsive_bidder->open_bid)));
          if ($not_responsive_bidder->bid_status === "responsive" || $not_responsive_bidder->bid_status === "non-responsive") {
            $worksheet->setCellValue("O" . $row, date("F d, Y", strtotime($not_responsive_bidder->post_qual_start)));
          } else {
            $worksheet->setCellValue("O" . $row, "N/A");
          }
          if ($not_responsive_bidder->bid_status === "responsive" || $not_responsive_bidder->bid_status === "non-responsive") {
            $worksheet->setCellValue("P" . $row, date("F d, Y", strtotime($not_responsive_bidder->post_qual_end)));
          } else {
            $worksheet->setCellValue("P" . $row, "N/A");
          }
          if ($not_responsive_bidder->bid_status == null) {
            $worksheet->setCellValue("Q" . $row, "Did Not Submit");
          } else {
            $worksheet->setCellValue("Q" . $row, $not_responsive_bidder->bid_status);
          }
          if ($not_responsive_bidder->procact_mode_id === 1) {
            if ($not_responsive_bidder->bid_status === "responsive") {
              $worksheet->setCellValue("L" . $row, (float)$not_responsive_bidder->fees);
            }
            if ($not_responsive_bidder->bid_status === "ongoing") {
              $worksheet->setCellValue("N" . $row, (float)$not_responsive_bidder->fees);
            } else {
              $worksheet->setCellValue("M" . $row, (float)$not_responsive_bidder->fees);
            }
          }
          $count = $count + 1;
          $row = $row + 1;
        }
      }
      // last sub total
      $worksheet->mergeCells("A" . $row . ":" . "B" . $row);
      $worksheet->getStyle("A" . $row . ":" . "Q" . $row)->applyFromArray($sub_total_style);
      $end_row = $row - 1;
      $worksheet->setCellValue("L" . $row, "=SUM(L" . $start_row . ":L" . ($row - 1) . ")");
      $worksheet->setCellValue("M" . $row, "=SUM(M" . $start_row . ":M" . ($row - 1) . ")");
      $worksheet->setCellValue("N" . $row, "=SUM(N" . $start_row . ":N" . ($row - 1) . ")");
      $worksheet->setCellValue("A" . $row, "Subtotal:");
      $worksheet->getStyle("L" . $row)->applyFromArray($right_align);
      $worksheet->getStyle("M" . $row)->applyFromArray($right_align);
      $worksheet->getStyle("N" . $row)->applyFromArray($right_align);
      array_push($sub_total_rows, $row);

      // total
      $row = $row + 1;
      $responsive_total_formula = "=";
      $not_responsive_formula = "=";
      $ongoing_total_formula = "=";
      foreach ($sub_total_rows as $sub_total_row) {
        if ($sub_total_row === $sub_total_rows[count($sub_total_rows) - 1]) {
          $responsive_total_formula = $responsive_total_formula . "L" . $sub_total_row;
          $not_responsive_formula = $not_responsive_formula . "M" . $sub_total_row;
          $ongoing_total_formula = $ongoing_total_formula . "N" . $sub_total_row;
        } else {
          $responsive_total_formula = $responsive_total_formula . "L" . $sub_total_row . "+";
          $not_responsive_formula = $not_responsive_formula . "M" . $sub_total_row . "+";
          $ongoing_total_formula = $ongoing_total_formula . "N" . $sub_total_row . "+";
        }
      }
      $worksheet->mergeCells("A" . $row . ":" . "B" . $row);
      $worksheet->getStyle("A" . $row . ":" . "Q" . $row)->applyFromArray($total_style);
      $worksheet->setCellValue("L" . $row, $responsive_total_formula);
      $worksheet->setCellValue("M" . $row, $not_responsive_formula);
      $worksheet->setCellValue("N" . $row, $ongoing_total_formula);
      $worksheet->setCellValue("A" . $row, "Total:");
      $worksheet->getStyle("L" . $row)->applyFromArray($right_align);
      $worksheet->getStyle("M" . $row)->applyFromArray($right_align);
      $worksheet->getStyle("N" . $row)->applyFromArray($right_align);
      $worksheet->getStyle("A11:" . "Q" . $row)->applyFromArray($borderedStyleArray);


      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save(public_path() . '\\' . "excel_templates/Custom Report-" . $date_start . "-" . $date_end . ".xlsx");
      return  response()->download(public_path() . '\\' . "excel_templates/Custom Report-" . $date_start . "-" . $date_end . ".xlsx")->deleteFileAfterSend(true);
    } else {
      return abort(403, 'No Specific Bidders data found on the selected dates');
    }
  }

  public function downloadAwarded($date_start, $date_end)
  {
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $date_start = $date_start;
    $date_end = $date_end;
    $ids_array = [];
    $APP = new APP;
    $count = 1;
    // $plans=DB::table('project_plans')
    // ->select('*','notice_of_awards.date_released AS noa_date_released','procacts.open_bid as bidding_date',DB::raw('DATE_FORMAT(notice_of_awards.date_released, "%Y-%m") AS month_group'))
    // ->whereRaw('notice_of_awards.date_released BETWEEN CAST( "'.$date_start.'" AS DATE) AND CAST( "'.$date_end.'" AS DATE) AND (resolutions.type="RRA" OR resolutions.type IS NULL)')
    // ->join('procacts','procacts.procact_id','project_plans.latest_procact_id')
    // ->join('project_timelines','project_timelines.procact_id','procacts.procact_id')
    // ->join('funds','project_plans.fund_id','funds.fund_id')
    // ->join('project_bidders','project_plans.project_bid_id','project_bidders.project_bid')
    // ->join('notice_of_awards','notice_of_awards.project_bid_id','project_bidders.project_bid')
    // ->leftJoin('barangays','barangays.barangay_id','project_plans.barangay_id')
    // ->join('municipalities','project_plans.municipality_id','municipalities.municipality_id')
    // ->leftJoin('resolution_projects','procacts.procact_id','resolution_projects.procact_id')
    // ->leftJoin('resolutions','resolutions.resolution_id','resolution_projects.resolution_id')
    // ->orderBy('month_group','asc')
    // ->orderBy('procacts.open_bid','asc')
    // ->orderBy('procacts.itb_arrangement','asc')
    // ->get();
    $plans = DB::table('project_plans')
      ->select('*', 'resolutions.resolution_date AS noa_date_released', 'procacts.open_bid as bidding_date', DB::raw('DATE_FORMAT(resolutions.resolution_date, "%Y-%m") AS month_group'))
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->join('project_bidders', 'project_plans.project_bid_id', 'project_bidders.project_bid')
      ->leftJoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
      ->leftJoin('contracts', 'project_bidders.project_bid', 'contracts.project_bid_id')
      ->leftJoin('chsp', 'project_bidders.project_bid', 'chsp.chsp_project_bid')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->join('resolution_projects', 'procacts.procact_id', 'resolution_projects.procact_id')
      ->join('resolutions', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
      ->whereRaw('resolutions.resolution_date BETWEEN CAST( "' . $date_start . '" AS DATE) AND CAST( "' . $date_end . '" AS DATE) AND resolutions.type="RRA"')
      ->orderBy('month_group', 'asc')
      ->orderBy('procacts.open_bid', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();

    if (count($plans) > 0) {

      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/CWB.xlsx");
      $worksheet = $spreadsheet->setActiveSheetIndexByName("Sheet1");
      $grouping = null;
      $sub_total_rows = [];
      $row = 12;
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
      $start_row = 12;
      foreach ($plans as $plan) {
        if (in_array($plan->plan_id, $ids_array) == false) {
          if ($plan->month_group != $grouping) {

            // Sub Total
            if ($grouping != null) {
              $worksheet->getStyle("A" . $row . ":" . "L" . $row)->applyFromArray($sub_total_style);
              $end_row = $row - 1;
              $worksheet->setCellValue("D" . $row, "=SUM(D" . $start_row . ":D" . $end_row . ")");
              $worksheet->setCellValue("E" . $row, "=SUM(D" . $start_row . ":D" . $end_row . ")");
              $worksheet->setCellValue("I" . $row, "=SUM(I" . $start_row . ":I" . $end_row . ")");
              $worksheet->setCellValue("J" . $row, "=SUM(I" . $start_row . ":I" . $end_row . ")");
              $worksheet->setCellValue("C" . $row, "Subtotal:");
              $worksheet->setCellValue("H" . $row, "Subtotal:");
              $worksheet->getStyle("C" . $row)->applyFromArray($right_align);
              $worksheet->getStyle("H" . $row)->applyFromArray($right_align);
              array_push($sub_total_rows, $row);
              $start_row = $row + 2;
              $row = $row + 1;
            } else {
              $start_row = 12;
            }

            $worksheet->mergeCells('A' . $row . ':' . 'L' . $row);
            $worksheet->getCell('A' . $row)->setValue(date("F Y", strtotime($plan->noa_date_released)));
            $worksheet->getStyle("A" . $row)->applyFromArray($month_style);
            $row = $row + 1;
            $grouping = $plan->month_group;
          }
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
            $project_cost = $project_cost . "= PHP " . number_format((float)$total, 2, '.', ',');
          } else {
            $title = $plan->project_title;
            $project_cost = "PHP " . number_format((float)$plan->project_cost, 2, '.', ',');
            $project_number = $plan->project_no;
            $source_of_fund = $plan->source;
            $duration = $plan->duration;
            $total = $plan->project_cost;
          }

          $winner = $APP->getBiddersData($plan->latest_procact_id, 'responsive');

          $detailed_bids = "";
          $isZero = false;
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

          $project_no = htmlspecialchars(strtoupper(strtolower($project_number)));
          // $project_title=str_replace("&amp;AMP;","&amp;",htmlspecialchars(strtoupper(strtolower($title))));
          $project_title = str_replace("&amp;", "&", htmlspecialchars(strtoupper(strtolower($title))));
          $project_cost = $project_cost;
          $total_project_cost = $total;
          $source_of_fund = htmlspecialchars(strtoupper(strtolower($source_of_fund)));
          if ($same_location === true) {
            if ($plan->barangay_id != null) {
              $location = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
            } else {
              $location = htmlspecialchars(strtoupper(strtolower($plan->barangay_name . ", " . $plan->municipality_name)));
            }
          } else {
            $location = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
          }

          $winning_bidder = str_replace("&amp;", "&", htmlspecialchars(strtoupper(strtolower($winner[0]->business_name))));
          $name_address = str_replace("&amp;", "&", htmlspecialchars(strtoupper(strtolower($winner[0]->owner)))) . " , " . $winner[0]->address;

          if ($isZero === false && count($cluster_bids) > 1) {
            $bid_amount = $detailed_bids;
          } else {
            $bid_amount = "PHP " . number_format((float)$winner[0]->final_minimum_cost, 2, '.', ',');
          }
          $total_bid = $winner[0]->final_minimum_cost;
          $bidding_date = date("F d,Y", strtotime($plan->bidding_date));
          $group = date("F Y", strtotime($plan->noa_date_released));
          $duration = $duration;
          $worksheet->getCell('A' . $row)->setValue($count);
          $worksheet->getCell('B' . $row)->setValue($project_no);
          $worksheet->getCell('C' . $row)->setValue($project_title);
          $worksheet->getCell('D' . $row)->setValue($total);
          $worksheet->getCell('E' . $row)->setValue($project_cost);
          $worksheet->getCell('F' . $row)->setValue($location);
          $worksheet->getCell('G' . $row)->setValue($winning_bidder);
          $worksheet->getCell('H' . $row)->setValue($name_address);
          $worksheet->getCell('I' . $row)->setValue($total_bid);
          $worksheet->getCell('J' . $row)->setValue($bid_amount);
          $worksheet->getCell('K' . $row)->setValue($bidding_date);
          $worksheet->getCell('L' . $row)->setValue($duration . " CD");
          $count = $count + 1;
          $row = $row + 1;
        }
      }

      // last sub total and total
      $worksheet->getStyle("A" . $row . ":" . "L" . $row)->applyFromArray($sub_total_style);
      $end_row = $row - 1;
      $worksheet->setCellValue("D" . $row, "=SUM(D" . $start_row . ":D" . $end_row . ")");
      $worksheet->setCellValue("E" . $row, "=SUM(D" . $start_row . ":D" . $end_row . ")");
      $worksheet->setCellValue("I" . $row, "=SUM(I" . $start_row . ":I" . $end_row . ")");
      $worksheet->setCellValue("J" . $row, "=SUM(I" . $start_row . ":I" . $end_row . ")");
      $worksheet->setCellValue("C" . $row, "Subtotal:");
      $worksheet->setCellValue("H" . $row, "Subtotal:");
      $worksheet->getStyle("C" . $row)->applyFromArray($right_align);
      $worksheet->getStyle("H" . $row)->applyFromArray($right_align);
      array_push($sub_total_rows, $row);
      $row = $row + 1;
      $abc_total_formula = "=";
      $bid_total_formula = "=";
      foreach ($sub_total_rows as $sub_total_row) {
        if ($sub_total_row === $sub_total_rows[count($sub_total_rows) - 1]) {
          $abc_total_formula = $abc_total_formula . "D" . $sub_total_row;
          $bid_total_formula = $bid_total_formula . "I" . $sub_total_row;
        } else {
          $abc_total_formula = $abc_total_formula . "D" . $sub_total_row . "+";
          $bid_total_formula = $bid_total_formula . "I" . $sub_total_row . "+";
        }
      }

      $worksheet->getStyle("A" . $row . ":" . "L" . $row)->applyFromArray($total_style);
      $worksheet->setCellValue("D" . $row, $abc_total_formula);
      $worksheet->setCellValue("E" . $row, $abc_total_formula);
      $worksheet->setCellValue("I" . $row, $bid_total_formula);
      $worksheet->setCellValue("J" . $row, $bid_total_formula);
      $worksheet->setCellValue("C" . $row, "Total:");
      $worksheet->setCellValue("H" . $row, "Total:");
      $worksheet->getStyle("C" . $row)->applyFromArray($right_align);
      $worksheet->getStyle("H" . $row)->applyFromArray($right_align);
      $worksheet->getStyle("A12:" . "L" . $row)->applyFromArray($borderedStyleArray);




      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save(public_path() . '\\' . "excel_templates/AwardedProjects-" . $date_start . "-" . $date_end . ".xlsx");
      return  response()->download(public_path() . '\\' . "excel_templates/AwardedProjects-" . $date_start . "-" . $date_end . ".xlsx")->deleteFileAfterSend(true);
    } else {
      return abort(403, 'No Projects  Were Awarded Selected Dates');
    }
  }


  public function submitGenerateChecklist(Request $request)
  {
    $data = $request->validate([
      "date_opened" => 'required'
    ]);

    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $desired_plan_array = [];
    $desired_plan_format = ["number" => null, "project_title" => null, "location" => null, "rows" => null, "bidders" => null];
    $date_opened = date("Y-m-d", strtotime($request->input('date_opened')));
    $ids_array = [];
    $APP = new APP;
    $plans = DB::table('project_plans')
      ->where('project_timelines.bid_submission_start', $date_opened)
      ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->orderBy('project_plans.mode_id', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();



    if (count($plans) > 0) {
      $count = 1;
      $initial_mode = 0;
      foreach ($plans as $plan) {
        $same_location = true;
        $initial_barangay = $plan->barangay_id;
        if ($initial_barangay === null) {
          $same_location = false;
        }
        if ($plan->mode_id != $initial_mode) {
          $initial_mode = $plan->mode_id;
          if ($initial_mode == 1) {
            $group = "PUBLIC BIDDING";
            $rank_label = "LCB";
          } else if ($initial_mode == 2) {
            $group = "SMALL VALUE PROCUREMENT";
            $rank_label = "LCPQ";
          } else if ($initial_mode == 3) {
            $group = "NEGOTIATED PROCUREMENT";
            $rank_label = "LCPQ";
          } else {
            $group = "";
          }
          $count = 1;
        }
        $temp_plan = $desired_plan_format;
        if (in_array($plan->plan_id, $ids_array) == false) {
          $title = $plan->project_title;
          array_push($ids_array, $plan->plan_id);
          $rank = 1;
          $title = $plan->project_title;
          $project_cost = $plan->project_cost;
          $total = $plan->project_cost;

          // Get cluster and append titles
          if ($plan->plan_cluster_id != null) {
            $letter = 'A';
            $total = 0;
            $title = "";
            $source_of_fund = "";
            $project_number = "";
            $project_cost = "";



            $clusters = DB::table('project_plans')
              ->where([['project_timelines.bid_submission_start', $date_opened], ['procacts.plan_cluster_id', $plan->plan_cluster_id]])
              ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
              ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
              ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
              ->orderBy('procacts.itb_arrangement', 'asc')
              ->get();

            foreach ($clusters as $cluster) {
              if ($initial_barangay != $cluster->barangay_id || $initial_barangay == null) {
                $same_location = false;
              }
              array_push($ids_array, $cluster->plan_id);
              $temp = $letter . '. ' . htmlspecialchars($cluster->project_title) . ";";
              $temp_source = $letter . '. ' . htmlspecialchars($cluster->source) . ";";
              $temp_project_number = $letter . '. ' . htmlspecialchars($cluster->project_no) . ";";
              $title = $title . "   " . $temp;
              $source_of_fund = $source_of_fund . "   " . $temp_source;
              $project_number = $project_number . "   " . $temp_project_number;
              // $temp2=$letter.'. '.$cluster->project_cost;
              $total = $total + $cluster->project_cost;
              $project_cost = $project_cost . "PHP" . number_format((float)$cluster->project_cost, 2, '.', ',') . " + ";
              $letter = ++$letter;
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
          }


          // get all bidders
          $bidders = $APP->getAllTakers($plan->procact_id);

          // bidder_with_ranks
          $bidder_ranks = [];
          $rank = 1;
          foreach ($bidders as $bidder) {
            $temp_bidder = (array) $bidder;

            if ($bidder->bid_status == null) {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "DNS"));
            } else if ($bidder->bid_status == "late") {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "Late Submission"));
            } else {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "Submitted"));
            }
            array_push($bidder_ranks, (object) $temp_bidder);
          }

          // $bidders=$APP->sortObject($bidder_ranks,array('date_released' => 'asc', 'main_id' => 'asc'));
          $bidders = $APP->sortObject($bidder_ranks, array('date_released' => 'asc', 'date_received' => 'asc'));
          $rows = count($bidders);
          if ($rows === 0) {
            $rows = 1;
          }
          $temp_plan["number"] = $count;
          $temp_plan["bidders"] = (array)$bidders;
          $temp_plan["bidder_count"] = count($bidders);
          $temp_plan["rows"] = $rows;
          $temp_plan["project_title"] = strtoupper(strtolower($title));
          $temp_plan["project_cost"] = $project_cost;
          $temp_plan["group"] = $group;
          $temp_plan["mode_id"] = $initial_mode;
          $temp_plan["project_no"] = htmlspecialchars(strtoupper(strtolower($project_number)));
          $temp_plan["source_of_fund"] = htmlspecialchars(strtoupper(strtolower($source_of_fund)));

          if ($same_location === true) {
            if ($plan->barangay_id != null) {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
            } else {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->barangay_name . ", " . $plan->municipality_name)));
            }
          } else {
            $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
          }
          $count = $count + 1;
          array_push($desired_plan_array, (object) $temp_plan);
        }
      }
      return back()->withInput()->with("project_plans", (object)$desired_plan_array);
    } else {
      return abort(403, 'No Projects Opened on Selected Date');
    }
  }

  public function downloadChecklist($opening_date)
  {
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $desired_plan_array = [];
    $desired_plan_format = ["number" => null, "total_abc" => null, "opening_date" => null, "duration" => null, "project_title" => null, "location" => null, "rows" => null, "bidders" => null];
    $date_opened = $opening_date;
    $ids_array = [];
    $APP = new APP;
    $bid_cnt = 0;
    $svp_cnt = 0;
    $negotiated_cnt = 0;

    $plans = DB::table('project_plans')
      ->where('project_timelines.bid_submission_start', $date_opened)
      ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->orderBy('project_plans.mode_id', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();



    if (count($plans) > 0) {
      $count = 1;
      foreach ($plans as $plan) {
        $same_location = true;
        $initial_barangay = $plan->barangay_id;
        if ($initial_barangay === null) {
          $same_location = false;
        }

        $temp_plan = $desired_plan_format;

        if (in_array($plan->plan_id, $ids_array) == false) {
          $title = $plan->project_title;
          array_push($ids_array, $plan->plan_id);
          $rank = 1;
          $title = $plan->project_title;
          $project_cost = $plan->project_cost;
          $total = $plan->project_cost;

          if ($plan->mode_id == 1) {
            $bid_cnt = $bid_cnt + 1;
          } else if ($plan->mode_id == 2) {
            $svp_cnt = $svp_cnt + 1;
          } else if ($plan->mode_id == 3) {
            $negotiated_cnt = $negotiated_cnt + 1;
          } else {
          }

          // Get cluster and append titles
          if ($plan->plan_cluster_id != null) {
            $letter = 'A';
            $total = 0;
            $title = "";
            $source_of_fund = "";
            $project_number = "";
            $project_cost = "";
            $duration = 0.00;
            $temp_duration = 0.00;
            $opening_date = $plan->open_bid;

            $clusters = DB::table('project_plans')
              ->where([['project_timelines.bid_submission_start', $date_opened], ['procacts.plan_cluster_id', $plan->plan_cluster_id]])
              ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
              ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
              ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
              ->orderBy('procacts.itb_arrangement', 'asc')
              ->get();

            foreach ($clusters as $cluster) {
              if ($initial_barangay != $cluster->barangay_id || $initial_barangay == null) {
                $same_location = false;
              }
              if ($cluster->duration != $temp_duration) {
                $duration = $duration + $cluster->duration;
              }
              $temp_duration = $cluster->duration;
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
              if ($cluster->special_case_1 == 1) {
                $title = $cluster->project_title;
              }
            }
            $project_cost = rtrim($project_cost, ' + ');
            $project_cost = $project_cost . "= PHP" . number_format((float)$total, 2, '.', ',');
          } else {
            $title = $plan->project_title;
            $project_cost = "PHP" . number_format((float)$plan->project_cost, 2, '.', ',');
            $total = $plan->project_cost;
            $project_number = $plan->project_no;
            $source_of_fund = $plan->source;
            $duration = $plan->duration;
            $opening_date = $plan->open_bid;
          }


          // get all bidders
          $bidders = $APP->getAllTakers($plan->procact_id);


          // bidder_with_ranks
          $bidder_ranks = [];
          $rank = 1;
          foreach ($bidders as $bidder) {
            $temp_bidder = (array) $bidder;

            if ($bidder->bid_status == null) {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "DNS"));
            } else if ($bidder->bid_status == "late") {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "Late Submission"));
            } else {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => ""));
            }
            array_push($bidder_ranks, (object) $temp_bidder);
          }



          $bidders = $APP->sortObject($bidder_ranks, array('date_released' => 'asc', 'date_received' => 'asc'));


          $rows = count($bidders);
          if ($rows === 0) {
            $rows = 1;
          }

          $temp_plan["number"] = $count;
          $temp_plan["bidders"] = $bidders;
          $temp_plan["bidder_count"] = count($bidders);
          $temp_plan["rows"] = $rows;
          $temp_plan["project_title"] = strtoupper(strtolower($title));
          $temp_plan["project_cost"] = $project_cost;
          $temp_plan["total_abc"] = $total;
          $temp_plan["mode_id"] = $plan->mode_id;
          $temp_plan["duration"] = $duration;
          $temp_plan["project_no"] = strtoupper(strtolower($project_number));
          $temp_plan["source_of_fund"] = strtoupper(strtolower($source_of_fund));
          $temp_plan["total"] = $total;
          $temp_plan["opening_date"] = date("F d,Y", strtotime($opening_date));

          if ($same_location === true) {
            if ($plan->barangay_id != null) {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
            } else {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->barangay_name . ", " . $plan->municipality_name)));
            }
          } else {
            $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
          }
          $count = $count + 1;
          array_push($desired_plan_array, (object) $temp_plan);
        }
      }
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/checklist.xlsx");

      $municipalityStyleArray = [
        'font' => ['bold'  =>  true, 'size'  =>  15, 'name'  =>  'Arial', 'color' => array('rgb' => 'FF0000'),],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]
      ];
      $borderedStyleArray = [
        'font' => ['bold'  =>  true, 'size'  =>  14, 'name'  =>  'Arial Narrow', 'color' => array('rgb' => '000000')],
        'borders' => [
          'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
          ],
        ],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP],
      ];

      $totalStyleArray = [
        'font' => ['bold'  =>  true, 'size'  =>  14, 'name'  =>  'Arial Narrow', 'color' => array('rgb' => '000000')]
      ];

      $centerStyleArray = [
        'font' => ['bold'  =>  true, 'size'  =>  14, 'name'  =>  'Arial Narrow', 'color' => array('rgb' => '000000')],
        'borders' => [
          'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
          ],
        ],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
      ];

      $align_center = ['alignment' => array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
      )];

      $align_left = ['alignment' => array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
      )];




      if ($bid_cnt > 0) {
        $item = 1;
        foreach ($desired_plan_array as $project_plan) {
          if ($project_plan->mode_id === 1) {
            $bidder_order = 1;
            $letter = "A";
            $starting_row = 7;
            $clonedWorksheet = clone $spreadsheet->getSheetByName('bidding-checklist');
            $split_array = explode("=", $project_plan->project_cost);
            $individual_costs = explode("+", $split_array[0]);
            $sheetName = $item . '. ' . $project_plan->location;
            $clonedWorksheet->setTitle($sheetName);
            $spreadsheet->addSheet($clonedWorksheet);
            $worksheet = $spreadsheet->setActiveSheetIndexByName($sheetName);
            $worksheet->getCell('A4')->setValue($item . ". )");
            $worksheet->getCell('D4')->setValue($project_plan->project_no . " ");
            $worksheet->getCell('D5')->setValue($project_plan->project_title);
            $worksheet->getCell('D6')->setValue($project_plan->location . ", BENGUET");
            $worksheet->getCell('D7')->setValue($project_plan->total);
            $worksheet->getCell('D8')->setValue($project_plan->source_of_fund);
            foreach ($individual_costs as $cost) {
              if ($starting_row > 7) {
                $worksheet->insertNewRowBefore($starting_row + 1, 1);
              }
              $cost = str_replace("PHP", "", $cost);
              $cost = str_replace(",", "", $cost);
              $worksheet->getCell('I' . $starting_row)->setValue((float)$cost);
              $starting_row = $starting_row + 1;
              $letter = ++$letter;
            }
            // Bidders
            if (count($project_plan->bidders) > 3) {
              $worksheet->insertNewColumnBefore("H", count($project_plan->bidders) - 1);
              $cmp = count($project_plan->bidders) - 1;
              $column_fix_start = "H";
              for ($i = 0; $i < $cmp; $i++) {
                $worksheet->getColumnDimension($column_fix_start)->setWidth(25);
                $column_fix_start = ++$column_fix_start;
              }
            }

            if (count($project_plan->bidders) === 0) {
              $worksheet->mergeCells('G' . ($starting_row + 3) . ':' . 'I' . ($starting_row + 4));
              $worksheet->getCell("G" . ($starting_row + 3))->setValue("No Bidders");
              $worksheet->getStyle("G" . ($starting_row + 3))->applyFromArray($centerStyleArray);
              $worksheet->getRowDimension($starting_row + 3)->setRowHeight(12);
            } else {
              $column = "G";
              $bidders = $project_plan->bidders;
              foreach ($bidders as $key => $bidder) {
                $worksheet->getCell($column . ($starting_row + 2))->setValue($bidder_order);
                $worksheet->getCell($column . ($starting_row + 3))->setValue(strtoupper(strtolower($bidder->business_name)));
                $worksheet->getStyle($column . ($starting_row + 28))->getNumberFormat()->setFormatCode('#,##0.00');
                $worksheet->getStyle($column . ($starting_row + 29))->getNumberFormat()->setFormatCode('#,##0.00');
                $worksheet->setCellValue($column . ($starting_row + 38), '=IF(' . $column . ($starting_row + 38) . '="-","-",IF(' . $column . ($starting_row + 41) . '="Please Input Amount Here",0,' . $column . ($starting_row + 38) . '-' . $column . ($starting_row + 41) . '))');
                $worksheet->setCellValue($column . ($starting_row + 37), '=IF(' . $column . ($starting_row + 39) . '="NO DISCOUNT",0.00,IF(' . $column . ($starting_row + 39) . '="ABC",D7*' . $column . ($starting_row + 40) . ',IF(' . $column . ($starting_row + 39) . '="PROPOSED BID",' . $column . ($starting_row + 38) . "*" . $column . ($starting_row + 40) . ',"Please Input Amount Here")))');
                $worksheet->setCellValue($column . ($starting_row + 29), '=IF(' . $column . ($starting_row + 28) . '>0,' . $column . ($starting_row + 28) . '," ")');
                $validation = $worksheet->getCell($column . ($starting_row + 35))->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(false);
                $validation->setShowErrorMessage(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"NO DISCOUNT,ABC,PROPOSED BID,AMOUNT"');
                $worksheet->setCellValue($column . ($starting_row + 35), "NO DISCOUNT");
                if ($bidder->remarks_status != "") {
                  $worksheet->getCell($column . ($starting_row + 4))->setValue("DNS");
                }
                $bidder_order = $bidder_order + 1;
                $column = ++$column;
              }
            }
            $item = $item + 1;
          }
        }
      } else {
        $sheetIndex = $spreadsheet->getIndex($spreadsheet->setActiveSheetIndexByName('bidding-checklist'));
        $spreadsheet->removeSheetByIndex($sheetIndex);
      }
      if ($svp_cnt > 0) {
        $item = 1;
        $row = 9;
        $worksheet = $spreadsheet->setActiveSheetIndexByName('svp-checklist');
        $worksheet->getCell('G6')->setValue(date("F j, Y", strtotime($date_opened)));
        foreach ($desired_plan_array as $project_plan) {
          if ($project_plan->mode_id === 2) {
            $worksheet->getCell('B' . $row)->setValue($project_plan->location);
            $worksheet->getStyle('B' . $row)->applyFromArray($municipalityStyleArray);

            $row = $row + 1;
            $start_row = $row;
            $worksheet->getCell('A' . $row)->setValue($item);
            $worksheet->getCell('B' . $row)->setValue("PROJECT TITLE");
            $worksheet->getCell('C' . $row)->setValue($project_plan->project_title);
            $worksheet->mergeCells('C' . $row . ':' . 'J' . $row);


            $row = $row + 1;
            $worksheet->getCell('B' . $row)->setValue("LOCATION");
            $worksheet->mergeCells('C' . $row . ':' . 'J' . $row);
            $worksheet->getCell('C' . $row)->setValue($project_plan->location);

            $row = $row + 1;
            $worksheet->getCell('B' . $row)->setValue("ABC");
            $worksheet->mergeCells('C' . $row . ':' . 'J' . $row);
            $worksheet->getCell('C' . $row)->setValue($project_plan->project_cost);

            $row = $row + 1;
            $worksheet->getCell('B' . $row)->setValue("SOURCE OF FUND");
            $worksheet->mergeCells('C' . $row . ':' . 'J' . $row);
            $worksheet->getCell('C' . $row)->setValue($project_plan->source_of_fund);

            $row = $row + 1;
            $worksheet->getCell('B' . $row)->setValue("PROJECT NUMBER");
            $worksheet->mergeCells('C' . $row . ':' . 'J' . $row);
            $worksheet->getCell('C' . $row)->setValue($project_plan->project_no);

            $row = $row + 1;
            $worksheet->mergeCells('A' . $row . ':' . 'B' . ($row + 1));
            $worksheet->mergeCells('C' . $row . ':' . 'J' . $row);
            $worksheet->getCell('C' . $row)->setValue("DOCUMENTS REQUIRED");
            $worksheet->getCell('A' . $row)->setValue("Invited Contractors & Interested Contractors");

            $row = $row + 1;
            $worksheet->getCell('C' . $row)->setValue("Price Quotations");
            $worksheet->getCell('D' . $row)->setValue("Detailed Estimates");
            $worksheet->getCell('E' . $row)->setValue("Platinum PhilGEPs membership/ Mayor’s Permit & Philgeps Reg. No.");
            // $worksheet->getCell('F' . $row)->setValue("PCAB License");
            $worksheet->getCell('F' . $row)->setValue("Omnibus Sworn Statement");
            // $worksheet->getCell('H' . $row)->setValue("Mayor's Permit");
            $worksheet->mergeCells('G' . $row . ':' . 'I' . $row);
            $worksheet->getCell('G' . $row)->setValue("Latest Income & Business Tax Returns (Electronically Filed)");

            $row = $row + 1;
            $bidders = (array)$project_plan->bidders;
            if (count($bidders) === 0) {
              $worksheet->mergeCells('A' . $row . ':' . 'G' . $row);
              $worksheet->getCell('A' . $row)->setValue("No Contractors");
              $row = $row + 1;
            } else {
              foreach ($bidders as $bidder) {

                $worksheet->mergeCells('G' . $row . ':' . 'I' . $row);
                $worksheet->mergeCells('A' . $row . ':' . 'B' . $row);
                $worksheet->getCell('A' . $row)->setValue(strtoupper(strtolower($bidder->business_name)));
                $currencyFormat = '_(#,##0.00_);_((#,##0.00);_("-"??_);_(@_)';
                $worksheet->getStyle('D' . $row)->applyFromArray($align_center);
                $worksheet->getStyle('E' . $row)->applyFromArray($align_center);
                $worksheet->getStyle('F' . $row)->applyFromArray($align_center);
                $worksheet->getStyle('G' . $row)->applyFromArray($align_center);
                $worksheet->getStyle('I' . $row)->applyFromArray($align_center);
                $worksheet->getStyle('H' . $row)->applyFromArray($align_center);
                $worksheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $worksheet->getCell('C' . $row)->setValue($bidder->remarks_status);
                $row = $row + 1;
              }
            }

            $end_row = $row - 1;
            $worksheet->getStyle('A' . $start_row . ':I' . $end_row)->applyFromArray($borderedStyleArray);
            $worksheet->getStyle('A' . $start_row . ':I' . $end_row)->getAlignment()->setWrapText(true);

            $row = $row + 2;
            $item = $item + 1;
          }
        }
      } else {
        $sheetIndex = $spreadsheet->getIndex($spreadsheet->setActiveSheetIndexByName('svp-checklist'));
        $spreadsheet->removeSheetByIndex($sheetIndex);
      }

      if ($negotiated_cnt > 0) {
        $item = 1;

        foreach ($desired_plan_array as $project_plan) {
          if ($project_plan->mode_id === 3) {
            $bidder_order = 1;
            $letter = "A";
            $row = 16;
            $clonedWorksheet = clone $spreadsheet->getSheetByName('negotiated-checklist');
            $split_array = explode("=", $project_plan->project_cost);
            $individual_costs = explode("+", $split_array[0]);
            $sheetName = $item . '.NEGOTIATED-' . $project_plan->location;
            $clonedWorksheet->setTitle($sheetName);
            $spreadsheet->addSheet($clonedWorksheet);
            $worksheet = $spreadsheet->setActiveSheetIndexByName($sheetName);
            $worksheet->getCell('C6')->setValue($project_plan->project_title);
            $worksheet->getCell('C8')->setValue($project_plan->project_cost);
            $worksheet->getCell('C9')->setValue($project_plan->project_no);
            $worksheet->getCell('C10')->setValue($project_plan->location . ", BENGUET");
            $worksheet->getCell('C11')->setValue($project_plan->source_of_fund);
            $worksheet->getCell('C12')->setValue($project_plan->duration);
            $worksheet->getCell('F7')->setValue($project_plan->opening_date);
            $worksheet->getStyle("C6:C12")->applyFromArray($align_left);
            if ($project_plan->total_abc > 1000000) {
              $worksheet->getCell('A14')->setValue("Negotiated Procurement (NP)-TWO FAILED BIDDING");
            } else {
              $worksheet->getCell('A14')->setValue("Negotiated Procurement (NP)-SMALL VALUE PROCUREMENT");
            }

            $end_column = ord("G");
            $starting_column = ord("D");
            if (count($project_plan->bidders) > 0) {
              if (count($project_plan->bidders) > 4) {

                $worksheet->insertNewRowBefore($row + 1, count($project_plan->bidders) - 4);
                $worksheet->insertNewColumnBefore("D", count($project_plan->bidders) - 4);
                $end_column = $end_column + count($project_plan->bidders) - 4;
                $requirements_row = $row + count($project_plan->bidders);
                $financial_docs = $requirements_row + 16;
              } else {
                $requirements_row = $row + 4;
                $financial_docs = $requirements_row + 16;
              }


              // participating dealers
              foreach ($project_plan->bidders as $bidder) {
                $worksheet->mergeCells('B' . $row . ':' . chr($end_column) . $row);
                $worksheet->getCell('A' . $row)->setValue($bidder_order);
                $worksheet->getCell('B' . $row)->setValue(str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($bidder->business_name)))));
                $worksheet->getColumnDimension(chr($starting_column))->setWidth(25);
                $worksheet->getCell(chr($starting_column) . $requirements_row)->setValue($bidder_order);
                $worksheet->getCell(chr($starting_column) . $financial_docs)->setValue($bidder_order);
                $starting_column = $starting_column + 1;
                $row++;
                $bidder_order++;
              }
              $worksheet->getStyle('A16:' . 'A' . ($row - 1))->applyFromArray($align_center);
              $worksheet->getStyle('B16:' . 'B' . ($row - 1))->applyFromArray($align_left);
              $worksheet->getStyle('D' . $requirements_row . ':' . chr($starting_column - 1) . ($financial_docs + 1))->applyFromArray($align_center);
            }
            $item = $item + 1;
          } else {
          }
        }
      } else {
        $sheetIndex = $spreadsheet->getIndex($spreadsheet->setActiveSheetIndexByName('negotiated-checklist'));
        $spreadsheet->removeSheetByIndex($sheetIndex);
      }
      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save(public_path() . '\\' . "excel_templates/" . $date_opened . "-Checklist.xlsx");
      return  response()->download(public_path() . '\\' . "excel_templates/" . $date_opened . "-Checklist.xlsx")->deleteFileAfterSend(true);
    }
  }

  public function generateAbstract()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("admin.generate_abstract", ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function submitGenerateAbstract(Request $request)
  {
    $data = $request->validate([
      "date_opened" => 'required'
    ]);

    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $desired_plan_array = [];
    $desired_plan_format = ["number" => null, "project_title" => null, "location" => null, "rows" => null, "bidders" => null];
    $date_opened = date("Y-m-d", strtotime($request->input('date_opened')));
    $ids_array = [];
    $APP = new APP;
    $plans = DB::table('project_plans')
      ->where('project_timelines.bid_submission_start', $date_opened)
      ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->orderBy('project_plans.mode_id', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();

    if (count($plans) > 0) {
      $count = 1;
      $initial_mode = 0;
      foreach ($plans as $plan) {
        $same_location = true;
        $initial_barangay = $plan->barangay_id;
        if ($initial_barangay === null) {
          $same_location = false;
        }
        if ($plan->mode_id != $initial_mode) {
          $initial_mode = $plan->mode_id;
          if ($initial_mode == 1) {
            $group = "PUBLIC BIDDING";
            $rank_label = "LCB";
          } else if ($initial_mode == 2) {
            $group = "SMALL VALUE PROCUREMENT";
            $rank_label = "LCPQ";
          } else if ($initial_mode == 3) {
            $group = "NEGOTIATED PROCUREMENT";
            $rank_label = "LCPQ";
          } else {
            $group = "";
          }
          $count = 1;
        }
        $temp_plan = $desired_plan_format;
        if (in_array($plan->plan_id, $ids_array) == false) {
          $title = $plan->project_title;
          array_push($ids_array, $plan->plan_id);
          $rank = 1;
          $title = $plan->project_title;
          $project_cost = $plan->project_cost;
          $total = $plan->project_cost;

          // Get cluster and append titles
          if ($plan->plan_cluster_id != null) {
            $letter = 'A';
            $total = 0;
            $title = "";
            $source_of_fund = "";
            $project_number = "";
            $project_cost = "";
            $clusters = DB::table('project_plans')
              ->where([['project_timelines.bid_submission_start', $date_opened], ['procacts.plan_cluster_id', $plan->plan_cluster_id]])
              ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
              ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
              ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
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
          }


          // get all bidders
          $bidders = $APP->getAllTakers($plan->procact_id);


          $rank = 1;
          $temp_bidders = [];
          foreach ($bidders as $key => $bidder) {
            $temp_bidder = (array)$bidder;

            if ($bidder->bid_status == null) {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "DNS"));
            } else if ($bidder->bid_status == "late") {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "Late Submission"));
            } else {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "Submitted"));
            }

            if ($bidder->bid_status == null) {
              $temp_bidder["rank"] = "";
            } elseif ($bidder->bid_status == "disqualified") {
              if ($bidder->bid_as_evaluated != null) {
                $temp_bidder["proposed_bid"] = "PHP " . number_format($bidder->proposed_bid, 2, '.', ',');
                $temp_bidder["bid_as_evaluated"] = "PHP " . number_format($bidder->bid_as_evaluated, 2, '.', ',');
              } else {
                $temp_bidder["proposed_bid"] = "";
                $temp_bidder["bid_as_evaluated"] = "";
              }
              $disqualification = DB::table('disqualification_records')->where([['project_bid', $bidder->project_bid], ['remarks', 'like', 'Disqualified:%']])->first();
              if ($disqualification != null) {
                $temp_bidder["rank"] = $disqualification->remarks;
              } else {
                $temp_bidder["rank"] = "";
              }
            } elseif ($bidder->bid_status == "ineligible") {
              if ($bidder->bid_as_evaluated != null) {
                $temp_bidder["proposed_bid"] = "PHP " . number_format($bidder->proposed_bid, 2, '.', ',');
                $temp_bidder["bid_as_evaluated"] = "PHP " . number_format($bidder->bid_as_evaluated, 2, '.', ',');
              } else {
                $temp_bidder["proposed_bid"] = "";
                $temp_bidder["bid_as_evaluated"] = "";
              }
              $disqualification = DB::table('disqualification_records')->where([['project_bid', $bidder->project_bid], ['remarks', 'like', 'Ineligible:%']])->first();
              $temp_bidder["rank"] = $disqualification->remarks;
            } else {
              if ($plan->main_status === "deferred") {
                $temp_bidder["proposed_bid"] = null;
                $temp_bidder["bid_as_evaluated"] = null;
                $temp_bidder["rank"] = 'Deferred Opening';
              } else {

                $active_bidder = $APP->getBidEvalBiddersData($plan->procact_id, 'responsive,active,non-responsive,disapproved,withdrawn');
                if (count($active_bidder) === 1) {
                  if ($plan->mode_id === 1) {
                    $ranking = "Lone Bidder";
                  } else {
                    $ranking = "Lone Quotation";
                  }
                } else if (count($active_bidder) > 1) {
                  if ($plan->mode_id === 1) {
                    $ranking = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . " LCB";
                  } else {
                    $ranking = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . " LCPQ";
                  }
                } else {
                }
                $temp_bidder["proposed_bid"] = "PHP " . number_format($bidder->proposed_bid, 2, '.', ',');
                $temp_bidder["bid_as_evaluated"] = "PHP " . number_format($bidder->bid_as_evaluated, 2, '.', ',');
                $temp_bidder["rank"] = $ranking;

                // Check if detailed bid is not null
                $cluster_bids = $APP->getClusterBids($bidder->project_bid);
                $with_detailed_bids = 0;
                $detailed_proposed_bid = "";
                $detailed_bid_in_words = "";
                $detailed_bid_as_evaluated = "";
                $letter = 'A';
                foreach ($cluster_bids as $key => $project_bid) {
                  if ($project_bid->detailed_bid_as_read > 0) {
                    if ($detailed_proposed_bid == "") {
                      $detailed_proposed_bid = "PHP" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                      $detailed_bid_in_words = "PHP" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                      $detailed_bid_as_evaluated = "PHP" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    } else {
                      $detailed_proposed_bid = $detailed_proposed_bid . " + PHP" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                      $detailed_bid_in_words = $detailed_bid_in_words . " + PHP" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                      $detailed_bid_as_evaluated = $detailed_bid_as_evaluated . " + PHP" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    }
                    ++$letter;
                    ++$with_detailed_bids;
                  }
                }

                if (count($cluster_bids) == $with_detailed_bids) {
                  $temp_bidder["proposed_bid"] = $detailed_proposed_bid . " = " . $temp_bidder["proposed_bid"];
                  $temp_bidder["bid_in_words"] = $detailed_bid_in_words . " = " . $temp_bidder["bid_in_words"];
                  $temp_bidder["bid_as_evaluated"] = $detailed_bid_as_evaluated . " = " . $temp_bidder["bid_as_evaluated"];
                }

                if ($temp_bidder["proposed_bid"] == $temp_bidder["bid_in_words"]) {
                  $temp_bidder["bid_in_words"] = "SAME AS READ";
                }

                $rank = $rank + 1;
              }
            }

            array_push($temp_bidders, (object) $temp_bidder);
          }

          $rows = count($bidders);
          if ($rows === 0) {
            $rows = 1;
          }
          $temp_bidders = $APP->sortObject($temp_bidders, array('date_received' => 'asc', 'time_received' => 'asc'));
          $temp_plan["number"] = $count;
          $temp_plan["bidders"] = (array)$temp_bidders;
          $temp_plan["bidder_count"] = count($bidders);
          $temp_plan["rows"] = $rows;
          $temp_plan["project_title"] = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($title))));
          $temp_plan["project_cost"] = $project_cost;
          $temp_plan["group"] = $group;
          $temp_plan["mode_id"] = $initial_mode;
          $temp_plan["project_no"] = htmlspecialchars(strtoupper(strtolower($project_number)));
          $temp_plan["source_of_fund"] = htmlspecialchars(strtoupper(strtolower($source_of_fund)));

          if ($same_location === true) {
            if ($plan->barangay_id != null) {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
            } else {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->barangay_name . ", " . $plan->municipality_name)));
            }
          } else {
            $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
          }
          $count = $count + 1;
          array_push($desired_plan_array, (object) $temp_plan);
        }
      }

      return back()->withInput()->with("project_plans", (object)$desired_plan_array);
    } else {
      return back()->withInput()->with("project_plans", []);
    }
  }

  public function downloadAbstract($date_opened)
  {

    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $desired_plan_array = [];
    $desired_plan_format = ["number" => null, "project_title" => null, "location" => null, "rows" => null, "bidders" => null];
    $date_opened = date("Y-m-d", strtotime($date_opened));
    $ids_array = [];
    $APP = new APP;
    $bidding_cnt = 0;
    $svp_cnt = 0;
    $negotiated_cnt = 0;
    $meeting = DB::table("meeting")
      ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
      ->where('meeting.meeting_date', $date_opened)
      ->orderBy("meeting_id", "desc")
      ->first();
    if ($meeting == null) {
      $meeting_room = "Ben Palispis Hall, 3rd Floor Provincial  Capitol, La Trinidad, Benguet";
    } else {
      $meeting_room = $meeting->address;
    }
    $plans = DB::table('project_plans')
      ->where('project_timelines.bid_submission_start', $date_opened)
      ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
      ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
      ->orderBy('project_plans.mode_id', 'asc')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();

    $bac = DB::table('bids_and_awards_committee')
      ->select(
        'bids_and_awards_committee.bac_id',
        DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
        DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name")
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



    $bac_infra_members = DB::table('bac_member')->where('bac_id', $bac->bac_id)
      ->select(DB::raw("CONCAT(member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"))
      ->where('bac_member.bac_member_type', 'BAC Infrastructure Member')
      ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();



    $bac_observers = DB::table('bac_observer')->where('bac_id', $bac->bac_id)
      ->select('observer.*', DB::raw("CONCAT(observer.observer_fname,' ',if(observer.observer_minitial is null ,'',observer.observer_minitial),' ',observer.observer_lname) AS observer_name"))
      ->join('observer', 'observer.observer_id', '=', 'bac_observer.observer_id')
      ->get();


    if (count($plans) > 0) {
      $count = 1;
      $initial_mode = 0;
      foreach ($plans as $plan) {
        $same_location = true;
        $initial_barangay = $plan->barangay_id;
        if ($initial_barangay === null) {
          $same_location = false;
        }
        if ($plan->mode_id != $initial_mode) {
          $initial_mode = $plan->mode_id;
          if ($initial_mode == 1) {
            $group = "PUBLIC BIDDING";
            $rank_label = "LCB";
          } else if ($initial_mode == 2) {
            $group = "SMALL VALUE PROCUREMENT";
            $rank_label = "LCPQ";
          } else if ($initial_mode == 3) {
            $group = "NEGOTIATED PROCUREMENT";
            $rank_label = "LCPQ";
          } else {
            $group = "";
          }
          $count = 1;
        }
        $temp_plan = $desired_plan_format;
        if (in_array($plan->plan_id, $ids_array) == false) {
          $title = $plan->project_title;
          array_push($ids_array, $plan->plan_id);
          $rank = 1;
          $title = $plan->project_title;
          $project_cost = $plan->project_cost;
          $total = $plan->project_cost;

          if ($plan->mode_id == 1) {
            $bidding_cnt = $bidding_cnt + 1;
          } else if ($plan->mode_id == 2) {
            $svp_cnt = $svp_cnt + 1;
          } else if ($plan->mode_id == 3) {
            $negotiated_cnt = $negotiated_cnt + 1;
          } else {
          }

          // Get cluster and append titles
          if ($plan->plan_cluster_id != null) {
            $letter = 'A';
            $total = 0;
            $title = "";
            $source_of_fund = "";
            $project_number = "";
            $project_cost = "";
            $clusters = DB::table('project_plans')
              ->where([['project_timelines.bid_submission_start', $date_opened], ['procacts.plan_cluster_id', $plan->plan_cluster_id]])
              ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
              ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
              ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
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
              $project_cost = $project_cost . "" . number_format((float)$cluster->project_cost, 2, '.', ',') . " + ";
              $letter = ++$letter;
              if ($cluster->special_case_1 == 1) {
                $title = $cluster->project_title;
              }
            }
            $project_cost = rtrim($project_cost, ' + ');
            $project_cost = $project_cost . "= " . number_format((float)$total, 2, '.', ',');
          } else {
            $title = $plan->project_title;
            $project_cost = "" . number_format((float)$plan->project_cost, 2, '.', ',');
            $project_number = $plan->project_no;
            $source_of_fund = $plan->source;
          }


          // get all bidders
          $bidders = $APP->getAllTakers($plan->procact_id);


          $rank = 1;
          $temp_bidders = [];
          foreach ($bidders as $key => $bidder) {
            $temp_bidder = (array)$bidder;

            if ($bidder->bid_status == null) {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "DNS"));
            } else if ($bidder->bid_status == "late") {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "Late Submission"));
            } else {
              $temp_bidder = array_merge($temp_bidder, array("remarks_status" => "Submitted"));
            }

            if ($bidder->bid_status == null) {
              $temp_bidder["rank"] = "";
              $temp_bidder["proposed_bid_number"] = 0;
              $temp_bidder["bid_as_evaluated_number"] = 0;
            } elseif ($bidder->bid_status == "disqualified") {
              if ($bidder->bid_as_evaluated != null) {
                $temp_bidder["proposed_bid"] = "PHP " . number_format($bidder->proposed_bid, 2, '.', ',');
                $temp_bidder["bid_as_evaluated"] = "PHP " . number_format($bidder->bid_as_evaluated, 2, '.', ',');
                $temp_bidder["proposed_bid_number"] = $bidder->proposed_bid;
                $temp_bidder["bid_as_evaluated_number"] = $bidder->bid_as_evaluated;
              } else {
                $temp_bidder["proposed_bid"] = "";
                $temp_bidder["bid_as_evaluated"] = "";
                $temp_bidder["proposed_bid_number"] = 0;
                $temp_bidder["bid_as_evaluated_number"] = 0;
              }
              $disqualification = DB::table('disqualification_records')->where([['project_bid', $bidder->project_bid], ['remarks', 'like', 'Disqualified:%']])->first();

              if ($disqualification != null) {
                $temp_bidder["rank"] = $disqualification->remarks;
              } else {
                $temp_bidder["rank"] = "";
              }
            } elseif ($bidder->bid_status == "ineligible") {
              if ($bidder->bid_as_evaluated != null) {
                $temp_bidder["proposed_bid"] = "PHP " . number_format($bidder->proposed_bid, 2, '.', ',');
                $temp_bidder["bid_as_evaluated"] = "PHP " . number_format($bidder->bid_as_evaluated, 2, '.', ',');
                $temp_bidder["proposed_bid_number"] = $bidder->proposed_bid;
                $temp_bidder["bid_as_evaluated_number"] = $bidder->bid_as_evaluated;
              } else {
                $temp_bidder["proposed_bid"] = "";
                $temp_bidder["bid_as_evaluated"] = "";
                $temp_bidder["proposed_bid_number"] = 0;
                $temp_bidder["bid_as_evaluated_number"] = 0;
              }
              $disqualification = DB::table('disqualification_records')->where([['project_bid', $bidder->project_bid], ['remarks', 'like', 'Ineligible:%']])->first();
              $temp_bidder["rank"] = $disqualification->remarks;
            } else {
              if ($plan->main_status === "deferred") {
                $temp_bidder["proposed_bid"] = null;
                $temp_bidder["bid_as_evaluated"] = null;
                $temp_bidder["rank"] = 'Deferred Opening';
              } else {
                $active_bidder = $APP->getBidEvalBiddersData($plan->procact_id, 'responsive,active,non-responsive,disapproved,withdrawn');
                if (count($active_bidder) === 1) {
                  if ($plan->mode_id === 1) {
                    $ranking = "Lone Bidder";
                  } else {
                    $ranking = "Lone Quotation";
                  }
                } else if (count($active_bidder) > 1) {
                  if ($plan->mode_id === 1) {
                    $ranking = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . " LCB";
                  } else {
                    $ranking = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . " LCPQ";
                  }
                } else {
                }

                $temp_bidder["proposed_bid_number"] = $bidder->proposed_bid;
                $temp_bidder["bid_as_evaluated_number"] = $bidder->bid_as_evaluated;
                $cluster_bids = $APP->getClusterBids($bidder->project_bid);

                $with_detailed_bids = 0;
                $detailed_proposed_bid = "";
                $detailed_bid_in_words = "";
                $detailed_bid_as_evaluated = "";
                $letter = 'A';
                foreach ($cluster_bids as $key => $project_bid) {
                  if ($project_bid->detailed_bid_as_read > 0) {
                    if ($detailed_proposed_bid == "") {
                      $detailed_proposed_bid = "PHP" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                      $detailed_bid_in_words = "PHP" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                      $detailed_bid_as_evaluated = "PHP" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    } else {
                      $detailed_proposed_bid = $detailed_proposed_bid . " + PHP" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                      $detailed_bid_in_words = $detailed_bid_in_words . " + PHP" . number_format($project_bid->detailed_bid_in_words, 2, '.', ',');
                      $detailed_bid_as_evaluated = $detailed_bid_as_evaluated . " + PHP" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    }
                    ++$letter;
                    ++$with_detailed_bids;
                  }
                }

                if (count($cluster_bids) == $with_detailed_bids) {
                  $bidder->proposed_bid = $detailed_proposed_bid . " = PHP" . number_format($bidder->proposed_bid, 2, '.', ',');
                  $bidder->bid_in_words = $detailed_bid_in_words . " = PHP" . number_format($bidder->bid_in_words, 2, '.', ',');
                  $bidder->bid_as_evaluated = $detailed_bid_as_evaluated . " = PHP" . number_format($bidder->bid_as_evaluated, 2, '.', ',');
                  $temp_bidder["proposed_bid"] = $bidder->proposed_bid;
                  $temp_bidder["bid_as_evaluated"] = $bidder->bid_as_evaluated;
                } else {
                  $temp_bidder["proposed_bid"] = "PHP " . number_format($bidder->proposed_bid, 2, '.', ',');
                  $temp_bidder["bid_as_evaluated"] = "PHP " . number_format($bidder->bid_as_evaluated, 2, '.', ',');
                }

                if ($bidder->proposed_bid == $bidder->bid_in_words) {
                  $bidder->bid_in_words = "SAME AS READ";
                }


                $temp_bidder = array_merge($temp_bidder, array("rank" => $ranking));
                $rank = $rank + 1;
              }
            }
            array_push($temp_bidders, (object) $temp_bidder);
          }

          $rows = count($bidders);

          if ($rows === 0) {
            $rows = 1;
          }

          $temp_bidders = $APP->sortObject($temp_bidders, array('date_received' => 'asc', 'time_received' => 'asc'));
          $temp_plan["number"] = $count;
          $temp_plan["bidders"] = (array)$temp_bidders;
          $temp_plan["bidder_count"] = count($bidders);
          $temp_plan["rows"] = $rows;
          $temp_plan["project_title"] = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($title))));
          $temp_plan["project_cost"] = $project_cost;
          $temp_plan["group"] = $group;
          $temp_plan["mode_id"] = $initial_mode;
          $temp_plan["project_no"] = htmlspecialchars(strtoupper(strtolower($project_number)));
          $temp_plan["source_of_fund"] = htmlspecialchars(strtoupper(strtolower($source_of_fund)));

          if ($same_location === true) {
            if ($plan->barangay_id != null) {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
            } else {
              $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->barangay_name . ", " . $plan->municipality_name)));
            }
          } else {
            $temp_plan["location"] = htmlspecialchars(strtoupper(strtolower($plan->municipality_display)));
          }
          $count = $count + 1;
          array_push($desired_plan_array, (object) $temp_plan);
        }
      }


      $myFontStyle = array('name' => 'Arial Narrow', 'size' => 12, 'bold' => true, 'valign' => 'center', 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),);
      $myFontStyle2 = array('name' => 'Arial Narrow', 'size' => 12, 'bold' => false, 'valign' => 'center', 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),);
      $myParagraphStyle = array('align' => 'center', 'valign' => 'center', 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0));
      $cellStyle = array('valign' => 'center');
      $file_names = [];

      $abstract_of_bids = "Abstract of Bids.docx";
      $abstract_of_quotations_svp = "Abstract of Quotations-SVP.docx";
      $abstract_of_quotations_negotiated = "Abstract of Quotations-Negotiated Procurement.docx";

      if ($bidding_cnt > 0) {
        array_push($file_names, $abstract_of_bids);
        $item = 1;
        $BiddingtemplateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/Abstract Of Bids.docx");
        $member_rows = ceil((count($bac_infra_members) + 2) / 5);
        $bac_member_no = 1;
        $BiddingtemplateProcessor->cloneRow('bac_name1', $member_rows);
        for ($i = 1; $i <= $member_rows; $i++) {
          $bac_number = 1;
          if ($i === 1) {
            // Insert BAC Chair and Vice  Chairman
            $BiddingtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac->bac_chairman_name)));
            $BiddingtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC CHAIRMAN");
            $bac_number = $bac_number + 1;
            $BiddingtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac->bac_vice_chairman_name)));
            $BiddingtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC - VICE CHAIRMAN");
            $bac_number = $bac_number + 1;
            while ($bac_number <= 5) {
              if ($bac_member_no <= count($bac_infra_members)) {
                $BiddingtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac_infra_members[($bac_member_no - 1)]->member_name)));
                $BiddingtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC MEMBER");
                $bac_member_no = $bac_member_no + 1;
              } else {
                $BiddingtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, "");
                $BiddingtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "");
              }
              $bac_number = $bac_number + 1;
            }
          } else {
            while ($bac_number <= 5) {
              if ($bac_member_no <= count($bac_infra_members)) {
                $BiddingtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac_infra_members[($bac_member_no - 1)]->member_name)));
                $BiddingtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC MEMBER");
                $bac_member_no = $bac_member_no + 1;
              } else {
                $BiddingtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, "");
                $BiddingtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "");
              }
              $bac_number = $bac_number + 1;
            }
          }
        }

        for ($i = 0; $i < count($bac_observers); $i++) {
          $BiddingtemplateProcessor->setValue('observer' . ($i + 1), strtoupper(strtolower($bac_observers[$i]->observer_name)));
          $BiddingtemplateProcessor->setValue('observer_position' . ($i + 1), strtoupper(strtolower($bac_observers[$i]->observer_office)) . '-REPRESENTATIVE');
        }

        $BiddingtemplateProcessor->cloneBlock("bidding_item", $bidding_cnt, true, true);



        foreach ($desired_plan_array as $plan) {
          if ($plan->mode_id == 1) {
            $BiddingtemplateProcessor->setValue('opening_date#' . $item, date("F d, Y", strtotime($date_opened)));
            $BiddingtemplateProcessor->setValue('item#' . $item, $item);
            $BiddingtemplateProcessor->setValue('project_number#' . $item, strtoupper(strtolower($plan->project_no)));
            $BiddingtemplateProcessor->setValue('bidding_project_title#' . $item, str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($plan->project_title)))));
            $BiddingtemplateProcessor->setValue('bidding_location#' . $item, strtoupper(strtolower($plan->location)));
            $BiddingtemplateProcessor->setValue('bid_abc#' . $item, strtoupper(strtolower($plan->project_cost)));
            $BiddingtemplateProcessor->setValue('source_of_fund#' . $item, str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($plan->source_of_fund)))));
            $BiddingtemplateProcessor->setValue('meeting_room#' . $item, $meeting_room);
            $BiddingtemplateProcessor->setValue('page_break#' . $item, '<w:p><w:r><w:br w:type="page"/></w:r></w:p>');
            $no_bidders = false;

            // bidders table
            if (count((array)$plan->bidders) === 0) {
              $no_bidders = true;
            }
            if (count((array)$plan->bidders) < 3 && count((array)$plan->bidders) > 0) {
              $bidder_width = 3800;
            } else {
              $bidder_width = 1800;
            }


            $bidder_cnt = 1;
            $table = new Table(array('borderSize' => 9, 'spacing' => 0, 'borderColor' => ' black', 'unit' => TblWidth::TWIP));
            $table->addRow();
            $table->addCell()->addText("");
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                $table->addCell()->addText($bidder_cnt, $myFontStyle, $myParagraphStyle);
                $bidder_cnt = $bidder_cnt + 1;
              }
            }

            $table->addRow();
            $table->addCell()->addText("NAME OF BIDDERS", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('No Bidders', $myFontStyle, $myParagraphStyle);
            } else {
              foreach ($plan->bidders as $bidder) {
                $business_name = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($bidder->business_name))));
                $table->addCell($bidder_width)->addText($business_name, $myFontStyle, $myParagraphStyle);
              }
            }

            $table->addRow();
            $table->addCell()->addText("BID AS READ", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->bid_status != "disqualified" && $bidder->bid_status != null) {
                  $table->addCell($bidder_width)->addText($bidder->proposed_bid, $myFontStyle, $myParagraphStyle);
                } else {
                  if ($bidder->proposed_bid_number > 0) {
                    $table->addCell($bidder_width)->addText($bidder->proposed_bid, $myFontStyle, $myParagraphStyle);
                  } else if ($bidder->remarks_status === "DNS") {
                    $table->addCell($bidder_width)->addText("Did Not Submit", $myFontStyle, $myParagraphStyle);
                  } else {
                    $table->addCell($bidder_width)->addText("", $myFontStyle, $myParagraphStyle);
                  }
                }
              }
            }

            $table->addRow();
            $table->addCell()->addText("BID AS EVALUATED", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->bid_status != "disqualified" && $bidder->bid_status != null) {
                  $table->addCell($bidder_width)->addText($bidder->bid_as_evaluated, $myFontStyle, $myParagraphStyle);
                } else {
                  if ($bidder->bid_as_evaluated_number > 0) {
                    $table->addCell($bidder_width)->addText($bidder->bid_as_evaluated, $myFontStyle, $myParagraphStyle);
                  } else {
                    $table->addCell($bidder_width)->addText("", $myFontStyle, $myParagraphStyle);
                  }
                }
              }
            }

            $table->addRow();
            $table->addCell()->addText("BID SECURITY", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->bid_status != "disqualified" && $bidder->bid_status != null) {
                  $table->addCell()->addText("BID SECURING DECLARATION", $myFontStyle, $myParagraphStyle);
                } else {
                  $table->addCell($bidder_width)->addText("", $myFontStyle, $myParagraphStyle);
                }
              }
            }

            $table->addRow();
            $table->addCell()->addText("RANK", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->remarks_status === "Submitted") {
                  $table->addCell($bidder_width)->addText($bidder->rank, $myFontStyle2, $myParagraphStyle);
                } else {
                  $table->addCell($bidder_width)->addText("", $myFontStyle2, $myParagraphStyle);
                }
              }
            }
            $BiddingtemplateProcessor->setComplexBlock('bidders_table#' . $item, $table);

            $item = $item + 1;
          }
        }
        $BiddingtemplateProcessor->saveAs(public_path() . '\\' . 'word_results/Abstract Of Bids.docx');
      }

      if ($svp_cnt > 0) {
        array_push($file_names, $abstract_of_quotations_svp);
        $item = 1;
        $SVPtemplateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/Abstract Of Quotations-SVP.docx");
        $member_rows = ceil((count($bac_infra_members) + 2) / 5);
        $bac_member_no = 1;
        $SVPtemplateProcessor->cloneRow('bac_name1', $member_rows);
        for ($i = 1; $i <= $member_rows; $i++) {
          $bac_number = 1;
          if ($i === 1) {
            // Insert BAC Chair and Vice  Chairman
            $SVPtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac->bac_chairman_name)));
            $SVPtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC CHAIRMAN");
            $bac_number = $bac_number + 1;
            $SVPtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac->bac_vice_chairman_name)));
            $SVPtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC - VICE CHAIRMAN");
            $bac_number = $bac_number + 1;
            while ($bac_number <= 5) {
              if ($bac_member_no <= count($bac_infra_members)) {
                $SVPtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac_infra_members[($bac_member_no - 1)]->member_name)));
                $SVPtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC MEMBER");
                $bac_member_no = $bac_member_no + 1;
              } else {
                $SVPtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, "");
                $SVPtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "");
              }
              $bac_number = $bac_number + 1;
            }
          } else {
            while ($bac_number <= 5) {
              if ($bac_member_no <= count($bac_infra_members)) {
                $SVPtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac_infra_members[($bac_member_no - 1)]->member_name)));
                $SVPtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC MEMBER");
                $bac_member_no = $bac_member_no + 1;
              } else {
                $SVPtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, "");
                $SVPtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "");
              }
              $bac_number = $bac_number + 1;
            }
          }
        }

        for ($i = 0; $i < count($bac_observers); $i++) {
          $SVPtemplateProcessor->setValue('observer' . ($i + 1), strtoupper(strtolower($bac_observers[$i]->observer_name)));
          $SVPtemplateProcessor->setValue('observer_position' . ($i + 1), strtoupper(strtolower($bac_observers[$i]->observer_office)) . '-REPRESENTATIVE');
        }
        $SVPtemplateProcessor->cloneBlock("svp_item", $svp_cnt, true, true);
        foreach ($desired_plan_array as $plan) {
          if ($plan->mode_id == 2) {
            $SVPtemplateProcessor->setValue('number#' . $item, $item);
            $SVPtemplateProcessor->setValue('page_break#' . $item, '<w:p><w:r><w:br w:type="page"/></w:r></w:p>');
            $SVPtemplateProcessor->setValue('opening_date#' . $item, date("F d, Y", strtotime($date_opened)));
            $SVPtemplateProcessor->setValue('project_number#' . $item, strtoupper(strtolower($plan->project_no)));
            $SVPtemplateProcessor->setValue('item#' . $item, $item);
            $SVPtemplateProcessor->setValue('project_title#' . $item, str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($plan->project_title)))));
            $SVPtemplateProcessor->setValue('location#' . $item, $plan->location);
            $SVPtemplateProcessor->setValue('abc#' . $item, $plan->project_cost);
            $SVPtemplateProcessor->setValue('meeting_room#' . $item, $meeting_room);
            $SVPtemplateProcessor->setValue('source_of_fund#' . $item, str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($plan->source_of_fund)))));
            $no_bidders = false;

            // bidders table
            if (count((array)$plan->bidders) === 0) {
              $no_bidders = true;
            }
            if (count((array)$plan->bidders) < 3 && count((array)$plan->bidders) > 0) {
              $bidder_width = 3800;
            } else {
              $bidder_width = 1800;
            }

            $bidder_cnt = 1;
            $table = new Table(array('borderSize' => 9, 'borderColor' => ' black', 'unit' => TblWidth::TWIP));
            $table->addRow();
            $table->addCell()->addText("");
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                $table->addCell()->addText($bidder_cnt, $myFontStyle, $myParagraphStyle);
                $bidder_cnt = $bidder_cnt + 1;
              }
            }

            $table->addRow();
            $table->addCell()->addText("NAME OF INTERESTED/INVITED CONTRACTORS", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('No Quotations', $myFontStyle, $myParagraphStyle);
            } else {
              foreach ($plan->bidders as $bidder) {
                $business_name = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($bidder->business_name))));
                $table->addCell($bidder_width)->addText($business_name, $myFontStyle, $myParagraphStyle);
              }
            }

            $table->addRow();
            $table->addCell()->addText("QUOTATION AS READ", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->bid_status != "disqualified" && $bidder->bid_status != null) {
                  $table->addCell($bidder_width)->addText($bidder->proposed_bid, $myFontStyle, $myParagraphStyle);
                } else {
                  if ($bidder->proposed_bid_number > 0) {
                    $table->addCell($bidder_width)->addText($bidder->proposed_bid, $myFontStyle, $myParagraphStyle);
                  } else if ($bidder->remarks_status === "DNS") {
                    $table->addCell($bidder_width)->addText("Did Not Submit", $myFontStyle, $myParagraphStyle);
                  } else {
                    $table->addCell($bidder_width)->addText("", $myFontStyle, $myParagraphStyle);
                  }
                }
              }
            }

            $table->addRow();
            $table->addCell()->addText("QUOTATION AS EVALUATED", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->bid_status != "disqualified" && $bidder->bid_status != null) {
                  $table->addCell($bidder_width)->addText($bidder->bid_as_evaluated, $myFontStyle, $myParagraphStyle);
                } else {
                  if ($bidder->bid_as_evaluated_number > 0) {
                    $table->addCell($bidder_width)->addText($bidder->bid_as_evaluated, $myFontStyle, $myParagraphStyle);
                  } else {
                    $table->addCell($bidder_width)->addText("", $myFontStyle, $myParagraphStyle);
                  }
                }
              }
            }

            $table->addRow();
            $table->addCell()->addText("RANK", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->remarks_status === "Submitted") {
                  $table->addCell($bidder_width)->addText($bidder->rank, $myFontStyle2, $myParagraphStyle);
                } else {
                  $table->addCell($bidder_width)->addText("", $myFontStyle2, $myParagraphStyle);
                }
              }
            }

            $SVPtemplateProcessor->setComplexBlock('bidders_table#' . $item, $table);


            $item = $item + 1;
          }
        }
        $SVPtemplateProcessor->saveAs(public_path() . '\\' . 'word_results/Abstract Of Quotations-SVP.docx');
      }

      if ($negotiated_cnt > 0) {
        array_push($file_names, $abstract_of_quotations_negotiated);
        $item = 1;
        $NegotiatedtemplateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/Abstract Of Quotations-Negotiated Procurement.docx");
        $member_rows = ceil((count($bac_infra_members) + 2) / 5);
        $bac_member_no = 1;
        $NegotiatedtemplateProcessor->cloneRow('bac_name1', $member_rows);
        for ($i = 1; $i <= $member_rows; $i++) {
          $bac_number = 1;
          if ($i === 1) {
            // Insert BAC Chair and Vice  Chairman
            $NegotiatedtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac->bac_chairman_name)));
            $NegotiatedtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC CHAIRMAN");
            $bac_number = $bac_number + 1;
            $NegotiatedtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac->bac_vice_chairman_name)));
            $NegotiatedtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC - VICE CHAIRMAN");
            $bac_number = $bac_number + 1;
            while ($bac_number <= 5) {
              if ($bac_member_no <= count($bac_infra_members)) {
                $NegotiatedtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac_infra_members[($bac_member_no - 1)]->member_name)));
                $NegotiatedtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC MEMBER");
                $bac_member_no = $bac_member_no + 1;
              } else {
                $NegotiatedtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, "");
                $NegotiatedtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "");
              }
              $bac_number = $bac_number + 1;
            }
          } else {
            while ($bac_number <= 5) {
              if ($bac_member_no <= count($bac_infra_members)) {
                $NegotiatedtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, strtoupper(strtolower($bac_infra_members[($bac_member_no - 1)]->member_name)));
                $NegotiatedtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "BAC MEMBER");
                $bac_member_no = $bac_member_no + 1;
              } else {
                $NegotiatedtemplateProcessor->setValue('bac_name' . $bac_number . '#' . $i, "");
                $NegotiatedtemplateProcessor->setValue('bac_position' . $bac_number . '#' . $i, "");
              }
              $bac_number = $bac_number + 1;
            }
          }
        }

        for ($i = 0; $i < count($bac_observers); $i++) {
          $NegotiatedtemplateProcessor->setValue('observer' . ($i + 1), strtoupper(strtolower($bac_observers[$i]->observer_name)));
          $NegotiatedtemplateProcessor->setValue('observer_position' . ($i + 1), strtoupper(strtolower($bac_observers[$i]->observer_office)) . '-REPRESENTATIVE');
        }
        $NegotiatedtemplateProcessor->cloneBlock("svp_item", $negotiated_cnt, true, true);
        $NegotiatedtemplateProcessor->setValue('date',  date("F d, Y", strtotime($date_opened)));
        foreach ($desired_plan_array as $plan) {

          if ($plan->mode_id === 3) {
            $NegotiatedtemplateProcessor->setValue('opening_date#' . $item, date("F d, Y", strtotime($date_opened)));
            $NegotiatedtemplateProcessor->setValue('project_number#' . $item, strtoupper(strtolower($plan->project_no)));
            $NegotiatedtemplateProcessor->setValue('project_title#' . $item, str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($plan->project_title)))));
            $NegotiatedtemplateProcessor->setValue('item#' . $item, $item);
            $NegotiatedtemplateProcessor->setValue('location#' . $item, $plan->location);
            $NegotiatedtemplateProcessor->setValue('abc#' . $item, $plan->project_cost);
            $NegotiatedtemplateProcessor->setValue('source_of_fund#' . $item, str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($plan->source_of_fund)))));
            $NegotiatedtemplateProcessor->setValue('page_break#' . $item, '<w:p><w:r><w:br w:type="page"/></w:r></w:p>');
            $NegotiatedtemplateProcessor->setValue('meeting_room#' . $item, $meeting_room);

            $no_bidders = false;

            // bidders table
            if (count((array)$plan->bidders) === 0) {
              $no_bidders = true;
            }
            if (count((array)$plan->bidders) < 3 && count((array)$plan->bidders) > 0) {
              $bidder_width = 3800;
            } else {
              $bidder_width = 1800;
            }

            $bidder_cnt = 1;
            $table = new Table(array('borderSize' => 9, 'borderColor' => ' black', 'unit' => TblWidth::TWIP));
            $table->addRow();
            $table->addCell()->addText("");
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                $table->addCell()->addText($bidder_cnt, $myFontStyle, $myParagraphStyle);
                $bidder_cnt = $bidder_cnt + 1;
              }
            }


            $table->addRow();
            $table->addCell()->addText("NAME OF INTERESTED/INVITED CONTRACTORS", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('No Quotations', $myFontStyle, $myParagraphStyle);
            } else {
              foreach ($plan->bidders as $bidder) {
                $business_name = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($bidder->business_name))));
                $table->addCell($bidder_width)->addText($business_name, $myFontStyle, $myParagraphStyle);
              }
            }

            $table->addRow();
            $table->addCell()->addText("QUOTATION AS READ", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->bid_status != "disqualified" && $bidder->bid_status != null) {
                  $table->addCell($bidder_width)->addText($bidder->proposed_bid, $myFontStyle, $myParagraphStyle);
                } else {
                  if ($bidder->proposed_bid_number > 0) {
                    $table->addCell($bidder_width)->addText($bidder->proposed_bid, $myFontStyle, $myParagraphStyle);
                  } else if ($bidder->remarks_status === "DNS") {
                    $table->addCell($bidder_width)->addText("Did Not Submit", $myFontStyle, $myParagraphStyle);
                  } else {
                    $table->addCell($bidder_width)->addText("", $myFontStyle, $myParagraphStyle);
                  }
                }
              }
            }

            $table->addRow();
            $table->addCell()->addText("QUOTATION AS EVALUATED", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->bid_status != "disqualified" && $bidder->bid_status != null) {
                  $table->addCell($bidder_width)->addText($bidder->bid_as_evaluated, $myFontStyle, $myParagraphStyle);
                } else {
                  if ($bidder->bid_as_evaluated_number > 0) {
                    $table->addCell($bidder_width)->addText($bidder->bid_as_evaluated, $myFontStyle, $myParagraphStyle);
                  } else {
                    $table->addCell($bidder_width)->addText("", $myFontStyle, $myParagraphStyle);
                  }
                }
              }
            }

            $table->addRow();
            $table->addCell()->addText("RANK", $myFontStyle, $myParagraphStyle);
            $table->addCell()->addText("");
            if ($no_bidders) {
              $table->addCell()->addText('');
            } else {
              foreach ($plan->bidders as $bidder) {
                if ($bidder->remarks_status === "Submitted") {
                  $table->addCell($bidder_width)->addText($bidder->rank, $myFontStyle2, $myParagraphStyle);
                } else {
                  $table->addCell($bidder_width)->addText("", $myFontStyle2, $myParagraphStyle);
                }
              }
            }

            $NegotiatedtemplateProcessor->setComplexBlock('bidders_table#' . $item, $table);
            $item = $item + 1;
          }
        }
        $NegotiatedtemplateProcessor->saveAs(public_path() . '\\' . 'word_results/Abstract Of Quotations-Negotiated Procurement.docx');
      }

      $zip_file = public_path() . '\\' . 'zips/Abstract' . $date_opened . '.zip';
      $zip = new \ZipArchive();
      $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
      foreach ($file_names as $key => $file_name) {
        $zip->addFile(public_path() . '\\' . 'word_results/' . $file_name, $file_name);
      }
      $zip->close();
      return  response()->file($zip_file)->deleteFileAfterSend(true);
    } else {
      return abort(403, 'No Projects Opened on Selected Date');
    }
  }

  public function generateCertificationOfPosting()
  {
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("admin.generate_certification", ['links' => $links, 'user_privilege' => $user_privilege, "modes" => $modes]);
  }


  public function SubmitGenerateCertificationOfPosting(Request $request)
  {
    $data = $request->validate([
      "date_opened" => 'required'
    ]);
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $date_opened = date("Y-m-d", strtotime($request->input('date_opened')));

    if ($request->input('mode_of_procurement') != null) {
      $plans = DB::table('project_plans')
        ->where([['procacts.procact_mode_id', $request->input('mode_of_procurement')], ['project_timelines.bid_submission_start', $date_opened]])
        ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->orderBy('project_plans.mode_id', 'asc')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->get();
    } else {
      $plans = DB::table('project_plans')->where('project_timelines.bid_submission_start', $date_opened)
        ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->orderBy('project_plans.mode_id', 'asc')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->get();
    }

    return back()->withInput()->with('project_plans', $plans);
  }

  public function downloadCertificationOfPosting(Request $request)
  {
    $data = $request->validate([
      "date_opened" => 'required'
    ]);
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $template_processor = new TemplateProcessor(public_path() . '\\' . "word_templates/certificate_of_posting.docx");
    $date_opened = date("Y-m-d", strtotime($request->input('date_opened')));
    $desired_plan_format = ["number" => null, "project_no" => null, "project_title" => null, "location" => null, "project_cost" => null, "advertisement_start" => null, "advertisement_end" => null, "mode" => null];
    $project_plans = [];
    $count = 0;

    if ($request->input('mode_of_procurement') != null) {
      $plans = DB::table('project_plans')
        ->where([['procacts.procact_mode_id', $request->input('mode_of_procurement')], ['project_timelines.bid_submission_start', $date_opened]])
        ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->orderBy('project_plans.mode_id', 'asc')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->get();
    } else {
      $plans = DB::table('project_plans')->where('project_timelines.bid_submission_start', $date_opened)
        ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
        ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
        ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
        ->orderBy('project_plans.mode_id', 'asc')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->get();
    }
    $ids_array = [];

    foreach ($plans as $plan) {
      $temp_plan = $desired_plan_format;
      if (in_array($plan->plan_id, $ids_array) == false) {
        $count++;
        $title = $plan->project_title;
        $project_cost = $plan->project_cost;
        $project_cost = '';

        // Get cluster and append titles
        if ($plan->plan_cluster_id != null) {
          $letter = 'A';
          $total = 0;
          $title = "";
          $project_cost = "";

          $clusters = DB::table('project_plans')
            ->where([['project_timelines.bid_submission_start', $date_opened], ['procacts.plan_cluster_id', $plan->plan_cluster_id]])
            ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
            ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
            ->leftJoin('barangays', 'barangays.barangay_id', 'project_plans.barangay_id')
            ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
            ->orderBy('procacts.itb_arrangement', 'asc')
            ->get();

          foreach ($clusters as $cluster) {
            array_push($ids_array, $cluster->plan_id);
            $temp = $letter . '. ' . $cluster->project_title;
            $title = $title . "   " . $temp;
            // $temp2=$letter.'. '.$cluster->project_cost;
            $total = $total + $cluster->project_cost;
            $project_cost = $project_cost . $letter . '.' . "Php" . number_format((float)$cluster->project_cost, 2, '.', ',') . ' ';
            $letter = ++$letter;
            if ($cluster->special_case_1 == 1) {
              $title = $cluster->project_title;
            }
          }

          $project_cost = $project_cost . "= Php" . number_format((float)$total, 2, '.', ',');
        } else {
          array_push($ids_array, $plan->plan_id);
          $project_cost = " Php" . number_format((float)$plan->project_cost, 2, '.', ',');
        }

        $temp_plan["number"] = $count;
        $temp_plan["location"] = $plan->municipality_name;
        $temp_plan["project_title"] = str_replace("&amp;AMP;", "&amp;", htmlspecialchars(strtoupper(strtolower($title))));
        $temp_plan["project_cost"] = $project_cost;
        $temp_plan["advertisement_start"] = date("F d, Y", strtotime($plan->advertisement_start));
        $temp_plan["advertisement_end"] = date("F d, Y", strtotime($plan->bid_submission_end));
        if ($plan->procact_mode_id == 1) {
          $temp_plan["mode"] = "Public Bidding";
        } else if ($plan->procact_mode_id == 2) {
          $temp_plan["mode"] = "Negotiated Procurement - Small Value Procurement";
        } else {
          $temp_plan["mode"] = "Negotiated Procurement - 2 Failed Bidding";
        }

        array_push($project_plans, $temp_plan);
      }
    }

    $meeting = DB::table("meeting")
      ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
      ->where('meeting.meeting_date', $date_opened)
      ->orderBy("meeting_id", "desc")
      ->first();

    if ($meeting == null) {
      $bac = DB::table('bids_and_awards_committee')->latest()->first();
      $insert = DB::table('meeting')->insert([
        "meeting_date_created" => $date_opened,
        "meeting_date" => $date_opened,
        "meeting_time" => "9:00",
        "meeting_room_id" => 1,
        "bac_id" => $bac->bac_id,
        "created_at" => now(),
        "updated_at" => now()
      ]);

      $meeting = DB::table("meeting")
        ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
        ->where('meeting.meeting_date', $date_opened)
        ->orderBy("meeting_id", "desc")
        ->first();
    }

    $bac_sec_chairman = DB::table('bids_and_awards_committee')->where('bids_and_awards_committee.bac_id', $meeting->bac_id)
      ->join('member', 'member.member_id', 'bids_and_awards_committee.bac_sec_chairman')
      ->first();

    if ($bac_sec_chairman->member_suffix != null) {
      $bac_sec_chairman_name = strtoupper(strtolower($bac_sec_chairman->member_fname . " " . $bac_sec_chairman->member_minitial . " " . $bac_sec_chairman->member_lname . " " . $bac_sec_chairman->member_suffix));
    } else {
      $bac_sec_chairman_name = strtoupper(strtolower($bac_sec_chairman->member_fname . " " . $bac_sec_chairman->member_minitial . " " . $bac_sec_chairman->member_lname));
    }

    $template_processor->cloneBlock("item", count($project_plans), true, true);
    $item = 1;
    foreach ($project_plans as $plan) {
      $day = date("jS", strtotime($plan['advertisement_end']));
      $month_year = date("F Y", strtotime($plan['advertisement_end']));
      $template_processor->setValue('project_title#' . $item, $plan['project_title']);
      $template_processor->setValue('abc#' . $item, $plan['project_cost']);
      $template_processor->setValue('start_date#' . $item, $plan['advertisement_start']);
      $template_processor->setValue('end_date#' . $item, $plan['advertisement_end']);
      $template_processor->setValue('day#' . $item, $day);
      $template_processor->setValue('bidding_or_svp#' . $item, $plan['mode']);
      $template_processor->setValue('month_year#' . $item, $month_year);
      $template_processor->setValue('bacsec_chairperson#' . $item, $bac_sec_chairman_name);
      $item++;
    }

    $template_processor->saveAs(public_path() . '\\' . 'word_results/Certification of Posting-' . date("F d, Y", strtotime($request->input('date_opened'))) . '.docx');
    return  response()->download(public_path() . '\\' . 'word_results/Certification of Posting-' . date("F d, Y", strtotime($request->input('date_opened'))) . '.docx');
  }
}
