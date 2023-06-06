@extends('layouts.app')
<style>
ul.ui-autocomplete {
  z-index: 1100;
}
.text-wrap {
  word-break: break-all !important;
}

tbody:nth-child(odd) {
  background: #CCC;
}
</style>
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">

    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Generate Awarded Projects Suspended For Transmittal</h2>
        </div>
        <div class="card-body">
          <form class="col-sm-8 mx-auto" method="POST" id="" action="/submit_generate_awarded_for_transmittal">
            @csrf
            <div class="row d-flex">

              <div class="form-group col-xs-5 col-sm-5 col-lg-5 ">
                <label for="date_start">Date Start</label>
                <input type="text" id="date_start" name="date_start" class="form-control form-control-sm datepicker" value="{{old('date_start')}}" >
                <label class="error-msg text-red" >@error('date_start'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-5 col-sm-5 col-lg-5 ">
                <label for="date_end">Date End</label>
                <input type="text" id="date_end" name="date_end" class="form-control form-control-sm datepicker" value="{{old('date_end')}}" >
                <label class="error-msg text-red" >@error('date_end'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-2 col-sm-2 col-lg-2 mt-4 d-flex">
                <button class="btn btn-primary text-center">Submit</button>
                @if(session('project_plans'))
                <a class="btn btn-info text-center" target="_blank" href="/download_awarded_for_transmittal/{{date('Y-m-d',strtotime(old('date_start')))}}/{{date('Y-m-d',strtotime(old('date_end')))}}">Download</a>
                @endif
              </div>

            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered " id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center">No</th>
                  <th class="text-center">Date Awarded</th>
                  <th class="text-center">Project Number</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Source of fund</th>
                  <th class="text-center">Date of Bidding</th>
                  <th class="text-center">Contract Amount</th>
                  <th class="text-center">Contractor</th>
                  <th class="text-center">Date of NOA Preparation</th>
                  <th class="text-center">Date of Receipt of NOA by Contractor</th>
                  <th class="text-center">No. of days from receipt of NOA to Date</th>
                  <th class="text-center">Date of P. Bond issuance and submission</th>
                  <th class="text-center">Date of CHSP Issuance & Submission</th>
                  <th class="text-center">Remarks</th>
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
var data = {!! json_encode(session('project_plans')) !!};
console.log(data);

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
});

var table= $('#app_table').DataTable(
  {
    data:data,
    dataType: 'json',
    columns: [
      { "data": "count" },
      { "data": "resolution_date" },
      { "data": "project_no" },
      { "data": "project_title" },
      { "data": "location" },
      { "data": "source_of_fund" },
      { "data": "bidding_date" },
      { "data": "bid_amount" },
      { "data": "winning_bidder" },
      { "data": "noa_preparation" },
      { "data": "date_received_by_contractor" },
      { "data": "noa_to_date" },
      { "data": "pb" },
      { "data": "chsp" },
      { "data": "remarks" }
    ],
    paging:false,
    order: [[ 0, "asc" ]],
    // columnDefs: [
    //   {
    //     targets: [0,5,10],
    //     visible: false
    //   }
    // ]
  }
);

// remove duplicate group header
var seen = {};
$('.dtrg-group').each(function() {
  var txt = $(this).text();
  if (seen[txt])
  $(this).remove();
  else
  seen[txt] = true;
});

</script>
@endpush
