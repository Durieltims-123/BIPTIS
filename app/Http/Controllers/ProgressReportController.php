<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProgressReportController extends Controller
{
    function generateProgressReport(){
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("admin.generate_progress_report", ['links' => $links, 'user_privilege' => $user_privilege]);
    }

}
