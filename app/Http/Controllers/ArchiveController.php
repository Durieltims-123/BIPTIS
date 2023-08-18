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
use App\ArchiveRFQAttachments;
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
use Validator;
use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\ProcurementController;
use App\ArchiveTransmittal;
use App\ArchiveTransmittalAttachments;

class ArchiveController extends Controller
{

  // APP Archive Settings
  public function getRegularAPP()
  {
    $year = date('Y');
    $project_type = 'regular';
    $archive_apps = ArchiveApp::where([['project_year', $year], ['project_type', $project_type]])
      ->leftJoin('fund_category', 'archive_apps.fund_category_id', 'fund_category.fund_category_id')
      ->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.app', ['links' => $links, 'user_privilege' => $user_privilege, 'fund_categories' => $fund_categories, 'archive_apps' => $archive_apps, 'year' => $year, "title" => "Archive Regular App", "project_type" => $project_type]);
  }

  public function getSupplementalAPP()
  {
    $year = date('Y');
    $project_type = 'supplemental';
    $archive_apps = ArchiveApp::where([['project_year', $year], ['project_type', $project_type]])
      ->leftJoin('fund_category', 'archive_apps.fund_category_id', 'fund_category.fund_category_id')
      ->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.app', ['links' => $links, 'user_privilege' => $user_privilege, 'fund_categories' => $fund_categories, 'archive_apps' => $archive_apps, 'year' => $year, "title" => "Archive Supplemental App", "project_type" => $project_type]);
  }

  public function filterArchiveApp(Request $request)
  {
    $data = $request->validate([
      "project_year" => "required",
    ]);

    $year = $request->project_year;
    $project_type = $request->app_type;
    $whereArray = [['project_year', $year], ['project_type', $project_type]];


    if ($request->app_group != null) {
      array_push($whereArray, ['app_group_no', $request->app_group]);
    }
    if ($request->fund_category != null) {
      array_push($whereArray, ['archive_apps.fund_category_id', $request->fund_category]);
    }

    $archive_apps = ArchiveApp::where($whereArray)
      ->leftJoin('fund_category', 'archive_apps.fund_category_id', 'fund_category.fund_category_id')
      ->get();

    return back()->withInput()->with('archive_apps', $archive_apps);
  }

  public function submitApp(Request $request)
  {

    $data = $request->validate([
      "year" => "required",
      "source_of_fund" => "required_if:project_type,===,regular",
      "sapp_number" => "required_if:project_type,===,supplemental",
    ]);

    $id = $request->id;
    $message = "success";
    $attachments = $request->file('attachments');
    if ($request->project_type === "supplemental") {
      $folder = "Supplemental APP";
      $naming = $request->year . "SAPP" . $request->sapp_number;
    } else {
      $fund_category = DB::table('fund_category')->where('fund_category_id', $request->source_of_fund)->first();
      $folder = "Regular APP";
      $naming = $request->year . $fund_category->title;
    }

    if ($id === null) {
      $app = ArchiveApp::firstOrCreate([
        "project_year" => $request->year,
        "app_group_no" => $request->sapp_number,
        "project_type" => $request->project_type,
        "fund_category_id" => $request->source_of_fund,
        "remarks" => $request->remarks
      ]);
    } else {
      $app = ArchiveApp::find($id);
      $app->project_year = $request->year;
      $app->app_group_no = $request->sapp_number;
      $app->project_type = $request->project_type;
      $app->fund_category_id = $request->source_of_fund;
      $app->remarks = $request->remarks;
      $app->save();
    }



    if (isset($attachments)) {
      // save attachments to folder and database
      foreach ($attachments as $attachment) {
        $filename = $attachment->getClientOriginalName();
        $pieces = explode(".", $filename);
        $last_index = count($pieces) - 1;
        $new_name = $naming . "-" . uniqid() . ".pdf";
        if ($pieces[$last_index] === "pdf") {

          Storage::disk('drive-d')->putFileAs('Archives/' . $folder . '/', $attachment, $new_name);

          ArchiveProjectAttachments::create([
            "archive_app_id" => $app->id,
            "attachment_name" => $new_name,
          ]);
        }
      }
    } else {
      $existing_attachments = ArchiveProjectAttachments::where("archive_app_id", $app->id)->count();
      if ($existing_attachments === 0) {
        $message = "missing_attachment";
        $app->delete();
      }
    }
    if ($message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getProjectAttachments(Request $request)
  {
    $attachments = ArchiveProjectAttachments::where('archive_app_id', $request->id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function deleteProjectAttachment(Request $request)
  {
    $data = ArchiveProjectAttachments::where('id', $request->id)->first();
    $app = ArchiveApp::find($data->archive_app_id);
    if ($app->project_type === "supplemental") {
      $folder = "Supplemental APP";
    } else {
      $folder = "Regular APP";
    }

    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/' . $folder . '/' . $data->attachment_name);
      ArchiveProjectAttachments::where('attachment_name', $data->attachment_name)->delete();
    }

    $existing_attachments = ArchiveProjectAttachments::where('archive_app_id', $data->archive_app_id)->count();

    if ($existing_attachments === 0) {
      $app->delete();
      return "reload";
    }

    return "success";
  }

  public function viewProjectAttachment(Request $request)
  {

    $data = ArchiveProjectAttachments::where('id', $request->id)->first();
    $app = ArchiveApp::find($data->archive_app_id);
    if ($app->project_type === "supplemental") {
      $folder = "Supplemental APP";
    } else {
      $folder = "Regular APP";
    }

    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/' . $folder . '/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewProjectAttachments(Request $request)
  {
    $app = ArchiveApp::find($request->id);
    if ($app->project_type === "supplemental") {
      $folder = "Supplemental APP";
    } else {
      $folder = "Regular APP";
    }

    if ($app != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archive_apps-" . $request->id;
      $attachments = ArchiveProjectAttachments::where("archive_app_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/' . $folder . '/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteProjectAttachments(Request $request)
  {
    $data = ArchiveApp::find($request->id);
    if ($data != null) {
      if ($data->project_type === "supplemental") {
        $folder = "Supplemental APP";
      } else {
        $folder = "Regular APP";
      }

      $attachments = ArchiveProjectAttachments::where("archive_app_id", $request->id)->get();
      foreach ($attachments as $value) {
        Storage::disk('drive-d')->delete('Archives/' . $folder . '/' . $value->attachment_name);
      }
      ArchiveProjectAttachments::where("archive_app_id", $request->id)->delete();
      $data->delete();
      return "success";
    } else {
      return abort(404);
    }
  }

  // ITB
  public function getITBArchive()
  {
    $year = date('Y');
    $project_type = null;
    $status = 'with_or_without_itbrfq_attachment';
    $mode = 1;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;
    $title = "Archive Invitation to Bid";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.itb', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'project_type' => $project_type, 'status' => $status, 'year' => $year]);
  }

  public function filterITB(Request $request)
  {
    $year = $request->project_year;
    $project_type = null;
    $status = 'with_or_without_itbrfq_attachment';
    $mode = 1;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;
    $title = "Archive Invitation to Bid";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return back()->with('filtered_data', $project_plans)->withInput();

    // return view(
    //   'archives.itb',
    //   ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'project_type' => $project_type, 'status' => $status, 'year' => $year]
    // );
  }

  public function submitITB(Request $request)
  {

    $data = $request->validate([
      "id" => "required"
    ]);

    $id = $request->id;
    $message = "success";
    $attachments = $request->file('attachments');
    $procurement_activity = Procact::find($id);
    $cluster = Procact::where('procact_id', $id)->get();
    if ($procurement_activity->plan_cluster_id != null) {
      $cluster = Procact::where('procacts.plan_cluster_id', $procurement_activity->plan_cluster_id)->get();
    }
    if (isset($attachments)) {
      // save attachments to folder and database
      foreach ($attachments as $attachment) {
        $filename = $attachment->getClientOriginalName();
        $pieces = explode(".", $filename);
        $last_index = count($pieces) - 1;
        $new_name = "ITB-" . uniqid() . ".pdf";
        if ($pieces[$last_index] === "pdf") {

          Storage::disk('drive-d')->putFileAs('Archives/Invitation To Bid/', $attachment, $new_name);

          foreach ($cluster as $value) {
            ArchiveITBAttachments::create([
              "procact_id" => $value->procact_id,
              "attachment_name" => $new_name,
            ]);

            $procact = Procact::find($value->procact_id);
            $procact->itbrfq_attachment = true;
            $procact->save();
          }
        }
      }
    } else {
      $existing_attachments = ArchiveItbAttachments::where("procact_id", $procurement_activity->procact_id)->count();
      if ($existing_attachments === 0) {
        $message = "missing_attachment";
      }
    }

    if ($message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getITBAttachments(Request $request)
  {
    $attachments = ArchiveITBAttachments::where('procact_id', $request->procact_id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function deleteITBAttachment(Request $request)
  {
    $data = ArchiveITBAttachments::where('id', $request->id)->first();
    $procact = Procact::find($data->procact_id);

    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/Invitation To Bid/' . $data->attachment_name);
      ArchiveITBAttachments::where('attachment_name', $data->attachment_name)->delete();
    }

    $existing_attachments = ArchiveITBAttachments::where('procact_id', $data->procact_id)->count();
    if ($existing_attachments === 0) {
      $cluster = Procact::where('procact_id', $data->procact_id)->get();
      if ($procact->plan_cluster_id != null) {
        $cluster = Procact::where('plan_cluster_id', $procact->plan_cluster_id)->get();
      }

      foreach ($cluster as $value) {
        $procact = Procact::find($value->procact_id);
        $procact->itbrfq_attachment = false;
        $procact->save();
      }

      return "reload";
    }

    return "success";
  }

  public function viewITBAttachment(Request $request)
  {

    $data = ArchiveITBAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/Invitation To Bid/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewITBAttachments(Request $request)
  {
    $procact = Procact::find($request->id);
    if ($procact != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archive-ITB" . $request->id;
      $attachments = ArchiveITBAttachments::where("procact_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/Invitation To Bid/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteITBAttachments(Request $request)
  {
    $data = Procact::find($request->id);
    if ($data != null) {

      $cluster = Procact::where('procact_id', $data->procact_id)->get();
      if ($data->plan_cluster_id != null) {
        $cluster = Procact::where('plan_cluster_id', $data->plan_cluster_id)->get();
      }
      foreach ($cluster as $value) {
        $procact = Procact::find($value->procact_id);
        $attachments = ArchiveITBAttachments::where("procact_id", $procact->procact_id)->get();
        foreach ($attachments as $value) {
          Storage::disk('drive-d')->delete('Archives/Invitation To Bid/' . $value->attachment_name);
        }
        ArchiveITBAttachments::where("procact_id", $procact->procact_id)->delete();
        $procact->itbrfq_attachment = false;
        $procact->save();
      }

      return "success";
    } else {
      return abort(404);
    }
  }

  // RFQ
  public function getRFQArchive()
  {
    $year = date('Y');
    $project_type = null;
    $status = 'with_or_without_itbrfq_attachment';
    $mode = 2;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;
    $title = "Request For Quotations";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view(
      'archives.rfq',
      ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'project_type' => $project_type, 'status' => $status, 'year' => $year]
    );
  }

  public function filterRFQ(Request $request)
  {
    $year = $request->project_year;
    $project_type = null;
    $status = 'with_or_without_itbrfq_attachment';
    $mode = 2;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;
    $title = "Request For Quotations";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    return back()->with("filtered_data", $project_plans)->withInput();
  }

  public function submitRFQ(Request $request)
  {

    $data = $request->validate([
      "id" => "required"
    ]);

    $id = $request->id;
    $message = "success";
    $attachments = $request->file('attachments');
    $procurement_activity = Procact::find($id);
    $cluster = Procact::where('procact_id', $id)->get();
    if ($procurement_activity->plan_cluster_id != null) {
      $cluster = Procact::where('procacts.plan_cluster_id', $procurement_activity->plan_cluster_id)->get();
    }
    if (isset($attachments)) {
      // save attachments to folder and database
      foreach ($attachments as $attachment) {
        $filename = $attachment->getClientOriginalName();
        $pieces = explode(".", $filename);
        $last_index = count($pieces) - 1;
        $new_name = "RFQ-" . uniqid() . ".pdf";
        if ($pieces[$last_index] === "pdf") {

          Storage::disk('drive-d')->putFileAs('Archives/Invitation To Bid/', $attachment, $new_name);

          foreach ($cluster as $value) {
            ArchiveRFQAttachments::create([
              "procact_id" => $value->procact_id,
              "attachment_name" => $new_name,
            ]);

            $procact = Procact::find($value->procact_id);
            $procact->itbrfq_attachment = true;
            $procact->save();
          }
        }
      }
    } else {
      $existing_attachments = ArchiveRFQAttachments::where("procact_id", $procurement_activity->procact_id)->count();
      if (
        $existing_attachments === 0
      ) {
        $message = "missing_attachment";
      }
    }

    if ($message === "success") {
      return back()->with(
        "message",
        $message
      );
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getRFQAttachments(Request $request)
  {
    $attachments = ArchiveRFQAttachments::where('procact_id', $request->procact_id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function deleteRFQAttachment(Request $request)
  {
    $data = ArchiveRFQAttachments::where('id', $request->id)->first();
    $procact = Procact::find($data->procact_id);

    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/Invitation To Bid/' . $data->attachment_name);
      ArchiveRFQAttachments::where('attachment_name', $data->attachment_name)->delete();
    }

    $existing_attachments = ArchiveRFQAttachments::where('procact_id', $data->procact_id)->count();
    if (
      $existing_attachments === 0
    ) {
      $cluster = Procact::where('procact_id', $data->procact_id)->get();
      if (
        $procact->plan_cluster_id != null
      ) {
        $cluster = Procact::where('plan_cluster_id', $procact->plan_cluster_id)->get();
      }

      foreach ($cluster as $value) {
        $procact = Procact::find($value->procact_id);
        $procact->itbrfq_attachment = false;
        $procact->save();
      }

      return "reload";
    }

    return "success";
  }

  public function viewRFQAttachment(Request $request)
  {

    $data = ArchiveRFQAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return response()->file(Storage::disk('drive-d')->path('Archives/Invitation To Bid/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewRFQAttachments(Request $request)
  {
    $procact = Procact::find($request->id);
    if ($procact != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archive-RFQ" . $request->id;
      $attachments = ArchiveRFQAttachments::where("procact_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/Invitation To Bid/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteRFQAttachments(Request $request)
  {
    $data = Procact::find($request->id);
    if ($data != null) {

      $cluster = Procact::where('procact_id', $data->procact_id)->get();
      if (
        $data->plan_cluster_id != null
      ) {
        $cluster = Procact::where('plan_cluster_id', $data->plan_cluster_id)->get();
      }
      foreach ($cluster as $value) {
        $procact = Procact::find($value->procact_id);
        $attachments = ArchiveRFQAttachments::where("procact_id", $procact->procact_id)->get();
        foreach ($attachments as $value) {
          Storage::disk('drive-d')->delete('Archives/Invitation To Bid/' . $value->attachment_name);
        }
        ArchiveRFQAttachments::where("procact_id", $procact->procact_id)->delete();
        $procact->itbrfq_attachment = false;
        $procact->save();
      }

      return "success";
    } else {
      return abort(404);
    }
  }

  // Archive NoticeOfMeeting Start
  public function getNoticeOfMeetingArchive(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = Meeting::where([['meeting_date', 'like', $year . '%']])
      ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
      ->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.notice_of_meeting', ['links' => $links, 'user_privilege' => $user_privilege, 'year' => $year, "title" => "Archive Notice Of Meetings", "data" => $data]);
  }

  public function filterNoticeOfMeeting(Request $request)
  {

    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = Meeting::where([['meeting_date', 'like', $year . '%']])
      ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
      ->get();

    return back()->with('data', $data)->withInput();
  }

  public function submitNoticeOfMeeting(Request $request)
  {
    $data = $request->validate([
      "id" => "required"
    ]);

    $id = $request->id;
    $message = "success";
    $attachments = $request->file('attachments');
    $meeting = Meeting::find($id);

    if (isset($attachments)) {
      // save attachments to folder and database
      foreach ($attachments as $attachment) {
        $filename = $attachment->getClientOriginalName();
        $pieces = explode(".", $filename);
        $last_index = count($pieces) - 1;
        $new_name = $meeting->meeting_date . "notice_of_meeting-" . uniqid() . ".pdf";
        if ($pieces[$last_index] === "pdf") {

          Storage::disk('drive-d')->putFileAs('Archives/NoticeOfMeetings/', $attachment, $new_name);
          ArchiveNoticeOfMeetingAttachments::create([
            "meeting_id" => $meeting->meeting_id,
            "attachment_name" => $new_name,
          ]);
        }
      }
    } else {
      $existing_attachments = ArchiveNoticeOfMeetingAttachments::where("meeting_id", $meeting->meeting_id)->count();
      if ($existing_attachments === 0) {
        $message = "missing_attachment";
      }
    }

    if ($message === "success") {
      $meeting->with_attachment = true;
      $meeting->save();
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getArchiveNoticeOfMeetingAttachments(Request $request)
  {
    $attachments = ArchiveNoticeOfMeetingAttachments::where('meeting_id', $request->meeting_id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function viewNoticeOfMeetingAttachment(Request $request)
  {
    $data = ArchiveNoticeOfMeetingAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/NoticeOfMeetings/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewNoticeOfMeetingAttachments(Request $request)
  {
    $meeting = Meeting::find($request->id);
    if ($meeting != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Notice_Of_Meeting-" . $request->id;
      $attachments = ArchiveNoticeOfMeetingAttachments::where("meeting_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/NoticeOfMeetings/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteNoticeOfMeetingAttachment(Request $request)
  {
    $data = ArchiveNoticeOfMeetingAttachments::where('id', $request->id)->first();
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/NoticeOfMeetings/' . $data->attachment_name);
      $delete = ArchiveNoticeOfMeetingAttachments::where('id', $request->id)->delete();
    }
    $existing_attachments = ArchiveNoticeOfMeetingAttachments::where('meeting_id', $data->meeting_id)->count();
    if ($existing_attachments === 0) {
      $meeting = Meeting::find($data->meeting_id);
      $meeting->with_attachment = false;
      $meeting->save();
    }
    return "success";
  }

  public function deleteNoticeOfMeeting(Request $request)
  {

    $meeting = Meeting::find($request->id);
    if ($meeting != null) {
      $meeting->with_attachment = false;
      $meeting->save();
      $attachment_data = ArchiveNoticeOfMeetingAttachments::where('meeting_id', $meeting->meeting_id)->get();
      foreach ($attachment_data as $attachment) {
        Storage::disk('drive-d')->delete('Archives/NoticeOfMeetings/' . $attachment->attachment_name);
      }
      ArchiveNoticeOfMeetingAttachments::where('meeting_id', $meeting->meeting_id)->delete();
    }

    $data = Meeting::where([['meeting_date', 'like', $request->year . '%']])
      ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')
      ->get();

    return $data;
  }

  // end notice of meeting

  // Minutes of Meeting
  public function getMinuteArchive(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = ArchiveMinute::where([['date_opened', 'like', $year . '%'], ['deleted', '<>', '1']])
      ->select('archive_minutes.*', 'users.name')
      ->join('users', 'archive_minutes.updated_by', 'users.id')
      ->orderBy('created_at', 'desc')
      ->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.minute', ['links' => $links, 'user_privilege' => $user_privilege, 'year' => $year, "title" => "Minutes of Meeting", "data" => $data]);
  }

  public function filterMinute(Request $request)
  {

    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = ArchiveMinute::where([['date_opened', 'like', $year . '%'], ['deleted', '<>', '1']])
      ->select('archive_minutes.*', 'users.name')
      ->join('users', 'archive_minutes.updated_by', 'users.id')
      ->get();

    return back()->with('data', $data)->withInput();
  }

  public function submitMinute(Request $request)
  {
    $data = $request->validate([
      "date_opened" => "required|before:tomorrow"
    ]);

    $date_opened = Date('Y-m-d', strtotime($request->date_opened));
    $id = $request->id;
    $message = "success";
    $attachments = $request->file('attachments');
    $meeting = Meeting::where('meeting_date', $date_opened)->count();
    if ($meeting === 0) {
      $message = "opening_error";
    } else {

      if ($id === null) {
        $duplicate = ArchiveMinute::where([['date_opened', $date_opened], ['deleted', '<>', '1']])->count();

        if ($duplicate === 0) {

          if (isset($attachments)) {
            $archive_minute = ArchiveMinute::create([
              "date_opened" => $date_opened,
              "updated_by" => Auth::user()->id,
              "deleted" => 0,
            ]);

            // save attachments to folder and database
            foreach ($attachments as $attachment) {
              $filename = $attachment->getClientOriginalName();
              $pieces = explode(".", $filename);
              $last_index = count($pieces) - 1;
              $new_name = $date_opened . "minute-" . uniqid() . ".pdf";
              if ($pieces[$last_index] === "pdf") {
                Storage::disk('drive-d')->putFileAs('Archives/Minutes', $attachment, $new_name);
                ArchiveMinuteAttachments::create([
                  "archive_minute_id" => $archive_minute->id,
                  "attachment_name" => $new_name,
                ]);
              }
            }
          } else {
            $message = "missing_attachment";
          }
        } else {
          $message = "duplicate_error";
        }
      } else {
        $archive_minute = ArchiveMinute::find($id);

        $archive_minute->date_opened = $date_opened;
        $archive_minute->updated_by = Auth::user()->id;
        $archive_minute->save();

        if (isset($attachments)) {
          // save attachments to folder and database
          foreach ($attachments as $attachment) {
            $filename = $attachment->getClientOriginalName();
            $pieces = explode(".", $filename);
            $last_index = count($pieces) - 1;
            $new_name = $date_opened . "minute-" . uniqid() . ".pdf";
            if ($pieces[$last_index] === "pdf") {
              Storage::disk('drive-d')->putFileAs('Archives/Minutes', $attachment, $new_name);

              ArchiveMinuteAttachments::create([
                "archive_minute_id" => $archive_minute->id,
                "attachment_name" => $new_name,
              ]);
            }
          }
        } else {
          $existing_attachments = ArchiveMinuteAttachments::where("archive_minute_id", $archive_minute->id)->count();
          if ($existing_attachments === 0) {
            $message = "missing_attachment";
          }
        }
      }
    }
    if ($message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getArchiveMinuteAttachments(Request $request)
  {
    $attachments = ArchiveMinuteAttachments::where('archive_minute_id', $request->archive_minute_id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function viewMinuteAttachment(Request $request)
  {
    $data = ArchiveMinuteAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/Minutes/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewMinuteAttachments(Request $request)
  {
    $archive_minute = ArchiveMinute::find($request->id);
    if ($archive_minute != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_minutes-" . $request->id;
      $attachments = ArchiveMinuteAttachments::where("archive_minute_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/minutes/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteMinuteAttachment(Request $request)
  {
    $data = ArchiveMinuteAttachments::where('id', $request->id)->first();
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/Minutes/' . $data->attachment_name);
      $data = ArchiveMinuteAttachments::where('id', $request->id)->delete();
    }
    return "success";
  }

  public function deleteMinute(Request $request)
  {

    $archive_minute = ArchiveMinute::find($request->id);
    if ($archive_minute != null) {
      $archive_minute->deleted = 1;
      $archive_minute->deleted_at = now();
      $archive_minute->deleted_by = Auth::user()->id;
      $archive_minute->save();
    }
    return "success";
  }

  // END minutes of meeting

  // Meeting Attendance
  public function getMeetingAttendanceArchive(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = ArchiveMeetingAttendance::where([['meeting_date', 'like', $year . '%'], ['deleted', '<>', '1']])
      ->select('archive_attendance.*', 'users.name')
      ->join('users', 'archive_attendance.updated_by', 'users.id')
      ->orderBy('created_at', 'desc')
      ->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.attendance', ['links' => $links, 'user_privilege' => $user_privilege, 'year' => $year, "title" => "Meeting Attendance", "data" => $data]);
  }

  public function filterMeetingAttendance(Request $request)
  {

    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = ArchiveMeetingAttendance::where([['meeting_date', 'like', $year . '%'], ['deleted', '<>', '1']])
      ->select('archive_attendance.*', 'users.name')
      ->join('users', 'archive_attendance.updated_by', 'users.id')
      ->get();

    return back()->with('data', $data)->withInput();
  }

  public function submitMeetingAttendance(Request $request)
  {

    $data = $request->validate([
      "meeting_date" => "required|before:tomorrow"
    ]);

    $meeting_date = Date('Y-m-d', strtotime($request->meeting_date));
    $id = $request->id;
    $message = "success";
    $attachments = $request->file('attachments');
    $meeting = Meeting::where('meeting_date', $meeting_date)->count();
    if ($meeting === 0) {
      $message = "opening_error";
    } else {

      if ($id === null) {
        $duplicate = ArchiveMeetingAttendance::where([['meeting_date', $meeting_date], ['deleted', '<>', '1']])->count();

        if ($duplicate === 0) {

          if (isset($attachments)) {
            $archive_attendance = ArchiveMeetingAttendance::create([
              "meeting_date" => $meeting_date,
              "updated_by" => Auth::user()->id,
              "deleted" => 0,
            ]);

            // save attachments to folder and database
            foreach ($attachments as $attachment) {
              $filename = $attachment->getClientOriginalName();
              $pieces = explode(".", $filename);
              $last_index = count($pieces) - 1;
              $new_name = $meeting_date . "attendance-" . uniqid() . ".pdf";
              if ($pieces[$last_index] === "pdf") {
                Storage::disk('drive-d')->putFileAs('Archives/MeetingAttendances', $attachment, $new_name);
                ArchiveMeetingAttendanceAttachments::create([
                  "archive_attendance_id" => $archive_attendance->id,
                  "attachment_name" => $new_name,
                ]);
              }
            }
          } else {
            $message = "missing_attachment";
          }
        } else {
          $message = "duplicate_error";
        }
      } else {
        $archive_attendance = ArchiveMeetingAttendance::find($id);

        $archive_attendance->meeting_date = $meeting_date;
        $archive_attendance->updated_by = Auth::user()->id;
        $archive_attendance->save();

        if (isset($attachments)) {
          // save attachments to folder and database
          foreach ($attachments as $attachment) {
            $filename = $attachment->getClientOriginalName();
            $pieces = explode(".", $filename);
            $last_index = count($pieces) - 1;
            $new_name = $meeting_date . "attendance-" . uniqid() . ".pdf";
            if ($pieces[$last_index] === "pdf") {
              Storage::disk('drive-d')->putFileAs('Archives/MeetingAttendances', $attachment, $new_name);

              ArchiveMeetingAttendanceAttachments::create([
                "archive_attendance_id" => $archive_attendance->id,
                "attachment_name" => $new_name,
              ]);
            }
          }
        } else {
          $existing_attachments = ArchiveMeetingAttendanceAttachments::where("archive_attendance_id", $archive_attendance->id)->count();
          if ($existing_attachments === 0) {
            $message = "missing_attachment";
          }
        }
      }
    }
    if ($message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getArchiveMeetingAttendanceAttachments(Request $request)
  {
    $attachments = ArchiveMeetingAttendanceAttachments::where('archive_attendance_id', $request->archive_attendance_id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function viewMeetingAttendanceAttachment(Request $request)
  {
    $data = ArchiveMeetingAttendanceAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/MeetingAttendances/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewMeetingAttendanceAttachments(Request $request)
  {
    $archive_attendance = ArchiveMeetingAttendance::find($request->id);
    if ($archive_attendance != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_attendances-" . $request->id;
      $attachments = ArchiveMeetingAttendanceAttachments::where("archive_attendance_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/MeetingAttendances/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteMeetingAttendanceAttachment(Request $request)
  {
    $data = ArchiveMeetingAttendanceAttachments::where('id', $request->id)->first();
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/MeetingAttendances/' . $data->attachment_name);
      $data = ArchiveMeetingAttendanceAttachments::where('id', $request->id)->delete();
    }
    return "success";
  }

  public function deleteMeetingAttendance(Request $request)
  {

    $archive_attendance = ArchiveMeetingAttendance::find($request->id);
    if ($archive_attendance != null) {
      $archive_attendance->deleted = 1;
      $archive_attendance->deleted_at = now();
      $archive_attendance->deleted_by = Auth::user()->id;
      $archive_attendance->save();
    }
    return "success";
  }

  // END attendances of meeting

  // Archive Abstract Start
  public function getAbstractArchive(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = ArchiveAbstract::where([['date_opened', 'like', $year . '%'], ['deleted', '<>', '1']])
      ->select('archive_abstracts.*', 'users.name')
      ->join('users', 'archive_abstracts.updated_by', 'users.id')
      ->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.abstract', ['links' => $links, 'user_privilege' => $user_privilege, 'year' => $year, "title" => "Abstracts", "data" => $data]);
  }

  public function filterAbstract(Request $request)
  {

    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = ArchiveAbstract::where([['date_opened', 'like', $year . '%'], ['deleted', '<>', '1']])
      ->select('archive_abstracts.*', 'users.name')
      ->join('users', 'archive_abstracts.updated_by', 'users.id')
      ->get();

    return back()->with('data', $data)->withInput();
  }

  public function submitAbstract(Request $request)
  {
    $data = $request->validate([
      "date_opened" => "required|before:tomorrow"
    ]);

    $date_opened = Date('Y-m-d', strtotime($request->date_opened));
    $id = $request->id;
    $message = "success";
    $attachments = $request->file('attachments');

    $opened_projects = ProjectTimeline::where('bid_submission_start', $date_opened)->count();

    if ($opened_projects === 0) {
      $message = "opening_error";
    } else {

      if ($id === null) {
        $duplicate = ArchiveAbstract::where([['date_opened', $date_opened], ['deleted', '<>', '1']])->count();

        if ($duplicate === 0) {

          if (isset($attachments)) {
            $archive_abstract = ArchiveAbstract::create([
              "date_opened" => $date_opened,
              "updated_by" => Auth::user()->id,
              "deleted" => 0,
            ]);

            // save attachments to folder and database
            foreach ($attachments as $attachment) {
              $filename = $attachment->getClientOriginalName();
              $pieces = explode(".", $filename);
              $last_index = count($pieces) - 1;
              $new_name = $date_opened . "abstract-" . uniqid() . ".pdf";
              if ($pieces[$last_index] === "pdf") {
                Storage::disk('drive-d')->putFileAs('Archives/Abstracts', $attachment, $new_name);
                ArchiveAbstractAttachments::create([
                  "archive_abstract_id" => $archive_abstract->id,
                  "attachment_name" => $new_name,
                ]);
              }
            }
          } else {
            $message = "missing_attachment";
          }
        } else {
          $message = "duplicate_error";
        }
      } else {
        $archive_abstract = ArchiveAbstract::find($id);

        $archive_abstract->date_opened = $date_opened;
        $archive_abstract->updated_by = Auth::user()->id;
        $archive_abstract->save();

        if (isset($attachments)) {
          // save attachments to folder and database
          foreach ($attachments as $attachment) {
            $filename = $attachment->getClientOriginalName();
            $pieces = explode(".", $filename);
            $last_index = count($pieces) - 1;
            $new_name = $date_opened . "abstract-" . uniqid() . ".pdf";
            if ($pieces[$last_index] === "pdf") {
              Storage::disk('drive-d')->putFileAs('Archives/Abstracts', $attachment, $new_name);

              ArchiveAbstractAttachments::create([
                "archive_abstract_id" => $archive_abstract->id,
                "attachment_name" => $new_name,
              ]);
            }
          }
        } else {
          $existing_attachments = ArchiveAbstractAttachments::where("archive_abstract_id", $archive_abstract->id)->count();
          if ($existing_attachments === 0) {
            $message = "missing_attachment";
          }
        }
      }
    }
    if ($message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getArchiveAbstractAttachments(Request $request)
  {
    $attachments = ArchiveAbstractAttachments::where('archive_abstract_id', $request->archive_abstract_id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function viewAbstractAttachment(Request $request)
  {
    $data = ArchiveAbstractAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/Abstracts/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewAbstractAttachments(Request $request)
  {
    $archive_abstract = ArchiveAbstract::find($request->id);
    if ($archive_abstract != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_abstracts-" . $request->id;
      $attachments = ArchiveAbstractAttachments::where("archive_abstract_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/abstracts/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteAbstractAttachment(Request $request)
  {
    $data = ArchiveAbstractAttachments::where('id', $request->id)->first();
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/Abstracts/' . $data->attachment_name);
      $data = ArchiveAbstractAttachments::where('id', $request->id)->delete();
    }
    return "success";
  }

  public function deleteAbstract(Request $request)
  {

    $archive_abstract = ArchiveAbstract::find($request->id);
    if ($archive_abstract != null) {
      $archive_abstract->deleted = 1;
      $archive_abstract->deleted_at = now();
      $archive_abstract->deleted_by = Auth::user()->id;
      $archive_abstract->save();
    }
    return "success";
  }

  // Archive Astract End

  // Archive CertificateOfPosting Start
  public function getCertificateOfPostingArchive(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = ArchiveCertificateOfPosting::where([['date_opened', 'like', $year . '%'], ['deleted', '<>', '1']])
      ->select('archive_certificate_of_posting.*', 'users.name')
      ->join('users', 'archive_certificate_of_posting.updated_by', 'users.id')
      ->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.certificate_of_posting', ['links' => $links, 'user_privilege' => $user_privilege, 'year' => $year, "title" => "Certificate Of Postings", "data" => $data]);
  }

  public function filterCertificateOfPosting(Request $request)
  {

    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = ArchiveCertificateOfPosting::where([['date_opened', 'like', $year . '%'], ['deleted', '<>', '1']])
      ->select('archive_certificate_of_posting.*', 'users.name')
      ->join('users', 'archive_certificate_of_posting.updated_by', 'users.id')
      ->get();

    return back()->with('data', $data)->withInput();
  }

  public function submitCertificateOfPosting(Request $request)
  {
    $data = $request->validate([
      "date_opened" => "required|before:tomorrow"
    ]);

    $date_opened = Date('Y-m-d', strtotime($request->date_opened));
    $id = $request->id;
    $message = "success";
    $attachments = $request->file('attachments');

    $opened_projects = ProjectTimeline::where('bid_submission_start', $date_opened)->count();

    if ($opened_projects === 0) {
      $message = "opening_error";
    } else {

      if ($id === null) {
        $duplicate = ArchiveCertificateOfPosting::where([['date_opened', $date_opened], ['deleted', '<>', '1']])->count();

        if ($duplicate === 0) {

          if (isset($attachments)) {
            $archive_certificate_of_posting = ArchiveCertificateOfPosting::create([
              "date_opened" => $date_opened,
              "updated_by" => Auth::user()->id,
              "deleted" => 0,
            ]);

            // save attachments to folder and database
            foreach ($attachments as $attachment) {
              $filename = $attachment->getClientOriginalName();
              $pieces = explode(".", $filename);
              $last_index = count($pieces) - 1;
              $new_name = $date_opened . "certificate_of_posting-" . uniqid() . ".pdf";
              if ($pieces[$last_index] === "pdf") {
                Storage::disk('drive-d')->putFileAs('Archives/CertificateOfPostings', $attachment, $new_name);
                ArchiveCertificateOfPostingAttachments::create([
                  "archive_certificate_of_posting_id" => $archive_certificate_of_posting->id,
                  "attachment_name" => $new_name,
                ]);
              }
            }
          } else {
            $message = "missing_attachment";
          }
        } else {
          $message = "duplicate_error";
        }
      } else {
        $archive_certificate_of_posting = ArchiveCertificateOfPosting::find($id);

        $archive_certificate_of_posting->date_opened = $date_opened;
        $archive_certificate_of_posting->updated_by = Auth::user()->id;
        $archive_certificate_of_posting->save();

        if (isset($attachments)) {
          // save attachments to folder and database
          foreach ($attachments as $attachment) {
            $filename = $attachment->getClientOriginalName();
            $pieces = explode(".", $filename);
            $last_index = count($pieces) - 1;
            $new_name = $date_opened . "certificate_of_posting-" . uniqid() . ".pdf";
            if ($pieces[$last_index] === "pdf") {
              Storage::disk('drive-d')->putFileAs('Archives/CertificateOfPostings', $attachment, $new_name);

              ArchiveCertificateOfPostingAttachments::create([
                "archive_certificate_of_posting_id" => $archive_certificate_of_posting->id,
                "attachment_name" => $new_name,
              ]);
            }
          }
        } else {
          $existing_attachments = ArchiveCertificateOfPostingAttachments::where("archive_certificate_of_posting_id", $archive_certificate_of_posting->id)->count();
          if ($existing_attachments === 0) {
            $message = "missing_attachment";
          }
        }
      }
    }
    if ($message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getArchiveCertificateOfPostingAttachments(Request $request)
  {
    $attachments = ArchiveCertificateOfPostingAttachments::where('archive_certificate_of_posting_id', $request->archive_certificate_of_posting_id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function viewCertificateOfPostingAttachment(Request $request)
  {
    $data = ArchiveCertificateOfPostingAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/CertificateOfPostings/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewCertificateOfPostingAttachments(Request $request)
  {
    $archive_certificate_of_posting = ArchiveCertificateOfPosting::find($request->id);
    if ($archive_certificate_of_posting != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_certificate_of_postings-" . $request->id;
      $attachments = ArchiveCertificateOfPostingAttachments::where("archive_certificate_of_posting_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/CertificateOfPostings/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteCertificateOfPostingAttachment(Request $request)
  {
    $data = ArchiveCertificateOfPostingAttachments::where('id', $request->id)->first();
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/CertificateOfPostings/' . $data->attachment_name);
      $data = ArchiveCertificateOfPostingAttachments::where('id', $request->id)->delete();
    }
    return "success";
  }

  public function deleteCertificateOfPosting(Request $request)
  {

    $archive_certificate_of_posting = ArchiveCertificateOfPosting::find($request->id);
    if ($archive_certificate_of_posting != null) {
      $archive_certificate_of_posting->deleted = 1;
      $archive_certificate_of_posting->deleted_at = now();
      $archive_certificate_of_posting->deleted_by = Auth::user()->id;
      $archive_certificate_of_posting->save();
    }
    return "success";
  }

  // Archive Certificate of Posting End

  // Other Resolutions
  public function getOtherResolutions(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $resolutions = DB::table("resolutions")->where([["type", "OTHERS"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Other Resolutions";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("archives.other_resolutions", ['links' => $links, 'user_privilege' => $user_privilege, 'resolution_type' => "RRA", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }


  // Resolution Recommending award_noticepublic
  public function getResolutionRecommendingAwards(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $resolutions = DB::table("resolutions")->where([["type", "RRA"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolutions Recommending Award";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("archives.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, 'resolution_type' => "RRA", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }

  public function getResolutionRecallCancellation(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $resolutions = DB::table("resolutions")->where([["type", "RRRC"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolutions Recommending Recall/Cancellation";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("archives.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, 'resolution_type' => "RRRC", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }

  public function getResolutionDeclaringFailure(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $resolutions = DB::table("resolutions")->where([["type", "RDF"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolutions Declaring Failure";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("archives.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, 'resolution_type' => "RDF", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }

  public function getResolutionGrantingMotion(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $resolutions = DB::table("resolutions")->where([["type", "RGMR"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolutions Granting the Motion for Reconsideration";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    $resolutions = getResolutionBidders($resolutions);

    return view("archives.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, 'resolution_type' => "RGMR", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }

  public function getResolutionDenyingMotion(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $resolutions = DB::table("resolutions")->where([["type", "RDMR"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolutions Denying the Motion for Reconsideration";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    $resolutions = getResolutionBidders($resolutions);

    return view("archives.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, 'resolution_type' => "RDMR", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }


  public function submitResolution(Request $request)
  {
    $attachments = $request->file('attachments');
    if ($request->id === null) {
      if (isset($attachments) === false) {
        return back()->withInput()->with('message', "missing_attachment");
      }
      $data = $request->validate([
        "resolution_date" => 'required',
        "resolution_number" => 'required',
        "remarks" => 'required',
      ]);
      $bypass = 1;
      $governor = DB::table('governors')->orderBy('governor_id', 'desc')->first();
      $duplicate = Resolution::where('resolution_number', $request->resolution_number)->count();

      if ($duplicate > 0) {
        return back()->withInput()->with('message', "duplicate");
      }

      $resolution = Resolution::create([
        'resolution_date' => date('Y-m-d', strtotime($request->resolution_date)), 'resolution_number' => $request->resolution_number, 'type' => 'OTHERS', 'with_attachment' => 0, 'governor_id' => $governor->governor_id, 'resolution_remarks' => $request->remarks
      ]);

      if (isset($attachments) === false && $resolution_attachments === 0) {
        $resolution->with_attachment = 0;
        $resolution->save();
        return back()->with('message', "missing_attachment");
      } else {
        if (isset($attachments)) {
          foreach ($attachments as $attachment) {
            $filename = $attachment->getClientOriginalName();
            $pieces = explode(".", $filename);
            $last_index = count($pieces) - 1;
            $new_name = $resolution->resolution_date . "-resolution-" . uniqid() . ".pdf";
            if ($pieces[$last_index] === "pdf") {
              Storage::disk('drive-d')->putFileAs('Archives/Resolutions', $attachment, $new_name);

              ArchiveResolutionAttachments::create([
                "resolution_id" => $resolution->resolution_id,
                "attachment_name" => $new_name,
              ]);
            }
          }
          $resolution->with_attachment = 1;
        } else {
          $resolution->with_attachment = 0;
        }
        $resolution->save();
        return back()->with('message', "success");
      }
    } else {
      $resolution = Resolution::find($request->id);
      $resolution_attachments = ArchiveResolutionAttachments::where('resolution_id', $resolution->resolution_id)->count();
      if ($resolution != null) {
        if (isset($attachments) === false && $resolution_attachments === 0) {
          $resolution->with_attachment = 0;
          $resolution->save();
          return back()->with('message', "missing_attachment");
        } else {
          if (isset($attachments)) {
            foreach ($attachments as $attachment) {
              $filename = $attachment->getClientOriginalName();
              $pieces = explode(".", $filename);
              $last_index = count($pieces) - 1;
              $new_name = $resolution->resolution_date . "-resolution-" . uniqid() . ".pdf";
              if ($pieces[$last_index] === "pdf") {
                Storage::disk('drive-d')->putFileAs('Archives/Resolutions', $attachment, $new_name);

                ArchiveResolutionAttachments::create([
                  "resolution_id" => $resolution->resolution_id,
                  "attachment_name" => $new_name,
                ]);
              }
            }
            $resolution->with_attachment = 1;
          }
          $resolution->save();

          if ($resolution->type === "RRRC") {
            $resolution_projects = DB::table('resolution_project_bids')->where('resolution_id', $resolution->resolution_id)->get();
            foreach ($resolution_projects as $row) {
              $bid = Db::table('project_bidders')->where([['project_bid', $row->project_bid], ['bid_status', 'cancelled']])->count();
              $project_bid = Db::table('project_bidders')->where('project_bid', $row->project_bid)->update(['bid_status' => 'cancelled']);
              if ($bid === 0) {
                DB::table('disqualification_records')->insert([
                  'project_bid'  => $row->project_bid,
                  'remarks'  => 'Resolution Recommending Recall/Cancellation of Awards : ' . $resolution->resolution_number,
                  'user_id'  => Auth::user()->id,
                  'created_at'  => now(),
                  'updated_at' => now()
                ]);
              }
            }
          }

          return back()->with('message', "success");
        }
      } else {
        return abort(403, 'Unknown Resolution');
      }
    }
  }

  public function getResolutionAttachments(Request $request)
  {
    $attachments = ArchiveResolutionAttachments::where('resolution_id', $request->resolution_id)->orderBy('created_at', 'asc')->get();

    return $attachments;
  }

  public function viewResolutionAttachment(Request $request)
  {
    $data = ArchiveResolutionAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/Resolutions/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }


  public function viewResolutionAttachments(Request $request)
  {
    $resolution = Resolution::find($request->id);
    if ($resolution != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_resolutions-" . $request->id;
      $attachments = ArchiveResolutionAttachments::where("resolution_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/resolutions/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }


  public function filterResolutions(Request $request)
  {
    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);
    $resolutions = DB::table("resolutions")->where([["type", $request->resolution_type], ["resolution_date", 'like', $request->year . "%"]])->get();
    if ($request->resolution_type === "RGMR" || $request->resolution_type === "RDMR") {
      $resolutions = getResolutionBidders($resolutions);
    }
    return back()->withInput()->with('resolutions', $resolutions);
  }

  public function deleteResolutionAttachment(Request $request)
  {
    $data = ArchiveResolutionAttachments::find($request->id);
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/Resolutions/' . $data->attachment_name);
      ArchiveResolutionAttachments::where('id', $request->id)->delete();
      $resolution_attachments = ArchiveResolutionAttachments::where('resolution_id', $data->resolution_id)->count();

      if ($resolution_attachments === 0) {
        $resolution = Resolution::find($data->resolution_id);
        $resolution->with_attachment = 0;
        $resolution->save();
        return "reload";
      }
    }
    return "success";
  }



  // Notice of Awards
  public function getNoticeOfAwards(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = NoticeOfAward::where('notice_of_awards.date_generated', 'like', $year . '%')
      ->select('municipalities.*', 'notice_of_awards.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_notice_of_awards_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'notice_of_awards.project_bid_id')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_notice_of_awards_attachments', 'notice_of_awards.notice_award_id', 'archive_notice_of_awards_attachments.notice_award_id')
      ->get();

    $data2 = NoticeOfAward::where('notice_of_awards.date_generated', 'like', $year . '%')
      ->select('municipalities.*', 'notice_of_awards.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_notice_of_awards_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'notice_of_awards.project_bid_id')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_notice_of_awards_attachments', 'notice_of_awards.notice_award_id', 'archive_notice_of_awards_attachments.notice_award_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }

    $title = "Notice of Awards";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("archives.noa", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year]);
  }

  public function filterNoticeOfAwards(Request $request)
  {
    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    $year = $request->year;
    $data = NoticeOfAward::where('notice_of_awards.date_generated', 'like', $year . '%')
      ->select('municipalities.*', 'notice_of_awards.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_notice_of_awards_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'notice_of_awards.project_bid_id')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_notice_of_awards_attachments', 'notice_of_awards.notice_award_id', 'archive_notice_of_awards_attachments.notice_award_id')
      ->get();

    $data2 = NoticeOfAward::where('notice_of_awards.date_generated', 'like', $year . '%')
      ->select('municipalities.*', 'notice_of_awards.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_notice_of_awards_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'notice_of_awards.project_bid_id')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->leftJoin('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_notice_of_awards_attachments', 'notice_of_awards.notice_award_id', 'archive_notice_of_awards_attachments.notice_award_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }

    return back()->withInput()->with("data", $data);
  }

  public function submitNoticeOfAward(Request $request)
  {
    $APP = new APP;
    $notice_of_award = NoticeOfAward::find($request->id);
    $clusters = $APP->getClusterBids($notice_of_award->project_bid_id);
    $procact = Procact::where('procact_id', $clusters[0]->procact_id)->first();
    $post_qualification_end = date('m/d/Y', strtotime($procact->post_qual));
    $data = $request->validate([
      "date_generated" => "required",
      "date_released" => "required|after_or_equal:date_generated|required_with:date_received_by_contractor|after_or_equal:" . $post_qualification_end,
      "date_received_by_contractor" => "required|after_or_equal:date_released|required_with:date_received_by_bac|after_or_equal:" . $post_qualification_end,
      "date_received_by_bac" => "required|after_or_equal:date_received_by_contractor|required_with:date_received_by_contractor|after_or_equal:" . $post_qualification_end,
    ]);
    $notice_of_awards_attachments = ArchiveNoticeOfAwardAttachments::where('notice_award_id', $request->id)->count();

    $attachments = $request->file('attachments');
    if ($notice_of_award != null) {
      // validate date received
      if (isset($attachments) === false && $notice_of_awards_attachments === 0) {
        foreach ($clusters as $value) {
          $id = NoticeOfAward::where('project_bid_id', $value->project_bid)->first();
          $notice_of_award = NoticeOfAward::find($id->notice_award_id);
          $notice_of_award->with_attachment = 0;
          $notice_of_award->save();
        }
        return back()->with('message', "missing_attachment");
      } else {
        if (isset($attachments)) {
          foreach ($attachments as $attachment) {
            $filename = $attachment->getClientOriginalName();
            $pieces = explode(".", $filename);
            $last_index = count($pieces) - 1;
            $new_name = date('m/d/Y', strtotime($request->date_received_by_contractor)) . "-noa-" . uniqid() . ".pdf";
            if ($pieces[$last_index] === "pdf") {
              Storage::disk('drive-d')->putFileAs('Archives/NoticeOfAwards', $attachment, $new_name);

              foreach ($clusters as $value) {
                $id = NoticeOfAward::where('project_bid_id', $value->project_bid)->first();
                $notice_of_award = NoticeOfAward::find($id->notice_award_id);
                ArchiveNoticeOfAwardAttachments::create([
                  "notice_award_id" => $notice_of_award->notice_award_id,
                  "attachment_name" => $new_name,
                ]);
              }
            }
          }
        }


        foreach ($clusters as $value) {
          $id = NoticeOfAward::where('project_bid_id', $value->project_bid)->first();
          $notice_of_award = NoticeOfAward::find($id->notice_award_id);
          $notice_of_award->with_attachment = 1;
          $notice_of_award->date_generated = Date('Y-m-d', strtotime($request->date_generated));
          $notice_of_award->date_released = Date('Y-m-d', strtotime($request->date_released));
          $notice_of_award->date_received_by_contractor = Date('Y-m-d', strtotime($request->date_received_by_contractor));
          $notice_of_award->date_received = Date('Y-m-d', strtotime($request->date_received_by_bac));
          $notice_of_award->noa_remarks = $request->remarks;
          $notice_of_award->save();
        }


        $procact = Procact::where('procact_id', $clusters[0]->procact_id)->first();
        if ($procact->award_notice != date("Y-m-d", strtotime($request->input("date_received_by_bac"))) && $procact->contract_signing === null) {
          $ProcurementController = new ProcurementController;
          $plan_ids_array = array_column((array)json_decode($clusters), 'plan_id');
          $plan_ids = implode(",", $plan_ids_array);
          $extend = $APP->extendSpecificProcess($plan_ids, "notice_of_award", $request->date_received_by_bac, "Automatic Extension");
          $parameters = ["plan_id" => $plan_ids, "award_notice_date" => date("m/d/Y", strtotime($request->date_received_by_contractor)), "bypass" => true];
          $request = new \Illuminate\Http\Request();
          $request->replace($parameters);
          $test = $ProcurementController->submitAwardNotice($request);
        }


        return back()->with('message', "success");
      }
    } else {
      return abort(403, 'Unknown Notice of Award');
    }
  }

  public function getNOAAttachments(Request $request)
  {
    $attachments = ArchiveNoticeOfAwardAttachments::where('notice_award_id', $request->notice_award_id)->orderBy('created_at', 'asc')->get();

    return $attachments;
  }

  public function viewNOAAttachment(Request $request)
  {
    $data = ArchiveNoticeOfAwardAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/NoticeOfAwards/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }


  public function viewNOAAttachments(Request $request)
  {
    $notice_of_award = NoticeOfAward::find($request->id);
    if ($notice_of_award != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_noa-" . $request->id;
      $attachments = ArchiveNoticeOfAwardAttachments::where("notice_award_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/NoticeOfAwards/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteNOAAttachment(Request $request)
  {
    $data = ArchiveNoticeOfAwardAttachments::find($request->id);
    $APP = new APP;
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/NoticeOfAwards/' . $data->attachment_name);
      ArchiveNoticeOfAwardAttachments::where('attachment_name', $data->attachment_name)->delete();
      $noa_attachments = ArchiveNoticeOfAwardAttachments::where('notice_award_id', $data->notice_award_id)->count();
      if ($noa_attachments === 0) {
        $notice_of_award = NoticeOfAward::find($data->notice_award_id);
        $clusters = $APP->getClusterBids($notice_of_award->project_bid_id);
        foreach ($clusters as $value) {
          $id = NoticeOfAward::where('project_bid_id', $value->project_bid)->first();
          $notice_of_award = NoticeOfAward::find($id->notice_award_id);
          $notice_of_award->with_attachment = 0;
          $notice_of_award->save();
        }
      }
    }

    $noa_attachments = ArchiveNoticeOfAwardAttachments::where('notice_award_id', $data->notice_award_id)->count();
    if ($noa_attachments === 0) {
      return "reload";
    } else {
      return "success";
    }
  }


  // Contracts

  public function getContracts(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = Contract::where('contracts.contract_date_generated', 'like', $year . '%')
      ->select('contracts.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_contract_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')

      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_contract_attachments', 'contracts.contract_id', 'archive_contract_attachments.contract_id')
      ->get();


    $data2 = Contract::where('contracts.contract_date_generated', 'like', $year . '%')
      ->select('contracts.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_contract_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_contract_attachments', 'contracts.contract_id', 'archive_contract_attachments.contract_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }
    $title = "Contracts";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("archives.contract", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year]);
  }

  public function filterContracts(Request $request)
  {
    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    $year = $request->year;

    $data = Contract::where('contracts.contract_date_generated', 'like', $year . '%')
      ->select('contracts.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_contract_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')

      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_contract_attachments', 'contracts.contract_id', 'archive_contract_attachments.contract_id')
      ->get();


    $data2 = Contract::where('contracts.contract_date_generated', 'like', $year . '%')
      ->select('contracts.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_contract_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_contract_attachments', 'contracts.contract_id', 'archive_contract_attachments.contract_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }

    return back()->withInput()->with("data", $data);
  }

  public function submitContract(Request $request)
  {
    $APP = new APP;
    $message = "success";
    $contract = Contract::find($request->id);
    $old_receive = $contract->contract_date_received_contractor;
    if ($contract != null) {
      $clusters = $APP->getClusterBids($contract->project_bid_id);
      foreach ($clusters as $cluster_bid) {
        $contract = Contract::where('project_bid_id', $cluster_bid->project_bid)->first();
        if ($contract->performance_bond_posted === null) {
          $message = "performance_bond_error";
        }
      }
      if ($message === "success") {
        $procact = Procact::where('procact_id', $clusters[0]->procact_id)->first();
        // $award_notice_end=date('m/d/Y', strtotime("+3 day", strtotime($procact->award_notice)));
        $noa = NoticeOfAward::where('project_bid_id', $clusters[0]->project_bid)->first();
        $noa_end = date('m/d/Y', strtotime($noa->date_received_by_contractor));
        $data = $request->validate([
          "date_generated" => "required",
          "date_released" => "required|after_or_equal:date_generated|after_or_equal:" . $noa_end,
          "date_received_by_contractor" => "required|after_or_equal:date_released|after_or_equal:date_released",
          "date_of_notarization" => "required|after_or_equal:date_received_contractor",
          "date_received_by_bac" => "required|after_or_equal:date_of_notarization"
        ]);


        $contract_attachments = ArchiveContractAttachments::where('contract_id', $request->id)->count();
        $attachments = $request->file('attachments');

        if (isset($attachments) === false && $contract_attachments === 0) {
          foreach ($clusters as $value) {
            $id = Contract::where('project_bid_id', $value->project_bid)->first();
            $contract = Contract::find($id->contract_id);
            $contract->with_attachment = 0;
            $contract->save();
          }
          return back()->with('message', "missing_attachment");
        } else {
          if (isset($attachments)) {
            foreach ($attachments as $attachment) {
              $filename = $attachment->getClientOriginalName();
              $pieces = explode(".", $filename);
              $last_index = count($pieces) - 1;
              $new_name = date('m/d/Y', strtotime($request->date_received_by_contractor)) . "-contract-" . uniqid() . ".pdf";
              if ($pieces[$last_index] === "pdf") {
                Storage::disk('drive-d')->putFileAs('Archives/Contracts', $attachment, $new_name);

                foreach ($clusters as $value) {
                  $id = Contract::where('project_bid_id', $value->project_bid)->first();
                  $contract = Contract::find($id->contract_id);
                  ArchiveContractAttachments::create([
                    "contract_id" => $contract->contract_id,
                    "attachment_name" => $new_name,
                  ]);
                }
              }
            }
          }
          foreach ($clusters as $value) {
            $id = Contract::where('project_bid_id', $value->project_bid)->first();
            $contract = Contract::find($id->contract_id);
            $contract->with_attachment = 1;
            $contract->contract_date_generated = date('Y-m-d', strtotime($request->date_generated));
            $contract->contract_release_date = date('Y-m-d', strtotime($request->date_released));
            $contract->contract_date_received_contractor = date('Y-m-d', strtotime($request->date_received_by_contractor));
            $contract->contract_date_of_notarization = date('Y-m-d', strtotime($request->date_of_notarization));
            $contract->contract_receive_date = date('Y-m-d', strtotime($request->date_received_by_bac));
            $contract->contract_remarks = $request->remarks;
            $contract->save();
          }

          if ($request->date_received_by_bac != null && date('Y-m-d', strtotime($request->date_received_by_contractor)) != $old_receive) {
            $plan_ids_array = array_column((array)json_decode($clusters), 'plan_id');
            $plan_ids = implode(",", $plan_ids_array);
            $procact = Procact::where('procact_id', $clusters[0]->procact_id)->first();
            if ($procact->award_notice != date("Y-m-d", strtotime($request->input("date_received_by_bac"))) && $procact->proceed_notice === null) {
              $extend = $APP->extendSpecificProcess($plan_ids, "contract_preparation_signing", $request->date_received_by_bac, "Automatic Extension");
              $ProcurementController = new ProcurementController;

              $parameters = ["plan_id" => $plan_ids, "contract_preparation_and_signing_date" => $request->date_received_by_contractor, "bypass" => true];
              $request = new \Illuminate\Http\Request();
              $request->replace($parameters);
              $ProcurementController->submitContractPreparationAndSigning($request);
            }
          }
        }
      }
      return back()->with('message', $message);
    } else {
      return abort(403, 'Unknown Contract');
    }
  }

  public function getContractAttachments(Request $request)
  {
    $attachments = ArchiveContractAttachments::where('contract_id', $request->contract_id)->orderBy('created_at', 'asc')->get();

    return $attachments;
  }

  public function viewContractAttachment(Request $request)
  {
    $data = ArchiveContractAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/Contracts/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }


  public function viewContractAttachments(Request $request)
  {
    $contract = Contract::find($request->id);
    if ($contract != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_Contract-" . $request->id;
      $attachments = ArchiveContractAttachments::where("contract_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/Contracts/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached file");
      }
    } else {
      abort(404);
    }
  }

  public function deleteContractAttachment(Request $request)
  {
    $data = ArchiveContractAttachments::find($request->id);
    $APP = new APP;
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/Contracts/' . $data->attachment_name);
      ArchiveContractAttachments::where('attachment_name', $data->attachment_name)->delete();
      $contract_attachments = ArchiveContractAttachments::where('contract_id', $data->contract_id)->count();
      if ($contract_attachments === 0) {
        $contract = Contract::find($data->contract_id);
        $clusters = $APP->getClusterBids($contract->project_bid_id);
        foreach ($clusters as $value) {
          $id = Contract::where('project_bid_id', $value->project_bid)->first();
          $contract = Contract::find($id->contract_id);
          $contract->with_attachment = 0;
          $contract->save();
        }
      }
    }
    $contract_attachments = ArchiveContractAttachments::where('contract_id', $data->contract_id)->count();
    if ($contract_attachments === 0) {
      return "reload";
    } else {
      return "success";
    }
  }

  // NTPs

  public function getNTPs(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = NoticeToProceed::where('notice_to_proceeds.ntp_date_generated', 'like', $year . '%')
      ->select('notice_to_proceeds.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_notice_to_proceed_attachments.created_at as date_uploaded', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"))
      ->join('project_bidders', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_notice_to_proceed_attachments', 'notice_to_proceeds.ntp_id', 'archive_notice_to_proceed_attachments.ntp_id')
      ->get();


    $data2 = NoticeToProceed::where('notice_to_proceeds.ntp_date_generated', 'like', $year . '%')
      ->select('notice_to_proceeds.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_notice_to_proceed_attachments.created_at as date_uploaded', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"))
      ->join('project_bidders', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_notice_to_proceed_attachments', 'notice_to_proceeds.ntp_id', 'archive_notice_to_proceed_attachments.ntp_id')
      ->get();



    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }
    $title = "Notice To Proceeds";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("archives.ntp", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year]);
  }

  public function filterNTPs(Request $request)
  {
    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    $year = $request->year;

    $data = NoticeToProceed::where('notice_to_proceeds.ntp_date_generated', 'like', $year . '%')
      ->select('notice_to_proceeds.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_notice_to_proceed_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_notice_to_proceed_attachments', 'notice_to_proceeds.ntp_id', 'archive_notice_to_proceed_attachments.ntp_id')
      ->get();


    $data2 = NoticeToProceed::where('notice_to_proceeds.ntp_date_generated', 'like', $year . '%')
      ->select('notice_to_proceeds.*', 'procacts.*', 'project_plans.*', 'contractors.*', 'procurement_modes.*', 'funds.*', 'archive_notice_to_proceed_attachments.created_at as date_uploaded')
      ->join('project_bidders', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('archive_notice_to_proceed_attachments', 'notice_to_proceeds.ntp_id', 'archive_notice_to_proceed_attachments.ntp_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }

    return back()->withInput()->with("data", $data);
  }

  function computeDuration(Request $request)
  {
    $APP = new APP;
    $ntp = NoticeToProceed::find($request->ntp_id);
    $clusters = $APP->getClusterBids($ntp->project_bid_id);
    $duration = 0;
    foreach ($clusters as $plan) {
      $duration = $duration + $plan->duration;
    }
    return $duration;
  }

  public function submitNTP(Request $request)
  {
    $APP = new APP;
    $NTP = NoticeToProceed::find($request->id);
    $old_receive = $NTP->ntp_date_received_by_contractor;
    $ntp_attachments = ArchiveNoticeToProceedAttachments::where('ntp_id', $request->id)->count();
    $attachments = $request->file('attachments');
    $clusters = $APP->getClusterBids($NTP->project_bid_id);
    // $procact=Procact::where('procact_id',$clusters[0]->procact_id)->first();
    // $contract_signing=date('m/d/Y', strtotime("+3 day", strtotime($procact->contract_signing)));
    $noa = NoticeOfAward::where('project_bid_id', $clusters[0]->project_bid)->first();
    $noa_end = date('m/d/Y', strtotime($noa->date_received_by_contractor));
    $data = $request->validate([
      "date_generated" => "required",
      "date_released" => "required|after_or_equal:date_generated|required_with:date_received_by_contractor|after_or_equal:" . $noa_end,
      "date_received_by_contractor" => "nullable|after_or_equal:date_released|required_with:date_received_by_bac",
      "start_date" => "nullable|after:date_received_by_contractor",
      "end_date" => "nullable|after:start_date|required_with:date_received_by_contractor",
      "date_received_by_bac" => "nullable|after_or_equal:date_received_by_contractor|required_with:date_received_by_contractor",
    ]);

    if ($NTP != null) {
      if (isset($attachments) === false && $ntp_attachments === 0) {
        foreach ($clusters as $value) {
          $id = NoticeToProceed::where('project_bid_id', $value->project_bid)->first();
          $NTP = NoticeToProceed::find($id->ntp_id);
          $NTP->with_attachment = 0;
          $NTP->save();
        }
        return back()->with('message', "missing_attachment");
      } else {
        if (isset($attachments)) {
          foreach ($attachments as $attachment) {
            $filename = $attachment->getClientOriginalName();
            $pieces = explode(".", $filename);
            $last_index = count($pieces) - 1;
            $new_name = date('m/d/Y', strtotime($request->date_received_by_contractor)) . "-NTP-" . uniqid() . ".pdf";
            if ($pieces[$last_index] === "pdf") {
              Storage::disk('drive-d')->putFileAs('Archives/NTPs', $attachment, $new_name);

              foreach ($clusters as $value) {
                $id = NoticeToProceed::where('project_bid_id', $value->project_bid)->first();
                $NTP = NoticeToProceed::find($id->ntp_id);
                ArchiveNoticeToProceedAttachments::create([
                  "ntp_id" => $NTP->ntp_id,
                  "attachment_name" => $new_name,
                ]);
              }
            }
          }
        }
        foreach ($clusters as $value) {
          $id = NoticeToProceed::where('project_bid_id', $value->project_bid)->first();
          $NTP = NoticeToProceed::find($id->ntp_id);
          $id = NoticeOfAward::where('project_bid_id', $value->project_bid)->first();
          $NTP->with_attachment = 1;
          $NTP->ntp_date_generated = Date('Y-m-d', strtotime($request->date_generated));
          $NTP->ntp_date_released = Date('Y-m-d', strtotime($request->date_released));
          $NTP->ntp_date_received_by_contractor = Date('Y-m-d', strtotime($request->date_received_by_contractor));
          $NTP->ntp_date_received = Date('Y-m-d', strtotime($request->date_received_by_bac));
          $NTP->duration_start_date = Date('Y-m-d', strtotime($request->start_date));
          $NTP->duration_end_date = Date('Y-m-d', strtotime($request->end_date));

          $NTP->ntp_remarks = $request->remarks;
          $NTP->save();
        }

        if ($old_receive != Date('Y-m-d', strtotime($request->date_received_by_bac))) {
          $plan_ids_array = array_column((array)json_decode($clusters), 'plan_id');
          $plan_ids = implode(",", $plan_ids_array);
          $ProcurementController = new ProcurementController;
          $extend = $APP->extendSpecificProcess($plan_ids, "notice_to_proceed", $request->input("date_received_by_bac"), "Automatic Extension");
          $parameters = ["plan_id" => $plan_ids, "notice_to_proceed_date" => $request->input("date_received_by_contractor"), "bypass" => true];
          $request = new \Illuminate\Http\Request();
          $request->replace($parameters);
          $ProcurementController->submitNoticeToProceed($request);
        }

        return back()->with('message', "success");
      }
    } else {
      return abort(403, 'Unknown Notice to Proceed');
    }
  }

  public function getNTPAttachments(Request $request)
  {
    $attachments = ArchiveNoticeToProceedAttachments::where('ntp_id', $request->ntp_id)->orderBy('created_at', 'asc')->get();

    return $attachments;
  }

  public function viewNTPAttachment(Request $request)
  {
    $data = ArchiveNoticeToProceedAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/NTPs/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }


  public function viewNTPAttachments(Request $request)
  {
    $NTP = NoticeToProceed::find($request->id);
    if ($NTP != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_NTP-" . $request->id;
      $attachments = ArchiveNoticeToProceedAttachments::where("ntp_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/NTPs/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteNTPAttachment(Request $request)
  {
    $data = ArchiveNoticeToProceedAttachments::find($request->id);
    $APP = new APP;
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/NTPs/' . $data->attachment_name);
      ArchiveNoticeToProceedAttachments::where('attachment_name', $data->attachment_name)->delete();
      $ntp_attachments = ArchiveNoticeToProceedAttachments::where('ntp_id', $data->ntp_id)->count();
      if ($ntp_attachments === 0) {
        $NTP = NoticeToProceed::find($data->ntp_id);
        $clusters = $APP->getClusterBids($NTP->project_bid_id);
        foreach ($clusters as $value) {
          $id = NoticeToProceed::where('project_bid_id', $value->project_bid)->first();
          $NTP = NoticeToProceed::find($id->ntp_id);
          $NTP->with_attachment = 0;
          $NTP->save();
        }
      }
    }
    $ntp_attachments = ArchiveNoticeToProceedAttachments::where('ntp_id', $data->ntp_id)->count();
    if ($ntp_attachments === 0) {
      return "reload";
    } else {
      return "success";
    }
  }



  public function getNoticeOfDisqualification(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $type = "NOD";
    $title = "Notice Of Disqualification";

    $data = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'rfqs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')
      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data2 = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'bid_docs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')

      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }


    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("archives.notice", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year, "type" => $type]);
  }



  // Notices
  public function getNoticeOfIneligibility(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = "2021";
    }
    $type = "NOI";
    $title = "Notice Of Ineligibility";

    $data = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'rfqs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')
      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data2 = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'bid_docs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')

      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }


    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("archives.notice", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year, "type" => $type]);
  }

  public function getNoticeofPostQualification(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $type = "NOPQ";
    $title = "Notice Of Post Qualification";


    $data = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->where('mr_id', null)
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'rfqs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')
      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('motion_for_reconsideration_project_bid', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data2 = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->where('mr_id', null)
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'bid_docs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')
      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->leftJoin('motion_for_reconsideration_project_bid', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }


    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("archives.notice", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year, "type" => $type]);
  }

  public function getNoticeofPostDisqualification(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $type = "NOPD";
    $title = "Notice Of Post Disqualification";

    $data = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'rfqs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')
      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data2 = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'bid_docs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')

      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("archives.notice", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year, "type" => $type]);
  }

  public function getNoticeToLosingBidder(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $type = "NTLB";
    $title = "Notice To Losing Bidder";

    $data = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'rfqs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')
      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data2 = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'bid_docs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')

      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->orderBy('project_bidder_notices.project_bidder_notice_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }


    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("archives.notice", ['links' => $links, 'user_privilege' => $user_privilege, 'data' => $data, "title" => $title, "year" => $year, "type" => $type]);
  }

  public function filterNotice(Request $request)
  {
    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    $year = $request->year;
    $type = $request->notice_type;

    $data = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'rfqs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')
      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->get();

    $data2 = ProjectBidderNotice::where([['project_bidder_notices.notice_type', $type], ['project_bidder_notices.date_generated', 'like', $year . '%']])
      ->select('project_bidders.project_bid as main_id', 'project_bidders.*', 'bid_docs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated')
      ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->get();

    $data = json_decode(json_encode($data));
    $data2 = json_decode(json_encode($data2));

    foreach ($data2 as $row) {
      array_push($data, $row);
    }

    return back()->withInput()->with('data', $data);
  }

  public function submitProjectBidderNotice(Request $request)
  {
    $notice = ProjectBidderNotice::find($request->id);
    $data = $request->validate([
      "date_generated" => "required",
      "date_released" => "required|after_or_equal:date_generated|required_with:date_received_by_contractor",
      "date_received_by_contractor" => "required|after_or_equal:date_released|required_with:date_received_by_bac",
      "mr_due_date" => "nullable|after_or_equal:date_released",
      "date_received_by_bac" => "required|after_or_equal:date_received_by_contractor|required_with:date_received_by_contractor",
    ]);

    $notice_attachments = ArchiveNoticeAttachments::where('project_bidder_notice_id', $request->id)->count();
    $APP = new APP;
    $clusters = $APP->getClusterBids($notice->project_bid);
    $attachments = $request->file('attachments');
    if ($notice != null) {
      if (isset($attachments) === false && $notice_attachments === 0) {
        foreach ($clusters as $value) {
          $id = ProjectBidderNotice::where([['project_bid', $value->project_bid], ['notice_type', $notice->notice_type]])->first();
          $notice = ProjectBidderNotice::find($id->project_bidder_notice_id);
          $notice->with_attachment = 0;
          $notice->save();
        }
        return back()->with('message', "missing_attachment");
      } else {
        if (isset($attachments)) {
          foreach ($attachments as $attachment) {
            $filename = $attachment->getClientOriginalName();
            $pieces = explode(".", $filename);
            $last_index = count($pieces) - 1;
            $new_name = $notice->notice_date_released . "-ProjectBidderNotice-" . uniqid() . ".pdf";
            if ($pieces[$last_index] === "pdf") {
              Storage::disk('drive-d')->putFileAs('Archives/ProjectBidderNotices', $attachment, $new_name);

              foreach ($clusters as $value) {
                $id = ProjectBidderNotice::where([['project_bid', $value->project_bid], ['notice_type', $notice->notice_type]])->first();
                $notice = ProjectBidderNotice::find($id->project_bidder_notice_id);
                ArchiveNoticeAttachments::create([
                  "project_bidder_notice_id" => $notice->project_bidder_notice_id,
                  "attachment_name" => $new_name,
                ]);
              }
            }
          }
        }
        foreach ($clusters as $value) {
          $id = ProjectBidderNotice::where([['project_bid', $value->project_bid], ['notice_type', $notice->notice_type]])->first();
          $notice = ProjectBidderNotice::find($id->project_bidder_notice_id);
          $notice->with_attachment = 1;
          $notice->date_generated = date('Y-m-d', strtotime($request->date_generated));
          $notice->date_released = date('Y-m-d', strtotime($request->date_released));
          $notice->date_received_by_contractor = date('Y-m-d', strtotime($request->date_received_by_contractor));
          $notice->date_received = date('Y-m-d', strtotime($request->date_received_by_bac));
          $notice->mr_due_date = date('Y-m-d', strtotime($request->mr_due_date));
          $notice->notice_remarks = $request->remarks;
          $notice->save();
        }
        return back()->with('message', "success");
      }
    } else {
      return abort(403, 'Unknown Notice');
    }
  }

  public function getProjectBidderNoticeAttachments(Request $request)
  {
    $attachments = ArchiveNoticeAttachments::where('project_bidder_notice_id', $request->project_bidder_notice_id)->orderBy('created_at', 'asc')->get();

    return $attachments;
  }

  public function viewProjectBidderNoticeAttachment(Request $request)
  {
    $data = ArchiveNoticeAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/ProjectBidderNotices/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }


  public function viewProjectBidderNoticeAttachments(Request $request)
  {
    $notice = ProjectBidderNotice::find($request->id);
    if ($notice != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_ProjectBidderNotice-" . $request->id;
      $attachments = ArchiveNoticeAttachments::where("project_bidder_notice_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/ProjectBidderNotices/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteProjectBidderNoticeAttachment(Request $request)
  {
    $data = ArchiveNoticeAttachments::find($request->id);
    $APP = new APP;
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/ProjectBidderNotices/' . $data->attachment_name);
      ArchiveNoticeAttachments::where('attachment_name', $data->attachment_name)->delete();
      $notice_attachments = ArchiveNoticeAttachments::where('project_bidder_notice_id', $data->project_bidder_notice_id)->count();

      if ($notice_attachments === 0) {
        $notice = ProjectBidderNotice::find($data->project_bidder_notice_id);
        $clusters = $APP->getClusterBids($notice->project_bid);
        foreach ($clusters as $value) {
          $id = ProjectBidderNotice::where([['project_bid', $value->project_bid], ['notice_type', $notice->notice_type]])->first();
          $notice = ProjectBidderNotice::find($id->project_bidder_notice_id);
          $notice->with_attachment = 0;
          $notice->date_received = null;
          $notice->save();
        }
      }
    }

    $attachments = ArchiveNoticeAttachments::where('project_bidder_notice_id', $data->project_bidder_notice_id)->count();
    if ($attachments === 0) {
      return "reload";
    } else {
      return "success";
    }
  }





  // Archive Order Start
  public function getOrderArchive(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = Order::where([['order_date_generated', 'like', $year . '%']])
      ->select('order_request.*', 'request_for_extension.*')
      ->join('request_for_extension', 'request_for_extension.request_id', 'order_request.request_id')
      ->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.order', ['links' => $links, 'user_privilege' => $user_privilege, 'year' => $year, "title" => "Archive Orders", "data" => $data]);
  }

  public function filterOrder(Request $request)
  {

    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }

    $data = Order::where([['order_date_generated', 'like', $year . '%']])
      ->select('order_request.*', 'request_for_extension.*')
      ->join('request_for_extension', 'request_for_extension.request_id', 'order_request.request_id')
      ->get();

    return back()->with('data', $data)->withInput();
  }

  public function submitOrder(Request $request)
  {

    $id = $request->id;
    $message = "success";
    $attachments = $request->file('attachments');


    $order_request = Order::find($id);

    if (isset($attachments)) {
      // save attachments to folder and database
      foreach ($attachments as $attachment) {
        $filename = $attachment->getClientOriginalName();
        $pieces = explode(".", $filename);
        $last_index = count($pieces) - 1;
        $new_name = "order" . $order_request->order_number . "-" . uniqid() . ".pdf";
        if ($pieces[$last_index] === "pdf") {
          Storage::disk('drive-d')->putFileAs('Archives/Orders', $attachment, $new_name);

          ArchiveOrderAttachments::create([
            "order_id" => $order_request->order_id,
            "attachment_name" => $new_name,
          ]);
        }
      }
      $order_request->order_with_attachment = true;
      $order_request->save();
    } else {
      $existing_attachments = ArchiveOrderAttachments::where("order_id", $request->id)->count();
      if ($existing_attachments === 0) {
        $message = "missing_attachment";
      }
    }

    if ($message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getArchiveOrderAttachments(Request $request)
  {
    $attachments = ArchiveOrderAttachments::where('order_id', $request->order_id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function viewOrderAttachment(Request $request)
  {
    $data = ArchiveOrderAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/Orders/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewOrderAttachments(Request $request)
  {
    $order_request = Order::find($request->id);
    if ($order_request != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_orders-" . $request->id;
      $attachments = ArchiveOrderAttachments::where("order_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/Orders/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteOrderAttachment(Request $request)
  {
    $data = ArchiveOrderAttachments::where('id', $request->id)->first();
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/Orders/' . $data->attachment_name);
      $delete = ArchiveOrderAttachments::where('id', $request->id)->delete();
    }
    $count = ArchiveOrderAttachments::where('order_id', $data->order_id)->count();

    if ($count === 0) {
      $order = Order::find($data->order_id);
      $order->order_with_attachment = false;
      $order->save();
      return "reload";
    } else {
      return "success";
    }
  }


  // Archive Orders End


  // Mutual Contract Termination
  public function getTerminationArchive(Request $request)
  {
    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $APP = new APP;
    $raw_data = Termination::where('termination.created_at', "like", $year . "%")
      ->orderBy("termination_id", "desc")
      ->join('governors', 'governors.governor_id', 'termination.governor_id')
      ->get();
    $data = [];
    foreach ($raw_data as $termination) {
      $bid_details = $APP->getBid($termination->project_bid);
      $data[] = [
        'termination_id' => $termination->termination_id,
        'project_number' => $bid_details->project_no,
        'procact_id' =>  $bid_details->procact_id,
        'project_title' => $bid_details->project_title,
        'project_bid' =>  $termination->project_bid,
        'contractor' => $bid_details->business_name,
        'governor_id' => $termination->governor_id,
        'governor' => $termination->name,
        'reason' =>  $termination->reason,
        'date_of_termination' =>  $termination->date_of_termination,
        'with_attachment' =>  $termination->with_attachment,
      ];
    }
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('archives.termination', ['links' => $links, 'user_privilege' => $user_privilege, 'year' => $year, "title" => "Archive Mutual Termination of Contracts", "data" => $data]);
  }

  public function filterTermination(Request $request)
  {

    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    if (isset($request->year)) {
      $year = $request->year;
    } else {
      $year = date('Y');
    }
    $APP = new APP;
    $raw_data = Termination::where('termination.created_at', "like", $year . "%")
      ->orderBy("termination_id", "desc")
      ->join('governors', 'governors.governor_id', 'termination.governor_id')
      ->get();
    $data = [];
    foreach ($raw_data as $termination) {
      $bid_details = $APP->getBid($termination->project_bid);
      $data[] = [
        'termination_id' => $termination->termination_id,
        'project_number' => $bid_details->project_no,
        'procact_id' =>  $bid_details->procact_id,
        'project_title' => $bid_details->project_title,
        'project_bid' =>  $termination->project_bid,
        'contractor' => $bid_details->business_name,
        'governor_id' => $termination->governor_id,
        'governor' => $termination->name,
        'reason' =>  $termination->reason,
        'date_of_termination' =>  $termination->date_of_termination,
        'with_attachment' =>  $termination->with_attachment,
      ];
    }


    return back()->with('data', $data)->withInput();
  }

  public function submitTermination(Request $request)
  {
    $data = $request->validate([
      "date_of_termination" => "required",
    ]);

    $id = $request->termination_id;
    $message = "success";
    $attachments = $request->file('attachments');
    $APP = new APP;
    $termination_request = Termination::find($id);
    $cluster_bids = $APP->getClusterBids($request->project_bid);
    if (isset($attachments)) {
      // save attachments to folder and database
      foreach ($attachments as $attachment) {
        $filename = $attachment->getClientOriginalName();
        $pieces = explode(".", $filename);
        $last_index = count($pieces) - 1;
        $new_name = "termination" . $termination_request->termination_id . "-" . uniqid() . ".pdf";
        if ($pieces[$last_index] === "pdf") {
          Storage::disk('drive-d')->putFileAs('Archives/Terminations', $attachment, $new_name);

          foreach ($cluster_bids as $bid) {
            $bid_termination = Termination::where('project_bid', $bid->project_bid)->first();
            ArchiveTerminationAttachments::create([
              "termination_id" => $bid_termination->termination_id,
              "attachment_name" => $new_name,
            ]);
          }
        }
      }
      foreach ($cluster_bids as $bid) {
        $bid_termination = Termination::where('project_bid', $bid->project_bid)->first();
        $termination = Termination::find($bid_termination->termination_id);
        $termination->with_attachment = true;
        $termination->date_of_termination = Date('Y-m-d', strtotime($request->date_of_termination));
        $termination->save();
      }
    } else {
      $existing_attachments = ArchiveTerminationAttachments::where("termination_id", $id)->count();
      if ($existing_attachments === 0) {
        $message = "missing_attachment";
      }
    }

    if ($message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function getArchiveTerminationAttachments(Request $request)
  {
    $attachments = ArchiveTerminationAttachments::where('termination_id', $request->termination_id)->orderBy('created_at', 'asc')->get();
    return $attachments;
  }

  public function viewTerminationAttachment(Request $request)
  {
    $data = ArchiveTerminationAttachments::where('id', $request->id)->first();
    if ($data != null) {
      return  response()->file(Storage::disk('drive-d')->path('Archives/Terminations/' . $data->attachment_name));
    } else {
      return abort(404);
    }
  }

  public function viewTerminationAttachments(Request $request)
  {
    $termination_request = Termination::find($request->id);
    if ($termination_request != null) {
      // Merge PDFS and show
      $initial = 0;
      $pdfMerger = PDFMerger::init();
      $name = "Archives_terminations-" . $request->id;
      $attachments = ArchiveTerminationAttachments::where("termination_id", $request->id)->get();

      if (count($attachments) > 0) {
        foreach ($attachments as $attachment) {
          $pdfMerger->addPDF(Storage::disk('drive-d')->path('Archives/Terminations/' . $attachment->attachment_name), 'all');
        }
        $pdfMerger->merge();
        $pdfMerger->save(storage_path("app/public/temp_archive/" . $name . ".pdf"));
        return  response()->file(storage_path("app/public/temp_archive/" . $name . ".pdf"))->deleteFileAfterSend(true);
      } else {
        abort(403, "No attached files");
      }
    } else {
      abort(404);
    }
  }

  public function deleteTerminationAttachment(Request $request)
  {
    $data = ArchiveTerminationAttachments::where('id', $request->id)->first();
    if ($data != null) {
      Storage::disk('drive-d')->delete('Archives/Terminations/' . $data->attachment_name);
      $delete = ArchiveTerminationAttachments::where('attachment_name', $data->attachment_name)->delete();
    }
    $count = ArchiveTerminationAttachments::where('termination_id', $data->termination_id)->count();

    if ($count === 0) {
      $APP = new APP;
      $base_termination = Termination::where('termination_id', $data->termination_id)->first();
      $cluster_bids = $APP->getClusterBids($base_termination->project_bid);
      foreach ($cluster_bids as $bid) {
        $bid_termination = Termination::where('project_bid', $bid->project_bid)->first();
        $termination = Termination::find($bid_termination->termination_id);
        $termination->with_attachment = false;
        $termination->save();
      }
      return "reload";
    } else {
      return "success";
    }
  }


  // End of Contract Termination


}
