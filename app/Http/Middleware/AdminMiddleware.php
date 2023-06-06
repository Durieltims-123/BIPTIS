<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\AdminMiddleware as Middleware;
use Illuminate\Support\Facades\DB;
use Auth;
use Closure;
use App\{link,UserLink};

class AdminMiddleware
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
      $user_id=Auth::user()->id;
      $url = url()->current();
      // $ip=request()->server('SERVER_ADDR');
      $ip = $_SERVER['HTTP_HOST'];
      $route=str_replace("http://".$ip."/","",$url);

      $privilege=UserLink::select('link_privileges.privilege')
      ->where([['user_id',$user_id],['link_route',$route],['privilege','view']])
      ->join('link_privileges','user_links.link_privilege_id','link_privileges.id')
      ->join('links','link_privileges.link_id','links.id')
      ->first();

      if($privilege===null){
        return abort(403,"Sorry, You don't have access to this feature. Please Contact Your System Administrator for more details.");
      }
    }
    else{
      return redirect('login');
    }
    return $next($request);
  }
}
