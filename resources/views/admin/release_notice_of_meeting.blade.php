@extends('layouts.app')

@section('content')
  @include('layouts.headers.cards2')
  <div class="container-fluid mt-1">

    <div id="app">
      <div class="modal" tabindex="-1" role="dialog" id="release_nom_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="release_nom_modal_title">Release Notice of Meeting</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="release_nom_form" action="/submit_release_meeting">
                @csrf
                <div class="row">
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none ">
                    <label for="observer_id">Meeting ID
                    </label>
                    <input type="text" id="meeting_id" name="meeting_id" class="form-control form-control-sm" readonly value="{{$meeting->meeting_id}}" >
                    <label class="error-msg text-red" >@error('meeting_id'){{$message}}@enderror
                    </label>
                  </div>
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto  d-none">
                    <label for="observer_id">Observer ID
                    </label>
                    <input type="text" id="observer_id" name="observer_id" class="form-control form-control-sm" readonly value="{{old('observer_id')}}" >
                    <label class="error-msg text-red" >@error('observer_id'){{$message}}@enderror
                    </label>
                  </div>
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto  d-none">
                    <label for="meeting_observer_id">Meeting Observer_id ID
                    </label>
                    <input type="text" id="meeting_observer_id" name="meeting_observer_id" class="form-control form-control-sm" readonly value="{{old('meeting_observer_id')}}" >
                    <label class="error-msg text-red" >@error('meeting_observer_id'){{$message}}@enderror
                    </label>
                  </div>
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto">
                    <label for="observer_name">Observer
                    </label>
                    <input type="text" id="observer_name" name="observer_name" class="form-control form-control-sm" readonly value="{{old('observer_name')}}" >
                    <label class="error-msg text-red" >@error('observer_name'){{$message}}@enderror
                    </label>
                  </div>
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="date_received">Date Received by Observer
                    </label>
                    <input type="text" id="date_received" name="date_received" class="form-control form-control-sm datepicker" value="{{old('date_received')}}" >
                    <label class="error-msg text-red" >@error('date_received'){{$message}}@enderror
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

      <div class="card shadow">
        <div class="card shadow border-0">
          <div class="card-header">
            <h2 id="title"> Release Notice of  Meeting To Observers </h2>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered wrap" id="observers_table">
                <thead class="">
                  <tr class="bg-primary text-white" >
                    <th class="text-center"></th>
                    <th class="text-center">Meeting Observer ID</th>
                    <th class="text-center">Observer ID</th>
                    <th class="text-center">Office</th>
                    <th class="text-center">Observer Name</th>
                    <th class="text-center">Date Received </th>
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

  if("{{old('meeting_id')}}"!=""){
    $("#release_nom_modal").modal('show');
  }

  let data = {!! json_encode($meeting_observers) !!};

  // datatables
  $('#observers_table thead tr').clone(true).appendTo( '#observers_table thead' );
  $('#observers_table thead tr:eq(1)').removeClass('bg-primary');

  var table=  $('#observers_table').DataTable({
    language: {
      paginate: {
        next: '<i class="fas fa-angle-right">',
        previous: '<i class="fas fa-angle-left">'
      }
    },
    data:data,
    columns: [
      { "data":"meeting_observer_id",render: function ( data, type, row ) {
        console.log(data);
        if(data!=null){
          return '<button type="button" class="btn btn-sm btn btn-sm btn-danger delete-btn" value="'+data+'">Delete</button><button type="button" class="btn btn-sm btn btn-sm btn-success edit-btn">Edit</button>';
        }
        else{
          return '<button class="btn btn-sm btn btn-sm btn-primary edit-btn">Release</button>';
        }
      }},
      { "data": "meeting_observer_id"},
      { "data": "observer_id" },
      { "data": "observer_office"},
      { "data": "observer_name", render:function ( data, type, row ) {
        if(data==null){
          return row.observer_office+" Representative";
        }
        else{
          return data;
        }
      }},
      { "data": "date_received" }
    ],
    orderCellsTop: true,
    select: {
      style: 'multi',
      selector: 'td:not(:first-child)'
    },
    order: [[ 2, "asc" ]],
    columnDefs: [ {
      targets: 0,
      orderable: false
    },
    {
      "targets": [1],
      "visible": false
    }],

  });


  if("{{session('message')}}"){
    if("{{session('message')}}"=="success"){
      swal.fire({
        title: `Success`,
        text: 'Successfully Saved',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-success',
        icon: 'success'
      });
      $("#release_nom_modal").modal('hide');
    }
    else if("{{session('message')}}"=="duplicate"){
      swal.fire({
        title: `Duplicate`,
        text: 'Sorry we already have the same data on the database',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-danger',
        icon: 'info'
      });
      $("#proposed_bid").modal('hide');
    }

    else if("{{session('message')}}"=="edit_success"){
      swal.fire({
        title: `Success`,
        text: 'Successfully Saved',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-success',
        icon: 'success'
      });
      $("#proposed_bid").modal('hide');
    }

    else if("{{session('message')}}"=="delete_success"){
      swal.fire({
        title: `Success`,
        text: 'Successfully Deleted Data',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-success',
        icon: 'success'
      });
      $("#proposed_bid").modal('hide');
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

  // events

  $('#observers_table thead tr:eq(1) th').each( function (i) {
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


  $(".datepicker").datepicker({
    format: 'mm/dd/yyyy',
    endDate: moment().format('L')
  });

  $('#observers_table tbody').on('click', '.edit-btn', function (e) {
    $('.error-msg').html("");
    var data = table.row( $(this).parents('tr') ).data();
    $("#observer_id").val(data.observer_id);

    if(data.observer_name==null){
      $("#observer_name").val(data.observer_office+" Representative");
    }
    else{
      $("#observer_name").val(data.observer_name);
    }
    $("#meeting_observer_id").val(data.meeting_observer_id);
    $("#date_received").val(moment(data.date_received).format('L'));
    $("#date_received").datepicker("update",moment(data.date_received).format('L'));
    $("#release_nom_modal").modal('show');
  });

  // show delete
  $('#observers_table tbody').on('click', '.delete-btn', function (e) {

    Swal.fire({
      title:'Delete Data',
      text: 'Are you sure to delete data?',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: "Don't Delete",
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      cancelButtonClass: 'btn btn-sm btn-default',
      icon: 'warning'
    }).then((result) => {
      if(result.value==true){
        console.log($(this).val());
        window.location.href = "/delete_release_nom/"+$(this).val();
      }
    });
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
