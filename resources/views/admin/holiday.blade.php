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
      <div class="modal" tabindex="-1" role="dialog" id="holiday_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="holiday_modal_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="holiday_form" action="{{route('submit_holiday')}}" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex">

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                    <label for="id">ID <span class="text-red">*</span></label>
                    <input  type="text" id="id" name="id" class="form-control form-control-sm" readonly value="{{old('id')}}" >
                    <label class="error-msg text-red" >@error('id'){{$message}}@enderror
                    </label>
                  </div>


                  <!-- Date Created -->
                  <div class="form-group col-xs-6 col-sm-12 col-lg-12">
                    <label for="holiday_date">Holiday Date <span class="text-red">*</span></label>
                    <input  type="text" id="holiday_date" name="holiday_date" class="form-control form-control-sm datepicker2" value="{{old('holiday_date')}}" >
                    <label class="error-msg text-red" >@error('holiday_date'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-12 col-lg-12">
                    <label for="holiday_name">Holiday Name<span class="text-red">*</span></label>
                    <input  type="text" id="holiday_name" name="holiday_name" class="form-control form-control-sm" value="{{old('holiday_name')}}" >
                    <label class="error-msg text-red" >@error('holiday_name'){{$message}}@enderror
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
          <h2 id="title">Holidays</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12" >
            <form class="row" id="filter" method="post" action="{{route('filter_holidays')}}">
              @csrf
              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="year" class="input-sm">Year
                </label>
                <input  class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}" >
                <label class="error-msg text-red" >@error('year'){{$message}}@enderror
                </label>
              </div>
            </form>
          </div>
          <div class="col-sm-12">
            {{-- <button class="btn btn-sm btn-primary text-white float-right mb-2 btn btn-sm ml-2 add_holiday">Add Data</button>
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
                  <th class="text-center">Holiday Date</th>
                  <th class="text-center">Holiday Name</th>
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

function HolidaySearch(json,search) {
  var data=json;
  return data.filter(
    function(data){ return data.id == search }
  );
}




// show inputs/messages on load
var oldInputs='{{ count(session()->getOldInput()) }}';



if (oldInputs>=5) {
  if("{{old('id')}}"){
    $("#holiday_modal_title").html("Edit Holiday ");
    $("#holiday_modal").modal('show');
  }
  else{
    $("#holiday_modal_title").html("Add Holiday ");
    $("#holiday_modal").modal('show');
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


  else if(@json(session('message'))=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#holiday_modal").modal('hide');
  }

  else if(@json(session('message'))=="delete_success"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Successfully deleted Holiday',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#holiday_modal").modal('hide');
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
var data = {!! json_encode(session('holidays')) !!};
if(data==null){
  data = {!! json_encode($holidays) !!};
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
      text: 'Add Holiday',
      attr: {
        id: 'add_holiday'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-primary text-white add_holiday'
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
      var  id=row.id;
      return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button class="btn btn-sm btn btn-sm shadow-0 border-0 text-white btn-success edit-btn" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="ni ni-ruler-pencil"></i></button>@endif @if(in_array("delete",$user_privilege))<button class="btn btn-sm btn btn-sm shadow-0 border-0 text-white btn-danger delete-btn" data-toggle="tooltip" data-placement="top" title="Delete" ><i class="ni ni-basket"></i></button>@endif</div>';
    }
  },
  { "data": "id" },
  { "data": "holiday_date" },
  { "data": "holiday_name" }
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

@if(in_array("add",$user_privilege))
$(".add_holiday").click(function functionName() {
  table.rows().deselect();
  $("#id").val('');
  $("#holidayname").val('');
  $("#holiday_date").val('');
  $("#plan_title").val('');
  $("#holiday_modal_title").html("Add Holiday ");
  $("#holiday_modal").modal('show');
});
@endif

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit-btn', function (e) {
  table.rows().deselect();
  var row=table.row($(this).parents('tr')).data();
  console.log(data);
  $("#id").val(row.id);
  $("#holiday_modal_title").html("Update Holiday");
  $("#holiday_date").datepicker('setDate',moment(row.holiday_date).format('MM/DD/YYYY'));
  $("#holiday_name").val(row.holiday_name);
  $("#holiday_modal").modal('show');
});
@endif

@if(in_array("delete",$user_privilege))
$('#app_table tbody').on('click', '.delete-btn', function (e) {
  var row=table.row($(this).parents('tr')).data();
  Swal.fire({
    text: 'Are you sure to delete this Holiday?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_holiday/"+row.id;
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
