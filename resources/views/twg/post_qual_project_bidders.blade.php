@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">
    <div class="modal" tabindex="-1" role="dialog" id="bidder_modal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="bidder_modal_title">Set Bidder as Non Responsive</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="col-sm-12" method="POST" id="bidders_form" action="/twg_non_responsive_bidder">
              @csrf
              <div class="row">

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="process">Action Type</label>
                  <input type="text" id="process" name="process" class="form-control form-control-sm" readonly value="{{old('process')}}" >
                  <label class="error-msg text-red" >@error('process'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="bidder_id">Bidder ID</label>
                  <input type="text" id="bidder_id" name="bidder_id" class="form-control form-control-sm" readonly value="{{old('bidder_id')}}" >
                  <label class="error-msg text-red" >@error('bidder_id'){{$message}}@enderror</label>
                </div>

                <!-- Business Name -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="business_name">Business Name</label>
                  <input type="text" id="business_name" name="business_name" class="form-control form-control-sm bg-white" readonly value="{{old('business_name')}}" >
                  <label class="error-msg text-red" >@error('business_name'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="owner">Owner</label>
                  <input type="text" id="owner" name="owner" class="form-control form-control-sm bg-white" readonly value="{{old('owner')}}" >
                  <label class="error-msg text-red" >@error('owner'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="post_qual_start_date">Post Qual Start Date <span class="text-red">*</span></label>
                  <input type="text" id="post_qual_start_date" name="post_qual_start_date" class="form-control form-control-sm bg-white datepicker" value="{{old('post_qual_start_date')}}" >
                  <label class="error-msg text-red" >@error('post_qual_start_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="post_qual_end_date">Post Qual End Date <span class="text-red">*</span></label>
                  <input type="text" id="post_qual_end_date" name="post_qual_end_date" class="form-control form-control-sm bg-white datepicker" value="{{old('post_qual_end_date')}}" >
                  <label class="error-msg text-red" >@error('post_qual_end_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                  <label for="detailed_bid_as_calculated">Detailed Bid as Calculated <span class="text-red">*</span></label>
                  <input type="text" id="detailed_bid_as_calculated" name="detailed_bid_as_calculated" class="form-control form-control-sm bg-white money2" value="{{old('detailed_bid_as_calculated')}}" >
                  <label class="error-msg text-red" >@error('detailed_bid_as_calculated'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="bid_as_calculated">Bid as Calculated <span class="text-red">*</span></label>
                  <input type="text" id="bid_as_calculated" name="bid_as_calculated" class="form-control form-control-sm bg-white money2" value="{{old('bid_as_calculated')}}" >
                  <label class="error-msg text-red" >@error('bid_as_calculated'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="remarks">Remarks<span class="text-red">*</span></label>
                  <textarea type="text" id="remarks" name="remarks" class="form-control form-control-sm" value="{{old('remarks')}}" ></textarea>
                  <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror</label>
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

    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Project Bidders</h2>
          <label class="text-sm">Project Number: <span class="">{{$data->project_number}}</span></label>
          <br />
          <label class="text-sm">Project Title: <span class="">{{$data->title2}}</span></label>
          <br />
          <label class="text-sm">Date Bid Opened: <span class="">{{$data->open_bid}}</span></label>
          <br />
          <label class="text-sm">Project Cost: <span class="text-red">Php {{number_format($data->project_cost,2,'.',',')}}</span></label>
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
                  <th class="text-center">Rank</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Proposed Bid</th>
                  <th class="text-center">Bid as Evaluated</th>
                  <th class="text-center">Bid as Calculated</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Evaluation Status</th>
                  <th class="text-center">Post Qual Start Date</th>
                  <th class="text-center">Post Qual End Date</th>
                  <th class="text-center">Remarks</th>
                  <th class="text-center">Lowest Bid</th>
                </tr>
              </thead>
              <tbody>
                @php
                $row_number=1;
                @endphp
                @foreach($data->project_bidders as $bidder)
                <tr>
                  <td style="white-space: nowrap">
                    @if(in_array('update',$user_privilege))
                    @if($bidder->twg_evaluation_status==='non-responsive')
                    <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                    @elseif($bidder->twg_evaluation_status==='responsive')
                    <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                    <span class="badge bg-yellow">Responsive</span>
                    @elseif($bidder->twg_evaluation_status==='active')
                    <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                    @else
                    <button class="btn btn-sm btn-primary add-bid-as-calculated-btn">Add Bid as Calculated</button>
                    <button class="btn btn-sm btn-danger non-responsive-btn">Non-responsive</button>
                    <button class="btn btn-sm btn-success responsive-btn">Responsive</button>
                    @endif
                    @endif
                  </td>
                  <td>{{$bidder->project_bid}}  </td>
                  @php

                  if($bidder->proposed_bid!=null && $bidder->proposed_bid>0 && $bidder->twg_evaluation_status!="non-responsive"){
                    echo "<td>".$row_number."</td>";
                    $row_number=$row_number+1;
                  }
                  else{
                    echo "<td></td>";
                  }
                  @endphp
                  <td>{{$bidder->business_name}}
                    @if($bidder->date_received>$data->open_bid)
                    <span class="badge badge-danger">Late</span>
                    @endif
                  </td>
                  <td>{{$bidder->owner}}</td>
                  <td>{{number_format($bidder->proposed_bid,2,'.',',')}}</td>
                  <td>{{number_format($bidder->bid_as_evaluated,2,'.',',')}}</td>
                  <td>{{number_format($bidder->twg_final_bid_evaluation,2,'.',',')}}</td>
                  <td>{{$bidder->bid_status}}</td>
                  <td>{{$bidder->twg_evaluation_status}}</td>
                  <td>{{$bidder->post_qual_start}}</td>
                  <td>{{$bidder->post_qual_end}}</td>
                  <td style="white-space: nowrap">{{$bidder->twg_evaluation_remarks}}</td>
                  <td>{{number_format($bidder->minimum_cost,2,'.',',')}}</td>
                </tr>
                @endforeach
              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="card shadow mt-2">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Detailed Project Bidders</h2>
        </div>
        <div class="card-body">

          <div class="col-sm-12">
            <!-- <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button> -->
          </div>

          <div class="table-responsive">
            <table class="table table-bordered wrap" id="detailed_bidders_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Project Cost</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Bid in Words</th>
                  <th class="text-center">Bid as Read</th>
                  <th class="text-center">Bid as Evaluated</th>
                  <th class="text-center">Bid as Calculated</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Evaluation Status</th>
                  <th class="text-center">Post Qual Start Date</th>
                  <th class="text-center">Post Qual End Date</th>
                  <th class="text-center">Remarks</th>
                </tr>
              </thead>
              <tbody>
                @php
                $row_number=1;
                @endphp
                @foreach($data->detailed_bids as $detailed_bid)
                <tr>
                  <td style="white-space: nowrap">
                    @if(in_array("update",$user_privilege))
                    @if($detailed_bid->twg_evaluation_status==='non-responsive')
                    <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                    @elseif($detailed_bid->twg_evaluation_status==='responsive')
                    <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                    <span class="badge bg-yellow">Responsive</span>
                    @elseif($detailed_bid->twg_evaluation_status==='active')
                    <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                    @else
                    <button class="btn btn-sm btn-primary add-bid-as-calculated-btn">Add Bid as Calculated</button>
                    <button class="btn btn-sm btn-danger non-responsive-btn">Non-responsive</button>
                    <button class="btn btn-sm btn-success responsive-btn">Responsive</button>
                    @endif
                    @endif
                  </td>
                  <td>{{$detailed_bid->bid_id}}  </td>
                  <td>{{$detailed_bid->project_title}}</td>
                  <td>{{$detailed_bid->project_cost}}</td>
                  <td>{{$detailed_bid->business_name}}</td>
                  <td>{{$detailed_bid->owner}}</td>
                  <td>{{number_format($detailed_bid->detailed_bid_in_words,2,'.',',')}}</td>
                  <td>{{number_format($detailed_bid->detailed_bid_as_read,2,'.',',')}}</td>
                  <td>{{number_format($detailed_bid->detailed_bid_as_evaluated,2,'.',',')}}</td>
                  <td>@if($detailed_bid->detailed_bid_as_calculated!=null){{number_format($detailed_bid->detailed_bid_as_calculated,2,'.',',')}}@endif</td>
                  <td>{{$detailed_bid->bid_status}}</td>
                  <td>{{$detailed_bid->twg_evaluation_status}}</td>
                  <td>{{$detailed_bid->post_qual_start}}</td>
                  <td>{{$detailed_bid->post_qual_end}}</td>
                  <td style="white-space: nowrap">{{$detailed_bid->twg_evaluation_remarks}}</td>

                </tr>
                @endforeach
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
  endDate: 'now'
});

$(".money2").click(function () {
  $('.money2').mask("#,##0.00", {reverse: true});
});

$(".money2").keyup(function () {
  $('.money2').mask("#,##0.00", {reverse: true});
});

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
  } ],

});

var detailed_table=  $('#detailed_bidders_table').DataTable({
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
  } ],

});



var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>2){
  var process="{{old('process')}}";
  if(process=="responsive"){
    $("#bidders_form").prop('action','/twg_responsive_bidder');
    $("#bidder_modal_title").html("Set Responsive Bidder");
  }
  else if(process=="detailed_responsive"){
    $("#detailed_bid_as_calculated").parent().removeClass("d-none");
    $("#bidders_form").prop('action','/twg_responsive_bidder');
    $("#bidder_modal_title").html("Set Responsive Bidder");
  }
  else if(process=="clear-post-qual"){
    $("#bidders_form").prop('action','/twg_clear_post_qualification_evaluation');
    $("#post_qual_start_date").parent().removeClass('d-none');
    $("#post_qual_start_date").parent().addClass('d-none');
    $("#post_qual_end_date").parent().removeClass('d-none');
    $("#post_qual_end_date").parent().addClass('d-none');
    $("#bid_as_calculated").parent().removeClass('d-none');
    $("#bid_as_calculated").parent().addClass('d-none');
    $("#bidder_modal_title").html("Clear Post Qualification");
  }
  else if(process=="bid_as_calculated"){
    $("#bidders_form").prop('action','/twg_bid_as_calculated_bidder');
    $("#bidder_modal_title").html("Add Bid as Calculated");
  }
  else if(process=="detailed_bid_as_calculated"){
    $("#detailed_bid_as_calculated").parent().removeClass("d-none");
    $("#bidders_form").prop('action','/twg_bid_as_calculated_bidder');
    $("#bidder_modal_title").html("Add Bid as Calculated");
  }
  else if(process=="detailed_non_responsive"){
    $("#detailed_bid_as_calculated").parent().removeClass("d-none");
    $("#bidders_form").prop('action','/twg_non_responsive_bidder');
    $("#bidder_modal_title").html("Set as Non Responsive");
  }
  else{
    $("#bidders_form").prop('action','/twg_non_responsive_bidder');
    $("#bidder_modal_title").html("Set as Non Responsive");
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

  else if("{{session('message')}}"=="bidder_chosen"){
    swal.fire({
      title: `Error`,
      text: 'This project has already one responsive bidder!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#proposed_bid").modal('hide');
  }

  else if("{{session('message')}}"=="zero_bid"){
    swal.fire({
      title: `Error`,
      text: 'Sorry! Project Bidder has zero proposed bid',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#proposed_bid").modal('hide');
  }

  else if("{{session('message')}}"=="range_error"){
    swal.fire({
      title: `Error`,
      text: 'Sorry! Post Qual Start - End Date should be in the range of Post Qualification Timeline.',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#proposed_bid").modal('hide');
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
    $(this).html( '<input class="px-0 mx-0" type="text" placeholder="Search '+title+'" />' );
    $(this).addClass('sorting_disabled');
    $( 'input', this ).on( 'keyup change', function () {
      if ( table.column(i).search() !== this.value ) {
        table
        .column(i)
        .search( this.value )
        .draw();
      }
    } );
  }
});


function clearpostqual(bidder_id,business_name,owner) {
  Swal.fire({
    title:'Clear Post Qualification Evaluation',
    text: 'Are you sure to Clear Post Qualification Evaluation this Project Bidder?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#bidders_form").prop("action","{{route('twg_clear_post_qualification_evaluation')}}");
      $("#bidder_id").val(bidder_id);
      $("#detailed_bid_as_calculated").parent().removeClass("d-none");
      $("#detailed_bid_as_calculated").parent().addClass("d-none");
      $("#process").val("clear-post-qual");
      $("#post_qual_start_date").parent().removeClass('d-none');
      $("#post_qual_start_date").parent().addClass('d-none');
      $("#post_qual_end_date").parent().removeClass('d-none');
      $("#post_qual_end_date").parent().addClass('d-none');
      $("#bid_as_calculated").parent().removeClass('d-none');
      $("#bid_as_calculated").parent().addClass('d-none');
      $("#bidder_modal_title").html("Clear Post Qualification Evaluation");
      business_name=business_name.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(business_name.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">1st</span>',''));
      $("#owner").val(owner);

      $("#bidder_modal").modal('show');
    }
  });
}


// Non-responsive
$('#bidders_table tbody').on('click', '.non-responsive-btn', function (e) {
  Swal.fire({
    title:'Set Project Bidder as Non-responsive',
    text: 'Are you sure to Set Bidder Status as Non-responsive?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#process").val("non-responsive");
      $("#bidders_form").prop('action','/twg_non_responsive_bidder');
      $("#bidder_modal_title").html("Set Non-Responsive Bidder");
      var data = table.row( $(this).parents('tr') ).data();
      $("#bid_as_calculated").val(data[13]);
      $("#bidder_id").val(data[1]);
      var  owner=data[3];
      owner=owner.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>',''));
      $("#owner").val(data[4]);
      $("#bidder_modal").modal('show');
    }
  });
});

$('#detailed_bidders_table tbody').on('click', '.non-responsive-btn', function (e) {
  Swal.fire({
    title:'Set Project Bidder as Non-responsive',
    text: 'Are you sure to Set Bidder Status as Non-responsive?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#process").val("detailed_non_responsive");
      $("#bidders_form").prop('action','/twg_non_responsive_bidder');
      $("#bidder_modal_title").html("Set Non-Responsive Bidder");
      var data = detailed_table.row( $(this).parents('tr') ).data();
      $("#bidder_id").val(data[1]);
      var  owner=data[4];
      $("#detailed_bid_as_calculated").parent().removeClass('d-none');
      owner=owner.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>',''));
      $("#owner").val(data[5]);
      $("#detailed_bid_as_calculated").parent().removeClass("d-none");
      $("#bidder_modal").modal('show');
    }
  });

});

@if(in_array("update",$user_privilege))
$('#bidders_table tbody').on('click', '.responsive-btn', function (e) {
  Swal.fire({
    title:'Set Project Bidder as Responsive',
    text: 'Are you sure to Set Bidder Status as Responsive?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-success btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#detailed_bid_as_calculated").parent().removeClass("d-none");
      $("#detailed_bid_as_calculated").parent().addClass("d-none");
      $("#bidders_form").prop('action','/twg_responsive_bidder');
      $("#bidder_modal_title").html("Set Responsive Bidder");
      $("#process").val("responsive");

      var data = table.row( $(this).parents('tr') ).data();
      $("#bidder_id").val(data[1]);
      $("#bid_as_calculated").val(data[13]);
      var  owner=data[3];
      owner=owner.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>',''));
      $("#owner").val(data[4]);
      $("#bidder_modal").modal('show');
    }
  });
});
$('#detailed_bidders_table tbody').on('click', '.responsive-btn', function (e) {
  Swal.fire({
    title:'Set Project Bidder as Responsive',
    text: 'Are you sure to Set Bidder Status as Responsive?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-success btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#detailed_bid_as_calculated").parent().removeClass("d-none");
      $("#bidders_form").prop('action','/twg_responsive_bidder');
      $("#bidder_modal_title").html("Set Responsive Bidder");
      $("#process").val("detailed_responsive");
      var data = detailed_table.row( $(this).parents('tr') ).data();
      $("#bidder_id").val(data[1]);
      var  owner=data[4];
      owner=owner.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>',''));
      $("#owner").val(data[5]);
      $("#bidder_modal").modal('show');
    }
  });
});
$('#bidders_table tbody').on('click', '.add-bid-as-calculated-btn', function (e) {
  Swal.fire({
    title:'Add Bid as Calculated',
    text: 'Are you sure to Add Bid as Calculated',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-success btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#detailed_bid_as_calculated").parent().removeClass('d-none');
      $("#detailed_bid_as_calculated").parent().addClass('d-none');
      $("#bidders_form").prop('action','/twg_bid_as_calculated_bidder');
      $("#bidder_modal_title").html("Add Bid as Calculated");
      $("#process").val("bid_as_calculated");
      var data = table.row( $(this).parents('tr') ).data();
      $("#bidder_id").val(data[1]);
      var  owner=data[3];
      $("#bid_as_calculated").val(data[13]);
      owner=owner.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>',''));
      $("#owner").val(data[4]);
      $("#bidder_modal").modal('show');
    }
  });

});
$('#detailed_bidders_table tbody').on('click', '.add-bid-as-calculated-btn', function (e) {
  Swal.fire({
    title:'Add Bid as Calculated',
    text: 'Are you sure to Add Bid as Calculated',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-success btn btn-sm',
    cancelButtonClass: 'btn btn-sm btn-default btn btn-sm',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      $("#bidders_form").prop('action','/twg_bid_as_calculated_bidder');
      $("#bidder_modal_title").html("Add Bid as Calculated");
      $("#detailed_bid_as_calculated").parent().removeClass('d-none');
      $("#process").val("detailed_bid_as_calculated");
      var data = detailed_table.row( $(this).parents('tr') ).data();
      $("#bidder_id").val(data[1]);
      var  owner=data[4];
      owner=owner.replace('                                                            <span class="badge badge-danger">Late</span>','');
      $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>',''));
      $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>',''));
      $("#owner").val(data[5]);
      $("#bidder_modal").modal('show');
    }
  });

});
@endif

@if(in_array("delete",$user_privilege))
$('#bidders_table tbody').on('click', '.clear-post-qual-btn', function (e) {
  var data = table.row( $(this).parents('tr') ).data();
  var bidder_id=data[1];
  var business_name=data[3];
  var owner=data[4];
  clearpostqual(bidder_id,business_name,owner);
});
$('#detailed_bidders_table tbody').on('click', '.clear-post-qual-btn', function (e) {
  var data = detailed_table.row( $(this).parents('tr') ).data();
  var bidder_id=data[1];
  var business_name=data[4];
  var owner=data[5];
  clearpostqual(bidder_id,business_name,owner);
});
@endif

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
