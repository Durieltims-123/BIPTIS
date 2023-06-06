<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\ProcurementController;
use App\APP;
use Validator;

class BACController extends Controller
{
  public function getMembers()
  {
    $members = DB::table('member')->orderBy('member_id', 'desc')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('admin.members', ['links' => $links, 'user_privilege' => $user_privilege, 'members' => $members]);
  }

  public function getBAC()
  {

    $bac = DB::table('bids_and_awards_committee')
      ->select(
        'bids_and_awards_committee.*',
        DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
        DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
        DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
        DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
        DB::raw("CONCAT(bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname) AS bac_twg_chairman_name"),
        DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
      )
      ->join('member as bac_ch', 'bac_ch.member_id', '=', 'bids_and_awards_committee.bac_chairman')
      ->join('member as bac_vice_ch', 'bac_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_vice_chairman')
      ->join('member as bac_sec_ch', 'bac_sec_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_chairman')
      ->join('member as bac_sec_vice_ch', 'bac_sec_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_vice_chairman')
      ->join('member as bac_twg_ch', 'bac_twg_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_chairman')
      ->join('member as bac_twg_vice_ch', 'bac_twg_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_vice_chairman')
      ->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('admin.bac', ['links' => $links, 'user_privilege' => $user_privilege, 'bac' => $bac]);
  }


  public function editBAC($id)
  {
    $user_privilege = getUserPrivilegeByLink('bids_and_awards_committee');
    $access = checkUserAccess('update', $user_privilege);
    $bac = DB::table('bids_and_awards_committee')->where('bac_id', $id)
      ->select(
        'bids_and_awards_committee.*',
        'bids_and_awards_committee.bac_start_date',
        DB::raw("DATE_FORMAT(bac_start_date,'%m/%d/%Y') as bac_start_date"),
        DB::raw("DATE_FORMAT(bac_end_date,'%m/%d/%Y') as bac_end_date"),
        DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
        DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
        DB::raw("CONCAT(bac_alt_vice_ch.member_fname,' ',if(bac_alt_vice_ch.member_minitial is null ,'',bac_alt_vice_ch.member_minitial),' ',bac_alt_vice_ch.member_lname) AS bac_alt_vice_chairman_name"),
        DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
        DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
        DB::raw("CONCAT(bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname) AS bac_twg_chairman_name"),
        DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
      )
      ->leftJoin('member as bac_ch', 'bac_ch.member_id', '=', 'bids_and_awards_committee.bac_chairman')
      ->leftJoin('member as bac_vice_ch', 'bac_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_vice_chairman')
      ->leftJoin('member as bac_alt_vice_ch', 'bac_alt_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_alternate_vice_chairman')
      ->leftJoin('member as bac_sec_ch', 'bac_sec_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_chairman')
      ->leftJoin('member as bac_sec_vice_ch', 'bac_sec_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_vice_chairman')
      ->leftJoin('member as bac_twg_ch', 'bac_twg_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_chairman')
      ->leftJoin('member as bac_twg_vice_ch', 'bac_twg_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_vice_chairman')
      ->get();

    $bac_members = DB::table('bac_member')->where('bac_id', $id)
      ->select('bac_member.*', DB::raw("CONCAT(member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"))
      ->join('member', 'member.member_id', '=', 'bac_member.member_id')
      ->orderBy('bac_member_arrangement', 'asc')
      ->get();

    $bac_observers = DB::table('bac_observer')->where('bac_id', $id)
      ->select('observer.*', DB::raw("CONCAT(observer.observer_fname,' ',if(observer.observer_minitial is null ,'',observer.observer_minitial),' ',observer.observer_lname) AS observer_name"))
      ->join('observer', 'observer.observer_id', '=', 'bac_observer.observer_id')
      ->get();

    $links = getUserLinks();

    return view('admin.bac_form', ['links' => $links, 'user_privilege' => $user_privilege, "title" => "UPDATE BIDS AND AWARDS COMMITTEE", "bac" => $bac, "members" => $bac_members, "observers" => $bac_observers]);
  }

  public function addBAC()
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('bids_and_awards_committee');
    $access = checkUserAccess('update', $user_privilege);

    return view('admin.bac_form', ['links' => $links, 'user_privilege' => $user_privilege, "title" => "ADD BIDS AND AWARDS COMMITTEE", "bac" => null, "members" => null, "observers" => null]);
  }

  public function submitBAC(Request $request)
  {
    $data = $request->validate([
      "start_date" => "required",
      "bac_chairman" => "required",
      "bac_vice_chairman" => "required",
      "bac_secretariat_chairman" => "required",
      "bac_secretariat_vice_chairman" => "required",
      "bac_twg_chairman" => "required",
      "bac_twg_vice_chairman" => "required",
      "bac_twg_members_id" => "required",
      "bac_infra_members_id" => "required",
      "bac_sec_members_id" => "required",
      "bac_support_members_id" => "required",
      "observers_id" => "required",
    ]);

    if ($request->end_date != null) {
      $bac_end_date = date("Y-m-d", strtotime($request->end_date));
    } else {
      $bac_end_date = null;
    }

    if ($request->bac_id == null) {
      // ADD
      $duplicate = DB::table('bids_and_awards_committee')
        ->where([
          ["bac_start_date", date("Y-m-d", strtotime($request->start_date))],
          ["bac_chairman", $request->bac_chairman_id],
          ["bac_vice_chairman", $request->bac_vice_chairman_id],
          ["bac_alternate_vice_chairman", $request->bac_alternate_vice_chairman_id],
          ["bac_sec_chairman", $request->bac_secretariat_chairman_id],
          ["bac_sec_vice_chairman", $request->bac_secretariat_vice_chairman_id],
          ["bac_twg_chairman", $request->bac_twg_chairman_id],
          ["bac_twg_vice_chairman", $request->bac_twg_vice_chairman_id]
        ])->count();

      if ($duplicate == 0) {

        $insert = DB::table('bids_and_awards_committee')->insert([
          "bac_chairman" => $request->bac_chairman_id,
          "bac_vice_chairman" => $request->bac_vice_chairman_id,
          "bac_alternate_vice_chairman" => $request->bac_alternate_vice_chairman_id,
          "bac_sec_chairman" => $request->bac_secretariat_chairman_id,
          "bac_sec_vice_chairman" => $request->bac_secretariat_vice_chairman_id,
          "bac_twg_chairman" => $request->bac_twg_chairman_id,
          "bac_twg_vice_chairman" => $request->bac_twg_vice_chairman_id,
          "bac_start_date" => date("Y-m-d", strtotime($request->start_date)),
          "bac_end_date" => $bac_end_date,
          "created_at" => now(),
          "updated_at" => now(),
        ]);

        // latest bac
        $bac = DB::table('bids_and_awards_committee')->latest()->first();

        // BAC INFRA MEMBERS
        $members_array = [];
        $bac_observers_array = [];
        $bac_infra_members_array = explode(',', $request->bac_infra_members_id);
        $bac_sec_members_array = explode(',', $request->bac_sec_members_id);
        $bac_support_members_array = explode(',', $request->bac_support_members_id);
        $bac_twg_members_array = explode(',', $request->bac_twg_members_id);
        $observers_array = explode(',', $request->observers_id);

        $count = 1;
        foreach ($bac_infra_members_array as $key => $value) {
          $member = ["bac_id" => $bac->bac_id, "member_id" => (int)$value, "bac_member_arrangement" => $count, "bac_member_type" => 'BAC Infrastructure Member', "created_at" => now(), "updated_at" => now()];
          array_push($members_array, $member);
          $count++;
        }

        $count = 1;
        foreach ($bac_sec_members_array as $key => $value) {
          $member = ["bac_id" => $bac->bac_id, "member_id" => (int)$value, "bac_member_arrangement" => $count, "bac_member_type" => 'BAC Secretariat Member', "created_at" => now(), "updated_at" => now()];
          array_push($members_array, $member);
          $count++;
        }

        $count = 1;
        foreach ($bac_support_members_array as $key => $value) {
          $member = ["bac_id" => $bac->bac_id, "member_id" => (int)$value, "bac_member_arrangement" => $count, "bac_member_type" => 'BAC Support Member', "created_at" => now(), "updated_at" => now()];
          array_push($members_array, $member);
          $count++;
        }

        $count = 1;
        foreach ($bac_twg_members_array as $key => $value) {
          $member = ["bac_id" => $bac->bac_id, "member_id" => (int)$value, "bac_member_arrangement" => $count, "bac_member_type" => 'BAC Technical Working Group Member', "created_at" => now(), "updated_at" => now()];
          array_push($members_array, $member);
          $count++;
        }

        $count = 1;
        foreach ($observers_array as $key => $value) {
          $observer = ["bac_id" => $bac->bac_id, "observer_id" => (int)$value, "bac_observer_arrangement" => $count, "created_at" => now(), "updated_at" => now()];
          array_push($bac_observers_array, $observer);
          $count++;
        }

        $insert_members = DB::table('bac_member')->insert($members_array);
        $insert_observers = DB::table('bac_observer')->insert($bac_observers_array);
        return back()->withInput()->with(['message' => "success"]);
      } else {
        return back()->withInput()->with(['message' => "duplicate"]);
      }
    } else {
      // Update
      $duplicate = DB::table('bids_and_awards_committee')
        ->where([
          ["bac_start_date", date("Y-m-d", strtotime($request->start_date))],
          ["bac_chairman", $request->bac_chairman_id],
          ["bac_vice_chairman", $request->bac_vice_chairman_id],
          ["bac_alternate_vice_chairman", $request->bac_alternate_vice_chairman_id],
          ["bac_sec_chairman", $request->bac_secretariat_chairman_id],
          ["bac_sec_vice_chairman", $request->bac_secretariat_vice_chairman_id],
          ["bac_twg_chairman", $request->bac_twg_chairman_id],
          ["bac_twg_vice_chairman", $request->bac_twg_vice_chairman_id],
          ["bac_id", "<>", $request->bac_id]
        ])->count();

      if ($duplicate == 0) {

        $update = DB::table('bids_and_awards_committee')
          ->where('bac_id', $request->bac_id)
          ->update([
            "bac_chairman" => $request->bac_chairman_id,
            "bac_vice_chairman" => $request->bac_vice_chairman_id,
            "bac_alternate_vice_chairman" => $request->bac_alternate_vice_chairman_id,
            "bac_sec_chairman" => $request->bac_secretariat_chairman_id,
            "bac_sec_vice_chairman" => $request->bac_secretariat_vice_chairman_id,
            "bac_twg_chairman" => $request->bac_twg_chairman_id,
            "bac_twg_vice_chairman" => $request->bac_twg_vice_chairman_id,
            "bac_start_date" => date("Y-m-d", strtotime($request->start_date)),
            "bac_end_date" => $bac_end_date,
            "updated_at" => now(),
          ]);

        // BAC
        $bac = DB::table('bids_and_awards_committee')->where('bac_id', $request->bac_id)->first();
        $delete_members = DB::table('bac_member')->where('bac_id', $request->bac_id)->delete();
        $delete_observers = DB::table('bac_observer')->where('bac_id', $request->bac_id)->delete();

        // BAC INFRA MEMBERS
        $members_array = [];
        $bac_observers_array = [];
        $bac_infra_members_array = explode(',', $request->bac_infra_members_id);
        $bac_sec_members_array = explode(',', $request->bac_sec_members_id);
        $bac_support_members_array = explode(',', $request->bac_support_members_id);
        $bac_twg_members_array = explode(',', $request->bac_twg_members_id);
        $observers_array = explode(',', $request->observers_id);

        $count = 1;
        foreach ($bac_infra_members_array as $key => $value) {
          $member = ["bac_id" => $bac->bac_id, "member_id" => (int)$value, "bac_member_arrangement" => $count, "bac_member_type" => 'BAC Infrastructure Member', "created_at" => now(), "updated_at" => now()];
          array_push($members_array, $member);
          $count++;
        }

        $count = 1;
        foreach ($bac_sec_members_array as $key => $value) {
          $member = ["bac_id" => $bac->bac_id, "member_id" => (int)$value, "bac_member_arrangement" => $count, "bac_member_type" => 'BAC Secretariat Member', "created_at" => now(), "updated_at" => now()];
          array_push($members_array, $member);
          $count++;
        }

        $count = 1;
        foreach ($bac_support_members_array as $key => $value) {
          $member = ["bac_id" => $bac->bac_id, "member_id" => (int)$value, "bac_member_arrangement" => $count, "bac_member_type" => 'BAC Support Member', "created_at" => now(), "updated_at" => now()];
          array_push($members_array, $member);
          $count++;
        }

        $count = 1;
        foreach ($bac_twg_members_array as $key => $value) {
          $member = ["bac_id" => $bac->bac_id, "member_id" => (int)$value, "bac_member_arrangement" => $count, "bac_member_type" => 'BAC Technical Working Group Member', "created_at" => now(), "updated_at" => now()];
          array_push($members_array, $member);
          $count++;
        }

        $count = 1;
        foreach ($observers_array as $key => $value) {
          $observer = ["bac_id" => $bac->bac_id, "observer_id" => (int)$value, "bac_observer_arrangement" => $count, "created_at" => now(), "updated_at" => now()];
          array_push($bac_observers_array, $observer);
          $count++;
        }

        $insert_members = DB::table('bac_member')->insert($members_array);
        $insert_observers = DB::table('bac_observer')->insert($bac_observers_array);
        return back()->withInput()->with(['message' => "success"]);
      } else {
        return back()->withInput()->with(['message' => "duplicate"]);
      }
    }
    // return back()->withInput();


    // dd("in");
  }

  public function submitMember(Request $request)
  {
    $data = $request->validate([
      "first_name" => "required",
      "last_name" => "required",
      "prefix" => "required"
    ]);

    if ($request->member_id === null) {
      // ADD
      $duplicate = DB::table('member')->where([["member_fname", $request->first_name], ["member_minitial", $request->middle_initial], ["member_lname", $request->last_name]])->first();
      if ($duplicate == null) {
        DB::table('member')->insert([
          "member_prefix" => $request->prefix,
          "member_fname" => $request->first_name,
          "member_minitial" => $request->middle_initial,
          "member_lname" => $request->last_name,
          "member_suffix" => $request->suffix,
          "created_at" => now(),
          "updated_at" => now(),
        ]);
        return back()->with("message", "success");
      } else {
        return back()->withInput()->with("message", "duplicate");
      }
    } else {
      $duplicate = DB::table('member')->where([["member_id", "<>", $request->member_id], ["member_minitial", $request->middle_initial], ["member_lname", $request->last_name]])->first();
      if ($duplicate == null) {
        DB::table('member')->where("member_id", $request->member_id)->update([
          "member_prefix" => $request->prefix,
          "member_fname" => $request->first_name,
          "member_minitial" => $request->middle_initial,
          "member_lname" => $request->last_name,
          "member_suffix" => $request->suffix,
          "updated_at" => now(),
        ]);
        return back()->with("message", "success");
      } else {
        return back()->withInput()->with("message", "duplicate");
      }
    }
  }

  public function getObservers()
  {
    $observers = DB::table('observer')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('admin.observers', ['links' => $links, 'user_privilege' => $user_privilege, 'observers' => $observers]);
  }

  public function submitObserver(Request $request)
  {
    $data = $request->validate([
      "office" => "required",
    ]);

    if ($request->observer_id === null) {
      // ADD
      $duplicate = DB::table('observer')->where([["observer_office", $request->office], ["observer_fname", $request->first_name], ["observer_minitial", $request->middle_initial], ["observer_lname", $request->last_name]])->first();
      if ($duplicate == null) {
        DB::table('observer')->insert([
          "observer_office" => $request->office,
          "observer_prefix" => $request->prefix,
          "observer_fname" => $request->first_name,
          "observer_minitial" => $request->middle_initial,
          "observer_lname" => $request->last_name,
          "observer_suffix" => $request->suffix,
          "created_at" => now(),
          "updated_at" => now(),
        ]);
        return back()->with("message", "success");
      } else {
        return back()->withInput()->with("message", "duplicate");
      }
    } else {
      $duplicate = DB::table('observer')->where([["observer_office", $request->office], ["observer_id", "<>", $request->observer_id], ["observer_minitial", $request->middle_initial], ["observer_lname", $request->last_name]])->first();
      if ($duplicate == null) {
        DB::table('observer')->where("observer_id", $request->observer_id)->update([
          "observer_office" => $request->office,
          "observer_prefix" => $request->prefix,
          "observer_fname" => $request->first_name,
          "observer_minitial" => $request->middle_initial,
          "observer_lname" => $request->last_name,
          "observer_suffix" => $request->suffix,
          "updated_at" => now(),
        ]);
        return back()->with("message", "success");
      } else {
        return back()->withInput()->with("message", "duplicate");
      }
    }
  }

  public function autoCompleteMembers(Request $request)
  {
    $term = $request->term;
    $results = array();
    $members = DB::table("member")->select('*', DB::raw("member_id,CONCAT(member_fname,' ',member_lname) as name"), DB::raw("member_id,CONCAT(member_fname,' ',member_minitial,' ',member_lname) as full_name"))->having('name', 'like', '%' . $term . '%')->orHaving('full_name', 'like', '%' . $term . '%')->limit(10)->get();
    if (count($members) > 0) {
      foreach ($members as $member) {
        if ($member->member_minitial != null) {
          $results[] = [
            'id' => $member->member_id,
            'value' => $member->member_fname . " " . $member->member_minitial . " " . $member->member_lname
          ];
        } else {
          $results[] = [
            'id' => $member->member_id,
            'value' => $member->member_fname . " " . $member->member_lname
          ];
        }
      }
    } else {
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }
    return response()->json($results);
  }

  public function autoCompleteObservers(Request $request)
  {
    $term = $request->term;
    $results = array();
    $observers = DB::table("observer")->select('*', DB::raw("CONCAT(observer_fname,' ',observer_lname) as name"), DB::raw("CONCAT(observer_office,' ',observer_office) as office"), DB::raw("CONCAT(observer_fname,' ',observer_minitial,' ',observer_lname) as full_name"))->having('name', 'like', '%' . $term . '%')->orHaving('full_name', 'like', '%' . $term . '%')->orHaving('office', 'like', '%' . $term . '%')->limit(10)->get();
    if (count($observers) > 0) {
      foreach ($observers as $observer) {
        if ($observer->observer_fname == null) {
          $results[] = [
            'id' => $observer->observer_id,
            'value' => $observer->observer_office . " Representative"
          ];
        } else {
          if ($observer->observer_minitial != null) {
            $results[] = [
              'id' => $observer->observer_id,
              'value' => $observer->observer_fname . " " . $observer->observer_minitial . " " . $observer->observer_lname
            ];
          } else {
            $results[] = [
              'id' => $observer->observer_id,
              'value' => $observer->observer_fname . " " . $observer->observer_lname
            ];
          }
        }
      }
    } else {
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }
    return response()->json($results);
  }

  public function  deleteMember($id)
  {
    $count = DB::table('bac_member')->where('member_id', $id)->count();
    if ($count > 0) {
      return back()->with("message", "delete_error");
    } else {
      DB::table('member')->where('member_id', $id)->delete();
      return back()->with('message', 'delete_success');
    }
  }
  public function  deleteObserver($id)
  {
    $count = DB::table('bac_observer')->where('observer_id', $id)->count();
    if ($count > 0) {
      return back()->with("message", "delete_error");
    } else {
      DB::table('observer')->where('observer_id', $id)->delete();
      return back()->with('message', 'delete_success');
    }
  }

  public function  deleteBAC($id)
  {

    return back()->with("message", "delete_error");
  }
}
