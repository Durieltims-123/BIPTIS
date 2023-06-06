<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;
use App\{Project, Fund, Procact, ProjectTimeline, Link, LinkPrivilege};

class ImportController extends Controller
{
  public function importAPP()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.import_app', ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function importLinks()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view('admin.import_links', ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function fixAPP()
  {

    $parents = DB::table('project_plans')->select('parent.*', 'project_plans.plan_id as child_id', 'project_plans.latest_procact_id as child_procact')
      ->join('project_plans as parent', 'project_plans.parent_id', 'parent.plan_id')
      ->where([['parent.is_old', true], ['parent.project_bid_id', '<>', null]])
      ->get();
    foreach ($parents as $parent) {
      if ($parent->latest_procact_id < $parent->child_procact) {
        $child = DB::table('project_plans')->where('latest_procact_id', $parent->child_procact)->first();
        $main_procact = DB::table('procacts')->where('procact_id', $parent->latest_procact_id)->first();
        // fix procacts
         DB::table('procacts')->where('procact_id', $parent->child_procact)
          ->update([
            "plan_id" => $parent->plan_id
          ]);

        DB::table('procacts')->where('procact_id', $parent->latest_procact_id)->update([
          "plan_id" => $parent->child_id
        ]);

        DB::table('project_activity_status')->where('procact_id', $parent->child_procact)->update([
          "plan_id" => $parent->plan_id
        ]);

        DB::table('project_activity_status')->where('procact_id', $parent->latest_procact_id)->update([
          "plan_id" => $parent->child_id
        ]);

        DB::table('project_timelines')->where('procact_id', $parent->child_procact)->update([
          "plan_id" => $parent->plan_id
        ]);

        DB::table('project_timelines')->where('procact_id', $parent->latest_procact_id)->update([
          "plan_id" => $parent->child_id
        ]);

        // fix parent
        $pp = DB::table('project_plans')->where('plan_id', $parent->plan_id)
         ->update([
          "status" => "pending",
          "latest_procact_id" => $parent->child_procact,
          "project_bid_id" => null
        ]);

        // fix child
        DB::table('project_plans')->where('plan_id', $parent->child_id)->update([
          "status" => $parent->status,
          "latest_procact_id" => $parent->latest_procact_id,
          "project_bid_id" => $parent->project_bid_id,
        ]);

        $logs=DB::table('project_logs')->where([['plan_id', $parent->plan_id],['created_at','>=',$main_procact->created_at]])
        ->update([
          "plan_id"=> $parent->child_id
        ]);
     
      }
    }

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view('admin.fix_app', ['links' => $links, 'user_privilege' => $user_privilege]);
  }
  public function checkApp()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view('admin.check_app', ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function checkStatus(Request $request)
  {
    if ($request->file('file') !== null) {
      if ($request->file('file')->isValid()) {
        $filename = $request->file('file')->getClientOriginalName();
        $pieces = explode(".", $filename);
        $last_index = count($pieces) - 1;
        if ($pieces[$last_index] == "xlsx" || $pieces[$last_index] == "xlsb" || $pieces[$last_index] == "xlsm") {
          $path = $request->file('file')->getRealPath();
          $collection = (new FastExcel)->sheet(2)->import($path);
          $duplicates = false;
          $unknown = 0;
          foreach ($collection as $key => $row) {

            $title = '%' . $row["Project Title"] . '%';
            $id = $row["number"];
            $plan = DB::table('project_plans')->where('project_title', 'like', $title)->where([['date_pow_added', '<>', null], ['procacts.open_bid', '<>', null]])->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')->orderBy('project_plans.plan_id', 'desc')->first();

            if ($plan === null) {
              $unknown = $unknown + 1;
              $plan = DB::table('project_plans')->where([['project_plans.plan_id', $id], ['procacts.open_bid', '<>', null]])->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')->orderBy('project_plans.plan_id', 'desc')->first();
            }
            if ($plan === null) {
              dump("double check");
            } else if ($plan->proceed_notice != null) {
              dump($plan);
              dump("finished");
            } else if ($plan->proceed_notice == null && $plan->contract_signing != null) {
              dump("for Notice to Proceed");
            } else if ($plan->contract_signing == null && $plan->award_notice != null) {
              dump("for Contract Preparation");
            } else if ($plan->award_notice == null && $plan->post_qual != null) {
              dump("for Notice of Award");
            } else if ($plan->post_qual == null && $plan->bid_evaluation != null) {
              dump("for Post Qualification");
            } else if ($plan->bid_evaluation == null) {
              dump("No Bidders");
            } else {
              dd($plan);
            }
          }

          dd("end");
        } else {
          $message = "type_error";
        }
      } else {
        $message = "file_error";
      }
    } else {
      $message = "missing_attachments";
    }
  }

  public function submitFixAPP(Request $request)
  {
    if ($request->file('file') !== null) {
      if ($request->file('file')->isValid()) {
        $filename = $request->file('file')->getClientOriginalName();
        $pieces = explode(".", $filename);
        $last_index = count($pieces) - 1;
        if ($pieces[$last_index] == "xlsx" || $pieces[$last_index] == "xlsb" || $pieces[$last_index] == "xlsm") {
          $path = $request->file('file')->getRealPath();
          $collection = (new FastExcel)->sheet(1)->import($path);
          $duplicates = false;
          foreach ($collection as $row) {
            if (getType($row['sub_open']) !== "object") {
              dd($row);
            }

            $abc_post_date = $row['ads_post']->format('Y-m-d');
            $sub_open_date = $row['sub_open']->format('Y-m-d');
            $award_notice_date = $row['noa']->format('Y-m-d');
            $contract_signing_date = $row['contract']->format('Y-m-d');

            $duplicate = DB::table('project_plans')->where([['project_no', $row['project_number']], ['project_type', $row['project_type']]])->get();
            if (count($duplicate) > 1 && $row['project_number'] !== "SEF 21014") {
              dd($duplicate);
            }
          }
        } else {
          $message = "type_error";
        }
      } else {
        $message = "file_error";
      }
    } else {
      $message = "missing_attachments";
    }

    return $message;
  }


  public function submitImportAPP(Request $request)
  {

    if ($request->file('file')->isValid()) {
      $filename = $request->file('file')->getClientOriginalName();
      $pieces = explode(".", $filename);
      $last_index = count($pieces) - 1;
      if ($pieces[$last_index] == "xlsx" || $pieces[$last_index] == "xlsb" || $pieces[$last_index] == "xlsm") {
        $path = $request->file('file')->getRealPath();
        $sheets = (new FastExcel)->importSheets($path);
        $duplicate_data = [];
        foreach ($sheets as $key => $sheet) {
          foreach ($sheet as $number => $row) {
            $row = (object) $row;
            $ads_post = date_format($row->ads_post, "Y-m-d");
            $sub_open = date_format($row->sub_open, "Y-m-d");
            $noa = date_format($row->noa, "Y-m-d");
            $contract = date_format($row->contract, "Y-m-d");
            $parent_id = null;
            $pow_ready = false;
            $date_pow_added = null;
            $pow_date_edited = null;
            $app_group_no = null;
            $duration = null;
            if ($row->abc > 1000000) {
              $mode = 1;
            } else {
              $mode = 2;
            }

            $duplicate = Project::where([['project_no', $row->project_number], ['abc_post_date', $ads_post], ['mode_id', $mode], ['sub_open_date', $sub_open], ['award_notice_date', $noa], ['contract_signing_date', $contract]])->count();
            if ($duplicate === 0) {
              $fund = DB::table('funds')->where('source', $row->source)->first();
              if ($fund == null) {
                if (strpos($row->source, "PDF") >= 0  && strpos($row->source, "PDF") !== false) {
                  $fund_category_id = 2;
                } else if (strpos($row->source, "GF") >= 0  && strpos($row->source, "GF") !== false) {
                  $fund_category_id = 3;
                } else if (strpos($row->source, "LDRRMF") >= 0  && strpos($row->source, "LDRRMF") !== false) {
                  $fund_category_id = 4;
                } else if (strpos($row->source, "SEF") >= 0  && strpos($row->source, "SEF") !== false) {
                  $fund_category_id = 5;
                } else if (strpos($row->source, "LGSF") >= 0  && strpos($row->source, "LGSF") !== false) {
                  $fund_category_id = 6;
                } else if (strpos($row->source, "DOH") >= 0  && strpos($row->source, "DOH") !== false) {
                  $fund_category_id = 7;
                } else if (strpos($row->source, "CMGP") >= 0  && strpos($row->source, "CMGP") !== false) {
                  $fund_category_id = 8;
                } else {
                  dd("Please Create source " . $row->source . " And Reload this Page (CTRL+R)");
                }
                $fund = Fund::create([
                  "source" => $row->source,
                  "fund_category_id" => $fund_category_id,
                  "status" => "active"
                ]);
              }
              if ($row->co !== null) {
                $account = 2;
              } else {
                $account = 1;
              }
              $input_array = [];
              $mun = str_replace(" ", "", strtolower($row->municipality));
              if ($mun === "latrinidad") {
                $mun = "la trinidad";
              }
              $municipality = DB::table('municipalities')->where('municipality_name', ucwords($mun))->first();
              if ($municipality == null) {
                // dd($row);
                dd("Please edit " . ucwords($row->municipality));
              }
              $rebid_count = 0;
              if ($row->project_type === "supplemental") {
                $parent = Project::where([['project_no', $row->project_number]])->orWhere([['project_title', $row->project_title]])->orderBy('plan_id', 'desc')->first();
                if ($parent !== null) {
                  $parent_id = $parent->plan_id;
                  $edit_parent = Project::find($parent->plan_id);
                  $edit_parent->is_old = true;
                  $edit_parent->save();
                  $rebid_count = $parent->re_bid_count;
                  $pow_ready = $parent->pow_ready;
                  $date_pow_added = $parent->date_pow_added;
                  $pow_date_edited = $parent->pow_date_edited;
                  $duration = $parent->duration;
                }
              }


              $project_plan = Project::create([
                'app_group_no' => $row->app_number,
                'project_no' => $row->project_number,
                'account_code' => $row->account_code,
                'project_title' => $row->project_title,
                'project_year' => $row->project_year,
                'year_funded' => $row->year_funded,
                'project_type' => $row->project_type,
                "date_added" => date('Y-m-d'),
                "municipality_id" => $municipality->municipality_id,
                "projtype_id" => "1",
                "mode_id" => $mode,
                "fund_id" => $fund->fund_id,
                'account_id' => $account,
                'abc' => $row->abc,
                'abc_post_date' => $ads_post,
                'sub_open_date' => $sub_open,
                'award_notice_date' => $noa,
                'contract_signing_date' => $contract,
                'status' => "pending",
                're_bid_count' => $rebid_count,
                'pow_ready' => $pow_ready,
                'date_pow_added' => $date_pow_added,
                'pow_date_edited' => $pow_date_edited,
                'duration' => $duration,
                'remarks' => $row->remarks,
                'is_old' => false,
                'parent_id' => $parent_id
              ]);


              $procact = Procact::create(
                ["plan_id" => $project_plan->plan_id, 'procact_mode_id' => $mode]
              );

              $project_plan->latest_procact_id = $procact->procact_id;
              $project_plan->save();


              ProjectTimeline::create([
                "plan_id" => $project_plan->plan_id,
                "procact_id" => $procact->procact_id,
                "timeline_status" => "pending",
                'created_at' => now(),
                'updated_at' => now()
              ]);

              if ($project_plan->abc > 5000000 && $project_plan->mode_id == 1) {
                DB::table('project_activity_status')->insert([
                  "plan_id" => $project_plan->plan_id, "procact_id" => $procact->procact_id, "pre_proc" => "pending", 'created_at' => now(), 'created_at' => now()
                ]);
              } else {
                DB::table('project_activity_status')->insert([
                  "plan_id" => $project_plan->plan_id, "procact_id" => $procact->procact_id, "pre_proc" => "not_needed", 'created_at' => now(), 'created_at' => now()
                ]);
              }
            } else {
              $id = Project::where([['project_no', $row->project_number], ['abc_post_date', $ads_post], ['mode_id', $mode], ['sub_open_date', $sub_open], ['award_notice_date', $noa], ['contract_signing_date', $contract]])->orderBy('plan_id', 'desc')->first()->plan_id;
              Project::where("plan_id", $id)->update([
                'app_group_no' => $row->app_number,
                'remarks' => $row->remarks,
                'abc' => $row->abc,
              ]);
            }
          }
        }
        $message = "success";
      } else {
        $message = "wrong_file_format";
      }
    } else {
      $message = "error";
    }

    return back()->with("message", $message);
  }

  public function importContractors()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view('admin.import_contractors', ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function submitImportContractors(Request $request)
  {

    if ($request->file('file')->isValid()) {
      $filename = $request->file('file')->getClientOriginalName();
      $pieces = explode(".", $filename);
      $last_index = count($pieces) - 1;
      if ($pieces[$last_index] == "xlsx" || $pieces[$last_index] == "xlsb" || $pieces[$last_index] == "xlsm") {
        $path = $request->file('file')->getRealPath();
        $collection = (new FastExcel)->sheet(1)->import($path);
        $duplicates = false;

        foreach ($collection as $row) {
          $count = DB::table("contractors")->where("business_name", $row['business_name'])->count();
          if ($count > 0) {
          } else {
            DB::table('contractors')->insert(
              [
                'business_name' => $row['business_name'],
                'owner' => $row['owner'],
                'address' => $row['address'],
                'contact_number' => $row['contact_number'],
                'position' => $row['position'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
              ]
            );
          }
        }
      } else {
        $message = "wrong_file_format";
      }
    }
  }


  public function submitImportLinks(Request $request)
  {

    if ($request->file('file')->isValid()) {
      $filename = $request->file('file')->getClientOriginalName();
      $pieces = explode(".", $filename);
      $last_index = count($pieces) - 1;
      if ($pieces[$last_index] == "xlsx" || $pieces[$last_index] == "xlsb" || $pieces[$last_index] == "xlsm") {
        $path = $request->file('file')->getRealPath();
        $sheets = (new FastExcel)->importSheets($path);
        $duplicate_data = [];
        foreach ($sheets as $key => $sheet) {
          if ($key === 0) {
            foreach ($sheet as $number => $row) {
              $row = (object)$row;
              $link = Link::where('link_route', $row->link_route)->first();
              if ($link === null) {
                $link = Link::create([
                  "link_order" => $row->link_order,
                  "link_route" => $row->link_route,
                  "link_name" => $row->link_name,
                  "parent_name" => $row->parent_name,
                  "link_icon" => $row->link_icon,
                  "link_type" => $row->link_type,

                ]);

                $privileges = explode(',', $row->privilege);
                foreach ($privileges as $privilege) {
                  LinkPrivilege::create([
                    "link_id" => $link->id,
                    "privilege" => $privilege
                  ]);
                }
              }
              $linkEdit = Link::find($link->id);
              $linkEdit->link_order = $row->link_order;
              $linkEdit->save();
              $link = Link::firstOrCreate([
                "link_order" => $row->link_order,
                "link_route" => $row->link_route,
                "link_name" => $row->link_name,
                "parent_name" => $row->parent_name,
                "link_icon" => $row->link_icon,
                "link_type" => $row->link_type,
              ]);

              // $privileges=explode(',',$row->privilege);
              // foreach($privileges as $privilege){
              //   LinkPrivilege::create([
              //     "link_id"=>$link->id,
              //     "privilege"=>$privilege
              //   ]);
              // }

            }
          }
        }
        $message = "success";
      } else {
        $message = "wrong_file_format";
      }
    } else {
      $message = "error";
    }

    return back()->with("message", $message);
  }
}
