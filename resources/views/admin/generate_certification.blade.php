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
          <h2 id="title">Generate Certification of Posting</h2>
        </div>
        <div class="card-body">
          <form class="col-sm-8 mx-auto" method="POST" id="certification_form" action="/submit_generate_certification_of_posting">
            @csrf
            <div class="row d-flex">


              <!-- date opened -->
              <div class="form-group col-xs-4 col-sm-4 col-lg-4 ">
                <label for="date_opened">Sheduled Opening</label>
                <input type="text" id="date_opened" name="date_opened" class="form-control form-control-sm datepicker" value="{{old('date_opened')}}" >
                <label class="error-msg text-red" >@error('date_opened'){{$message}}@enderror</label>
              </div>

              <!-- mode of procurement -->
              <div class="form-group col-xs-4 col-sm-4 col-lg-4 ">
                <label for="mode_of_procurement">Mode of Procurement<span class="text-red">*</span></label>
                <select type="" id="mode_of_procurement" name="mode_of_procurement" class="form-control form-control-sm" value="{{old('mode_of_procurement')}}" >
                  <option value=""  {{ old('mode_of_procurement') == '' ? 'selected' : ''}} >All</option>
                  @foreach($modes as $mode)
                  <option value="{{$mode->mode_id}}"  {{ old('mode_of_procurement') == $mode->mode_id ? 'selected' : ''}} >{{$mode->mode}}</option>
                  @endforeach
                </select>
                <label class="error-msg text-red" >@error('mode_of_procurement'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-4 col-sm-4 col-lg-4 mt-4">
                <button type="button" class="btn btn-primary text-center" id="submit_btn">Submit</button>
                @if(session('project_plans'))
                <button type="button" class="btn btn-info text-center" id="download_btn">Download</button>
                @endif
              </div>

            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered " id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center">Mode</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Project Number</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">ABC</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Advertisement Start</th>
                  <th class="text-center">Advertisement End</th>
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

$("#certification_form").prop('action',"{{route('submit_generate_certification_of_posting')}}");
$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
});

var data= @json(session('project_plans'));
var table= $('#app_table').DataTable(
  {
    data:data,
    columns:[
      {"data":"mode_id"},
      {"data":"current_cluster",render:function (data,e,row) {
        if(data!=null){
          return '<span class="badge bg-yellow">Cluster '+data+'</span>';
        }
        else{
          return '';
        }
      }},
      {"data":"mode"},
      {"data":"project_no"},
      {"data":"project_title"},
      {"data":"municipality_name"},
      {"data":"abc",render: function ( data, type, row ) {
        if(data!=null){
          return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        return "";
      }},
      {"data":"source"},
      {"data":"advertisement_start",render: function ( data, type, row ) {
        return moment(data).format('MMM DD,Y');
      }},
      {"data":"advertisement_end",render: function ( data, type, row ) {
        return moment(data).format('MMM DD,Y');
      }}
    ],
    pageLength: 100,
    language: {
      paginate: {
        next: '<i class="fas fa-angle-right">',
        previous: '<i class="fas fa-angle-left">'
      }
    },
    rowGroup: {
      startRender: function ( rows, group ) {
        return group;
      },
      dataSrc:"mode"
    },
    columnDefs: [
      {
        targets: [0,1,2,3,4,5,6,7,8,9],
        orderable: false
      },
      {
        targets: [0],
        visible: false
      }
    ],

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


$("#download_btn").click(function functionName() {
  $("#certification_form").prop('action',"{{route('download_certification_of_posting')}}");
  $("#certification_form").submit();
});
$("#submit_btn").click(function functionName() {
  $("#certification_form").prop('action',"{{route('submit_generate_certification_of_posting')}}");
  $("#certification_form").submit();
});

</script>
@endpush
