@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="app">
    @include('layouts.components.extend_modal')
    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12 " id="filter">
            <form class="row" id="app_filter" method="POST" action="{{route('filter_post_qual')}}">
              @csrf
              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="project_year" class="input-sm">Project Year </label>
                <input  class="form-control form-control-sm yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}" >
                <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror
                </label>
              </div>

            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="app_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Date Opened</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Ongoing Post Qual</th>
                  <th class="text-center">Ongoing Post Qual Amount</th>
                  <th class="text-center">Post Qualification End</th>
                  <th class="text-center">Post Qual Days Consumed</th>
                  <th class="text-center">Maximum Days</th>
                  <th class="text-center">Municipality</th>
                  <th class="text-center">Mode of Procurement</th>
                  <th class="text-center">Project Cost</th>
                  <th class="text-center">Project Year</th>
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
$('#app_table thead tr').clone(true).appendTo( '#app_table thead' );
$('#app_table thead tr:eq(1)').removeClass('bg-primary');

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
  endDate:'{{$year}}'
});


$(".datepicker2").datepicker({
  format: 'mm/dd/yyyy',
});

$('.timepicker').timepicker({
  showRightIcon: false,
  showOnFocus: true,
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

// inputs
var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>3){

  if('{{old("request_extension_plan_id")}}'!=''){
    $("#request_extension_modal").modal('show');
  }
}


var data = @json($project_plans);

if(@json(old('project_year'))!=null){
  data = @json(session('project_plans'));
}
else{
  $("#project_year").val(@json($year));
}


var table=  $('#app_table').DataTable({
  dom: 'Bfrtip',
  buttons: [
    {
      text: 'Hide Filter',
      attr: {
        id: 'show_filter'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-dark text-white',
      action: function ( e, dt, node, config ) {

        if(config.text=="Show Filter"){
          $('#filter').removeClass('d-none');
          $('#filter_btn').removeClass('d-none');
          config.text="Hide Filter";
          $("#show_filter").html("Hide Filter");
        }
        else{
          $('#filter').addClass('d-none');
          $('#filter_btn').addClass('d-none');
          config.text="Show Filter";
          $("#show_filter").html("Show Filter");
        }
      }
    },
    {
      text: 'Filter',
      attr: {
        id: 'filter_btn'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-warning text-white filter_btn',
      action: function ( e, dt, node, config ) {
        $("#app_filter").submit();
      }
    },
    {
      text: 'Excel',
      className: 'btn btn-sm shadow-0 border-0 bg-success text-white',
      action: function ( e, dt, node, config ) {
        window.open("/download_ongoing_post_qual/"+$("#project_year").val());
        let timerInterval
        Swal.fire({
          title: 'Generating Excel!',
          html: 'Please Wait',
          timer: 300,
          timerProgressBar: true,
          didOpen: () => {
            Swal.showLoading()
          },
          willClose: () => {
            clearInterval(timerInterval)
          }
        }).then((result) => {

        });
      }
    },
    {
      text: 'Print',
      extend: 'print',
      className: 'btn btn-sm shadow-0 border-0 bg-info text-white'
    }
  ],
  data:data,
  columns: [
    { "data":"plan_id",
    render: function ( data, type, row ) {
      let link='<div style="white-space: nowrap"><a class="btn btn-sm btn btn-sm btn-success text-white" target="_blank" href="/post_qual_project_bidders/'+row.plan_id+'"><i class="ni ni-circle-08"></i></a>';

      if(row.post_qual_days>=10 && row.post_qual_days<=12){
        link=link+' <span class="btn btn-sm btn btn-sm btn-warning request_extension_btn" ><i class="ni ni-curved-next">Request Extension</i></span></div>';
      }
      else if(row.post_qual_days>=29 && row.post_qual_days<=30){
        link=link+'<span class="btn btn-sm btn btn-sm btn-warning request_extension_btn" ><i class="ni ni-curved-next">Request Extension</i></span></div>';
      }
      else if(row.post_qual_days > 40){
        link=link+'<span class="btn btn-sm btn btn-sm btn-danger">Due: '+moment(row.post_qualification_end,'Y-MM-DD').format('L')+'</span></div>';
      }
      else{
        link=link+'</div>';
      }

      return link;

    }},
    {"data":"plan_id"},
    {"data":"open_bid"},
    {"data":"plan_cluster_id"},
    {"data":"project_no"},
    {"data":"project_title"},
    {"data":"ongoing_post_qual"},
    {"data":"ongoing_post_qual_amount",render: function ( data, type, row ) {
      if(data!=null){
        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }
      return "";
    }},
    {"data":"post_qualification_end"},
    {"data":"post_qual_days"},
    {"data":"maximum_days"},
    {"data":"municipality_name"},
    {"data":"mode"},
    {"data":"project_cost",render: function ( data, type, row ) {
      if(data!=null){
        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }
      return "";
    }},
    {"data":"project_year"}
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
    selector: 'td:not(:first-child)'
  },
  responsive:true,
  columnDefs: [ {
    width: 100,
    targets: 0,
    orderable: false
  },
  {
    targets:1,
    visible:false
  } ],
  order:[ [ 2, 'asc' ] ]
});

if("{{session('message')}}"){
  if("{{session('message')}}"=="success"){
    swal.fire({
      title: 'Success',
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#plan_id").val('');
    $("#post_qualification_date").val('');
    $("#form_modal").modal('hide');
  }
  else if("{{session('message')}}"=="equal_error"){
    swal.fire({
      title: 'Error',
      text: 'Date is same as old schedule',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else{

  }
}

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

$("#project_year").change(function () {
  $("#app_filter").submit();
})

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





$("#extend_btn").click(function functionName() {
  if(table.rows( { selected: true } ).count()<1){
    Swal.fire({
      title:"Warning",
      text: 'Please select rows Extend ',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-info',
      icon: 'info'
    });
  }
  else{
    let rows = table.rows( { selected: true } );
    var plan_ids =  table.cells( rows.nodes(), 1 ).data().toArray();
    var plan_numbers =  table.cells( rows.nodes(), 9 ).data().toArray();
    $("#extend_plan_id").val(plan_ids.toString());
    $("#extend_project_number").val(plan_numbers.toString());
    $("#process").val("post_qualification");
    $("#extend_days").val("");
    $("#extend_remarks").val("");
    $("#extend_modal").modal('show');
  }
});




</script>
@endpush
