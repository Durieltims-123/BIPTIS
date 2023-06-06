<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use Validator;

class TWGController extends Controller
{
  public function home()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('twg.dashboard', ['links' => $links, 'user_privilege' => $user_privilege]);
  }


  public function getPostQualificationActivity(Request $request)
  {
    if ($request->project_year != null) {
      $year = $request->project_year;
      $APP = new APP;
      $project_plans = $APP->getSpecificProcurementActivity('post_qualification', $year);
      return back()->withInput()->with('project_plans', $project_plans);
    } else {
      $year = date('Y');
      $title = "TWG POST QUALIFICATION";
      $APP = new APP;
      $project_plans = $APP->getSpecificProcurementActivity('post_qualification', $year);
      // dd($project_plans);
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();

      return view('twg.post_qualification', ['links' => $links, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
    }
  }

  public function getProjectBidders($id)
  {
    $APP = new APP;
    $data = $APP->getAllCurrentBidders($id);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("twg.project_bidders", ['links' => $links, "data" => $data]);
  }

  public function getProjectsWithBidders()
  {
    $current_year = date('Y');
    $APP = new APP;
    $project_plans = $APP->getSpecificProcurementActivity('projects_with_bidders', $current_year);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("twg.projects", ['links' => $links, 'user_privilege' => $user_privilege, 'project_plans' => $project_plans, 'title' => "Projects Bidders and Price Quotations", 'year' => $current_year]);
  }

  public function filterProjectsWithBidders(Request $request)
  {
    $current_year = $request->project_year;
    $APP = new APP;
    $project_plans = $APP->getSpecificProcurementActivity('projects_with_bidders', $current_year);
    return back()->withInput()->with("filtered_data", $project_plans);
  }
  public function downloadOngoingPostQual($year)
  {
    $APP = new APP;
    $project_plans = $APP->getSpecificProcurementActivity('post_qualification', $year);
    $now = Date("F j, Y");
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/ongoing_post_qual.xlsx");
    $worksheet = $spreadsheet->setActiveSheetIndexByName("Sheet1");
    $worksheet->getCell('A2')->setValue("AS OF " . strtoupper(strtolower(date("F j, Y"))));
    $cluster = null;
    $row = 5;
    $initial_opening = null;
    $opening_start = 5;
    $initial_cluster = null;
    $cluster_start = null;
    $initial_data = [];
    $borderedStyleArray = [
      'borders' => [
        'allBorders' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => ['rgb' => '000000']
        ]
      ]
    ];
    foreach ($project_plans as $i => $plan) {

      //Open Bid Formatting
      if ($i === 0) {
        $initial_opening = $plan->open_bid;
        $initial_cluster = $plan->plan_cluster_id;
        $initial_data = $plan;
        if ($plan->plan_cluster_id != null) {
          $cluster_start = $row;
        }
      } else {

        // For Open Bid
        if ($initial_opening != $plan->open_bid) {
          if ($opening_start != ($row - 1)) {
            $worksheet->mergeCells('A' . $opening_start . ':' . 'A' . ($row - 1));
          }
          $worksheet->getCell('A' . $opening_start)->setValue(Date('m/d/Y', strtotime($initial_opening)));
          $initial_opening = $plan->open_bid;
          $opening_start = $row;
        }

        // for other Details
        if ($initial_cluster != $plan->plan_cluster_id || $plan->plan_cluster_id == null) {
          if ($initial_cluster != null) {
            $worksheet->mergeCells('D' . $cluster_start . ':' . 'D' . ($row - 1));
            $worksheet->mergeCells('E' . $cluster_start . ':' . 'E' . ($row - 1));
            $worksheet->mergeCells('F' . $cluster_start . ':' . 'F' . ($row - 1));
            $worksheet->mergeCells('G' . $cluster_start . ':' . 'G' . ($row - 1));
            $worksheet->mergeCells('H' . $cluster_start . ':' . 'H' . ($row - 1));
            $worksheet->mergeCells('I' . $cluster_start . ':' . 'I' . ($row - 1));
            $worksheet->mergeCells('J' . $cluster_start . ':' . 'J' . ($row - 1));
            $worksheet->mergeCells('L' . $cluster_start . ':' . 'L' . ($row - 1));

            $worksheet->getCell('D' . $cluster_start)->setValue($initial_data->ongoing_post_qual);
            $worksheet->getCell('E' . $cluster_start)->setValue($initial_data->ongoing_post_qual_amount);
            $worksheet->getCell('F' . $cluster_start)->setValue(date("m/d/Y", strtotime($initial_data->post_qualification_end)));
            $worksheet->getCell('G' . $cluster_start)->setValue($initial_data->post_qual_days);
            $worksheet->getCell('H' . $cluster_start)->setValue($initial_data->maximum_days);
            $worksheet->getCell('I' . $cluster_start)->setValue($initial_data->municipality_name);
            $worksheet->getCell('J' . $cluster_start)->setValue($initial_data->mode);
            $worksheet->getCell('L' . $cluster_start)->setValue($initial_data->project_year);
          } else {
            $worksheet->getCell('D' . ($row - 1))->setValue($initial_data->ongoing_post_qual);
            $worksheet->getCell('E' . ($row - 1))->setValue($initial_data->ongoing_post_qual_amount);
            $worksheet->getCell('F' . ($row - 1))->setValue(date("m/d/Y", strtotime($initial_data->post_qualification_end)));
            $worksheet->getCell('G' . ($row - 1))->setValue($initial_data->post_qual_days);
            $worksheet->getCell('H' . ($row - 1))->setValue($initial_data->maximum_days);
            $worksheet->getCell('I' . ($row - 1))->setValue($initial_data->municipality_name);
            $worksheet->getCell('J' . ($row - 1))->setValue($initial_data->mode);
            $worksheet->getCell('K' . ($row - 1))->setValue($initial_data->project_cost);
            $worksheet->getCell('L' . ($row - 1))->setValue($initial_data->project_year);
          }

          if ($plan->plan_cluster_id != null) {
            $initial_cluster = $plan->plan_cluster_id;
            $cluster_start = $row;
            $initial_data = $plan;
          } else {
            $initial_cluster = null;
            $cluster_start = null;
            $initial_data = $plan;
          }
        }
      }

      // Other Details
      $worksheet->getCell('B' . $row)->setValue($plan->project_no);
      $worksheet->getCell('C' . $row)->setValue($plan->project_title);
      $worksheet->getCell('K' . $row)->setValue($plan->project_cost);

      // $worksheet->getCell('D'.$row)->setValue($plan->project_no);

      $row = $row + 1;
    }

    $row = $row - 1;
    // For Open Bid
    if ($initial_opening === $project_plans[(count($project_plans) - 1)]->open_bid) {
      $worksheet->mergeCells('A' . $opening_start . ':' . 'A' . $row);
      $worksheet->getCell('A' . $opening_start)->setValue(Date('m/d/Y', strtotime($initial_opening)));
    } else {
      $worksheet->getCell('A' . $row)->setValue(Date('m/d/Y', strtotime($project_plans[(count($plans) - 1)]->open_bid)));
    }

    // for other Details
    if ($initial_cluster === $plan->plan_cluster_id) {
      if ($initial_cluster != null) {
        $worksheet->mergeCells('D' . $cluster_start . ':' . 'D' . ($row));
        $worksheet->mergeCells('E' . $cluster_start . ':' . 'E' . ($row));
        $worksheet->mergeCells('F' . $cluster_start . ':' . 'F' . ($row));
        $worksheet->mergeCells('G' . $cluster_start . ':' . 'G' . ($row));
        $worksheet->mergeCells('H' . $cluster_start . ':' . 'H' . ($row));
        $worksheet->mergeCells('I' . $cluster_start . ':' . 'I' . ($row));
        $worksheet->mergeCells('J' . $cluster_start . ':' . 'J' . ($row));
        $worksheet->mergeCells('L' . $cluster_start . ':' . 'L' . ($row));

        $worksheet->getCell('D' . $cluster_start)->setValue($initial_data->ongoing_post_qual);
        $worksheet->getCell('E' . $cluster_start)->setValue($initial_data->ongoing_post_qual_amount);
        $worksheet->getCell('F' . $cluster_start)->setValue(date("m/d/Y", strtotime($initial_data->post_qualification_end)));
        $worksheet->getCell('G' . $cluster_start)->setValue($initial_data->post_qual_days);
        $worksheet->getCell('H' . $cluster_start)->setValue($initial_data->maximum_days);
        $worksheet->getCell('I' . $cluster_start)->setValue($initial_data->municipality_name);
        $worksheet->getCell('J' . $cluster_start)->setValue($initial_data->mode);
        $worksheet->getCell('L' . $cluster_start)->setValue($initial_data->project_year);
      } else {
        $worksheet->getCell('D' . ($row))->setValue($initial_data->ongoing_post_qual);
        $worksheet->getCell('E' . ($row))->setValue($initial_data->ongoing_post_qual_amount);
        $worksheet->getCell('F' . ($row))->setValue(date("m/d/Y", strtotime($initial_data->post_qualification_end)));
        $worksheet->getCell('G' . ($row))->setValue($initial_data->post_qual_days);
        $worksheet->getCell('H' . ($row))->setValue($initial_data->maximum_days);
        $worksheet->getCell('I' . ($row))->setValue($initial_data->municipality_name);
        $worksheet->getCell('J' . ($row))->setValue($initial_data->mode);
        $worksheet->getCell('L' . ($row))->setValue($initial_data->project_year);
      }

      if ($plan->plan_cluster_id != null) {
        $initial_cluster = $plan->plan_cluster_id;
        $cluster_start = $row;
        $initial_data = $plan;
      } else {
        $initial_cluster = null;
        $cluster_start = null;
        $initial_data = $plan;
      }
    }
    $worksheet->getStyle("A5:" . "L" . ($row))->applyFromArray($borderedStyleArray);





    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save(public_path() . '\\' . "excel_templates/Ongoing Post Qualification-" . $now . ".xlsx");
    return  response()->download(public_path() . '\\' . "excel_templates/Ongoing Post Qualification-" . $now . ".xlsx")->deleteFileAfterSend(true);
  }
}
