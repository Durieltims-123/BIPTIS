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
              action="{{route('archive.submit_notice_of_meeting')}}" enctype="multipart/form-data">
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

                <!-- Attachment -->
                <div class="form-group col-xs-12 col-sm-12 col-lg-12">
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
            <form class="row" id="filter_archive_notice_of_meeting" method="post" action="{{route('archive.filter_notice_of_meeting')}}">
              @csrf
              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="year" class="input-sm">Project Year </label>
                <input class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy"
                minimum-view="year" value="{{old('year')}}">
                <label id="year_error" class="error-msg text-red">@error('year'){{$message}}@enderror
                </label>
              </div>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="archive_table">
              <thead class="">
                <tr class="bg-primary text-white">
                  <th class="text-center"></th>
                  <th class="text-center">Meeting ID</th>
                  <th class="text-center">Meeting Date</th>
                  <th class="text-center">Meeting Room</th>
                  <th class="text-center">Created At</th>
                  <th class="text-center">Updated At</th>
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

var oldInputs=@json(count(session()->getOldInput()));
let year_error = $("#year_error").html();
let old_year=@json(old('year'));


if(old_year==null){
  $("#year").val(@json($year));
}

// datatables
$('#archive_table thead tr').clone(true).appendTo('#archive_table thead');
$('#archive_table thead tr:eq(1)').removeClass('bg-primary');

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
  endDate: '{{$year}}'
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

console.log("{{session('message')}}");

if ("{{session('message')}}") {
  if ("{{session('message')}}" == "duplicate_error") {
    swal.fire({
      title: `Duplicate`,
      text: 'We already have the same entry in the database!',
      buttonsStyling: false,
      icon: 'warning',
      customClass: {
        confirmButton: 'btn btn-sm btn-warning',
      },
    });
  }

  else if ("{{session('message')}}" == "missing_attachment") {
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

  else if ("{{session('message')}}" == "opening_error") {
    swal.fire({
      title: "Date Opened Error",
      text: 'Sorry! There were no projects opened on the selected date!',
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


var data = @json(session('data'));
if (data == null) {
  data = @json($data);
}

var table = $('#archive_table').DataTable({
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
      className: 'btn btn-sm shadow-0 border-0 bg-warning text-white'
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
  responsive: true,
  columnDefs: [{
    targets: 0,
    orderable: false
  }],
  columns: [
    {
      data: "with_attachment",
      render: function (data, type, row) {
        var id = row.meeting_id;
        if(data==1){
          return '<div style="white-space: nowrap"> @if(in_array("update",$user_privilege))<button class="btn btn-sm btn-success edit-btn  " id="edit-btn-'+id+'" data-toggle="tooltip" data-placement="top" title="Edit"><i class="ni ni-ruler-pencil"></i></button>@endif  @if(in_array("delete",$user_privilege)) <button class="btn btn-sm btn-danger delete-btn  " notice_of_meeting_id="'+id+'" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button> @endif <a  class="btn btn-sm shadow-0 border-0 btn-primary text-white" target="_blank"  href="/archive/view_notice_of_meeting_attachments/' + id + '"><i class="ni ni-tv-2" data-toggle="tooltip" data-placement="top" title="View Attachment/s"></i></a><div>';
        }
        else{
          return ' @if(in_array("add",$user_privilege))<button class="btn btn-sm btn-primary edit-btn" id="edit-btn-'+id+'" data-toggle="tooltip" data-placement="top" title="Add"><i class="ni ni-fat-add"></i></button>@endif';
        }
      }
    },
    { "data": "meeting_id" },
    { "data": "meeting_date" ,
    render: function (data, type, row) {
      return moment(data).format('LL');
    }},
    { "data": "address" },
    { "data": "created_at",
    render: function (data, type, row) {
      return moment(data).format('LL');
    }},
    { "data": "updated_at" ,
    render: function (data, type, row) {
      return moment(data).format('LL');
    }}
  ],
  order: [[1, "desc"]]
});



if (year_error != null && year_error != '') {
  $('#filter').removeClass('d-none');
  $('#filter_btn').removeClass('d-none');
  $('#show_filter').text('Hide Filter');
}





$('#archive_table thead tr:eq(1) th').each(function (i) {
  var title = $(this).text();
  if (title != "") {
    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
    $(this).addClass('sorting_disabled');
    $('input', this).on('keyup change', function () {
      if (table.column(i).search() !== this.value) {
        table
        .column(i)
        .search(this.value)
        .draw();
      }
    });
  }
});



$("#filter_btn").click(function () {
  $("#filter_archive_notice_of_meeting").submit();
});

$("#year").change(function () {
  $("#filter_archive_notice_of_meeting").submit();
});

@if(in_array('update',$user_privilege)||in_array('add',$user_privilege))
$('#archive_table tbody').on('click', '.edit-btn', function (e) {
  table.rows().deselect();
  var row = table.row($(this).parents('tr')).data();
  $("#existing_attachments").html('');
  table.rows().deselect();
  $("#id").val(row.meeting_id);
  $("#attachment_div").find('.attachment_group').each(function (index) {
    if(index!=0){
      $(this).remove();
    }
  });
  $("#archive_title").html('Add/Update Archive');

  $.ajax({
    'url': "{{route('archive.get_archive_notice_of_meeting_attachments')}}",
    'data': {
      "_token": "{{ csrf_token() }}",
      "meeting_id" : row.meeting_id,
    },
    'method': "post",
    'success': function(data) {
      if(data.length>0){
        data.forEach((item, i) => {
          let existing_attachment='<div class="row existing_attachment_group">';
          existing_attachment=existing_attachment+'<div class="col-md-11">';
          existing_attachment=existing_attachment+'<div class="form-control attachment">';
          existing_attachment=existing_attachment+'<a href="/archive/view_notice_of_meeting_attachment/'+item.id+'" target="_blank"> Attachment '+(i+1)+'</a>';
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
                'url': "{{route('archive.delete_notice_of_meeting_attachment')}}",
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

@if(in_array('delete',$user_privilege))
$('#archive_table tbody').on('click', '.delete-btn', function (e) {
  var row = table.row($(this).parents('tr')).data();
  let this_button=$(this);
  Swal.fire({
    title:'Delete Data',
    text: 'Are you sure to delete this data?',
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
        'url': "{{route('archive.delete_notice_of_meeting')}}",
        'data': {
          "_token": "{{ csrf_token() }}",
          "id" : $(this).attr('notice_of_meeting_id'),
          "year":$("#year").val(),
        },
        'method': "post",
        'success': function(data) {
          table.clear().draw();
          table.rows.add(data); // Add new data
          table.columns.adjust().draw();
          swal.fire({
            title: `Success`,
            text: 'Successfully deleted data',
            buttonsStyling: false,
            icon: 'success',
            buttonsStyling: false,
            customClass: {
              confirmButton: 'btn btn-sm btn-success',
            },
          });
          console.log(table.row($(this_button).parents('tr')).data());
        }
      });
    }
  });

});
@endif

$("input").change(function () {
  $(this).siblings('.error-msg').html("");
});

$(".custom-radio").change(function () {
  $(this).parent().siblings('.error-msg').html("");
});

$("select").change(function () {
  $(this).siblings('.error-msg').html("");
});

$("#add_more_attachment").click(function () {
  $("#attachment_div .row").last().after('<div class="row attachment_group"><div class="col-md-11"><input  type="file"  name="attachments[]" accept="application/pdf" class="form-control attachment"></div><div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_attachment"><i class="ni ni-fat-remove"></i></button></div></div>');
  $(".remove_attachment").click(function () {
    $(this).parents('.attachment_group').remove();
  });
});


if(oldInputs>1 &&  "{{ old('year') }}"==""){
  $("#archive").modal('show');
  if($("#id").val()!=""){
    $("#archive_title").html("Update Archive");
    $("#edit-btn-"+$("#id").val()).trigger("click");
  }
  else{
    $("#archive_title").html("Add Archive");
  }
}

</script>
@endpush
