@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div class="modal" tabindex="-1" role="dialog" id="bidder_modal">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="bidder_modal_title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="col-sm-12" method="POST" id="bidders_form" action="{{route('submit_notice')}}">
            @csrf
            <div class="row">
              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                <label for="notice_id">Notice ID
                </label>
                <input type="text" id="notice_id" name="notice_id" class="form-control form-control-sm" readonly value="{{old('notice_id')}}" >
                <label class="error-msg text-red" >@error('notice_id'){{$message}}@enderror
                </label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                <label for="project_bid">Project Bid
                </label>
                <input type="text" id="project_bid" name="project_bid" class="form-control form-control-sm" readonly value="{{old('project_bid')}}" >
                <label class="error-msg text-red" >@error('project_bid'){{$message}}@enderror

                </label>
              </div>


              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                <label for="date_generated">Date Generated<span class="text-red">*</span>
                </label>
                <input type="text" id="date_generated" name="date_generated" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_generated')}}" >
                <label class="error-msg text-red" >@error('date_generated'){{$message}}@enderror
                </label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                <label for="date_released">Date Released to Contractor<span class="text-red"></span>
                </label>
                <input type="text" id="date_released" name="date_released" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_released')}}" >
                <label class="error-msg text-red" >@error('date_released'){{$message}}@enderror
                </label>
              </div>



              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                <label for="date_received_by_contractor">Date Received by Contractors<span class="text-red"></span>
                </label>
                <input type="text" id="date_received_by_contractor" name="date_received_by_contractor" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received_by_contractor')}}" >
                <label class="error-msg text-red" >@error('date_received_by_contractor'){{$message}}@enderror
                </label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 ">
                <label for="mr_due_date">MR Due Date<span class="text-red"></span>
                </label>
                <input type="text" id="mr_due_date" name="mr_due_date" class="form-control form-control-sm bg-white " readonly  value="{{old('mr_due_date')}}" >
                <label class="error-msg text-red" >@error('mr_due_date'){{$message}}@enderror
                </label>
              </div>


              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                <label for="date_received_by_bac">Date Received by BAC Infra/Support<span class="text-red"></span>
                </label>
                <input type="text" id="date_received_by_bac" name="date_received_by_bac" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received_by_bac')}}" >
                <label class="error-msg text-red" >@error('date_received_by_bac'){{$message}}@enderror
                </label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                <label for="notice_type">Notice type<span class="text-red">*</span>
                </label>
                <select type="text" id="notice_type" name="notice_type" class="form-control form-control-sm bg-white"  value="{{old('notice_type')}}" >
                  <option value="NOI"  {{ old('notice_type') == 'NOI' ? 'selected' : ''}}>Notice of Ineligibility</option>
                  <option value="NOD"  {{ old('notice_type') == 'NOD' ? 'selected' : ''}}>Notice of Disqualification</option>
                  <option value="NOPQ"  {{ old('notice_type') == 'NOPQ' ? 'selected' : ''}}>Notice of Post Qualification</option>
                  <option value="NOPD"  {{ old('notice_type') == 'NOPD' ? 'selected' : ''}}>Notice of Post Disqualification</option>
                  <option value="NTLB"  {{ old('notice_type') == 'NTLB' ? 'selected' : ''}}>Notice to Loosing Bidders</option>
                </select>
                <label class="error-msg text-red" >@error('notice_type'){{$message}}@enderror
                </label>
              </div>

              <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                <label for="remarks">Remarks
                </label>
                <textarea type="text" id="remarks" name="remarks" class="form-control form-control-sm" value="{{old('remarks')}}" ></textarea>
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


  <div id="app">
    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Project Bidders</h2>
          <label class="text-sm">Project Number: <span class="">{{$project_number}}</span></label>
          <br />
          <label class="text-sm">Project Title: <span class="">{{$title}}</span></label>
          <br />
          <label class="text-sm">Date Bid Opened: <span class="">{{$open_bid}}</span></label>
          <br />
          <label class="text-sm">Project Cost: <span class="text-red">Php {{number_format($project_cost,2,'.',',')}}</span></label>
        </div>
        <div class="card-body">
          <div class="col-sm-12">
            <!-- <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button> -->
          </div>

          <div class="table-responsive">
            <table class="table table-bordered wrap" id="bidders_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Bid Order</th>
                  <th class="text-center">Project Bid</th>
                  <th class="text-center">Opening Date</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Date Generated</th>
                  <th class="text-center">Date Released To Contractor</th>
                  <th class="text-center">Date Received By Contractor</th>
                  <th class="text-center">MR Due Date</th>
                  <th class="text-center">Date Received by BAC Infra/Support</th>
                  <th class="text-center">Bid as Read</th>
                  <th class="text-center">Bid as Evaluated</th>
                  <th class="text-center">Bid as Calculated</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Post Qual Start</th>
                  <th class="text-center">Post Qual End</th>
                  <th class="text-center">TWG Evaluation</th>
                  <th class="text-center">TWG Remarks</th>
                  <th class="text-center">Notice Remarks</th>

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
// datatables
$('#bidders_table thead tr').clone(true).appendTo( '#bidders_table thead' );
$('#bidders_table thead tr:eq(1)').removeClass('bg-primary');
let data=@json($project_bidders);
var table=  $('#bidders_table').DataTable({
  data:data,
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

  columns: [
    { data:"bid_status",
    render: function ( data, type, row ) {
      let display="";
      let label="";
      if(row.project_bidder_notice_id!=null){
        label="Edit";
      }
      else{
        label="Add";
      }
      if(data==='disqualified'){
        display='<button class="btn btn-sm btn btn-sm btn-danger disqualification-btn">'+label+' Disqualification</button>';
      }
      else if(data==='ineligible'){
        display='<button class="btn btn-sm btn btn-sm btn-info ineligibility-btn">'+label+' Ineligibility</button>';
      }
      else if(data==='responsive'){
        display='<button class="btn btn-sm btn btn-sm btn-success postqualification-btn">'+label+'  Post Qualification</button>';
      }
      else if(data==='non-responsive'){
        display='<button class="btn btn-sm btn btn-sm btn-primary postdisqualification-btn">'+label+' Post Disqualification</button>';
      }
      else if(data==='active' && "{{$responsive_count}}"=="1"){
        display='<button class="btn btn-sm btn btn-sm btn-secondary noticetolosingbidder-btn">'+label+' Loosing Bidder</button>';
      }
      else if(data==='active' && "{{$responsive_count}}"=="0"){
        display='<label>Active</label>';
      }
      else{
        display="<label>DNS</label>";
      }

      if(row.project_bidder_notice_id!=null){
        display='<div style="white-space: nowrap">'+display+'<a  class="btn btn-sm btn btn-sm shadow-0 border-0 btn-primary text-white"  href="/generate_notice/'+row.project_bidder_notice_id+'"><i class="ni ni-cloud-download-95"></i></a></div>';
      }

      return display;
    }
  },
  { data:"project_bidder_notice_id"},
  { data:"bid_order"},
  { data:"project_bid"},
  { data:"open_bid"},
  { data:"business_name"},
  { data:"owner"},
  { data:"notice_date_generated"},
  { data:"notice_date_released"},
  { data:"date_received_by_contractor"},
  { data:"mr_due_date"},
  { data:"notice_date_received"},
  { data:"proposed_bid",render: function ( data, type, row ) {
    if(data!=null){
      return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    return "";
  }},
  { data:"bid_as_evaluated",render: function ( data, type, row ) {
    if(data!=null){
      return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    return "";
  }},
  { data:"twg_final_bid_evaluation",render: function ( data, type, row ) {
    if(data!=null){
      return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    return "";
  }},
  { data:"bid_status"},
  { data:"post_qual_start"},
  { data:"post_qual_end"},
  { data:"twg_evaluation_status"},
  { data:"twg_evaluation_remarks"},
  { data:"notice_remarks"}
],

order: [[ 2, "asc" ]],
columnDefs: [ {
  targets: 0,
  orderable: false
},
{
  targets: [1,2,3],
  visible: false
}],
drawCallback: function ( settings ) {
  var api = this.api();
  var rows = api.rows( {page:'current'} ).nodes();
  var last=null;

  api.column(2, {page:'current'} ).data().each( function ( group, i ) {
    if ( last !== group ) {
      $(rows).eq( i ).before(
        '<tr class="group bg-secondary"><td colspan="19">'+group+'</td></tr>'
      );

      last = group;
    }
  } );
}

});



if("{{old('project_bid')}}"!=""){
  if("{{old('notice_id')}}"!=""){
    $("#bidder_modal_title").html("Edit Notice");
  }
  else{
    $("#bidder_modal_title").html("Add Notice");
  }

  $("#bidder_modal").modal('show');
}

if("{{session('message')}}"){
  if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully Saved Project Bidder',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#bidder_modal").modal('hide');
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


// events

$('#bidders_table thead tr:eq(1) th').each( function (i) {
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

$('#bidders_table tbody').on('click', '.postqualification-btn', function (e) {
  $("#mr_due_date").parents('.form-group').removeClass('d-none');
  $("#mr_due_date").parents('.form-group').addClass('d-none');
  var data = table.row( $(this).parents('tr') ).data();
  $("#date_received_by_contractor").val('');
  $("#date_received_by_bac").val('');

  if(data.project_bidder_notice_id=="" || data.project_bidder_notice_id==null){
    $("#date_generated").val('');
    $("#date_released").val('');
    $("#date_received_by_bac").val('');
    $("#notice_id").val('');
    $("#bidder_modal_title").html('Add Notice');
  }
  else{
    $("#date_generated").datepicker('setDate',moment(moment(data.notice_date_generated).format('Y-MM-DD')).format("MM/DD/YYYY"));
    if(data.notice_date_released!=null){
      $("#date_released").datepicker('setDate',moment(moment(data.notice_date_released).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_released").val('');
    }
    if(data.notice_date_received!=null){
      $("#date_received_by_bac").datepicker('setDate',moment(moment(data.notice_date_received).format('Y-MM-DD')).format("MM/DD/YYYY"));
      $("#date_received_by_contractor").datepicker('setDate',moment(moment(data.date_received_by_contractor).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_received_by_bac").val('');
      $("#date_received_by_contractor").val('');
    }

    console.log("test");
    $("#notice_id").val(data.project_bidder_notice_id);
    $("#remarks").val(data.notice_remarks);
    $("#bidder_modal_title").html('Edit Notice');
  }
  $("#notice_type").val('NOPQ');
  $("#project_bid").val(data.project_bid);
  $("#bidder_modal").modal('show');
});

$('#bidders_table tbody').on('click', '.disqualification-btn', function (e) {
  $("#mr_due_date").parents('.form-group').removeClass('d-none');
  var data = table.row( $(this).parents('tr') ).data();
  $("#date_received_by_contractor").val('');
  console.log(data.project_bid);
  if(data.project_bidder_notice_id=="" || data.project_bidder_notice_id==null){
    $("#date_generated").val('');
    $("#date_released").val('');
    $("#date_received_by_bac").val('');
    $("#notice_id").val('');
    $("#bidder_modal_title").html('Add Notice');
  }
  else{
    $("#date_generated").datepicker('setDate',moment(moment(data.notice_date_generated).format('Y-MM-DD')).format("MM/DD/YYYY"));
    if(data.notice_date_released!=null){
      $("#date_released").datepicker('setDate',moment(moment(data.notice_date_released).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_released").val('');
    }
    if(data.notice_date_received!=null){
      $("#date_received_by_bac").datepicker('setDate',moment(moment(data.notice_date_received).format('Y-MM-DD')).format("MM/DD/YYYY"));
      $("#date_received_by_contractor").datepicker('setDate',moment(moment(data.date_received_by_contractor).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_received_by_bac").val('');
      $("#date_received_by_contractor").val('');
    }
    $("#notice_id").val(data.project_bidder_notice_id);
    $("#remarks").val(data.notice_remarks);
    $("#bidder_modal_title").html('Edit Notice');
  }
  $("#notice_type").val('NOD');
  $("#project_bid").val(data.project_bid);
  $("#bidder_modal").modal('show');
});

$('#bidders_table tbody').on('click', '.ineligibility-btn', function (e) {
  $("#mr_due_date").parents('.form-group').removeClass('d-none');
  var data = table.row( $(this).parents('tr') ).data();
  $("#date_received_by_contractor").val('');
  $("#date_received_by_bac").val('');

  if(data.project_bidder_notice_id=="" || data.project_bidder_notice_id==null){
    $("#date_generated").val('');
    $("#date_released").val('');
    $("#date_received_by_bac").val('');
    $("#notice_id").val('');
    $("#bidder_modal_title").html('Add Notice');
  }
  else{
    $("#date_generated").datepicker('setDate',moment(moment(data.notice_date_generated).format('Y-MM-DD')).format("MM/DD/YYYY"));
    if(data.notice_date_released!=null){
      $("#date_released").datepicker('setDate',moment(moment(data.notice_date_released).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_released").val('');
    }
    if(data.notice_date_received!=null){
      $("#date_received_by_bac").datepicker('setDate',moment(moment(data.notice_date_received).format('Y-MM-DD')).format("MM/DD/YYYY"));
      $("#date_received_by_contractor").datepicker('setDate',moment(moment(data.date_received_by_contractor).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_received_by_bac").val('');
      $("#date_received_by_contractor").val('');
    }
    $("#notice_id").val(data.project_bidder_notice_id);
    $("#remarks").val(data.notice_remarks);
    $("#bidder_modal_title").html('Edit Notice');
  }
  $("#notice_type").val('NOI');
  $("#project_bid").val(data.project_bid);
  $("#bidder_modal").modal('show');
});

$('#bidders_table tbody').on('click', '.postdisqualification-btn', function (e) {
  $("#mr_due_date").parents('.form-group').removeClass('d-none');
  var data = table.row( $(this).parents('tr') ).data();
  $("#date_received_by_contractor").val('');
  $("#date_received_by_bac").val('');

  if(data.project_bidder_notice_id=="" || data.project_bidder_notice_id==null){
    $("#date_generated").val('');
    $("#date_released").val('');
    $("#date_received_by_bac").val('');
    $("#notice_id").val('');
    $("#bidder_modal_title").html('Add Notice');
  }
  else{
    $("#date_generated").datepicker('setDate',moment(moment(data.notice_date_generated).format('Y-MM-DD')).format("MM/DD/YYYY"));
    if(data.notice_date_released!=null){
      $("#date_released").datepicker('setDate',moment(moment(data.notice_date_released).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_released").val('');
    }
    if(data.notice_date_received!=null){
      $("#date_received_by_bac").datepicker('setDate',moment(moment(data.notice_date_received).format('Y-MM-DD')).format("MM/DD/YYYY"));
      $("#date_received_by_contractor").datepicker('setDate',moment(moment(data.date_received_by_contractor).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_received_by_bac").val('');
      $("#date_received_by_contractor").val('');
    }
    $("#notice_id").val(data.project_bidder_notice_id);
    $("#remarks").val(data.notice_remarks);
    $("#bidder_modal_title").html('Edit Notice');
  }
  $("#notice_type").val('NOPD');
  $("#project_bid").val(data.project_bid);
  $("#bidder_modal").modal('show');
});

$('#bidders_table tbody').on('click', '.noticetolosingbidder-btn', function (e) {
  $("#mr_due_date").parents('.form-group').removeClass('d-none');
  $("#mr_due_date").parents('.form-group').addClass('d-none');
  var data = table.row( $(this).parents('tr') ).data();
  $("#date_received_by_contractor").val('');
  $("#date_received_by_bac").val('');

  if(data.project_bidder_notice_id=="" || data.project_bidder_notice_id==null){
    $("#date_generated").val('');
    $("#date_released").val('');
    $("#date_received_by_bac").val('');
    $("#notice_id").val('');
    $("#bidder_modal_title").html('Add Notice');
  }
  else{
    $("#date_generated").datepicker('setDate',moment(moment(data.notice_date_generated).format('Y-MM-DD')).format("MM/DD/YYYY"));
    if(data.notice_date_released!=null){
      $("#date_released").datepicker('setDate',moment(moment(data.notice_date_released).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_released").val('');
    }
    if(data.notice_date_received!=null){
      $("#date_received_by_bac").datepicker('setDate',moment(moment(data.notice_date_received).format('Y-MM-DD')).format("MM/DD/YYYY"));
      $("#date_received_by_contractor").datepicker('setDate',moment(moment(data.date_received_by_contractor).format('Y-MM-DD')).format("MM/DD/YYYY"));
    }
    else{
      $("#date_received_by_bac").val('');
      $("#date_received_by_contractor").val('');
    }
    $("#notice_id").val(data.project_bidder_notice_id);
    $("#remarks").val(data.notice_remarks);
    $("#bidder_modal_title").html('Edit Notice');
  }
  $("#notice_type").val('NTLB');
  $("#project_bid").val(data.project_bid);
  $("#bidder_modal").modal('show');
});

$("#date_released").change(function(){
  if($("#mr_due_date").parents('.form-group').hasClass('d-none')===false && $(this).val()!=""){
    $.ajax({
      'url': '/calculate_due_date',
      'data': {
        "_token": "{{ csrf_token() }}",
        "date" : $(this).val(),
        "days" : 3,
        "date_type" : "Working Days",
      },
      'method': "post",
      'success': function(data) {
        if(data!=null){
          $("#mr_due_date").val(data);
        }
        else{
          $("#mr_due_date").val("");
        }
      }
    });
  }
  else{
    $("#mr_due_date").val("");
  }
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
