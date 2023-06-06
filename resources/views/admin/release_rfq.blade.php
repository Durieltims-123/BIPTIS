@extends('layouts.app')
<style>
ul.ui-autocomplete {
  z-index: 1100;
}
</style>
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">
    <div class="col-sm-12">
      <div class="modal" tabindex="-1" role="dialog" id="release_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="release_modal_title">@if(old('rfq_id')!=="")Update @else Add @endif Release RFQ </h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="release_form" action="submit_release_rfq">
                @csrf
                <div class="row d-flex">

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <label for="rfq_id">RFQ ID
                    </label>
                    <input type="text" id="rfq_id" name="rfq_id" class="form-control form-control-sm" value="{{old('rfq_id')}}" >
                    <label class="error-msg text-red" >@error('rfq_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <label for="plan_id">Plan Id/s
                    </label>
                    <input type="text" id="plan_id" name="plan_id" class="form-control form-control-sm" value="{{old('plan_id')}}" >
                    <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- Opening date -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="opening_date">Opening Date <span class="text-red">*</span>
                    </label>
                    <input  type="text" id="opening_date" name="opening_date" class="form-control form-control-sm datepicker2" value="{{old('opening_date')}}" >
                    <label class="error-msg text-red" >@error('opening_date'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- project_title -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="plan_title">Project Title <span class="text-red">*</span>
                    </label>
                    <input list="titles" type="text" id="plan_title" name="plan_title" class="form-control form-control-sm" value="{{old('plan_title')}}" >
                    <label class="error-msg text-red" >@error('plan_title'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="project_number">Project Number <span class="text-red">*</span>
                    </label>
                    <input type="text" id="project_number" name="project_number" class="form-control form-control-sm" value="{{old('project_number')}}" readonly>
                    <label class="error-msg text-red" >@error('project_number'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="project_type">Project Type <span class="text-red">*</span>
                    </label>
                    <input type="text" id="project_type" name="project_type" class="form-control form-control-sm" value="{{old('project_type')}}" readonly>
                    <label class="error-msg text-red" >@error('project_type'){{$message}}@enderror
                    </label>
                  </div>


                  <!-- Contractor  -->

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <label for="contractor_id">Contractor ID <span class="text-red">*</span>
                    </label>
                    <input type="text" id="contractor_id" name="contractor_id" class="form-control form-control-sm" value="{{old('contractor_id')}}" >
                    <label class="error-msg text-red" >@error('contractor_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 ui-widget">
                    <label for="contractor">Contractor <span class="text-red">*</span>
                    </label>
                    <input type="text" id="contractor" name="contractor" class="form-control form-control-sm" value="{{old('contractor')}}" >
                    <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror
                    </label>
                  </div>


                  <!-- date released -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 ">
                    <label for="date_released">Date RFQ Released <span class="text-red">*</span>
                    </label>
                    <input type="text" id="date_released" name="date_released" class="form-control form-control-sm datepicker" value="{{old('date_released')}}" >
                    <label class="error-msg text-red" >@error('date_released'){{$message}}@enderror
                    </label>
                  </div>


                </div>
                <div class="d-flex justify-content-center col-sm-12">
                  <button class="btn btn-primary text-center">Submit</button>
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
            <form class="row" id="filter_rfq" method="post" action="{{route('filter_rfq')}}">
              @csrf
              <!--  year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="year" class="input-sm">Year RFQ Released
                </label>
                <input  class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}" >
                <label class="error-msg text-red" >@error('year'){{$message}}@enderror
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
                  <th class="text-center">Cluster</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">APP/SAPP No.</th>
                  <th class="text-center">Opening Date</th>
                  <th class="text-center">Date Released</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Municipality</th>
                  <th class="text-center">Contractor ID</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Source of fund</th>
                  <th class="text-center">Project Type</th>
                  <th class="text-center">Approved Budget Cost</th>
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

let data=@json($released_rfqs);
if(@json(old('year'))!=null){
  data=@json(session('released_rfqs'));
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




var contractor_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_contractors',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term
      },
      'method': "post",
      'dataType': "json",
      'success': function(data) {
        response(data);
      }
    });
  },
  select: function(event, ui){
    if(ui.item.id != ''){
      $(this).val(ui.item.value);
    }else{
      $(this).val('');
    }
    return false;
  },
  change: function (event, ui) {


    if (ui.item == null || ui.item=="") {
      if("{{old('contractor_id')}}"!=''){
        $(this).val("{{old('contractor')}}");
        $("#contractor_id").val("{{old('contractor_id')}}");
      }
      else{
        $(this).val('');
        $("#contractor_id").val('');
      }
    }
    else{
      var selected_contractor=ui.item;
      $("#contractor_id").val(selected_contractor.id);

    }

  }
}

$("#contractor").autocomplete(contractor_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});


var project_title_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_project_titles',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term,
        "mode_id":'2',
        "opening_date":$("#opening_date").val()
      },
      'method': "post",
      'dataType': "json",
      'success': function(data) {
        response(data);
      }
    });
  },
  select: function(event, ui){
    if(ui.item.id != ''){
      $(this).val(ui.item.value);
    }else{
      $(this).val('');
    }
    return false;
  },
  change: function (event, ui) {

    if (ui.item == null || ui.item=="") {
      if("{{old('plan_id')}}"!=''){
        $(this).val("{{old('plan_title')}}");
        $("#plan_id").val("{{old('plan_id')}}");
        $("#project_number").val("{{old('project_number')}}");
        $("#project_type").val("{{old('project_type')}}");
      }
      else{
        $(this).val('');
        $("#plan_id").val('');
        $("#project_number").val('');
        $("#project_type").val('');
      }
    }
    else{
      var selected_project=ui.item;
      $("#plan_id").val(selected_project.id);
      $("#project_number").val(selected_project.project_number);
      $("#project_type").val(selected_project.project_type);
    }

  }
}

$("#plan_title").autocomplete(project_title_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});


// show inputs/messages on load
var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs==1){
  // $('#filter').removeClass('d-none');
  // $('#filter_btn').removeClass('d-none');
  // $("#show_filter").html("Hide Filter");
}
else if (oldInputs==5) {
  $("#release_modal").modal('show');
}

else if (oldInputs>5) {
  $("#release_modal").modal('show');
}

else{
}

if(@json(old('year'))==null){
  $("#year").val("{{$year}}");
}


if("{{session('message')}}"){
  if("{{session('message')}}"=="duplicate"){
    swal.fire({
      title: `Duplicate`,
      text: 'We already have the same entry in the database!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-warning',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#release_modal").modal('hide');
  }
  else if("{{session('message')}}"=="delete_success"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Successfully deleted RFQ',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#release_modal").modal('hide');
  }

  else if("{{session('message')}}"=="bid_opening_done"){
    swal.fire({
      title: `Error`,
      text: 'Sorry, You cannot manipulate RFQ after Submission/Opening of bids!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }

  else if("{{session('message')}}"=="delete_error"){
    swal.fire({
      title: `Error`,
      text: 'You cannot delete Received RFQ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }

  else{
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact your system developer',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
}

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
      className: 'btn btn-sm shadow-0 border-0 bg-warning text-white filter_btn',
      action: function ( e, dt, node, config ) {
        $("#filter_rfq").submit();
      }
    },
    @if(in_array('add',$user_privilege))
    {
      text: 'Add Data',
      attr: {
        id: 'add_release'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-primary text-white add_release'
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
    }
  ],
  data:data,
  columns: [
    { "data":"plan_id",
    render: function ( data, type, row ) {
      return '<div style="white-space: nowrap"> @if(in_array("update",$user_privilege))<button class="btn btn-sm btn btn-sm shadow-0 border-0 text-white btn-success edit_btn" data-toggle="tooltip" data-placement="top" title="Edit" value="'+data+'"><i class="ni ni-ruler-pencil"></i></button>@endif @if(in_array("delete",$user_privilege))<button  class="btn btn-sm btn btn-sm shadow-0 border-0 btn-danger delete_btn" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button>@endif</div>';
    }
  },
  { "data": "rfq_id" },
  { "data": "current_cluster" },
  { "data": "project_no" },
  { "data": "app_group_no" },
  { "data": "bid_submission_start" },
  { "data": "date_released" },
  { "data": "business_name" },
  { "data": "project_title" },
  { "data": "municipality_name" },
  { "data": "contractor_id" },
  { "data": "owner" },
  { "data": "source" },
  { "data": "project_type" },
  { "data": "abc", render: function ( data, type, row ) {
    if(data!=null){
      return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    return "";}
  }
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
},{
  targets: [1,2,4,10,11,13],
  visible: false
} ],
order: [[ 1, "desc" ],[7 , "desc"]],
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
  dataSrc: 2
}
});



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

@if(in_array('add',$user_privilege))
$(".add_release").click(function functionName() {
  table.rows().deselect();
  $("#rfq_id").val('');
  $("#plan_id").val('');
  $("#plan_title").val('');
  $("#contractor_id").val('');
  $("#contractor").val('');
  $("#date_released").val('');
  $("#release_form")[0].reset();
  $("#release_modal_title").html("Release RFQ ");
  $("#date_released").datepicker('setDate',moment().format('MM/DD/YYYY'));
  $("#release_modal").modal('show');
});
@endif

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit_btn', function (e) {
  table.rows().deselect();
  var row=table.row($(this).parents('tr')).data();
  $("#release_modal_title").html("Update Release RFQ");
  $("#opening_date").datepicker('setDate',moment(row.bid_submission_start).format('MM/DD/YYYY'));
  $("#plan_id").val(row.plan_id);
  $("#rfq_id").val(row.rfq_id);
  $("#plan_title").val(row.project_title);
  $("#contractor_id").val(row.contractor_id);
  $("#project_number").val(row.project_no);
  $("#project_type").val(row.project_type);
  $("#contractor").val(row.business_name);
  $("#date_released").datepicker('setDate',moment(row.date_released).format('MM/DD/YYYY'));
  $("#release_modal").modal('show');
});
@endif

@if(in_array("delete",$user_privilege))
$('#app_table tbody').on('click', '.delete_btn', function (e) {
  var row=table.row($(this).parents('tr')).data();
  Swal.fire({
    text: 'Do you want to delete RFQ?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_rfq/"+row.rfq_id;
    }
  });

});
@endif


$("#year").change(function () {
  $("#filter_rfq").submit();
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


</script>
@endpush
