<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use App\APP;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        $links = getUserLinks();
        $user_privilege = getUserPrivilege();
        $APP = new APP;
        $year = date('Y');
        $project_type = null;
        $mode = null;
        $municipality = null;
        $source = null;
        $type = null;
        $filter = null;
        $date_added = null;
        $month = null;
        $sort = [["column" => "project_plans.plan_id", "sorting" => "asc"]];
        $pow = null;

        $completed = $APP->getAPP($year, $project_type, 'all_completed', $mode, $municipality, $source, $type, $date_added, $month, $pow, $sort, $filter, true);
        $post_qual_to_verify = count($APP->getSpecificProcurementActivity('post_qual_to_verify', null));
        $ongoing = $APP->getAPP(null, $project_type, 'all_ongoing', $mode, $municipality, $source, $type, $date_added, $month, $pow, $sort, $filter, true);
        $for_rebid = count($APP->getSpecificProcurementActivity('projects_for_rebid', null));
        $for_review = count($APP->getSpecificProcurementActivity('projects_for_review', null));
        $unprocured_projects = $APP->getAPP(null, $project_type, 'unprocured_projects', $mode, $municipality, $source, $type, $date_added, $month, $pow, $sort, $filter, true);
        $reverted_projects = $APP->getAPP($year, $project_type, 'reverted', $mode, $municipality, $source, $type, $date_added, $month, $pow, $sort, $filter, true);
        $terminated_projects = $APP->getAPP($year, $project_type, 'terminated', $mode, $municipality, $source, $type, $date_added, $month, $pow, $sort, $filter, true);
        $pow_this_year = $APP->getAPP($year, $project_type, "new", $mode, $municipality, $source, $type, $date_added, $month, "true", $sort, $filter, true);
        $pending_rdf = $APP->getSpecificProcurementActivity('pending_rdf', $year);
        // $pow_last_year=$APP->getAPP($year-1, $project_type,"new", $mode, $municipality, $source, $type, $date_added, $month, "true" , $sort , $filter,true);
        // $pow_last_year=$APP->getAPP($year-1, $project_type,"new", $mode, $municipality, $source, $type, $date_added, $month, "true" , $sort , $filter,true);

        $insufficient_performance_bond = $insufficient_performance_bond = getInsufficientPerformanceBond($year, true);

        return view('dashboard', ["links" => $links, 'user_privilege' => $user_privilege, 'completed' => $completed, 'ongoing' => $ongoing, 'for_review' => $for_review, "for_rebid" => $for_rebid, "unprocured_projects" => $unprocured_projects, "reverted_projects" => $reverted_projects, "terminated_projects" => $terminated_projects, "pow_this_year" => $pow_this_year, "post_qual_to_verify" => $post_qual_to_verify, "insufficient_performance_bond" => $insufficient_performance_bond, "pending_rdf" => count($pending_rdf)]);
    }

    function getEvents()
    {
        // $procacts = DB::table('procacts')->whereNotNull('open_bid')->get();
        // $APP = new APP;
        // foreach ($procacts as $cluster) {
        //     $bidders = $APP->getBiddersData($cluster->procact_id, 'responsive,active');
        //     if (count($bidders) == 0 ) {
        //         DB::table('procacts')->where('procact_id', $cluster->procact_id)->update([
        //             "is_inactive" => true
        //         ]);
        //     }
        //     // else{
        //     //     DB::table('procacts')->where('procact_id', $cluster->procact_id)->update([
        //     //         "is_inactive" => false
        //     //     ]);  
        //     // }
        // }

        $APP = new APP;
        $advertisement = json_decode(json_encode($APP->getIncomingEvents('advertisement')));
        $pre_bid = json_decode(json_encode($APP->getIncomingEvents('pre_bid')));
        $submission = json_decode(json_encode($APP->getIncomingEvents('submission')));
        $bid_evaluation = json_decode(json_encode($APP->getIncomingEvents('bid_evaluation')));
        $post_qualification = json_decode(json_encode($APP->getIncomingEvents('post_qualification')));
        $contract_signing = json_decode(json_encode($APP->getIncomingEvents('contract_signing')));
        $notice_of_award = json_decode(json_encode($APP->getIncomingEvents('notice_of_award')));
        $notice_to_proceed = json_decode(json_encode($APP->getIncomingEvents('notice_to_proceed')));
        $events = array_merge($advertisement, $pre_bid, $submission, $bid_evaluation, $post_qualification, $contract_signing, $notice_of_award, $notice_to_proceed);
        return $events;
    }
}
