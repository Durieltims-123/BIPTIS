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
      <div class="modal" tabindex="-1" role="dialog" id="receive_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="receive_modal_title">Receive RFQ </h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="release_form" action="submit_receive_rfq">
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
                    <input type="text" id="plan_id" name="plan_id" class="form-control form-control-sm" value="{{old('plan_id')}}" readonly>
                    <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- project_title -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="plan_title">Project Title <span class="text-red">*</span>
                    </label>
                    <input list="titles" type="text" id="plan_title" name="plan_title" class="form-control form-control-sm" value="{{old('plan_title')}}" readonly>
                    <label class="error-msg text-red" >@error('plan_title'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="project_number">Project Number  <span class="text-red">*</span>
                    </label>
                    <input type="text" id="project_number" name="project_number" class="form-control form-control-sm" value="{{old('project_number')}}" readonly>
                    <label class="error-msg text-red" >@error('project_number'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="project_type">Project Type  <span class="text-red">*</span>
                    </label>
                    <input type="text" id="project_type" name="project_type" class="form-control form-control-sm" value="{{old('project_type')}}" readonly>
                    <label class="error-msg text-red" >@error('project_type'){{$message}}@enderror
                    </label>
                  </div>


                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <label for="contractor_id">Contractor ID
                    </label>
                    <input type="text" id="contractor_id" name="contractor_id" class="form-control form-control-sm" value="{{old('contractor_id')}}" readonly>
                    <label class="error-msg text-red" >@error('contractor_id'){{$message}}@enderror
                    </label>
                  </div>


                  <!-- Contractor  -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 ui-widget">
                    <label for="contractor">Contractor  <span class="text-red">*</span>
                    </label>
                    <input type="text" id="contractor" name="contractor" class="form-control form-control-sm" value="{{old('contractor')}}" readonly >
                    <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror
                    </label>
                  </div>


                  <!-- date released -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 ">
                    <label for="date_released">Date RFQ Released <span class="text-red">*</span>
                    </label>
                    <input type="text" id="date_released" name="date_released" class="form-control form-control-sm datepicker" value="{{old('date_released')}}" readonly>
                    <label class="error-msg text-red" >@error('date_released'){{$message}}@enderror
                    </label>
                  </div>


                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 ">
                    <label for="date_received">Date  Received <span class="text-red">*</span>
                    </label>
                    <input type="text" id="date_received" name="date_received" class="form-control form-control-sm datepicker" value="{{old('date_received')}}" >
                    <label class="error-msg text-red" >@error('date_received'){{$message}}@enderror
                    </label>
                  </div>


                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 ">
                    <label for="time_received">Time Received <span class="text-red">*</span>
                    </label>
                    <input type="text" id="time_received" name="time_received" class="form-control form-control-sm timepicker bg-white" readonly value="{{old('time_received')}}" >
                    <label class="error-msg text-red" >@error('time_received'){{$message}}@enderror
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
            <form class="row" id="filter_rfq" method="post" action="{{ route('filter_rfq') }}">
              @csrf
              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="year" class="input-sm">Rleased Year</label>
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
                  <th class="text-center">Date Received</th>
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
else{
  $("#year").val("{{$year}}");
}

// datatables
$('#app_table thead tr').clone(true).appendTo( '#app_table thead' );
$('#app_table thead tr:eq(1)').removeClass('bg-primary');

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
  endDate:'{{$year}}',
  ignoreReadonly: true
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

$('.timepicker').timepicker({
  showRightIcon: false,
  showOnFocus: true,
});

// show inputs/messages on load
var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs==1){
  // $('#filter').removeClass('d-none');
  // $('#filter_btn').removeClass('d-none');
  // $("#show_filter").html("Hide Filter");
}
else if (oldInputs==5) {
  $("#receive_modal").modal('show');
}

else if (oldInputs>5) {
  $("#receive_modal").modal('show');
}

else{
}

console.log("{{session('message')}}");


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
    $("#receive_modal").modal('hide');
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
    $("#receive_modal").modal('hide');
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

  else if("{{session('message')}}"=="update_error"){
    swal.fire({
      title: `Error`,
      text: 'You cannot Update this RFQ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }

  else if("{{session('message')}}"=="delete_error"){
    swal.fire({
      title: `Error`,
      text: 'You cannot delete RFQ for Received RFQ',
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
      if(row.date_received==null){
        return '@if(in_array("receive",$user_privilege))<div style="white-space: nowrap"><button class="btn btn-sm btn btn-sm shadow-0 border-0 text-white btn-primary receive_btn" data-toggle="tooltip" data-placement="top" title="Receive" value="'+data+'"><i class="ni ni-box-2"></i></button></div>@endif';
      }
      else{
        return '@if(in_array("update",$user_privilege))<div style="white-space: nowrap"><button class="btn btn-sm btn btn-sm shadow-0 border-0 text-white btn-success edit_btn" data-toggle="tooltip" data-placement="top" title="Edit" value="'+data+'"><i class="ni ni-ruler-pencil"></i></button></div>@endif';
      }}
    },
    { "data": "rfq_id" },
    { "data": "current_cluster" },
    { "data": "project_no" },
    { "data": "app_group_no" },
    { "data": "bid_submission_start" },
    { "data": "date_released" },
    { "data": "date_received" },
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
    targets: [1,2,4,11,12,14],
    visible: false
  }],
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

$("#year").change(function () {
  console.log("test");
  $("#filter_rfq").submit();
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

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit_btn', function (e) {
  table.rows().deselect();
  $("#receive_modal_title").html("Receive RFQ");
  var row=table.row($(this).parents('tr')).data();
  $("#plan_id").val(row.plan_id);
  $("#rfq_id").val(row.rfq_id);
  $("#project_number").val(row.project_no);
  $("#project_type").val(row.project_type);
  $("#plan_title").val(row.project_title);
  $("#contractor_id").val(row.contractor_id);
  $("#contractor").val(row.business_name);
  $("#date_received").datepicker('setDate',moment(row.date_received).format('MM/DD/YYYY'));
  $("#time_received").val(moment(row.time_received,"hh:mm a").format('HH:mm'));
  $("#date_released").val(moment(row.date_released).format('MM/DD/YYYY'));
  $("#receive_modal").modal('show');
});
@endif

@if(in_array("receive",$user_privilege))
$('#app_table tbody').on('click', '.receive_btn', function (e) {
  table.rows().deselect();
  $("#receive_modal_title").html("Receive RFQ");
  table.rows().deselect();
  $("#receive_modal_title").html("Receive RFQ");
  var row=table.row($(this).parents('tr')).data();
  $("#plan_id").val(row.plan_id);
  $("#rfq_id").val(row.rfq_id);
  $("#project_number").val(row.project_no);
  $("#project_type").val(row.project_type);
  $("#plan_title").val(row.project_title);
  $("#contractor_id").val(row.contractor_id);
  $("#contractor").val(row.business_name);
  $("#date_received").val('');
  $("#time_received").val('');
  $("#date_released").val(moment(row.date_released).format('MM/DD/YYYY'));
  $("#receive_modal").modal('show');
});
@endif

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
