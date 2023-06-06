@extends('layouts.app')
<style>
ul.ui-autocomplete {
  z-index: 1100;
}
</style>
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">
    <div class="col-sm-12">
      <div class="modal" tabindex="-1" role="dialog" id="ntspqd_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="ntspqd_modal_title">@if(old('ntspqd_id')!=="")Update @else Add @endif Release RFQ </h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="ntspqd_form" action="submit_ntspqd">
                @csrf
                <div class="row d-flex">

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <label for="ntspqd_id">NTSPQD ID</label>
                    <input type="text" id="ntspqd_id" name="ntspqd_id" class="form-control form-control-sm" value="{{old('ntspqd_id')}}" >
                    <label class="error-msg text-red" >@error('ntspqd_id'){{$message}}@enderror</label>
                  </div>
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <label for="project_bid">Project bid</label>
                    <input type="text" id="project_bid" name="project_bid" class="form-control form-control-sm" value="{{old('project_bid')}}" >
                    <label class="error-msg text-red" >@error('project_bid'){{$message}}@enderror</label>
                  </div>

                  <!-- project_title -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="plan_title">Project Title</label>
                    <input list="titles" type="text" id="plan_title" name="plan_title" class="form-control form-control-sm" value="{{old('plan_title')}}" readonly >
                    <label class="error-msg text-red" >@error('plan_title'){{$message}}@enderror</label>
                  </div>


                  <!-- Contractor  -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 ">
                    <label for="contractor">Name of Firm</label>
                    <input  readonlytype="text" id="contractor" name="contractor" class="form-control form-control-sm" value="{{old('contractor')}}" readonly >
                    <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror</label>
                  </div>

                  <!-- Opening Date -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="date_of_opening">Date  of Opening</label>
                    <input  type="text" id="date_of_opening" name="date_of_opening" class="form-control form-control-sm" value="{{old('date_of_opening')}}" readonly >
                    <label class="error-msg text-red" >@error('date_of_opening'){{$message}}@enderror</label>
                  </div>



                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 border pb-2">
                    <label>Documents (Please Check Present Documents)</label>
                    <div class="custom-control custom-checkbox">
                      <input  type="checkbox" id="latest_income_and_business_tax" name="latest_income_and_business_tax" class="custom-control-input" value="{{old('latest_income_and_business_tax')}}"  >
                      <label class="custom-control-label" for="latest_income_and_business_tax">Latest Income and Business Tax Return</label>
                      <label class="error-msg text-red" >@error('latest_income_and_business_tax'){{$message}}@enderror</label>
                    </div>

                    <div class="custom-control custom-checkbox">
                      <input  type="checkbox" id="provincial_permit" name="provincial_permit" class="custom-control-input" value="{{old('provincial_permit')}}"  >
                      <label class="custom-control-label" for="provincial_permit">Provincial Permit</label>
                      <label class="error-msg text-red" >@error('provincial_permit'){{$message}}@enderror</label>
                    </div>

                    <div class="custom-control custom-checkbox">
                      <input  type="checkbox" id="printed_copy_of_the_itb" name="printed_copy_of_the_itb" class="custom-control-input" value="{{old('printed_copy_of_the_itb')}}"  >
                      <label class="custom-control-label" for="printed_copy_of_the_itb">Printed Copy of the Invitation to Bid @ Philgeps</label>
                      <label class="error-msg text-red" >@error('printed_copy_of_the_itb'){{$message}}@enderror</label>
                    </div>

                    <div class="custom-control custom-checkbox">
                      <input  type="checkbox" id="construction_of_schedule_and_s_curve" name="construction_of_schedule_and_s_curve" class="custom-control-input" value="{{old('construction_of_schedule_and_s_curve')}}"  >
                      <label class="custom-control-label" for="construction_of_schedule_and_s_curve">Construction of Schedule and S-curve</label>
                      <label class="error-msg text-red" >@error('construction_of_schedule_and_s_curve'){{$message}}@enderror</label>
                    </div>

                    <div class="custom-control custom-checkbox">
                      <input  type="checkbox" id="manpower_schedule" name="manpower_schedule" class="custom-control-input" value="{{old('manpower_schedule')}}"  >
                      <label class="custom-control-label" for="manpower_schedule">Man Power Schedule</label>
                      <label class="error-msg text-red" >@error('manpower_schedule'){{$message}}@enderror</label>
                    </div>

                    <div class="custom-control custom-checkbox">
                      <input  type="checkbox" id="construction_methods" name="construction_methods" class="custom-control-input" value="{{old('construction_methods')}}"  >
                      <label class="custom-control-label" for="construction_methods">Construction Methods</label>
                      <label class="error-msg text-red" >@error('construction_methods'){{$message}}@enderror</label>
                    </div>

                    <div class="custom-control custom-checkbox">
                      <input  type="checkbox" id="equipment_utilization_schedule" name="equipment_utilization_schedule" class="custom-control-input" value="{{old('equipment_utilization_schedule')}}"  >
                      <label class="custom-control-label" for="equipment_utilization_schedule">Equipment Utilization Schedule</label>
                      <label class="error-msg text-red" >@error('equipment_utilization_schedule'){{$message}}@enderror</label>
                    </div>

                    <div class="custom-control custom-checkbox">
                      <input  type="checkbox" id="construction_safety_and_health_programs" name="construction_safety_and_health_programs" class="custom-control-input" value="{{old('construction_safety_and_health_programs')}}"  >
                      <label class="custom-control-label" for="construction_safety_and_health_programs">Construction Safety and Health Programs</label>
                      <label class="error-msg text-red" >@error('construction_safety_and_health_programs'){{$message}}@enderror</label>
                    </div>

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
    </div>

    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12 d-none" id="filter">
            <form class="row" id="filter_rfq" method="post" action="filter_bid_doc">
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
          <div class="col-sm-12">
            <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button>
            <button class="btn btn-sm btn-warning text-white float-right mb-2 btn btn-sm ml-2 d-none" id="filter_btn">Filter</button>
            <button class="btn btn-sm btn-default text-white float-right mb-2 btn btn-sm ml-2" id="show_filter">Show Filter</button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center text-nowrap"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Project_bid</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">Opening Date</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center" style="width:150px">Project_title</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Latest IT And BTR</th>
                  <th class="text-center">Provincial Permit</th>
                  <th class="text-center">Printed Copy of ITB @ Philgeps</th>
                  <th class="text-center">Construction of Schedule and S-curve</th>
                  <th class="text-center">Manpower Schedule</th>
                  <th class="text-center">Construction Methods</th>
                  <th class="text-center">Equipment Utilization Schedule</th>
                  <th class="text-center">Construction Safety and Health Programs</th>
                </thead>
                <tbody>

                  @if(session('newData'))
                  @foreach(session('newData') as $project_plan)

                  <tr>
                    <td></td>
                    <td>{{$project_plan->ntspqd_id}}</td>
                    <td>{{$project_plan->project_bid}}</td>
                    <td>{{$project_plan->current_cluster}}</td>
                    <td>{{$project_plan->bid_submission_start}}</td>
                    <td>{{$project_plan->business_name}}</td>
                    <td>{{$project_plan->project_no}}</td>
                    <td style="width:150px">{{$project_plan->project_title}}</td>
                    <td>{{$project_plan->ntspqd_status}}</td>
                    <td>{{$project_plan->latest_income_business_tax}}</td>
                    <td>{{$project_plan->provincial_permit}}</td>
                    <td>{{$project_plan->itb_copy}}</td>
                    <td>{{$project_plan->schedule_and_scurve}}</td>
                    <td>{{$project_plan->manpower_schedule}}</td>
                    <td>{{$project_plan->construction_methods}}</td>
                    <td>{{$project_plan->equipment_utilization_schedule}}</td>
                    <td>{{$project_plan->construction_safety_health_programs}}</td>
                  </tr>

                  @endforeach

                  @else


                  @foreach($project_plans as $project_plan)

                  <tr>
                    <td class="text-nowrap">
                      @if($project_plan->ntspqd_id==null)
                      <button class="btn btn-sm btn btn-sm btn-primary add-btn">Add</button>
                      @else
                      <button class="btn btn-sm btn btn-sm btn-success edit-btn">Edit</button>
                      @if($project_plan->ntspqd_status==="incomplete")
                        <a  class="btn btn-sm shadow-0 border-0 btn-primary text-white"  href="/generate_ntspqd/{{$project_plan->ntspqd_id}}"><i class="ni ni-cloud-download-95"></i></a>
                      @endif
                      @endif
                    </td>
                    <td>{{$project_plan->ntspqd_id}}</td>
                    <td>{{$project_plan->project_bid}}</td>
                    <td>{{$project_plan->plan_cluster_id}}</td>
                    <td>{{$project_plan->bid_submission_start}}</td>
                    <td>{{$project_plan->business_name}}</td>
                    <td>{{$project_plan->project_no}}</td>
                    <td class="text-nowrap" style="width:150px">{{$project_plan->project_title}}</td>
                    <td>{{$project_plan->ntspqd_status}}</td>
                    <td>{{$project_plan->latest_income_business_tax}}</td>
                    <td>{{$project_plan->provincial_permit}}</td>
                    <td>{{$project_plan->itb_copy}}</td>
                    <td>{{$project_plan->schedule_and_scurve}}</td>
                    <td>{{$project_plan->manpower_schedule}}</td>
                    <td>{{$project_plan->construction_methods}}</td>
                    <td>{{$project_plan->equipment_utilization_schedule}}</td>
                    <td>{{$project_plan->construction_safety_health_programs}}</td>

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

  $(".datepicker2").datepicker({
    format: 'mm/dd/yyyy',
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




  // show inputs/messages on load
  var oldInputs='{{ count(session()->getOldInput()) }}';
  if(oldInputs==1){
    // $('#filter').removeClass('d-none');
    // $('#filter_btn').removeClass('d-none');
    // $("#show_filter").html("Hide Filter");
  }
  else if (oldInputs>=5) {
    $("#ntspqd_modal").modal('show');
  }

  else{
    $("#project_year").val("{{$year}}");
  }


  if("{{session('message')}}"){
    if("{{session('message')}}"=="duplicate"){
      swal.fire({
        title: `Duplicate`,
        text: 'We already have the same entry in the database!',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-warning',
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
      $("#ntspqd_modal").modal('hide');
    }

    else if("{{session('message')}}"=="delete_error"){
      swal.fire({
        title: `Error`,
        text: 'You cannot delete RFQ for Received RFQ',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-danger',
        icon: 'warning'
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
    },{
      targets: [1,2],
      visible: false
    } ],
    order: [[ 1, "desc" ],[7 , "desc"]],
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
      dataSrc: 3
    }
  });



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

  $('#app_table tbody').on('click', '.add-btn', function (e) {
    table.rows().deselect();
    var data=table.row($(this).parents('tr')).data();
    $("#ntspqd_form")[0].reset();
    $("#ntspqd_id").val(data[1]);
    $("#project_bid").val(data[2]);
    $("#date_of_opening").val(moment(data[4],"YYYY-MM-DD").format("LL"));
    $("#plan_title").val(data[7]);
    $("#contractor").val(data[5]);
    $("#ntspqd_modal_title").html("Checklist of Post Qualification Docs");
    $("#ntspqd_modal").modal('show');
  });

  $('#app_table tbody').on('click', '.edit-btn', function (e) {
    table.rows().deselect();
    var data=table.row($(this).parents('tr')).data();
    $("#ntspqd_form")[0].reset();
    $("#ntspqd_id").val(data[1]);
    $("#project_bid").val(data[2]);
    $("#date_of_opening").val(moment(data[4],"YYYY-MM-DD").format("LL"));
    $("#plan_title").val(data[7]);
    $("#contractor").val(data[5]);
    $("#ntspqd_modal_title").html("Checklist of Post Qualification Docs");
    if(data[9]=="1"){
      $("#latest_income_and_business_tax").prop("checked",true);
    }
    if(data[10]=="1"){
      $("#provincial_permit").prop("checked",true);
    }
    if(data[11]=="1"){
      $("#printed_copy_of_the_itb").prop("checked",true);
    }
    if(data[12]=="1"){
      $("#construction_of_schedule_and_s_curve").prop("checked",true);
    }
    if(data[13]=="1"){
      $("#manpower_schedule").prop("checked",true);
    }
    if(data[14]=="1"){
      $("#construction_methods").prop("checked",true);
    }
    if(data[15]=="1"){
      $("#equipment_utilization_schedule").prop("checked",true);
    }
    if(data[16]=="1"){
      $("#construction_safety_and_health_programs").prop("checked",true);
    }



    $("#ntspqd_modal").modal('show');
  });

  $('#app_table tbody').on('click', '.delete_btn', function (e) {

    Swal.fire({
      text: 'Do you want to delete RFQ?',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: "Don't Delete",
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      cancelButtonClass: 'btn btn-sm btn-default',
      icon: 'warning'
    }).then((result) => {
      if(result.value==true){
        window.location.href = "/delete_bid_doc/"+table.row($(this).parents('tr')).data()[1];
      }
    });

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


  $("#filter_btn").click(function () {
    $("#filter_rfq").submit();
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
