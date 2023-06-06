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
      <div class="modal" tabindex="-1" role="dialog" id="form_modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="form_modal_title">Edit Performance Bond Date</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body pt-0">
              <form class="col-sm-12" method="POST" id="bidders_form" action="{{route('submit_additional_performance_bond')}}">
                @csrf
                <div class="row">

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                    <label for="additional_pb_id">ID</label>
                    <input type="text" id="additional_pb_id" name="additional_pb_id" class="form-control form-control-sm" readonly value="{{old('additional_pb_id')}}" >
                    <label class="error-msg text-red" >@error('additional_pb_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                    <label for="contract_id">Contract ID</label>
                    <input type="text" id="contract_id" name="contract_id" class="form-control form-control-sm" readonly value="{{old('contract_id')}}" >
                    <label class="error-msg text-red" >@error('contract_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-3 col-sm-3 col-lg-3">
                    <label for="project_number">Project Number<span class="text-red">*</span></label>
                    <input type="text" id="project_number" name="project_number" class="form-control " value="{{old('project_number')}}" readonly >
                    <label class="error-msg text-red" >@error('project_number'){{$message}}@enderror</label>
                  </div>

                  <div class="form-group col-xs-9 col-sm-9 col-lg-9">
                    <label for="project_title">Project Title<span class="text-red">*</span></label>
                    <input type="text" id="project_title" name="project_title" class="form-control " value="{{old('project_title')}}" >
                    <label class="error-msg text-red" >@error('project_title'){{$message}}@enderror</label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                    <label for="bidder_id">Contractor ID</label>
                    <input type="text" id="bidder_id" name="bidder_id" class="form-control " value="{{old('bidder_id')}}" >
                    <label class="error-msg text-red" >@error('contract_id'){{$message}}@enderror</label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="contractor">Contractor<span class="text-red">*</span></label>
                    <input type="text" id="contractor" name="contractor" class="form-control bg-white" readonly value="{{old('contractor')}}" >
                    <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror</label>
                  </div>
                </div>

                <div class="row">

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="request_to_submit_performance_bond">Performance Bond Date of Issuance<span class="text-red">*</span></label>
                    <input type="text" id="request_to_submit_performance_bond" name="request_to_submit_performance_bond" class="form-control form-control-sm bg-white datepicker"  value="{{old('request_to_submit_performance_bond')}}" >
                    <label class="error-msg text-red" >@error('request_to_submit_performance_bond'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="performance_bond_expiration">Performance Bond Expiration<span class="text-red">*</span></label>
                    <input type="text" id="performance_bond_expiration" name="performance_bond_expiration" class="form-control form-control-sm bg-white datepicker"  value="{{old('performance_bond_expiration')}}" >
                    <label class="error-msg text-red" >@error('performance_bond_expiration'){{$message}}@enderror
                    </label>
                  </div>
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="date_received_by_bac">Date Received by BAC Infra/Support<span class="text-red">*</span></label>
                    <input type="text" id="date_received_by_bac" name="date_received_by_bac" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received_by_bac')}}" >
                    <label class="error-msg text-red" >@error('date_received_by_bac'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="performance_bond_remarks">Remarks</label>
                    <textarea type="text" id="performance_bond_remarks" name="performance_bond_remarks" class="form-control form-control-sm">{{old('performance_bond_remarks')}}</textarea>
                    <label class="error-msg text-red" >@error('performance_bond_remarks'){{$message}}@enderror
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


    @include('layouts.components.modals')
    @include('layouts.components.extend_modal')


    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12" id="filter">
            <form class="row" id="app_filter" method="post" action="{{route('filter_additional_performance_bond')}}">
              @csrf

              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="project_year" class="input-sm">Project Year </label>
                <input  class="form-control form-control-sm yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}" >
                <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror
                </label>
              </div>

              <!-- Month added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="month_added">Month Added </label>
                <input type="text" id="month_added" name="month_added" class="form-control form-control-sm monthpicker" value="{{old('month_added')}}" >
                <label class="error-msg text-red" >@error('month_added'){{$message}}@enderror
                </label>
              </div>

              <!-- date added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="date_added">Date Added </label>
                <input type="text" id="date_added" name="date_added" class="form-control form-control-sm datepicker" value="{{old('date_added')}}" >
                <label class="error-msg text-red" >@error('date_added'){{$message}}@enderror
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
                  <th class="text-center">Contract ID</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Contractor ID</th>
                  <th class="text-center">Contractor</th>
                  <th class="text-center">Performance Bond Date Issued</th>
                  <th class="text-center">Performance Bond Expiration </th>
                  <th class="text-center">Performance Bond Date Received</th>
                  <th class="text-center">Remarks</th>
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

let data=@json($data);
if(@json(old('project_year'))!=null){
  data=@json(session('data'));
}
else{
  $("#project_year").val(@json($year));
}
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

// inputs
var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>4){
  console.log(@json(session()->getOldInput()));
  if(@json(old('additional_pb_id'))!=null){
    $("#form_modal_title").html('Edit Performance Bond Date');
  }
  else{
    $("#form_modal_title").html('Add Performance Bond Date');
  }
  $("#form_modal").modal('show');
}




var table=  $('#app_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },

  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  dom: 'Bfrtip',
  buttons: [
    {
      text: 'Hide Filter',
      attr: {
        id: 'show_filter'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-dark text-white',
      action: function (e, dt, node, config) {

        if (config.text == "Show Filter") {
          $('#filter').removeClass('d-none');
          $('#filter_btn').removeClass('d-none');
          config.text = "Hide Filter";
          $("#show_filter").html("Hide Filter");
        }
        else {
          $('#filter').addClass('d-none');
          $('#filter_btn').addClass('d-none');
          config.text = "Show Filter";
          $("#show_filter").html("Show Filter");
        }
      }
    },
    {
      text: 'Filter',
      attr: {
        id: 'filter_btn'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-warning text-white'
    },
    @if(in_array("add",$user_privilege))
    {
      text: 'Add Performance Bond',
      attr: {
        id: 'add_btn'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-primary text-white'
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
    { data:"additional_pb_id",
    render: function ( data, type, row ) {
      if(data!=null){
        return '@if(in_array("update",$user_privilege))<div style="white-space: nowrap"><button class="btn btn-sm btn-success edit-performance-bond mr-0" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="ni ni-ruler-pencil"></i></button> <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn btn-sm btn-danger delete_btn"><i class="ni ni-basket text-white"></i></button></div>@endif';
      }

    }},
    { "data": "additional_pb_id" },
    { "data": "contract_id" },
    { "data": "project_no" },
    { "data": "project_title" },
    { "data": "contractor_id" },
    { "data": "business_name" },
    { "data": "additional_pb_date_issuance" },
    { "data": "additional_pb_expiration" },
    { "data": "additional_pb_received_date" },
    { "data": "additional_pb_remarks" }
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
  },
  {
    targets:[1,2,5],
    visible:false
  }],
  // rowGroup: {
  //   startRender: function ( rows, group ) {
  //     if(group==""||group==null){
  //       var group_title="Non-Clustered Project";
  //     }
  //     else{
  //       var group_title="Cluster "+group;
  //     }
  //     return group_title;
  //   },
  //   endRender: null,
  //   dataSrc: 4
  // }
});

if("{{session('message')}}"){

  if("{{session('message')}}"=="duplicate"){
    swal.fire({
      title: 'Duplicate Error',
      text: 'We already have this data on the database ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('hide');
  }

  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: 'Success',
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#form_modal").modal('hide');
  }
  else{

  }
}



var project_title_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_project_with_contracts',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term,
        "opening_date":$("#opening_date").val(),
      },
      'method': "post",
      'dataType': "json",
      'success': function(data) {
        if($("#opening_date").val()==""){
          response({
            'id' : '',
            'value' : 'No Match Found'
          });
        }
        else{
          response(data);
        }
      }
    });
  },
  select: function(event, ui){
    if(ui.item.id != '' && $("#opening_date").val()!=""){
      $(this).val(ui.item.value);
    }else{
      $(this).val('');
    }
    return false;
  },
  change: function (event, ui) {

    $("#contractor").val('');
    $("#bidder_id").val('');

    if (ui.item == null || ui.item=="") {
      if("{{old('contract_id')}}"!=''){
        $(this).val("{{old('project_title')}}");
        $("#contract_id").val("{{old('contract_id')}}");
        $("#project_number").val("{{old('project_number')}}");
        $("#bidder_id").val("{{old('bidder_id')}}");
        $("#contractor").val("{{old('contractor')}}");
      }
      else{
        $(this).val('');
        $("#contract_id").val('');
        $("#project_number").val('');
        $("#bidder_id").val('');
        $("#contractor").val('');
      }
    }
    else{
      var selected_project=ui.item;
      $("#contract_id").val(selected_project.contract_id);
      $("#project_number").val(selected_project.project_number);
      $("#bidder_id").val(selected_project.contractor_id);
      $("#contractor").val(selected_project.contractor);
    }

  }
}


// events
$('#app_table thead tr:eq(1) th').each( function (i) {
  var title = $(this).text();
  if(title!=""){
    $(this).html( '<input type="text" placeholder="Search" />' );
    $(this).addClass('sorting_disabled');
    var index=0;

    $( 'input', this ).on( 'keyup change', function () {
      if ( table.column(':contains('+title+')').search() !== this.value ) {
        table
        .column(':contains('+title+')')
        .search( this.value )
        .draw();
      }

    } );
  }
});


@if(in_array("add",$user_privilege))
$("#add_btn").click(function(){
  $('.error-msg').html("");
  $("#additional_pb_id").val("");
  $("#form_modal_title").html('Add Performance Bond Date');
  $("#contract_id").val('');
  $("#project_number").val('');
  $("#bidder_id").val('');
  $("#contractor").val('');
  $("#project_title").val('');
  $("#date_received_by_bac").val('');
  $("#request_to_submit_performance_bond").val('');
  $("#performance_bond_remarks").val('');
  $("#performance_bond_expiration").val('');
  $("#additional_pb_id").prop('readonly',false);
  $("#contract_id").prop('readonly',false);
  $("#project_number").prop('readonly',false);
  $("#bidder_id").prop('readonly',false);
  $("#contractor").prop('readonly',false);
  $("#project_title").prop('readonly',false);
  $("#form_modal").modal('show');
  $("#project_title").autocomplete({
    disabled: false
  });
  $("#project_title").autocomplete(project_title_autocomplete_init).focus(function() {
    $(this).autocomplete('search', $(this).val())
  });
});
@endif


$("#project_year").change(function functionName() {
  $("#app_filter").submit();
});

$("#date_added").change(function functionName() {
  $("#project_year").val("");
  $("#month_added").val("");
});

$("#month_added").change(function functionName() {
  $("#project_year").val("");
  $("#date_added").val("");
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

@if(in_array("delete",$user_privilege))
$('#app_table tbody').on('click', '.delete_btn', function (e) {
  var data = table.row( $(this).parents('tr') ).data();
  Swal.fire({
    title:"Delete Performance Bond",
    text: 'Are you sure to Delete This Performance Bond?',
    showCancelButton: true,
    cancelButtonText: "No",
    confirmButtonText: 'Yes',
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $.ajax({
        'url': '/delete_additional_performance_bond',
        'data': {
          "_token": "{{ csrf_token() }}",
          "id" : data.additional_pb_id,
        },
        'method': "post",
        'dataType': "text",
        'success': function(data) {
          if(data==="success"){
            swal.fire({
              title: 'Success',
              text: 'Successfully deleted from database',
              buttonsStyling: false,
              confirmButtonClass: 'btn btn-sm btn-success',
              icon: 'success'
            });
            location.reload();
          }
          else{
            swal.fire({
              title: 'Warning',
              text: 'Sorry you cannot delete this data',
              buttonsStyling: false,
              confirmButtonClass: 'btn btn-sm btn-success',
              icon: 'warning'
            });
          }
        }
      });
    }
  });
});
@endif

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit-performance-bond', function (e) {
  $('.error-msg').html("");
  $("#form_modal_title").html('Edit Performance Bond Date');
  var data = table.row( $(this).parents('tr') ).data();
  $("#additional_pb_id").val(data.additional_pb_id);
  $("#contract_id").val(data.contract_id);
  $("#project_number").val(data.project_no);
  $("#bidder_id").val(data.contractor_id);
  $("#contractor").val(data.business_name);
  $("#project_title").val(data.project_title);
  $("#additional_pb_id").prop('readonly',true);
  $("#contract_id").prop('readonly',true);
  $("#project_number").prop('readonly',true);
  $("#bidder_id").prop('readonly',true);
  $("#contractor").prop('readonly',true);
  $("#project_title").prop('readonly',true);
  $("#date_received_by_bac").datepicker('setDate',moment(data.additional_pb_received_date).format('L'));
  $("#request_to_submit_performance_bond").datepicker('setDate',moment(data.additional_pb_date_issuance).format('L'));
  $("#performance_bond_remarks").val(data.additional_pb_remarks);
  $("#performance_bond_expiration").datepicker('setDate',moment(data.additional_pb_expiration).format('L'));
  $("#project_title").autocomplete({
    disabled: true
  });
  $("#form_modal").modal('show');

});
@endif


</script>
@endpush
