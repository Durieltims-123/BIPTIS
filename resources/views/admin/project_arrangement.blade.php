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

          <form class="col-sm-12 mx-auto" method="POST" id="get_date_projects_form" action="{{route('get_date_projects')}}">
            @csrf
            <div class=" col-sm-6 d-flex pl-0">

              <div class="form-group col-xs-4 col-sm-4 col-lg-4 mb-0 pl-0">
                <label for="date_opened">Date Opened</label>
                <input type="text" id="date_opened" name="date_opened" class="form-control form-control-sm datepicker" value="{{old('date_opened')}}" >
                <label class="error-msg text-red" >@error('date_opened'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-3 col-sm-3 col-lg-3 mt-4 mb-0 pl-0">
                <button class="btn btn-sm btn-success text-center">Submit</button>
              </div>
            </div>
          </form>
          <hr>
          <h2>Projects</h2>
          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center">Arrangement</th>
                  <th class="text-center">Procact ID</th>
                  <th class="text-center">Project Number</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Municipality</th>
                  <th class="text-center">ABC</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">Duration</th>
                  <th class="text-center">Mode</th>
                </tr>
              </thead>
              <tbody>
                @if(session('project_plans'))
                @foreach(session('project_plans') as $project_plan)
                <tr>
                  <td>{{$project_plan->row_number}}</td>
                  <td>{{ $project_plan->procact_id }}</td>
                  <td><a href="/view_project/{{$project_plan->plan_id}}">{{ $project_plan->project_no }}</a></td>
                  <td>{{ $project_plan->source}}</td>
                  <td>{{ $project_plan->project_title }}</td>
                  <td>{{ $project_plan->municipality_name }}</td>
                  <td>{{ number_format($project_plan->project_cost,2,'.',',')}}</td>
                  <td>{{ $project_plan->plan_cluster_id }}</td>
                  <td>{{ $project_plan->duration }}</td>
                  <td>{{ $project_plan->mode }}</td>
                </tr>
                @endforeach
                @endif
              </tbody>
            </table>

          </div>

          @if(session('project_plans')!=null)
          <form class="col-sm-12 d-flex" method="POST" id="submit_arrangement" action="{{route('submit_arrangement')}}">
            @csrf
            <div class="form-group col-xs-4 col-sm-4 col-lg-4 mb-0 pl-0 d-none">
              <label for="date_opened_save">Date Opened</label>
              <input type="text" id="date_opened_save" name="date_opened_save" class="form-control form-control-sm datepicker" value="{{old('date_opened')}}" >
              <label class="error-msg text-red" >@error('date_opened_save'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
              <label for="procact_ids" class="input-sm">Procurement Activity ID</label>
              <input  class="form-control form-control-sm" id="procact_ids" name="procact_ids"   value="{{old('procact_ids')}}" >
              <label class="error-msg text-red" >@error('procact_ids'){{$message}}@enderror</label>
            </div>

            <button class="btn btn-sm btn-primary mx-auto mt-3" id="save_changes" type="submit">Save Changes</button>
          </form>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('custom-scripts')
<script>

var table=  $('#app_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  pageLength: 100,
  orderCellsTop: true,
  select: {
    style: 'multi',
    selector: 'td:not(:first-child)'
  },
  responsive:true,
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
  },
  columnDefs: [
    {
      targets:[1],
      visible:false
    }
  ],
  rowReorder: true,
});

// show Dates
if("{{old('date_opened_save')}}"!=""){
  $("#date_opened_save").val("{{old('date_opened_save')}}");
  $("#date_opened").val("{{old('date_opened_save')}}");
}

// initialize row order to procact_ids
let rows = table.rows();
var procacts =  table.cells( rows.nodes(), 1 ).data().toArray();
$("#procact_ids").val(procacts.toString());

if(table.rows().count()==0){
  $("#save_changes").addClass("d-none");
}

table.on( 'row-reorder', function ( e, diff, edit ) {
  var procacts=[];
  var b="";
  table.one( 'draw', function () {
    table.rows().every(function (rowIdx, tableLoop, rowLoop) {
      b=this.data()[1];
      procacts.push(this.data()[1]);
    });
    $("#procact_ids").val(procacts.toString());
  });

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

  else if("{{session('message')}}"=="bidder_error"){
    swal.fire({
      title: 'Project Bidder Error',
      text: 'Some rows have no active project bidders',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  else if("{{session('message')}}"=="nopd_mismatch"){
    swal.fire({
      title: 'Notice of Post Disqualification Error',
      text: 'Please release Notice of Post Disqualification to Non-responsive Bidder Before Generating Notice to Proceed',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  else if("{{session('message')}}"=="nopd_null_release"){
    swal.fire({
      title: 'Notice of Post Disqualification Error',
      text: 'Please Update Notice of Post Disqualification with Null Date Received Before Generating Notice to Proceed',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }
  else if("{{session('message')}}"=="nopd_less_release"){
    swal.fire({
      title: 'Notice Error',
      text: "Sorry!, You can't generate Notice to Proceed until 3 days upon receipt of other Notices. The Notice of Post Disqualification with received dated {{session('last_received')}} can still file Motion for Reconsideration.",
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }
  else if("{{session('message')}}"=="nopd_release"){
    swal.fire({
      title: 'Notice Error',
      text: "Please Recheck Notice of Post Disqualification, Date Received Must Be Before NTP Release Date. ",
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  else if("{{session('message')}}"=="resolution_error"){
    swal.fire({
      title: 'Resolution Error',
      text: 'The Project Plan is not included in any Resolution  Recommending Award',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  else if("{{session('message')}}"=="ntlb_null_release"){
    swal.fire({
      title: 'Notice To Loosing Bidders Error',
      text: 'Please Update Notice To Loosing Bidders with Null Date Received Before Generating Notice to Proceed',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  else if("{{session('message')}}"=="ntlb_less_release"){
    swal.fire({
      title: 'Notice Error',
      text: 'Please Recheck Notice to Loosing Bidders, Date Received Must Be Before NTP Release Date. ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  else if("{{session('message')}}"=="ntlb_mismatch"){
    swal.fire({
      title: 'Notice of Post Loosing Bidder Error',
      text: 'Please Release Notice To Loosing Bidders Before Generating Notice to Proceed',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#form_modal").modal('show');
  }

  else if("{{session('message')}}"=="resolution_error"){
    swal.fire({
      title: 'Resolution Error',
      text: 'This project is not included in any Resolution.',
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
    $("#award_notice_date").val('');
    $("#form_modal").modal('hide');
  }
  else{

  }
}


$("#date_opened").change(function functionName() {
  $("#date_opened_save").val($(this).val());
  $("#get_date_projects_form").submit();
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



</script>
@endpush
