@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="app">
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
            <button class="btn btn-sm btn-danger text-white float-right mb-2 btn btn-sm ml-2" id="extend_btn">Extend</button>
            <button class="btn btn-sm btn-warning text-white float-right mb-2 btn btn-sm ml-2 d-none" id="filter_btn">Filter</button>
            <button class="btn btn-sm btn-default text-white float-right mb-2 btn btn-sm ml-2" id="show_filter">Show Filter</button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Date Opened</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Post Qualification</th>
                  <th class="text-center">Post Qual Days Consumed</th>
                  <th class="text-center">Maximum Days</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">APP/SAPP No.</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Ongoing Post Qual</th>
                  <th class="text-center">Ongoing Post Qual Amount</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Project Cost</th>
                  <th class="text-center">Project Year</th>
                  <th class="text-center">Remarks</th>
                </tr>
              </thead>
              <tbody>
                @if(session('newData'))

                @foreach(session('newData') as $project_plan)
                <tr>

                  <td class="" style="white-space: nowrap">
                    <a class="btn btn-sm btn btn-sm btn-success" target='_blank' href="/post_qual_project_bidders/{{$project_plan->plan_id}}" >Responsive:{{$project_plan->responsive_count}}</a>
                    <a class="btn btn-sm btn btn-sm btn-success" target='_blank' href="/project_bidders/{{$project_plan->plan_id}}" ><i class="ni ni-circle-08"></i></a>
                    @if($project_plan->post_qual_days==12)
                    <button class="btn btn-sm btn btn-sm btn-warning request_extension_btn" ><i class="ni ni-curved-next">Request Extension</i></button>
                    @elseif($project_plan->post_qual_days==30)
                    <button class="btn btn-sm btn btn-sm btn-warning request_extension_btn" ><i class="ni ni-curved-next">Request Extension</i></button>
                    @elseif($project_plan->post_qual_days > 40)
                    <span class="btn btn-sm btn btn-sm btn-danger">Due: {{ date("M d,Y", strtotime($project_plan->post_qualification_end))}}</span>
                    @else

                    @endif
                  </td>

                  <td>{{ $project_plan->plan_id}}</td>
                  <td>{{ $project_plan->open_bid}}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>{{ date("M d,Y", strtotime($project_plan->post_qualification_start))}} - {{ date("M d,Y", strtotime($project_plan->post_qualification_end))}}</td>
                  <td>{{$project_plan->post_qual_days}}</td>
                  <td>{{$project_plan->maximum_days}}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td>{{ $project_plan->app_group_no }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->ongoing_post_qual) ?></td>
                  <td>{{ number_format((float)$project_plan->ongoing_post_qual_amount,2,'.',',') }}</td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->mode }}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ number_format($project_plan->project_cost,2,'.',',') }}</td>
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->remarks }}</td>

                </tr>
                @endforeach
                @else
                @foreach($project_plans as $project_plan)
                <tr>

                  <td class="" style="white-space: nowrap">
                    @if($project_plan->responsive_count>=1)
                    <a class="btn btn-sm btn btn-sm btn-warning" target='_blank' href="/post_qual_project_bidders/{{$project_plan->plan_id}}" >Responsive:{{$project_plan->responsive_count}}</a>
                    @else
                    <a class="btn btn-sm btn btn-sm btn-success" target='_blank' href="/post_qual_project_bidders/{{$project_plan->plan_id}}" >Responsive:{{$project_plan->responsive_count}}</a>
                    @endif
                    <a class="btn btn-sm btn btn-sm btn-success" target='_blank' href="/project_bidders/{{$project_plan->plan_id}}" ><i class="ni ni-circle-08"></i></a>
                    @if($project_plan->post_qual_days>=10 && $project_plan->post_qual_days<=12)
                    <button class="btn btn-sm btn btn-sm btn-warning request_extension_btn" ><i class="ni ni-curved-next">Request Extension</i></button>
                    @elseif($project_plan->post_qual_days>=29 && $project_plan->post_qual_days<=30)
                    <button class="btn btn-sm btn btn-sm btn-warning request_extension_btn" ><i class="ni ni-curved-next">Request Extension</i></button>
                    @elseif($project_plan->post_qual_days > 40)
                    <span class="btn btn-sm btn btn-sm btn-danger">Due: {{ date("M d,Y", strtotime($project_plan->post_qualification_end))}}</span>
                    @else

                    @endif
                  </td>


                  <td>{{ $project_plan->plan_id}}</td>
                  <td>{{ $project_plan->open_bid}}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>{{ date("M d,Y", strtotime($project_plan->post_qualification_start))}} - {{ date("M d,Y", strtotime($project_plan->post_qualification_end))}}</td>
                  <td>{{$project_plan->post_qual_days}}</td>
                  <td>{{$project_plan->maximum_days}}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td>{{ $project_plan->app_group_no }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->ongoing_post_qual) ?></td>
                  <td>{{ number_format((float)$project_plan->ongoing_post_qual_amount,2,'.',',') }}</td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->mode }}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ number_format($project_plan->project_cost,2,'.',',') }}</td>
                  <td>{{ $project_plan->project_year }}</td>
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
if(oldInputs>2){

  if('{{old("request_extension_plan_id")}}'!=''){
    $("#request_extension_modal").modal('show');
    $("#process").val("post_qualification");
  }

}
else{
  $("#project_year").val("{{$year}}");
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
    width: 100,
    targets: 0,
    orderable: false
  },
  {
    targets:1,
    visible:false
  } ],
  order:[ [ 2, 'asc' ] ],
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
    dataSrc: 7
  }
});

if("{{session('message')}}"){
  if("{{session('message')}}"=="success"){
    swal.fire({
      title: 'Success',
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#plan_id").val('');
    $("#post_qualification_date").val('');
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





$("#extend_btn").click(function functionName() {
  if(table.rows( { selected: true } ).count()<1){
    Swal.fire({
      title:"Warning",
      text: 'Please select rows Extend ',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-info',
      icon: 'info'
    });
  }
  else{
    let rows = table.rows( { selected: true } );
    var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
    var plan_numbers =  table.cells( rows.nodes(), 9 ).data().toArray();
    $("#extend_plan_id").val(plan_ids.toString());
    $("#extend_project_number").val(plan_numbers.toString());
    $("#process").val("post_qualification");
    $("#extend_days").val("");
    $("#extend_remarks").val("");
    $("#extend_modal").modal('show');
  }
});




</script>
@endpush
