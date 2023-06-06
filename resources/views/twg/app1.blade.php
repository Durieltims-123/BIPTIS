@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="app">
    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12 d-none" id="filter">
            <form class="row" id="app_filter" method="post" action="{{route('filter_app')}}">
              @csrf

              <!-- APP TYPE -->
              <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                <label for="app_type">APP Type <span class="text-red">*</span></label>
                <input type="text" id="app_type" name="app_type" class="form-control" value="{{$project_type}}" >
                <label class="error-msg text-red" >@error('app_type'){{$message}}@enderror</label>
              </div>

              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="project_year" class="input-sm">Project Year </label>
                <input  class="form-control form-control-sm yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}" >
                <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror</label>
              </div>

              <!-- Month added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="month_added">Month Added </label>
                <input type="text" id="month_added" name="month_added" class="form-control form-control-sm monthpicker" value="{{old('month_added')}}" >
                <label class="error-msg text-red" >@error('month_added'){{$message}}@enderror</label>
              </div>

              <!-- date added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="date_added">Date Added </label>
                <input type="text" id="date_added" name="date_added" class="form-control form-control-sm datepicker" value="{{old('date_added')}}" >
                <label class="error-msg text-red" >@error('date_added'){{$message}}@enderror</label>
              </div>




            </form>
          </div>
          <div class="col-sm-12">

            @if($project_type == "supplemental")
            <a v-if=" project_type=='supplemental'" target='_blank' class="btn btn-sm btn-danger text-white float-right mb-2 btn btn-sm" href="/add_supplemental_app"><i class="ni ni-fat-add">Supplemental APP</i> </a>
            @endif
            @if($project_type == "regular")
            <a v-if=" project_type=='regular'" target='_blank' class="btn btn-sm btn-danger text-white float-right mb-2 btn btn-sm" href="/add_regular_app"><i class="ni ni-fat-add">Regular APP</i> </a>
            @endif
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
                  <th class="text-center">APP/SAPP No.</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Posting</th>
                  <th class="text-center">Sub/Open of Bids</th>
                  <th class="text-center">Notice of Award</th>
                  <th class="text-center">Contract Signing</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Approved Budget Cost</th>
                  <th class="text-center">Actual Project Cost</th>
                  <th class="text-center">Project Year</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Remarks</th>
                </tr>
              </thead>
              <tbody>
                @if(session('newData'))

                @foreach(session('newData') as $project_plan)
                <tr>


                  <td><a  class="btn btn-sm shadow-0 border-0 btn-primary text-white"  href="/view_project/{{ $project_plan->plan_id }}"><i class="ni ni-tv-2"></i></a></td>


                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ $project_plan->app_group_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->mode }}</td>
                  <td>{{ $project_plan->abc_post_date }}</td>
                  <td>{{ $project_plan->sub_open_date }}</td>
                  <td>{{ $project_plan->award_notice_date }}</td>
                  <td>{{ $project_plan->contract_signing_date }}</td>
                  <td>{{ $project_plan->source }}</td>
                  @if($project_plan->project_cost!=null)
                  <td>{{ number_format($project_plan->project_cost) }}</td>
                  @else
                  <td></td>
                  @endif
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->project_status }}</td>
                  <td>{{ $project_plan->remarks }}</td>

                </tr>
                @endforeach



                @else
                @foreach($project_plans as $project_plan)
                <tr>




                  <td><a  class="btn btn-sm shadow-0 border-0 btn-primary text-white"  href="/view_project/{{ $project_plan->plan_id }}" ><i class="ni ni-tv-2"></i></a></td>

                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ $project_plan->app_group_no }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->mode }}</td>
                  <td>{{ $project_plan->abc_post_date }}</td>
                  <td>{{ $project_plan->sub_open_date }}</td>
                  <td>{{ $project_plan->award_notice_date }}</td>
                  <td>{{ $project_plan->contract_signing_date }}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ number_format($project_plan->abc,2, '.',',')}}</td>
                  @if($project_plan->project_cost!=null)
                  <td>{{ number_format($project_plan->project_cost,2, '.',',') }}</td>
                  @else
                  <td></td>
                  @endif
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->project_status }}</td>
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

var table=  $('#app_table').DataTable({
  dom: 'Bfrtip',
  buttons: [
    'copy', 'csv', 'excel', 'pdf', 'print'
  ],
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
  } ,
  {
    targets: 1,
    visible: false,
  }]
});


// $('#app_table').DataTable( {
//   dom: 'Bfrtip',
//   buttons: [
//     'copy', 'csv', 'excel', 'pdf', 'print'
//   ]
// });

// inputs
var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>0){
  $('#filter').removeClass('d-none');
  $('#filter_btn').removeClass('d-none');
  $("#show_filter").html("Hide Filter");
}
else{
  $("#project_year").val("{{$year}}");

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


</script>
@endpush
