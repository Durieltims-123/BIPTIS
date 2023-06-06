<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;

class ProfileController extends Controller
{
  /**
  * Show the form for editing the profile.
  *
  * @return \Illuminate\View\View
  */
  public function edit()
  {
    $is_admin=false;
    $count=DB::table("user_roles")->where([["user_id",Auth::user()->id],["role_id",1]])->count();
    if ($count>0)
    {
      $is_admin=true;
    }
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("profile.edit",['links'=>$links,'user_privilege'=>$user_privilege,"is_admin"=>$is_admin]);
  }

  /**
  * Update the profile
  *
  * @param  \App\Http\Requests\ProfileRequest  $request
  * @return \Illuminate\Http\RedirectResponse
  */
  public function update(ProfileRequest $request)
  {
    auth()->user()->update($request->all());

    return back()->withStatus(__("Profile successfully updated."));
  }

  /**
  * Change the password
  *
  * @param  \App\Http\Requests\PasswordRequest  $request
  * @return \Illuminate\Http\RedirectResponse
  */
  public function password(PasswordRequest $request)
  {
    auth()->user()->update(["password" => Hash::make($request->get("password"))]);

    return back()->withPasswordStatus(__("Password successfully updated."));
  }
}
