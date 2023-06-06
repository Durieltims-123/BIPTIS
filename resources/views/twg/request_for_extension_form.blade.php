@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div class="card shadow mt-4 mb-5" >
    <div class="card shadow border-0"  style="background:#F7F5F5">
      <div class="card-header" style="background:#F7F5F5">
        <h2 id="title">{{$title}}</h2>
      </div>
      <div class="card-body ">
        <form class="col-sm-12" method="POST" id="resolution_form" action="{{route('submit_request_for_extension')}}">
          @csrf
          <div class="row">

            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
              <label for="project_bidders">Project Bidders</label>
              <input type="text" id="project_bidders" name="project_bidders" class="form-control form-control-sm" readonly value="{{old('project_bidders')}}" >
              <label class="error-msg text-red" ></label>
            </div>


            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
              <label for="opening_dates">Opening Dates</label>
              <input type="text" id="opening_dates" name="opening_dates" class="form-control form-control-sm" readonly value="{{old('opening_dates')}}" >
              <label class="error-msg text-red" ></label>
            </div>

            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
              <label for="request_id">ID</label>
              <input type="text" id="request_id" name="request_id" class="form-control form-control-sm" readonly value="{{old('request_id')}}" >
              <label class="error-msg text-red" >@error('request_id'){{$message}}@enderror</label>
            </div>


            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0" >
              <label for="date_generated">Date Generated<span class="text-red">*</span></label>
              <input type="text" id="date_generated" name="date_generated" class="form-control form-control-sm datepicker" value="{{old('date_generated')}}" >
              <label class="error-msg text-red" >@error('date_generated'){{$message}}@enderror</label>
            </div>


            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0" >
              <label for="request_date">Requested Date<span class="text-red">*</span></label>
              <input type="text" id="request_date" name="request_date" class="form-control form-control-sm datepicker" value="{{old('request_date')}}" >
              <label class="error-msg text-red" >@error('request_date'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-lg-6 mb-0 " >
              <label for="reason">Reason<span class="text-red">*</span></label>
              <textarea type="text" id="reason" name="reason" class="form-control form-control-sm" >
              </textarea>
              <label class="error-msg text-red" >@error('reason'){{$message}}@enderror</label>
            </div>


            <div class="form-group col-xs-12 col-sm-12 col-lg-6 mb-0 " >
              <label for="remarks">Remarks</label>
              <textarea type="text" id="remarks" name="remarks" class="form-control form-control-sm " >
              </textarea>
              <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
              <label for="mode_of_procurement">Mode of Procurement<span class="text-red">*</span></label>
              <select type="" id="mode_of_procurement" name="mode_of_procurement" class="form-control" value="{{old('mode_of_procurement')}}" >
                @foreach($modes as $mode)
                <option value="{{$mode->mode_id}}"  {{ old('mode_of_procurement') == $mode->mode_id ? 'selected' : ''}} >{{$mode->mode}}</option>
                @endforeach
              </select>
              <label class="error-msg text-red" >@error('mode_of_procurement'){{$message}}@enderror</label>
            </div>

          </div>
          <div class="row">
            <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
              <div class="card p-3">
                <label>Please Select Projects:</label>
                <label class="error-msg text-red mx-auto"></label>
                <div class="table-responsive">
                  <table class="table table-bordered" id="bidder_table">
                    <thead class="">
                      <tr class="bg-primary text-white" >
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center">Plan ID</th>
                        <th class="text-center">Date Opened</th>
                        <th class="text-center">Project Number</th>
                        <th class="text-center">Project Title</th>
                        <th class="text-center">Contractor</th>
                        <th class="text-center">Consumed Days</th>
                        <th class="text-center">ITB Arrangement</th>
                        <th class="text-center">Mode</th>
                        <th class="text-center">Cluster</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
              <div class="card p-3">
                <label for="date_generated">Selected Projects:</label>
                <label class="error-msg text-red mx-auto">@error('project_bidders') Please select Projects @enderror </label>
                <div class="table-responsive">
                  <table class="table table-bordered" id="selected_bidder_table">
                    <thead class="">
                      <tr class="bg-primary text-white" >
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center">Procact ID</th>
                        <th class="text-center">Date Opened</th>
                        <th class="text-center">Project Number</th>
                        <th class="text-center">Project Title</th>
                        <th class="text-center">Contractor</th>
                        <th class="text-center">ITB Arrangement</th>
                        <th class="text-center">Mode</th>
                        <th class="text-center">Cluster</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
          <div class="d-flex justify-content-center col-sm-12 mt-3">
            <button class="btn btn-primary text-center">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



@endsection

@push('custom-scripts')
<script>

let request_for_extension=@json($request_for_extension);
if(request_for_extension!=null){
  $("#project_bidders").val(@json($project_bidders));
  $("#request_id").val(request_for_extension.request_id);
  $("#date_generated").val(moment(request_for_extension.request_date_generated).format('MM/DD/YYYY'));
  $("#request_date").val(moment(request_for_extension.request_date).format('MM/DD/YYYY'));
  $("#reason").html(request_for_extension.request_reason);
  $("#remarks").html(request_for_extension.request_remarks);
}
else{
  let old_remarks=@json(old('remarks'));
  let old_reason=@json(old('reason'));
  if(old_remarks!=null){
    $("#remarks").html(old_remarks);
  }
  else{
    $("#remarks").html("");
  }

  if(old_reason!=null){
    $("#reason").html(old_reason);
  }
  else{
    $("#reason").html("");
  }
}

// messages
if("{{session('message')}}"){
  if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }

  else if("{{session('message')}}"=="duplicate"){
    swal.fire({
      title: `Duplicate`,
      text: 'Data with the same date_generated and request date already exist in the database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else{
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact system developer',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
}

var table=  $('#bidder_table').DataTable({
  info: false,
  lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
  pageLength: 10,
  dateFormat: 'yyyy-mm-dd',
  processing: true,
  serverSide: true,
  recordsTotal: 50,
  // stateSave: true,
  paging: true,
  ajax: {
    url:'{{route("get_all_post_qual_without_extension")}}',
    type: 'POST',
    dataType: "json",
    dataSrc: function ( json ) {
      return json.data;
    },
    data: function ( d ) {
      d.mode_of_procurement = $("#mode_of_procurement").val();
    },
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  },
  columns: [
    {"data": "project_bid",render: function ( data, type, row ) {
      let edit="/edit_request/"+data;
      let download="/generate_cca/"+data;
      return "<div style='white-space: nowrap'><button data-toggle='tooltip' data-placement='top' title='Select' type='button'  class='btn btn-sm btn-success select-btn'><i class='ni ni-fat-add text-white'></i></button>  </div>";
    }},
    {"data":"project_bid"},
    {"data":"procact_id"},
    {"data":"open_bid"},
    {"data":"project_no"},
    {"data":"project_title"},
    {"data":"business_name"},
    {"data":"consumed_days"},
    {"data":"itb_arrangement"},
    {"data":"mode"},
    {"data":"plan_cluster_id"}
  ],
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  orderCellsTop: true,
  columnDefs: [ {
    targets: [1,2,8],
    visible: false
  }]
});

let data=[];
var selected_table=  $('#selected_bidder_table').DataTable({
  info: false,
  lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
  pageLength: 10,
  dateFormat: 'yyyy-mm-dd',
  processing: true,
  serverSide: true,
  recordsTotal: 50,
  stateSave: true,
  paging: true,
  ajax: {
    url:'{{route("get_selected_project_bidders")}}',
    type: 'POST',
    dataType: "json",
    dataSrc: function ( json ) {
      rows=json.data;
      let openings=[];
      rows.forEach(function(item,key){
        if(key===0){
          openings.push(item.open_bid);
        }
        else{
          if(!openings.includes(item.open_bid)){
            openings.push(item.open_bid);
          }
        }
      });
      $("#opening_dates").val(openings.toString(''));
      return json.data;
    },
    data: function ( d ) {
      d.project_bidders = $("#project_bidders").val();
    },
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  },
  columns: [
    {"data": "project_bid",render: function ( data, type, row ) {
      let edit="/edit_request/"+data;
      let download="/generate_cca/"+data;
      return "<div style='white-space: nowrap'><button data-toggle='tooltip' data-placement='top' title='Remove' type='button'  class='btn btn-sm btn-danger remove-btn'><i class='ni ni-fat-remove text-white'></i></button>  </div>";
    }},
    {"data":"project_bid"},
    {"data":"procact_id"},
    {"data":"open_bid"},
    {"data":"project_no"},
    {"data":"project_title"},
    {"data":"business_name"},
    {"data":"itb_arrangement"},
    {"data":"mode"},
    {"data":"plan_cluster_id"}
  ],
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  orderCellsTop: true,
  columnDefs: [ {
    targets: [1,2,8],
    visible: false
  }]
});



let project_bidders_array=[];
if($("#project_bidders").val()!=""){
  project_bidders_array=$("#project_bidders").val().split(",");
  project_bidders_array=project_bidders_array.map(Number);
}


$('#bidder_table tbody').on('click', '.select-btn', function (e) {

  var data = table.row( $(this).parents('tr') ).data();

  if(project_bidders_array.includes(data.project_bid)===false){
    project_bidders_array.push(data.project_bid);
    $("#project_bidders").val(project_bidders_array.toString());
    selected_table.ajax.reload();
    selected_table.rows().data().map((row) => {
      console.log(row);
    });
  }


});

$('#selected_bidder_table tbody').on('click', '.remove-btn', function (e) {
  let button=$(this);
  Swal.fire({
    title:'Remove Project',
    text: 'Are you sure to Remove this Project?',
    showCancelButton: true,
    confirmButtonText: 'Remove',
    cancelButtonText: "Cancel",
    buttonsStyling: false,
    customClass: {
      confirmButton: 'btn btn-sm btn-danger',
      cancelButton: 'btn btn-sm btn-default',
    },
    icon: 'warning'
  }).then((result) => {
    if (result.value == true) {
      var data = selected_table.row( $(button).parents('tr') ).data();
      let index = project_bidders_array.indexOf(data.project_bid);
      if (index > -1) {
        project_bidders_array.splice(index, 1);
      }
      $("#project_bidders").val(project_bidders_array.toString());
      selected_table.ajax.reload();
      selected_table.rows().data().map((row) => {
        console.log(row);
      });
    }
  });

});


$("#mode_of_procurement").change(function (){
  table.ajax.reload();
});








</script>
@endpush
