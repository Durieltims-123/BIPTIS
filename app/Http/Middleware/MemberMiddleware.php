<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\MemberMiddleware as Middleware;
use Illuminate\Support\Facades\DB;
use Closure;
use Auth;

class MemberMiddleware
{
  /**
  * Handle an incoming request.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \Closure  $next
  * @return mixed
  */
  public function handle($request, Closure $next)
  {
    if(Auth::user()){
      $count=DB::table('user_roles')->where([['user_id',Auth::user()->id]])
      ->whereIn('role_id',[2,3])->count();
      if ($count==0)
      {
        return redirect()->back();
      }
    }
    else{
      return redirect('twg');
    }

    return $next($request);


  }
}
