<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::get('/', 'UserController@Redirect')->name('');
Route::post('/get_events', 'HomeController@getEvents')->name('get_events');
Route::get('get_summary_report', 'SummaryReportController@generateSummaryReport')->name('get_summary_report');
Route::post('get_month_year_report','SummaryReportController@getReportbyMonthYear')->name('get_month_year_report');
Route::post('get_unprocured_project','SummaryReportController@getUnprocuredProject')->name('get_unprocured_project');
Route::post('get_reg_supp_project','SummaryReportController@getRegSuppProject')->name('get_reg_supp_project');
Route::post('get_mode_project','SummaryReportController@getModeProject')->name('get_mode_project');
Route::post('get_proj_status_mun','SummaryReportController@getStatusProjMun')->name('get_proj_status_mun');
Route::get('progress_report','ProgressReportController@generateProgressReport')->name('progress_report');
Route::post('/get_project_table','ProgressReportController@getTableData')->name('get_project_table');


Route::group(['middleware' => 'admin'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    Route::group(['prefix' => 'archive', 'as' => 'archive.'], function () {
        Route::get('regular_app', 'ArchiveController@getRegularAPP')->name('regular_app');
        Route::get('supplemental_app', 'ArchiveController@getSupplementalAPP')->name('supplemental_app');
        Route::get('certificate_of_postings', 'ArchiveController@getCertificateOfPostingArchive')->name('certificate_of_postings');
        Route::get('invitation_to_bids', 'ArchiveController@getITBArchive')->name('invitation_to_bids');
        Route::get('request_for_quotations', 'ArchiveController@getRFQArchive')->name('request_for_quotations');
        Route::get('notice_of_meetings', 'ArchiveController@getNoticeOfMeetingArchive')->name('notice_of_meetings');
        Route::get('meeting_attendance', 'ArchiveController@getMeetingAttendanceArchive')->name('meeting_attendance');
        Route::get('minutes', 'ArchiveController@getMinuteArchive')->name('minutes');
        Route::get('abstracts', 'ArchiveController@getAbstractArchive')->name('abstracts');
        Route::get('notice_of_ineligibility', 'ArchiveController@getNoticeOfIneligibility')->name('notice_of_ineligibility');
        Route::get('notice_of_disqualification', 'ArchiveController@getNoticeOfDisqualification')->name('notice_of_disqualification');
        Route::get('notice_of_post_qualification', 'ArchiveController@getNoticeofPostQualification')->name('notice_of_post_qualification');
        Route::get('notice_of_post_disqualification', 'ArchiveController@getNoticeofPostDisqualification')->name('notice_of_post_disqualification');
        Route::get('notice_to_losing_bidder', 'ArchiveController@getNoticeToLosingBidder')->name('notice_to_losing_bidder');
        Route::get('orders', 'ArchiveController@getOrderArchive')->name('orders');
        Route::get('resolution_recommending_awards', 'ArchiveController@getResolutionRecommendingAwards')->name('resolution_recommending_awards');
        Route::get('resolution_declaring_failure', 'ArchiveController@getResolutionDeclaringFailure')->name('resolution_declaring_failure');
        Route::get('resolution_granting_motion', 'ArchiveController@getResolutionGrantingMotion')->name('resolution_granting_motion');
        Route::get('resolution_recall_cancellation', 'ArchiveController@getResolutionRecallCancellation')->name('resolution_recall_cancelling');
        Route::get('other_resolutions', 'ArchiveController@getOtherResolutions')->name('other_resolutions');
        Route::get('resolution_denying_motion', 'ArchiveController@getResolutionDenyingMotion')->name('resolution_denying_motion');
        Route::get('resolution_recommending_recall_cancellation', 'ArchiveController@getResolutionRecommendingRecallCancellation')->name('resolution_recommending_recall_cancellation');
        Route::get('notice_of_awards', 'ArchiveController@getNoticeOfAwards')->name('notice_of_awards');
        Route::get('contracts', 'ArchiveController@getContracts')->name('contracts');
        Route::get('ntps', 'ArchiveController@getNTPs')->name('ntps');
        Route::get('termination_of_contract', 'ArchiveController@getTerminationArchive')->name('termination_of_contract');
        Route::get('transmittals', 'ArchiveController2@getTransmittals')->name('transmittals');
    });


    Route::get('/regular_app', 'APPController@getRegularAPP')->name('regular_app');
    Route::get('/supplemental_app', 'APPController@getSupplementalAPP')->name('supplemental_app');
    Route::get('/pow', 'APPController@getPowApp')->name('pow');
    Route::get('/svp_schedules', 'ScheduleController@getSVPSchedules')->name('svp_schedules');
    Route::get('/bidding_schedules', 'ScheduleController@getBiddingSchedules')->name('bidding_schedules');
    Route::get('/meetings', 'MeetingController@getMeetings')->name('meetings');
    Route::get('/supplemental_bid_bulletin', 'SupplementalBidBulletinController@getSupplementalBids')->name('supplemental_bid_bulletin');
    Route::get('/release_rfq', 'RFQController@getReleaseRFQ')->name('release_rfq');
    Route::get('/receive_rfq', 'RFQController@getRecieveRFQ')->name('receive_rfq');
    Route::get('/release_bid_docs', 'BiddocController@getReleaseBidDocs')->name('release_bid_docs');
    Route::get('/receive_bid_doc', 'BiddocController@getRecieveBidDocs')->name('receive_bid_doc');
    Route::get('/generate_checklist', 'ReportController@generateChecklist')->name('generate_checklist');
    Route::get('/twg_projects_with_bidders', 'TWGController@getProjectsWithBidders')->name('twg_projects_with_bidders');
    Route::get('/twg_post_qualification', 'TWGController@getPostQualificationActivity')->name('twg_post_qualification');

    Route::get('/post_qualification', 'ProcurementController@getPostQualificationActivity')->name('post_qualification');
    Route::get('/project_bidders_additional_documents', 'AdditionalDocumentController@getRequirementsChecklist')->name('project_bidders_additional_documents');
    Route::get('/release_notice_to_submit_documents', 'AdditionalDocumentController@releaseNoticeToSubmitDocuments')->name('release_notice_to_submit_documents');
    Route::get('/receive_documents', 'AdditionalDocumentController@receiveDocuments')->name('receive_documents');
    Route::get('/bac_request_for_extension', 'RequestForExtensionController@getRequestForExtensions')->name('bac_request_for_extension');
    Route::get('/orders', 'OrderController@getOrders')->name('orders');
    Route::get('/motion_for_reconsideration', 'MotionForReconsiderationController@getMotionForReconsiderations')->name('motion_for_reconsideration');
    Route::get('/resolution_recommending_awards', 'ResolutionController@getResolutionRecommendingAwards')->name('resolution_recommending_awards');
    Route::get('/resolution_recommending_recall_cancellation', 'ResolutionController@getResolutionRecommendingRecallCancellation')->name('resolution_recommending_recall_cancellation');
    Route::get('/resolution_declaring_failure', 'ResolutionController@getResolutionDeclaringFailure')->name('resolution_declaring_failure');
    Route::get('/resolution_granting_the_motion_for_reconsideration', 'ResolutionController@getResolutionGrantingTheMotionForReconsideration')->name('resolution_granting_the_motion_for_reconsideration');
    Route::get('/resolution_denying_the_motion_for_reconsideration', 'ResolutionController@getResolutionDenyingTheMotionForReconsideration')->name('resolution_denying_the_motion_for_reconsideration');
    Route::get('/notices', 'NoticeController@getNotices')->name('notices');

    Route::group(['prefix' => 'prepare', 'as' => 'prepare.'], function () {
        Route::get('notice_of_disqualification', 'NoticeController@prepareNoticeOfDisqualification')->name('notice_of_disqualification');
        Route::get('notice_of_ineligibility', 'NoticeController@prepareNoticeOfIneligibility')->name('notice_of_ineligibility');
        Route::get('notice_of_post_disqualification', 'NoticeController@prepareNoticeOfPostDisqualification')->name('notice_of_post_disqualification');
        Route::get('notice_of_post_qualification', 'NoticeController@prepareNoticeOfPostQualification')->name('notice_of_post_qualification');
        Route::get('notice_to_losing_bidder', 'NoticeController@prepareNoticeToLosingBidder')->name('notice_to_losing_bidder');
    });

    Route::get('/prepare_notice_of_award', 'NoticeController@prepareNoticeOfAward')->name('prepare_notice_of_award');
    Route::get('/performance_bonds', 'ContractController@getPerformanceBond')->name('performance_bonds');
    // chsp
    Route::get('/additional_performance_bond', 'PerformanceBondController@getAdditionalPerformanceBond')->name('additional_performance_bond');
    Route::get('/prepare_contracts', 'ContractController@prepareContract')->name('prepare_contracts');
    Route::get('/prepare_notice_to_proceed', 'NoticeController@prepareNoticeToProceed')->name('prepare_notice_to_proceed');
    Route::get('/termination_of_contract', 'TerminationController@getTerminationOfContract')->name('termination_of_contract');
    Route::get('/generate_certification_of_posting', 'ReportController@generateCertificationOfPosting')->name('generate_certification_of_posting');
    Route::get('/generate_abstract', 'ReportController@generateAbstract')->name('generate_abstract');
    Route::get('/generate_bid_evaluation', 'ReportController@generateBidEvaluation')->name('generate_bid_evaluation');
    Route::get('/generate_awarded_projects', 'ReportController@generateAwardedProjects')->name('generate_awarded_projects');
    Route::get('/generate_project_monitoring_report', 'ReportController2@generateProjectMonitoringReport')->name('generate_project_monitoring_report');
    Route::get('/get_with_pow', 'APPController@getWithPowYearly')->name('get_with_pow');
    Route::get('/get_without_pow', 'APPController@getWithoutPowYearly')->name('get_without_pow');
    Route::get('/generate_custom_bidders_report', 'ReportController@generateBidderCustomReport')->name('generate_custom_bidders_report');
    Route::get('/users', 'UserController@getUsers')->name('users');
    Route::get('/contractors', 'BidderController@getContractors')->name('contractors');
    Route::get('/source_of_fund', 'SettingsController@getSourceOfFund')->name('source_of_fund');
    Route::get('/project_types', 'SettingsController@getProjectTypes')->name('project_types');
    Route::get('/project_arrangement', 'ArrangementController@viewProjectArrangement')->name('project_arrangement');
    Route::get('/reactivate_rebid_projects', 'ProcurementController@getProjectsToReactivate')->name('reactivate_rebid_projects');
    Route::get('/unreceive_bidders_documents', 'SettingsController@unreceiveBiddersDocuments')->name('unreceive_bidders_documents');
    Route::get('/clear_termination_of_contract', 'SettingsController@clearTermination')->name('clear_termination_of_contract');
    Route::get('/clear_reversion', 'SettingsController@clearReversion')->name('clear_reversion');
    Route::get('/clear_post_qualification', 'SettingsController@clearPostQualification')->name('clear_post_qualification');
    Route::get('/clear_twg_post_qualification', 'SettingsController@clearPostTWGQualification')->name('clear_twg_post_qualification');
    Route::get('/members', 'BACController@getMembers')->name('members');
    Route::get('/observers', 'BACController@getObservers')->name('observers');
    Route::get('/bids_and_awards_committee', 'BACController@getBAC')->name('bids_and_awards_committee');
    Route::get('/meeting_rooms', 'MeetingController@getMeetingRooms')->name('meeting_rooms');
    Route::get('/holidays', 'HolidayController@getHolidays')->name('holidays');
    Route::get('/bidding_additional_documents', 'AdditionalDocumentController@getBiddingAdditionalDocuments')->name('bidding_additional_documents');
    Route::get('/svp_additional_documents', 'AdditionalDocumentController@getSVPAdditionalDocuments')->name('svp_additional_documents');
    Route::get('/widthraw_bidder_documents', 'SettingsController@withdrawBidderDocuments')->name('widthraw_bidder_documents');
    Route::get('/resolution_projects', 'ResolutionController@getResolutionProjects')->name('resolution_projects');

    // documenttracking/index
    // documenttracking/checklist
    // documenttracking/create
    // documenttracking/trackingsettings


    Route::get('/ongoing_projects', 'APPController@getOngoingProjects')->name('ongoing_projects');
    Route::get('/completed_projects', 'APPController@getCompletedProjects')->name('completed_projects');
    Route::get('/projects_for_rebid', 'ProcurementController@getProjectsForRebid')->name('projects_for_rebid');
    Route::get('/projects_for_review', 'ProcurementController@getProjectsForReview')->name('projects_for_review');
    Route::get('/unprocured_projects', 'APPController@getUnprocuredProjects')->name('unprocured_projects');
    Route::get('/reverted_projects', 'APPController@getRevertedProjects')->name('reverted_projects');
    Route::get('/terminated_projects', 'APPController@getTerminatedProjects')->name('terminated_projects');
    Route::get('/insufficient_performance_bond', 'PerformanceBondController@getInsufficientPerformanceBond')->name('insufficient_performance_bond');
    Route::get('/post_qualification_to_verify', 'ProcurementController@getPostQualToVerify')->name('post_qualification_to_verify');

    Route::get('/chsp', 'CHSPController@getCHSP')->name('chsp');
    Route::get('/generate_awarded_projects_for_transmittal', 'ReportController2@generateAwardedProjectsForTransmittal')->name('generate_awarded_projects_for_transmittal');
    Route::get('/reschedule', 'SettingsController@reschedule')->name('reschedule');
    Route::get('/lce_evaluation', 'LCEEvaluationController@index')->name('lce_evaluation');
    Route::get('/summary_of_bidding_documents', 'ReportController2@getSummaryOfBiddingDocuments')->name('summary_of_bidding_documents');
});

Route::group(['middleware' => 'auth'], function () {

    // postings
    Route::group(['prefix' => 'posting', 'as' => 'posting.'], function () {

        // Notice of Awards
        Route::get('/notice_of_award', 'PostingController@getPostingNoticeOfAwards')->name('notice_of_award');
        Route::post('/filter_notice_of_awards', 'PostingController@filterPostingNoticeOfAwards')->name('filter_notice_of_awards');
        Route::post('/submit_notice_of_award', 'PostingController@submitPostingNoticeOfAward')->name('submit_notice_of_award');
        Route::post('/download_noa_zip', 'PostingController@downloadNOAZip')->name('download_noa_zip');

        // Contracts
        Route::get('/contract', 'PostingController@getPostingContracts')->name('contract');
        Route::post('/filter_contracts', 'PostingController@filterPostingContracts')->name('filter_contracts');
        Route::post('/submit_contract', 'PostingController@submitPostingContract')->name('submit_contract');
        Route::post('/download_contract_zip', 'PostingController@downloadContractZip')->name('download_contract_zip');
        Route::get('/download_contract_zip/{posting_date}/{id}', 'PostingController@downloadContractZip');


        // NoticeToProceeds
        Route::get('/notice_to_proceed', 'PostingController@getPostingNoticeToProceeds')->name('notice_to_proceed');
        Route::post('/filter_notice_to_proceeds', 'PostingController@filterPostingNoticeToProceeds')->name('filter_notice_to_proceeds');
        Route::post('/submit_notice_to_proceed', 'PostingController@submitPostingNoticeToProceed')->name('submit_notice_to_proceed');
        Route::post('/download_notice_to_proceed_zip', 'PostingController@downloadNoticeToProceedZip')->name('download_notice_to_proceed_zip');
        Route::get('/download_notice_to_proceed_zip/{posting_date}/{id}', 'PostingController@downloadNoticeToProceedZip');


        // ITB
        Route::get('/itb', 'PostingController@getPostingITBs')->name('itb');
        Route::post('/filter_itbs', 'PostingController@filterPostingITBs')->name('filter_itbs');
        Route::post('/submit_itb', 'PostingController@submitPostingITB')->name('submit_itb');
        Route::post('/download_itb_zip', 'PostingController@downloadITBZip')->name('download_itb_zip');
        Route::get('/download_itb_zip/{posting_date}/{id}', 'PostingController@downloadITBZip');


        // RFQ
        Route::get('/rfq', 'PostingController@getPostingRFQs')->name('rfq');
        Route::post('/filter_rfqs', 'PostingController@filterPostingRFQs')->name('filter_rfqs');
        Route::post('/submit_rfq', 'PostingController@submitPostingRFQ')->name('submit_rfq');
        Route::post('/download_rfq_zip', 'PostingController@downloadRFQZip')->name('download_rfq_zip');
        Route::get('/download_rfq_zip/{posting_date}/{id}', 'PostingController@downloadRFQZip');
    });

    // withraw bid
    Route::post('/submit_widthraw_bidder_documents', 'SettingsController@submitWithdrawBidderDocuments')->name('submit_widthraw_bidder_documents');

    //download
    Route::get('/download_ongoing_post_qual/{year}', 'TWGController@downloadOngoingPostQual');

    // Summary of Bidding
    Route::post('/submit_summary_of_bidding_documents', 'ReportController2@submitSummaryOfBiddingDocuments')->name('submit_summary_of_bidding_documents');
    Route::get('/download_summary_of_bidding_documents/{date_start}/{date_end}/{bidder_status}', 'ReportController2@downloadSummaryOfBiddingDocuments');


    // LCE Evaluation
    Route::post('/filter_lce_evaluation', 'LCEEvaluationController@index')->name('filter_lce_evaluation');
    Route::post('/submit_lce_evaluation', 'LCEEvaluationController@submitLCEEvaluation')->name('submit_lce_evaluation');
    Route::post('/get_lce_evaluation_attachments', 'LCEEvaluationController@getLCEEvaluationAttachments')->name('get_lce_evaluation_attachments');
    Route::get('/lce_evaluation_attachment/{id}', ['uses' => 'LCEEvaluationController@viewLCEEvaluationAttachment', 'as' => 'id']);
    Route::get('/view_lce_evaluation_attachments/{id}', ['uses' => 'LCEEvaluationController@viewLCEEvaluationAttachments', 'as' => 'id']);
    Route::post('/delete_lce_evaluation_attachment', 'LCEEvaluationController@deleteLCEEvaluationAttachment')->name('delete_lce_evaluation_attachment');
    Route::post('/delete_lce_evaluation', 'LCEEvaluationController@deleteLCEEvaluation')->name('delete_lce_evaluation');


    Route::post('/filter_resolution_projects', 'ResolutionController@getResolutionProjects')->name('filter_resolution_projects');

    // PLan
    Route::post('/filter_plan', 'APPController@filterApp')->name('filter_app');
    Route::resource('user', 'UserController', ['except' => ['show']]);
    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
    Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
    Route::get('/view_project/{id}', ['uses' => 'APPController@viewProject', 'as' => 'id']);
    Route::get('/get_ending_post_qual', 'ProcurementController@getEndingPostquals')->name('get_ending_post_qual');
    Route::post('/submit_request_extension', 'ProcurementController@submitRequestExtension')->name('submit_request_extension');

    // Bidders
    Route::get('/project_bidders/{id}', ['uses' => 'BidderController@getProjectBidders', 'as' => 'id']);
    Route::get('/post_qual_project_bidders/{id}', ['uses' => 'BidderController@getPostQualProjectBidders', 'as' => 'id']);
    Route::get('/responsive_bidder/{id}', ['uses' => 'BidderController@setResponsiveBiddder', 'as' => 'id']);
    Route::post('/disqualify_bidder', 'BidderController@disqualifyBidder')->name('disqualify_bidder');
    Route::post('/reactivate_bidder', 'BidderController@reactivateBidder')->name('reactivate_bidder');
    Route::post('/non_responsive_bidder', 'BidderController@setNonResponsiveBiddder')->name('non_responsive_bidder');
    Route::post('/clear_post_qualification_evaluation', 'BidderController@clearPostQualificationEvaluation')->name('clear_post_qualification_evaluation');
    Route::post('/edit_proposed_bid', 'BidderController@editProposedBid')->name('edit_proposed_bid');
    Route::post('/edit_detailed_bid', 'BidderController@editDetailedBid')->name('edit_detailed_bid');

    // bidders additional Docs
    Route::post('/filter_project_bidder_additional_docs', 'AdditionalDocumentController@filterProjectBiddersRequirements')->name('filter_project_bidder_additional_docs');
    Route::post('/filter_release_project_bidder_additional_docs', 'AdditionalDocumentController@filterReleaseProjectBiddersRequirements')->name('filter_release_project_bidder_additional_docs');
    Route::post('/filter_receive_project_bidder_additional_docs', 'AdditionalDocumentController@filterReceiveProjectBiddersRequirements')->name('filter_receive_project_bidder_additional_docs');
    Route::post('/submit_bidders_additional_documents', 'AdditionalDocumentController@submitProjectBiddersAdditionalDocuments')->name('submit_bidders_additional_documents');
    Route::post('/submit_release_notice_to_submit_documents', 'AdditionalDocumentController@submitReleaseNoticeToSubmitDocuments')->name('submit_release_notice_to_submit_documents');
    Route::post('/submit_receive_documents', 'AdditionalDocumentController@submitReceiveDocuments')->name('submit_receive_documents');

    // bid evaluation form
    Route::get('/submit_generate_bid_evaluation/{date_opened}', ['uses' => 'ReportController@submitGenerateBidEvaluation', 'as' => 'date_opened']);
    Route::post('/submit_generate_bid_evaluation_table', 'ReportController@submitGenerateBidEvaluationTable')->name('submit_generate_bid_evaluation_table');

    // process
    Route::post('/extend_process', 'ProcurementController@extendProcess')->name('extend_process');

    // notice_to_submit_post_qual_docs
    Route::post('/submit_ntspqd', 'NoticeController@submitNoticeToSubmitPostQualDocs')->name('submit_ntspqd');
    Route::get('/generate_ntspqd/{id}', ['uses' => 'NoticeController@generateNTSPQD', 'as' => 'id']);

    // Document  Tracking
    Route::resource('documenttracking', 'DocumentTrackingController');
    Route::get('/getdocumenttypes', 'DocumentTrackingController@getdocumenttypes')->name('getdocumenttypes');
    Route::get('/getprojectnames', 'DocumentTrackingController@getprojectnames')->name('getprojectnames');
    Route::get('/getprojectplans', 'DocumentTrackingController@getprojectplans')->name('getprojectplans');
    Route::get('/getOffice', 'DocumentTrackingController@getOffice')->name('getOffice');
    Route::get('/getcontractors', 'DocumentTrackingController@getcontractors')->name('getcontractors');
    Route::get('/getdocuments', 'DocumentTrackingController@getdocuments')->name('getdocuments');
    Route::get('/getdocumentsroutinghistory', 'DocumentTrackingController@getdocumentsroutinghistory')->name('getdocumentsroutinghistory');
    Route::get('/documenttracking/projectdocuments/{id}', 'DocumentTrackingController@getprojectdocuments')->name('documenttracking.showprojectdocuments');
    Route::get('/documenttracking/showdocuments/{id}', 'DocumentTrackingController@showdocuments')->name('documenttracking.showdocuments');
    Route::get('/managedocuments', 'DocumentTrackingController@managedocuments')->name('documenttracking.managedocuments');
    Route::get('/trackingsettings', 'DocumentTrackingController@trackingsettings')->name('documenttracking.trackingsettings');
    Route::get('/savesettings', 'DocumentTrackingController@savesettings')->name('documenttracking.savesettings');
    Route::get('/saveprocessdoccuments', 'DocumentTrackingController@saveprocessdoccuments')->name('documenttracking.saveprocessdoccuments');
    Route::get('/getdocumentslist', 'DocumentTrackingController@getdocumentslist')->name('documenttracking.getdocumentslist');
    Route::get('/managedocumenttypes', 'DocumentTrackingController@managedocumenttypes')->name('documenttracking.managedocumenttypes');
    Route::get('/getprocesseslist', 'DocumentTrackingController@getprocesseslist')->name('documenttracking.getprocesseslist');
    Route::get('/getprocurementprocesses', 'DocumentTrackingController@getprocurementprocesses')->name('documenttracking.getprocurementprocesses');
    Route::post('/storedocuments', 'DocumentTrackingController@storedocuments')->name('documenttracking.storedocuments');
    Route::get('/getnotifications', 'DocumentTrackingController@getnotifications')->name('documenttracking.getnotifications');
    Route::get('/removeprocessdocuments', 'DocumentTrackingController@removeprocessdocuments')->name('documenttracking.removeprocessdocuments');
    Route::get('/checklist', 'DocumentTrackingController@checklist')->name('documenttracking.checklist');
    Route::get('/getdocumentchecklist', 'DocumentTrackingController@getdocumentchecklist')->name('documenttracking.getdocumentchecklist');
    Route::get('/generatedocumentchecklist', 'DocumentTrackingController@generatedocumentchecklist')->name('documenttracking.generatedocumentchecklist');
    Route::get('/getallcontractors', 'DocumentTrackingController@getallcontractors')->name('documenttracking.getallcontractors');
    // Route::get('files/{filename}', function ($filename) {
    //     $path = storage_path('app/files/' . $filename);
    //     if (!File::exists($path)) {
    //         abort(404);
    //     }
    //     $file = File::get($path);
    //     $type = File::mimeType($path);
    //     $response = Response::make($file, 200);
    //     $response->header("Content-Type", $type);
    //     return $response;
    // });

    // Request for Extension
    Route::post('/get_all_post_qual_without_extension', 'RequestForExtensionController@getAllPostQual')->name('get_all_post_qual_without_extension');
    Route::post('/get_ongoing_post_qual', 'RequestForExtensionController@getOngoingPostual')->name('get_ongoing_post_qual');
    Route::post('/get_selected_project_bidders', 'RequestForExtensionController@getSelectedBids')->name('get_selected_project_bidders');
    Route::post('/filter_request_for_extention', 'RequestForExtensionController@getRequestForExtensions')->name('filter_request_for_extention');
    Route::post('/twg_clear_post_qualification_evaluation', 'BidderController@TWGClearPostQualificationEvaluation')->name('twg_clear_post_qualification_evaluation');

    // links and sublinks
    Route::get('get_links', 'LinkController@getLinks')->name('get_links');

    // APP
    Route::get('/add_regular_app', 'APPController@addRegularAPP')->name('add_regular_app');
    Route::get('/add_supplemental_app', 'APPController@addSupplementalAPP')->name('add_supplemental_app');
    Route::get('/submit_plan', 'APPController@submitPlan')->name('submit_plan');
    // Route::post('/submit_adjust_sapp', 'APPController@submitAdjustSapp')->name('submit_adjust_sapp');
    Route::post('/submit_adjust_sapp', 'APPController@submitAdjustSapp')->name('submit_adjust_sapp');
    Route::post('/get_sector', 'APPController@getSector')->name('get_sector');
    Route::post('/get_barangays', 'APPController@getBarangays')->name('get_barangays');
    Route::get('/edit_app/{id}', ['uses' => 'APPController@editApp', 'as' => 'id']);
    Route::get('/edit_sapp/{id}', ['uses' => 'APPController@editSapp', 'as' => 'id']);
    Route::get('/add_sapp/{id}', ['uses' => 'APPController@additionalSupplementalApp', 'as' => 'id']);
    Route::get('/adjust_sapp/{id}', ['uses' => 'APPController@adjustSupplementalApp', 'as' => 'id']);
    Route::get('/delete_app/{id}', ['uses' => 'APPController@deleteApp', 'as' => 'id']);
    Route::get('/update_plan', 'APPController@updatePlan')->name('update_plan');
    Route::post('/autocomplete_project_titles', 'APPController@autoCompletePlanTitles')->name('autocomplete_project_titles');
    Route::post('/autocomplete_unreceive_project_titles', 'APPController@autoCompleteUnreceivePlanTitles')->name('autocomplete_unreceive_project_titles');
    Route::post('/autocomplete_project', 'APPController@autoCompletePlanTitlesForFile')->name('autocomplete_project');
    Route::post('/autocomplete_project_with_contracts', 'PerformanceBondController@autoCompleteProjectWithContracts')->name('autocomplete_project_with_contracts');
    Route::post('/filter_reverted', 'APPController@getRevertedProjects')->name('filter_reverted');
    Route::post('/filter_terminated', 'APPController@getTerminatedProjects')->name('filter_terminated');
    Route::get('/for_review_projects', 'APPController@getForReviewProjects')->name('for_review_projects');
    Route::get('/for_rebid_projects', 'APPController@getForRebidProjects')->name('for_rebid_projects');
    Route::get('/create_sapp/{id}', ['uses' => 'APPController@createAsSAPP', 'as' => 'id']);
    Route::post('/download_app', 'APPController@downloadApp')->name('download_app');
    Route::get('/change_project_type/{id}', ['uses' => 'APPController@changeProjectType', 'as' => 'id']);

    // POW
    Route::get('/filter_pow', 'APPController@getPowApp')->name('filter_pow');
    Route::post('/submit_pow', 'APPController@submitPow')->name('submit_pow');
    Route::post('/submit_pow_remarks', 'APPController@submitPowRemarks')->name('submit_pow_remarks');
    Route::get('/delete_pow/{id}', ['uses' => 'APPController@deletePow', 'as' => 'id']);

    Route::post('/filter_without_pow', 'APPController@getWithoutPowYearly')->name('filter_without_pow');
    Route::post('/filter_with_pow', 'APPController@getWithPowYearly')->name('filter_without_pow');

    // Scheduling
    Route::post('/submit_schedule', 'ScheduleController@submitSchedule')->name('submit_schedule');
    Route::get('/delete_schedule/{id}', ['uses' => 'ScheduleController@deleteSchedule', 'as' => 'id']);
    Route::post('/filter_schedule', 'ScheduleController@filterSchedule')->name('filter_schedule');
    Route::post('/submit_cluster', 'ScheduleController@submitCluster')->name('submit_cluster');
    Route::post('/cancel_schedule', 'ScheduleController@cancelSchedule')->name('cancel_schedule');
    Route::post('/defer_schedule', 'ScheduleController@deferSchedule')->name('defer_schedule');
    // Project_arrangement
    Route::get('/test', 'ArrangementController@test')->name('test');
    Route::post('/get_date_projects', 'ArrangementController@getDateProjects')->name('get_date_projects');
    Route::post('/submit_arrangement', 'ArrangementController@submitArrangement')->name('submit_arrangement');

    //Contractors
    Route::post('/filter_contractors', 'BidderController@filterContractors')->name('filter_contractors');
    Route::post('/submit_contractor', 'BidderController@submitContractor')->name('submit_contractor');
    Route::get('/delete_contractor/{id}', ['uses' => 'BidderController@deleteContractor', 'as' => 'id']);
    Route::post('/autocomplete_contractors', 'BidderController@autoCompleteContractors')->name('autocomplete_contractors');
    Route::post('/autocomplete_unreceive_contractors', 'BidderController@autoCompleteUnreceiveContractors')->name('autocomplete_unreceive_contractors');
    Route::post('/autocomplete_bidders', 'BidderController@autoCompleteBidders')->name('autocomplete_bidders');
    Route::post('/autocomplete_similar_bidders', 'BidderController@autoCompleteSimilarBidders')->name('autocomplete_similar_bidders');
    Route::post('/autocomplete_project_engineers', 'ProjectEngineerController@autoCompleteProjectEngineer')->name('autocomplete_project_engineers');

    // RFQ
    Route::post('/submit_release_rfq', 'RFQController@submitReleaseRFQ')->name('submit_release_rfq');
    Route::get('/delete_rfq/{id}', ['uses' => 'RFQController@deleteRFQ', 'as' => 'id']);
    Route::post('/filter_rfq', 'RFQController@filterRFQ')->name('filter_rfq');
    Route::post('/submit_receive_rfq', 'RFQController@submitReceiveRFQ')->name('submit_receive_rfq');

    // Bid Docs
    Route::post('/submit_release_bid_doc', 'BiddocController@submitReleaseBidDoc')->name('submit_release_bid_doc');
    Route::get('/delete_bid_doc/{id}', ['uses' => 'BiddocController@deleteBidDoc', 'as' => 'id']);
    Route::post('/filter_bid_doc', 'BiddocController@filterBidDoc')->name('filter_bid_doc');
    Route::post('/submit_receive_bid_doc', 'BiddocController@submitReceiveBidDoc')->name('submit_receive_bid_doc');
    Route::get('/generate_order_of_payment/{id}', ['uses' => 'BiddocController@generateOrderOfPayment', 'as' => 'id']);

    // Meeting
    Route::post('/submit_meeting_room', 'MeetingController@submitMeetingRoom')->name('submit_meeting_room');
    Route::get('/delete_meeting_room/{meeting_room_id}',  ['uses' => 'MeetingController@deleteMeetingRoom', 'as' => 'meeting_room_id']);
    Route::post('/submit_meeting', 'MeetingController@submitMeeting')->name('submit_meeting');
    Route::get('/delete_meeting/{meeting_id}',  ['uses' => 'MeetingController@deleteMeeting', 'as' => 'meeting_id']);
    Route::get('/download_notice_of_meeting/{meeting_id}',  ['uses' => 'MeetingController@downloadNoticeOfMeeting', 'as' => 'meeting_id']);
    Route::get('/release_notice_of_meeting/{id}', ['uses' => 'MeetingController@releaseNoticeOfMeeting', 'as' => 'id']);
    Route::post('/submit_release_meeting', 'MeetingController@submitReleaseMeeting')->name('submit_release_meeting');
    Route::post('/filter_meetings', 'MeetingController@filterMeetings')->name('filter_meetings');
    Route::get('/delete_release_nom/{id}', 'MeetingController@deleteReleaseNOM');

    // Holidays
    Route::post('/submit_holiday', 'HolidayController@submitHoliday')->name('submit_holiday');
    Route::get('/delete_holiday/{id}', ['uses' => 'HolidayController@deleteHoliday', 'as' => 'id']);
    Route::post('/calculate_due_date', 'HolidayController@calculateDueDate')->name('calculate_due_date');
    Route::post('/filter_holidays', 'HolidayController@getHolidays')->name('filter_holidays');

    // Request for extension
    Route::get('/view_request_for_extension/{id}', ['uses' => 'RequestForExtensionController@viewRequestForExtensionForm', 'as' => 'id']);

    // Orders
    Route::post('/filter_orders', 'OrderController@getOrders')->name('filter_orders');
    Route::post('/submit_order', 'OrderController@submitOrder')->name('submit_order');
    Route::get('/generate_order/{id}', ['uses' => 'OrderController@generateOrderRequest', 'as' => 'id']);

    // Unreceive RFQ and Bids Docs
    Route::post('/submit_unreceive_bidders_documents', 'SettingsController@submitUnreceiveBiddersDocuments')->name('submit_unreceive_bidders_documents');

    //Procurement
    Route::get('/procurement_activity/{id}', ['uses' => 'ProcurementController@getProcurementActivity', 'as' => 'id']);
    Route::post('/submit_preprocurement', 'ProcurementController@submitPreprocurement')->name('submit_preprocurement');
    Route::post('/submit_advertisement_posting', 'ProcurementController@submitAdvertisementPosting')->name('submit_advertisement_posting');
    Route::post('/submit_prebid', 'ProcurementController@submitPrebid')->name('submit_prebid');
    Route::post('/submit_submission_opening_of_bid', 'ProcurementController@submitSubmissionOpeningOfBid')->name('submit_submission_opening_of_bid');
    Route::post('/submit_bid_evaluation', 'ProcurementController@submitBidEvaluation')->name('submit_bid_evaluation');
    Route::post('/submit_post_qualification', 'ProcurementController@submitPostQualification')->name('submit_post_qualification');
    Route::post('/submit_award_notice', 'ProcurementController@submitAwardNotice')->name('submit_award_notice');
    Route::post('/submit_contract_preparation_and_signing', 'ProcurementController@submitContractPreparationAndSigning')->name('submit_contract_preparation_and_signing');
    Route::post('/submit_authority_approval', 'ProcurementController@submitAuthorityApproval')->name('submit_authority_approval');
    Route::post('/submit_notice_to_proceed', 'ProcurementController@submitNoticeToProceed')->name('submit_notice_to_proceed');
    Route::post('/submit_rebid', 'ProcurementController@submitRebid')->name('submit_rebid');
    Route::post('/submit_review', 'ProcurementController@submitReview')->name('submit_review');
    Route::post('/submit_revert', 'ProcurementController@submitRevert')->name('submit_revert');
    Route::get('/procurement_activity/{id}', ['uses' => 'ProcurementController@getProcurementActivity', 'as' => 'id']);


    // Grouped Procurement
    Route::get('/pre_procurement', 'ProcurementController@getPreprocurementActivity')->name('pre_procurement');
    Route::get('/advertisement_posting', 'ProcurementController@getAdvertisementPostingActivity')->name('advertisement_posting');
    Route::get('/pre_bid', 'ProcurementController@getPreBidActivity')->name('pre_bid');
    Route::get('/submission_opening', 'ProcurementController@getSubmissionOpeningActivity')->name('submission_opening');
    Route::get('/bid_evaluation', 'ProcurementController@getBidEvaluationActivity')->name('bid_evaluation');
    Route::post('/filter_post_qual', 'ProcurementController@getPostQualificationActivity')->name('filter_post_qual');
    Route::get('/notice_of_award', 'ProcurementController@getNoticeOfAwardActivity')->name('notice_of_award');
    Route::get('/contract_preparation_signing', 'ProcurementController@getContractPreparationSigningActivity')->name('contract_preparation_signing');
    Route::get('/approval_by_higher_authority', 'ProcurementController@getApprovalByAuthorityActivity')->name('approval_by_higher_authority');
    Route::get('/notice_to_proceed', 'ProcurementController@getNoticeToProceedActivity')->name('notice_to_proceed');
    Route::get('/post_qualification_to_verify', 'ProcurementController@getPostQualToVerify')->name('post_qualification_to_verify');
    Route::get('/pending_resolution_declaring_failure', 'ResolutionController@pendingRDF')->name('pending_resolution_declaring_failure');

    // rebid Projects
    Route::post('/submit_reactivate', 'ProcurementController@submitReactivateProject')->name('submit_reactivate');


    // Importing
    Route::get('/import_app', 'ImportController@importAPP')->name('import_app');
    Route::post('/submit_import_app', 'ImportController@submitImportAPP')->name('submit_import_app');
    Route::get('/import_contractors', 'ImportController@importContractors')->name('import_conrtactors');
    Route::post('/submit_import_contractors', 'ImportController@submitImportContractors')->name('submit_import_contractors');
    Route::get('/fix_app', 'ImportController@fixAPP')->name('fix_app');
    Route::post('/submit_fix_app', 'ImportController@submitFixAPP')->name('submit_fix_app');
    Route::get('/check_app', 'ImportController@checkApp')->name('check_app');
    Route::post('/submit_check_app', 'ImportController@checkStatus')->name('submit_check_app');
    Route::get('/import_links', 'ImportController@importLinks')->name('import_links');
    Route::post('/submit_import_links', 'ImportController@submitImportLinks')->name('submit_import_links');

    // Termination
    Route::post('/filter_termination_of_contract', 'TerminationController@getTerminationOfContract')->name('filter_termination_of_contract');
    Route::post('/submit_termination', 'TerminationController@submitTerminationOfContract')->name('submit_termination');

    // Settings
    // fund_categories
    Route::get('/fund_category', 'SettingsController@getFundCategory')->name('fund_category');
    Route::post('/submit_fund_category', 'SettingsController@submitFundCategory')->name('submit_fund_category');
    Route::get('/delete_fund_category/{id}', ['uses' => 'SettingsController@deleteFundCategory', 'as' => 'id']);

    //source of funds
    Route::post('/submit_source_of_fund', 'SettingsController@submitSourceOfFund')->name('submit_source_of_fund');
    Route::get('/delete_source/{id}', ['uses' => 'SettingsController@deleteSource', 'as' => 'id']);

    // autoComplete
    Route::post('/autocomplete_awarded_project', 'SettingsController@autoCompleteAwardedProjects')->name('autocomplete_awarded_project');
    Route::post('/autocomplete_terminated_project', 'SettingsController@autoCompleteTerminatedProjects')->name('autocomplete_terminated_project');
    Route::post('/autocomplete_reverted_project', 'SettingsController@autoCompleteRevertedProjects')->name('autocomplete_reverted_project');
    Route::post('/autocomplete_post_qual_projects', 'SettingsController@autoCompletePostQualProjects')->name('autocomplete_post_qual_projects');
    Route::post('/autocomplete_post_qualified_contractors', 'SettingsController@autoCompletePostQualifiedContractors')->name('autocomplete_post_qualified_contractors');

    // Project Type
    Route::post('/submit_project_type', 'SettingsController@submitProjectType')->name('submit_project_type');
    Route::get('/delete_project_type/{id}', ['uses' => 'SettingsController@deleteProjectType', 'as' => 'id']);

    // Sectors
    Route::get('/sectors', 'SettingsController@getSectors')->name('sectors');
    Route::post('/submit_sector', 'SettingsController@submitSector')->name('submit_sector');
    Route::get('/delete_sector/{id}', ['uses' => 'SettingsController@deleteSector', 'as' => 'id']);

    // Roles or Office
    Route::get('/offices', 'RoleController@getRoles')->name('offices');
    Route::post('/submit_role', 'RoleController@submitRole')->name('submit_role');
    Route::get('/delete_role/{id}', ['uses' => 'RoleController@deleteRole', 'as' => 'id']);

    // Users
    Route::post('/submit_user', 'UserController@submitUser')->name('submit_user');
    Route::get('/delete_user/{id}', ['uses' => 'UserController@deleteUser', 'as' => 'id']);

    // Clear Termination
    Route::post('/submit_clear_termination_of_contract', 'SettingsController@submitClearTermination')->name('submit_clear_termination_of_contract');

    // Reschedule
    Route::post('/submit_reschedule', 'SettingsController@submitReschedule')->name('submit_reschedule');

    // Clear Reversion
    Route::post('/submit_clear_reversion', 'SettingsController@submitClearReversion')->name('submit_clear_reversion');

    // reports
    Route::get('/generate_bid_evaluation_table', 'ReportController@generateBidEvaluationTable')->name('generate_bid_evaluation_table');
    Route::post('/submit_generate_checklist', 'ReportController@submitGenerateChecklist')->name('submit_generate_checklist');
    Route::get('/download_checklist/{opening_date}', ['uses' => 'ReportController@downloadChecklist', 'as' => 'opening_date']);
    Route::post('/submit_generate_awarded', 'ReportController@submitGenerateAwarded')->name('submit_generate_awarded');
    Route::get('/download_awarded/{date_start}/{date_end}', 'ReportController@downloadAwarded');
    Route::get('/download_awarded_for_transmittal/{date_start}/{date_end}', 'ReportController2@downloadAwardedForTransmittal');
    Route::post('/submit_generate_custom_bidders_report', 'ReportController@SubmitGenerateBidderCustomReport')->name('submit_generate_custom_bidders_report');
    Route::get('/download_custom_bidders_report/{date_start}/{date_end}/{bidder_status}/{procurement_mode}', 'ReportController@downloadGenerateBidderCustomReport');
    Route::post('/submit_generate_certification_of_posting', 'ReportController@SubmitGenerateCertificationOfPosting')->name('submit_generate_certification_of_posting');
    Route::post('/download_certification_of_posting', 'ReportController@downloadCertificationOfPosting')->name('download_certification_of_posting');
    Route::get('/download_pmr/{date_start}/{date_end}', 'ReportController2@downloadPMR')->name('download_pmr');
    Route::post('/submit_generate_awarded_for_transmittal', 'ReportController2@submitGenerateAwardedForTransmittal')->name('submit_generate_awarded_for_transmittal');
    Route::get('/download_certification/{id}', 'ReportController2@downloadCertification');


    // Notices
    Route::post('/submit_notice', 'NoticeController@submitNotice')->name('submit_notice');
    Route::get('/generate_file', 'NoticeController@generateFile')->name('generate_file');
    Route::get('/notice_bidders/{id}', ['uses' => 'NoticeController@getNoticeBidders', 'as' => 'id']);
    Route::get('/generate_notice/{id}', ['uses' => 'NoticeController@generateNotice', 'as' => 'id']);
    Route::post('/filter_prepare_notice_of_awards', 'NoticeController@filterNoticeOfAwards')->name('filter_prepare_notice_of_awards');
    Route::get('/generate_noa/{id}', ['uses' => 'NoticeController@generateNOA', 'as' => 'id']);
    Route::post('/filter_prepare_notice_to_proceed', 'NoticeController@prepareNoticeToProceed')->name('filter_prepare_notice_to_proceed');
    Route::get('/generate_ntp/{id}', ['uses' => 'NoticeController@generateNTP', 'as' => 'id']);
    Route::get('/ntspqd', 'NoticeController@getAdminNTSPQD')->name('ntspqd');
    Route::get('/generate_additional_docs/{id}', ['uses' => 'NoticeController@generateProjectBiddersAdditionalRequiredDocuments', 'as' => 'id']);
    Route::post('/filter_bidders_per_project', 'NoticeController@filterNoticePerProject')->name('filter_bidders_per_project');


    // Additional Documents
    Route::post('/submit_additional_documents', 'AdditionalDocumentController@submitAdditionalDocuments')->name('submit_additional_documents');

    // resolutions
    Route::get('/add_resolution_recommending_award', 'ResolutionController@addResolutionRecommendingAward')->name('add_resolution_recommending_award');
    Route::get('/add_resolution_recommending_recall_cancellation', 'ResolutionController@addResolutionRecommendingRecallCancellation')->name('add_resolution_recommending_recall_cancellation');
    Route::get('/edit_resolution_recommending_recall_cancellation/{id}', 'ResolutionController@editResolutionRecommendingRecallCancellation');
    Route::get('/add_resolution_declaring_failure', 'ResolutionController@addResolutionDeclaringFailure')->name('add_resolution_declaring_failure');
    Route::post('/submit_resolution', 'ResolutionController@submitResolution')->name('submit_resolution');
    Route::get('/generate_cca/{id}', ['uses' => 'ResolutionController@generateCCA', 'as' => 'id']);
    Route::get('/generate_rdf/{id}', ['uses' => 'ResolutionController@generateRDF', 'as' => 'id']);
    Route::get('/generate_rrrc/{id}', ['uses' => 'ResolutionController@generateRRRC', 'as' => 'id']);
    Route::get('/edit_resolution_recommending_award/{id}', 'ResolutionController@editResolutionRecommendingAward');
    Route::get('/edit_resolution_declaring_failure/{id}', 'ResolutionController@editResolutionDeclaringFailure');
    Route::get('/edit_resolution_denying_the_motion_for_reconsideration/{id}', 'ResolutionController@editResolutionDenyingTheMotionFOrReconsideration');
    Route::get('/add_resolution_denying_the_motion_for_reconsideration', 'ResolutionController@addResolutionDenyingTheMotionForReconsideration')->name('add_resolution_denying_the_motion_for_reconsideration');
    Route::get('/add_resolution_granting_the_motion_for_reconsideration', 'ResolutionController@addResolutionGrantingTheMotionForReconsideration')->name('add_resolution_granting_the_motion_for_reconsideration');
    Route::post('/submit_mr_resolution', 'ResolutionController@submitMRResolution')->name('submit_mr_resolution');
    Route::get('/edit_resolution_granting_the_motion_for_reconsideration/{id}', 'ResolutionController@editResolutionGrantingTheMotionFOrReconsideration');
    Route::get('/generate_mr_resolution/{id}', 'ResolutionController@generateMRResolution');
    Route::post('/filter_resolution', 'ResolutionController@filterResolution')->name('filter_resolution');
    Route::post('delete_resolution', 'ResolutionController@deleteResolution')->name('delete_resolution');

    // contracts
    Route::post('/filter_performance_bonds', 'ContractController@getPerformanceBond')->name('filter_performance_bonds');
    Route::post('/submit_performance_bond', 'ContractController@submitPerformanceBond')->name('submit_performance_bond');

    Route::post('/filter_contracts', 'ContractController@prepareContract')->name('filter_contracts');
    Route::post('/submit_contract', 'ContractController@submitContract')->name('submit_contract');
    Route::get('/generate_contract/{id}', ['uses' => 'ContractController@generateContract', 'as' => 'id']);

    // CHSP
    Route::post('/filter_chsp', 'CHSPController@getCHSP')->name('filter_chsp');
    Route::post('/submit_chsp', 'CHSPController@submitCHSP')->name('submit_chsp');

    // additional Performance Bond
    Route::post('/filter_additional_performance_bond', 'PerformanceBondController@getAdditionalPerformanceBond')->name('filter_additional_performance_bond');
    Route::post('/submit_additional_performance_bond', 'PerformanceBondController@submitAdditionalPerformanceBond')->name('submit_additional_performance_bond');
    Route::post('/delete_additional_performance_bond', 'PerformanceBondController@deleteAdditionalPerformanceBond')->name('delete_additional_performance_bond');


    // generateAbstract
    Route::post('/submit_generate_abstract', 'ReportController@submitGenerateAbstract')->name('submit_generate_abstract');
    Route::get('/download_abstract/{date_opened}', ['uses' => 'ReportController@downloadAbstract', 'as' => 'date_opened']);


    // supplementalbid
    Route::get('/view_supplemental_bid/{id}', ['uses' => 'SupplementalBidBulletinController@viewSupplementalBid', 'as' => 'id']);
    Route::get('/delete_supplemental_bid/{id}', ['uses' => 'SupplementalBidBulletinController@deleteSupplementalBid', 'as' => 'id']);
    Route::post('/submit_supplemental_bid', 'SupplementalBidBulletinController@submitSupplementalBid')->name('submit_supplemental_bid');
    Route::post('/get_supplemental_bid_attachments', 'SupplementalBidBulletinController@getSBAttachments')->name('get_supplemental_bid_attachments');
    Route::post('/delete_supplemental_bid_attachment', 'SupplementalBidBulletinController@deleteSBAttachment')->name('delete_supplemental_bid_attachment');
    Route::get('/view_supplemental_bid_attachment/{id}', ['uses' => 'SupplementalBidBulletinController@viewSBAttachment', 'as' => 'id']);
    Route::post('/filter_sb', 'SupplementalBidBulletinController@filterSupplementalBids')->name('filter_sb');

    // motion for reconsideration
    Route::post('/submit_motion_for_reconsideration', 'MotionForReconsiderationController@submitMotionForReconsideration')->name('submit_motion_for_reconsideration');
    Route::get('/view_motion_for_reconsideration/{id}', ['uses' => 'MotionForReconsiderationController@viewMotionFOrConsideration', 'as' => 'id']);
    Route::get('/delete_motion_for_reconsideration/{mr_id}', ['uses' => 'MotionForReconsiderationController@deleteMotionForReconsideration', 'as' => 'mr_id']);
    Route::post('/filter_motion_for_reconsideration', 'MotionForReconsiderationController@filterMotionForReconsiderations')->name('filter_motion_for_reconsideration');
    Route::post('/get_mr_attachments', 'MotionForReconsiderationController@getMRAttachments')->name('get_mr_attachments');
    Route::post('/delete_mr_attachment', 'MotionForReconsiderationController@deleteMRAttachment')->name('delete_mr_attachment');
    Route::get('/view_mr_attachment/{id}', ['uses' => 'MotionForReconsiderationController@viewMRAttachment', 'as' => 'id']);


    //BAC
    Route::get('/add_bac', 'BACController@addBAC')->name('add_bac');
    Route::get('/edit_bac/{id}', 'BACController@editBAC')->name('edit_bac');
    Route::get('/delete_bac/{id}', ['uses' => 'BACController@deleteBAC', 'as' => 'id']);

    // members
    Route::post('/submit_member', 'BACController@submitMember')->name('submit_member');
    Route::post('/autocomplete_members', 'BACController@autoCompleteMembers')->name('autocomplete_members');
    Route::post('/submit_bac', 'BACController@submitBAC')->name('submit_bac');
    Route::get('/delete_member/{id}', ['uses' => 'BACController@deleteMember', 'as' => 'id']);

    // observers
    Route::post('/submit_observer', 'BACController@submitObserver')->name('submit_observer');
    Route::post('/autocomplete_observers', 'BACController@autoCompleteObservers')->name('autocomplete_observers');
    Route::get('/delete_observer/{id}', ['uses' => 'BACController@deleteObserver', 'as' => 'id']);

    // twg
    Route::post('/filter_twg_post_qual', 'TWGController@getPostQualificationActivity')->name('filter_twg_post_qual');
    Route::get('/limited_regular_app', 'APPController@getLimitedRegularAPP')->name('limited_regular_app');
    Route::get('/limited_supplemental_app', 'APPController@getLimitedSupplementalAPP')->name('limited_supplemental_app');
    Route::get('/twg_project_bidders/{id}', ['uses' => 'BidderController@getTWGBidders', 'as' => 'id']);
    Route::post('/twg_responsive_bidder', 'BidderController@setTWGResponsiveBiddder')->name('twg_responsive_bidder');
    Route::post('/twg_non_responsive_bidder', 'BidderController@setTWGNonResponsiveBiddder')->name('twg_non_responsive_bidder');
    Route::post('/twg_bid_as_calculated_bidder', 'BidderController@setTWGBidAsCalculated')->name('twg_bid_as_calculated_bidder');
    Route::get('/notice_to_submit_post_qualification_docs', 'NoticeController@getNoticeToSubmitPostQualDocs')->name('notice_to_submit_post_qualification_docs');
    Route::post('/filter_project_with_bidders', 'TWGController@filterProjectsWithBidders')->name('filter_project_with_bidders');

    // Request for Extension
    Route::get('/request_for_extension', 'RequestForExtensionController@getRequestForExtensions')->name('request_for_extension');
    Route::get('/create_request_for_extension', 'RequestForExtensionController@getRequestForExtensionForm')->name('create_request_for_extension');
    Route::post('/submit_request_for_extension', 'RequestForExtensionController@submitRequestForExtension')->name('submit_request_for_extension');
    Route::get('/edit_request_for_extension/{id}', ['uses' => 'RequestForExtensionController@getRequestForExtensionForm', 'as' => 'id']);
    Route::post('/delete_request_for_extension', 'RequestForExtensionController@deleteRequestForExtension')->name('delete_request_for_extension');
    Route::get('/generate_request_for_extension/{id}', ['uses' => 'RequestForExtensionController@generateRequestForExtension', 'as' => 'id']);


    // Archiving
    Route::group(['prefix' => 'archive', 'as' => 'archive.'], function () {

        // APP
        Route::post('submit_app', 'ArchiveController@submitApp')->name('submit_app');
        Route::post('get_project_attachments', 'ArchiveController@getProjectAttachments')->name('get_project_attachments');
        Route::post('delete_project_attachment', 'ArchiveController@deleteProjectAttachment')->name('delete_project_attachment');
        Route::get('view_project_attachment/{id}', 'ArchiveController@viewProjectAttachment')->name('view_project_attachment/{id}');
        Route::get('view_project_attachments/{id}', 'ArchiveController@viewProjectAttachments')->name('view_project_attachments/{id}');
        Route::post('delete_project_attachments', 'ArchiveController@deleteProjectAttachments')->name('delete_project_attachments');
        Route::post('filter_archive_app', 'ArchiveController@filterArchiveApp')->name('filter_archive_app');

        // ITB
        Route::get('invitation_to_bids/{year}', 'ArchiveController@getITBArchive')->name('invitation_to_bids/{year}');
        Route::post('filter_invitation_to_bid', 'ArchiveController@filterITB')->name('filter_invitation_to_bid');
        Route::post('submit_invitation_to_bid', 'ArchiveController@submitITB')->name('submit_invitation_to_bid');
        Route::post('get_invitation_to_bid_attachments', 'ArchiveController@getITBAttachments')->name('get_invitation_to_bid_attachments');
        Route::get('view_invitation_to_bid_attachment/{id}', 'ArchiveController@viewITBAttachment')->name('view_invitation_to_bid_attachment/{id}');
        Route::get('view_invitation_to_bid_attachments/{id}', 'ArchiveController@viewITBAttachments')->name('view_invitation_to_bid_attachments/{id}');
        Route::post('delete_invitation_to_bid_attachment', 'ArchiveController@deleteITBAttachment')->name('delete_invitation_to_bid_attachment');
        Route::post('delete_invitation_to_bid_attachments', 'ArchiveController@deleteITBAttachments')->name('delete_invitation_to_bid_attachments');

        // RFQ
        Route::get('rfqs/{year}', 'ArchiveController@getRFQArchive')->name('rfqs/{year}');
        Route::post('filter_rfq', 'ArchiveController@filterRFQ')->name('filter_rfq');
        Route::post('submit_rfq', 'ArchiveController@submitRFQ')->name('submit_rfq');
        Route::post('get_rfq_attachments', 'ArchiveController@getRFQAttachments')->name('get_rfq_attachments');
        Route::get('view_rfq_attachment/{id}', 'ArchiveController@viewRFQAttachment')->name('view_rfq_attachment/{id}');
        Route::get('view_rfq_attachments/{id}', 'ArchiveController@viewRFQAttachments')->name('view_rfq_attachments/{id}');
        Route::post('delete_rfq_attachment', 'ArchiveController@deleteRFQAttachment')->name('delete_rfq_attachment');
        Route::post('delete_rfq_attachments', 'ArchiveController@deleteRFQAttachments')->name('delete_rfq_attachments');

        //abstracts
        Route::get('abstracts/{year}', 'ArchiveController@getAbstractArchive')->name('abstracts/{year}');
        Route::post('filter_abstract', 'ArchiveController@filterAbstract')->name('filter_abstract');
        Route::post('submit_abstract', 'ArchiveController@submitAbstract')->name('submit_abstract');
        Route::post('get_archive_abstract_attachments', 'ArchiveController@getArchiveAbstractAttachments')->name('get_archive_abstract_attachments');
        Route::get('view_abstract_attachment/{id}', 'ArchiveController@viewAbstractAttachment')->name('view_abstract_attachment/{id}');
        Route::get('view_abstract_attachments/{id}', 'ArchiveController@viewAbstractAttachments')->name('view_abstract_attachments/{id}');
        Route::post('delete_abstract_attachment', 'ArchiveController@deleteAbstractAttachment')->name('delete_abstract_attachment');
        Route::post('delete_abstract', 'ArchiveController@deleteAbstract')->name('delete_abstract');

        //certificate_of_postings
        Route::get('certificate_of_postings/{year}', 'ArchiveController@getCertificateOfPostingArchive')->name('certificate_of_postings/{year}');
        Route::post('filter_certificate_of_posting', 'ArchiveController@filterCertificateOfPosting')->name('filter_certificate_of_posting');
        Route::post('submit_certificate_of_posting', 'ArchiveController@submitCertificateOfPosting')->name('submit_certificate_of_posting');
        Route::post('get_archive_certificate_of_posting_attachments', 'ArchiveController@getArchiveCertificateOfPostingAttachments')->name('get_archive_certificate_of_posting_attachments');
        Route::get('view_certificate_of_posting_attachment/{id}', 'ArchiveController@viewCertificateOfPostingAttachment')->name('view_certificate_of_posting_attachment/{id}');
        Route::get('view_certificate_of_posting_attachments/{id}', 'ArchiveController@viewCertificateOfPostingAttachments')->name('view_certificate_of_posting_attachments/{id}');
        Route::post('delete_certificate_of_posting_attachment', 'ArchiveController@deleteCertificateOfPostingAttachment')->name('delete_certificate_of_posting_attachment');
        Route::post('delete_certificate_of_posting', 'ArchiveController@deleteCertificateOfPosting')->name('delete_certificate_of_posting');

        // minutes
        Route::get('minutes/{year}', 'ArchiveController@getMinuteArchive')->name('minutes/{year}');
        Route::post('filter_minute', 'ArchiveController@filterMinute')->name('filter_minute');
        Route::post('submit_minute', 'ArchiveController@submitMinute')->name('submit_minute');
        Route::post('get_archive_minute_attachments', 'ArchiveController@getArchiveMinuteAttachments')->name('get_archive_minute_attachments');
        Route::get('view_minute_attachment/{id}', 'ArchiveController@viewMinuteAttachment')->name('view_minute_attachment/{id}');
        Route::get('view_minute_attachments/{id}', 'ArchiveController@viewMinuteAttachments')->name('view_minute_attachments/{id}');
        Route::post('delete_minute_attachment', 'ArchiveController@deleteMinuteAttachment')->name('delete_minute_attachment');
        Route::post('delete_minute', 'ArchiveController@deleteMinute')->name('delete_minute');

        // Meeting Attendance
        Route::get('meeting_attendance/{year}', 'ArchiveController@getMeetingAttendanceArchive')->name('meeting_attendance/{year}');
        Route::post('filter_meeting_attendance', 'ArchiveController@filterMeetingAttendance')->name('filter_meeting_attendance');
        Route::post('submit_meeting_attendance', 'ArchiveController@submitMeetingAttendance')->name('submit_meeting_attendance');
        Route::post('get_archive_meeting_attendance_attachments', 'ArchiveController@getArchiveMeetingAttendanceAttachments')->name('get_archive_meeting_attendance_attachments');
        Route::get('view_meeting_attendance_attachment/{id}', 'ArchiveController@viewMeetingAttendanceAttachment')->name('view_meeting_attendance_attachment/{id}');
        Route::get('view_meeting_attendance_attachments/{id}', 'ArchiveController@viewMeetingAttendanceAttachments')->name('view_meeting_attendance_attachments/{id}');
        Route::post('delete_meeting_attendance_attachment', 'ArchiveController@deleteMeetingAttendanceAttachment')->name('delete_meeting_attendance_attachment');
        Route::post('delete_meeting_attendance', 'ArchiveController@deleteMeetingAttendance')->name('delete_meeting_attendance');

        // Resolutions
        Route::get('resolution_recommending_awards/{year}', 'ArchiveController@getResolutionRecommendingAwards')->name('resolution_recommending_awards/{year}');
        Route::get('resolution_recommending_awards/{year}', 'ArchiveController@getResolutionRecommendingAwards')->name('resolution_recommending_awards/{year}');
        Route::get('resolution_declaring_failure/{year}', 'ArchiveController@getResolutionDeclaringFailure')->name('resolution_declaring_failure/{year}');
        Route::get('resolution_granting_motion/{year}', 'ArchiveController@getResolutionGrantingMotion')->name('resolution_granting_motion/{year}');
        Route::get('resolution_denying_motion/{year}', 'ArchiveController@getResolutionDenyingMotion')->name('resolution_denying_motion/{year}');
        Route::get('resolution_recall_cancelling/{year}', 'ArchiveController@getResolutionRecallCancelling')->name('resolution_recall_cancelling/{year}');
        Route::get('other_resolutions/{year}', 'ArchiveController@getOtherResolutions')->name('other_resolutions/{year}');
        

        Route::post('submit_resolution', 'ArchiveController@submitResolution')->name('submit_resolution');
        Route::post('get_archive_resolution_attachments', 'ArchiveController@getResolutionAttachments')->name('get_archive_resolution_attachments');
        Route::get('view_resolution_attachment/{id}', 'ArchiveController@viewResolutionAttachment')->name('view_resolution_attachment/{id}');
        Route::get('view_resolution_attachments/{id}', 'ArchiveController@viewResolutionAttachments')->name('view_resolution_attachments/{id}');
        Route::post('delete_resolution_attachment', 'ArchiveController@deleteResolutionAttachment')->name('delete_resolution_attachment');
        Route::post('filter_resolutions', 'ArchiveController@filterResolutions')->name('filter_resolutions');

        //orders
        Route::get('orders/{year}', 'ArchiveController@getOrderArchive')->name('orders/{year}');
        Route::post('filter_order', 'ArchiveController@filterOrder')->name('filter_order');
        Route::post('submit_order', 'ArchiveController@submitOrder')->name('submit_order');
        Route::post('get_archive_order_attachments', 'ArchiveController@getArchiveOrderAttachments')->name('get_archive_order_attachments');
        Route::get('view_order_attachment/{id}', 'ArchiveController@viewOrderAttachment')->name('view_order_attachment/{id}');
        Route::get('view_order_attachments/{id}', 'ArchiveController@viewOrderAttachments')->name('view_order_attachments/{id}');
        Route::post('delete_order_attachment', 'ArchiveController@deleteOrderAttachment')->name('delete_order_attachment');
        Route::post('delete_order', 'ArchiveController@deleteOrder')->name('delete_order');

        // Notice of Awards
        Route::get('notice_of_awards/{year}', 'ArchiveController@getNoticeOfAwards')->name('notice_of_awards/{year}');
        Route::post('filter_notice_of_awards', 'ArchiveController@filterNoticeOfAwards')->name('filter_notice_of_awards');
        Route::post('submit_notice_of_award', 'ArchiveController@submitNoticeOfAward')->name('submit_notice_of_award');
        Route::post('get_archive_noa_attachments', 'ArchiveController@getNOAAttachments')->name('get_archive_noa_attachments');
        Route::get('view_noa_attachment/{id}', 'ArchiveController@viewNOAAttachment')->name('view_noa_attachment/{id}');
        Route::get('view_noa_attachments/{id}', 'ArchiveController@viewNOAAttachments')->name('view_noa_attachments/{id}');
        Route::post('delete_noa_attachment', 'ArchiveController@deleteNOAAttachment')->name('delete_noa_attachment');




        // Contracts
        Route::get('contracts/{year}', 'ArchiveController@getContracts')->name('contracts/{year}');
        Route::post('filter_contracts', 'ArchiveController@filterContracts')->name('filter_contracts');
        Route::post('submit_contract', 'ArchiveController@submitContract')->name('submit_contract');
        Route::post('get_archive_contract_attachments', 'ArchiveController@getContractAttachments')->name('get_archive_contract_attachments');
        Route::get('view_contract_attachment/{id}', 'ArchiveController@viewContractAttachment')->name('view_contract_attachment/{id}');
        Route::get('view_contract_attachments/{id}', 'ArchiveController@viewContractAttachments')->name('view_contract_attachments/{id}');
        Route::post('delete_contract_attachment', 'ArchiveController@deleteContractAttachment')->name('delete_contract_attachment');

        // Notice to Proceed
        Route::get('ntps/{year}', 'ArchiveController@getNTPs')->name('ntps/{year}');
        Route::post('filter_ntps', 'ArchiveController@filterNTPs')->name('filter_ntps');
        Route::post('submit_ntp', 'ArchiveController@submitNTP')->name('submit_ntp');
        Route::post('get_archive_ntp_attachments', 'ArchiveController@getNTPAttachments')->name('get_archive_ntp_attachments');
        Route::get('view_ntp_attachment/{id}', 'ArchiveController@viewNTPAttachment')->name('view_ntp_attachment/{id}');
        Route::get('view_ntp_attachments/{id}', 'ArchiveController@viewNTPAttachments')->name('view_ntp_attachments/{id}');
        Route::post('delete_ntp_attachment', 'ArchiveController@deleteNTPAttachment')->name('delete_ntp_attachment');
        Route::post('filter_ntps', 'ArchiveController@filterNTPs')->name('filter_ntps');
        Route::post('compute_duration', 'ArchiveController@computeDuration')->name('compute_duration');



        // Transmittal
        Route::get('transmittals/{year}', 'ArchiveController2@getTransmittals')->name('transmittals/{year}');
        Route::post('filter_transmittals', 'ArchiveController2@filterTransmittals')->name('filter_transmittals');
        Route::post('submit_transmittal', 'ArchiveController2@submitTransmittal')->name('submit_transmittal');
        Route::post('get_archive_transmittal_attachments', 'ArchiveController2@getTransmittalAttachments')->name('get_archive_transmittal_attachments');
        Route::get('view_transmittal_attachment/{id}', 'ArchiveController2@viewTransmittalAttachment')->name('view_transmittal_attachment/{id}');
        Route::get('view_transmittal_attachments/{id}', 'ArchiveController2@viewTransmittalAttachments')->name('view_transmittal_attachments/{id}');
        Route::post('delete_transmittal_attachment', 'ArchiveController2@deleteTransmittalAttachment')->name('delete_transmittal_attachment');
        Route::post('delete_transmittal', 'ArchiveController2@deleteTransmittal')->name('delete_transmittal');
        Route::post('filter_transmittals', 'ArchiveController2@filterTransmittals')->name('filter_transmittals');
        Route::post('autocomplete_project_with_ntps', 'ArchiveController2@autoCompleteProjectWithNTPs')->name('autocomplete_project_with_ntps');


        // Notices
        Route::post('filter_notices', 'ArchiveController@filterNotice')->name('filter_notices');
        Route::post('submit_notice', 'ArchiveController@submitProjectBidderNotice')->name('submit_notice');
        Route::post('get_archive_notice_attachments', 'ArchiveController@getProjectBidderNoticeAttachments')->name('get_archive_notice_attachments');
        Route::get('view_notice_attachment/{id}', 'ArchiveController@viewProjectBidderNoticeAttachment')->name('view_notice_attachment/{id}');
        Route::get('view_notice_attachments/{id}', 'ArchiveController@viewProjectBidderNoticeAttachments')->name('view_notice_attachments/{id}');
        Route::post('delete_notice_attachment', 'ArchiveController@deleteProjectBidderNoticeAttachment')->name('delete_notice_attachment');

        // notices of meetings
        Route::get('notice_of_meetings/{year}', 'ArchiveController@getNoticeOfMeetingArchive')->name('notice_of_meetings/{year}');
        Route::post('filter_notice_of_meeting', 'ArchiveController@filterNoticeOfMeeting')->name('filter_notice_of_meeting');
        Route::post('submit_notice_of_meeting', 'ArchiveController@submitNoticeOfMeeting')->name('submit_notice_of_meeting');
        Route::post('get_archive_notice_of_meeting_attachments', 'ArchiveController@getArchiveNoticeOfMeetingAttachments')->name('get_archive_notice_of_meeting_attachments');
        Route::get('view_notice_of_meeting_attachment/{id}', 'ArchiveController@viewNoticeOfMeetingAttachment')->name('view_notice_of_meeting_attachment/{id}');
        Route::get('view_notice_of_meeting_attachments/{id}', 'ArchiveController@viewNoticeOfMeetingAttachments')->name('view_notice_of_meeting_attachments/{id}');
        Route::post('delete_notice_of_meeting_attachment', 'ArchiveController@deleteNoticeOfMeetingAttachment')->name('delete_notice_of_meeting_attachment');
        Route::post('delete_notice_of_meeting', 'ArchiveController@deleteNoticeOfMeeting')->name('delete_notice_of_meeting');

        // termination of contract
        Route::get('termination_of_contract/{year}', 'ArchiveController@getTerminationArchive')->name('termination_of_contract/{year}');
        Route::post('filter_termination_of_contract', 'ArchiveController@filterTermination')->name('filter_termination_of_contract');
        Route::post('submit_termination_of_contract', 'ArchiveController@submitTermination')->name('submit_termination_of_contract');
        Route::post('get_archive_termination_of_contract_attachments', 'ArchiveController@getArchiveTerminationAttachments')->name('get_archive_termination_of_contract_attachments');
        Route::get('view_termination_of_contract_attachment/{id}', 'ArchiveController@viewTerminationAttachment')->name('view_termination_of_contract_attachment/{id}');
        Route::get('view_termination_of_contract_attachments/{id}', 'ArchiveController@viewTerminationAttachments')->name('view_termination_of_contract_attachments/{id}');
        Route::post('delete_termination_of_contract_attachment', 'ArchiveController@deleteTerminationAttachment')->name('delete_termination_of_contract_attachment');
        Route::post('delete_termination_of_contract', 'ArchiveController@deleteTermination')->name('delete_termination_of_contract');
    });

    // preparation
    Route::group(['prefix' => 'prepare', 'as' => 'prepare.'], function () {
        Route::post('filter_bidder_notices', 'NoticeController@filterBidderNotice')->name('filter_bidder_notices');
    });
});
