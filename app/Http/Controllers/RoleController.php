<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use Validator;

class RoleController extends Controller
{
  public function getRoles()
  {
    $roles=DB::table("roles")->get();
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.roles",['links'=>$links,'user_privilege'=>$user_privilege,"roles"=>$roles]);
  }

  public function submitRole(Request $request)
  {
    $roles=DB::table("roles")->get();

    $data=$request->validate([
      "display_name"=>"required",
      "name"=>"required"
    ]);

    $id=$request->input("id");
    $display_name=$request->input("display_name");
    $name=$request->input("name");
    $message="success";

    // add
    if($id==null){
      // duplicate
      $duplicate=DB::table("roles")->where([["display_name",$display_name]])->orWhere([["name",$name]])->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{
        DB::table('roles')->insert([
          "name"=>$name,
          "display_name"=>$display_name
        ]);
      }

    }
    // edit
    else{

      // duplicate
      $duplicate=DB::table("roles")->where([["display_name",$display_name],["id","<>",$id]])->orWhere([["name",$name],["id","<>",$id]])->count();
      if($duplicate>0){
        $message="duplicate";
      }
      else{
        DB::table('roles')->where("id",$id)->update([
          "name"=>$name,
          "display_name"=>$display_name
        ]);
      }

    }

    return back()->with("message",$message);
  }

  public function deleteRole($id)
  {
    $message="delete_success";
    $count=DB::table("roles")->where("id",$id)->count();
    if($count==0){
      abort("404");
    }
    else{
      $users_linked=DB::table("user_roles")->where("role_id",$id)->count();
      if($users_linked>0){
        $messge="delete_error";
      }
      else{
        $count=DB::table("roles")->where("id",$id)->delete();
      }


    }

    return back()->with("message",$message);
  }


}
