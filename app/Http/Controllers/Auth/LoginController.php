<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
  * Where to redirect users after login.
  *
  * @var string
  */
  protected function redirectTo()
  {


    $bacsec=DB::table('user_roles')
    ->where([['users.email',auth()->user()->email]])
    ->whereIn('user_roles.role_id',[1,4,5])
    ->join('users','users.id','user_roles.user_id')->count();


    if ($bacsec>0) {
      session(['role' => '1']);
      return '/home';
    }
    else{
      $twg=DB::table('user_roles')->where([['users.email',auth()->user()->email]])
      ->whereIn('user_roles.role_id',[2,3])
      ->join('users','users.id','user_roles.user_id')->count();

      if($twg>0){
        session(['role' => '2']);
        return '/home';
      }
    }

  }




  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct()
  {
    $this->middleware('guest')->except('logout');
  }
}
