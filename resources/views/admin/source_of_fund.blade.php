@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">
    <div class="modal" tabindex="-1" role="dialog" id="source_modal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="source_modal_title">Source of Fund</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="col-sm-12" method="POST" id="source_form" action="submit_source_of_fund">
              @csrf
              <div class="row">
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="source_id">Source ID</label>
                  <input type="text" id="source_id" name="source_id" class="form-control form-control-sm" readonly value="{{old('source_id')}}" >
                  <label class="error-msg text-red" >@error('source_id'){{$message}}@enderror</label>
                </div>
                <!--  Source -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="source">Source of Fund </label>
                  <input type="text" id="source" name="source" class="form-control form-control-sm" value="{{old('source')}}" >
                  <label class="error-msg text-red" >@error('source'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="status">Fund Category</label>
                  <select id="fund_category" name="fund_category" class="form-control form-control-sm" >
                    <option value="">Select a Category</option>
                    @foreach($fund_categories as $value)
                    <option value="{{$value->fund_category_id}}" >{{$value->title}}</option>
                    @endforeach
                  </select>
                  <label class="error-msg text-red" >@error('fund_category'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="status">Status</label>
                  <select id="status" name="status" class="form-control form-control-sm" >
                    <option value="">Select a Status</option>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : ''}}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : ''}}>Inactive</option>
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

    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Sources of Funds</h2>
        </div>
        <div class="card-body">

          <div class="table-responsive">
            <table class="table table-bordered" id="data_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Source</th>
                  <th class="text-center">Category ID</th>
                  <th class="text-center">Category</th>
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
// datatables
$('#data_table thead tr').clone(true).appendTo( '#data_table thead' );
$('#data_table thead tr:eq(1)').removeClass('bg-primary');

let data=@json($source_of_funds);
var table=  $('#data_table').DataTable({
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
      text: 'Add Source',
      attr: {
        id: 'add_source'
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
  columns: [
    { "data": "fund_id",render: function ( data, type, row ) {
      return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button type="button" class="btn btn-sm btn btn-sm btn-success edit-btn" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="ni ni-ruler-pencil"></i></button>@endif  @if(in_array("delete",$user_privilege)) <button type="button" value="'+data+'" class="btn btn-sm btn btn-sm btn-danger delete-btn" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button>@endif</div>';
    }},
    { "data":"fund_id"},
    { "data":"source"},
    { "data":"fund_category_id"},
    { "data":"title"},
    { "data":"fund_status"}

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
  $("#source_modal").modal("show");
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
      text: 'You cannot delete this Source Fund',
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

$('#data_table thead tr:eq(1) th').each( function (i) {
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
@if(in_array("delete",$user_privilege))
$('#data_table tbody').on('click', '.delete-btn', function (e) {

  Swal.fire({
    text: 'Are you sure to delete this Source of Fund?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_source/"+$(this).val();
    }
  });

});
@endif
// add button
@if(in_array("add",$user_privilege))
$("#add_source").click(function () {
  $("#source_form")[0].reset();
  $("#source_modal_title").html("Add Source of Fund");
  $("#source_modal").modal("show");
});
@endif

@if(in_array("update",$user_privilege))
// edit button
$('#data_table tbody').on('click', '.edit-btn', function (e) {
  $("#source_form")[0].reset();
  $("#source_modal_title").html("Edit Source of Fund");
  table.rows().deselect();
  var data=table.row($(this).parents('tr')).data();
  $("#source_id").val(data.fund_id);
  $("#source").val(data.source);
  $("#fund_category").val(data.fund_category_id);
  $("#status").val(data.fund_status);
  $("#source_modal").modal("show");
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
