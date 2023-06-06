<?php

namespace App\Http\Controllers;

use App\{User, Link, UserLink, APP};
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
  /**
   * Display a listing of the users
   *
   * @param  \App\User  $model
   * @return \Illuminate\View\View
   */


  public function __construct()
  {
    $this->middleware('auth');
  }


  public function index(User $model)
  {
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('users.index', ['links' => $links, 'user_privilege' => $user_privilege]);
  }

  public function getUsers()
  {

    // // $noa_array=[];
    // $array=DB::table('notice_to_proceeds')->where([['ntp_date_received_by_contractor','<>',null]])->get();
    // $count=0;
    // // $APP=new APP;
    // foreach($array as $data){
    //   $noa_i=DB::table('notice_to_proceeds')->where("ntp_id",$data->ntp_id)->first();
    //   $plans=DB::table('project_plans')->where('project_bid_id',$data->project_bid_id)
    //   ->join('procacts','procacts.procact_id','project_plans.latest_procact_id')
    //   ->first();
    //   //   // dd($plans);
    //   //   if($plans===null){
    //   //     dd($noa_i);
    //   //     dd($APP->getBid($data->project_bid_id));
    //   //     $count=$count+1;
    //   //   }
    //   //   if($plans->proceed_notice!=$noa_i->ntp_date_received_by_contractor){
    //   $update=DB::table('project_activity_status')->where('procact_id',$plans->latest_procact_id)->update([
    //     "main_status"=>"completed"
    //   ]);
    //   //
    //   //     $update=DB::table('project_plans')->where('plan_id',$plans->plan_id)->update([
    //   //       "status"=>"completed"
    //   //     ]);
    //   //     // dump($data->ntp_id.":".$plans->proceed_notice."    ".$noa_i->ntp_date_received_by_contractor);
    //   $count=$count+1;
    //   //   }
    //   //   // $update=DB::table('procacts')->where('procact_id',$plans->latest_procact_id)->update([
    //   //   //   "contract_signing"=>$noa_i->contract_date_received_contractor
    //   //   // ]);
    //   //   // dump($update);
    //   //
    // }
    // //
    // dd($count);
    $offices = DB::table("roles")->get();
    $display_links = Link::with('getLinkPrivileges')->orderBy('link_order', 'asc')->get();
    $users = User::with(['user_links', 'user_roles', 'user_roles.role'])
      ->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("admin.users", ['links' => $links, 'user_privilege' => $user_privilege, "users" => $users, "offices" => $offices, "display_links" => $display_links]);
  }

  public function submitUser(Request $request)
  {
    $data = $request->validate([
      "name" => "required",
      "email" => "required|email",
      "office" => "required",
      "password" => "nullable|required_with:verify_password",
      "verify_password" => "nullable|required_with:password|same:password",
      "privileges" => "required",
    ]);

    $id = $request->input("id");

    if ($id === null) {
      $data = $request->validate([
        "password" => "required",
        "verify_password" => "required|same:password"
      ]);
    }
    $name = $request->input("name");
    $email = $request->input("email");
    $password = $request->input("password");
    $office = $request->input("office");
    $privileges = explode(',', $request->privileges);
    $message = "success";
    $administrator = true;
    if ($request->administrator === "No") {
      $administrator = false;
    }
    if ($id === null) {
      $duplicate = DB::table("users")->where([["users.email", $email], ["user_roles.role_id", $office]])->join("user_roles", "user_roles.user_id", "users.id")->count();
      if ($duplicate > 0) {
        $message = "duplicate";
      } else {
        DB::table("users")->insert([
          "email" => $email,
          "name" => $name,
          "administrator" => $administrator,
          "password" => Hash::make($password),
          "created_at" => now(),
          "updated_at" => now()
        ]);

        $latest_user = DB::table("users")->where("email", $email)->orderBy("created_at", "desc")->first();

        DB::table("user_roles")->insert([
          "user_id" => $latest_user->id,
          "role_id" => $office
        ]);
      }

      // Add User Links
      foreach ($privileges  as $privilege) {
        UserLink::create([
          "user_id" => $latest_user->id,
          "link_privilege_id" => $privilege
        ]);
      }
    } else {
      getUserLinks();
      $duplicate = DB::table("users")->where([["users.email", $email], ["user_roles.role_id", $office], ["users.id", "<>", $id]])->join("user_roles", "user_roles.user_id", "users.id")->count();
      if ($duplicate > 0) {
        $message = "duplicate";
      } else {

        if ($password != null) {
          DB::table("users")->where("id", $id)->update([
            "email" => $email,
            "name" => $name,
            "administrator" => $administrator,
            "password" => Hash::make($password),
            "created_at" => now(),
            "updated_at" => now()
          ]);
        } else {
          DB::table("users")->where("id", $id)->update([
            "email" => $email,
            "name" => $name,
            "administrator" => $administrator,
            "created_at" => now(),
            "updated_at" => now()
          ]);
        }


        DB::table("user_roles")->where("user_id", $id)->update([
          "role_id" => $office
        ]);
        // Adjust User Links

        $delete_links = UserLink::where("user_id", $id)
          ->whereNotIn('link_privilege_id', $privileges)
          ->delete();

        foreach ($privileges  as $privilege) {
          UserLink::firstOrCreate([
            "user_id" => $id,
            "link_privilege_id" => $privilege
          ]);
        }
      }
    }

    return back()->with("message", $message);
  }

  public function deleteUser($id)
  {
    $project_logs = DB::table("project_logs")->where("user_id", $id)->count();
    $disqualification_records = DB::table("disqualification_records")->where("user_id", $id)->count();

    $count = $project_logs + $disqualification_records;
    if ($count > 0) {
      $message = "delete_error";
    } else {
      $message = "success";
      DB::table('users')->where("id", $id)->delete();
    }

    return back()->with("message", $message);
  }


  public function Redirect()
  {
    $bacsec = DB::table('user_roles')->where([['users.email', auth()->user()->email]])
      ->whereIn('user_roles.role_id', [1, 4, 5])
      ->join('users', 'users.id', 'user_roles.user_id')->count();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('welcome', ["links" => $links, 'user_privilege' => $user_privilege]);
  }
}
