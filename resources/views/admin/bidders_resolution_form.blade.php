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
        <form class="col-sm-12" method="POST" id="resolution_form" action="{{route('submit_mr_resolution')}}">
          @csrf
          <div class="row">
            <div class="form-group col-xs-6 col-sm-6 col-lg-3 mx-auto d-none">
              <label for="mr_project_bid_ids">MR PROJECT BID IDS</label>
              <input type="text" id="mr_project_bid_ids" name="mr_project_bid_ids" class="form-control form-control-sm" readonly value="{{old('mr_project_bid_ids')}}" >
              <label class="error-msg text-red" ></label>
            </div>

            <div class="form-group col-xs-6 col-sm-6 col-lg-3 mx-auto d-none">
              <label for="resolution_id">ID</label>
              <input type="text" id="resolution_id" name="resolution_id" class="form-control form-control-sm" readonly value="{{old('resolution_id')}}" >
              <label class="error-msg text-red" >@error('resolution_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-6 col-sm-6 col-lg-3 mb-0">
              <label for="resolution_number">Resolution Number<span class="text-red">*</span></label>
              <input type="text" id="resolution_number" name="resolution_number" class="form-control form-control-sm" value="{{old('resolution_number')}}" >
              <label class="error-msg text-red" >@error('resolution_number'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-6 col-sm-6 col-lg-3 mb-0" >
              <label for="resolution_date">Resolution Date<span class="text-red">*</span></label>
              <input type="text" id="resolution_date" name="resolution_date" class="form-control form-control-sm datepicker" value="{{old('resolution_date')}}" >
              <label class="error-msg text-red" >@error('resolution_date'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-6 col-sm-6 col-lg-3 mb-0 @if($resolution_type==='RDMR')d-none @endif" >
              <label for="succeeding_process">Succeeding Process<span class="text-red">*</span></label>
              {{old('succeeding_process')}}
              <select type="text" id="succeeding_process" name="succeeding_process" class="form-control form-control-sm" >
                <option value="" >Select Succeeding Process</option>
                <option value="Opening" @if(old('succeeding_process')=="Opening") selected @endif>For Opening</option>
                <option value="New Schedule" @if(old('succeeding_process')=="New Schedule") selected @endif>New Schedule of Opening</option>
                <option value="Post Qualification" @if(old('succeeding_process')=="Post Qualification") selected @endif>For Further Post Qualification</option>
                <option value="Notice of Award" @if(old('succeeding_process')=="Notice of Award") selected @endif>Recommend for Award</option>
              </select>
              <label class="error-msg text-red" >@error('succeeding_process'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-6 col-sm-6 col-lg-3 mb-0 d-none" >
              <label for="next_opening_date">Date of Opening</label>
              <input type="text" id="next_opening_date" name="next_opening_date" class="form-control form-control-sm datepicker" value="{{old('next_opening_date')}}" >
              <label class="error-msg text-red" >@error('next_opening_date'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0 d-none" id="reason_container">
              <label for="reason">Reason</label>
              <input type="text" id="reason" name="reason" class="form-control form-control-sm" value="{{old('reason')}}" >
              <label class="error-msg text-red" >@error('reason'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-6 col-sm-6 col-lg-3 mb-0 d-none" >
              <label for="resolution_type">Resolution Type</label>
              <select type="text" id="resolution_type" name="resolution_type" class="form-control form-control-sm" >
                <option value="RGMR" @if($resolution_type==="RGMR") selected @endif>Resolution Granting The Motion fo Reconsideration</option>
                <option value="RDMR" @if($resolution_type==="RDMR") selected @endif>Resolution Denying The Motion fo Reconsideration</option>
              </select>
              <label class="error-msg text-red" >@error('resolution_type'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0 bg-white">
              <label for="resolution_date">Select MRs:</label>
              <label class="error-msg text-red mx-auto"> @error('mr_project_bid_ids') Please select MRs @enderror </label>
              <div class="table-responsive">
                <table class="table table-bordered" id="project_table">
                  <thead class="">
                    <tr class="bg-primary text-white" >
                      <th class="text-center"></th>
                      <th class="text-center">MR ID</th>
                      <th class="text-center">Date Opened</th>
                      <th class="text-center">MR Date</th>
                      <th class="text-center">Resolution Due Date</th>
                      <th class="text-center">Project Number</th>
                      <th class="text-center">Project Title</th>
                      <th class="text-center">Selected</th>
                      <th class="text-center">Mode</th>
                      <th class="text-center">Contractor</th>
                      <th class="text-center">Cluster</th>
                      <th class="text-center">MR Type</th>

                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
              </div>

            </div>

          </div>
          <div class="d-flex justify-content-center col-sm-12 mt-3">
            <button  type="button" id="submit_btn" class="btn btn-sm btn-primary text-center">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


@endsection

@push('custom-scripts')

<script>

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
  endDate:moment().format('MMMM')
});

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

  else if("{{session('message')}}"=="multiple_opening"){
    swal.fire({
      title: `Opening Error`,
      text: 'You cannot mix projects with various opening in 1 Resolution ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }

  else if("{{session('message')}}"=="multiple_failure_status"){
    swal.fire({
      title: `Failure Status`,
      text: 'You cannot mix no bidders,failure upon opening and failure after post qual in 1 Resolution Declaring Failure.',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="duplicate"){
    swal.fire({
      title: `Duplicate`,
      text: 'Data already exist in the database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="unknown_resolution"){
    swal.fire({
      title: `Unknown Resolution`,
      text: 'Sorry! We cannot find the Resolution You are Looking for.',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="reload_error"){
    swal.fire({
      title: `Page Reload`,
      text: 'Motion for Reconsideration already have a Resolution',
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



let data = {!! json_encode($mrs) !!};
let table=  $('#project_table').DataTable({
  data:data,
  dataType: 'json',
  columns: [
    {"data": "",render: function ( data, type, row ) { return ""; }},
    { "data": "mr_project_bid_id" },
    { "data": "open_bid" },
    { "data": "mr_date_received" },
    { "data": "resolution_due_date" },
    { "data": "project_no" },
    { "data": "project_title" },
    { "data": "selected",render: function ( data, type, row ) { if(data=="1"){return "1";}else{ return "";} }},
    { "data": "mode" },
    { "data": "business_name" },
    { "data": "plan_cluster_id" },
    { "data": "mr_type" }
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
    selector: 'td'
  },
  responsive:true,
  columnDefs: [
    {
      targets: [1,7],
      visible: false
    },
    {
      className: 'select-checkbox',
      targets:   0
    }],
    order: [[  7, "asc" ],[ 4, "asc" ],[ 2, "asc" ],[ 3, "asc" ]]
  });


  table.on( 'select', function ( e, dt, type, indexes ) {
    let row=dt.data();
    let cluster_id=row.plan_cluster_id;
    let business_name=row.business_name;
    let rows = table.rows( { selected: true } );
    let initial_contractor='';
    let initial_opening='';
    let cluster_ids=[];
    let deselect_all=0;

    table.rows( { selected: true } ).every(function (rowIdx, tableLoop, rowLoop) {
      if(initial_opening==''){
        table.cell(rowIdx,7).data("1");
        table.rows( { selected: false } ).every(function (rowIdx, tableLoop, rowLoop) {
          if(cluster_id!=null && cluster_id!="" && table.cell(rowIdx,10).data()==cluster_id && table.cell(rowIdx,9).data()==business_name){
            console.log(table.cell(rowIdx,10));
            table.cell(rowIdx,7).data("1");
            cluster_ids.push(rowIdx);
          }
        }).draw();
      }
      else{
        if(initial_opening!=table.cell(rowIdx,2).data()){
          swal.fire({
            title: `Opening Error`,
            text: 'Sorry! You cannot select projects with various opening date',
            confirmButtonColor: '#3085d6',
            icon: 'warning'
          });
          deselect_all=1;
        }
        else if(initial_contractor!=table.cell(rowIdx,10).data()){
          swal.fire({
            title: `Contractor Error`,
            text: 'Sorry! You can only select multiple projects with the same contractor',
            confirmButtonColor: '#3085d6',
            icon: 'warning'
          });
          deselect_all=1;
        }
        else{
          table.cell(rowIdx,7).data("1").draw();
        }
      }
      initial_contractor=table.cell(rowIdx,10).data();
      initial_opening=table.cell(rowIdx,2).data();
    }).order([ 7, 'desc' ],[ 4, 'asc' ],[ 2, 'asc' ],[ 3, 'asc' ]).draw();

    if(deselect_all==1){
      table.rows( { selected: true } ).every(function (rowIdx, tableLoop, rowLoop) {
        this.deselect();
      });
      $("#mr_project_bid_ids").val('');
    }
    else{
      table.rows(cluster_ids).select().order([ 7, 'desc' ],[ 4, 'asc' ],[ 2, 'asc' ],[ 3, 'asc' ]).draw();
      let selected_rows = table.rows( { selected: true });
      let mr_project_bid_ids = table.cells(selected_rows.nodes(), 1 ).data().toArray();
      $("#mr_project_bid_ids").val(mr_project_bid_ids.toString());
    }
  });

  table.on( 'deselect', function ( e, dt, type, indexes ) {
    let row=dt.data();
    let cluster_id=row.plan_cluster_id;
    let business_name=row.business_name;
    table.cell(indexes,6).data("");
    table.rows( { selected: true } ).every(function (rowIdx, tableLoop, rowLoop) {
      if(cluster_id!=null && cluster_id!="" && table.cell(rowIdx,10).data()==cluster_id && table.cell(rowIdx,10).data()==business_name){
        table.cell(rowIdx,7).data("");
        this.deselect();
      }
    }).order( [ 7, 'desc' ],[ 4, 'asc' ],[ 2, 'asc' ],[ 3, 'asc' ] ).draw();
    let selected_rows = table.rows( { selected: true } );
    let mr_project_bid_ids =  table.cells( selected_rows.nodes(), 1 ).data().toArray();
    $("#mr_project_bid_ids").val(mr_project_bid_ids.toString());
  });

  let mr_project_bid_ids="{{old('mr_project_bid_ids')}}";
  if(mr_project_bid_ids!=""&&mr_project_bid_ids!=null){
    let mr_project_bid_ids_array=mr_project_bid_ids.split(",");
    mr_project_bid_ids_array.forEach(function(item,index) {
      table.rows().every (function (rowIdx, tableLoop, rowLoop) {
        if(table.cells( this.nodes(), 1 ).data().to$()[0]==item){
          this.select();
        }
      });
    });
  }
  else{
    let resolution=@json($resolution);
    let mr_project_bid_ids=@json($mr_project_bid_ids);
    if(resolution!=null){
      $("#resolution_id").val(resolution.resolution_id);
      $("#succeeding_process").val(resolution.succeeding_process);
      $("#resolution_number").val(resolution.resolution_number);
      $("#next_opening_date").val('@if($resolution) {{date("m/d/Y", strtotime($resolution->next_opening_date )) }}@endif');
      $("#resolution_date").val('@if($resolution) {{date("m/d/Y", strtotime($resolution->resolution_date )) }}@endif');
      if(resolution!=null){
        mr_project_bid_ids=mr_project_bid_ids[0].mr_project_bid_ids;
        let mr_project_bid_ids_array=mr_project_bid_ids.split(",");
        mr_project_bid_ids_array.forEach(function(item,index) {
          table.rows().every (function (rowIdx, tableLoop, rowLoop) {
            if(table.cells( this.nodes(), 1 ).data().to$()[0]==item){
              this.select();
            }
          });
        });
      }


    }
  }


  $("#submit_btn").click(function functionName() {
    if($("#resolution_type").val()=="RGMR"){
      Swal.fire({
        title: 'Warning?',
        text: "Please Verify Succeeding Process. You cannot edit it after submission",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Fully verified'
      }).then((result) => {
        if (result.isConfirmed) {
          $("#resolution_form").submit();
        }
      })
    }
    else{
      $("#resolution_form").submit();
    }
  });


  if($("#succeeding_process").val()=="New Schedule"){
    $("#next_opening_date").parent().removeClass('d-none');
  }


  $("#succeeding_process").change(function functionName() {
    $("#next_opening_date").val('');
    if($(this).val()=="New Schedule"){
      $("#next_opening_date").parent().removeClass('d-none');
    }
    else{
      $("#next_opening_date").parent().addClass('d-none');
    }
  });

</script>
@endpush
