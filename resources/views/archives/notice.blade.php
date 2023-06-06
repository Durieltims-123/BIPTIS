@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="app">
    <div class="col-sm-12">
      <div class="modal" tabindex="-1" role="dialog" id="archive">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="archive_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="archive_form"
              action="{{route('archive.submit_notice')}}" enctype="multipart/form-data">
              @csrf
              <div class="row d-flex">

                <!--ID -->
                <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                  <label for="id">ID <span class="text-red">*</span></label>
                  <input type="text" id="id" name="id" class="form-control form-control-sm" readonly
                  value="{{old('id')}}">
                  <label class="error-msg text-red">@error('id'){{$message}}@enderror
                  </label>
                </div>

                {{-- edit-id --}}
                <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                  <label for="edit-id">ID <span class="text-red">*</span></label>
                  <input type="text" id="edit-id" name="edit-id" class="form-control form-control-sm" readonly
                  value="{{old('edit-id')}}">
                  <label class="error-msg text-red">@error('edit-id'){{$message}}@enderror
                  </label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="date_generated">Date Generated<span class="text-red">*</span>
                  </label>
                  <input type="text" id="date_generated" name="date_generated" class="form-control form-control-sm bg-white datepicker" value="{{old('date_generated')}}">
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
                  <label for="date_received_by_contractor">Date Received by Contractor<span class="text-red">*</span>
                  </label>
                  <input type="text" id="date_received_by_contractor" name="date_received_by_contractor" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received_by_contractor')}}" >
                  <label id="date_received_error" class="error-msg text-red" >@error('date_received_by_contractor'){{$message}}@enderror
                  </label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="date_received_by_bac">Date Received by BAC Infra/Support<span class="text-red">*</span>
                  </label>
                  <input type="text" id="date_received_by_bac" name="date_received_by_bac" class="form-control form-control-sm bg-white datepicker"  value="{{old('date_received_by_bac')}}" >
                  <label class="error-msg text-red" >@error('date_received_by_bac'){{$message}}@enderror
                  </label>
                </div>
                @if($type === 'NOD' || $type === 'NOPD')
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 ">
                  <label for="mr_due_date">MR Due Date<span class="text-red"></span>
                  </label>
                  <input type="text" id="mr_due_date" name="mr_due_date" class="form-control form-control-sm bg-white " readonly  value="{{old('mr_due_date')}}" >
                  <label class="error-msg text-red" >@error('mr_due_date'){{$message}}@enderror
                  </label>
                </div>
                @endif

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="remarks">Remark
                  </label>
                  <textarea type="text" id="remarks" name="remarks" class="form-control form-control-sm" value="{{old('remarks')}}" ></textarea>
                  <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror
                  </label>
                </div>



                <!-- Attachment -->
                <a class="form-group col-xs-12 col-sm-12 col-lg-12">
                  <label for="attachment">Attachment/s <span class="text-red">*</span></label>
                  <div id="existing_attachments">

                  </div>
                  <div id="attachment_div">
                    <div class="row attachment_group">
                      <div class="col-md-11">
                        <input type="file" name="attachments[]" accept="application/pdf"
                        class="form-control attachment">
                      </div>
                    </div>
                  </div>
                  <button type="button" id="add_more_attachment" class="btn btn-sm btn-primary mt-2">Add More
                    Attachments</button>
                    <label class="error-msg text-red">@error('attachment'){{$message}}@enderror
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
    </div>


    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
          <div class="col-sm-12" id="filter">
            <form class="row" id="filter_notices" method="post" action="{{route('archive.filter_notices')}}">
              @csrf
              <!-- year -->
              <div class="form-group col-xs-6 col-sm-4 col-lg-2 mb-0">
                <label for="year" class="input-sm">Year </label>
                <input class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy"
                minimum-view="year" value="{{old('year')}}">
                <label id="year_error" class="error-msg text-red">@error('year'){{$message}}@enderror
                </label>
              </div>

              <!-- notice_type -->
              <div class="form-group col-xs-6 col-sm-4 col-lg-2 mb-0 d-none">
                <label for="notice_type" class="input-sm">Notice Type</label>
                <input class="form-control form-control-sm" id="notice_type" name="notice_type" value="{{$type}}">
                <label id="notice_type_error" class="error-msg text-red">@error('notice_type'){{$message}}@enderror
                </label>
              </div>

            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="archive_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Cluster</th>
                  <th class="text-center">Opening Date</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Business Name</th>
                  <th class="text-center">Owner</th>
                  <th class="text-center">Date Generated</th>
                  <th class="text-center">Date Released To Contractor</th>
                  <th class="text-center">Date Received by Contractor</th>
                  <th class="text-center">Date Received by BAC Infra/Support</th>
                  <th class="text-center">MR Due Date</th>
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

$(".yearpicker").datepicker({
  format: 'yyyy',
  viewMode: "years",
  minViewMode: "years"
});

$("#date_received").datepicker({
  format: 'mm/dd/yyyy',
  endDate:'{{$year}}',
  autoclose: true,
  language: 'da',
  enableOnReadonly: false
});

let date_received_error=$("#date_received_error").html();
date_received_error=date_received_error.replaceAll(" ","");
let year_error = $("#year_error").html();
let edit_id=@json(old('edit-id'));
let old_year=@json(old('year'));
if(old_year==null){
  $("#year").datepicker('update',@json($year));
}


// datatables
$('#archive_table thead tr').clone(true).appendTo( '#archive_table thead' );
$('#archive_table thead tr:eq(1)').removeClass('bg-primary');
var data = @json(session('data'));
if (data == null) {
  data = @json($data);
}

var table=  $('#archive_table').DataTable({
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
      className: 'btn btn-sm shadow-0 border-0 bg-warning text-white d-none'
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
  data: data,
  columns: [
    {"data": "with_attachment",render: function ( data, type, row ) {
      if(data==1){
        return '<div style="white-space: nowrap"> @if(in_array("update",$user_privilege))<button class="btn btn-sm btn-success edit-btn  "  id="edit-btn-'+row.project_bidder_notice_id+'" project_bidder_notice_id="'+row.project_bidder_notice_id+'" data-toggle="tooltip" data-placement="top" title="Edit"><i class="ni ni-ruler-pencil"></i></button>@endif <a  class="btn btn-sm shadow-0 border-0 btn-primary text-white" target="_blank"   href="view_notice_attachments/' + row.project_bidder_notice_id+'"><i class="ni ni-tv-2" data-toggle="tooltip" data-placement="top" title="View Attachment/s"></i></a><div>';
      }
      else{
        return '@if(in_array("add",$user_privilege))<button class="btn btn-sm btn-primary add-btn" type="button" project_bidder_notice_id="'+row.project_bidder_notice_id+'" data-toggle="tooltip" data-placement="top" title="Add"><i class="ni ni-fat-add"></i></button>@endif';
      }
    }},
    { "data": "project_bidder_notice_id" },
    { data:"plan_cluster_id"},
    { data:"open_bid"},
    { data:"project_no"},
    { data:"project_title"},
    { data:"business_name"},
    { data:"owner"},
    { data:"notice_date_generated"},
    { data:"notice_date_released"},
    { data:"date_received_by_contractor"},
    { data:"notice_date_received"},
    { data:"mr_due_date"},
    { data:"notice_remarks"}
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
  order: [[ 1, "desc" ]],
  columnDefs: [ {
    targets: 0,
    orderable: false
  } ],
});

if (year_error != null && year_error != '') {
  $('#filter').removeClass('d-none');
  $('#filter_btn').removeClass('d-none');
  $('#show_filter').text('Hide Filter');
}
if(date_received_error!=null && date_received_error!=''){
  $("#archive").modal('show');
  $('#filter').removeClass('d-none');
  $('#filter_btn').removeClass('d-none');
  $('#show_filter').text('Hide Filter');
}


var oldInputs='{{ count(session()->getOldInput()) }}';

if(oldInputs>=5){
  $("#archive").modal("show");
}

// messages
if ("{{session('message')}}") {
  if ("{{session('message')}}" == "missing_attachment") {
    swal.fire({
      title: `Missing Attachment`,
      text: 'Please attach your document in pdf format',
      buttonsStyling: false,
      customClass: {
        confirmButton: 'btn btn-sm btn-warning',
      },
      icon: 'warning'
    });
  }

  else if ("{{session('message')}}" == "success") {
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      buttonsStyling: false,
      icon: 'success',
      buttonsStyling: false,
      customClass: {
        confirmButton: 'btn btn-sm btn-success',
      },
    });
    $("#archive").modal('hide');
  }


  else {
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact your system developer',
      buttonsStyling: false,
      icon: 'warning',
      customClass: {
        confirmButton: 'btn btn-sm btn-warning',
      },
    });
  }
}


// events

$('#archive_table thead tr:eq(1) th').each( function (i) {
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
@if(in_array('add',$user_privilege))
$('#archive_table tbody').on('click', '.add-btn', function (e) {
  $(".error-msg").each(function () {
    $(this).html("");
  });
  let id=$(this).attr('project_bidder_notice_id');
  var row = table.row($(this).parents('tr')).data();
  $("#archive_form")[0].reset();
  $("#archive_title").html("Add Notice Attachment/s");
  $('#id').val(id);
  $("#existing_attachments").html('');
  $("#remarks").html(row.notice_remarks);
  $("#mr_due_date").val("");

  if(row.date_generated!=null){
    $("#date_generated").val(moment(row.date_generated,'Y-MM-DD').format("MM/DD/YYYY"));
  }
  else{
    $("#date_generated").val('');
  }
  if(row.date_released!=null){
    $("#date_released").datepicker('setDate',moment(row.date_released,'Y-MM-DD').format("MM/DD/YYYY"));
  }
  else{
    $("#date_released").val('');
  }
  if(row.date_received_by_contractor!=null){
    $("#date_received_by_contractor").datepicker('setDate',moment(row.date_received_by_contractor,'Y-MM-DD').format("MM/DD/YYYY"));
  }
  else{
    $("#date_received_by_contractor").val('');
  }
  if(row.date_received!=null){
    $("#date_received_by_bac").datepicker('setDate',moment(row.date_received,'Y-MM-DD').format("MM/DD/YYYY"));
  }
  else{
    $("#date_received_by_bac").val('');
  }



  $("#attachment_div").find('.attachment_group').each(function (index) {
    if(index!=0){
      $(this).remove();
    }
  });

  $("#archive").modal('show');
});
@endif

@if(in_array('update',$user_privilege))
$('#archive_table tbody').on('click', '.edit-btn', function (e) {

  let id=$(this).attr('project_bidder_notice_id');
  var row = table.row($(this).parents('tr')).data();
  $("#archive_form")[0].reset();
  $("#archive_title").html("Update Notice Attachment/s");
  $('#id').val(id);
  $('#edit-id').val(id);
  $("#mr_due_date").val("");
  $("#remarks").html(row.notice_remarks);
  if(row.date_generated!=null){
    $("#date_generated").val(moment(row.date_generated,'Y-MM-DD').format("MM/DD/YYYY"));
  }
  else{
    $("#date_generated").val('');
  }
  if(row.date_released!=null){
    $("#date_released").datepicker('setDate',moment(row.date_released,'Y-MM-DD').format("MM/DD/YYYY"));
  }
  else{
    $("#date_released").val('');
  }
  if(row.date_received_by_contractor!=null){
    $("#date_received_by_contractor").datepicker('setDate',moment(row.date_received_by_contractor,'Y-MM-DD').format("MM/DD/YYYY"));
  }
  else{
    $("#date_received_by_contractor").val('');
  }
  if(row.date_received!=null){
    $("#date_received_by_bac").datepicker('setDate',moment(row.date_received,'Y-MM-DD').format("MM/DD/YYYY"));
  }
  else{
    $("#date_received_by_bac").val('');
  }


  $("#existing_attachments").html('');
  $("#attachment_div").find('.attachment_group').each(function (index) {
    if(index!=0){
      $(this).remove();
    }
  });

  $.ajax({
    'url': "{{route('archive.get_archive_notice_attachments')}}",
    'data': {
      "_token": "{{ csrf_token() }}",
      "project_bidder_notice_id" : row.project_bidder_notice_id,
    },
    'method': "post",
    'success': function(data) {
      if(data.length>0){
        data.forEach((item, i) => {
          let existing_attachment='<div class="row existing_attachment_group">';
          existing_attachment=existing_attachment+'<div class="col-md-11">';
          existing_attachment=existing_attachment+'<div class="form-control attachment">';
          existing_attachment=existing_attachment+'<a href="view_notice_attachment/'+item.id+'" target="_blank"> Attachment '+(i+1)+'</a>';
          existing_attachment=existing_attachment+'</div> </div> <div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_existing_attachment" attachment_id="'+item.id+'"><i class="ni ni-fat-remove"></i></button></div> </div>';
          $("#existing_attachments").html($("#existing_attachments").html()+existing_attachment);
        });

        // $("#existing_attachments").html($("#existing_attachments").html()+"<hr/>");
        $(".remove_existing_attachment").click(function() {
          let this_button=$(this);
          Swal.fire({
            title:'Delete Attachment',
            text: 'Are you sure to delete this attachment?',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: "Don't Delete",
            buttonsStyling: false,
            customClass: {
              confirmButton: 'btn btn-sm btn-danger',
              cancelButton: 'btn btn-sm btn-default',
            },
            icon: 'warning'
          }).then((result) => {
            if (result.value == true) {
              $.ajax({
                'url': "{{route('archive.delete_notice_attachment')}}",
                'data': {
                  "_token": "{{ csrf_token() }}",
                  "id" : $(this).attr('attachment_id'),
                },
                'method': "post",
                'success': function(data) {
                  if(data=="success"){
                    swal.fire({
                      title: `Success`,
                      text: 'Successfully deleted attachment',
                      buttonsStyling: false,
                      icon: 'success',
                      buttonsStyling: false,
                      customClass: {
                        confirmButton: 'btn btn-sm btn-success',
                      },
                    });
                    $(this_button).parents('.existing_attachment_group').remove();
                  }
                  else{
                    location.reload();
                  }

                }
              });
            }
          });
        });

      }
    }
  });


  $("#archive").modal('show');
});
@endif

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


$("#add_more_attachment").click(function functionName() {
  $("#attachment_div .row").last().after('<div class="row attachment_group"><div class="col-md-11"><input  type="file"  name="attachments[]" accept="application/pdf" class="form-control attachment"></div><div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_attachment"><i class="ni ni-fat-remove"></i></button></div></div>');
  $(".remove_attachment").click(function functionName() {
    $(this).parents('.attachment_group').remove();
  });
});

if(edit_id!=null){
  $("#archive_title").html("Update Notice Attachment/s");
  $("#edit-btn-"+edit_id).trigger('click');
}
else{
  $("#archive_title").html("Add Notice Attachment/s");
}


$("#filter_btn").click(function functionName() {
  $("#filter_notices").submit();
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

$("#year").change(function() {
  $("#filter_notices").submit();
});

@if($type === 'NOD' || $type === 'NOPD')
$("#date_received_by_contractor").change(function(){
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
});
@endif




</script>
@endpush
