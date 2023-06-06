@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">
    <div class="modal" tabindex="-1" role="dialog" id="bac_modal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="bac_modal_title">Modal title</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="col-sm-12" method="POST" id="bac_form" action="submit_bac">
              @csrf
              <div class="row">
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="bac_id">bac ID</label>
                  <input type="text" id="bac_id" name="bac_id" class="form-control form-control-sm" readonly value="{{old('bac_id')}}" >
                  <label class="error-msg text-red" >@error('bac_id'){{$message}}@enderror</label>
                </div>
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="prefix">Prefix</label>
                  <input type="text" id="prefix" name="prefix" class="form-control form-control-sm" value="{{old('prefix')}}" >
                  <label class="error-msg text-red" >@error('prefix'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="first_name">First Name</label>
                  <input type="text" id="first_name" name="first_name" class="form-control form-control-sm" value="{{old('first_name')}}" >
                  <label class="error-msg text-red" >@error('first_name'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="middle_initial">Middle Initial</label>
                  <input type="text" id="middle_initial" name="middle_initial" class="form-control form-control-sm" value="{{old('middle_initial')}}" >
                  <label class="error-msg text-red" >@error('middle_initial'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="last_name">Last Name</label>
                  <input type="text" id="last_name" name="last_name" class="form-control form-control-sm" value="{{old('last_name')}}" >
                  <label class="error-msg text-red" >@error('last_name'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="suffix">Suffix</label>
                  <input type="text" id="suffix" name="suffix" class="form-control form-control-sm" value="{{old('suffix')}}" >
                  <label class="error-msg text-red" >@error('suffix'){{$message}}@enderror</label>
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

    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Bids and Awards Committee</h2>
        </div>
        <div class="card-body">

          <div class="table-responsive">
            <table class="table table-bordered" id="bac_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Start Date</th>
                  <th class="text-center">End Date</th>
                  <th class="text-center">BAC Infra Chairperson</th>
                  <th class="text-center">BAC Infra Vice-Chairperson</th>
                  <th class="text-center">BAC SEC chairman</th>
                  <th class="text-center">BAC SEC Vice-chairman</th>
                  <th class="text-center">BAC TWG chairman</th>
                  <th class="text-center">BAC TWG Vice-chairman</th>
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
// datatables
var  data = {!! json_encode($bac) !!};

$('#bac_table thead tr').clone(true).appendTo( '#bac_table thead' );
$('#bac_table thead tr:eq(1)').removeClass('bg-primary');


var table=  $('#bac_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  dom: 'Bfrtip',
  buttons: [
    @if(in_array("add",$user_privilege))
    {
      text: 'Add BAC',
      attr: {
        id: 'add_bac'
      },
      action: function ( e, dt, button, config ) {
        window.open("{{route('add_bac')}}", "_blank");
      } ,
      className: 'btn btn-sm shadow-0 border-0 bg-danger text-white add_bac'
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
  data:data,
  columns: [
    { "data": "bac_id","render": function ( data, type, row, meta ) {
      return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<a type="button" target="_blank" href="/edit_bac/'+data+'" class="btn btn-sm btn-success edit-btn" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="ni ni-ruler-pencil"></i></a>@endif  @if(in_array("delete",$user_privilege)) <button type="button" value="'+data+'" class="btn btn-sm btn-danger delete-btn" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button>@endif</div>';

    }},
    { "data": "bac_id" },
    { "data": "bac_start_date" },
    { "data": "bac_end_date" },
    { "data": "bac_chairman_name" },
    { "data": "bac_vice_chairman_name" },
    { "data": "bac_sec_chairman_name" },
    { "data": "bac_sec_vice_chairman_name" },
    { "data": "bac_twg_chairman_name" },
    { "data": "bac_twg_vice_chairman_name" },
  ],
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
});


var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>=2){
  $("#bac_modal").modal("show");
}

// messages
if("{{session('message')}}"){
  if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="duplicate"){
    swal.fire({
      title: `Duplicate`,
      text: 'Data already exist in the database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="delete_success"){
    swal.fire({
      title: `Success`,
      text: 'Data was deleted successfully',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="delete_error"){
    swal.fire({
      title: `Delete Error`,
      text: 'You cannot delete this BAC',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else{
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact system developer',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
}


// events

$('#bac_table thead tr:eq(1) th').each( function (i) {
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



// show delete
$('#bac_table tbody').on('click', '.delete-btn', function (e) {
  Swal.fire({
    text: 'Are you sure to delete this bac?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_bac/"+$(this).val();
    }
  });

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
