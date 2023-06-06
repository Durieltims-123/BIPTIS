@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  @include('layouts.components.modals')
  <div class="card shadow">
    <div class="card shadow border-0">
      <div class="card-header">
        <h2>Procurement Activity</h2>
        <p class="text-sm"  id="title">{{$project_plans[0]->project_no}} - {{$project_plans[0]->project_title}}</p>
        <p class="text-sm" >ABC: <span class="text-red">Php {{number_format($project_plans[0]->abc)}}</span></p>
      </div>
      <div class="card-body">
        <div id="stepper1" class="bs-stepper">
          <div class="bs-stepper-header mb-3">

            @if($activity_status->pre_proc!='not_needed')
            <div class="step" data-target="#process-l-1">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Preprocurement" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->pre_proc=='finished') bg-primary text-white @endif"><i class="ni ni-shop"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>
            <div class="line"></div>
            @endif



            <div class="step" data-target="#process-l-2">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Advertisement/Posting" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->advertisement=='finished') bg-primary text-white @endif"><i class="ni ni-notification-70"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>

            <div class="line"></div>
            @if($activity_status->pre_bid!='not_needed')
            <div class="step" data-target="#process-l-3">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Pre-bid" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->pre_bid=='finished') bg-primary text-white @endif"><i class="ni ni-money-coins"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>
            <div class="line"></div>
            @endif



            <div class="step" data-target="#process-l-4">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Submission/Opening of Bids" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->open_bid=='finished') bg-primary text-white @endif"><i class="ni ni-books"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>

            <div class="line"></div>

            <div class="step" data-target="#process-l-5">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Bid Evaluation" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->bid_evaluation=='finished') bg-primary text-white @endif"><i class="ni ni-check-bold"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>

            <div class="line"></div>

            <div class="step" data-target="#process-l-6">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Post Qualification" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->post_qual=='finished') bg-primary text-white @endif"><i class="ni ni-box-2"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>

            <div class="line"></div>

            <div class="step" data-target="#process-l-7">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Notice of Award" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->award_notice=='finished') bg-primary text-white @endif"><i class="ni ni-trophy"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>

            <div class="line"></div>

            <div class="step" data-target="#process-l-8">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Contract Preparation and Signing" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->contract_signing=='finished') bg-primary text-white @endif"><i class="ni ni-single-copy-04"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>

            <div class="line"></div>

            @if($activity_status->authority_approval!='not_needed')
            <div class="step" data-target="#process-l-9">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Authority Approval" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->authority_approval=='finished') bg-primary text-white @endif"><i class="ni ni-like-2"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>
            @endif

            <div class="line"></div>

            <div class="step" data-target="#process-l-10">
              <button type="button" class="btn btn-smstep-trigger p-1" data-toggle="tooltip" data-placement="top" title="Notice to Proceed" data-container="body" data-animation="true">
                <span class="bs-stepper-circle @if($activity_status->proceed_notice=='finished') bg-primary text-white @endif"><i class="ni ni-notification-70"></i></span>
                <span class="bs-stepper-label"></span>
              </button>
            </div>

          </div>
          <div class="bs-stepper-content">

            @if($activity_status->pre_proc!='not_needed')
            <div id="process-l-1" class="content row">
              <h2 class="mt-3 text-center">PREPROCUREMENT</h2>
              <form  class="row mt-4"  method="POST" action="{{route('submit_preprocurement')}}">
                @csrf
                <div class="form-group col-xs-12 col-sm-6 col-md-6 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="advertisement_posting_start_date">Advertisement/Posting Start Date:</h5>
                  <input type="text"  name="advertisement_posting_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->advertisement_start))}}">
                  <label class="error-msg text-red" >@error('advertisement_posting_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="advertisement_posting_end_date">Advertisement/Posting End Date:</h5>
                  <input type="text"  name="advertisement_posting_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->advertisement_end))}}">
                  <label class="error-msg text-red" >@error('advertisement_posting_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4 mx-auto">
                  <h5 for="preprocurement_date">Date of Procurement<span class="text-red">*</span></h5>
                  <input type="text" id="preprocurement_date" name="preprocurement_date" class="form-control form-control-sm datepicker"   @if($activity_status->pre_proc=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->pre_proc))}}" @else value="{{old('preprocurement_date')}}"@endif >
                  <label class="error-msg text-red" >@error('preprocurement_date'){{$message}}@enderror</label>
                </div>

                @if($activity_status->pre_proc=='pending')
                <div class="form-group col-xs-12 col-sm-6 col-md-6 mx-auto d-flex justify-content-center">
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>
                </div>
                @endif
              </form>
              @if($activity_status->pre_proc=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info ml-2" onclick="stepper1.next()">Next</button>
              </div>
              @endif
            </div>
            @endif

            <div id="process-l-2" class="content active dstepper-block">
              <h2 class="mt-3 text-center">ADVERTISEMENT/POSTING</h2>
              <form  class="row col-sm-12 mx-auto mt-4"  method="POST" action="{{route('submit_advertisement_posting')}}">
                @csrf

                <div class="form-group col-xs-12 col-sm-4 col-md-4 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="advertisement_posting_start_date">Advertisement/Posting Start Date:</h5>
                  <input type="text" id="advertisement_posting_start_date" name="advertisement_posting_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->advertisement_start))}}">
                  <label class="error-msg text-red" >@error('advertisement_posting_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="advertisement_posting_end_date">Advertisement/Posting End Date:</h5>
                  <input type="text" id="advertisement_posting_end_date" name="advertisement_posting_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->advertisement_end))}}">
                  <label class="error-msg text-red" >@error('advertisement_posting_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="advertisement_posting_date">Advertisement/Posting Date<span class="text-red">*</span></h5>
                  <input type="text" id="advertisement_posting_date" name="advertisement_posting_date" class="form-control form-control-sm datepicker"   @if($activity_status->advertisement=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->advertisement))}}" @else value="{{old('advertisement_posting_date')}}"@endif >
                  <label class="error-msg text-red" >@error('advertisement_posting_date'){{$message}}@enderror</label>
                </div>

                @if($activity_status->advertisement=='pending')
                <div class="form-group col-xs-12 col-sm-6 col-md-6 mx-auto d-flex justify-content-center">
                  <button type="button" class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>
                </div>
                @endif
              </form>
              @if($activity_status->advertisement=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                <button class="btn btn-sm btn btn-sm btn-info ml-2" onclick="stepper1.next()">Next</button>
              </div>
              @endif
            </div>

            @if($activity_status->pre_bid!='not_needed')
            <div id="process-l-3" class="content">
              <h2 class="mt-3 text-center">PRE-BID CONFERENCE</h2>
              <form  class="row col-sm-12 mx-auto mt-4"  method="POST" action="{{route('submit_prebid')}}">
                @csrf

                <div class="form-group col-xs-12 col-sm-4 col-md-4 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="pre_bid_start_date">Pre-bid Conference Start Date:</h5>
                  <input type="text" id="pre_bid_start_date" name="pre_bid_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->pre_bid_start))}}">
                  <label class="error-msg text-red" >@error('pre_bid_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="pre_bid_end_date">Pre-bid Conference End Date:</h5>
                  <input type="text" id="pre_bid_end_date" name="pre_bid_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->pre_bid_end))}}">
                  <label class="error-msg text-red" >@error('pre_bid_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="pre_bid_date">Pre-bid Conference Date<span class="text-red">*</span></h5>
                  <input type="text" id="pre_bid_date" name="pre_bid_date" class="form-control form-control-sm datepicker"   @if($activity_status->pre_bid=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->pre_bid))}}" @else value="{{old('pre_bid_date')}}"@endif >
                  <label class="error-msg text-red" >@error('pre_bid_date'){{$message}}@enderror</label>
                </div>

                @if($activity_status->pre_bid=='pending')
                <div class="form-group col-xs-12 col-sm-6 col-md-6 mx-auto d-flex justify-content-center">
                  <button type="button" class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>
                </div>
                @endif
              </form>
              @if($activity_status->pre_bid=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                <button class="btn btn-sm btn btn-sm btn-info ml-2" onclick="stepper1.next()">Next</button>
              </div>
              @endif
            </div>
            @endif

            <div id="process-l-4" class="content">
              <h2 class="mt-3 text-center">SUBMISSION/OPENING OF BIDS</h2>
              <form  class="row col-sm-12 mx-auto mt-4"  method="POST" action="{{route('submit_submission_opening_of_bid')}}">
                @csrf

                <div class="form-group col-xs-12 col-sm-4 col-md-4 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="submission_opening_of_bid_start_date">Submission/Opening of Bids Start Date:</h5>
                  <input type="text" id="submission_opening_of_bid_start_date" name="submission_opening_of_bid_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->bid_submission_start))}}">
                  <label class="error-msg text-red" >@error('submission_opening_of_bid_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="submission_opening_of_bid_end_date">Submission/Opening of Bids End Date:</h5>
                  <input type="text" id="submission_opening_of_bid_end_date" name="submission_opening_of_bid_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->bid_submission_end))}}">
                  <label class="error-msg text-red" >@error('submission_opening_of_bid_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="submission_opening_of_bid_date">Submission/Opening of Bids Date<span class="text-red">*</span></h5>
                  <input type="text" id="submission_opening_of_bid_date" name="submission_opening_of_bid_date" class="form-control form-control-sm datepicker"   @if($activity_status->open_bid=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->open_bid))}}" @else value="{{old('submission_opening_of_bid_date')}}"@endif >
                  <label class="error-msg text-red" >@error('submission_opening_of_bid_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="submission_opening_of_bid_time">Submission/Opening of Bids Time (24 hour format)<span class="text-red">*</span></h5>
                  <input type="text" id="submission_opening_of_bid_time" name="submission_opening_of_bid_time" class="form-control form-control-sm timepicker"    @if($activity_status->open_bid=='finished') readonly value="{{$project_plans[0]->open_time}}" @else value="{{old('submission_opening_of_bid_time')}}"@endif >
                  <label class="error-msg text-red" >@error('submission_opening_of_bid_time'){{$message}}@enderror</label>
                </div>

                @if($activity_status->open_bid=='pending')
                <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                  <a type="button"  href="../project_bidders/{{$project_plans[0]->plan_id}}" target="_blank" class="btn btn-sm btn btn-sm btn-success bidd" >Bidders</a>
                  @if($project_plans[0]->re_bid_count<=3||$project_plans[0]->re_bid_count==null)
                  <button type="button" class="btn btn-sm btn btn-sm btn-warning rebid_btn" >Rebid</button>
                  @else
                  <button type="button" class="btn btn-sm btn btn-sm btn-danger review_btn" >Review</button>
                  @endif
                  <button type="button" class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>

                </div>
                @endif
              </form>
              @if($activity_status->open_bid=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                <button class="btn btn-sm btn btn-sm btn-info ml-2" onclick="stepper1.next()">Next</button>
              </div>
              @endif
            </div>


            <div id="process-l-5" class="content">
              <h2 class="mt-3 text-center">BID EVALUATION</h2>
              <form  class="row col-sm-12 mx-auto mt-4"  method="POST" action="{{route('submit_bid_evaluation')}}">
                @csrf

                <div class="form-group col-xs-12 col-sm-4 col-md-4 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="bid_evaluation_start_date">Bid Evaluation Start Date:</h5>
                  <input type="text" id="bid_evaluation_start_date" name="bid_evaluation_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->bid_evaluation_start))}}">
                  <label class="error-msg text-red" >@error('bid_evaluation_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="bid_evaluation_end_date">Bid Evaluation End Date:</h5>
                  <input type="text" id="bid_evaluation_end_date" name="bid_evaluation_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->bid_evaluation_end))}}">
                  <label class="error-msg text-red" >@error('bid_evaluation_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="bid_evaluation_date">Bid Evaluation Date<span class="text-red">*</span></h5>
                  <input type="text" id="bid_evaluation_date" name="bid_evaluation_date" class="form-control form-control-sm datepicker"   @if($activity_status->bid_evaluation=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->bid_evaluation))}}" @else value="{{old('bid_evaluation_date')}}"@endif >
                  <label class="error-msg text-red" >@error('bid_evaluation_date'){{$message}}@enderror</label>
                </div>


                @if($activity_status->bid_evaluation=='pending')
                <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                  <a type="button"  href="../project_bidders/{{$project_plans[0]->plan_id}}" target="_blank" class="btn btn-sm btn btn-sm btn-success bidd" >Bidders</a>
                  @if($project_plans[0]->re_bid_count<=3||$project_plans[0]->re_bid_count==null)
                  <button type="button" class="btn btn-sm btn btn-sm btn-warning rebid_btn" >Rebid</button>
                  @else
                  <button type="button" class="btn btn-sm btn btn-sm btn-danger review_btn" >Review</button>
                  @endif
                  <button type="button" class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>
                </div>
                @endif
              </form>
              @if($activity_status->bid_evaluation=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                <button class="btn btn-sm btn btn-sm btn-info ml-2" onclick="stepper1.next()">Next</button>
              </div>
              @endif
            </div>


            <div id="process-l-6" class="content">
              <h2 class="mt-3 text-center">POST QUALIFICATION</h2>
              <form  class="row col-sm-12 mx-auto mt-4"  method="POST" action="{{route('submit_post_qualification')}}">
                @csrf

                <div class="form-group col-xs-12 col-sm-4 col-md-4 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="post_qualification_start_date">Post Qualification Start Date:</h5>
                  <input type="text" id="post_qualification_start_date" name="post_qualification_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->post_qualification_start))}}">
                  <label class="error-msg text-red" >@error('post_qualification_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="post_qualification_end_date">Post Qualification End Date:</h5>
                  <input type="text" id="post_qualification_end_date" name="post_qualification_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->post_qualification_end))}}">
                  <label class="error-msg text-red" >@error('post_qualification_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="post_qualification_date">Post Qualification Date<span class="text-red">*</span></h5>
                  <input type="text" id="post_qualification_date" name="post_qualification_date" class="form-control form-control-sm datepicker"   @if($activity_status->post_qual=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->post_qual))}}" @else value="{{old('post_qualification_date')}}"@endif >
                  <label class="error-msg text-red" >@error('post_qualification_date'){{$message}}@enderror</label>
                </div>


                @if($activity_status->post_qual=='pending')
                <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                  <a type="button"  href="../project_bidders/{{$project_plans[0]->plan_id}}" target="_blank" class="btn btn-sm btn btn-sm btn-success bidd" >Bidders</a>
                  @if($project_plans[0]->re_bid_count<=3||$project_plans[0]->re_bid_count==null)
                  <button type="button" class="btn btn-sm btn btn-sm btn-warning rebid_btn" >Rebid</button>
                  @else
                  <button type="button" class="btn btn-sm btn btn-sm btn-danger review_btn" >Review</button>
                  @endif
                  <button type="button" class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>
                </div>
                @endif
              </form>
              @if($activity_status->post_qual=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                <button class="btn btn-sm btn btn-sm btn-info ml-2" onclick="stepper1.next()">Next</button>
              </div>
              @endif
            </div>


            <div id="process-l-7" class="content">
              <h2 class="mt-3 text-center">NOTICE OF AWARD</h2>
              <form  class="row col-sm-12 mx-auto mt-4"  method="POST" action="{{route('submit_award_notice')}}">
                @csrf

                <div class="form-group col-xs-12 col-sm-4 col-md-4 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="award_notice_start_date">Notice of Award  Start Date:</h5>
                  <input type="text" id="award_notice_start_date" name="award_notice_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->award_notice_start))}}">
                  <label class="error-msg text-red" >@error('award_notice_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="award_notice_end_date">Notice of Award  End Date:</h5>
                  <input type="text" id="award_notice_end_date" name="award_notice_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->award_notice_end))}}">
                  <label class="error-msg text-red" >@error('award_notice_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="award_notice_date">Notice of Award  Date<span class="text-red">*</span></h5>
                  <input type="text" id="award_notice_date" name="award_notice_date" class="form-control form-control-sm datepicker"   @if($activity_status->award_notice=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->award_notice))}}" @else value="{{old('award_notice_date')}}"@endif >
                  <label class="error-msg text-red" >@error('award_notice_date'){{$message}}@enderror</label>
                </div>


                @if($activity_status->award_notice=='pending')
                <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                  <a type="button"  href="../project_bidders/{{$project_plans[0]->plan_id}}" target="_blank" class="btn btn-sm btn btn-sm btn-success bidd" >Bidders</a>
                  @if($project_plans[0]->re_bid_count<=3||$project_plans[0]->re_bid_count==null)
                  <button type="button" class="btn btn-sm btn btn-sm btn-warning rebid_btn" >Rebid</button>
                  @else
                  <button type="button" class="btn btn-sm btn btn-sm btn-danger review_btn" >Review</button>
                  @endif
                  <button type="button" class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>
                </div>
                @endif
              </form>
              @if($activity_status->award_notice=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                <button class="btn btn-sm btn btn-sm btn-info ml-2" onclick="stepper1.next()">Next</button>
              </div>
              @endif
            </div>


            <div id="process-l-8" class="content">
              <h2 class="mt-3 text-center">CONTRACT PREPARATION AND SIGNING</h2>
              <form  class="row col-sm-12 mx-auto mt-4"  method="POST" action="{{route('submit_contract_preparation_and_signing')}}">
                @csrf

                <div class="form-group col-xs-12 col-sm-4 col-md-4 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="contract_preparation_and_signing_start_date">Contract Preparation and Signing Date:</h5>
                  <input type="text" id="contract_preparation_and_signing_start_date" name="contract_preparation_and_signing_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->contract_signing_start))}}">
                  <label class="error-msg text-red" >@error('contract_preparation_and_signing_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="contract_preparation_and_signing_end_date">Contract Preparation and Signing  End Date:</h5>
                  <input type="text" id="contract_preparation_and_signing_end_date" name="contract_preparation_and_signing_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->contract_signing_end))}}">
                  <label class="error-msg text-red" >@error('contract_preparation_and_signing_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="contract_preparation_and_signing_date">Contract Preparation and Signing  Date<span class="text-red">*</span></h5>
                  <input type="text" id="contract_preparation_and_signing_date" name="contract_preparation_and_signing_date" class="form-control form-control-sm datepicker"   @if($activity_status->contract_signing=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->contract_signing))}}" @else value="{{old('contract_preparation_and_signing_date')}}"@endif >
                  <label class="error-msg text-red" >@error('contract_preparation_and_signing_date'){{$message}}@enderror</label>
                </div>


                @if($activity_status->contract_signing=='pending')
                <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                  <a type="button"  href="../project_bidders/{{$project_plans[0]->plan_id}}" target="_blank" class="btn btn-sm btn btn-sm btn-success bidd" >Bidders</a>
                  @if($project_plans[0]->re_bid_count<=3||$project_plans[0]->re_bid_count==null)
                  <button type="button" class="btn btn-sm btn btn-sm btn-warning rebid_btn" >Rebid</button>
                  @else
                  <button type="button" class="btn btn-sm btn btn-sm btn-danger review_btn" >Review</button>
                  @endif
                  <button type="button" class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>
                </div>
                @endif
              </form>
              @if($activity_status->contract_signing=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                <button class="btn btn-sm btn btn-sm btn-info ml-2" onclick="stepper1.next()">Next</button>
              </div>
              @endif
            </div>

            @if($activity_status->authority_approval!='not_needed')
            <div id="process-l-9" class="content row">
              <h2 class="mt-3 text-center">AUTHORITY APPROVAL</h2>
              <form  class="row col-sm-12 mx-auto mt-4"  method="POST" action="{{route('submit_authority_approval')}}">
                @csrf
                <div class="form-group col-xs-12 col-sm-6 col-md-6 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="authority_approval_start_date">Authority Appproval Start Date:</h5>
                  <input type="text" id="authority_approval_start_date" name="authority_approval_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->authority_approval_start))}}">
                  <label class="error-msg text-red" >@error('authority_approval_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="authority_approval_end_date">Authority Appproval End Date:</h5>
                  <input type="text" id="authority_approval_end_date" name="authority_approval_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->authority_approval_end))}}">
                  <label class="error-msg text-red" >@error('authority_approval_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="authority_approval_date">Authority Appproval Date<span class="text-red">*</span></h5>
                  <input type="text" id="authority_approval_date" name="authority_approval_date" class="form-control form-control-sm datepicker"   @if($activity_status->authority_approval=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->authority_approval))}}" @else value="{{old('authority_approval_date')}}"@endif >
                  <label class="error-msg text-red" >@error('authority_approval_date'){{$message}}@enderror</label>
                </div>

                @if($activity_status->authority_approval=='pending')
                <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                  <a type="button"  href="../project_bidders/{{$project_plans[0]->plan_id}}" target="_blank" class="btn btn-sm btn btn-sm btn-success bidd" >Bidders</a>
                  @if($project_plans[0]->re_bid_count<=3||$project_plans[0]->re_bid_count==null)
                  <button type="button" class="btn btn-sm btn btn-sm btn-warning rebid_btn" >Rebid</button>
                  @else
                  <button type="button" class="btn btn-sm btn btn-sm btn-danger review_btn" >Review</button>
                  @endif
                  <button type="button" class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>
                </div>
                @endif
              </form>
              @if($activity_status->authority_approval=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                <button class="btn btn-sm btn btn-sm btn-info ml-2" onclick="stepper1.next()">Next</button>
              </div>
              @endif
            </div>
            @endif

            <div id="process-l-10" class="content">
              <h2 class="mt-3 text-center">NOTICE TO PROCEED</h2>
              <form  class="row col-sm-12 mx-auto mt-4"  method="POST" action="{{route('submit_notice_to_proceed')}}">
                @csrf

                <div class="form-group col-xs-12 col-sm-4 col-md-4 mx-auto d-none">
                  <h5 for="plan_id">Plan ID:</h5>
                  <input type="text"  name="plan_id" class="form-control form-control-sm" value="{{$activity_status->plan_id}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="notice_to_proceed_start_date">Notice to Proceed Start Date:</h5>
                  <input type="text" id="notice_to_proceed_start_date" name="notice_to_proceed_start_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->proceed_notice_start))}}">
                  <label class="error-msg text-red" >@error('notice_to_proceed_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="notice_to_proceed_end_date">Notice to Proceed  End Date:</h5>
                  <input type="text" id="notice_to_proceed_end_date" name="notice_to_proceed_end_date" class="form-control form-control-sm datepicker"   readonly value="{{date("m/d/Y", strtotime($project_plans[0]->proceed_notice_end))}}">
                  <label class="error-msg text-red" >@error('notice_to_proceed_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-4 col-md-4">
                  <h5 for="notice_to_proceed_date">Notice to Proceed  Date<span class="text-red">*</span></h5>
                  <input type="text" id="notice_to_proceed_date" name="notice_to_proceed_date" class="form-control form-control-sm datepicker"   @if($activity_status->proceed_notice=='finished') readonly value="{{date("m/d/Y", strtotime($project_plans[0]->proceed_notice))}}" @else value="{{old('notice_to_proceed_date')}}"@endif >
                  <label class="error-msg text-red" >@error('notice_to_proceed_date'){{$message}}@enderror</label>
                </div>


                @if($activity_status->proceed_notice=='pending')
                <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                  <a type="button"  href="../project_bidders/{{$project_plans[0]->plan_id}}" target="_blank" class="btn btn-sm btn btn-sm btn-success bidd" >Bidders</a>
                  @if($project_plans[0]->re_bid_count<=3||$project_plans[0]->re_bid_count==null)
                  <button type="button" class="btn btn-sm btn btn-sm btn-warning rebid_btn" >Rebid</button>
                  @else
                  <button type="button" class="btn btn-sm btn btn-sm btn-danger review_btn" >Review</button>
                  @endif
                  <button type="button" class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                  <button type="submit" class="btn btn btn-sm btn-primary">Confirm</button>
                </div>
                @endif
              </form>
              @if($activity_status->proceed_notice=='finished')
              <div class="form-group col-xs-12 col-sm-12 col-md-12 mx-auto d-flex justify-content-center">
                <button class="btn btn-sm btn btn-sm btn-info" onclick="stepper1.previous()">Previous</button>
                <button class="btn btn-sm btn btn-sm btn-success ml-2">Completed</button>
              </div>
              @endif
            </div>


          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('custom-scripts')
<script>

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
  orientation:'right',
  autoclose: true,
  language: 'da',
  enableOnReadonly: false,
  endDate:'{{date("Y")}}'
});


$('.timepicker').timepicker({
  showRightIcon: false,
  showOnFocus: true,
});


$(window).keydown(function(event){
  if(event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});

$('.rebid_btn').click(function functionName() {
  Swal.fire({
    title:"Rebid Project",
    text: 'Are you sure to Rebid Project?',
    showCancelButton: true,
    cancelButtonText: "No",
    confirmButtonText: 'Yes',
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#rebid_plan_id").val("{{$project_plans[0]->plan_id}}");
      $("#rebid_project_title").val("{{$project_plans[0]->project_title}}");
      $("#rebid_modal").modal('show');
    }
  });


});


$('.review_btn').click(function functionName() {
  Swal.fire({
    title:"Set Project for Review",
    text: 'Are you sure to Set Project for Review?',
    showCancelButton: true,
    cancelButtonText: "No",
    confirmButtonText: 'Yes',
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#review_plan_id").val("{{$project_plans[0]->plan_id}}");
      $("#review_project_title").val("{{$project_plans[0]->project_title}}");
      $("#review_modal").modal('show');
    }
  });
});

var stepper1 = new Stepper(document.querySelector('#stepper1'));
stepper1.to("{{$current_tab}}");


if("{{session('message')}}"){
  if("{{session('message')}}"=="date_error"){
    swal.fire({
      title: 'Preprocurement Date Error',
      text: 'Preprocurement Should be atleast 7 days before the advertisement.',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="range_error"){

    swal.fire({
      title: 'Date Error',
      text: 'Date should be equal or in between the given date range. ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: 'Success',
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else{

  }
}
</script>
@endpush
