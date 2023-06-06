@extends('layouts.app')

@section('content')
  @include('layouts.headers.cards2')
  <div class="container-fluid mt-1">
    <div id="app">
      <div class="card shadow">
        <div class="card shadow border-0">
          <div class="card-header">
            <h2 id="title">{{$title}}</h2>
          </div>
          <div class="card-body">
            <div class="col-sm-12" id="filter">
              <form class="row" id="filter_bidders_per_project" method="post" action="{{route('filter_bidders_per_project')}}">
                @csrf
                <!-- project year -->
                <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                  <label for="project_year" class="input-sm">Project Year
                  </label>
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
                    <th class="text-center">Bid Opened</th>
                    <th class="text-center">Cluster</th>
                    <th class="text-center">Project No.</th>
                    <th class="text-center">Project Title</th>
                    <th class="text-center">Location</th>
                    <th class="text-center">Mode of Procurement</th>
                    <th class="text-center">Source of Fund</th>
                    <th class="text-center">APP/SAPP No.</th>
                    <th class="text-center">ABC</th>
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

  let data=@json($project_plans);


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
  if(oldInputs>0){
    data=@json(session('project_plans'));
    console.log(data);
  }
  else{
    $("#project_year").val("{{$year}}");
  }

  var table=  $('#app_table').DataTable({
    language: {
      paginate: {
        next: '<i class="fas fa-angle-right">',
        previous: '<i class="fas fa-angle-left">'
      }
    },
    dom: 'Bfrtip',
    buttons: [
      {
        text: 'Hide Filter',
        attr: {
          id: 'show_filter'
        },
        className: 'btn btn-sm shadow-0 border-0 bg-dark text-white',
        action: function (e, dt, node, config) {

          if (config.text == "Show Filter") {
            $('#filter').removeClass('d-none');
            $('#filter_btn').removeClass('d-none');
            config.text = "Hide Filter";
            $("#show_filter").html("Hide Filter");
          }
          else {
            $('#filter').addClass('d-none');
            $('#filter_btn').addClass('d-none');
            config.text = "Show Filter";
            $("#show_filter").html("Show Filter");
          }
        }
      },
      {
        text: 'Filter',
        attr: {
          id: 'filter_btn'
        },
        className: 'btn btn-sm shadow-0 border-0 bg-warning text-white',
        action: function (e, dt, node, config) {
          $("#filter_bidders_per_project").submit();
        }
      },
      {
        text: 'Excel',
        extend: 'excel',
        className: 'btn btn-sm shadow-0 border-0 bg-success text-white'
      },
      {
        text: 'Print',
        extend: 'print',
        className: 'btn btn-sm shadow-0 border-0 bg-info text-white'
      }

    ],
    data:data,
    columns: [
      { data:"plan_id",render:function( data, type, row ) {
        return '<td><a class="btn btn-sm btn btn-sm btn-success" target="_blank" href="/notice_bidders/'+data+'" data-toggle="tooltip" data-placement="top" ><i class="ni ni-circle-08"></i></a></td>';
      }},
      { data:"plan_id"},
      { data:"bid_submission_start"},
      { data:"plan_cluster_id"},
      { data:"project_no"},
      { data:"project_title"},
      { data:"municipality_name"},
      { data:"mode"},
      { data:"source"},
      { data:"app_group_no"},
      {"data":"project_cost",render: function ( data, type, row ) {
        if(data!=null){
          return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        return "";
      }},
      { data:"project_year"},
    ],
    serverSide: false,
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
    columnDefs: [  {
      targets: [ 3 ],
      orderData: [ 0, 1 ]
    },
    {
      targets: 0,
      orderable: false
    },{
      targets:1,
      visible:false
    }
  ],
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

    });
  }
});

$("#project_year").change( function(){
  $("#filter_bidders_per_project").submit();
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
