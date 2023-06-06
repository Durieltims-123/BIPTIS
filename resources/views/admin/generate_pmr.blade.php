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
          <h2 id="title">Generate Project Monitoring Report</h2>
        </div>
        <div class="card-body">
          <form class="col-sm-8 mx-auto" method="POST" id="" action="/submit_generate_awarded">
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


              <div class="form-group col-xs-5 col-sm-5 col-lg-5 d-none">
                <label for="report_type">Report Type</label>
                <input type="text" id="report_type" name="report_type" class="form-control form-control-sm datepicker" value="PMR" >
                <label class="error-msg text-red" >@error('report_type'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-2 col-sm-2 col-lg-2 mt-4 d-flex">
                <button class="btn btn-primary text-center">Submit</button>
                @if(session('project_plans'))
                <a class="btn btn-info text-center" target="_blank" href="/download_pmr/{{date('Y-m-d',strtotime(old('date_start')))}}/{{date('Y-m-d',strtotime(old('date_end')))}}">Download</a>
                @endif
              </div>

            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered " id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center">Municipality</th>
                  <th class="text-center">Group</th>
                  <th class="text-center">No</th>
                  <th class="text-center">Reference Number</th>
                  <th class="text-center">Name of Project_title</th>
                  <th class="text-center">Approved Budget for Contract</th>
                  <th class="text-center">Total ABC</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">RRC Date</th>
                  <th class="text-center">Date Awarded</th>
                  <th class="text-center">Winning Bidder</th>
                  <th class="text-center">Bid Amount</th>
                  <th class="text-center">Total Bid Amount</th>
                  <th class="text-center">Bidding Date</th>
                  <th class="text-center">Contract Duration (CAL DAYS)</th>

                </tr>
              </thead>
              <tbody>

              </tbody>
              <tfoot>
                <tr>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                </tr>
              </tfoot>

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

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
});

var table= $('#app_table').DataTable(
  {
    data:data,
    dataType: 'json',
    columns: [
      { "data": "municipality_name" },
      { "data": "group" },
      { "data": "count" },
      { "data": "project_no" },
      { "data": "project_title",render:function (data,type,row) {
        return "<div class='text-wrap' style='width:200px'>"+data+"</div>";
      } },
      {"data":"project_cost",render: function ( data, type, row ) {
        if(data!=null){
          return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        return "";
      }},
      { "data": "total_project_cost" },
      { "data": "location" },
      { "data": "resolution_date" ,  render: function ( data, type, row ) {
        return moment(data).format('MMM DD, YYYY');
      }},
      { "data": "award_notice" ,  render: function ( data, type, row ) {
        return moment(data).format('MMM DD, YYYY');
      }},
      { "data": "winning_bidder",render:function (data,type,row) {
        return "<div class='text-wrap' style='width:200px'>"+data+"</div>";
      } },
      {"data":"bid_amount",render: function ( data, type, row ) {
        if(data!=null){
          return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        return "";
      }},
      {"data":"total_bid"},
      { "data": "bidding_date" },
      { "data": "duration" }
    ],
    paging:false,
    order: [[ 2, "asc" ]],

    rowGroup: {
      endRender: function ( rows, group ) {
        var cost_total = rows
        .data()
        .pluck("total_project_cost").sum();
        var bid_total = rows
        .data()
        .pluck("total_bid").sum();
        return "<h4>"+group+" ABC SUB TOTAL:<span class='text-red'> PHP "+cost_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span>    BID AMOUNT SUBTOTAL: <span class='text-red'>  PHP "+bid_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span>"+"</h4>";
      },
      dataSrc:"municipality_name"
    },
    columnDefs: [
      {
        targets: [0,1,6,12],
        visible: false
      }
    ],
    footerCallback: function ( row, data, start, end, display ) {
      var api = this.api(), data

      // Remove the formatting to get integer data for summation
      var intVal = function ( i ) {
        return typeof i === 'string' ?
        i.replace(/[\$,]/g, '')*1 :
        typeof i === 'number' ?
        i : 0;
      };

      // Total over all pages
      total_abc = api
      .column( 6 ,{ page: 'current'})
      .data()
      .reduce( function (a, b) {
        return intVal(a) + intVal(b);
      }, 0 );

      total_bid = api
      .column( 12 ,{ page: 'current'})
      .data()
      .reduce( function (a, b) {
        return intVal(a) + intVal(b);
      }, 0 );


      // Update footer
      $( api.column( 4 ).footer() ).html(
        '<h3>TOTAL: <span class="text-red"> PHP '+total_abc.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span></h3>"
      );

      $( api.column( 9 ).footer() ).html(
        '<h3>TOTAL: <span class="text-red"> PHP '+total_bid.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span></h3>"
      );

    }

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
