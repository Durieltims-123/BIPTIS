@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="app">

    <div class="col-sm-12">
      <div class="modal" tabindex="-1" role="dialog" id="itb_modal">
        <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="itb_modal_title">Modal title</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="itb_form" action="submit_itb">
                @csrf
                <div class="d-flex">
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto">
                    <label for="date_itb_added">Plan Id/s</label>
                    <input type="text" id="plan_ids" name="plan_ids" class="form-control form-control-sm" readonly value="{{old('plan_ids')}}" >
                    <label class="error-msg text-red" >@error('plan_ids'){{$message}}@enderror</label>
                  </div>
                  <!-- date ITB added -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto">
                    <label for="date_itb_added">Date ITB Added </label>
                    <input type="text" id="date_itb_added" name="date_itb_added" class="form-control form-control-sm datepicker" value="{{old('date_itb_added')}}" >
                    <label class="error-msg text-red" >@error('date_itb_added'){{$message}}@enderror</label>
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
          <h2 id="title">Invitation to Bid</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12 d-none" id="filter">
            <form class="row" id="app_filter" method="post" action="{{route('filter_itb')}}">
              @csrf
              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="project_year" class="input-sm">Project Year </label>
                <input  class="form-control form-control-sm yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}" >
                <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror</label>
              </div>

              <!-- Month added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="month_added">Month Added </label>
                <input type="text" id="month_added" name="month_added" class="form-control form-control-sm monthpicker" value="{{old('month_added')}}" >
                <label class="error-msg text-red" >@error('month_added'){{$message}}@enderror</label>
              </div>

              <!-- date added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="date_added">Date Added </label>
                <input type="text" id="date_added" name="date_added" class="form-control form-control-sm datepicker" value="{{old('date_added')}}" >
                <label class="error-msg text-red" >@error('date_added'){{$message}}@enderror</label>
              </div>




            </form>
          </div>
          <div class="col-sm-12">
            <!-- <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Filter</button> -->
            <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button>
            <button class="btn btn-sm btn-warning text-white float-right mb-2 btn btn-sm ml-2 d-none" id="filter_btn">Filter</button>
            <button class="btn btn-sm btn-default text-white float-right mb-2 btn btn-sm ml-2" id="show_filter">Show Filter</button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Date ITB Added</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Approved Budget Cost</th>
                  <th class="text-center">Project Year</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Remarks</th>
                </tr>
              </thead>
              <tbody>
                @if(session('newData'))

                @foreach(session('newData') as $project_plan)
                <tr>

                  @if($project_plan->date_itb_added != null )
                  <td><button value='{{ $project_plan->plan_id }}'  class="btn btn-sm shadow-0 border-0 btn-success edit_itb">Edit</button><button value='{{ $project_plan->plan_id }}' class="btn btn-sm shadow-0 border-0 btn-danger delete_btn">delete</button></td>
                  @else
                  <td><button value='{{ $project_plan->plan_id }}'  class="btn btn-sm shadow-0 border-0 btn-primary add_itb">Add ITB</button></td>
                  @endif

                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>{{ $project_plan->date_itb_added }}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->abc }}</td>
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->status }}</td>
                  <td>{{ $project_plan->remarks }}</td>

                </tr>
                @endforeach



                @else
                @foreach($project_plans as $project_plan)
                <tr>

                  @if($project_plan->date_itb_added != null )
                  <td><button value='{{ $project_plan->plan_id }}'  class="btn btn-sm shadow-0 border-0 btn-success edit_itb">Edit</button><button value='{{ $project_plan->plan_id }}' class="btn btn-sm shadow-0 border-0 btn-danger delete_btn">delete</button></td>
                  @else
                  <td><button value='{{ $project_plan->plan_id }}'  class="btn btn-sm shadow-0 border-0 btn-primary add_itb">Add ITB</button></td>
                  @endif

                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>{{ $project_plan->date_itb_added }}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->abc }}</td>
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->status }}</td>
                  <td>{{ $project_plan->remarks }}</td>

                </tr>
                @endforeach
                @endif
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

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
  endDate:'{{$year}}'
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


// show inputs/messages on load
var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>=4){
  $('#filter').removeClass('d-none');
  $('#filter_btn').removeClass('d-none');
  $("#show_filter").html("Hide Filter");
}
else{
  $("#project_year").val("{{$year}}");
}
if(oldInputs==3){
  // $("#itb_modal").modal('show');
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
  }
  else if("{{session('message')}}"=="delete_success"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Successfully deleted ITB',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }

  else if("{{session('message')}}"=="multiple itb"){
    swal.fire({
      title: `Error`,
      text: 'Sorry, Some of the selected rows already have ITB!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'warning'
    });
  }

  else if("{{session('message')}}"=="all set"){
    swal.fire({
      title: `Error`,
      text: 'Sorry, All of the rows already have ITB!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'warning'
    });
  }

  else if("{{session('message')}}"=="delete_error"){
    swal.fire({
      title: `Error`,
      text: 'You cannot delete ITB for ongoing APP',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'warning'
    });
  }

  else{
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact your system developer',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'warning'
    });
  }
}

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

$(".add_itb").click(function functionName() {
  table.rows().deselect();
  $("#itb_form")[0].reset();
  $("#itb_modal_title").html("Add ITB");
  $("#plan_ids").val($(this).val());
  $("#date_itb_added").datepicker('setDate',moment().format('MM/DD/YYYY'));
  $("#itb_modal").modal('show');
});

$(".edit_itb").click(function functionName() {
  table.rows().deselect();
  $("#itb_modal_title").html("Update ITB");
  $("#plan_ids").val($(this).val());
  $("#date_itb_added").datepicker('setDate',moment(table.row($(this).parents('tr')).data()[4]).format('MM/DD/YYYY'));
  $("#itb_modal").modal('show');
});


// show delete Success
$(".delete_btn").click(function functionName() {

  Swal.fire({
    text: 'Do you want to delete ITB?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_itb/"+$(this).val();
    }
  });

});



// hide/show Filter
$("#show_filter").click(function() {
  if($(this).html()=="Show Filter"){
    $('#filter').removeClass('d-none');
    $('#filter_btn').removeClass('d-none');
    $("#show_filter").html("Hide Filter");
  }
  else{
    $('#filter').addClass('d-none');
    $('#filter_btn').addClass('d-none');
    $("#show_filter").html("Show Filter");
  }
});





$("#filter_btn").click(function () {
  $("#app_filter").submit();
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
