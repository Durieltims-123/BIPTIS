@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="app">
    <div class="col-sm-12">
      <div class="modal" tabindex="-1" role="dialog" id="form_modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="form_modal_title">Set Notice of Award Date</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body pt-0">
              <form class="col-sm-12" method="POST" id="submit_award_notice" action="/submit_award_notice">
                @csrf
                <div class="row">
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto">
                    <h5 for="award_notice_date">Plan Id/s</h5>
                    <input type="text" id="plan_id" name="plan_id" class="form-control form-control-sm" value="{{old('plan_id')}}" readonly>
                    <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                  </div>
                  <div class="form-group col-xs-16 col-sm-6 col-md-6 mb-2  mb-0">
                    <h5 for="award_notice_date">Notice of Award Date</h5>
                    <input type="text" id="award_notice_date" name="award_notice_date" class="form-control form-control-sm datepicker" value="{{old('award_notice_date')}}" >
                    <label class="error-msg text-red" >@error('award_notice_date'){{$message}}@enderror</label>
                  </div>
                </div>
                <div class="d-flex justify-content-center col-sm-12">
                  <button  class="btn btn-primary text-center">Submit</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>


    @include('layouts.components.modals')
    @include('layouts.components.extend_modal')


    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12 d-none" id="filter">
            <form class="row" id="app_filter" method="post" action="{{route('filter_app')}}">
              @csrf


              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="project_year" class="input-sm">Project Year </label>
                <input  class="form-control form-control-sm yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}" >
                <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror</label>
              </div>

              <!-- Month added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="month_added">Month Added </label>
                <input type="text" id="month_added" name="month_added" class="form-control form-control-sm monthpicker" value="{{old('month_added')}}" >
                <label class="error-msg text-red" >@error('month_added'){{$message}}@enderror</label>
              </div>

              <!-- date added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="date_added">Date Added </label>
                <input type="text" id="date_added" name="date_added" class="form-control form-control-sm datepicker" value="{{old('date_added')}}" >
                <label class="error-msg text-red" >@error('date_added'){{$message}}@enderror</label>
              </div>

            </form>
          </div>
          <div class="col-sm-12">

            <!-- <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Filter</button> -->
            <button class="btn btn-sm btn-danger text-white float-right mb-2 btn btn-sm ml-2" id="extend_btn">Extend</button>
            <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button>
            <button class="btn btn-sm btn-primary text-white float-right mb-2 btn btn-sm ml-2" id="set_dates_btn">Set Dates</button>
            <button class="btn btn-sm btn-warning text-white float-right mb-2 btn btn-sm ml-2 d-none" id="filter_btn">Filter</button>
            <button class="btn btn-sm btn-default text-white float-right mb-2 btn btn-sm ml-2" id="show_filter">Show Filter</button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Notice of Award</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">APP/SAPP No.</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Contractor</th>
                  <th class="text-center">Project Cost</th>
                  <th class="text-center">Proposed Bid</th>
                  <th class="text-center">Project Year</th>
                  <th class="text-center">Remarks</th>
                </tr>
              </thead>
              <tbody>
                @if(session('newData'))

                @foreach(session('newData') as $project_plan)
                <tr>

                  <td class="col-md-12" style="white-space: nowrap">
                    <button class="btn btn-sm btn btn-sm btn-primary set_date_btn" >Set Date</button>

                    <a class="btn btn-sm btn btn-sm btn-success" target='_blank' href="/project_bidders/{{$project_plan->plan_id}}" >Bidders</a>
                    @if($project_plan->re_bid_count<=3||$project_plan->re_bid_count==null)
                    <button class="btn btn-sm btn btn-sm btn-warning rebid_btn" >Rebid</button>
                    @else
                    <button class="btn btn-sm btn btn-sm btn-danger review_btn" >Review</button>
                    @endif
                  </td>
                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->award_notice_start))}} - {{ date("M d,Y", strtotime($project_plan->award_notice_end))}}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td>{{ $project_plan->app_group_no }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->mode }}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->business_name }}</td>
                  <td>{{ number_format($project_plan->project_cost,2,'.',',') }}</td>
                  <td>{{ number_format($project_plan->minimum_cost,2,'.',',') }}</td>
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->remarks }}</td>

                </tr>
                @endforeach

                @else
                @foreach($project_plans as $project_plan)
                <tr>

                  <td class="col-md-12" style="white-space: nowrap">
                    <button class="btn btn-sm btn btn-sm btn-primary set_date_btn" >Set Date</button>

                    <a class="btn btn-sm btn btn-sm btn-success" target='_blank' href="/project_bidders/{{$project_plan->plan_id}}" >Bidders</a>
                    @if($project_plan->re_bid_count<=3||$project_plan->re_bid_count==null)
                    <button class="btn btn-sm btn btn-sm btn-warning rebid_btn" >Rebid</button>
                    @else
                    <button class="btn btn-sm btn btn-sm btn-danger review_btn" >Review</button>
                    @endif
                  </td>
                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->award_notice_start))}} - {{ date("M d,Y", strtotime($project_plan->award_notice_end))}}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td>{{ $project_plan->app_group_no }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->mode }}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->business_name }}</td>
                  <td>{{ number_format($project_plan->project_cost,2,'.',',') }}</td>
                  <td>{{ number_format($project_plan->minimum_cost,2,'.',',') }}</td>
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->remarks }}</td>
                </tr>
                @endforeach
                @endif
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
// datatables
$('#app_table thead tr').clone(true).appendTo( '#app_table thead' );
$('#app_table thead tr:eq(1)').removeClass('bg-primary');

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
  endDate:'{{$year}}'
});

$(".datepicker2").datepicker({
  format: 'mm/dd/yyyy',
});



$('.timepicker').timepicker({
  showRightIcon: false,
  showOnFocus: true,
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

// inputs
var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>2){


  if('{{old("rebid_plan_id")}}'!=''){
    $("#rebid_modal").modal('show');
  }
  if('{{old("review_plan_id")}}'!=''){
    $("#review_modal").modal('show');
  }
  if('{{old("extend_plan_id")}}'!=''){
    $("#process").val("notice_of_award");
    $("#extend_modal").modal('show');
  }
  if('{{old("plan_id")}}'!=''){
    $("#form_modal").modal('show');
  }


}
else{
  $("#project_year").val("{{$year}}");
}

var table=  $('#app_table').DataTable({
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
    targets:1,
    visible:false
  } ],
  rowGroup: {

    startRender: function ( rows, group ) {
      if(group==""||group==null){
        var group_title="Non-Clustered Project";
      }
      else{
        var group_title="Cluster "+group;
      }
      return group_title;
    },
    endRender: null,
    dataSrc: 3
  }
});

if("{{session('message')}}"){
  if("{{session('message')}}"=="range_error"){

    swal.fire({
      title: 'Date Error',
      text: 'Date should be equal or in between the given date range. ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="rebid_success"){

    swal.fire({
      title: 'REBID SUCCESS',
      text: 'Project was queued for Rebid',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-warning',
      icon: 'warning'
    });
    $("#rebid_modal").modal('hide');
  }
  else if("{{session('message')}}"=="rebideval_success"){

    swal.fire({
      title: 'SUCCESS',
      text: 'Project was queued for New Bid Evaluation',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-warning',
      icon: 'warning'
    });
    $("#rebideval_modal").modal('hide');
  }
  else if("{{session('message')}}"=="review_success"){
    swal.fire({
      title: 'REVIEW SUCCESS',
      text: 'Project status was set to for review',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-warning',
      icon: 'warning'
    });
    $("#review_modal").modal('hide');
  }
  else if("{{session('message')}}"=="bidder_error"){
    swal.fire({
      title: 'Project Bidder Error',
      text: 'Some rows have no active project bidders',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="resolution_error"){
    swal.fire({
      title: 'Resolution Error',
      text: 'The Project Plan is not included in any Resolution Declaring Failure',
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
    $("#plan_id").val('');
    $("#award_notice_date").val('');
    $("#form_modal").modal('hide');
  }
  else{

  }
}

// events
$('#app_table thead tr:eq(1) th').each( function (i) {
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


// hide/show Filter
$("#show_filter").click(function() {
  if($(this).html()=="Show Filter"){
    $('#filter').removeClass('d-none');
    $('#filter_btn').removeClass('d-none');
    $("#show_filter").html("Hide Filter");
  }
  else{
    $('#filter').addClass('d-none');
    $('#filter_btn').addClass('d-none');
    $("#show_filter").html("Show Filter");
  }
});

$("#project_year").change(function functionName() {
  $("#date_added").val("");
  $("#month_added").val("");
});

$("#date_added").change(function functionName() {
  $("#project_year").val("");
  $("#month_added").val("");
});

$("#month_added").change(function functionName() {
  $("#project_year").val("");
  $("#date_added").val("");
});


$("#filter_btn").click(function () {
  $("#app_filter").submit();
});

$("input").change(function functionName() {
  $(this).siblings('.error-msg').html("");
});

$(".custom-radio").change(function functionName() {
  $(this).parent().siblings('.error-msg').html("");
});

$("select").change(function functionName() {
  $(this).siblings('.error-msg').html("");
});



$('#app_table tbody').on('click', '.set_date_btn', function (e) {
  table.rows().deselect();
  var data = table.row( $(this).parents('tr') ).data();
  $("#plan_id").val(data[1]);
  $("#form_modal").modal('show');
});

$("#set_dates_btn").click(function functionName() {
  if(table.rows( { selected: true } ).count()<=1){
    Swal.fire({
      title:"Warning",
      text: 'Please select rows Set Date ',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-info',
      icon: 'info'
    });
  }
  else{
    let rows = table.rows( { selected: true } );
    var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
    $("#plan_id").val(plan_ids.toString());
    $("#form_modal").modal('show');
  }
});

$('#app_table tbody').on('click', '.rebid_btn', function (e) {
  var data = table.row( $(this).parents('tr') ).data();
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
      $("#rebid_plan_id").val(data[1]);
      $("#rebid_project_title").val(data[6]);
      $("#rebid_modal").modal('show');
    }
  });


});





$('#app_table tbody').on('click', '.review_btn', function (e) {
  var data = table.row( $(this).parents('tr') ).data();
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
      $("#review_plan_id").val(data[1]);
      $("#review_project_title").val(data[6]);
      $("#review_modal").modal('show');
    }
  });


});

$("#extend_btn").click(function functionName() {
  if(table.rows( { selected: true } ).count()<1){
    Swal.fire({
      title:"Warning",
      text: 'Please select rows Extend ',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-info',
      icon: 'info'
    });
  }
  else{
    let rows = table.rows( { selected: true } );
    var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
    var plan_numbers =  table.cells( rows.nodes(), 6 ).data().toArray();
    $("#extend_plan_id").val(plan_ids.toString());
    $("#extend_project_number").val(plan_numbers.toString());
    $("#process").val("notice_of_award");
    console.log($("#process").val());
    $("#extend_days").val("");
    $("#extend_remarks").val("");
    $("#extend_modal").modal('show');
  }
});

</script>
@endpush
