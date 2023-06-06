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
  <div id="app" class="row">
    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Custom Project Bidders Report</h2>
        </div>
        <div class="card-body">
          <form class="col-sm-12 mx-auto" method="POST" id="" action="/submit_generate_custom_bidders_report">
            @csrf
            <div class="row d-flex">
              <div class="form-group col-xs-3 col-sm-3 col-lg-3 ">
                <label for="bidder_status">Bidder Status</label>
                <select type="text" id="bidder_status" name="bidder_status" class="form-control form-control-sm" value="{{old('bidder_status')}}" >
                  <option value="0" {{ old('bidder_status') == "0" ? 'selected' : ''}}>Did Not Submit</option>
                  <option value="1" {{ old('bidder_status') == "1" ? 'selected' : ''}}>Disqualified</option>
                  <option value="2" {{ old('bidder_status') == "2" ? 'selected' : ''}}>Non-Responsive</option>
                  <option value="3" {{ old('bidder_status') == "3" ? 'selected' : ''}}>Awarded/Responsive</option>
                  <option value="4" {{ old('bidder_status') == "4" ? 'selected' : ''}}>Ongoing</option>
                  <option value="5" {{ old('bidder_status') == "5" ? 'selected' : ''}}>Loosing</option>
                  <option value="6" {{ old('bidder_status') == "6" ? 'selected' : ''}}>Withdrawn</option>
                  <option value="7" {{ old('bidder_status') == "7" ? 'selected' : ''}}>All</option>
                </select>
                <label class="error-msg text-red" >@error('bidder_status'){{$message}}@enderror</label>
              </div>


              <div class="form-group col-xs-2 col-sm-2 col-lg-2 ">
                <label for="date_start">Date Start</label>
                <input type="text" id="date_start" name="date_start" class="form-control form-control-sm datepicker" value="{{old('date_start')}}" >
                <label class="error-msg text-red" >@error('date_start'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-2 col-sm-2 col-lg-2 ">
                <label for="date_end">Date End</label>
                <input type="text" id="date_end" name="date_end" class="form-control form-control-sm datepicker" value="{{old('date_end')}}" >
                <label class="error-msg text-red" >@error('date_end'){{$message}}@enderror</label>
              </div>


              <div class="form-group col-xs-2 col-sm-2 col-lg-2 ">
                <label for="procurement_mode">Procurement Mode</label>
                <select type="text" id="procurement_mode" name="procurement_mode" class="form-control form-control-sm" value="{{old('procurement_mode')}}" >
                  <option value="0" {{ old('procurement_mode') == "0" ? 'selected' : ''}}>All</option>
                  <option value="1" {{ old('procurement_mode') == "1" ? 'selected' : ''}}>Bidding</option>
                  <option value="2" {{ old('procurement_mode') == "2" ? 'selected' : ''}}>SVP</option>
                  <option value="3" {{ old('procurement_mode') == "3" ? 'selected' : ''}}>Negotiated</option>
                  <option value="4" {{ old('procurement_mode') == "4" ? 'selected' : ''}}>SVP and Negotiated</option>
                </select>
                <label class="error-msg text-red" >@error('procurement_mode'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-2 col-sm-2 col-lg-2 mt-4">
                <button class="btn btn-primary text-center mx-auto">Submit</button>
                @if(session('project_bidders'))
                <a class="btn btn-info text-center" target="_blank" href="/download_custom_bidders_report/{{date('Y-m-d',strtotime(old('date_start')))}}/{{date('Y-m-d',strtotime(old('date_end')))}}/{{old('bidder_status')}}/{{old('procurement_mode')}}">Download</a>
                @endif
              </div>

            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered " id="table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center">Group</th>
                  <th class="text-center">No</th>
                  <th class="text-center">Reference Number</th>
                  <th class="text-center">Name of Project_title</th>
                  <th class="text-center">Approved Budget for Contract</th>
                  <th class="text-center">Total ABC</th>
                  <th class="text-center">Mode</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Bidder</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Name and Address</th>
                  <th class="text-center">Bid Amount</th>
                  <th class="text-center">Total Bid Amount</th>
                  <th class="text-center">Bidding Date</th>
                  <th class="text-center">Awarded Total</th>
                  <th class="text-center">Awarded</th>
                  <th class="text-center">Not Awarded Total</th>
                  <th class="text-center">DNS/Disqualified/Non-Responsive/Loosing</th>
                  <th class="text-center">Ongoing Total</th>
                  <th class="text-center">Ongoing</th>
                  <th class="text-center">Post Qual Start</th>
                  <th class="text-center">Post Qual End</th>
                  <th class="text-center">Notice of Award</th>
                  <th class="text-center">Remarks</th>
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
var data = {!! json_encode(session('project_bidders')) !!};
$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
});


// $('#table thead tr').clone(true).appendTo( '#table thead' );
// $('#table thead tr:eq(1)').removeClass('bg-primary');

var table= $('#table').DataTable(
  {
    data:data,
    dataType: 'json',
    columns: [
      { "data": "group" },
      { "data": "count" },
      { "data": "project_no" },
      { "data": "project_title" },
      { "data": "project_cost" },
      { "data": "total_project_cost" },
      { "data": "mode" },
      { "data": "location" },
      { "data": "bidder" },
      { "data": "bid_status" },
      { "data": "name_address" },
      { "data": "bid_amount" },
      { "data": "total_bid" },
      { "data": "bidding_date" },
      { "data": "total_fees","render": function ( data, type, row, meta ) {
        if(row.bid_status=="responsive"){
          return data;
        }
        else{
          return 0.00;
        }
      }},
      { "data": "fees" ,"render": function ( data, type, row, meta ) {
        if(row.bid_status=="responsive"){
          return data;
        }
        else{
          return "";
        }
      }},
      { "data": "total_fees","render": function ( data, type, row, meta ) {
        if(row.bid_status=="Did Not Submit" || row.bid_status=="disqualified" ||row.bid_status=="non-responsive"){
          return data;
        }
        else{
          return 0.00;
        }
      }},
      { "data": "fees" ,"render": function ( data, type, row, meta ) {
        if(row.bid_status=="Did Not Submit" || row.bid_status=="disqualified" ||row.bid_status=="non-responsive" || row.bid_status=="Loosing Bid"){
          return data;
        }
        else{
          return "";
        }
      }},
      { "data": "total_fees","render": function ( data, type, row, meta ) {
        if(row.bid_status=="ongoing"){
          return data;
        }
        else{
          return 0.00;
        }
      }},
      { "data": "fees" ,"render": function ( data, type, row, meta ) {
        if(row.bid_status=="ongoing"){
          return data;
        }
        else{
          return "";
        }
      }},
      { "data": "post_qual_start","render": function ( data, type, row, meta ) {
        if(row.bid_status=="ongoing"){
          return "";
        }
        else{
          return data;
        }
      }},
      { "data": "post_qual_end","render": function ( data, type, row, meta ) {
        if(row.bid_status=="ongoing"){
          return "";
        }
        else{
          return data;
        }
      }},
      { "data": "award_date","render": function ( data, type, row, meta ) {
        if(row.bid_status=="responsive" || row.bid_status=="Loosing Bid"){
          return data;
        }
        return "";

      }},
      { "data": "remarks" ,"render": function ( data, type, row, meta ) {
        if(row.bid_status=="ongoing"){
          return "";
        }
        else{
          return data;
        }
      }},
    ],
    order: [[ 1, "asc" ]],
    paging:false,
    rowGroup: {
      endRender: function ( rows, group ) {
        var total_awarded=0.00;
        var total_not_responsive=0.00;
        var total_ongoing=0.00;
        var fees = rows.data().each( function ( value, index ) {
          if(value.bid_status=="responsive"){
            total_awarded=total_awarded+parseFloat(value.total_fees);
          }
          else if(value.bid_status=="ongoing"){
            total_ongoing=total_ongoing+parseFloat(value.total_fees);
          }
          else{
            total_not_responsive=total_not_responsive+parseFloat(value.total_fees);
          }
        });

        return "<div class='row'><h3 class='col-sm-3 mx-auto'>"+group+"</h3><h3 class='col-sm-3 mx-auto'>Total Awarded: <span class='text-red'> PHP "+total_awarded.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span></h3> <h3 class='col-sm-3 mx-auto'> Total DNS/Disqualified/Non-Responsive/Loosing:  <span class='text-red'>PHP "+total_not_responsive.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span></h3> <h3 class='col-sm-3 mx-auto'> Total Ongoing: <span class='text-red'> PHP "+total_ongoing.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span></h3></div>";
      },
      dataSrc:"group"
    },
    columnDefs: [
      {
        targets: [0,5,7,10,12,14,16,18],
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



      total_awarded = api.cells( null, 14 ).render( 'display' ).reduce( function (a, b) {
        return intVal(a) + intVal(b);
      },0);

      total_not_responsive =api.cells( null, 16 ).render( 'display' ).reduce( function (a, b) {
        return intVal(a) + intVal(b);
      },0);

      total_ongoing = api.cells( null, 18 ).render( 'display' ).reduce( function (a, b) {
        return intVal(a) + intVal(b);
      },0);



      // Update footer
      $( api.column( 15 ).footer() ).html(
        '<h3>TOTAL AWARDED: <span class="text-red"> PHP '+total_awarded.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span></h3>"
      );

      $( api.column( 17 ).footer() ).html(
        '<h3>TOTAL DNS/Disqualified/Non-Responsive/Loosing Bids: <span class="text-red"> PHP '+total_not_responsive.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span></h3>"
      );

      $( api.column( 19 ).footer() ).html(
        '<h3>TOTAL ONGOING: <span class="text-red"> PHP '+total_ongoing.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"</span></h3>"
      );

    }

  }
);
console.log(table.cells( null, 14 ).render( 'display' ))
$('#table thead tr:eq(1) th').each( function (i) {
  var title = $(this).text();
  if(title!=""){
    $(this).html( '<input type="text" placeholder="Search" />' );
    $(this).addClass('sorting_disabled');
    var index=0;

    $( 'input', this ).on( 'keyup change', function () {
      if ( table.column(':contains('+title+')').search() !== this.value ) {
        table
        .column(':contains('+title+')')
        .search( this.value )
        .draw();
      }

    } );
  }
});


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
