@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">
    <div class="modal" tabindex="-1" role="dialog" id="contractor_modal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="contractor_modal_title">Modal title</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="col-sm-12" method="POST" id="contractors_form" action="submit_contractor">
              @csrf
              <div class="row">
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="contractor_id">Contractor ID</label>
                  <input type="text" id="contractor_id" name="contractor_id" class="form-control form-control-sm" readonly value="{{old('contractor_id')}}" >
                  <label class="error-msg text-red" >@error('contractor_id'){{$message}}@enderror</label>
                </div>
                <!-- Business Name -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="business_name">Business Name</label>
                  <input type="text" id="business_name" name="business_name" class="form-control form-control-sm" value="{{old('business_name')}}" >
                  <label class="error-msg text-red" >@error('business_name'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="owner">Owner</label>
                  <input type="text" id="owner" name="owner" class="form-control form-control-sm" value="{{old('owner')}}" >
                  <label class="error-msg text-red" >@error('owner'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="position">Position</label>
                  <input type="text" id="position" name="position" class="form-control form-control-sm" value="{{old('position')}}" >
                  <label class="error-msg text-red" >@error('position'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="address">Address</label>
                  <input type="text" id="address" name="address" class="form-control form-control-sm" value="{{old('address')}}" >
                  <label class="error-msg text-red" >@error('address'){{$message}}@enderror</label>
                </div>


                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="contact_number">Contact Number</label>
                  <input type="text" id="contact_number" name="contact_number" class="form-control form-control-sm" value="{{old('contact_number')}}" >
                  <label class="error-msg text-red" >@error('contact_number'){{$message}}@enderror</label>
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
          <h2 id="title">Contractors</h2>
        </div>
        <div class="card-body">

          <div class="col-sm-12">
            <button  target='_blank' class="btn btn-sm btn-danger text-white float-right mb-2 btn btn-sm" id="add_contractor" ><i class="ni ni-fat-add">Add Contractor</i> </button>
            <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Position</th>
                  <th class="text-center">Address</th>
                  <th class="text-center">Contact Number</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($contractors as $contractor)
                <tr>
                  <td><button type="button" class="btn btn-sm btn-primary edit-btn">Edit</button><button type="button" value="{{$contractor->contractor_id}}" class="btn btn-sm btn-danger delete-btn">delete</button></td>
                  <td>{{$contractor->contractor_id}}</td>
                  <td>{{$contractor->business_name}}</td>
                  <td>{{$contractor->owner}}</td>
                  <td>{{$contractor->position}}</td>
                  <td>{{$contractor->address}}</td>
                  <td>{{$contractor->contact_number}}</td>
                  <td>{{$contractor->status}}</td>
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
$('#app_table thead tr').clone(true).appendTo( '#app_table thead' );
$('#app_table thead tr:eq(1)').removeClass('bg-primary');


var table=  $('#app_table').DataTable({
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
  $("#contractor_modal").modal("show");
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
      text: 'You cannot delete contractor who are project bidders',
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



// show delete
$('#app_table tbody').on('click', '.delete-btn', function (e) {
  Swal.fire({
    text: 'Are you sure to delete this Contractor?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_contractor/"+$(this).val();
    }
  });

});

// add button

$("#add_contractor").click(function () {
  $("#contractors_form")[0].reset();
  $("#contractor_modal_title").html("Add Contractor");
  $("#contractor_modal").modal("show");
});

// edit button
$('#app_table tbody').on('click', '.edit-btn', function (e) {
  $("#contractors_form")[0].reset();
  $("#contractor_modal_title").html("Edit Contractor");
  table.rows().deselect();
  var data = table.row( $(this).parents('tr') ).data();
  $("#contractor_id").val(data[1]);
  $("#business_name").val(data[2]);
  $("#owner").val(data[3]);
  $("#position").val(data[4]);
  $("#address").val(data[5]);
  $("#contact_number").val(data[6]);
  $("#status").val(data[7]);
  $("#contractor_modal").modal("show");
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
