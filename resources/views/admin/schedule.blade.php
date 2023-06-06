@extends('layouts.app')
<style>
.card .table td, .card .table th {
  padding-right: 0.5rem !important;
  padding-left: 0.5rem !important;
}
</style>
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="">
    <div class="col-sm-12">
      <div class="modal" tabindex="-1" role="dialog" id="schedule_modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="schedule_modal_title">Modal title</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body pt-0">
              <form class="col-sm-12" method="POST" id="submit_schedule" action="submit_schedule">
                @csrf
                @if($mode===1)
                <div class="row">
                  <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2  mb-0">
                    <h5 for="pre_procurement">Pre-Procurement</h5>
                    <input type="text" id="pre_procurement" name="pre_procurement" class="form-control form-control-sm datepicker" value="{{old('pre_procurement')}}" >
                    <label class="error-msg text-red" >@error('pre_procurement'){{$message}}@enderror
                    </label>
                  </div>
                </div>

                @endif
                <div class="row">
                  <div class="form-group col-xs-3 col-sm-3 col-lg-3 mx-auto d-none">
                    <label for="">Plan Id/s
                    </label>
                    <input type="text" id="plan_ids" name="plan_ids" class="form-control form-control-sm" value="{{old('plan_ids')}}" >
                    <label class="error-msg text-red" >@error('plan_ids'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2  mb-0">
                    <h5 for="begin_date">Date to Begin With:</h5>
                    <input type="text" id="begin_date" name="begin_date" class="form-control form-control-sm" value="{{old('begin_date')}}" >
                    <label class="error-msg text-red" >@error('begin_date'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm- col-md-4 mt-sm-4 mt-xs-0  align-items-center">
                    <button type="button" id="compute-btn" class="btn btn-sm btn-info btn btn-sm align-middle">Compute /Reset to Earliest Time</button>
                  </div>
                  <div class="form-group col-xs-12 col-sm- col-md-2 mt-sm-4 mt-xs-0  align-items-center">
                    <button type="button" id="start-over-btn" class="btn btn-smbg-white border btn btn-sm align-middle">Start Over</button>
                  </div>
                  <div class="form-group col-xs-12 col-sm- col-md-2 mt-sm- mt-xs-0   align-items-center d-none">
                    <button type="button" id="repopulate-btn" class="btn btn-smbg-white border btn btn-sm align-middle">Repopulate Dates</button>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2 mb-2">
                    <h5 for="">ADS/Post:</h5>
                  </div>
                  <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 mb-2">
                    <input type="text" id="ads-post-start" name="ads-post-start" placeholder="start date" class="form-control form-control-sm datepicker bg-white" readonly value="{{old('ads-post-start')}}">
                    <label class="error-msg text-red" >@error('ads-post-start'){{$message}}@enderror
                    </label>
                  </div>
                  <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 mb-2">
                    <input type="text" id="ads-post-end" name="ads-post-end" placeholder="end date" class="form-control form-control-sm datepicker"  value="{{old('ads-post-end')}}" >
                    <label class="error-msg text-red" >@error('ads-post-end'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                    <input type="number" id="ads-post-days" name="ads-post-days" placeholder="days" class="form-control form-control-sm auto_update" value="{{old('ads-post-days')}}" >
                  </div>
                  <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                    <button type="button" class="btn btn-sm btn btn-sm btn-success align-middle update_trigger" id="ads-update" name="ads-update">Update</button>
                  </div>
                </div>

                <div class="row @if($mode==2) d-none
                @endif">
                <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2">
                  <h5 for="">Prebid Conference:</h5>

                  <div class="form-check form-check-inline  d-none ">
                    <input class="form-check-input " type="radio" id="pre-bid-no" name="pre-bid-radio" value="No" @if($mode==2) checked
                    @endif>
                    <h5 class="form-check-label" for="pre-bid-no">No</h5>
                  </div>
                  <div class="form-check form-check-inline d-none">
                    <input class="form-check-input " type="radio" id="pre-bid-yes" name="pre-bid-radio" @if($mode==1) checked
                    @endif value="Yes">
                    <h5 class="form-check-label" for="pre-bid-yes">Yes</h5>
                  </div>

                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2  ">
                  <input type="text" id="pre-bid-start" name="pre-bid-start" placeholder="start date" class="form-control align-middle form-control-sm datepicker @if($mode==1) bg-white
                  @endif" readonly value="{{old('pre-bid-start')}}" >
                  <label class="error-msg text-red" >@error('pre-bid-start'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2  ">
                  <input type="text" id="pre-bid-end" name="pre-bid-end" placeholder="end date" class="form-control align-middle form-control-sm datepicker @if($mode==1) bg-white
                  @endif" @if($mode==2) readonly
                  @endif   value="{{old('pre-bid-end')}}" >
                  <label class="error-msg text-red" >@error('pre-bid-end'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2  ">
                  <input type="number" id="pre-bid-days" name="pre-bid-days" placeholder="days" class="form-control align-middle form-control-sm auto_update" @if($mode==2) readonly
                  @endif value="{{old('pre-bid-days')}}" >
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2  ">
                  <button type="button" class="btn btn-sm btn btn-sm btn-success align-middle update_trigger" name="pre-bid-update"  id="pre-bid-update">Update</button>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2">
                  <h5 for="">Submission of Bid:</h5>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="sub-of-bid-start" name="sub-of-bid-start" placeholder="start date" class="form-control form-control-sm datepicker bg-white" readonly value="{{old('sub-of-bid-start')}}" >
                  <label class="error-msg text-red" >@error('sub-of-bid-start'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="sub-of-bid-end" name="sub-of-bid-end" placeholder="end date" class="form-control form-control-sm datepicker bg-white"  value="{{old('sub-of-bid-end')}}" >
                  <label class="error-msg text-red" >@error('sub-of-bid-end'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="number" id="sub-of-bid-days" name="sub-of-bid-days" placeholder="days" class="form-control form-control-sm auto_update" value="{{old('sub-of-bid-days')}}" >
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <button type="button" class="btn btn-sm btn btn-sm btn-success align-middle update_trigger" name="sub-of-bid-days" id="sub-of-bid-update">Update</button>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2">
                  <h5 for="">Bid Evaluation:</h5>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="bid-eval-start" name="bid-eval-start" placeholder="start date" class="form-control form-control-sm datepicker bg-white" readonly value="{{old('bid-eval-start')}}" >
                  <label class="error-msg text-red" >@error('bid-eval-start'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="bid-eval-end" name="bid-eval-end" placeholder="end date" class="form-control form-control-sm datepicker bg-white"  value="{{old('bid-eval-end')}}" >
                  <label class="error-msg text-red" >@error('bid-eval-end'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="number" id="bid-eval-days" name="bid-eval-days" placeholder="days" class="form-control form-control-sm auto_update" readonly="readonly" value="0">
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <button type="button" class="btn btn-sm btn btn-sm btn-success align-middle update_trigger" id="bid-eval-update" name="bid-eval-update">Update</button>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2">
                  <h5 class="label" for="">Post Qualification:</h5>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="post-qual-start" name="post-qual-start" placeholder="start date" class="form-control form-control-sm datepicker bg-white" readonly value="{{old('post-qual-start')}}" >
                  <label class="error-msg text-red" >@error('post-qual-start'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="post-qual-end" name="post-qual-end" placeholder="end date" class="form-control form-control-sm datepicker bg-white"  value="{{old('post-qual-end')}}" >
                  <label class="error-msg text-red" >@error('post-qual-end'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="number" id="post-qual-days" name="post-qual-days" placeholder="days" class="form-control form-control-sm auto_update" value="{{old('post-qual-days')}}" >
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <button type="button" name="post-qual-update" class="btn btn-sm btn btn-sm btn-success align-middle update_trigger" id="post-qual-update">Update</button>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2">
                  <h5 for="">Issuance of Notice of Awards:</h5>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="iss-of-noa-start" name="iss-of-noa-start" placeholder="start date" class="form-control form-control-sm datepicker bg-white" readonly value="{{old('iss-of-noa-start')}}" >
                  <label class="error-msg text-red" >@error('iss-of-noa-start'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="iss-of-noa-end" name="iss-of-noa-end" placeholder="end date" class="form-control form-control-sm datepicker bg-white"  value="{{old('iss-of-noa-end')}}" >
                  <label class="error-msg text-red" >@error('iss-of-noa-end'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="number" id="iss-of-noa-days" name="iss-of-noa-days" placeholder="days" class="form-control form-control-sm auto_update" value="{{old('iss-of-noa-days')}}" >
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <button type="button" class="btn btn-sm btn btn-sm btn-success align-middle update_trigger" name="iss-of-noa-update" id="iss-of-noa-update">Update</button>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2">
                  <h5 for="">Contract Preparation and Signing:</h5>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="contract-prep-start" name="contract-prep-start" placeholder="start date" class="form-control form-control-sm datepicker bg-white" readonly value="{{old('contract-prep-start')}}" >
                  <label class="error-msg text-red" >@error('contract-prep-start'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="contract-prep-end" name="contract-prep-end" placeholder="end date" class="form-control form-control-sm datepicker bg-white"  value="{{old('contract-prep-end')}}" >
                  <label class="error-msg text-red" >@error('contract-prep-end'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="number" id="contract-prep-days" name="contract-prep-days" placeholder="days" class="form-control form-control-sm auto_update" value="{{old('acontract-prep-days')}}" >
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <button type="button" class="btn btn-sm btn btn-sm btn-success align-middle update_trigger" name="contract-prep-update" id="contract-prep-update">Update</button>
                </div>
              </div>

              <div class="row d-none">
                <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2">
                  <h5 for="">Approval by Higher Authority:</h5>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="app-by-ha-no" name="app-by-ha-radio" value="No" checked>
                    <h5 class="form-check-label" for="app-by-ha-no">No</h5>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="app-by-ha-yes" name="app-by-ha-radio"  value="Yes">
                    <h5 class="form-check-label" for="app-by-ha-yes">Yes</h5>
                  </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="app-by-ha-start" name="app-by-ha-start" placeholder="start date" class="form-control form-control-sm datepicker bg-white " readonly value="{{old('app-by-ha-start')}}" >
                  <label class="error-msg text-red" >@error('app-by-ha-start'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="app-by-ha-end" name="app-by-ha-end" placeholder="end date" class="form-control form-control-sm datepicker bg-white " readonly value="{{old('app-by-ha-end')}}" >
                  <label class="error-msg text-red" >@error('app-by-ha-end'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="number" id="app-by-ha-days" name="app-by-ha-days" placeholder="days" class="form-control form-control-sm auto_update" value="{{old('app-by-ha-days')}}" >
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <button type="button" class="btn btn-sm btn btn-sm btn-success align-middle update_trigger" name="app-by-ha-update" id="app-by-ha-update">Update</button>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-xs-12 col-sm-4 col-md-4 mb-2">
                  <h5 for="">Notice to Proceed:</h5>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="ntp-start" name="ntp-start" placeholder="start date" class="form-control form-control-sm datepicker bg-white" readonly value="{{old('ntp-start')}}" >
                  <label class="error-msg text-red" >@error('ntp-start'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="text" id="ntp-end" name="ntp-end" placeholder="end date" class="form-control form-control-sm datepicker bg-white"  value="{{old('ntp-end')}}" >
                  <label class="error-msg text-red" >@error('ntp-end'){{$message}}@enderror
                  </label>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <input type="number" id="ntp-days" name="ntp-days" placeholder="days" class="form-control form-control-sm auto_update" value="{{old('ntp-days')}}" >
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 mb-2 ">
                  <button type="button" class="btn btn-sm btn btn-sm btn-success align-middle update_trigger" name="ntp-update" id="ntp-update">Update</button>
                </div>
              </div>

              <div class="d-flex justify-content-center col-sm-12">
                <button id="submit_btn" type="button" class="btn btn-sm btn-primary text-center" disabled>Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card shadow">
    <div class="card shadow border-0">
      <div class="card-header">
        <h2 id="title">{{$title}}</h2>
      </div>
      <div class="card-body">
        <div class="col-sm-12" id="filter">
          <form class="row" id="app_filter" method="post" action="{{route('filter_schedule')}}">
            @csrf

            <!-- Mode -->
            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
              <label for="project_year" class="input-sm">Mode
              </label>
              <input  class="form-control form-control-sm" id="mode" name="mode" value="{{old('mode')}}" >
              <label class="error-msg text-red" >@error('mode'){{$message}}@enderror
              </label>
            </div>


            <!-- project year -->
            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
              <label for="project_year" class="input-sm">Project Year
              </label>
              <input  class="form-control form-control-sm yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}" >
              <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror
              </label>
            </div>

            {{-- with schedule --}}
            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
              <label for="schedule_status" class="input-sm">Status
              </label>
              <select  class="form-control form-control-sm" id="schedule_status" name="schedule_status" >
                <option value="0"  {{ old('schedule_status') == '0' ? 'selected' : ''}} >All</option>
                <option value="1"  {{ old('schedule_status') == '1' ? 'selected' : ''}} >With Schedule</option>
                <option value="2"  {{ old('schedule_status') == '2' ? 'selected' : ''}} >Without Schedule</option>
              </select>
              <label class="error-msg text-red" >@error('schedule_status'){{$message}}@enderror
              </label>
            </div>

            <!-- Month added -->
            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
              <label for="month_added">Month Added
              </label>
              <input type="text" id="month_added" name="month_added" class="form-control form-control-sm monthpicker" value="{{old('month_added')}}" >
              <label class="error-msg text-red" >@error('month_added'){{$message}}@enderror
              </label>
            </div>

            <!-- date added -->
            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
              <label for="date_added">Date Added
              </label>
              <input type="text" id="date_added" name="date_added" class="form-control form-control-sm" value="{{old('date_added')}}" >
              <label class="error-msg text-red" >@error('date_added'){{$message}}@enderror
              </label>
            </div>

          </form>
        </div>
        <div class="col-sm-12 d-none">
          <form class="row" id="cluster_form" method="post" action="{{route('submit_cluster')}}">
            @csrf
            <div class="form-group col-xs-3 col-sm-3 col-lg-3 mx-auto d-none">
              <label for="">Plan Id/s
              </label>
              <input type="text" id="cluster_ids" name="cluster_ids" class="form-control form-control-sm" value="{{old('cluster_ids')}}" >
              <label class="error-msg text-red" >@error('cluster_ids'){{$message}}@enderror
              </label>
            </div>
            <div class="form-group col-xs-3 col-sm-3 col-lg-3 mx-auto d-none">
                <label for="">One Title
                </label>
                <input type="text" id="one_title" name="one_title" class="form-control form-control-sm" value="{{old('one_title')}}">
                <label class="error-msg text-red">@error('one_title'){{$message}}@enderror
                </label>
            </div>

            <!-- Mode -->
            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
              <label for="" class="input-sm">Mode
              </label>
              <input  class="form-control form-control-sm" id="cluster_mode" name="cluster_mode" value="{{old('cluster_mode')}}" >
              <label class="error-msg text-red" >@error('cluster_mode'){{$message}}@enderror
              </label>
            </div>
            <!--Cluster -->
            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
              <label for="" class="input-sm">Cluster Process
              </label>
              <input  class="form-control form-control-sm" id="cluster_process" name="cluster_process" value="{{old('cluster_process')}}" >
              <label class="error-msg text-red" >@error('cluster_process'){{$message}}@enderror
              </label>
            </div>
          </form>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered" id="app_table">
            <thead class="">
              <tr class="bg-primary text-white" >
                <th class="text-center"></th>
                <th class="text-center">ID</th>
                <th class="text-center">Project#</th>
                <th class="text-center">Project Title</th>
                <th class="text-center">Type</th>
                <th class="text-center">Location</th>
                <th class="text-center">Group#</th>
                <th class="text-center">Cluster</th>
                <th class="text-center">Pre <br/> Procurement</th>
                <th class="text-center">Ads/Posting</th>
                <th class="text-center">Pre-bid</th>
                <th class="text-center">Opening</th>
                <th class="text-center">Bid Evaluation</th>
                <th class="text-center">Post Qualification</th>
                <th class="text-center">Issuance of NOA</th>
                <th class="text-center">Contract Signing</th>
                <th class="text-center">Approval by Higher Authority</th>
                <th class="text-center">Notice to Proceed</th>
                <th class="text-center">Source of Fund</th>
                <th class="text-center">Project Cost</th>
                <th class="text-center">Project Year</th>
                <th class="text-center">Fund Year</th>
                <th class="text-center">Mode of Procurement</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
            </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

@endsection

@push('custom-scripts')
<script>

$(window).keydown(function(event){
  if(event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});

// table data
let data= @json(session('newData'));

if(data==null){
  data= @json($project_plans);
}
// mode
$("#mode").val('{{$mode}}');
$("#cluster_mode").val('{{$mode}}');

// datatables
$('#app_table thead tr').clone(true).appendTo( '#app_table thead' );
$('#app_table thead tr:eq(1)').removeClass('bg-primary');

$("#date_added").datepicker({
  format: 'mm/dd/yyyy',
  endDate:'{{$year}}'
});

$("#begin_date").datepicker({
  format: 'mm/dd/yyyy',
});


$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
  autoclose: true,
  language: 'da',
  enableOnReadonly: false
});


$(".yearpicker").datepicker({
  format: 'yyyy',
  viewMode: "years",
  minViewMode: "years"
});

$(".monthpicker").datepicker({
  format: 'mm-yyyy',
  startView: 'months',
  minViewMode: 'months',
});

var table=  $('#app_table').DataTable({
  dom: 'Bfrtip',
  buttons: [
    {
      text: 'Hide Filter',
      attr: {
        id: 'show_filter'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-dark text-white',
      action: function ( e, dt, node, config ) {
        if(config.text=="Show Filter"){
          $('#filter').removeClass('d-none');
          $('#filter_btn').removeClass('d-none');
          config.text="Hide Filter";
          $("#show_filter").html("Hide Filter");
        }
        else{
          $('#filter').addClass('d-none');
          $('#filter_btn').addClass('d-none');
          config.text="Show Filter";
          $("#show_filter").html("Show Filter");
        }
      }
    },
    {
      text: 'Filter',
      attr: {
        id: 'filter_btn'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-warning text-white'
    },
    @if(in_array('add',$user_privilege))
    {
      text: 'Add Schedules',
      attr: {
        id: 'add_schedules'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-primary text-white',
      action: function ( e, dt, node, config ) {
        if(table.rows( { selected: true } ).count()==0){
          Swal.fire({
            title:"Warning",
            text: 'Please select rows to add Schedule ',
            confirmButtonText: 'Ok',
            icon: 'info'
          });
        }
        else{
          $("#submit_schedule")[0].reset();
          let rows = table.rows( { selected: true } );
          if(table.rows( { selected: true } ).count()>1){
            var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray()
            $("#plan_ids").val(plan_ids.toString());
            $("#schedule_modal").modal('show');
          }
          else{
            swal.fire({
              title: `Error`,
              text: 'Please select rows to Add schedule',
              icon: 'warning'
            });
          }
        }
      }
    },
    @endif
    @if(in_array('cluster',$user_privilege))
    {
      text: 'Cluster',
      attr: {
        id: 'add_cluster'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-yellow text-white',
      action: function ( e, dt, node, config ) {
        $("#cluster_process").val("add_cluster");
        if(table.rows( { selected: true } ).count()<=1){
          Swal.fire({
            title:"Warning",
            text: 'Please select rows to Cluster ',
            confirmButtonText: 'Ok',
            icon: 'info'
          });
        }
        else{
          let rows = table.rows( { selected: true } );
          var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
          var plan_number =  table.cells( rows.nodes(), 3 ).data().toArray();

          Swal.fire({
            text: 'Are you sure to Cluster Plans '+plan_number.toString()+'?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: "No",
            icon: 'warning'
          }).then((result) => {
            if(result.isConfirmed){
              $("#cluster_ids").val(plan_ids.toString());
              $("#one_title").val('');
              $("#cluster_form").submit();
            }
          });
        }
      }
    },
     {
      text: 'Cluster w/ One Project Title',
      attr: {
        id: 'add_cluster_two'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-yellow text-white',
      action: function ( e, dt, node, config ) {
        $("#cluster_process").val("add_cluster");
        if(table.rows( { selected: true } ).count()<=1){
          Swal.fire({
            title:"Warning",
            text: 'Please select rows to Cluster ',
            confirmButtonText: 'Ok',
            icon: 'info'
          });
        }
        else{
          let rows = table.rows( { selected: true } );
          var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
          var plan_number =  table.cells( rows.nodes(), 3 ).data().toArray();

          Swal.fire({
            text: 'Are you sure to Cluster Plans '+plan_number.toString()+'?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: "No",
            icon: 'warning'
          }).then((result) => {
            if(result.isConfirmed){
              $("#cluster_ids").val(plan_ids.toString());
              $("#one_title").val('1');
              $("#cluster_form").submit();
            }
          });
        }
      }
    },
    {
      text: 'Uncluster',
      attr: {
        id: 'add_cluster'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-danger text-white',
      action: function ( e, dt, node, config ) {
        $("#cluster_process").val("uncluster");
        if(table.rows( { selected: true } ).count()==0){
          Swal.fire({
            title:"Warning",
            text: 'Please select rows to Uncluster ',
            confirmButtonText: 'Ok',
            icon: 'info'
          });
        }
        else{
          let rows = table.rows( { selected: true } );
          var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
          var plan_number =  table.cells( rows.nodes(), 3 ).data().toArray();

          Swal.fire({
            text: 'Are you sure to Uncluster Plan/s '+plan_number.toString()+'?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: "No",
            icon: 'warning'
          }).then((result) => {
            if(result.isConfirmed){
              $("#cluster_ids").val(plan_ids.toString());
              $("#one_title").val('');
              $("#cluster_form").submit();
            }
          });
        }
      }
    },
    @endif
    {
      text: 'Excel',
      extend: 'excel',
      className: 'btn btn-sm shadow-0 border-0 bg-success text-white'
    },
    {
      text: 'Print',
      extend: 'print',
      className: 'btn btn-sm shadow-0 border-0 bg-info text-white'
    },
    @if(in_array('cancel',$user_privilege))
    {
      text: 'Cancel Schedules',
      attr: {
        id: 'cancel_btn'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-white',
      action: function ( e, dt, node, config ) {
        let rows = table.rows( { selected: true } );
        var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
        if(plan_ids.length==0){
          swal.fire({
            title: `Select error`,
            text: 'Please select schedules to be cancelled.',
            icon: 'error'
          });
        }
        else{
          Swal.fire({
            title: 'Cancellation Remarks',
            icon: 'info',
            html:'<input id="cancellation_remarks" class="form-control">',
            backdrop:true,
            showCancelButton: true,
            confirmButtonText: 'Submit',
            showLoaderOnConfirm: true,
            preConfirm: (login,inputValue ) => {
              if($("#cancellation_remarks").val()==null || $("#cancellation_remarks").val()==""){
                swal.fire({
                  title: `Remarks Error`,
                  text: 'Remarks is required.',
                  icon: 'error'
                });
              }
              else{
                $.ajax({
                  type: 'POST',
                  url: "{{route('cancel_schedule')}}",
                  data: {
                    "_token": "{{ csrf_token() }}",
                    "plan_ids":plan_ids,
                    "remarks":$("#cancellation_remarks").val()
                  },
                  success: function (data) {
                    console.log(data);
                    if(data.message=="success"){
                      swal.fire({
                        title: `Success`,
                        text: 'Schedules were Cancelled',
                        icon: 'success'
                      });
                      location.reload();

                    }
                    else if(data.message=="with_bidder"){
                      swal.fire({
                        title: `Cancellation Error`,
                        text: 'Selected plan/s have responsive bidder',
                        icon: 'error'
                      });
                    }
                    else if(data.message=="pending_schedule"){
                      swal.fire({
                        title: `Cancellation Error`,
                        text: 'Selected plan/s are unscheduled',
                        icon: 'error'
                      });
                    }
                    else{
                      swal.fire({
                        title: `Cancellation Error`,
                        text: 'Request Failed',
                        icon: 'error'
                      });
                    }
                  }
                });
              }

            },
            allowOutsideClick: () => !Swal.isLoading()
          }).then((result) => {
          })
        }
      }
    },
    {
      text: 'Defer Schedules',
      attr: {
        id: 'defer_btn'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-white',
      action: function ( e, dt, node, config ) {
        let rows = table.rows( { selected: true } );
        var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
        if(plan_ids.length==0){
          swal.fire({
            title: `Select error`,
            text: 'Please select schedules to be Deferred.',
            icon: 'error'
          });
        }
        else{
          Swal.fire({
            title: 'Deferred Remarks',
            icon: 'info',
            html:'<input id="defer_remarks" class="form-control">',
            backdrop:true,
            showCancelButton: true,
            confirmButtonText: 'Submit',
            showLoaderOnConfirm: true,
            preConfirm: (login,inputValue ) => {
              if($("#defer_remarks").val()==null || $("#defer_remarks").val()==""){
                swal.fire({
                  title: `Remarks Error`,
                  text: 'Remarks is required.',
                  icon: 'error'
                });
              }
              else{
                $.ajax({
                  type: 'POST',
                  url: "{{route('defer_schedule')}}",
                  data: {
                    "_token": "{{ csrf_token() }}",
                    "plan_ids":plan_ids,
                    "remarks":$("#defer_remarks").val()
                  },
                  success: function (data) {
                    console.log(data);
                    if(data.message=="success"){
                      swal.fire({
                        title: `Success`,
                        text: 'Schedules were Deferred',
                        icon: 'success'
                      });
                      location.reload();
                    }
                    else{
                      swal.fire({
                        title: `Defer Error`,
                        text: 'Request Failed',
                        icon: 'error'
                      });
                    }
                  }
                });
              }

            },
            allowOutsideClick: () => !Swal.isLoading()
          }).then((result) => {
          })
        }
      }
    }
    @endif
  ],
  data:data,
  columns:[
    {"data":"advertisement_start",render: function ( data, type, row ) {
      let cluster="";
      if(row.current_cluster!=null){
        cluster='<br/><span class="badge bg-yellow  mt-2">Cluster '+row.current_cluster+'</span>';
      }
      if(data!=null){
        return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button class="btn btn-sm shadow-0 border-0 btn-success edit_schedule" data-toggle="tooltip" data-placement="top" title="Edit" value="'+row.plan_id+'"><i class="ni ni-ruler-pencil"></i></button>@endif @if(in_array("delete",$user_privilege))<button value="'+row.plan_id+'" data-toggle="tooltip" data-placement="top" title="Delete" class="btn btn-sm shadow-0 border-0 btn-danger delete_btn"><i class="ni ni-basket text-white"></i></button>@endif'+cluster+'</div>';
      }
      else{
        return  '@if(in_array("add",$user_privilege))<div style="white-space: nowrap"><button  class="btn btn-sm shadow-0 border-0 btn-primary text-white add_schedule" data-toggle="tooltip" data-placement="top" title="Add Schedule"  value="'+row.plan_id+'"><i class="ni ni-fat-add text-white"></i></button>'+cluster+'</div>@endif';
      }
    }},
    {"data":"plan_id"},
    {"data":"project_no"},
    {"data":"project_title"},
    {"data":"project_type"},
    {"data":"municipality_name"},
    {"data":"app_group_no"},
    {"data":"current_cluster"},
    {"data":"pre_proc_date"},
    {"data":"fullAdvertisement"},
    {"data":"fullPrebid"},
    {"data":"fullOpening"},
    {"data":"fullBidEvalutation"},
    {"data":"fullPostQualification"},
    {"data":"fullNOA"},
    {"data":"fullContract"},
    {"data":"fullApproval"},
    {"data":"fullNTP"},
    {"data":"source"},
    {"data":"project_cost",render: function ( data, type, row ) {
      if(data!=null){
        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }
      return "";
    }},
    {"data":"project_year"},
    {"data":"year_funded"},
    {"data":"mode"},
    {"data":"project_status"},
    // {"data":"pre_proc"}
  ],
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  orderCellsTop: true,
  select: {
    style: 'multi',
    selector: 'td:not(:first-child)'
  },
  responsive:true,
  columnDefs: [ {
    targets: 0,
    orderable: false
  },
  {
    targets: [1,6,7,20,21,22,23],
    visible: false
  }],
  order: [[ 2, "asc" ]],
  // rowGroup: {
  //
  //   startRender: function ( rows, group ) {
  //     if(group==""||group==null){
  //       var group_title="Non-Clustered Project";
  //     }
  //     else{
  //       var group_title="Cluster "+group;
  //     }
  //     return group_title;
  //   },
  //   endRender: null,
  //   dataSrc: 7
  // }
});

// show inputs/messages on load
var oldInputs='{{ count(session()->getOldInput()) }}';

if(oldInputs>=5){
  $('#filter').removeClass('d-none');
  $('#filter_btn').removeClass('d-none');
  $("#show_filter").html("Hide Filter");
}
else{
  $("#project_year").val("{{$year}}");
}

$("#schedule_status").change(function () {
  $("#app_filter").submit();
});


$("#project_year").change(function () {
  $("#app_filter").submit();
});


if("{{session('message')}}"){
  if("{{session('message')}}"=="delete_error"){
    swal.fire({
      title: `Error`,
      text: 'You cannot delete Schedule for Ongoing Projects or With Project Bidders',


      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',

      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="delete_success"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Successfully deleted Schedule',
      confirmButtonText: 'Ok',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="cluster_success"){
    Swal.fire({
      title: `Cluster Success`,
      text: 'Successfully Clustered Plans',
      confirmButtonText: 'Ok',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="uncluster_success"){
    Swal.fire({
      title: `Uncluster Success`,
      text: 'Successfully Unclustered Plans',
      confirmButtonText: 'Ok',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="multiple status"){
    Swal.fire({
      title: `Error`,
      text: 'Sorry! Some Selected rows have already defined schedule.',
      confirmButtonText: 'Ok',
      icon: 'error'
    });
  }
  else if("{{session('message')}}"=="set status"){
    Swal.fire({
      title: `Error`,
      text: 'Sorry! Selected rows have already defined schedules.',
      confirmButtonText: 'Ok',
      icon: 'error'
    });
  }
  else if("{{session('message')}}"=="edit_error"){
    Swal.fire({
      title: `Edit Error`,
      text: "{{session('specific_message')}}",
      confirmButtonText: 'Ok',
      icon: 'error'
    });
  }

  else if("{{session('message')}}"=="already_clustered"){
    Swal.fire({
      title: `Edit Error`,
      text: "Sorry, rows have pre-defined clusters",
      confirmButtonText: 'Ok',
      icon: 'error'
    });
  }

  else{
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact your system developer',


      icon: 'error'
    });
  }
}

// functions
function fillForm() {
  if($("#begin_date").val()!=""){
    var ads_post_days=7;
    var pre_bid_days=1;
    var sub_of_bid_days=1;
    var bid_eval_days=0;
    var post_qual_days=12;
    var iss_of_noa_days=1;
    var contract_prep_days=1;
    var app_by_ha_days=1;
    var ntp_days=1;


    // advertisement
    if($("#ads-post-days").val()!=""){
      if(parseInt($("#ads-post-days").val())<1){
        ads_post_days=7;
        $("#ads-post-days").val(ads_post_days);
      }
      else{
        ads_post_days=$("#ads-post-days").val();
      }
    }
    else{
      $("#ads-post-days").val(ads_post_days);
    }

    $("#ads-post-start").datepicker("update",moment($("#begin_date").val(),"MM/DD/YYYY").format("MM/DD/YYYY"));
    $("#ads-post-end").datepicker("update",moment($("#begin_date").val(),"MM/DD/YYYY").add(ads_post_days, 'day').format("MM/DD/YYYY"));

    var date=moment($("#ads-post-end").val(),"MM/DD/YYYY");

    // prebid
    if($("#pre-bid-yes").prop('checked')==true){
      if(parseInt($("#pre-bid-days").val())>53)
      {
        $("#pre-bid-days").val(pre_bid_days);
        swal.fire({
          title: `Prebid Error`,
          text: 'Pre-bid Conference should not exceed 53 calendar days.',
          icon: 'warning'
        });
      }
      else if(parseInt($("#pre-bid-days").val())>=0){
        pre_bid_days=parseInt($("#pre-bid-days").val());
      }
      else{
        $("#pre-bid-days").val(pre_bid_days);
      }

      date.add(pre_bid_days, 'days');
      $pre_bid_weekend_adjust= date;
      if(date.format("e")==6){
        $pre_bid_weekend_adjust.subtract(1,"days");
      }
      if(date.format("e")==7){
        $pre_bid_weekend_adjust.subtract(2,"days");
      }

      $("#pre-bid-start").datepicker("update",$pre_bid_weekend_adjust.format("MM/DD/YYYY"));
      $("#pre-bid-end").off('change');
      $("#pre-bid-end").datepicker("update",$pre_bid_weekend_adjust.format("MM/DD/YYYY"));
      $("#pre-bid-end").change(function () {
        prebidEndAdjust();
      });

    }


    // submission of bid

    if($("#pre-bid-yes").prop('checked')==true){
      sub_of_bid_days=10;
      if(parseInt($("#sub-of-bid-days").val())>65)
      {
        $("#sub-of-bid-days").val(sub_of_bid_days);
        swal.fire({
          title: `Opening Error`,
          text: 'Bids cannot be submitted beyond the 65th day from the last day of Advertisement',
          icon: 'warning'
        });
      }
      else if(parseInt($("#sub-of-bid-days").val())<0){
        sub_of_bid_days=1;
        $("#sub-of-bid-days").val(sub_of_bid_days);
        swal.fire({
          title: `Opening Error`,
          text: 'Allowed input days must be equal or more than 1 day.',
          icon: 'warning'
        });
      }
      else if(parseInt($("#sub-of-bid-days").val())>=10 || parseInt($("#sub-of-bid-days").val())<=65){
        sub_of_bid_days=parseInt($("#sub-of-bid-days").val());
        sub_of_bid_days=sub_of_bid_days;
      }
      else{
        $("#sub-of-bid-days").val(sub_of_bid_days);
      }
    }
    else{
      sub_of_bid_days=1;
      if(parseInt($("#sub-of-bid-days").val())>65)
      {
        $("#sub-of-bid-days").val(sub_of_bid_days);
        swal.fire({
          title: `Opening Error`,
          text: 'Bids cannot be submitted beyond the 65th day from the last day of Advertisement',
          icon: 'warning'
        });
      }
      else if(parseInt($("#sub-of-bid-days").val())>1){
        sub_of_bid_days=parseInt($("#sub-of-bid-days").val());
        sub_of_bid_days=sub_of_bid_days;
      }
      else if(parseInt($("#sub-of-bid-days").val())==0){
        sub_of_bid_days=0;
        $("#sub-of-bid-days").val(sub_of_bid_days);
      }
      else if(parseInt($("#sub-of-bid-days").val())<1){
        sub_of_bid_days=1;
        $("#sub-of-bid-days").val(sub_of_bid_days);
        swal.fire({
          title: `Opening Error`,
          text: 'Allowed input days must be equal or more than 1 day.',
          icon: 'warning'
        });
      }
      else{
        $("#sub-of-bid-days").val(sub_of_bid_days);
      }
    }
    $("#sub-of-bid-start").datepicker("update",date.add(sub_of_bid_days, 'day').format("MM/DD/YYYY"));
    $("#sub-of-bid-end").off('change');
    $("#sub-of-bid-end").datepicker("update",date.format("MM/DD/YYYY"));
    $("#sub-of-bid-end").change(function () {
      submissionEndAdjust();
    });

    // bid evaluation
    if(parseInt($("#bid-eval-days").val())>7)
    {
      $("#bid-eval-days").val('');
      swal.fire({
        title: `Bid Evaluation Error`,
        text: 'Evaluation of bids should not exceed 7 days.',
        icon: 'warning'
      });
    }
    else if(parseInt($("#bid-eval-days").val())>=1){
      bid_eval_days=parseInt($("#bid-eval-days").val());
    }
    else if($("#bid-eval-days").val()=="0"){
      bid_eval_days=0;
      $("#bid-eval-days").val(bid_eval_days);
    }
    else if(parseInt($("#bid-eval-days").val())<0){
      $("#bid-eval-days").val(bid_eval_days);
      swal.fire({
        title: `Bid Evaluation Error`,
        text: 'Allowed input days must be atleast 1 day.',
        icon: 'warning'
      });
    }
    else{
      $("#bid-eval-days").val(bid_eval_days);
    }

    if(parseInt($("#bid-eval-days").val())==0){
      $("#bid-eval-start").datepicker("update",moment($("#sub-of-bid-end").val(),"MM/DD/YYYY").format("MM/DD/YYYY"));
    }
    else{
      $("#bid-eval-start").datepicker("update",moment($("#sub-of-bid-end").val(),"MM/DD/YYYY").add(bid_eval_days, 'day').format("MM/DD/YYYY"));
    }

    $("#bid-eval-end").off('change')
    $("#bid-eval-end").datepicker("update",date.add(bid_eval_days, 'day').format("MM/DD/YYYY"));
    $("#bid-eval-end").change(function () {
      bidEvaluationAdjust();
    });



    // post qualification
    // if(parseInt($("#post-qual-days").val())>45)
    // {
    //   $("#post-qual-days").val('');
    //   swal.fire({
    //     title: `Error`,
    //     text: 'Post-qualification cannot exceed 45 days.',
    //     icon: 'warning'
    //   });
    // }
    // else
    if(parseInt($("#post-qual-days").val())>1){
      post_qual_days=parseInt($("#post-qual-days").val());
    }
    else if(parseInt($("#post-qual-days").val())<1){
      $("#post-qual-days").val('');
      post_qual_days=1;
      swal.fire({
        title: `Post Qualification Error`,
        text: 'Allowed input days must be atleast 1 day.',
        icon: 'warning'
      });
    }
    else{
      $("#post-qual-days").val(post_qual_days);
    }
    $("#post-qual-start").datepicker("update",moment(date,"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY"));
    $("#post-qual-end").off('change');
    $("#post-qual-end").datepicker("update",date.add(post_qual_days, 'day').format("MM/DD/YYYY"));
    $("#post-qual-end").change(function () {
      postQualAdjust();
    });

    // Issuance of NOA
    if(parseInt($("#iss-of-noa-days").val())>100)
    {
      iss_of_noa_days=2;
      $("#iss-of-noa-days").val('');
      swal.fire({
        title: `Notice of Award Error`,
        text: 'The longest allowable time for Issuance of NOA is 15 days.',
        icon: 'warning'
      });
    }
    else if(parseInt($("#iss-of-noa-days").val())>1){
      iss_of_noa_days=parseInt($("#iss-of-noa-days").val());
    }
    else if(parseInt($("#iss-of-noa-days").val())<1){
      iss_of_noa_days=1;
      $("#iss-of-noa-days").val(iss_of_noa_days);
      swal.fire({
        title: `Notice of Award Error`,
        text: 'Allowed input days must be atleast 1 day.',
        icon: 'warning'
      });
    }
    else{
      $("#iss-of-noa-days").val(iss_of_noa_days);
    }
    $("#iss-of-noa-start").datepicker("update",moment(date,"MM/DD/YYYY").add(1,'day').format("MM/DD/YYYY"));
    $("#iss-of-noa-end").off('change')
    $("#iss-of-noa-end").datepicker("update",date.add(iss_of_noa_days, 'day').format("MM/DD/YYYY"));
    $("#iss-of-noa-end").change(function () {
      issOfNoaAdjust();
    });

    // Contract Preparation
    if(parseInt($("#contract-prep-days").val())>100)
    {
      contract_prep_days=1;
      $("#contract-prep-days").val(contract_prep_days);
      swal.fire({
        title: `Contract Error`,
        text: 'Contract Preparation and Signing must be completed in 10 calendar days.',
        icon: 'warning'
      });
    }
    else if(parseInt($("#contract-prep-days").val())>=1){
      contract_prep_days=parseInt($("#contract-prep-days").val());
    }
    else if(parseInt($("#contract-prep-days").val())<=0){
      contract_prep_days=1;
      $("#contract-prep-days").val(contract_prep_days);
      swal.fire({
        title: `Contract Error`,
        text: 'Allowed input days must be atleast 1 day.',
        icon: 'warning'
      });
    }
    else{
      $("#contract-prep-days").val(contract_prep_days);
    }
    $("#contract-prep-start").datepicker("update",moment(date,"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY"));
    $("#contract-prep-end").off('change')
    $("#contract-prep-end").datepicker("update",date.add(contract_prep_days, 'day').format("MM/DD/YYYY"));
    $("#contract-prep-end").change(function () {
      contractSigningdjust();
    });

    // Approval of Higher Authority
    if($("#app-by-ha-yes").prop("checked")==true){
      if(parseInt($("#app-by-ha-days").val())>25)
      {
        app_by_ha_days=1;
        $("#app-by-ha-days").val(app_by_ha_days);
        swal.fire({
          title: `Approval Error`,
          text: 'The longest allowable time for this stage is 25 days.',
          icon: 'warning'
        });
      }
      else if(parseInt($("#app-by-ha-days").val())>=1){
        app_by_ha_days=parseInt($("#app-by-ha-days").val());
      }
      else if(parseInt($("#app-by-ha-days").val())<1){
        app_by_ha_days=1;
        $("#app-by-ha-days").val(app_by_ha_days);
        swal.fire({
          title: `Approval Error`,
          text: 'Allowed input days must be atleast 1 day.',
          icon: 'warning'
        });
      }
      else{
        $("#app-by-ha-days").val(app_by_ha_days);
      }
      $("#app-by-ha-start").datepicker("update",moment(date,"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY"));
      $("#app-by-ha-end").off('change')
      $("#app-by-ha-end").datepicker("update",date.add(app_by_ha_days, 'day').format("MM/DD/YYYY"));
      $("#app-by-ha-end").change(function () {
        appByHaAdjust();
      });
    }

    // Notice to Proceed
    if(parseInt($("#ntp-days").val())>7)
    {
      $("#ntp-days").val(ntp_days);
      swal.fire({
        title: `Notice To Proceed Error`,
        text: 'The NTP must be issued within 3 days or 7 days for above 50 million.',
        icon: 'warning'
      });
    }
    else  if(parseInt($("#ntp-days").val())>1){
      ntp_days=parseInt($("#ntp-days").val());
    }
    else  if(parseInt($("#ntp-days").val())<1){
      $("#ntp-days").val(ntp_days);
      swal.fire({
        title: `Notice To Proceed Error`,
        text: 'Allowed input days must be atleast 1 day.',
        icon: 'warning'
      });
    }
    else{
      $("#ntp-days").val(ntp_days);
    }
    $("#ntp-start").datepicker("update",moment(date,"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY"));
    $("#ntp-end").off('change');
    $("#ntp-end").datepicker("update",date.add(ntp_days, 'day').format("MM/DD/YYYY"));
    $("#ntp-end").change(function () {
      ntpAdjust();
    });
    $("#submit_btn").prop("disabled",false);
  }
  else{
    swal.fire({
      title: `Begin Date Error`,
      text: 'Please Input Date to Begin With ',
      icon: 'warning'
    });
  }
}

// get dates and formatDate
function formatDateRange(string) {
  console.log(string);
  var array=[];
  var oldDates=string.split("-");
  var days=days;
  var startDate=null;
  var endDate=null;
  if(oldDates.length>0){
    startDate=moment(oldDates[0],"MMM DD,YYYY");
    endDate=moment(oldDates[1],"MMM DD,YYYY");
    days=endDate.diff(startDate,'days');
    if(days<=1){
      days=null;
    }
  }
  array.push(moment(startDate).format("MM/DD/YYYY"));
  array.push(moment(endDate).format("MM/DD/YYYY"));
  array.push(days);
  return array;
}


function  prebidEndAdjust() {
  var start=$("#ads-post-end").val();
  startDate=moment(start,"MM/DD/YYYY").add("days");
  var end=moment($('#pre-bid-end').val(),"MM/DD/YYYY");
  if(end.isBefore(startDate)==true){
    $("#pre-bid-days").val('');
    $("#pre-bid-end").off('change')
    $("#pre-bid-end").datepicker("update",startDate.format("MM/DD/YYYY"));
    $("#pre-bid-end").change(function () {
      prebidEndAdjust();
    });
  }
  else{
    if(end.isAfter(startDate)==true){
      var difference=end.diff(moment($("#ads-post-end").val(),"MM/DD/YYYY"),'days');
      $("#pre-bid-days").val(difference);
    }
    else{
      $("#pre-bid-days").val(0);
    }
  }
  fillForm();
}

function  submissionEndAdjust() {
  var sub_of_bid_days=$("#sub-of-bid-days").val();
  if($("#pre-bid-yes").prop('checked')==true){
    var start=$("#pre-bid-end").val();
    if(sub_of_bid_days==0){
      startDate=moment(start,"MM/DD/YYYY");
    }
    else{
      startDate=moment(start,"MM/DD/YYYY").add(10,'day');
    }
  }
  else{
    var start=$("#ads-post-end").val();
    if(sub_of_bid_days==0){
      startDate=moment(start,"MM/DD/YYYY");
    }
    else{
      startDate=moment(start,"MM/DD/YYYY").add(1,'day');
    }
  }

  var end=moment($('#sub-of-bid-end').val(),"MM/DD/YYYY");
  var difference=end.diff(moment(start,"MM/DD/YYYY"),'days');

  if(end.isBefore(startDate)==true){
    $("#sub-of-bid-end").off('change');
    $("#sub-of-bid-end").datepicker("update", startDate.format("MM/DD/YYYY"));
    $("#sub-of-bid-end").change(function () {
      submissionEndAdjust();
    });
    $("#sub-of-bid-days").val('');
  }
  else{
    $("#sub-of-bid-days").val(difference);
  }
  fillForm();
}

function bidEvaluationAdjust() {

  var start=$("#sub-of-bid-end").val();
  if($("#bid-eval-days").val()=="0"){
    startDate=moment(start,"MM/DD/YYYY");
  }
  else{
    startDate=moment(start,"MM/DD/YYYY").add(0,"days");
  }

  var end=moment($('#bid-eval-end').val(),"MM/DD/YYYY");

  if(end.isBefore(startDate)==true){
    $("#bid-eval-days").val('');
    $("#bid-eval-end").off('change')
    $("#bid-eval-end").datepicker("update",startDate.format("MM/DD/YYYY"));
    $("#bid-eval-end").change(function () {
      bidEvaluationAdjust();
    });
  }
  if(end.isSame(startDate)==true){
    $("#bid-eval-end").off('change')
    $("#bid-eval-end").datepicker("update",startDate.format("MM/DD/YYYY"));
    $("#bid-eval-end").change(function () {
      bidEvaluationAdjust();
    });
  }
  else{
    if(end.isAfter(startDate)==true){
      var difference=end.diff(moment($("#sub-of-bid-end").val(),"MM/DD/YYYY"),'days');
      $("#bid-eval-days").val(difference);
    }
    else{
      $("#bid-eval-days").val('');
    }
  }

  fillForm();
}

function postQualAdjust() {
  var start=$("#bid-eval-end").val();
  startDate=moment(start,"MM/DD/YYYY").add(1,"days");
  var end=moment($('#post-qual-end').val(),"MM/DD/YYYY");
  if(end.isBefore(startDate)==true){
    $("#post-qual-days").val('');
    $("#post-qual-end").off('change')
    $("#post-qual-end").datepicker("update",startDate.format("MM/DD/YYYY"));
    $("#post-qual-end").change(function () {
      postQualAdjust();
    });
  }
  else{
    if(end.isAfter(startDate)==true){
      var difference=end.diff(moment($("#bid-eval-end").val(),"MM/DD/YYYY"),'days');
      $("#post-qual-days").val(difference);
    }
    else{
      $("post-qual-days").val('');
    }
  }
  fillForm();
}

function issOfNoaAdjust() {
  var start=$("#post-qual-end").val();
  startDate=moment(start,"MM/DD/YYYY").add(1,"days");
  var end=moment($('#iss-of-noa-end').val(),"MM/DD/YYYY");
  if(end.isBefore(startDate)==true){
    $("#iss-of-noa-days").val('');
    $("#iss-of-noa-end").off('change')
    $("#iss-of-noa-end").datepicker("update",startDate.format("MM/DD/YYYY"));
    $("#iss-of-noa-end").change(function () {
      issOfNoaAdjust();
    });
  }
  else{
    if(end.isAfter(startDate)==true||end.isSame(startDate)){
      var difference=end.diff(moment($("#post-qual-end").val(),"MM/DD/YYYY"),'days');
      $("#iss-of-noa-days").val(difference);
    }
    else{
      $("iss-of-noa-days").val('');
    }
  }
  fillForm();
}

function contractSigningdjust() {
  var start=$("#iss-of-noa-end").val();
  startDate=moment(start,"MM/DD/YYYY").add(1,"days");
  var end=moment($('#contract-prep-end').val(),"MM/DD/YYYY");
  if(end.isBefore(startDate)==true){
    $("#contract-prep-days").val('');
    $("#contract-prep-end").off('change')
    $("#contract-prep-end").datepicker("update",startDate.format("MM/DD/YYYY"));
    $("#contract-prep-end").change(function () {
      contractSigningdjust();
    });
  }
  else{
    if(end.isAfter(startDate)==true){
      var difference=end.diff(moment($("#iss-of-noa-end").val(),"MM/DD/YYYY"),'days');
      $("#contract-prep-days").val(difference);
    }
    else{
      $("contract-prep-days").val('');
    }
  }
  fillForm();
}

function appByHaAdjust() {

  if($("#app-by-ha-yes").prop('checked')==true){
    var start=$("#contract-prep-end").val();
    startDate=moment(start,"MM/DD/YYYY").add(1,"days");
    var end=moment($('#app-by-ha-end').val(),"MM/DD/YYYY");
    if(end.isBefore(startDate)==true){
      $("#app-by-ha-days").val('');
      $("#app-by-ha-end").off('change')
      $("#app-by-ha-end").datepicker("update",startDate.format("MM/DD/YYYY"));
      $("#app-by-ha-end").change(function () {
        appByHaAdjust();
      });
    }
    else{
      if(end.isAfter(startDate)==true){
        var difference=end.diff(moment($("#contract-prep-end").val(),"MM/DD/YYYY"),'days');
        $("#app-by-ha-days").val(difference);
      }
      else{
        $("app-by-ha-days").val('');
      }
    }
    fillForm();
  }
}

function  ntpAdjust() {

  if($("#app-by-ha-yes").prop('checked')==true){
    var start=$("#app-by-ha-end").val();
  }
  else{
    var start=$("#contract-prep-end").val();
  }

  startDate=moment(start,"MM/DD/YYYY").add(1,'day');
  var end=moment($('#ntp-end').val(),"MM/DD/YYYY");
  var difference=end.diff(moment(start,"MM/DD/YYYY"),'days');

  if(end.isBefore(startDate)==true || end.isSame(startDate)==true){
    $("#ntp-end").off('change');
    $("#ntp-end").datepicker("update", startDate.format("MM/DD/YYYY"));
    $("#ntp-end").change(function () {
      ntpAdjust();
    });
    $("#ntp-days").val('');
  }
  else{
    $("#ntp-days").val(difference);
  }
  fillForm();
}


// events
$('#app_table thead tr:eq(1) th').each( function (i) {
  var title = $(this).text();
  if(title!=""){
    $(this).html( '<input type="text" placeholder="Search" />' );
    $(this).addClass('sorting_disabled');

    $( 'input', this ).on( 'keyup change clear', function () {
      if ( table.column(':contains('+title+')').search() !== this.value ) {
        table
        .column(':contains('+title+')')
        .search( this.value )
        .draw();
      }
    } );
  }
});

// show delete
@if(in_array('delete',$user_privilege))
$('#app_table tbody').on('click', '.delete_btn', function (e) {

  Swal.fire({
    title:'Delete Schedule',
    text: 'Are you sure to delete Schedule?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    icon: 'warning'
  }).then((result) => {
    if(result.isConfirmed){
      window.location.href = "/delete_schedule/"+$(this).val();
    }
  });
});
@endif

@if(in_array('add',$user_privilege))
$('#app_table tbody').on('click', '.add_schedule', function (e) {
  table.rows().deselect();
  $("#schedule_modal_title").html("Add Schedule");
  $("#submit_schedule")[0].reset();
  $("#plan_ids").val($(this).val());
  $("#schedule_modal").modal('show');
});
@endif

@if(in_array('update',$user_privilege))
$('#app_table tbody').on('click', '.edit_schedule', function (e) {
  $("#submit_schedule")[0].reset();
  table.rows().deselect();
  var data=table.row($(this).parents('tr')).data();
  if(data.pre_proc_date!=""&&data.pre_proc_date!=null){
    $("#pre_procurement").datepicker("update",moment(data.pre_proc_date).format('L'));
  }
  else{
    $("#pre_procurement").val('');
  }
  var advertisement_post=formatDateRange(data.fullAdvertisement);
  var prebid=null;
  var sub_of_bid=formatDateRange(data.fullOpening);
  var bid_eval=formatDateRange(data.fullBidEvalutation);
  var post_qual=formatDateRange(data.fullPostQualification);
  var iss_of_noa=formatDateRange(data.fullNOA);
  var contract_signing=formatDateRange(data.fullContract);
  var app_by_ha=null;
  var ntp=formatDateRange(data.fullNTP);
  $("#begin_date").datepicker("update",advertisement_post[0]);
  $("#ads-post-start").datepicker("update",advertisement_post[0]);
  $("#ads-post-end").datepicker("update",advertisement_post[1]);
  if(advertisement_post[2]==7){
  }
  else{
    $("#ads-post-days").val(advertisement_post[2]);
  }

  if(sub_of_bid[2]==null){
    $("#sub-of-bid-days").val("0");
  }
  else{
    $("#sub-of-bid-days").val(bid_eval[2]);
  }

  if(bid_eval[2]==null){
    $("#bid-eval-days").val("0");
  }
  else{
    $("#bid-eval-days").val(bid_eval[2]);
  }

  if(data.fullPrebid==""||data.fullPrebid==null){
    $("#pre-bid-no").prop('checked',true);
  }
  else{
    $("#pre-bid-yes").prop('checked',true);
    prebid=formatDateRange(data.fullPrebid);
    $("#pre-bid-start").datepicker("update",prebid[0]);
    $("#pre-bid-end").datepicker("update",prebid[1]);
    $("#pre-bid-start").addClass('bg-white');
    $("#pre-bid-end").addClass('bg-white');
    prebidEndAdjust();
    $("#pre-bid-days").prop('readonly',false);
  }

  $("#sub-of-bid-start").datepicker("update",sub_of_bid[0]);
  $("#sub-of-bid-end").datepicker("update",sub_of_bid[1]);
  submissionEndAdjust();


  $("#bid-eval-start").datepicker("update",bid_eval[0]);
  $("#bid-eval-end").datepicker("update",bid_eval[1]);
  bidEvaluationAdjust();


  $("#post-qual-start").datepicker("update",post_qual[0]);
  $("#post-qual-end").datepicker("update",post_qual[1]);
  postQualAdjust();
  $("#iss-of-noa-start").datepicker("update",iss_of_noa[0]);
  $("#iss-of-noa-end").datepicker("update",iss_of_noa[1]);
  issOfNoaAdjust();
  $("#contract-prep-start").datepicker("update",contract_signing[0]);
  $("#contract-prep-end").datepicker("update",contract_signing[1]);
  contractSigningdjust();
  console.log(data.fullApproval);
  if(data.fullApproval=="" || data.fullApproval==null){
    $("#app-by-ha-no").prop('checked',true);
  }
  else{
    $("#app-by-ha-yes").prop('checked',true);
    app_by_ha=formatDateRange(data.fullApproval);
    $("#app-by-ha-start").datepicker("update",app_by_ha[0]);
    $("#app-by-ha-end").datepicker("update",app_by_ha[1]);
    $("#app-by-ha-days").val(app_by_ha[2]);
    $("#app-by-ha-start").addClass('bg-white');
    $("#app-by-ha-end").addClass('bg-white');
    $("#app-by-ha-days").addClass('bg-white');
    appByHaAdjust();

  }



  $("#ntp-start").datepicker("update",ntp[0]);
  $("#ntp-end").datepicker("update",ntp[1]);
  ntpAdjust();
  $("#submit_btn").prop("disabled",false);
  $("#schedule_modal_title").html("Update Schedule");
  $("#plan_ids").val($(this).val());
  $("#schedule_modal").modal('show');

});
@endif

$("#project_year").change(function () {
  $("#date_added").val("");
  $("#month_added").val("");
});

$("#date_added").change(function () {
  $("#project_year").val("");
  $("#month_added").val("");
});

$("#month_added").change(function () {
  $("#project_year").val("");
  $("#date_added").val("");
});


$("#filter_btn").click(function () {
  $("#app_filter").submit();
});

$("input").change(function () {
  $(this).siblings('.error-msg').html("");
});

$(".custom-radio").change(function () {
  $(this).parent().siblings('.error-msg').html("");
});

$("select").change(function () {
  $(this).siblings('.error-msg').html("");
});

$( "#plan_ids" ).keyup(function() {
  var str = $(this).val();
  str=str.replace(/[^0-9\-\a-zA-Z \@\#\.\/\,]/g,'');
  $(this).val(str);
});

//compute button
$("#compute-btn").click(function () {
  if($("#begin_date").val()==null||$("#begin_date").val()==""){
    swal.fire({
      title: `Input Error`,
      text: 'Please Input Date to Begin With ',
      icon: 'warning'
    });
  }
  else{
    var plan_ids=$("#plan_ids").val();
    var begin_date=$("#begin_date").val();
    $("#submit_schedule")[0].reset();
    $("#begin_date").val(begin_date);
    $("#plan_ids").val(plan_ids);
    fillForm();
  }
});


$("#start-over-btn").click(function () {
  $("#submit_schedule")[0].reset();
  $("#submit_btn").prop("disabled",true);
});

$("#pre-bid-no").click(function () {
  $("#pre-bid-start").val("");
  $("#pre-bid-end").val("");
  $("#pre-bid-days").val("");
  $("#pre-bid-start").removeClass("bg-white");
  $("#pre-bid-end").removeClass("bg-white");
  $("#pre-bid-days").removeClass("bg-white");
  $("#pre-bid-days").prop("readonly",true);
  fillForm();
});

$("#pre-bid-yes").click(function () {
  $("#pre-bid-start").removeClass("bg-white");
  $("#pre-bid-end").removeClass("bg-white");
  $("#pre-bid-days").removeClass("bg-white");
  $("#pre-bid-start").addClass("bg-white");
  $("#pre-bid-end").addClass("bg-white");
  $("#pre-bid-days").addClass("bg-white");
  $("#pre-bid-days").prop("readonly",false);
  fillForm();
});

$("#app-by-ha-no").click(function () {
  $("#app-by-ha-start").val("");
  $("#app-by-ha-end").val("");
  $("#app-by-ha-days").val("");
  $("#app-by-start").removeClass("bg-white");
  $("#app-by-end").removeClass("bg-white");
  $("#app-by-days").removeClass("bg-white");
  $("#app-by-days").prop("readonly",true);
  fillForm();
});

$("#app-by-ha-yes").click(function () {
  $("#app-by-ha-start").removeClass("bg-white");
  $("#app-by-ha-end").removeClass("bg-white");
  $("#app-by-ha-days").removeClass("bg-white");
  $("#app-by-ha-start").addClass("bg-white");
  $("#app-by-ha-end").addClass("bg-white");
  $("#app-by-ha-days").addClass("bg-white");
  $("#app-by-ha-days").prop("readonly",false);
  fillForm();
});


$(".update_trigger").click(function () {
  fillForm();
});

$(".auto_update").change(function functionName() {
  fillForm();
});

$(".datepicker").change(function () {
  if($(this).prop("id")!="pre_procurement" && $(this).prop("id")!="begin_date"){
    if($('#begin_date').val()==""){
      $(this).val('');
    }
  }
});

$(".datepicker").each(function () {
  if($(this).prop("id").indexOf('start')!=-1){
    $(this).prop('disabled',true);
  }
});

$("#submit_btn").click(function () {
  $(".datepicker").each(function () {
    if($(this).prop("id").indexOf('start')!=-1){
      $(this).prop('disabled',false);
    }
  });
  $("#submit_schedule").submit();
});

</script>
@endpush
