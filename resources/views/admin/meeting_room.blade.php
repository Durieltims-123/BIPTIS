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
      <div class="modal" tabindex="-1" role="dialog" id="meeting_room_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="meeting_room_modal_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="meeting_form" action="{{route('submit_meeting_room')}}" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex">

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                    <label for="meeting_room_id">ID <span class="text-red">*</span></label>
                    <input  type="text" id="meeting_room_id" name="meeting_room_id" class="form-control form-control-sm" readonly value="{{old('meeting_room_id')}}" >
                    <label class="error-msg text-red" >@error('meeting_room_id'){{$message}}@enderror</label>
                  </div>


                  <!-- Address -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="address">Address<span class="text-red">*</span></label>
                    <input  type="text" id="address" name="address" class="form-control form-control-sm" value="{{old('address')}}" >
                    <label class="error-msg text-red" >@error('address'){{$message}}@enderror</label>
                  </div>

                  <!-- Status -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="status">Status <span class="text-red">*</span></label>
                    <select  type="text" id="status" name="status" class="form-control form-control-sm" value="{{old('status')}}" >
                      <option value="1" {{ old('status') == "1" ? 'selected' : ''}}>Active</option>
                      <option value="0" {{ old('status') == "0" ? 'selected' : ''}}>Disabled</option>
                    </select>
                    <label class="error-msg text-red" >@error('status'){{$message}}@enderror</label>
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
          <h2 id="title">Meeting Rooms</h2>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Address</th>
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

$('.timepicker').timepicker({
  showRightIcon: false,
  showOnFocus: true,
});


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

function MeetingBidSearch(json,search) {
  var data=json;
  return data.filter(
    function(data){ return data.meeting_room_id == search }
  );
}


$("#meeting_date").change(function () {
  $("#selected_plan_titles").html('');
  $("#plan_ids").val('');
  $("#plan_title").val('');
});


// show inputs/messages on load
var oldInputs='{{ count(session()->getOldInput()) }}';

if (oldInputs>=2) {
  if("{{old('meeting_room_id')}}"){
    $("#meeting_room_modal_title").html("Edit Meeting Room ");
    $("#meeting_room_modal").modal('show');
  }
  else{
    $("#meeting_room_modal_title").html("Add Meeting Room");
    $("#meeting_room_modal").modal('show');
  }
}

else{
  $("#project_year").val("{{$year}}");
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
    $("#meeting_room_modal").modal('hide');
  }

  else if("{{session('message')}}"=="delete_success"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Successfully deleted Meeting Room',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#meeting_room_modal").modal('hide');
  }


  else if("{{session('message')}}"=="delete_error"){
    swal.fire({
      title: `Error`,
      text: 'You cannot deletethis data',
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

var  data = {!! json_encode($meeting_rooms) !!};


var table=  $('#app_table').DataTable({
  dom: 'Bfrtip',
  buttons: [
    @if(in_array("add",$user_privilege))
    {
      text: 'Add Meeting Room',
      attr: {
        id: 'add_meeting_room'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-danger text-white add_source'
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
  } ],
  columns: [
    { "data": "meeting_room_id",render: function ( data, type, row ) {
      return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button type="button" class="btn btn-sm btn btn-sm btn-success edit-btn" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="ni ni-ruler-pencil"></i></button>@endif  @if(in_array("delete",$user_privilege)) <button type="button" value="'+data+'" class="btn btn-sm btn btn-sm btn-danger delete-btn" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button>@endif</div>';
    }},
    { "data": "meeting_room_id" },
    { "data": "address" },
    { "data": "status" }
  ],
  order: [[ 1, "desc" ]]
});



$('#app_table thead tr:eq(1) th').each( function (i) {
  var title = $(this).text();
  if(title!=""){
    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    $(this).addClass('sorting_disabled');
    $( 'input', this ).on( 'keyup change', function () {
      if ( table.column(i).search() !== this.value ) {
        table
        .column(i)
        .search( this.value )
        .draw();
      }
    } );
  }
});
@if(in_array("add",$user_privilege))
$("#add_meeting_room").click(function functionName() {
  table.rows().deselect();
  $("#title").val('');
  $("#meeting_date").val('');
  $("#address").val('');
  $("#plan_title").val('');
  $("#selected_plan_titles").html('');
  $("#plan_ids").val('');
  $("#meeting_room_modal_title").html("Add Meeting Room");
  $("#meeting_room_modal").modal('show');
});
@endif

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit-btn', function (e) {
  table.rows().deselect();
  var row=table.row($(this).parents('tr')).data();
  $("#meeting_room_modal_title").html("Update Meeting Room");
  $("#address").val(row.address);
  $("#status").val(row.status);
  $("#meeting_room_id").val(row.meeting_room_id);
  $("#meeting_room_modal").modal('show');
});
@endif

@if(in_array("delete",$user_privilege))
$('#app_table tbody').on('click', '.delete-btn', function (e) {
  var row=table.row($(this).parents('tr')).data();
  Swal.fire({
    text: 'Are you sure to delete this Meeting Room?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_meeting_room/"+row.meeting_room_id;
    }
  });

});
@endif

// hide/show Filter
$("#show_filter").click(function() {
  if($(this).html()=="Show Filter"){
    $('#filter').removeClass('');
    $('#filter_btn').removeClass('');
    $("#show_filter").html("Hide Filter");
  }
  else{
    $('#filter').addClass('');
    $('#filter_btn').addClass('');
    $("#show_filter").html("Show Filter");
  }
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
