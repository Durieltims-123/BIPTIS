@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="app">

    <div class="col-sm-12">
      <div class="modal" tabindex="-1" role="dialog" id="reactivate_modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="reactivate_modal_title">Reactivate Project</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body pt-0">
              <form class="col-sm-12" method="POST" id="submit_reactivate" action="/submit_reactivate">
                @csrf
                <div class="row">
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <h5 for="">Plan ID</h5>
                    <input type="text" id="reactivate_plan_id" name="reactivate_plan_id" class="form-control form-control-sm" value="{{old('Reactivate_plan_id')}}" readonly>
                    <label class="error-msg text-red" >@error('Reactivate_plan_id'){{$message}}@enderror</label>
                  </div>
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mx-auto">
                    <h5 for="">Project Title</h5>
                    <input type="text" id="reactivate_project_title" name="reactivate_project_title" class="form-control form-control-sm" readonly value="{{old('Reactivate_project_title')}}" >
                    <label class="error-msg text-red" >@error('Reactivate_project_title'){{$message}}@enderror</label>
                  </div>


                  <div class="form-group col-xs-12 col-sm-12 col-md-12  mb-0">
                    <h5 for="reactivate_remarks">Remarks</h5>
                    <textarea type="text" id="reactivate_remarks" name="reactivate_remarks" class="form-control form-control-sm " value="{{old('Reactivate_remarks')}}" ></textarea>
                    <label class="error-msg text-red" >@error('Reactivate_remarks'){{$message}}@enderror</label>
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
                  <th class="text-center">Cluster</th>
                  <th class="text-center">APP/SAPP No.</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Project Cost</th>
                  <th class="text-center">Project Year</th>
                  <th class="text-center">Status</th>
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
var data = {!! json_encode(session('newData')) !!};
if(data==null){
  data = {!! json_encode($project_plans) !!};
}
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
  if('{{old("reactivate_plan_id")}}'!=''){
    $("#reactivate_modal").modal('show');
  }

}
else{
  $("#project_year").val("{{$year}}");
}

var table=  $('#app_table').DataTable({
  data:data,
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  columns: [
    { data:"re_bid_count",
    render: function ( data, type, row ) {
      return "<button class='btn btn-sm btn btn-sm btn-warning reactivate_btn'>Reactivate</button>";
    }
  },
  { data:"plan_id"},
  { data:"current_cluster"},
  { data:"app_group_no"},
  { data:"project_no",
  render: function ( data, type, row ){
    return "<a href='/view_project/"+row.plan_id+"'>"+data+"</a>";
  }},
  { data:"project_title"},
  { data:"municipality_name"},
  { data:"mode"},
  { data:"source"},
  { data:"project_cost"},
  { data:"project_year"},
  { data:"status"},
  { data:"remarks"},
],
orderCellsTop: true,
select: {
  style: 'multi',
  selector: 'td:not(:first-child)'
},
responsive:true,
columnDefs: [
  {
    targets: 0,
    orderable: false
  },
  {
    targets:1,
    visible:false
  }
],
rowGroup: {

  startRender: function ( rows, group ) {
    console.log(group);
    if(group=="No group"||group==null){
      var group_title="Non-Clustered Project";
    }
    else{
      var group_title="Cluster "+group;
    }
    return group_title;
  },
  endRender: null,
  dataSrc: 'current_cluster'
}
});

if("{{session('message')}}"){
  if("{{session('message')}}"=="reactivate_success"){

    swal.fire({
      title: 'REACTIVATE SUCCESS',
      text: 'Project was  Reactivated',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-warning',
      icon: 'warning'
    });
    $("#reactivate_modal").modal('hide');
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

  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: 'Success',
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#plan_id").val('');
    $("#bid_evaluation_date").val('');
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





$('#app_table tbody').on('click', '.reactivate_btn', function (e) {
  var data = table.row( $(this).parents('tr') ).data();
  Swal.fire({
    title:"Reactivate Project",
    text: 'Are you sure to Reactivate Project?',
    showCancelButton: true,
    cancelButtonText: "No",
    confirmButtonText: 'Yes',
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#reactivate_plan_id").val(data.plan_id);
      $("#reactivate_project_title").val(data.project_title);
      $("#reactivate_modal").modal('show');
    }
  });


});


</script>
@endpush
