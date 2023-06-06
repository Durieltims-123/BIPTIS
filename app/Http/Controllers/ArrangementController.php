<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use Validator;

class ArrangementController extends Controller
{
  public function viewProjectArrangement()
  {
    $year=date('Y');
    $title="Project Arrangement";
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();

    return view('admin.project_arrangement',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>$title,'year'=>$year]);
  }

  public function getProjects(Request $request)
  {
    $year=date('Y');
    $title="EDIT Arrangement";
    $APP = new APP;
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();

    return view('admin.prepare_contracts',['links'=>$links,'user_privilege'=>$user_privilege,'title'=>$title,'project_plans'=>$project_plans,'year'=>$year]);
  }

  public function getDateProjects(Request $request)
  {
    $data=$request->validate([
      "date_opened"=>"required"
    ]);
    $date=$request->input("date_opened");
    $APP=new APP();
    $project_plans=$APP->getDateProjects($date);

    return back()->withInput()->with("project_plans",$project_plans);
  }

  public function submitArrangement(Request $request)
  {

    $procact_ids=$request->input("procact_ids");
    $procact_ids_array=explode(",",$procact_ids);
    $order=1;

    foreach ($procact_ids_array as $procact_id) {
      DB::table('procacts')
      ->where('procact_id',$procact_id)
      ->update([
        "itb_arrangement"=>$order,
        "updated_at"=>now()
      ]);
      $order=$order+1;
    }

    $date=$request->input("date_opened_save");
    $APP=new APP();
    $project_plans=$APP->getDateProjects($date);

    return back()->withInput()->with("project_plans",$project_plans)->with("message","success");

  }

  public function test()
  {
    // code...
  }
}
