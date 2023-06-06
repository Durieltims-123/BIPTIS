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
              <h3 class="modal-title" id="form_modal_title">Edit Notice to Proceed</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body pt-0">
              <form class="col-sm-12" method="POST" id="bidders_form" action="{{route('submit_notice')}}">
                @csrf
                <div class="row">
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                    <label for="notice_id">Notice ID</label>
                    <input type="text" id="notice_id" name="notice_id" class="form-control form-control-sm" readonly value="{{old('notice_id')}}" >
                    <label class="error-msg text-red" >@error('notice_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                    <label for="project_bid">Project Bid</label>
                    <input type="text" id="project_bid" name="project_bid" class="form-control form-control-sm" readonly value="{{old('project_bid')}}" >
                    <label class="error-msg text-red" >@error('project_bid'){{$message}}@enderror
                    </label>
                  </div>


                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="date_generated">Date Generated<span class="text-red">*</span></label>
                    <input type="text" id="date_generated" name="date_generated" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_generated')}}" >
                    <label class="error-msg text-red" >@error('date_generated'){{$message}}@enderror
                    </label>
                  </div>


                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                    <label for="date_released">Date Released to Contractor<span class="text-red"></span></label>
                    <input type="text" id="date_released" name="date_released" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_released')}}" >
                    <label class="error-msg text-red" >@error('date_released'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                    <label for="date_received_by_contractor">Date Received By Contractor</label>
                    <input type="text" id="date_received_by_contractor" name="date_received_by_contractor" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received_by_contractor')}}" >
                    <label class="error-msg text-red" >@error('date_received_by_contractor'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                    <label for="date_received_by_bac">Date Received By BAC Infra</label>
                    <input type="text" id="date_received_by_bac" name="date_received_by_bac" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received_by_bac')}}" >
                    <label class="error-msg text-red" >@error('date_received_by_bac'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                    <label for="notice_type">Notice type<span class="text-red">*</span></label>
                    <select type="text" id="notice_type" name="notice_type" class="form-control form-control-sm bg-white"  value="{{old('notice_type')}}" >
                      <option value="NTP"  {{ old('notice_type') == 'NTP' ? 'selected' : ''}}>Notice To Proceed</option>
                    </select>
                    <label class="error-msg text-red" >@error('notice_type'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="remarks">Remarks</label>
                    <textarea type="text" id="remarks" name="remarks" class="form-control form-control-sm" >{{old('remarks')}}</textarea>
                    <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror
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
            <form class="row" id="filter_notice" method="POST" action="{{route('filter_prepare_notice_to_proceed')}}">
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
          <div class="col-sm-12">


          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Project Bid</th>
                  <th class="text-center">Notice to Proceed</th>
                  <th class="text-center">Notice to Proceed</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">APP/SAPP No.</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Contractor</th>
                  <th class="text-center">Project Cost</th>
                  <th class="text-center">Proposed Bid</th>
                  <th class="text-center">Date Generated</th>
                  <th class="text-center">Date Released</th>
                  <th class="text-center">Date Received by Contractor</th>
                  <th class="text-center">Date Received by BAC Infra</th>
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
  $("#project_year").val("{{$year}}");
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



$('.timepicker').timepicker({
  showRightIcon: false,
  showOnFocus: true,
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
if(oldInputs>5){

  // if("@error('date_released') true @enderror"==' true ' || "@error('date_received_by_bac') true @enderror"==' true '){
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
    { data:"ntp_id",
    render: function ( data, type, row ) {
      if(data!=null){
        return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button class="btn btn-sm btn btn-sm btn-success edit-ntp mr-0" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="ni ni-ruler-pencil"></i></button> @endif <a  class="btn btn-sm btn btn-sm shadow-0 border-0 btn-primary text-white" target="_blank" data-toggle="tooltip" data-placement="top" title="View File"  href="/generate_ntp/'+data+'"><i class="ni ni-cloud-download-95"></i></a></div>';
      }
      else{
        return '<div style="white-space: nowrap">@if(in_array("add",$user_privilege))<button class="btn btn-sm btn btn-sm btn-danger edit-ntp mr-0" data-toggle="tooltip" data-placement="top" title="Add"> <i class="ni ni-fat-add"></i></button> @endif</div>';
      }

    }},
    { "data": "ntp_id" },
    { "data": "project_bid" },
    { "data": "proceed_notice_start" },
    { "data": "proceed_notice_end",render: function ( data, type, row ) {
      return moment(row.proceed_notice_start,'YYYY-MM-DD').format('MMMM DD,YYYY') +" - "+ moment(data,'YYYY-MM-DD').format('MMMM DD,YYYY');
    }},
    { "data": "plan_cluster_id" },
    { "data": "app_group_no" },
    { "data": "project_no" },
    { "data": "project_title" },
    { "data": "municipality_name" },
    { "data": "mode" },
    { "data": "source" },
    { "data": "business_name" },
    {"data":"project_cost",render: function ( data, type, row ) {
      if(data!=null){
        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }
      return "";
    }},
    {"data":"proposed_bid",render: function ( data, type, row ) {
      if(data!=null){
        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }
      return "";
    }},
    { "data": "ntp_date_generated" },
    { "data": "ntp_date_released" },
    { "data": "ntp_date_received_by_contractor"},
    { "data": "ntp_date_received" },
    { "data": "ntp_remarks" }
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
    targets:[1,2,6],
    visible:false
  } ],
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
  },

  order: [[3, "desc"]]
});

if("{{session('message')}}"){
  if("{{session('message')}}"=="range_error"){

    swal.fire({
      title: 'Date Error',
      text: 'Date should be equal or in between the given date range. ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  if("{{session('message')}}"=="duplicate"){
    swal.fire({
      title: 'Duplicate Error',
      text: 'We already have the same dat! Please Reload Page. ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  else if("{{session('message')}}"=="contract_error"){

    swal.fire({
      title: 'Contract Error',
      text: 'Please Verify if the Contract is Received by Bac Infra.',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: 'Success',
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#plan_id").val('');
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

$("#project_year").change(function functionName() {
  $("#filter_notice").submit();
});



$("#filter_btn").click(function () {
  $("#filter_notice").submit();
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

@if(in_array("add",$user_privilege)||in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit-ntp', function (e) {
  $('.error-msg').html("");
  var data = table.row( $(this).parents('tr') ).data();

  if(data.ntp_id=="" || data.ntp_id==null){
    $("#date_generated").val('');
    $("#date_released").val('');
    $("#date_received_by_contractor").val('');
    $("#date_received_by_bac").val('');
    $("#notice_id").val('');
    $("#remarks").val('');
    $("#form_modal_title").html('Create Notice to Proceed');
  }
  else{
    $("#date_released").val('');
    $("#date_received_by_contractor").val('');
    $("#date_received_by_bac").val('');

    $("#date_generated").datepicker('setDate',moment(data.ntp_date_generated,'Y-MM-DD').format("MM/DD/YYYY"));
    if(data.ntp_date_released!=null){
      $("#date_released").datepicker('setDate',moment(data.ntp_date_released,'Y-MM-DD').format("MM/DD/YYYY"));
    }
    else{
      $("#date_released").val('');
    }
    if(data.ntp_date_received_by_contractor!=null){
      $("#date_received_by_contractor").datepicker('setDate',moment(data.ntp_date_received_by_contractor,'Y-MM-DD').format("MM/DD/YYYY"));
    }
    else{
      $("#date_received_by_contractor").val('');
    }
    if(data.ntp_date_received!=null){
      $("#date_received_by_bac").datepicker('setDate',moment(data.ntp_date_received,'Y-MM-DD').format("MM/DD/YYYY"));
    }
    else{
      $("#date_received_by_bac").val('');
    }
    $("#notice_id").val(data.ntp_id);
    $("#remarks").val(data.ntp_remarks);
    $("#form_modal_title").html('Edit Notice to Proceed');
  }
  $("#project_bid").val(data.project_bid);
  $("#notice_type").val('NTP');
  $("#form_modal").modal('show');
});
@endif



</script>
@endpush
