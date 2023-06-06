<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\{APP, ArchiveNoticeOfMeetingAttachments};
use Validator;
use PhpOffice\PhpWord\Element\Field;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MeetingController extends Controller
{

  public function getMeetingRooms()
  {
    $year = date('Y');
    $meeting_rooms = DB::table("meeting_room")->get();
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view('admin.meeting_room', ['links' => $links, 'user_privilege' => $user_privilege, "year" => $year, "meeting_rooms" => $meeting_rooms]);
  }

  public function submitMeetingRoom(Request $request)
  {
    $data = $request->validate([
      "address" => "required",
      "status" => "required",
    ]);

    $meeting_room_id = $request->meeting_room_id;
    $address = $request->address;
    $status = $request->status;
    $message = "success";

    // Add
    if ($meeting_room_id === null) {
      $duplicate = DB::table("meeting_room")->where('address', $address)->count();
      if ($duplicate > 0) {
        $message = "duplicate";
      } else {
        DB::table("meeting_room")->insert([
          "address" => $address,
          "status" => $status
        ]);
      }
    }
    // Update
    else {
      $duplicate = DB::table("meeting_room")->where([['address', $address], ["meeting_room_id", "<>", $meeting_room_id]])->count();
      if ($duplicate > 0) {
        $message = "duplicate";
      } else {
        DB::table("meeting_room")
          ->where("meeting_room_id", $meeting_room_id)
          ->update([
            "address" => $address,
            "status" => $status
          ]);
      }
    }

    return back()->with("message", $message);
  }

  public function deleteMeetingRoom($meeting_room_id)
  {
    $message = "delete_success";
    $linked_meetings = DB::table("meeting")->where("meeting_room_id", $meeting_room_id)->count();
    if ($linked_meetings > 0) {
      $message = "delete_error";
    } else {
      DB::table("meeting_room")->where("meeting_room_id", $meeting_room_id)->delete();
    }
    return back()->with("message", $message);
  }
  public function getMeetings(Request $request)
  {
    $year = Date('Y');
    $meetings = DB::table("meeting")
      ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
      ->where('meeting.meeting_date', "like", $year . "%")
      ->orderBy("meeting_id", "desc")
      ->get();
    $meeting_rooms = DB::table("meeting_room")->get();
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view('admin.meeting', ['links' => $links, 'user_privilege' => $user_privilege, 'meetings' => $meetings, "year" => $year, "meeting_rooms" => $meeting_rooms]);
  }

  public function filterMeetings(Request $request)
  {
    $year = $request->year;
    $meeting_date = $request->filter_meeting_date;
    $meetings = DB::table("meeting")
      ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
      ->where('meeting.meeting_date', "like", $year . "%");
    if ($meeting_date != null) {
      $meetings = $meetings->where('meeting.meeting_date', date('Y-m-d', strtotime($meeting_date)));
    }
    $meetings = $meetings->orderBy("meeting_id", "desc")
      ->get();

    return back()->withInput()->with("meetings", $meetings);
  }

  public function submitMeeting(Request $request)
  {
    $data = $request->validate([
      "date_created" => "required",
      "meeting_date" => "required|after:date_created",
      "meeting_time" => "required",
      "meeting_room" => "required"
    ]);

    $meeting_date = date("Y-m-d", strtotime($request->meeting_date));
    $date_created = date("Y-m-d", strtotime($request->date_created));
    $message = "success";
    $meeting_date_validate = DB::table('project_timelines')
      ->where('pre_bid_start', $meeting_date)
      ->orWhere('pre_proc_date', $meeting_date)
      ->orWhere('bid_submission_start', $meeting_date)
      ->count();

    //
    // if($meeting_date_validate>0){

    $bac = DB::table('bids_and_awards_committee')->latest()->first();
    if ($bac != null) {
      $bac_id = $bac->bac_id;
    } else {
      $bac_id = null;
    }
    // ADD
    if ($request->meeting_id === null) {
      $duplicate = DB::table("meeting")->where('meeting_date', $meeting_date)->count();
      if ($duplicate > 0) {
        $message = "duplicate";
      } else {
        $insert = DB::table('meeting')->insert([
          "meeting_date_created" => $date_created,
          "meeting_date" => $meeting_date,
          "meeting_time" => $request->meeting_time,
          "meeting_room_id" => $request->meeting_room,
          "bac_id" => $bac_id,
          "created_at" => now(),
          "updated_at" => now()
        ]);
      }
    } else {
      $duplicate = DB::table("meeting")->where([['meeting_date', $meeting_date], ["meeting_id", "<>", $request->meeting_id]])->count();
      if ($duplicate > 0) {
        $message = "duplicate";
      } else {
        $insert = DB::table('meeting')
          ->where("meeting_id", $request->meeting_id)
          ->update([
            "meeting_date_created" => $date_created,
            "meeting_date" => $meeting_date,
            "meeting_time" => $request->meeting_time,
            "meeting_room_id" => $request->meeting_room,
            "bac_id" => $bac_id,
            "updated_at" => now()
          ]);
      }
    }

    // }
    // else{
    //   $message="no_activities";
    // }
    return back()->with("message", $message);
  }

  public function deleteMeeting($meeting_id)
  {
    $linked_attachment = ArchiveNoticeOfMeetingAttachments::where('meeting_id', $meeting_id)->count();
    if ($linked_attachment > 0) {
      return back()->with("message", "delete_error");
    } else {
      DB::table("meeting")->where("meeting_id", $meeting_id)->delete();
      return back()->with("message", "delete_success");
    }
  }

  public function downloadNoticeOfMeeting($meeting_id)
  {
    $APP = new APP;
    $meeting = DB::table("meeting")
      ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
      ->where('meeting.meeting_id', $meeting_id)
      ->first();

    $bac = DB::table('bids_and_awards_committee')
      ->where('bac_id', $meeting->bac_id)
      ->select(
        'bids_and_awards_committee.*',
        DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
        DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
        DB::raw("CONCAT(bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
        DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
        DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
        DB::raw("CONCAT(bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname) AS bac_twg_chairman_name"),
        DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
      )
      ->join('member as bac_ch', 'bac_ch.member_id', '=', 'bids_and_awards_committee.bac_chairman')
      ->join('member as bac_vice_ch', 'bac_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_vice_chairman')
      ->leftJoin('member as bac_alternate_vice_ch', 'bac_alternate_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_alternate_vice_chairman')
      ->join('member as bac_sec_ch', 'bac_sec_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_chairman')
      ->join('member as bac_sec_vice_ch', 'bac_sec_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_vice_chairman')
      ->join('member as bac_twg_ch', 'bac_twg_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_chairman')
      ->join('member as bac_twg_vice_ch', 'bac_twg_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_vice_chairman')
      ->first();


    $bac_infra_members = DB::table('bac_member')->where('bac_id', $meeting->bac_id)
      ->select(DB::raw("CONCAT(member.member_prefix,' ',member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"))
      ->where('bac_member.bac_member_type', 'BAC Infrastructure Member')
      ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();

    $bac_sec_members = DB::table('bac_member')->where('bac_id', $meeting->bac_id)
      ->select(DB::raw("CONCAT(member.member_prefix,' ',member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"))
      ->where('bac_member.bac_member_type', 'BAC Secretariat Member')
      ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();

    $bac_support_members = DB::table('bac_member')->where('bac_id', $meeting->bac_id)
      ->select(DB::raw("CONCAT(member.member_prefix,' ',member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"))
      ->where('bac_member.bac_member_type', 'BAC Support Member')
      ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();

    $bac_twg_members = DB::table('bac_member')->where('bac_id', $meeting->bac_id)
      ->select(DB::raw("CONCAT(member.member_prefix,' ',member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"))
      ->where('bac_member.bac_member_type', 'BAC Technical Working Group Member')
      ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();

    $bac_observers = DB::table('bac_observer')->where('bac_id', $meeting->bac_id)
      ->select('observer.*', DB::raw("CONCAT(if(observer.observer_prefix is null ,'',CONCAT(observer.observer_prefix,' ')),observer.observer_fname,' ',if(observer.observer_minitial is null ,'',observer.observer_minitial),' ',observer.observer_lname) AS observer_name"))
      ->join('observer', 'observer.observer_id', '=', 'bac_observer.observer_id')
      ->get();


    $members = "____" . strtoupper(strtolower($bac->bac_vice_chairman_name)) . ", BAC Vice Chairperson<w:br/>";
    if ($bac->bac_alternate_vice_chairman_name != null) {
      $members = $members . "____" . strtoupper(strtolower($bac->bac_alternate_vice_chairman_name)) . ", BAC Vice Chairperson (Alternate)<w:br/>";
    }
    foreach ($bac_infra_members as $member) {
      $members = $members . "____" . strtoupper(strtolower($member->member_name)) . ", Member<w:br/>";
    }

    $secretariat = '____' . strtoupper(strtolower($bac->bac_sec_chairman_name)) . ', BAC Sec. Chairman <w:br/>____' . strtoupper(strtolower($bac->bac_sec_vice_chairman_name)) . ', BAC Sec. Vice-Chairman <w:br/>';

    foreach ($bac_sec_members as $member) {
      $secretariat = $secretariat . "____" . strtoupper(strtolower($member->member_name)) . ", Member<w:br/>";
    }
    foreach ($bac_support_members as $member) {
      $secretariat = $secretariat . "____" . strtoupper(strtolower($member->member_name)) . ", BAC Support<w:br/>";
    }

    $twg = '____' . strtoupper(strtolower($bac->bac_twg_chairman_name)) . ', BAC-TWG Chairman <w:br/>____' . strtoupper(strtolower($bac->bac_twg_vice_chairman_name)) . ', BAC-TWG Vice-Chairman <w:br/>';

    foreach ($bac_twg_members as $member) {
      $twg = $twg . "____" . strtoupper(strtolower($member->member_name)) . ", Member<w:br/>";
    }

    $observers = "";
    foreach ($bac_observers  as $observer) {
      if ($observer->observer_name == null) {
        $observers = $observers . "____" . strtoupper(strtolower($observer->observer_office . " - " . $observer->observer_office)) . " Rep.<w:br/>";
      } else {
        $observers = $observers . "____" . strtoupper(strtolower($observer->observer_name . " - " . $observer->observer_office)) . "<w:br/>";
      }
    }


    $opening_projects = [];
    $desired_array = ["project_plans" => null, "opening_title" => null];
    $numbering = 2;
    $opening_type = 0;

    $date_created = date("F d,Y", strtotime($meeting->meeting_date_created));
    $meeting_date = date("F d,Y", strtotime($meeting->meeting_date));
    $meeting_time = date("h:i a", strtotime(date("Y-m-d ") . $meeting->meeting_time));

    $project_types = DB::table('project_timelines')
      ->select('procacts.procact_mode_id')
      ->where('project_timelines.bid_submission_start', date("Y-m-d", strtotime($meeting->meeting_date)))
      ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->groupBy('procacts.procact_mode_id')
      ->get();

    $project_types_array = [];
    foreach ($project_types as $project_type) {
      array_push($project_types_array, $project_type->procact_mode_id);
    }

    if (in_array(1, $project_types_array) && in_array(2, $project_types_array)) {
      $project_types_new = [];
      foreach ($project_types as $project_type) {
        if ($project_type->procact_mode_id === 1) {
          array_push($project_types_new, (object)["procact_mode_id" => 2]);
        } else if ($project_type->procact_mode_id === 2) {
          array_push($project_types_new, (object)["procact_mode_id" => 1]);
        } else {
          array_push($project_types_new, (object)["procact_mode_id" => $project_type->procact_mode_id]);
        }
      }
      $project_types = $project_types_new;
    }

    foreach ($project_types as $project_type) {
      if ($project_type->procact_mode_id === 1) {
        $projects = DB::table('project_timelines')
          ->where([['project_timelines.bid_submission_start', date("Y-m-d", strtotime($meeting->meeting_date))], ['procact_mode_id', 1]])
          ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
          ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
          ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
          ->join('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
          ->join("funds", "project_plans.fund_id", "funds.fund_id")
          ->orderBy('procacts.itb_arrangement')
          ->get();
        $opening_title = "of the following (number projects-Bidding)";
      }
      if ($project_type->procact_mode_id === 2) {
        $projects = DB::table('project_timelines')
          ->where([['project_timelines.bid_submission_start', date("Y-m-d", strtotime($meeting->meeting_date))], ['procact_mode_id', 2]])
          ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
          ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
          ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
          ->join('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
          ->join("funds", "project_plans.fund_id", "funds.fund_id")
          ->orderBy('procacts.itb_arrangement')
          ->get();
        $opening_title = "of the following (number projects-SVP)";
      }
      if ($project_type->procact_mode_id === 3) {
        $projects = DB::table('project_timelines')
          ->where([['project_timelines.bid_submission_start', date("Y-m-d", strtotime($meeting->meeting_date))], ['procact_mode_id', 3]])
          ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
          ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
          ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
          ->join('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
          ->join("funds", "project_plans.fund_id", "funds.fund_id")
          ->orderBy('procacts.itb_arrangement')
          ->get();
        $opening_title = "through Negotiated Procurement under 2-Failed Biddings(number projects)";
      }

      $project_plans = $APP->itemizeProject($projects);
      if (count($project_plans) === 1) {
        $opening_title = str_replace("projects", "project", $opening_title);
      }
      $opening_title = str_replace("number", count($project_plans), $opening_title);
      $desired_array['project_plans'] = $project_plans;
      $desired_array['opening_title'] = $opening_title;
      array_push($opening_projects, $desired_array);
    }


    // prebid project
    $prebid_projects = DB::table('project_timelines')
      ->where([['project_timelines.pre_bid_start', date("Y-m-d", strtotime($meeting->meeting_date))], ['procact_mode_id', 1]])
      ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
      ->join("funds", "project_plans.fund_id", "funds.fund_id")
      ->orderBy('procacts.itb_arrangement')
      ->get();

    if (count($prebid_projects) > 0) {
      $prebid_projects = $APP->itemizeProject($prebid_projects);
    }

    // pre_procurement project
    $preproc_projects = DB::table('project_timelines')
      ->where([['project_timelines.pre_proc_date', date("Y-m-d", strtotime($meeting->meeting_date))], ['procact_mode_id', 1]])
      ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
      ->join("funds", "project_plans.fund_id", "funds.fund_id")
      ->orderBy('procacts.itb_arrangement')
      ->get();


    if (count($preproc_projects) > 0) {
      $preproc_projects = $APP->itemizeProject($preproc_projects);
    }

    $new_line = new \PhpOffice\PhpWord\Element\PreserveText('</w:t><w:br/><w:t>');
    $TemplateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/Notice of Meeting.docx");
    $TemplateProcessor->setValue("date_created", $date_created);
    $TemplateProcessor->setValue("meeting_date", $meeting_date);
    $TemplateProcessor->setValue("meeting_time", $meeting_time);
    $TemplateProcessor->setValue("place_of_meeting", $meeting->address);
    $TemplateProcessor->setValue("bac_chairman", strtoupper(strtolower($bac->bac_chairman_name)));
    $TemplateProcessor->cloneBlock("opening_block", count($project_types), true, true);
    $number = 2;
    $opening_block_counter = 1;
    foreach ($opening_projects as $opening_project) {
      $i = "opening_item_block#" . $opening_block_counter;
      $item_number = 1;
      $previous_location = "";
      $opening_project = (object)$opening_project;
      $TemplateProcessor->setValue("opening_number#" . $opening_block_counter, $number);
      $TemplateProcessor->setValue("opening_title#" . $opening_block_counter, $opening_project->opening_title);
      $TemplateProcessor->cloneBlock("opening_item_block#" . $opening_block_counter, count($opening_project->project_plans), true, true);
      $items = $opening_project->project_plans;
      foreach ($items as $item) {
        if ($previous_location != $item['location']) {
          $previous_location = $item['location'];
          $municipality = str_replace(',BENGUET', '', $previous_location);
          $TemplateProcessor->setValue("opening_municipality_name#" . $opening_block_counter . "#" . $item_number, $municipality . ":");

          $TemplateProcessor->setValue("opening_new_line#" . $opening_block_counter . "#" . $item_number, "");
        } else {
          $TemplateProcessor->setValue("opening_municipality_name#" . $opening_block_counter . "#" . $item_number, "");
          $TemplateProcessor->setValue("opening_new_line#" . $opening_block_counter . "#" . $item_number, "");
        }
        $TemplateProcessor->setValue("item_number#" . $opening_block_counter . "#" . $item_number, $item_number);
        $TemplateProcessor->setValue("location#" . $opening_block_counter . "#" . $item_number, $item['location']);
        $TemplateProcessor->setValue("project_title#" . $opening_block_counter . "#" . $item_number, htmlspecialchars($item['project_title']));
        $TemplateProcessor->setValue("location#" . $opening_block_counter . "#" . $item_number, $item['location']);
        $TemplateProcessor->setValue("project_number#" . $opening_block_counter . "#" . $item_number, $item['project_number']);
        $TemplateProcessor->setValue("source_of_fund#" . $opening_block_counter . "#" . $item_number, $item['source_of_fund']);
        $TemplateProcessor->setValue("abc#" . $opening_block_counter . "#" . $item_number, $item['abc']);
        $TemplateProcessor->setValue("duration#" . $opening_block_counter . "#" . $item_number, $item['duration']);
        $item_number = $item_number + 1;
      }
      $opening_block_counter = $opening_block_counter + 1;
      $number = $number + 1;
    }

    // pre_bid
    if (count($prebid_projects) > 0) {
      $item_number = 1;
      $previous_location = "";
      $TemplateProcessor->cloneBlock("pre_bid_block", 1, true);
      $pre_bid_title = "of the following (number projects)";
      if (count($prebid_projects) === 1) {
        $pre_bid_title = str_replace("projects", "project", $pre_bid_title);
      }
      $pre_bid_title = str_replace("number", count($prebid_projects), $pre_bid_title);
      $TemplateProcessor->setValue("pre_bid_number", $number);
      $TemplateProcessor->setValue("pre_bid_title", $pre_bid_title);
      $TemplateProcessor->cloneBlock("pre_bid_item_block", count($prebid_projects), true, true);
      foreach ($prebid_projects as $item) {
        if ($previous_location != $item['location']) {
          $previous_location = $item['location'];
          $municipality = str_replace(',BENGUET', '', $previous_location);
          $TemplateProcessor->setValue("pre_bid_municipality_name#" . $item_number, $municipality . ":");

          $TemplateProcessor->setValue("pre_bid_new_line#" . $item_number, "");
        } else {
          $TemplateProcessor->setValue("pre_bid_municipality_name#" . $item_number, "");
          $TemplateProcessor->setValue("pre_bid_new_line#" . $item_number, "");
        }
        $TemplateProcessor->setValue("pre_bid_item_number#" . $item_number, $item_number);
        $TemplateProcessor->setValue("pre_bid_location#" . $item_number, $item['location']);
        $TemplateProcessor->setValue("pre_bid_project_title#" . $item_number, htmlspecialchars($item['project_title']));
        $TemplateProcessor->setValue("pre_bid_location#" . $item_number, $item['location']);
        $TemplateProcessor->setValue("pre_bid_project_number#" . $item_number, $item['project_number']);
        $TemplateProcessor->setValue("pre_bid_source_of_fund#" . $item_number, $item['source_of_fund']);
        $TemplateProcessor->setValue("pre_bid_abc#" . $item_number, $item['abc']);
        $TemplateProcessor->setValue("pre_bid_duration#" . $item_number, $item['duration']);
        $TemplateProcessor->setValue("presenter#" . $item_number, " ".$item['project_engineer']);
        $item_number = $item_number + 1;
      }

      $number = $number + 1;
    } else {
      $TemplateProcessor->cloneBlock("pre_bid_block", 0, true, true);
    }

    // Pre procurement
    if (count($preproc_projects) > 0) {
      $item_number = 1;
      $previous_location = "";
      $TemplateProcessor->cloneBlock("pre_proc_block", 1, true);
      $pre_proc_title = "of the following (number projects)";
      if (count($preproc_projects) === 1) {
        $pre_proc_title = str_replace("projects", "project", $pre_proc_title);
      }
      $pre_proc_title = str_replace("number", count($preproc_projects), $pre_proc_title);
      $TemplateProcessor->setValue("pre_proc_number", $number);
      $TemplateProcessor->setValue("pre_proc_title", $pre_proc_title);
      $TemplateProcessor->cloneBlock("pre_proc_item_block", count($preproc_projects), true, true);

      foreach ($preproc_projects as $item) {
        if ($previous_location != $item['location']) {
          $previous_location = $item['location'];
          $municipality = str_replace(',BENGUET', '', $previous_location);
          $TemplateProcessor->setValue("pre_proc_municipality_name#" . $item_number, $municipality . ":");

          $TemplateProcessor->setValue("pre_proc_new_line#" . $item_number, "");
        } else {
          $TemplateProcessor->setValue("pre_proc_municipality_name#" . $item_number, "");
          $TemplateProcessor->setValue("pre_proc_new_line#" . $item_number, "");
        }
        $TemplateProcessor->setValue("pre_proc_item_number#" . $item_number, $item_number);
        $TemplateProcessor->setValue("pre_proc_item_number#" . $item_number, $item_number);
        $TemplateProcessor->setValue("pre_proc_location#" . $item_number, $item['location']);
        $TemplateProcessor->setValue("pre_proc_project_title#" . $item_number, htmlspecialchars($item['project_title']));
        $TemplateProcessor->setValue("pre_proc_location#" . $item_number, $item['location']);
        $TemplateProcessor->setValue("pre_proc_project_number#" . $item_number, $item['project_number']);
        $TemplateProcessor->setValue("pre_proc_source_of_fund#" . $item_number, $item['source_of_fund']);
        $TemplateProcessor->setValue("pre_proc_abc#" . $item_number, $item['abc']);
        $TemplateProcessor->setValue("pre_proc_duration#" . $item_number, $item['duration']);
        $item_number = $item_number + 1;
      }
      $number = $number + 1;
    } else {
      $TemplateProcessor->cloneBlock("pre_proc_block", 0, true, true);
    }

    $TemplateProcessor->setValue("twg_number", $number);
    $TemplateProcessor->setValue("review_number", ($number + 1));
    $TemplateProcessor->setValue("matters_arising_number", ($number + 2));
    $TemplateProcessor->setValue("other_matters_number", ($number + 3));

    //  get last meeting
    $last_opening_meeting = DB::table('project_timelines')
      ->select('bid_submission_start as date')
      ->where([['project_timelines.bid_submission_start', '<', date("Y-m-d", strtotime($meeting->meeting_date))]])
      ->orderBy('project_timelines.bid_submission_start', 'desc')
      ->first();

    $last_pre_bid_meeting = DB::table('project_timelines')
      ->select('pre_bid_start as date')
      ->where([['project_timelines.pre_bid_start', '<', date("Y-m-d", strtotime($meeting->meeting_date))]])
      ->orderBy('project_timelines.bid_submission_start', 'desc')
      ->first();

    $last_pre_proc_meeting = DB::table('project_timelines')
      ->select('pre_bid_start as date')
      ->where([['project_timelines.pre_proc_date', '<', date("Y-m-d", strtotime($meeting->meeting_date))]])
      ->orderBy('project_timelines.bid_submission_start', 'desc')
      ->first();
    $TemplateProcessor->setValue("members", $members);
    $TemplateProcessor->setValue("secretariat", $secretariat);
    $TemplateProcessor->setValue("twg", $twg);
    $TemplateProcessor->setValue("observers", $observers);
    if (count($opening_projects) > 0) {
      $TemplateProcessor->cloneBlock("note",1, true, true);
    }
    else{
      $TemplateProcessor->cloneBlock("note", 0, true, true);
    }

    if ($last_pre_proc_meeting == null) {
      $meeting_dates = [$last_opening_meeting->date, $last_pre_bid_meeting->date];
    } else {
      $meeting_dates = [$last_opening_meeting->date, $last_pre_bid_meeting->date, $last_pre_proc_meeting->date];
    }
    $TemplateProcessor->setValue("last_meeting", date('F d,Y', strtotime((max($meeting_dates)))));
    $TemplateProcessor->saveAs(public_path() . '\\' . 'word_results/Notice of Meeting-' . $meeting->meeting_date . '.docx');
    return  response()->download(public_path() . '\\' . 'word_results/Notice of Meeting-' . $meeting->meeting_date . '.docx')->deleteFileAfterSend(true);
  }

  public function releaseNoticeOfMeeting($id)
  {
    $user_privilege = getUserPrivilegeByLink('meetings');
    $access = checkUserAccess('update', $user_privilege);

    $meeting = DB::table("meeting")
      ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
      ->where('meeting.meeting_id', $id)
      ->first();


    $bac = DB::table('bids_and_awards_committee')->where('bac_id', $meeting->bac_id)->latest()->first();
    $observers = DB::table('bac_observer')
      ->where([['bac_id', $meeting->bac_id]])
      ->select(DB::raw("CONCAT(if(observer.observer_prefix is null ,'',CONCAT(observer.observer_prefix,' ')),observer.observer_fname,' ',if(observer.observer_minitial is null ,'',observer.observer_minitial),' ',observer.observer_lname) AS observer_name"), 'observer.*')
      ->join('observer', 'observer.observer_id', '=', 'bac_observer.observer_id')
      ->get();

    foreach ($observers as $data) {
      $observer = DB::table('bac_observer')
        ->where([['bac_id', $meeting->bac_id], ['meeting_id', $id], ['meeting_observer.observer_id', $data->observer_id]])
        ->select('observer.*', DB::raw("CONCAT(if(observer.observer_prefix is null ,'',CONCAT(observer.observer_prefix,' ')),observer.observer_fname,' ',if(observer.observer_minitial is null ,'',observer.observer_minitial),' ',observer.observer_lname) AS observer_name"), "meeting_observer.date_received", "meeting_observer.meeting_observer_id")
        ->join('observer', 'observer.observer_id', '=', 'bac_observer.observer_id')
        ->join('meeting_observer', 'observer.observer_id', '=', 'meeting_observer.observer_id')->first();

      if ($observer != null) {
        $meeting_observers[] = $observer;
      } else {
        $data->meeting_observer_id = null;
        $data->meeting_id = null;
        $data->date_received = null;
        $meeting_observers[] = $data;
      }
    }

    $links = getUserLinks();


    return view('admin.release_notice_of_meeting', ['links' => $links, 'user_privilege' => $user_privilege, 'meeting' => $meeting, 'bac' => $bac, 'meeting_observers' => $meeting_observers]);
  }

  public function submitReleaseMeeting(Request $request)
  {
    $meeting = DB::table('meeting')->where('meeting_id', $request->meeting_id)->first();
    $meeting_date = Date('m/d/Y', strtotime($meeting->meeting_date));
    $data = $request->validate([
      "date_received" => "required|before:" . $meeting_date
    ]);
    if ($request->meeting_observer_id === null) {
      $duplicate = DB::table("meeting_observer")->where([['meeting_id', $meeting->meeting_id], ['observer_id', $request->observerf_id]])->count();
      if ($duplicate == 0) {
        DB::table("meeting_observer")->insert([
          "meeting_id" => $meeting->meeting_id,
          "observer_id" => $request->observer_id,
          "date_received" => Date('Y-m-d', strtotime($request->date_received)),
          "created_at" => now(),
          "updated_at" => now()
        ]);
        return back()->with('message', 'success');
      } else {
        return back()->with('message', 'duplicate');
      }
    } else {
      $duplicate = DB::table("meeting_observer")->where([['meeting_observer_id', '<>', $request->meeting_observer_id], ['meeting_id', $meeting->meeting_id], ['observer_id', $request->observerf_id]])->count();

      if ($duplicate == 0) {
        DB::table("meeting_observer")
          ->where('meeting_observer_id', $request->meeting_observer_id)
          ->update([
            "meeting_id" => $meeting->meeting_id,
            "observer_id" => $request->observer_id,
            "date_received" => Date('Y-m-d', strtotime($request->date_received)),
            "updated_at" => now()
          ]);
        return back()->with('message', 'success');
      } else {
        return back()->with('message', 'duplicate');
      }
    }
  }

  public function deleteReleaseNOM($id)
  {
    DB::table("meeting_observer")->where("meeting_observer_id", $id)->delete();
    return back()->with("message", "delete_success");
  }
}
