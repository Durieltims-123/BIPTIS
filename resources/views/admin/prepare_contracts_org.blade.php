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
              <h3 class="modal-title" id="form_modal_title">Edit Contract</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body pt-0">
              <form class="col-sm-12" method="POST" id="bidders_form" action="{{route('submit_contract')}}">
                @csrf
                <div class="row">
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                    <label for="contract_id">Contract ID</label>
                    <input type="text" id="contract_id" name="contract_id" class="form-control form-control-sm" readonly value="{{old('contract_id')}}" >
                    <label class="error-msg text-red" >@error('contract_id'){{$message}}@enderror</label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                    <label for="project_bid">Project Bid</label>
                    <input type="text" id="project_bid" name="project_bid" class="form-control form-control-sm" readonly value="{{old('project_bid')}}" >
                    <label class="error-msg text-red" >@error('project_bid'){{$message}}@enderror</label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="date_generated">Date Generated<span class="text-red">*</span></label>
                    <input type="text" id="date_generated" name="date_generated" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_generated')}}" >
                    <label class="error-msg text-red" >@error('date_generated'){{$message}}@enderror</label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="date_released">Date Released to Contractor<span class="text-red">*</span></label>
                    <input type="text" id="date_released" name="date_released" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_released')}}" >
                    <label class="error-msg text-red" >@error('date_released'){{$message}}@enderror</label>
                  </div>


                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="date_received_by_contractor">Date Received By Contractor<span class="text-red">*</span></label>
                    <input type="text" id="date_received_by_contractor" name="date_received_by_contractor" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received_by_contractor')}}" >
                    <label class="error-msg text-red" >@error('date_received_by_contractor'){{$message}}@enderror</label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="date_received_by_bac">Date Received<span class="text-red">*</span></label>
                    <input type="text" id="date_received_by_bac" name="date_received_by_bac" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received_by_bac')}}" >
                    <label class="error-msg text-red" >@error('date_received_by_bac'){{$message}}@enderror</label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="date_of_notarization">Date of Notarization<span class="text-red">*</span></label>
                    <input type="text" id="date_of_notarization" name="date_of_notarization" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_of_notarization')}}" >
                    <label class="error-msg text-red" >@error('date_of_notarization'){{$message}}@enderror</label>
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
          <div class="col-sm-12 d-none" id="filter">
            <form class="row" id="app_filter" method="post" action="{{route('filter_app')}}">
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
                  <th class="text-center">Project Bid</th>
                  <th class="text-center">Contract Range</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">SAPP No.</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Contractor</th>
                  <th class="text-center">Project Cost</th>
                  <th class="text-center">Proposed Bid</th>
                  <th class="text-center">Release Date</th>
                  <th class="text-center">Received Date</th>
                  <th class="text-center">Performance Bond Date</th>
                </tr>
              </thead>
              <tbody>
                @if(session('newData'))

                @foreach(session('newData') as $project_plan)
                <tr>

                  <td style="white-space: nowrap">
                    @if($project_plan->contract_release_date!==null)
                    <button class="btn btn-sm btn btn-sm btn-primary  edit-contract"> Edit</button>
                    <a  class="btn btn-sm shadow-0 border-0 btn-primary text-white"  href="/generate_contract/{{$project_plan->contract_id}}"><i class="ni ni-cloud-download-95"></i></a>
                    @else
                    <button class="btn btn-sm btn btn-sm btn-success  edit-contract" >Add</button>
                    @endif
                  </td>
                  <td>{{ $project_plan->contract_id }}</td>
                  <td>{{ $project_plan->project_bid }}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->contract_signing_start))}} - {{ date("M d,Y", strtotime($project_plan->contract_signing_end))}}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td>{{ $project_plan->app_group_no }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->mode }}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->business_name }}</td>
                  <td>{{ number_format($project_plan->project_cost,2,'.',',') }}</td>
                  <td>{{ number_format($project_plan->minimum_cost,2,'.',',') }}</td>
                  <td>{{ $project_plan->contract_release_date }}</td>
                  <td>{{ $project_plan->contract_receive_date }}</td>
                  <td>{{ $project_plan->performance_bond_posted }}</td>

                </tr>
                @endforeach

                @else
                @foreach($project_plans as $project_plan)
                <tr>

                  <td style="white-space: nowrap">
                    @if($project_plan->contract_release_date!==null)
                    <button class="btn btn-sm btn btn-sm btn-primary  edit-contract"> Edit</button>
                    <a  class="btn btn-sm shadow-0 border-0 btn-primary text-white"  href="/generate_contract/{{$project_plan->contract_id}}"><i class="ni ni-cloud-download-95"></i></a>
                    @else
                    <button class="btn btn-sm btn btn-sm btn-success  edit-contract" >Add</button>
                    @endif
                  </td>
                  <td>{{ $project_plan->contract_id }}</td>
                  <td>{{ $project_plan->project_bid }}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->contract_signing_start))}} - {{ date("M d,Y", strtotime($project_plan->contract_signing_end))}}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td>{{ $project_plan->app_group_no }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->mode }}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->business_name }}</td>
                  <td>{{ number_format($project_plan->project_cost,2,'.',',') }}</td>
                  <td>{{ number_format($project_plan->minimum_cost,2,'.',',') }}</td>
                  <td>{{ $project_plan->contract_release_date }}</td>
                  <td>{{ $project_plan->contract_receive_date }}</td>
                  <td>{{ $project_plan->performance_bond_posted }}</td>

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

  if("@error('date_generated') true @enderror"==' true ' || "@error('date_received_by_bac') true @enderror"==' true '){
    $("#modal-title").html('Edit Contract');
    $("#form_modal").modal('show');
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
  },
  {
    targets:[1,2],
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

  if("{{session('message')}}"==="duplicate"){
    swal.fire({
      title: 'Duplicate Error',
      text: 'We already have this data on the database ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('hide');
  }
  if("{{session('message')}}"==="range_error"){
    swal.fire({
      title: 'Range Error',
      text: 'Contract Release Date Must be within the Given Contract Signing Date',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('hide');
  }

  else if("{{session('message')}}"==="success"){
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

$("#project_year").change(function functionName() {
  $("#date_added").val("");
  $("#month_added").val("");
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


$('#app_table tbody').on('click', '.edit-contract', function (e) {
  $('.error-msg').html("");
  var data = table.row( $(this).parents('tr') ).data();
  if(data[14]=="" || data[14]==null){
    $("#date_generated").val('');
    $("#modal-title").html('Add Performance Bond Date');
  }
  else{

    $("#date_generated").datepicker('setDate',moment(moment(data[14]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    if(data[15]!=="" || data[15]==null){
      $("#date_received_by_bac").datepicker('setDate',moment(moment(data[15]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    $("#modal-title").html('Edit Performance Bond Date');

  }
  $("#contract_id").val(data[1]);
  $("#project_bid").val(data[2]);
  $("#form_modal").modal('show');
});

</script>
@endpush
