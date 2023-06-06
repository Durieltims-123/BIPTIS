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
                <label for="date_released">Date Released to Contractor<span class="text-red">*</span>
                </label>
                <input type="text" id="date_released" name="date_released" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_released')}}" >
                <label class="error-msg text-red" >@error('date_released'){{$message}}@enderror
                </label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                <label for="date_received">Date Received by BAC Infra/Support<span class="text-red">*</span>
                </label>
                <input type="text" id="date_received" name="date_received" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received')}}" >
                <label class="error-msg text-red" >@error('date_received'){{$message}}@enderror
                </label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                <label for="notice_type">Notice type<span class="text-red">*</span>
                </label>
                <select type="text" id="notice_type" name="notice_type" class="form-control form-control-sm bg-white"  value="{{old('notice_type')}}" >
                  <option value="NOI"  {{ old('notice_type') == 'NOI' ? 'selected' : ''}}>Notice of Disqualification</option>
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
                  <th class="text-center">Project Bid</th>
                  <th class="text-center">Bid Order</th>
                  <th class="text-center">Rank</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Bid as Read</th>
                  <th class="text-center">Bid as Evaluated</th>
                  <th class="text-center">Date Generated</th>
                  <th class="text-center">Date Released</th>
                  <th class="text-center">Date Received</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Remarks</th>
                  <th class="text-center">Post Qual Start</th>
                  <th class="text-center">Post Qual End</th>
                  <th class="text-center">TWG Evaluation</th>
                  <th class="text-center">TWG Remarks</th>

                </tr>
              </thead>
              <tbody>
                @if(count($project_bidders)>0)
                @php
                $row_number=1;
                $old=$project_bidders[0]->procact_id;
                @endphp
                @foreach($project_bidders as $bidder)
                @if($bidder->bid_status!=null)
                <tr>
                  <td style="white-space: nowrap">
                    @if($bidder->bid_status==='disqualified')
                    <button class="btn btn-sm btn btn-sm btn-danger ineligibility-btn">Edit Notice of Ineligibility</button>
                    @elseif($bidder->bid_status==='responsive')
                    <button class="btn btn-sm btn btn-sm btn-success postqual-btn">Edit Notice of Post Qual</button>
                    @elseif($bidder->bid_status==='non-responsive')
                    <button class="btn btn-sm btn btn-sm btn-warning postineligibility-btn">Edit Notice of Post Disqual</button>
                    @elseif($bidder->bid_status==='active' && $responsive_count==1)
                    <button class="btn btn-sm btn btn-sm btn-warning notice-to-loosing-btn">Edit Notice to Loosing Bidder</button>
                    @endif
                    @if($bidder->project_bidder_notice_id!=null)
                    <a  class="btn btn-sm shadow-0 border-0 btn-primary text-white"  href="/edit_notice/{{$bidder->project_bidder_notice_id}}"><i class="ni ni-cloud-download-95"></i></a>
                    @endif
                  </td>
                  <td>{{$bidder->project_bidder_notice_id}}</td>
                  <td>{{$bidder->project_bid}}</td>
                  <td>{{$bidder->bid_order}}</td>
                  @php
                  if($bidder->proposed_bid!=null && $bidder->proposed_bid>0){
                    if($old!=$bidder->procact_id){
                      $old=$bidder->procact_id;
                      $row_number=1;
                    }
                    echo "<td>".$row_number."</td>";
                    $row_number=$row_number+1;
                  }
                  else{
                    echo "<td></td>";
                  }
                  @endphp
                  <td>{{$bidder->business_name}}
                  </td>
                  <td>{{$bidder->owner}}</td>
                  <td>{{number_format($bidder->proposed_bid,2,'.',',')}}</td>
                  <td>{{number_format($bidder->bid_as_evaluated,2,'.',',')}}</td>
                  <td>{{$bidder->date_generated}}</td>
                  <td>{{$bidder->date_released}}</td>
                  <td>{{$bidder->notice_released}}</td>
                  <td>{{$bidder->notice_received}}</td>
                  <td>{{$bidder->bid_status}}</td>
                  <td>{{$bidder->notice_remarks}}</td>
                  <td>{{$bidder->post_qual_start}}</td>
                  <td>{{$bidder->post_qual_end}}</td>
                  <td>{{$bidder->twg_evaluation_status}}</td>
                  <td>{{$bidder->twg_evaluation_remarks}}</td>
                </tr>
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
// datatables
$('#bidders_table thead tr').clone(true).appendTo( '#bidders_table thead' );
$('#bidders_table thead tr:eq(1)').removeClass('bg-primary');


var table=  $('#bidders_table').DataTable({
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
  order: [[ 2, "asc" ]],
  columnDefs: [ {
    targets: 0,
    orderable: false
  }, {
    targets: 1,
    visible: false
  } ],
  rowGroup: {
    startRender: function ( rows, group ) {
      var group_title=group;
      return group_title;
    },
    endRender: null,
    dataSrc: 3
  }
});



var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>2){
  if("{{old('notice_type')}}"=="NOPQ"){
    $("#bidder_modal_title").html("Edit Notice of Post Qualification");
  }
  if("{{old('notice_type')}}"=="NOI"){
    $("#bidder_modal_title").html("Edit Notice of Disqualification");
  }
  if("{{old('notice_type')}}"=="NOPD"){
    $("#bidder_modal_title").html("Edit Notice of Post Disqualification");
  }
  if("{{old('notice_type')}}"=="NTLB"){
    $("#bidder_modal_title").html("Edit Notice To Loosing Bidder");
  }
  var isReleasedError="@error('date_released') true @enderror";
  var isReceivedError="@error('date_received') true @enderror";
  if(isReleasedError==" true " || isReceivedError==" true "){
    $("#bidder_modal").modal('show');
  }

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


$('#bidders_table tbody').on('click', '.postqual-btn', function (e) {
  var data = table.row( $(this).parents('tr') ).data();
  $("#mr_due_date_container").removeClass('d-none');
  $("#mr_due_date_container").addClass('d-none');
  if(data[1]=="" || data[1]==null){
    $("#date_released").val('');
    $("#date_received").val('');
    $("#notice_id").val('');
  }
  else{
    $("#date_released").datepicker('setDate',moment(moment(data[9]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    $("#date_received").datepicker('setDate',moment(moment(data[10]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    $("#notice_id").val(data[1]);
    $("#remarks").val(data[12]);
  }
  $("#project_bid").val(data[2]);
  $("#notice_type").val('NOPQ');
  $("#bidder_modal_title").html('Edit Notice of Post Qualification');
  $("#bidder_modal").modal('show');
});

$('#bidders_table tbody').on('click', '.ineligibility-btn', function (e) {
  var data = table.row( $(this).parents('tr') ).data();
  $("#mr_due_date_container").removeClass('d-none');
  if(data[1]=="" || data[1]==null){
    $("#date_released").val('');
    $("#date_received").val('');
    $("#notice_id").val('');
  }
  else{
    $("#date_released").datepicker('setDate',moment(moment(data[9]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    $("#date_received").datepicker('setDate',moment(moment(data[10]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    $("#notice_id").val(data[1]);
    $("#remarks").val(data[12]);
  }
  $("#project_bid").val(data[2]);
  $("#notice_type").val('NOI');
  $("#bidder_modal_title").html('Edit Notice of Disqualification');
  $("#bidder_modal").modal('show');
});

$('#bidders_table tbody').on('click', '.postineligibility-btn', function (e) {
  var data = table.row( $(this).parents('tr') ).data();
  $("#mr_due_date_container").removeClass('d-none');
  if(data[1]=="" || data[1]==null){
    $("#date_released").val('');
    $("#date_received").val('');
    $("#notice_id").val('');
  }
  else{
    $("#date_released").datepicker('setDate',moment(moment(data[9]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    $("#date_received").datepicker('setDate',moment(moment(data[10]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    $("#notice_id").val(data[1]);
    $("#remarks").val(data[12]);
  }
  $("#project_bid").val(data[2]);
  $("#notice_type").val('NOPD');
  $("#bidder_modal_title").html('Edit Notice of Post Disqualification');
  $("#bidder_modal").modal('show');
});

$('#bidders_table tbody').on('click', '.notice-to-loosing-btn', function (e) {
  var data = table.row( $(this).parents('tr') ).data();
  $("#mr_due_date_container").removeClass('d-none');
  $("#mr_due_date_container").addClass('d-none');
  if(data[1]=="" || data[1]==null){
    $("#date_released").val('');
    $("#date_received").val('');
    $("#notice_id").val('');
  }
  else{
    $("#date_released").datepicker('setDate',moment(moment(data[9]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    $("#date_received").datepicker('setDate',moment(moment(data[10]).format('Y-MM-DD')).format("MM/DD/YYYY"));
    $("#notice_id").val(data[1]);
    $("#remarks").val(data[12]);
  }
  $("#project_bid").val(data[2]);
  $("#notice_type").val('NTLB');
  $("#bidder_modal_title").html('Edit Notice To Loosing Bidder');
  $("#bidder_modal").modal('show');
});

$("#date_received").change(function(){
  if($("#notice_type").val()==="NOI"||$("#notice_type").val()==="NOPD"){
    let mr_due_date="";
    if($(this).val()!=""){
      mr_due_date=moment($("#date_received").val()).add(3, 'days').format('MM/DD/YYYY');
    }
    $("#mr_due_date").val(mr_due_date);
  }
  else{
    $("#mr_due_date").val('');
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
