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
      <div class="modal" tabindex="-1" role="dialog" id="meeting_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="meeting_modal_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="meeting_form" action="{{route('submit_meeting')}}" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex">

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                    <label for="meeting_id">ID <span class="text-red">*</span></label>
                    <input  type="text" id="meeting_id" name="meeting_id" class="form-control form-control-sm" readonly value="{{old('meeting_id')}}" >
                    <label class="error-msg text-red" >@error('meeting_id'){{$message}}@enderror
                    </label>
                  </div>


                  <!-- Date Created -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="date_created">Date Created <span class="text-red">*</span></label>
                    <input  type="text" id="date_created" name="date_created" class="form-control form-control-sm datepicker2" value="{{old('date_created')}}" >
                    <label class="error-msg text-red" >@error('date_created'){{$message}}@enderror
                    </label>
                  </div>

                </div>
                <div class="row d-flex">
                  <!-- Meeting Date -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="meeting_date">Meeting Date <span class="text-red">*</span></label>
                    <input  type="text" id="meeting_date" name="meeting_date" class="form-control form-control-sm datepicker2" value="{{old('meeting_date')}}" >
                    <label class="error-msg text-red" >@error('meeting_date'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- Meeting Time -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="meeting_time">Meeting Time <span class="text-red">*</span></label>
                    <input  type="text" id="meeting_time" name="meeting_time" class="form-control form-control-sm timepicker" value="{{old('meeting_time')}}" >
                    <label class="error-msg text-red" >@error('meeting_time'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- Meeting Rooms -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="meeting_room">Meeting Room<span class="text-red">*</label>
                      <select class="form-control form-control-sm" name="meeting_room" id="meeting_room">
                        <option value=""></option>
                        @foreach($meeting_rooms as $meeting_room)
                        <option value="{{$meeting_room->meeting_room_id}}"  {{ old('meeting_room') == $meeting_room->meeting_room_id ? 'selected' : ''}} >{{$meeting_room->address}}</option>
                        @endforeach
                      </select>
                      <label class="error-msg text-red" >@error('meeting_room'){{$message}}@enderror
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
            <h2 id="title">Meetings</h2>
          </div>
          <div class="card-body">
            <div class="col-sm-12" >
              <form class="row" id="filter" method="post" action="{{route('filter_meetings')}}">
                @csrf
                <!-- project year -->
                <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                  <label for="year" class="input-sm">Year
                  </label>
                  <input  class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}" >
                  <label class="error-msg text-red" >@error('year'){{$message}}@enderror
                  </label>
                </div>


                <!-- meeting date -->
                <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 ">
                  <label for="filter_meeting_date">Meeting Date
                  </label>
                  <input type="text" id="filter_meeting_date" name="filter_meeting_date" class="form-control form-control-sm datepicker" value="{{old('filter_meeting_date')}}" >
                  <label class="error-msg text-red" >@error('filter_meeting_date'){{$message}}@enderror
                  </label>
                </div>

              </form>
            </div>
            <div class="col-sm-12">
              {{-- <button class="btn btn-sm btn-primary text-white float-right mb-2 btn btn-sm ml-2 add_meeting">Add Data</button>
              <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
              <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button>
              <button class="btn btn-sm btn-warning text-white float-right mb-2 btn btn-sm ml-2 " id="filter_btn">Filter</button>
              <button class="btn btn-sm btn-default text-white float-right mb-2 btn btn-sm ml-2" id="show_filter">Show Filter</button> --}}
            </div>
            <div class="table-responsive">
              <table class="table table-bordered" id="app_table">
                <thead class="">
                  <tr class="bg-primary text-white" >
                    <th class="text-center"></th>
                    <th class="text-center">ID</th>
                    <th class="text-center">Date Created</th>
                    <th class="text-center">Meeting Date</th>
                    <th class="text-center">Time</th>
                    <th class="text-center">Meeting Room ID</th>
                    <th class="text-center">Room</th>
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

  function MeetingSearch(json,search) {
    var data=json;
    return data.filter(
      function(data){ return data.meeting_id == search }
    );
  }

  $("#meeting_date").change(function () {
    $("#selected_plan_titles").html('');
    $("#plan_ids").val('');
    $("#plan_title").val('');
  });


  // show inputs/messages on load
  var oldInputs='{{ count(session()->getOldInput()) }}';



  if (oldInputs>=5) {
    if("{{old('meeting_id')}}"){
      $("#meeting_modal_title").html("Edit Meeting ");
      $("#meeting_modal").modal('show');
    }
    else{
      $("#meeting_modal_title").html("Add Meeting ");
      $("#meeting_modal").modal('show');
    }
  }

  if(@json(old('year'))!=null){
    $("#year").val(@json(old('year')));
  }
  else{
    $("#year").val(@json($year));
  }

  if(@json(session('message'))){
    if(@json(session('message'))=="duplicate"){
      swal.fire({
        title: `Duplicate`,
        text: 'We already have the same entry in the database!',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else if(@json(session('message'))=="delete_error"){
      swal.fire({
        title: `Meeting Attachment Error`,
        text: 'Please Delete Meeting attachment on Archives to Delete Meeting. ',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }

    else if(@json(session('message'))=="success"){
      swal.fire({
        title: `Success`,
        text: 'Successfully saved to database',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-success',
        icon: 'success'
      });
      $("#meeting_modal").modal('hide');
    }

    else if(@json(session('message'))=="delete_success"){
      Swal.fire({
        title: `Delete Success`,
        text: 'Successfully deleted Meeting',
        confirmButtonText: 'Ok',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-success',
        icon: 'success'
      });
      $("#meeting_modal").modal('hide');
    }


    else if(@json(session('message'))=="delete_error"){
      swal.fire({
        title: `Error`,
        text: 'You cannot delete this data',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-danger',
        icon: 'warning'
      });
    }

    else if(@json(session('message'))=="no_activities"){
      swal.fire({
        title: `Error`,
        text: 'Sorry! There were no scheduled activities for the chosen meeting date',
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
  var data = {!! json_encode(session('meetings')) !!};
  if(data==null){
    data = {!! json_encode($meetings) !!};
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
          $("#filter").submit();
        }
      },
      @if(in_array("add",$user_privilege))
      {
        text: 'Add Meeting',
        attr: {
          id: 'add_meeting'
        },
        className: 'btn btn-sm shadow-0 border-0 bg-primary text-white add_meeting'
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
      { "data":"",
      render: function ( data, type, row ) {
        var  meeting_id=row.meeting_id;
        return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button class="btn btn-sm btn btn-sm shadow-0 border-0 text-white btn-success edit-btn" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="ni ni-ruler-pencil"></i></button>@endif @if(in_array("delete",$user_privilege))<button  class="btn btn-sm btn btn-sm shadow-0 border-0 btn-danger delete-btn" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button>@endif @if(in_array("update",$user_privilege))<a href="/release_notice_of_meeting/'+meeting_id+'" target="__blank" data-toggle="tooltip" data-placement="top" title="Release" class="btn btn-sm btn btn-sm btn-warning"><i class="ni ni-single-02"></i></a><a  class="btn btn-sm btn btn-sm shadow-0 border-0 btn-primary text-white" target="_blank" data-toggle="tooltip" data-placement="top" title="Download"  href="/download_notice_of_meeting/'+meeting_id+'"><i class="ni ni-cloud-download-95"></i></a>@endif</div>';
      }
    },
    { "data": "meeting_id" },
    { "data": "meeting_date_created" },
    { "data": "meeting_date" },
    { "data": "meeting_time",render:function ( data, type, row ) {
      return moment(data,"HH:mm").format('HH:mm a');
    } },
    { "data": "meeting_room_id" },
    { "data": "address" }
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

$("#year").change(function () {
  $("#filter").submit();
});

$("#filter_meeting_date").change(function () {
  $("#filter").submit();
});

@if(in_array("add",$user_privilege))
$(".add_meeting").click(function functionName() {
  table.rows().deselect();
  $("#title").val('');
  $("#meeting_date").val('');
  $("#date_created").val('');
  $("#plan_title").val('');
  $("#selected_plan_titles").html('');
  $("#plan_ids").val('');
  $("#meeting_modal_title").html("Add Meeting ");
  $("#meeting_modal").modal('show');
});
@endif

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit-btn', function (e) {
  table.rows().deselect();
  var row=table.row($(this).parents('tr')).data();
  $("#meeting_modal_title").html("Update Meeting");
  $("#meeting_date").datepicker('setDate',moment(row.meeting_date).format('MM/DD/YYYY'));
  $("#date_created").datepicker('setDate',moment(row.meeting_date_created).format('MM/DD/YYYY'));
  $("#meeting_id").val(row.meeting_id);
  var time=row.meeting_time;
  time=time.split(":");
  var desired_format=time[0]+":"+time[1];
  $("#meeting_time").val(moment(row.meeting_time,"hh:mm a").format('HH:mm'));
  $("#meeting_room").val(row.meeting_room_id);
  $("#meeting_modal").modal('show');
});
@endif

@if(in_array("delete",$user_privilege))
$('#app_table tbody').on('click', '.delete-btn', function (e) {
  var row=table.row($(this).parents('tr')).data();
  Swal.fire({
    text: 'Are you sure to delete this Meeting?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_meeting/"+row.meeting_id;
    }
  });

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
