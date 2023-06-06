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
      <div class="modal" tabindex="-1" role="dialog" id="contractor_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="contractor_modal_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="contractor_form" action="submit_contractor">
                @csrf
                <div class="row">
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                    <label for="contractor_id">Contractor ID</label>
                    <input type="text" id="contractor_id" name="contractor_id" class="form-control form-control-sm" readonly value="{{old('contractor_id')}}" >
                    <label class="error-msg text-red" >@error('contractor_id'){{$message}}@enderror
                    </label>
                  </div>
                  <!-- Business Name -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="business_name">Business Name<span class='text-red'>*</span></label>
                    <input type="text" id="business_name" name="business_name" class="form-control form-control-sm" value="{{old('business_name')}}" >
                    <label class="error-msg text-red" >@error('business_name'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="owner">Owner<span class='text-red'>*</span></label>
                    <input type="text" id="owner" name="owner" class="form-control form-control-sm" value="{{old('owner')}}" >
                    <label class="error-msg text-red" >@error('owner'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="position">Position<span class='text-red'>*</span></label>
                    <input type="text" id="position" name="position" class="form-control form-control-sm" value="{{old('position')}}" >
                    <label class="error-msg text-red" >@error('position'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="address">Address<span class='text-red'>*</span></label>
                    <input type="text" id="address" name="address" class="form-control form-control-sm" value="{{old('address')}}" >
                    <label class="error-msg text-red" >@error('address'){{$message}}@enderror
                    </label>
                  </div>


                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="contact_number">Contact Number<span class='text-red'>*</span></label>
                    <input type="text" id="contact_number" name="contact_number" class="form-control form-control-sm" value="{{old('contact_number')}}" >
                    <label class="error-msg text-red" >@error('contact_number'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="email">Email<span class='text-red'>*</span></label>
                    <input type="text" id="email" name="email" class="form-control form-control-sm" value="{{old('email')}}" >
                    <label class="error-msg text-red" >@error('email'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="status">Status<span class='text-red'>*</span></label>
                    <select id="status" name="status" class="form-control form-control-sm" >
                      <option value="">Select a Status</option>
                      <option value="active" {{ old('status') == 'active' ? 'selected' : ''}}>Active</option>
                      <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : ''}}>Inactive</option>
                    </select>
                    <label class="error-msg text-red" >@error('status'){{$message}}@enderror
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
          <h2 id="title">Contractors</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12" >
            <form class="row" id="filter" method="post" action="{{route('filter_contractors')}}">
              @csrf
              <!-- contractor -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="filter_business_name" class="input-sm">Business Name</label>
                <input  class="form-control form-control-sm" id="filter_business_name" name="filter_business_name" format="yyyy" minimum-view="filter_business_name" value="{{old('filter_business_name')}}" >
                <label class="error-msg text-red" >@error('filter_business_name'){{$message}}@enderror
                </label>
              </div>
              <!-- filter_owner-->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 ">
                <label for="filter_owner">Owner</label>
                <input type="text" id="filter_owner" name="filter_owner" class="form-control form-control-sm" value="{{old('filter_owner')}}" >
                <label class="error-msg text-red" >@error('filter_owner'){{$message}}@enderror
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
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Position</th>
                  <th class="text-center">Address</th>
                  <th class="text-center">Contact Number</th>
                  <th class="text-center">Email</th>
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


$(".datepicker2").datepicker({
  format: 'mm/dd/yyyy',
});


$(".monthpicker").datepicker({
  format: 'mm-yyyy',
  startView: 'months',
  minViewMode: 'months',
});

function ContractorSearch(json,search) {
  var data=json;
  return data.filter(
    function(data){ return data.contractor_id == search }
  );
}


// show inputs/messages on load
var oldInputs='{{ count(session()->getOldInput()) }}';



if (oldInputs>=5) {
  if("{{old('contractor_id')}}"){
    $("#contractor_modal_title").html("Edit Contractor ");
    $("#contractor_modal").modal('show');
  }
  else{
    $("#contractor_modal_title").html("Add Contractor ");
    $("#contractor_modal").modal('show');
  }
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


var data = {!! json_encode(session('contractors')) !!};
if(data==null){
  data = {!! json_encode($contractors) !!};
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
      text: 'Add Contractor',
      attr: {
        id: 'add_contractor'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-primary text-white add_contractor'
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
      var  contractor_id=row.contractor_id;
      return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button class="btn btn-sm shadow-0 border-0 text-white btn-success edit-btn" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="ni ni-ruler-pencil"></i></button>@endif @if(in_array("delete",$user_privilege))<button  class="btn btn-sm shadow-0 border-0 btn-danger delete-btn" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button>@endif </div>';
    }
  },
  { "data":'contractor_id'},
  { "data":'business_name'},
  { "data":'owner'},
  { "data":'position'},
  { "data":'address'},
  { "data":'contact_number'},
  { "data":'email'},
  { "data":'status'},
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

$("#filter_business_name").change(function () {
  $("#filter").submit();
});

$("#filter_owner").change(function () {
  $("#filter").submit();
});

@if(in_array("add",$user_privilege))
$(".add_contractor").click(function functionName() {
  table.rows().deselect();
  $("#contractor_form")[0].reset();
  $("#contractor_modal_title").html("Add Contractor ");
  $("#contractor_modal").modal('show');
});
@endif

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit-btn', function (e) {
  table.rows().deselect();
  var row=table.row($(this).parents('tr')).data();
  $("#contractor_modal_title").html("Update Contractor");
  $("#contractor_form")[0].reset();
  $("#contractor_id").val(row.contractor_id);
  $("#business_name").val(row.business_name);
  $("#owner").val(row.owner);
  $("#position").val(row.position);
  $("#address").val(row.address);
  $("#contact_number").val(row.contact_number);
  $("#email").val(row.email);
  $("#status").val(row.status);
  $("#contractor_modal").modal('show');
});
@endif

@if(in_array("delete",$user_privilege))
$('#app_table tbody').on('click', '.delete-btn', function (e) {
  var row=table.row($(this).parents('tr')).data();
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
      window.location.href = "/delete_contractor/"+row.contractor_id;
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
