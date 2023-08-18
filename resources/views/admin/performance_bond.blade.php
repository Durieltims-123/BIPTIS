@extends('layouts.app')

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
              <form class="col-sm-12" method="POST" id="bidders_form" action="{{route('submit_performance_bond')}}">
                @csrf
                <div class="row">
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                    <label for="contract_id">Contract ID</label>
                    <input type="text" id="contract_id" name="contract_id" class="form-control form-control-sm" readonly value="{{old('contract_id')}}" >
                    <label class="error-msg text-red" >@error('contract_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                    <label for="project_bid">Project Bid</label>
                    <input type="text" id="project_bid" name="project_bid" class="form-control form-control-sm" readonly value="{{old('project_bid')}}" >
                    <label class="error-msg text-red" >@error('project_bid'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="noa_date_received">NOA Date Received By Contractor</label>
                    <input type="text" id="noa_date_received" name="noa_date_received" class="form-control form-control-sm"  value="{{old('noa_date_received')}}" readonly>
                    <label class="error-msg text-red" >@error('noa_date_received'){{$message}}@enderror
                    </label>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="performance_bond_issuance">Performance Bond Issuance<span class="text-red">*</span></label>
                    <input type="text" id="performance_bond_issuance" name="performance_bond_issuance" class="form-control form-control-sm bg-white datepicker"  value="{{old('performance_bond_issuance')}}" >
                    <label class="error-msg text-red" >@error('performance_bond_issuance'){{$message}}@enderror
                    </label>
                  </div>
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="performance_bond_expiration">Performance Bond Expiration Date<span class="text-red">*</span></label>
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
                    <label for="performance_bond_duration">Duration (CD)<span class="text-red">*</span></label>
                    <input type="number" id="performance_bond_duration" name="performance_bond_duration" class="form-control form-control-sm bg-white" readonly  value="{{old('performance_bond_duration')}}">
                    <label class="error-msg text-red" >@error('performance_bond_duration'){{$message}}@enderror
                    </label>
                  </div>


                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="performance_bond_remarks">Remarks</label>
                    <input type="text" id="performance_bond_remarks" name="performance_bond_remarks" class="form-control form-control-sm"  value="{{old('performance_bond_remarks')}}">
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
            <form class="row" id="app_filter" method="post" action="{{route('filter_performance_bonds')}}">
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
                  <th class="text-center">Project Bid</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">SAPP No.</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Contractor</th>
                  <th class="text-center">NOA Date Received by BAC Infra</th>
                  <th class="text-center">Performance Bond Date Received</th>
                  <th class="text-center">Duration</th>
                  <th class="text-center">Performance Bond Issuance </th>
                  <th class="text-center">Performance Bond Expiration </th>
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

let data=@json($project_plans);
if(@json(old('project_year'))!=null){
  data=@json(session('project_plans'));
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
if(oldInputs>2){
  console.log(@json(session()->getOldInput()).length);
  // if("@error('date_received_by_bac') true @enderror"==' true ' || "@error('request_to_submit_performance_bond') true @enderror"==' true '||"@error('performance_bond_expiration') true @enderror"==' true '){
    if("{{old('contract_id')}}"!="null"||"{{old('contract_id')}}"!=""){
      $("#form_modal_title").html('Edit Performance Bond Date');
    }
    else{
      $("#form_modal_title").html('Add Performance Bond Date');
    }
    $("#form_modal").modal('show');
  // }

}


var table=  $('#app_table').DataTable({
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
    { data:"contract_id",
    render: function ( data, type, row ) {
      if(data!=null){
        return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button class="btn btn-sm btn btn-sm btn-success edit-performance-bond mr-0" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="ni ni-ruler-pencil"></i></button>@endif</div>';
      }
      else{
        return '@if(in_array("add",$user_privilege))<div style="white-space: nowrap"><button class="btn btn-sm btn btn-sm btn-danger edit-performance-bond mr-0" data-toggle="tooltip" data-placement="top" title="Add"> <i class="ni ni-fat-add"></i></button></div>@endif';
      }

    }},
    { "data": "contract_id" },
    { "data": "project_bid" },
    { "data": "plan_cluster_id" },
    { "data": "app_group_no" },
    { "data": "project_no" },
    { "data": "project_title" },
    { "data": "municipality_name" },
    { "data": "mode" },
    { "data": "source" },
    { "data": "business_name" },
    { "data": "date_received_by_contractor" },
    { "data": "performance_bond_receive_date" },
    { "data": "performance_bond_duration" },
    { "data": "performance_bond_posted" },
    { "data": "performance_bond_expiration" },
    { "data": "performance_bond_remarks" },
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
    targets:[1,2,4],
    visible:false
  }],
  rowGroup: {
    startRender: function ( rows, group ) {
      if(group==""||group==null){
        var group_title="Non-Clustered Project";
      }
      else{
        var group_title="Cluster "+group;
      }
      return group_title;
    },
    endRender: null,
    dataSrc: 4
  }
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

function fillDuration(){

  if($("#noa_date_received").val()!="" && $("#date_received_by_bac").val()!=""){
    var noa_date_received=moment($("#noa_date_received").val(),"MM/DD/YYYY");
    var date_received_by_bac=moment($("#date_received_by_bac").val(),"MM/DD/YYYY");
    var diff = date_received_by_bac.diff(noa_date_received,'days');
    if(diff<0){
      $("#performance_bond_duration").val("");
      $("#date_received_by_bac").off('change');
      $("#date_received_by_bac").datepicker('setDate',null);
      $("#date_received_by_bac").change(function functionName() {
        fillDuration();
      });
    }
    else{
      $("#performance_bond_duration").val(diff);
    }
  }
  else{
    $("#performance_bond_duration").val("");
  }

}



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


$("#date_received_by_bac").change(function functionName() {
  fillDuration();
});

@if(in_array("add",$user_privilege)||in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit-performance-bond', function (e) {
  $('.error-msg').html("");
  var data = table.row( $(this).parents('tr') ).data();
  if(data.date_received_by_contractor==null){
    swal.fire({
      title: 'NOA Date Receive Error',
      text: 'Please input NOA Date Received by Contractor before updating performance bond!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else{
    $("#noa_date_received").val(moment(data.date_received_by_contractor,'Y-MM-DD').format("MM/DD/YYYY"));
    if(data.contract_id=="" || data.contract_id==null){
      $("#date_received_by_bac").val('');
      $("#form_modal_title").html('Add Performance Bond Date');
      $("#date_received_by_bac").val('');
      $("#performance_bond_remarks").val('');
      $("#performance_bond_duration").val('');
      $("#performance_bond_expiration").val('');
      $("#performance_bond_issuance").val('');

    }
    else{
      console.log(data.performance_bond_issuance);

      $("#request_to_submit_performance_bond").datepicker('setDate',moment(moment(data.performance_bond_posted).format('Y-MM-DD')).format("MM/DD/YYYY"));
      $("#performance_bond_expiration").datepicker('setDate',moment(moment(data.performance_bond_expiration).format('Y-MM-DD')).format("MM/DD/YYYY"));
      $("#performance_bond_issuance").datepicker('setDate',moment(moment(data.performance_bond_issuance).format('Y-MM-DD')).format("MM/DD/YYYY"));
      $("#date_received_by_bac").datepicker('setDate',moment(moment(data.performance_bond_receive_date).format('Y-MM-DD')).format("MM/DD/YYYY"));
      $("#performance_bond_remarks").val(data.performance_bond_remarks);
      $("#form_modal_title").html('Edit Performance Bond Date');
      $("#contract_id").val(data.contract_id);
    }
    $("#project_bid").val(data.project_bid);
    $("#form_modal").modal('show');
  }
});
@endif


</script>
@endpush
