@extends('layouts.app')
<style>
ul.ui-autocomplete {
  z-index: 1100;
}
.text-wrap {
  word-break: break-all !important;
}

tbody:nth-child(odd) {
  background: #CCC;
}
</style>
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">

    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Generate Checklist</h2>
        </div>
        <div class="card-body">
          <form class="col-sm-8 mx-auto" method="POST" id="release_form" action="/submit_generate_checklist">
            @csrf
            <div class="row d-flex">

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 ">
                <label for="date_opened">Date Opened</label>
                <input type="text" id="date_opened" name="date_opened" class="form-control form-control-sm datepicker" value="{{old('date_opened')}}" >
                <label class="error-msg text-red" >@error('date_opened'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mt-4">
                <button class="btn btn-primary text-center">Submit</button>
                @if(session('project_plans'))
                <a class="btn btn-info text-center" target="_blank" href="/download_checklist/{{date('Y-m-d',strtotime(old('date_opened')))}}">Download</a>
                @endif
              </div>

            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered " id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center">Mode ID</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Item</th>
                  <th class="text-center">Project Number</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Project Cost</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Contractor</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @if(session('project_plans'))

                @foreach(session('project_plans') as $project_plan)

                @if(count($project_plan->bidders)===0)
                <tr>
                  <td >{{$project_plan->mode_id}}</td>
                  <td >{{$project_plan->group}}</td>
                  <td >{{$project_plan->number}}</td>
                  <td >{{$project_plan->project_no}}</td>
                  <td ><div class='text-wrap' style="width:200px">{{$project_plan->project_title}}</div></td>
                  <td >{{$project_plan->location}}, Benguet</td>
                  <td >{{$project_plan->project_cost}}</td>
                  <td >{{$project_plan->source_of_fund}}</td>
                  <td  >@if($project_plan->mode_id===1)No Bidders @else No Quotations @endif</td>
                  <td  ></td>
                </tr>

                @else
                @foreach($project_plan->bidders as $bidder)
                <tr>
                  <td >{{$project_plan->mode_id}}</td>
                  <td >{{$project_plan->group}}</td>
                  <td >{{$project_plan->number}}</td>
                  <td >{{$project_plan->project_no}}</td>
                  <td ><div class='text-wrap' style="width:200px">{{$project_plan->project_title}}</div></td>
                  <td >{{$project_plan->location}}, Benguet</td>
                  <td >{{$project_plan->project_cost}}</td>
                  <td >{{$project_plan->source_of_fund}}</td>
                  <td >{{$bidder->business_name}}</td>
                  <td >{{$bidder->remarks_status}}</td>
                </tr>
                @endforeach
                @endif
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

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
});

var table= $('#app_table').DataTable(
  {
    language: {
      paginate: {
        next: '<i class="fas fa-angle-right">',
        previous: '<i class="fas fa-angle-left">'
      }
    },
    pageLength: 100,
    rowGroup: {
      startRender: function ( rows, group ) {
        return group;
      },
      dataSrc: 1
    },
    columnDefs: [
      {
        targets: [0,1,2,3,4,5,6,7,8,9],
        orderable: false
      },
      {
        targets: [0,1],
        visible: false
      }
    ],
    rowsGroup: [0,1,2,3,4,5,6,7],
  }
);

// remove duplicate group header
var seen = {};
$('.dtrg-group').each(function() {
  var txt = $(this).text();
  if (seen[txt])
  $(this).remove();
  else
  seen[txt] = true;
});

</script>
@endpush
