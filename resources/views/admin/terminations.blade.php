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
      <div class="modal" tabindex="-1" role="dialog" id="termination_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="termination_modal_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="meeting_form" action="{{route('submit_termination')}}" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex">

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                    <label for="termination_id">ID <span class="text-red">*</span></label>
                    <input  type="text" id="termination_id" name="termination_id" class="form-control form-control-sm" readonly value="{{old('termination_id')}}" >
                    <label class="error-msg text-red" >@error('termination_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                    <label for="procact_id">Procact ID<span class="text-red">*</span></label>
                    <input  type="text" id="procact_id" name="procact_id" class="form-control form-control-sm" readonly  value="{{old('procact_id')}}" >
                    <label class="error-msg text-red" >@error('procact_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="project_title">Project Title<span class="text-red">*</span></label>
                    <input  type="text" id="project_title" name="project_title" class="form-control form-control-sm" value="{{old('project_title')}}" >
                    <label class="error-msg text-red" >@error('project_title'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                    <label for="project_bid">Project Bidder ID<span class="text-red">*</span></label>
                    <input  type="text" id="project_bid" name="project_bid" class="form-control form-control-sm" readonly value="{{old('project_bid')}}" >
                    <label class="error-msg text-red" >@error('project_bid'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="contractor">Contractor<span class="text-red">*</span></label>
                    <input  type="text" id="contractor" name="contractor" class="form-control form-control-sm" readonly value="{{old('contractor')}}" >
                    <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror
                    </label>
                  </div>
                </div>
                <div class="row d-flex">
                  <!-- Meeting Rooms -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="governor">Governor<span class="text-red">*</label>
                      <select class="form-control form-control-sm" name="governor" id="governor">
                        <option value=""></option>
                        @foreach($governors as $governor)
                        <option value="{{$governor->governor_id}}"  {{ old('governor') == $governor->governor_id ?'selected' :''}} >{{$governor->name}}</option>
                        @endforeach
                      </select>
                      <label class="error-msg text-red" >@error('governor'){{$message}}@enderror
                      </label>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                      <label for="reason">Reason<span class="text-red">*</span></label>
                      <textarea  type="text" id="reason" name="reason" class="form-control form-control-sm" >
                      </textarea>
                      <label class="error-msg text-red" >@error('reason'){{$message}}@enderror
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
            <h2 id="title">Mutual Termination of Contract</h2>
          </div>
          <div class="card-body">
            <div class="col-sm-12" >
              <form class="row" id="filter" method="post" action="{{route('filter_termination_of_contract')}}">
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
              {{-- <button class="btn btn-sm btn-primary text-white float-right mb-2 btn btn-sm ml-2 add_btn">Add Data</button>
              <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
              <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button>
              <button class="btn btn-sm btn-warning text-white float-right mb-2 btn btn-sm ml-2" id="filter_btn">Filter</button>
              <button class="btn btn-sm btn-default text-white float-right mb-2 btn btn-sm ml-2" id="show_filter">Show Filter</button> --}}
            </div>
            <div class="table-responsive">
              <table class="table table-bordered" id="app_table">
                <thead class="">
                  <tr class="bg-primary text-white" >
                    <th class="text-center"></th>
                    <th class="text-center">ID</th>
                    <th class="text-center">Project Number</th>
                    <th class="text-center">Procact ID</th>
                    <th class="text-center">Project Title</th>
                    <th class="text-center">Project Bid</th>
                    <th class="text-center">Contractor</th>
                    <th class="text-center">Governor id</th>
                    <th class="text-center">Governor</th>
                    <th class="text-center">Reason</th>
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

  $("#reason").html('');

  // datatables
  $('#app_table thead tr').clone(true).appendTo('#app_table thead' );
  $('#app_table thead tr:eq(1)').removeClass('bg-primary');

  $(".datepicker").datepicker({
    format:'mm/dd/yyyy',
    endDate:'{{$year}}'
  });

  $(".datepicker2").datepicker({
    format:'mm/dd/yyyy',
  });

  $(".yearpicker").datepicker({
    format:'yyyy',
    viewMode:"years",
    minViewMode:"years"
  });

  $(".monthpicker").datepicker({
    format:'mm-yyyy',
    startView:'months',
    minViewMode:'months',
  });


  // show inputs/messages on load
  var oldInputs='{{ count(session()->getOldInput()) }}';
  if (oldInputs>=3) {
    if("{{old('termination_id')}}"){
      $("#termination_modal_title").html("Edit Mutual Contract Termination");
      $("#project_title").prop('readonly',true);
      $("#termination_modal").modal('show');
    }
    else{
      $("#termination_modal_title").html("Add Mutual Contract Termination");
      $("#project_title").prop('readonly',false);
      $("#termination_modal").modal('show');
    }
    $("#reason").html("{{old('reason')}}");
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
        text:'We already have the same entry in the database!',
        buttonsStyling: false,
        confirmButtonClass:'btn btn-sm btn-warning',
        icon:'warning'
      });
    }
    else if(@json(session('message'))=="success"){
      swal.fire({
        title: `Success`,
        text:'Successfully saved to database',
        buttonsStyling: false,
        confirmButtonClass:'btn btn-sm btn-success',
        icon:'success'
      });
      $("#termination_modal").modal('hide');
    }

    else if(@json(session('message'))=="unknown_termination"){
      swal.fire({
        title: `Error`,
        text:'Unknown Termination of Contract',
        buttonsStyling: false,
        confirmButtonClass:'btn btn-sm btn-danger',
        icon:'warning'
      });
    }

    else{
      swal.fire({
        title: `Error`,
        text:'An error occured please contact your system developer',
        buttonsStyling: false,
        confirmButtonClass:'btn btn-sm btn-danger',
        icon:'warning'
      });
    }
  }
  var data = {!! json_encode(session('terminations')) !!};
  if(data==null){
    data = {!! json_encode($terminations) !!};
  }



  var table=  $('#app_table').DataTable({
    dom:'Bfrtip',
    buttons: [
      {
        text:'Hide Filter',
        attr: {
          id:'show_filter'
        },
        className:'btn btn-sm shadow-0 border-0 bg-dark text-white',
        action: function ( e, dt, node, config ) {

          if(config.text=="Show Filter"){
            $('#filter').removeClass('');
            $('#filter_btn').removeClass('');
            config.text="Hide Filter";
            $("#show_filter").html("Hide Filter");
          }
          else{
            $('#filter').addClass('');
            $('#filter_btn').addClass('');
            config.text="Show Filter";
            $("#show_filter").html("Show Filter");
          }
        }
      },
      {
        text:'Filter',
        attr: {
          id:'filter_btn'
        },
        className:'btn btn-sm shadow-0 border-0 bg-warning text-white filter_btn',
        action: function ( e, dt, node, config ) {
          $("#filter").submit();
        }
      },
      @if(in_array("add",$user_privilege))
      {
        text:'Add Mutual Contract Termination',
        attr: {
          id:'add_btn'
        },
        className:'btn btn-sm shadow-0 border-0 bg-primary text-white add_btn'
      },
      @endif
      {
        text:'Excel',
        extend:'excel',
        className:'btn btn-sm shadow-0 border-0 bg-success text-white'
      },
      {
        text:'Print',
        extend:'print',
        className:'btn btn-sm shadow-0 border-0 bg-info text-white'
      }

    ],
    data:data,
    language: {
      paginate: {
        next:'<i class="fas fa-angle-right">',
        previous:'<i class="fas fa-angle-left">'
      }
    },
    orderCellsTop: true,
    select: {
      style:'multi',
      selector:'td:not(:first-child)'
    },
    responsive:true,
    columnDefs: [ {
      targets: 0,
      orderable: false
    } ],
    columns: [
      {"data":"",
      render: function ( data, type, row ) {
        var  termination_id=row.termination_id;
        return'<div style="white-space: nowrap"> @if(in_array("update",$user_privilege))<button class="btn btn-sm btn btn-sm shadow-0 border-0 text-white btn-success edit-btn" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="ni ni-ruler-pencil"></i></button>@endif</div>';
      }
    },
    {"data":"termination_id" },
    {"data":"project_number" },
    {"data":"procact_id" },
    {"data":"project_title" },
    {"data":"project_bid" },
    {"data":"contractor" },
    {"data":"governor_id" },
    {"data":"governor" },
    {"data":"reason" }

  ],
  order: [[ 1,"desc" ]]
});

var project_title_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){
    $.ajax({
      'url':'/autocomplete_awarded_project',
      'data': {
        "_token":"{{ csrf_token() }}",
        "term" : request.term
      },
      'method':"post",
      'dataType':"json",
      'success': function(data) {
        response(data);
      }
    });
  },
  select: function(event, ui){
    if(ui.item.id !=''){
      $(this).val(ui.item.value);
    }else{
      $(this).val('');
    }
    return false;
  },
  change: function (event, ui) {

    if (ui.item == null || ui.item=="") {
      if("{{old('procact_id')}}"!=''){
        $(this).val("{{old('project_title')}}");
        $("#procact_id").val("{{old('procact_id')}}");
        $("#contractor").val("{{old('contractor')}}");
        $("#project_bid").val("{{old('project_bid')}}");
      }
      else{
        $(this).val('');
        $("#procact_id").val('');
        $("#contractor").val('');
        $("#project_bid").val('');
      }
    }
    else{
      var selected_project=ui.item;
      $("#procact_id").val(selected_project.procact_id);
      $("#contractor").val(selected_project.contractor);
      $("#project_bid").val(selected_project.project_bid_id);
    }

  }
}

$("#project_title").autocomplete(project_title_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});



$('#app_table thead tr:eq(1) th').each( function (i) {
  var title = $(this).text();
  if(title!=""){
    $(this).html('<input type="text" placeholder="Search'+title+'" />' );
    $(this).addClass('sorting_disabled');
    $('input', this ).on('keyup change', function () {
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
$(".add_btn").click(function functionName() {
  table.rows().deselect();
  $("#title").val('');
  $("#project_title").val('');
  $("#procact_id").val('');
  $("#project_bid").val('');
  $("#contractor").val('');
  $("#reason").html('');
  $("#governor").val('');
  $("#termination_id").val('');
  $("#project_title").prop('readonly',false);
  $("#termination_modal_title").html("Add Mutual Contract Termination");
  $("#termination_modal").modal('show');
});
@endif

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click','.edit-btn', function (e) {
  table.rows().deselect();
  var row=table.row($(this).parents('tr')).data();
  $("#termination_modal_title").html("Update Mutual Contract Termination");
  $("#termination_id").val(row.termination_id);
  $("#project_title").val(row.project_title);
  $("#procact_id").val(row.procact_id);
  $("#project_bid").val(row.project_bid);
  $("#contractor").val(row.contractor);
  $("#reason").html(row.reason);
  $("#governor").val(row.governor_id);
  $("#project_title").prop('readonly',true);
  $("#termination_modal").modal('show');
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
