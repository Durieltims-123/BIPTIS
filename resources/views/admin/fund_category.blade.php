@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">
    <div class="modal" tabindex="-1" role="dialog" id="fund_category_modal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="fund_category_modal_title">Fund Category</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="col-sm-12" method="POST" id="fund_category_form" action="submit_fund_category">
              @csrf
              <div class="row">
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="fund_category_id">fund_category ID</label>
                  <input type="text" id="fund_category_id" name="fund_category_id" class="form-control form-control-sm" readonly value="{{old('fund_category_id')}}" >
                  <label class="error-msg text-red" >@error('fund_category_id'){{$message}}@enderror</label>
                </div>
                <!--  fund_category -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="title">Title</label>
                  <input type="text" id="title" name="title" class="form-control form-control-sm" value="{{old('title')}}" >
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
          <h2 id="title">Fund Categories</h2>
        </div>
        <div class="card-body">

          <div class="col-sm-12">
            <button  target='_blank' class="btn btn-sm btn-danger text-white float-right mb-2 btn btn-sm" id="add_title" ><i class="ni ni-fat-add">Add Fund Category</i> </button>
            <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered" id="data_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Title</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($fund_categories as $fund_category_of_fund)
                <tr>
                  <td><button type="button" class="btn btn-sm btn btn-sm btn-primary edit-btn">Edit</button><button type="button" value="{{$fund_category_of_fund->fund_category_id}}" class="btn btn-sm btn btn-sm btn-danger delete-btn">delete</button></td>
                  <td>{{$fund_category_of_fund->fund_category_id}}</td>
                  <td>{{$fund_category_of_fund->title}}</td>
                  <td>{{$fund_category_of_fund->status}}</td>
                </tr>
                @endforeach
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


var table=  $('#data_table').DataTable({
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
});


var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>=2){
  $("#fund_category_modal").modal("show");
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
      text: 'You cannot delete this fund_category Fund',
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
$('#data_table tbody').on('click', '.delete-btn', function (e) {

  Swal.fire({
    text: 'Are you sure to delete this Fund Category?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_fund_category/"+$(this).val();
    }
  });

});

// add button

$("#add_title").click(function () {
  $("#fund_category_form")[0].reset();
  $("#fund_category_modal_title").html("Add Fund Category");
  $("#fund_category_modal").modal("show");
});

// edit button
$('#data_table tbody').on('click', '.edit-btn', function (e) {
  $("#fund_category_form")[0].reset();
  $("#fund_category_modal_title").html("Edit Fund Category");
  table.rows().deselect();
  var data = table.row( $(this).parents('tr') ).data();
  $("#fund_category_id").val(data[1]);
  $("#title").val(data[2]);
  $("#status").val(data[3]);
  $("#fund_category_modal").modal("show");
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
