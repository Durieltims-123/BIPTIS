@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="">
    <div class="col-sm-12">
      <div class="modal" tabindex="-1" role="dialog" id="schedule_modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="schedule_modal_title">Modal title</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body pt-0">
                </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12 d-none" id="filter">
            <form class="row" id="app_filter" method="post" action="{{route('filter_schedule')}}">
              @csrf

              <!-- Mode -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="project_year" class="input-sm">Mode</label>
                <input  class="form-control form-control-sm" id="mode" name="mode" value="{{old('mode')}}" >
                <label class="error-msg text-red" >@error('mode'){{$message}}@enderror</label>
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
                <input type="text" id="date_added" name="date_added" class="form-control form-control-sm" value="{{old('date_added')}}" >
                <label class="error-msg text-red" >@error('date_added'){{$message}}@enderror</label>
              </div>

            </form>
          </div>
          <div class="col-sm-12 d-none">
            <form class="row" id="cluster_form" method="post" action="{{route('submit_cluster')}}">
              @csrf
              <div class="form-group col-xs-3 col-sm-3 col-lg-3 mx-auto d-none">
                <label for="">Plan Id/s</label>
                <input type="text" id="cluster_ids" name="cluster_ids" class="form-control form-control-sm" value="{{old('cluster_ids')}}" >
                <label class="error-msg text-red" >@error('cluster_ids'){{$message}}@enderror</label>
              </div>
              <!-- Mode -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="" class="input-sm">Mode</label>
                <input  class="form-control form-control-sm" id="cluster_mode" name="cluster_mode" value="{{old('cluster_mode')}}" >
                <label class="error-msg text-red" >@error('cluster_mode'){{$message}}@enderror</label>
              </div>
              <!--Cluster -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="" class="input-sm">Cluster Process</label>
                <input  class="form-control form-control-sm" id="cluster_process" name="cluster_process" value="{{old('cluster_process')}}" >
                <label class="error-msg text-red" >@error('cluster_process'){{$message}}@enderror</label>
              </div>
            </form>
          </div>

          <div class="col-sm-12">
            <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button>
            <button class="btn btn-sm btn-danger text-white float-right mb-2 btn btn-sm ml-2" id="uncluster">Uncluster</button>
            <button class="btn btn-smbg-yellow text-white float-right mb-2 btn btn-sm ml-2" id="add_cluster">Cluster</button>
            <button class="btn btn-sm btn-primary text-white float-right mb-2 btn btn-sm ml-2" id="add_schedules">Add Schedules</button>
            <button class="btn btn-sm btn-warning text-white float-right mb-2 btn btn-sm ml-2 d-none" id="filter_btn">Filter</button>
            <button class="btn btn-sm btn-default text-white float-right mb-2 btn btn-sm ml-2" id="show_filter">Show Filter</button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">APP Type</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Current Cluster</th>
                  <th class="text-center">Ads/Posting</th>
                  <th class="text-center">Pre-bid</th>
                  <th class="text-center">Submission of Bid</th>
                  <th class="text-center">Bid Evaluation</th>
                  <th class="text-center">Post Qualification</th>
                  <th class="text-center">Issuance of NOA</th>
                  <th class="text-center">Contract Signing</th>
                  <th class="text-center">Approval by Higher Authority</th>
                  <th class="text-center">Notice to Proceed</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Approved Budget Cost</th>
                  <th class="text-center">Project Year</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @if(session('newData'))

                @foreach(session('newData') as $project_plan)
                <tr>

                  @if($project_plan->timeline_status != null && $project_plan->timeline_status != 'pending')
                  <td><button class="btn btn-sm shadow-0 border-0 btn-success edit_schedule" value='{{ $project_plan->plan_id }}'>Edit</button><button value='{{ $project_plan->plan_id }}' class="btn btn-sm shadow-0 border-0 btn-danger delete_btn">delete</button>@if($project_plan->current_cluster) <span class="badge bg-yellow">Clustered</span>@endif</td>
                  @else
                  <td><button  class="btn btn-sm shadow-0 border-0 btn-primary text-white add_schedule" value='{{ $project_plan->plan_id }}'>Add Schedule</button> @if($project_plan->current_cluster) <span class="badge bg-yellow">Clustered</span>@endif</td>
                  @endif

                  @if($project_plan->advertisement_start)
                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>{{ $project_plan->project_type }}</td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->advertisement_start))}} - {{ date("M d,Y", strtotime($project_plan->advertisement_end))}}</td>
                  <td>@if($project_plan->pre_bid_start) {{ date("M d,Y", strtotime($project_plan->pre_bid_start))}} - {{ date("M d,Y", strtotime($project_plan->pre_bid_end))}} @endif</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->bid_submission_start))}} - {{ date("M d,Y", strtotime($project_plan->bid_submission_end))}}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->post_qualification_start))}} - {{ date("M d,Y", strtotime($project_plan->post_qualification_end))}}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->post_qualification_start))}} - {{ date("M d,Y", strtotime($project_plan->post_qualification_end))}}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->award_notice_start))}} - {{ date("M d,Y", strtotime($project_plan->award_notice_end))}}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->contract_signing_start))}} - {{ date("M d,Y", strtotime($project_plan->contract_signing_end))}}</td>
                  <td>@if($project_plan->authority_approval_start) {{ date("M d,Y", strtotime($project_plan->authority_approval_start))}} - {{ date("M d,Y", strtotime($project_plan->authority_approval_end))}} @endif</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->proceed_notice_start))}} - {{ date("M d,Y", strtotime($project_plan->proceed_notice_end))}}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->abc }}</td>
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->project_status }}</td>
                  @else
                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>{{ $project_plan->project_type }}</td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->abc }}</td>
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->project_status }}</td>
                  @endif

                </tr>
                @endforeach



                @else
                @foreach($project_plans as $project_plan)
                <tr>

                  @if($project_plan->timeline_status != null && $project_plan->timeline_status != 'pending')
                  <td><button class="btn btn-sm shadow-0 border-0 btn-success edit_schedule" value='{{ $project_plan->plan_id }}'>Edit</button><button value='{{ $project_plan->plan_id }}' class="btn btn-sm shadow-0 border-0 btn-danger delete_btn">delete</button>@if($project_plan->current_cluster) <span class="badge bg-yellow">Clustered</span>@endif</td>
                  @else
                  <td><button  class="btn btn-sm shadow-0 border-0 btn-primary text-white add_schedule" value='{{ $project_plan->plan_id }}'>Add Schedule</button>@if($project_plan->current_cluster) <span class="badge bg-yellow">Clustered</span>@endif</td>
                  @endif

                  @if($project_plan->advertisement_start)
                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>{{ $project_plan->project_type }}</td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->advertisement_start))}} - {{ date("M d,Y", strtotime($project_plan->advertisement_end))}}</td>
                  <td>@if($project_plan->pre_bid_start) {{ date("M d,Y", strtotime($project_plan->pre_bid_start))}} - {{ date("M d,Y", strtotime($project_plan->pre_bid_end))}} @endif</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->bid_submission_start))}} - {{ date("M d,Y", strtotime($project_plan->bid_submission_end))}}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->bid_evaluation_start))}} - {{ date("M d,Y", strtotime($project_plan->bid_evaluation_end))}}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->post_qualification_start))}} - {{ date("M d,Y", strtotime($project_plan->post_qualification_end))}}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->award_notice_start))}} - {{ date("M d,Y", strtotime($project_plan->award_notice_end))}}</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->contract_signing_start))}} - {{ date("M d,Y", strtotime($project_plan->contract_signing_end))}}</td>
                  <td>@if($project_plan->authority_approval_start) {{ date("M d,Y", strtotime($project_plan->authority_approval_start))}} - {{ date("M d,Y", strtotime($project_plan->authority_approval_end))}} @endif</td>
                  <td>{{ date("M d,Y", strtotime($project_plan->proceed_notice_start))}} - {{ date("M d,Y", strtotime($project_plan->proceed_notice_end))}}</td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->abc }}</td>
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->project_status }}</td>
                  @else
                  <td>{{ $project_plan->plan_id }}</td>
                  <td>{{ $project_plan->project_no }}</td>
                  <td><?php echo str_replace('&quot;','"',$project_plan->project_title) ?></td>
                  <td>{{ $project_plan->project_type }}</td>
                  <td>@if( $project_plan->barangay_name!=null){{ $project_plan->barangay_name }},@endif{{ $project_plan->municipality_name }}</td>
                  <td>{{ $project_plan->current_cluster }}</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>{{ $project_plan->source }}</td>
                  <td>{{ $project_plan->abc }}</td>
                  <td>{{ $project_plan->project_year }}</td>
                  <td>{{ $project_plan->project_status }}</td>
                  @endif

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

$(window).keydown(function(event){
  if(event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});

// mode
$("#mode").val('{{$mode}}');
$("#cluster_mode").val('{{$mode}}');

// datatables
$('#app_table thead tr').clone(true).appendTo( '#app_table thead' );
$('#app_table thead tr:eq(1)').removeClass('bg-primary');

$("#date_added").datepicker({
  format: 'mm/dd/yyyy',
  endDate:'{{$year}}'
});

$("#begin_date").datepicker({
  format: 'mm/dd/yyyy',
});


$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
  autoclose: true,
  language: 'da',
  enableOnReadonly: false
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
  } ],
});

// show inputs/messages on load
var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>32||oldInputs==3){
  // $('#filter').removeClass('d-none');
  // $('#filter_btn').removeClass('d-none');
  // $("#show_filter").html("Hide Filter");
}
else{
  $("#project_year").val("{{$year}}");
}
if(oldInputs==3){
  $("#schedule_modal").modal('show');
}

if("{{session('message')}}"){
  if("{{session('message')}}"=="delete_error"){
    swal.fire({
      title: `Error`,
      text: 'You cannot delete Schedule for Ongoing Procurement Activities',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="delete_success"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Successfully deleted Schedule',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="cluster_success"){
    Swal.fire({
      title: `Cluster Success`,
      text: 'Successfully Clustered Plans',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="uncluster_success"){
    Swal.fire({
      title: `Uncluster Success`,
      text: 'Successfully Unclustered Plans',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="multiple status"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Sorry! Some Selected rows have already defined schedule.',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="set status"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Sorry! Selected rows have already defined schedules.',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'success'
    });
  }

  else{
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact your system developer',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
}

// functions
function fillForm() {
  if($("#begin_date").val()!=""){

    var pre_bid_days=1;
    var sub_of_bid_days=1;
    var bid_eval_days=1;
    var post_qual_days=1;
    var iss_of_noa_days=1;
    var contract_prep_days=1;
    var app_by_ha_days=1;
    var ntp_days=1;


    // advertisement
    $("#ads-post-start").val(moment($("#begin_date").val(),"MM/DD/YYYY").format("MM/DD/YYYY"));
    $("#ads-post-end").val(moment($("#begin_date").val(),"MM/DD/YYYY").add(7, 'day').format("MM/DD/YYYY"));
    var date=moment($("#ads-post-end").val(),"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY");

    // prebid
    if($("#pre-bid-yes").prop('checked')==true){
      if(parseInt($("#pre-bid-days").val())>53)
      {
        $("#pre-bid-days").val('');
        swal.fire({
          title: `Error`,
          text: 'Pre-bid Conference should not exceed 53 calendar days.',
          buttonsStyling: false,
          confirmButtonClass: 'btn btn-sm btn-warning',
          icon: 'warning'
        });
      }
      else if(parseInt($("#pre-bid-days").val())>1){
        pre_bid_days=parseInt($("#pre-bid-days").val());
      }
      else if(parseInt($("#pre-bid-days").val())<=1){
        $("#pre-bid-days").val('');
        swal.fire({
          title: `Error`,
          text: 'Allowed input days must be atleast 2 days.',
          buttonsStyling: false,
          confirmButtonClass: 'btn btn-sm btn-warning',
          icon: 'warning'
        });
      }
      else{

      }
      $("#pre-bid-start").val(moment(date,"MM/DD/YYYY").format("MM/DD/YYYY"));
      $("#pre-bid-end").val(moment(date,"MM/DD/YYYY").add(pre_bid_days, 'day').format("MM/DD/YYYY"));
      date=moment($("#pre-bid-end").val(),"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY");
    }

    // submission of bid
    if(parseInt($("#sub-of-bid-days").val())>65)
    {
      $("#sub-of-bid-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Bids cannot be submitted beyond the 65th day from the last day of Advertisement',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else if(parseInt($("#sub-of-bid-days").val())>1){
      sub_of_bid_days=parseInt($("#sub-of-bid-days").val());
    }
    else if(parseInt($("#sub-of-bid-days").val())<=1){
      $("#sub-of-bid-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Allowed input days must be atleast 2 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else{

    }
    $("#sub-of-bid-start").val(moment(date,"MM/DD/YYYY").format("MM/DD/YYYY"));
    $("#sub-of-bid-end").val(moment(date,"MM/DD/YYYY").add(sub_of_bid_days, 'day').format("MM/DD/YYYY"));
    date=moment($("#sub-of-bid-end").val(),"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY");

    // bid evaluation
    if(parseInt($("#bid-eval-days").val())>7)
    {
      $("#bid-eval-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Evaluation of bids should not exceed 7 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else if(parseInt($("#bid-eval-days").val())>1){
      bid_eval_days=parseInt($("#bid-eval-days").val());
    }
    else if(parseInt($("#bid-eval-days").val())<=1){
      $("#bid-eval-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Allowed input days must be atleast 2 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else{

    }
    $("#bid-eval-start").val(moment(date,"MM/DD/YYYY").format("MM/DD/YYYY"));
    $("#bid-eval-end").val(moment(date,"MM/DD/YYYY").add(bid_eval_days, 'day').format("MM/DD/YYYY"));
    date=moment($("#bid-eval-end").val(),"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY");

    // post qualification
    if(parseInt($("#post-qual-days").val())>45)
    {
      $("#post-qual-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Post-qualification cannot exceed 45 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else if(parseInt($("#post-qual-days").val())>1){
      post_qual_days=parseInt($("#post-qual-days").val());
    }
    else if(parseInt($("#post-qual-days").val())<=1){
      $("#post-qual-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Allowed input days must be atleast 2 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else{

    }
    $("#post-qual-start").val(moment(date,"MM/DD/YYYY").format("MM/DD/YYYY"));
    $("#post-qual-end").val(moment(date,"MM/DD/YYYY").add(post_qual_days, 'day').format("MM/DD/YYYY"));
    date=moment($("#post-qual-end").val(),"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY");

    // Issuance of NOA
    if(parseInt($("#iss-of-noa-days").val())>15)
    {
      $("#iss-of-noa-days").val('');
      swal.fire({
        title: `Error`,
        text: 'The longest allowable time for Issuance of NOA is 15 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else if(parseInt($("#iss-of-noa-days").val())>1){
      iss_of_noa_days=parseInt($("#iss-of-noa-days").val());
    }
    else if(parseInt($("#iss-of-noa-days").val())<=1){
      $("#iss-of-noa-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Allowed input days must be atleast 2 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else{

    }
    $("#iss-of-noa-start").val(moment(date,"MM/DD/YYYY").format("MM/DD/YYYY"));
    $("#iss-of-noa-end").val(moment(date,"MM/DD/YYYY").add(iss_of_noa_days, 'day').format("MM/DD/YYYY"));
    date=moment($("#iss-of-noa-end").val(),"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY");

    // Contract Preparation
    if(parseInt($("#contract-prep-days").val())>10)
    {
      $("#contract-prep-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Contract Preparation and Signing must be completed in 10 calendar days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else if(parseInt($("#contract-prep-days").val())>1){
      contract_prep_days=parseInt($("#contract-prep-days").val());
    }
    else if(parseInt($("#contract-prep-days").val())<=1){
      $("#contract-prep-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Allowed input days must be atleast 2 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else{

    }
    $("#contract-prep-start").val(moment(date,"MM/DD/YYYY").format("MM/DD/YYYY"));
    $("#contract-prep-end").val(moment(date,"MM/DD/YYYY").add(contract_prep_days, 'day').format("MM/DD/YYYY"));
    date=moment($("#contract-prep-end").val(),"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY");

    // Approval of Higher Authority
    if($("#app-by-ha-yes").prop("checked")==true){
      if(parseInt($("#app-by-ha-days").val())>25)
      {
        $("#app-by-ha-days").val('');
        swal.fire({
          title: `Error`,
          text: 'The longest allowable time for this stage is 25 days.',
          buttonsStyling: false,
          confirmButtonClass: 'btn btn-sm btn-warning',
          icon: 'warning'
        });
      }
      else if(parseInt($("#app-by-ha-days").val())>1){
        app_by_ha_days=parseInt($("#app-by-ha-days").val());
      }
      else if(parseInt($("#app-by-ha-days").val())<=1){
        $("#app-by-ha-days").val('');
        swal.fire({
          title: `Error`,
          text: 'Allowed input days must be atleast 2 days.',
          buttonsStyling: false,
          confirmButtonClass: 'btn btn-sm btn-warning',
          icon: 'warning'
        });

      }
      else{

      }
      $("#app-by-ha-start").val(moment(date,"MM/DD/YYYY").format("MM/DD/YYYY"));
      $("#app-by-ha-end").val(moment(date,"MM/DD/YYYY").add(app_by_ha_days, 'day').format("MM/DD/YYYY"));
      date=moment($("#app-by-ha-end").val(),"MM/DD/YYYY").add(1, 'day').format("MM/DD/YYYY");
    }

    // Notice to Proceed
    if(parseInt($("#ntp-days").val())>3)
    {
      $("#ntp-days").val('');
      swal.fire({
        title: `Error`,
        text: 'The NTP must be issued within 3 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else  if(parseInt($("#ntp-days").val())>1){
      ntp_days=parseInt($("#ntp-days").val());
    }
    else  if(parseInt($("#ntp-days").val())<=1){
      $("#ntp-days").val('');
      swal.fire({
        title: `Error`,
        text: 'Allowed input days must be atleast 2 days.',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
        icon: 'warning'
      });
    }
    else{

    }
    $("#ntp-start").val(moment(date,"MM/DD/YYYY").format("MM/DD/YYYY"));
    $("#ntp-end").val(moment(date,"MM/DD/YYYY").add(ntp_days, 'day').format("MM/DD/YYYY"));
    $("#submit_btn").prop("disabled",false);
  }
  else{
    swal.fire({
      title: `Input Error`,
      text: 'Please Input Date to Begin With ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-warning',
      icon: 'warning'
    });
  }
}

// get dates and formatDate
function formatDateRange(string) {
  var array=[];
  var oldDates=string.split(" - ");
  var days=days;
  var startDate=null;
  var endDate=null;
  if(oldDates.length>0){
    startDate=moment(oldDates[0],"MMM DD,YYYY");
    endDate=moment(oldDates[1],"MMM DD,YYYY");
    days=endDate.diff(startDate,'days');
    if(days<=1){
      days=null;
    }
  }
  array.push(moment(startDate).format("MM/DD/YYYY"));
  array.push(moment(endDate).format("MM/DD/YYYY"));
  array.push(days);
  return array;
}
// events
$('#app_table thead tr:eq(1) th').each( function (i) {
  var title = $(this).text();
  if(title!=""){
    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
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

// show delete
$('#app_table tbody').on('click', '.delete_btn', function (e) {

  Swal.fire({
    text: 'Are you sure to delete Schedule?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_schedule/"+$(this).val();
    }
  });
});

$('#app_table tbody').on('click', '.add_schedule', function (e) {
  table.rows().deselect();
  $("#schedule_modal_title").html("Add Schedule");
  $("#submit_schedule")[0].reset();
  $("#plan_ids").val($(this).val());
  $("#schedule_modal").modal('show');
});

$('#app_table tbody').on('click', '.edit_schedule', function (e) {
  $("#submit_schedule")[0].reset();
  table.rows().deselect();
  var data = table.row( $(this).parents('tr') ).data();
  var advertisement_post=formatDateRange(data[7]);
  var prebid=null;
  var sub_of_bid=formatDateRange(data[9]);
  var bid_eval=formatDateRange(data[10]);
  var post_qual=formatDateRange(data[11]);
  var iss_of_noa=formatDateRange(data[12]);
  var contract_signing=formatDateRange(data[13]);
  var app_by_ha=null;
  var ntp=formatDateRange(data[15]);

  $("#begin_date").val(advertisement_post[0]);
  $("#ads-post-start").val(advertisement_post[0]);
  $("#ads-post-end").val(advertisement_post[1]);
  $("#sub-of-bid-start").val(sub_of_bid[0]);
  $("#sub-of-bid-end").val(sub_of_bid[1]);
  $("#sub-of-bid-days").val(sub_of_bid[2]);
  $("#bid-eval-start").val(bid_eval[0]);
  $("#bid-eval-end").val(bid_eval[1]);
  $("#bid-eval-days").val(bid_eval[2]);
  $("#post-qual-start").val(post_qual[0]);
  $("#post-qual-end").val(post_qual[1]);
  $("#post-qual-days").val(post_qual[2]);
  $("#iss-of-noa-start").val(iss_of_noa[0]);
  $("#iss-of-noa-end").val(iss_of_noa[1]);
  $("#iss-of-noa-days").val(iss_of_noa[2]);
  $("#contract-prep-start").val(contract_signing[0]);
  $("#contract-prep-end").val(contract_signing[1]);
  $("#contract-prep-days").val(contract_signing[2]);
  $("#ntp-start").val(ntp[0]);
  $("#ntp-end").val(ntp[1]);
  $("#ntp-days").val(ntp[2]);

  if(data[8]==""){
    $("#pre-bid-no").prop('checked',true);
  }
  else{
    $("#pre-bid-yes").prop('checked',true);
    prebid=formatDateRange(data[7]);
    $("#pre-bid-start").val(prebid[0]);
    $("#pre-bid-end").val(prebid[1]);
    $("#pre-bid-days").val(prebid[2]);
    $("#pre-bid-start").addClass('bg-white');
    $("#pre-bid-end").addClass('bg-white');
    $("#pre-bid-days").addClass('bg-white');
    $("#pre-bid-days").prop('readonly',false);
  }

  if(data[14]==""){
    $("#app-by-ha-no").prop('checked',true);
  }
  else{
    $("#app-by-ha-yes").prop('checked',true);
    app_by_ha=formatDateRange(data[13]);
    $("#app-by-ha-start").val(app_by_ha[0]);
    $("#app-by-ha-end").val(app_by_ha[1]);
    $("#app-by-ha-days").val(app_by_ha[2]);
    $("#app-by-ha-start").addClass('bg-white');
    $("#app-by-ha-end").addClass('bg-white');
    $("#app-by-ha-days").addClass('bg-white');
    $("#app-by-ha-days").prop('readonly',false);

  }
  $("#submit_btn").prop("disabled",false);
  $("#schedule_modal_title").html("Update Schedule");
  $("#plan_ids").val($(this).val());
  $("#schedule_modal").modal('show');
});


$("#add_schedules").click(function functionName() {
  if(table.rows( { selected: true } ).count()==0){
    Swal.fire({
      title:"Warning",
      text: 'Please select rows to add Schedule ',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-info',
      icon: 'info'
    });
  }
  else{
    $("#submit_schedule")[0].reset();
    let rows = table.rows( { selected: true } );
    var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray()
    $("#plan_ids").val(plan_ids.toString());
    $("#schedule_modal").modal('show');

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

$( "#plan_ids" ).keyup(function() {
  var str = $(this).val();
  str=str.replace(/[^0-9\-\a-zA-Z \@\#\.\/\,]/g,'');
  $(this).val(str);
});

//compute button
$("#compute-btn").click(function functionName() {
  if($("#begin_date").val()==null||$("#begin_date").val()==""){
    swal.fire({
      title: `Input Error`,
      text: 'Please Input Date to Begin With ',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-warning',
      icon: 'warning'
    });
  }
  else{
    var plan_ids=$("#plan_ids").val();
    var begin_date=$("#begin_date").val();
    $("#submit_schedule")[0].reset();
    $("#begin_date").val(begin_date);
    $("#plan_ids").val(plan_ids);
    fillForm();
  }
});


$("#add_cluster").click(function functionName() {
  $("#cluster_process").val("add_cluster");
  if(table.rows( { selected: true } ).count()<=1){
    Swal.fire({
      title:"Warning",
      text: 'Please select rows to Cluster ',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-info',
      icon: 'info'
    });
  }
  else{
    let rows = table.rows( { selected: true } );
    var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
    var plan_number =  table.cells( rows.nodes(), 2 ).data().toArray();

    Swal.fire({
      text: 'Are you sure to Cluster Plans '+plan_number.toString()+'?',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: "No",
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-smbg-yellow text-white',
      cancelButtonClass: 'btn btn-sm btn-default',
      icon: 'warning'
    }).then((result) => {
      if(result.value==true){
        $("#cluster_ids").val(plan_ids.toString());
        $("#cluster_form").submit();
      }
    });
  }
});


$("#uncluster").click(function functionName() {
  $("#cluster_process").val("uncluster");
  if(table.rows( { selected: true } ).count()==0){
    Swal.fire({
      title:"Warning",
      text: 'Please select rows to Uncluster ',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-info',
      icon: 'info'
    });
  }
  else{
    let rows = table.rows( { selected: true } );
    var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
    var plan_number =  table.cells( rows.nodes(), 2 ).data().toArray();

    Swal.fire({
      text: 'Are you sure to Uncluster Plan/s '+plan_number.toString()+'?',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: "No",
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-smbg-yellow text-white',
      cancelButtonClass: 'btn btn-sm btn-default',
      icon: 'warning'
    }).then((result) => {
      if(result.value==true){
        $("#cluster_ids").val(plan_ids.toString());
        $("#cluster_form").submit();
      }
    });
  }
});


$("#start-over-btn").click(function functionName() {
  $("#submit_schedule")[0].reset();
  $("#pre-bid-start").removeClass("bg-white");
  $("#pre-bid-end").removeClass("bg-white");
  $("#pre-bid-days").removeClass("bg-white");
  $("#pre-bid-days").prop("readonly",true);
  $("#app-by-start").removeClass("bg-white");
  $("#app-by-end").removeClass("bg-white");
  $("#app-by-days").removeClass("bg-white");
  $("#app-by-days").prop("readonly",true);
  $("#submit_btn").prop("disabled",true);
});

$("#pre-bid-no").click(function functionName() {
  $("#pre-bid-start").val("");
  $("#pre-bid-end").val("");
  $("#pre-bid-days").val("");
  $("#pre-bid-start").removeClass("bg-white");
  $("#pre-bid-end").removeClass("bg-white");
  $("#pre-bid-days").removeClass("bg-white");
  $("#pre-bid-days").prop("readonly",true);
  fillForm();
});

$("#pre-bid-yes").click(function functionName() {
  $("#pre-bid-start").removeClass("bg-white");
  $("#pre-bid-end").removeClass("bg-white");
  $("#pre-bid-days").removeClass("bg-white");
  $("#pre-bid-start").addClass("bg-white");
  $("#pre-bid-end").addClass("bg-white");
  $("#pre-bid-days").addClass("bg-white");
  $("#pre-bid-days").prop("readonly",false);
  fillForm();
});

$("#app-by-ha-no").click(function functionName() {
  $("#app-by-ha-start").val("");
  $("#app-by-ha-end").val("");
  $("#app-by-ha-days").val("");
  $("#app-by-start").removeClass("bg-white");
  $("#app-by-end").removeClass("bg-white");
  $("#app-by-days").removeClass("bg-white");
  $("#app-by-days").prop("readonly",true);
  fillForm();
});

$("#app-by-ha-yes").click(function functionName() {
  $("#app-by-ha-start").removeClass("bg-white");
  $("#app-by-ha-end").removeClass("bg-white");
  $("#app-by-ha-days").removeClass("bg-white");
  $("#app-by-ha-start").addClass("bg-white");
  $("#app-by-ha-end").addClass("bg-white");
  $("#app-by-ha-days").addClass("bg-white");
  $("#app-by-ha-days").prop("readonly",false);
  fillForm();
});


$(".update_trigger").click(function functionName() {
  fillForm();
});



</script>
@endpush
