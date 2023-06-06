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
              <h3 class="modal-title" id="form_modal_title">Set Submission/Opening Date</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body pt-0">
              <form class="col-sm-12" method="POST" id="submit_bid_evaluation" action="/submit_bid_evaluation">
                @csrf
                <div class="row">
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto">
                    <h5 for="bid_evaluation_date">Plan Id/s</h5>
                    <input type="text" id="plan_id" name="plan_id" class="form-control form-control-sm" value="{{old('plan_id')}}" readonly>
                    <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                  </div>
                  <div class="form-group col-xs-16 col-sm-6 col-md-6 mb-2  mb-0">
                    <h5 for="bid_evaluation_date">Bid Evaluation Date</h5>
                    <input type="text" id="bid_evaluation_date" name="bid_evaluation_date" class="form-control form-control-sm datepicker" value="{{old('bid_evaluation_date')}}" >
                    <label class="error-msg text-red" >@error('bid_evaluation_date'){{$message}}@enderror</label>
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


    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
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
  if('{{old("rebid_plan_id")}}'!=''){
    $("#rebid_modal").modal('show');
  }
  if('{{old("review_plan_id")}}'!=''){
    $("#review_modal").modal('show');
  }


}
else{
  $("#project_year").val("{{$year}}");
}

var table=  $('#app_table').DataTable({
  dom: 'Bfrtip',
  buttons: [
    {
      text: 'Excel',
      extend: 'excel',
      className: 'btn btn-sm shadow-0 border-0 bg-success text-white'
    },
    {
      text: 'Print',
      extend: 'print',
      className: 'btn btn-sm shadow-0 border-0 bg-info text-white'
    }

  ],
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
      if(data<=3){
        return "@if(in_array('rebid',$user_privilege)||in_array('review',$user_privilege))<button class='btn btn-sm btn btn-sm btn-warning rebid_btn'>Rebid</button>@endif";
      }
      else{
        return "@if(in_array('rebid',$user_privilege)||in_array('review',$user_privilege))<button class='btn btn-sm btn btn-sm btn-danger review_btn' >Review</button>@endif";
      }
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
  { data:"failure_status"},

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
      text: 'Some rows have no active project bidders with proposed bid',
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
    $("#bid_evaluation_date").val('');
    $("#form_modal").modal('hide');
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

$("input").change(function functionName() {
  $(this).siblings('.error-msg').html("");
});

$(".custom-radio").change(function functionName() {
  $(this).parent().siblings('.error-msg').html("");
});

$("select").change(function functionName() {
  $(this).siblings('.error-msg').html("");
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

@if(in_array('rebid',$user_privilege)||in_array('review',$user_privilege))
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
      $("#rebid_plan_id").val(data.plan_id);
      $("#rebid_project_title").val(data.project_title);
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
      $("#review_plan_id").val(data.plan_id);
      $("#review_project_title").val(data.project_title);
      $("#review_modal").modal('show');
    }
  });
});

@endif

</script>
@endpush
