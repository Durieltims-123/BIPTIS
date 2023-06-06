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
            <div class="col-sm-12">
              <!-- <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Filter</button> -->
              <!-- <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2" id="excel_btn">Download Excel</button> -->
              <!-- <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button> -->
              <!-- <button class="btn btn-sm btn-warning text-white float-right mb-2 btn btn-sm ml-2 d-none" id="filter_btn">Filter</button> -->
              <!-- <button class="btn btn-sm btn-default text-white float-right mb-2 btn btn-sm ml-2" id="show_filter">Show Filter</button> -->
            </div>
            <div class="table-responsive">
              <table class="table table-bordered wrap" id="app_table">
                <thead class="">
                  <tr class="bg-primary text-white" >
                    <th class="text-center"></th>
                    <th class="text-center">ID</th>
                    <th class="text-center">APP/SAPP No.</th>
                    <th class="text-center">Project No.</th>
                    <th class="text-center">Project Title</th>
                    <th class="text-center">Location</th>
                    <th class="text-center">Mode of Procurement</th>
                    <th class="text-center">Posting</th>
                    <th class="text-center">Sub/Open of Bids</th>
                    <th class="text-center">Notice of Award</th>
                    <th class="text-center">Contract Signing</th>
                    <th class="text-center">Source of Fund</th>
                    <th class="text-center">Approved Budget Cost</th>
                    <th class="text-center">Actual Project Cost</th>
                    <th class="text-center">Project Year</th>
                    <th class="text-center">Year Funded</th>
                    <th class="text-center">Rebid Count</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Remarks</th>
                    <th class="text-center">Post Qual</th>
                    <th class="text-center">Classification</th>
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

  // change filter account classification
  let account_classification="{{old('account_classification')}}";
  $("#account_classification").val(account_classification);

  // table data
  let data= @json(session('filtered_data'));

  if(data==null){
    data= @json($project_plans);
  }

  // sessions/messages
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
        text: 'Successfully deleted from database',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-success',
        icon: 'success'
      });
    }
    else{
      swal.fire({
        title: `Error`,
        text: 'An error occured please contact your system developer',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-success',
        icon: 'warning'
      });
    }
  }

  // datatables
  $('#app_table thead tr').clone(true).appendTo( '#app_table thead' );
  $('#app_table thead tr:eq(1)').removeClass('bg-primary');

  $(".datepicker").datepicker({
    format: 'mm/dd/yyyy',
    endDate:'{{$year}}'
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
    data:data,
    dom: 'Bfrtip',
    buttons: [
      {
        text: 'Excel',
        extend: 'excel',
        className: 'btn btn-sm shadow-0 border-0 bg-success text-white'
      },
      {
        text: 'Print',
        extend: 'print',
        className: 'btn btn-sm shadow-0 border-0 bg-blue text-white'
      }
    ],
    columns: [
      { "data":"advertisement",
      render: function ( data, type, row ) {
        return "<div style='white-space: nowrap'><a  class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank'  href='/view_project/"+row.plan_id+"'><i class='ni ni-tv-2'></i></a></div>";
      }},
      {"data":"plan_id"},
      {"data":"app_group_no"},
      {"data":"project_no"},
      {"data":"project_title"},
      {"data":"municipality_name"},
      {"data":"mode"},
      {"data":"abc_post_date",  render: function ( data, type, row ) {
        return moment(data).format('MMM-YYYY');
      }},
      {"data":"sub_open_date",  render: function ( data, type, row ) {
        return moment(data).format('MMM-YYYY');
      }},
      {"data":"award_notice_date",  render: function ( data, type, row ) {
        return moment(data).format('MMM-YYYY');
      }},
      {"data":"contract_signing_date",  render: function ( data, type, row ) {
        return moment(data).format('MMM-YYYY');
      }},
      {"data":"source"},
      {"data":"abc",render: function ( data, type, row ) {
        if(data!=null){
          return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        return "";
      }},
      {"data":"project_cost",render: function ( data, type, row ) {
        if(data!=null){
          return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        return "";
      }},
      {"data":"project_year"},
      {"data":"year_funded"},
      {"data":"re_bid_count"},
      {"data":"project_status"},
      {"data":"remarks"},
      {"data":"post_qualification_end"},
      {"data":"classification"}

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
      targets: 0,
      width: "100px"
    },
    { width: 200,
      visible:false,
      targets: 1,
    }],

  });

  // inputs
  var oldInputs='{{ count(session()->getOldInput()) }}';
  if(oldInputs>0){
    $('#filter').removeClass('d-none');
    $('#filter_btn').removeClass('d-none');
    $("#show_filter").html("Hide Filter");
  }
  else{
    $("#project_year").val("{{$year}}");
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
