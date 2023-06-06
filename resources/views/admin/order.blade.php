@extends('layouts.app')

@section('content')
  @include('layouts.headers.cards2')
  <div class="container-fluid mt-1">

    <div id="app">

      <div class="col-sm-12">
        <div class="modal" tabindex="-1" role="dialog" id="form_modal" data-backdrop="static" data-keyboard="false">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="modal-title" id="form_modal_title">Order Details</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body pt-0">
                <form class="col-sm-12" method="POST" id="submit_award_notice" action="{{route('submit_order')}}">
                  @csrf
                  <div class="row">
                    <div class="form-group col-xs-6 col-sm-6 col-md-6 mb-2  mb-0">
                      <h5 for="request_date_generated">Request Date Generated</h5>
                      <input type="text" id="request_date_generated" name="request_date_generated" class="form-control form-control-sm bg-white" readonly value="{{old('request_date_generated')}}" >
                      <label class="error-msg text-red" >@error('request_date_generated'){{$message}}
                      @enderror</label>
                    </div>

                    <div class="form-group col-xs-6 col-sm-6 col-md-6 mb-2  mb-0">
                      <h5 for="requested_date">Requested Date</h5>
                      <input type="text" id="requested_date" name="requested_date" class="form-control form-control-sm bg-white" readonly value="{{old('requested_date')}}" >
                      <label class="error-msg text-red" >@error('requested_date'){{$message}}
                      @enderror</label>
                    </div>

                    <div class="form-group col-xs-6 col-sm-6 col-md-6 mb-2  mb-0 d-none">
                      <h5 for="request_id">Request ID</h5>
                      <input type="text" id="request_id" name="request_id" class="form-control form-control-sm bg-white" readonly value="{{old('request_id')}}" >
                      <label class="error-msg text-red" >@error('request_id'){{$message}}
                      @enderror</label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-xs-6 col-sm-6 col-md-6 mb-2  mb-0">
                      <h5 for="order_date_generated">Order Date Generated <span class="text-red">*</span></h5>
                      <input type="text" id="order_date_generated" name="order_date_generated" class="form-control form-control-sm datepicker" value="{{old('order_date_generated')}}" >
                      <label class="error-msg text-red" >@error('order_date_generated'){{$message}}
                      @enderror</label>
                    </div>

                    <div class="form-group col-xs-6 col-sm-6 col-md-6 mb-2  mb-0">
                      <h5 for="order_number">Order Number <span class="text-red">*</span></h5>
                      <input type="text" id="order_number" name="order_number" class="form-control form-control-sm" value="{{old('order_number')}}" >
                      <label class="error-msg text-red" >@error('order_number'){{$message}}
                      @enderror</label>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 col-md-12 mb-2  mb-0">
                      <h5 for="order_remarks">Order Remarks <span class="text-red">*</span></h5>
                      <textarea type="text" id="order_remarks" name="order_remarks" class="form-control form-control-sm" value="{{old('order_remarks')}}" >
                      </textarea>
                      <label class="error-msg text-red" >@error('order_remarks'){{$message}}
                      @enderror</label>
                    </div>


                  </div>
                  <div class="d-flex justify-content-center col-sm-12">
                    <button  class="btn btn-primary text-center">Submit</button>
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
            <div class="col-sm-12" id="filter">
              <form class="row" id="filter_request_for_extention" method="post" action="{{route('filter_orders')}}">
                @csrf
                <!-- project year -->
                <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                  <label for="year" class="input-sm">Year </label>
                  <input  class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}" >
                  <label class="error-msg text-red" >@error('year'){{$message}}
                  @enderror
                </label>
              </div>
            </form>
          </div>

          <div class="col-sm-12">
            <div class="table-responsive">
              <table class="table table-bordered" id="data_table">
                <thead class="">
                  <tr class="bg-primary text-white" >
                    <th class="text-center"></th>
                    <th class="text-center">ID</th>
                    <th class="text-center">Order Generated</th>
                    <th class="text-center">Request Generated</th>
                    <th class="text-center">Order Number</th>
                    <th class="text-center">Requested Date</th>
                    <th class="text-center">Remarks</th>
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

  $(".datepicker").datepicker({
    format: 'mm/dd/yyyy',
    endDate:'{{$year}}'
  });

  $(".yearpicker").datepicker({
    format: 'yyyy',
    viewMode: "years",
    minViewMode: "years"
  });


  if(@json(old('year'))==null){
    $("#year").val(@json($year));
  }

  if("{{session('message')}}"){
    if("{{session('message')}}"=="success"){
      swal.fire({
        title: 'Success',
        text: 'Successfully saved to database',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-success',
        icon: 'success'
      });
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
    else if("{{session('message')}}"=="duplicate"){
      swal.fire({
        title: 'Error',
        text: 'The Order Number is already used',
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

  // datatables
  $('#data_table thead tr').clone(true).appendTo( '#data_table thead' );
  $('#data_table thead tr:eq(1)').removeClass('bg-primary');
  var data = @json($requests);

  if(@json(old('year'))!=null){
    data = @json(session('requests'));
  }
  else{
    $("#project_year").val(@json($year));
  }

  $request_id=@json(old('request_id'));
  if($request_id!=""&&$request_id!=null){
    $("#order_remarks").val(@json(old('order_remarks')));
    $("#form_modal").modal('show');
  }

  var table=  $('#data_table').DataTable({  data:data,
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
          $("#filter_request_for_extention").submit();
        }
      },
      {
        text: 'Excel',
        extend: 'excel',
        className: 'btn btn-sm shadow-0 border-0 bg-success text-white',
      },
      {
        text: 'Print',
        extend: 'print',
        className: 'btn btn-sm shadow-0 border-0 bg-info text-white'
      }
    ],
    dataType: 'json',
    columns: [
      {"data": "order_id",render: function ( data, type, row ) {
        let view="/view_request_for_extension/"+row.request_id;
        let download="/generate_order/"+row.order_number;
        let btns="";
        if(data!=null){
          btns="@if(in_array('update',$user_privilege))<button type='button' class='btn btn-sm btn btn-sm btn-success update-btn' data-toggle='tooltip' data-placement='top' title='Edit' ><i class='ni ni-ruler-pencil'></i></button>@endif <a type='button' target='_blank' href='"+download+"' class='btn btn-sm btn btn-sm btn-warning download-btn' data-toggle='tooltip' data-placement='top' title='Download' ><i class='ni ni-cloud-download-95'></i></a>"
        }
        else{
          btns="@if(in_array('add',$user_privilege))<button type='button' class='btn btn-sm btn btn-sm btn-danger update-btn' data-toggle='tooltip' data-placement='top' title='Add' ><i class='ni ni-fat-add'></i></button>@endif"
        }
        return "<div style='white-space: nowrap'>"+btns+"<a type='button' target='_blank' href='"+view+"' class='btn btn-sm btn btn-sm btn-info view-btn' data-toggle='tooltip' data-placement='top' title='View' ><i class='ni ni-tv-2'></i> </a> </div>";
      }},
      { "data": "request_id" },
      { "data": "order_date_generated"},
      { "data": "request_date_generated"},
      { "data": "order_number"},
      { "data": "request_date" },
      { "data": "order_remarks" }
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
      orderable: false
    } ],
  });

  // events

  $('#data_table thead tr:eq(1) th').each( function (i) {
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

  $("#year").change(function () {
    $("#filter_request_for_extention").submit();
  });



  // show
  @if(in_array('add',$user_privilege)||in_array('update',$user_privilege))
  $('#data_table tbody').on('click', '.update-btn', function (e) {
    let this_button=$(this);
    var row = table.row($(this_button).parents('tr')).data();
    $("#order_remarks").html('');
    $("#request_date_generated").val(moment(row.request_date_generated).format('MM/DD/YYYY'));
    $("#requested_date").val(moment(row.request_date).format('MM/DD/YYYY'));
    $("#request_id").val(row.request_id);
    $("#order_date_generated").val(row.order_date_generated);
    $("#order_number").val(row.order_number);
    $("#order_remarks").html(row.order_remarks);
    $("#form_modal").modal('show');

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
