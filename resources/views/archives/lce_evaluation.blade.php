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
    <div class="modal" tabindex="-1" role="dialog" id="evaluation">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="evaluation_title"></h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="col-sm-12" method="POST" id="evaluation_form"action="{{route('submit_lce_evaluation')}}" enctype="multipart/form-data">
              @csrf
              <div class="row d-flex">

                <!--ID -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 pb-1 mb-1 d-none">
                  <label for="id">ID <span class="text-red">*</span></label>
                  <input type="text" id="id" name="id" class="form-control" readonly value="{{old('id')}}">
                  <label class="error-msg text-red">@error('id'){{$message}}@enderror
                  </label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 pb-1 mb-1">
                  <label for="opening_date">Opening Date<span class="text-red">*</span>
                  </label>
                  <input  type="text" id="opening_date" name="opening_date" class="form-control datepicker" value="{{old('opening_date')}}" >
                  <label class="error-msg text-red" >@error('opening_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 pb-1 mb-1 d-none">
                  <label for="plan_id">Plan ID</label>
                  <input type="text" id="plan_id" name="plan_id" class="form-control " value="{{old('plan_id')}}" >
                  <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 pb-1 mb-1">
                  <label for="project_number">Project Number<span class="text-red">*</span></label>
                  <input type="text" id="project_number" name="project_number" class="form-control " value="{{old('project_number')}}" readonly >
                  <label class="error-msg text-red" >@error('project_number'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 pb-1 mb-1">
                  <label for="project_title">Project Title<span class="text-red">*</span></label>
                  <input type="text" id="project_title" name="project_title" class="form-control " value="{{old('project_title')}}" >
                  <label class="error-msg text-red" >@error('project_title'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none pb-1 mb-1">
                  <label for="project_bid">Project Bid</label>
                  <input type="text" id="project_bid" name="project_bid" class="form-control " value="{{old('project_bid')}}" >
                  <label class="error-msg text-red" >@error('project_bid'){{$message}}@enderror</label>
                </div>


                <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none pb-1 mb-1">
                  <label for="contractor_id">Contractor ID</label>
                  <input type="text" id="contractor_id" name="contractor_id" class="form-control " value="{{old('contractor_id')}}" >
                  <label class="error-msg text-red" >@error('contractor_id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 pb-1 mb-1">
                  <label for="contractor">Contractor<span class="text-red">*</span></label>
                  <input type="text" id="contractor" name="contractor" class="form-control " value="{{old('contractor')}}" >
                  <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 pb-1 mb-1">
                  <label for="evaluation_date">Evaluation Date<span class="text-red">*</span></label>
                  <input type="text" id="evaluation_date" name="evaluation_date" class="form-control datepicker" value="{{old('evaluation_date')}}" >
                  <label class="error-msg text-red" >@error('evaluation_date'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 pb-1 mb-1">
                  <label for="status">Status<span class="text-red">*</span></label>
                  <select type="" id="status" name="status" class="form-control" value="{{old('status')}}" >
                    <option value=""  {{ old('status') == '' ? 'selected' : ''}} >Select Account Classification</option>
                    <option value="disapproved"  {{ old('status') == 'disapproved' ? 'selected' : ''}} >Disapproved</option>
                  </select>
                  <label class="error-msg text-red" >@error('status'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                  <label for="reason">Reason<span class="text-red">*</span></label>
                  <input type="text" id="reason" name="reason" class="form-control " value="{{old('reason')}}" >
                  <label class="error-msg text-red" >@error('reason'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                  <label for="remarks">Remarks</label>
                  <input type="text" id="remarks" name="remarks" class="form-control " value="{{old('remarks')}}" >
                  <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror</label>
                </div>


                <div class="form-group col-xs-6 col-sm-6 col-lg-6 pb-1 mb-1">
                  <label for="contractor_date_received">Date Received by Contractor<span class="text-red">*</span></label>
                  <input type="text" id="contractor_date_received" name="contractor_date_received" class="form-control datepicker" value="{{old('contractor_date_received')}}" >
                  <label class="error-msg text-red" >@error('contractor_date_received'){{$message}}@enderror</label>
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
                    Attachment/s</button>
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
            <form class="row" id="app_filter" method="post" action="{{route('filter_lce_evaluation')}}">
              @csrf


              <div class="form-group col-xs-12 col-sm-2 col-lg-2 mb-0">
                <label for="project_year">Project  Year<span class="text-red">*</span></label>
                <input type="text" id="project_year" name="project_year" class=" yearpicker form-control  form-control" >
                <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror
                </label>
              </div>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered wrap" id="evaluation_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">Project Number</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Contractor</th>
                  <th class="text-center">Evaluation Date</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Reason</th>
                  <th class="text-center">Remarks</th>
                  <th class="text-center">Date Received by Contractor</th>
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


var project_title_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_unreceive_project_titles',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term,
        "opening_date":$("#opening_date").val(),
      },
      'method': "post",
      'dataType': "json",
      'success': function(data) {
        response(data);
      }
    });
  },
  select: function(event, ui){
    if(ui.item.id != ''){
      $(this).val(ui.item.value);
    }else{
      $(this).val('');
    }
    return false;
  },
  change: function (event, ui) {

    $("#contractor").val('');
    $("#contractor_id").val('');

    if (ui.item == null || ui.item=="") {
      if("{{old('plan_id')}}"!=''){
        $(this).val("{{old('project_title')}}");
        $("#plan_id").val("{{old('plan_id')}}");
        $("#project_number").val("{{old('project_number')}}");
        $("#project_type").val("{{old('project_type')}}");
      }
      else{
        $(this).val('');
        $("#plan_id").val('');
        $("#project_number").val('');
        $("#project_type").val('');
      }
    }
    else{
      var selected_project=ui.item;
      $("#plan_id").val(selected_project.id);
      $("#project_number").val(selected_project.project_number);
      $("#project_type").val(selected_project.project_type);
    }

  }
}

$("#project_title").autocomplete(project_title_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});



var contractor_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_bidders',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term,
        "plan_id":$("#plan_id").val(),
        "opening_date":$("#opening_date").val(),
        "status":"responsive",
      },
      'method': "post",
      'dataType': "json",
      'success': function(data) {
        response(data);
      }
    });
  },
  select: function(event, ui){

    if(ui.item.id != ''){
      $(this).val(ui.item.value);
    }else{
      $(this).val('');
    }
    return false;
  },
  change: function (event, ui) {
    //
    if (ui.item == null || ui.item=="") {
      if("{{old('contractor_id')}}"!=''){
        $(this).val("{{old('contractor')}}");
        $("#contractor_id").val("{{old('contractor_id')}}");
        $("#project_bid").val("{{old('project_bid')}}");
      }
      else{
        $(this).val('');
        $("#contractor_id").val('');
        $("#project_bid").val('');
      }
    }
    else{
      var selected_contractor=ui.item;
      $("#contractor_id").val(selected_contractor.contractor_id);
      $("#project_bid").val(selected_contractor.id);
    }

  }
}

$("#contractor").autocomplete(contractor_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});


var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>=3){
  if('{{old("id")}}'===""||'{{old("id")}}'==="null"||'{{old("id")}}'===null){
    $("#evaluation_title").html("Add Evaluation");
  }
  else{
    $("#project_title").prop('readonly',true);
    $("#contractor").prop('readonly',true);
    $("#opening_date").prop('readonly',true);
    $("#opening_date").datepicker('destroy');
    $("#project_title").autocomplete({ disabled: true });
    $("#contractor").autocomplete({ disabled: true });
    $("#evaluation_title").html("Update Evaluation");
    $.ajax({
      'url': "{{route('get_lce_evaluation_attachments')}}",
      'data': {
        "_token": "{{ csrf_token() }}",
        "id" : $("#id").val(),
      },
      'method': "post",
      'success': function(data) {
        if(data.length>0){
          data.forEach((item, i) => {
            let existing_attachment='<div class="row existing_attachment_group">';
            existing_attachment=existing_attachment+'<div class="col-md-11">';
            existing_attachment=existing_attachment+'<div class="form-control attachment">';
            existing_attachment=existing_attachment+'<a href="/lce_evaluation_attachment/'+item.id+'" target="_blank"> Attachment '+(i+1)+'</a>';
            existing_attachment=existing_attachment+'</div> </div> <div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_existing_attachment" attachment_id="'+item.id+'"><i class="ni ni-fat-remove"></i></button></div> </div>';
            $("#existing_attachments").html($("#existing_attachments").html()+existing_attachment);
          });

          $("#existing_attachments").html($("#existing_attachments").html()+"<hr/>");
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
                  'url': "{{route('delete_lce_evaluation_attachment')}}",
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
  }
  $("#evaluation").modal("show");
}

// table data
let data= @json(session('data'));

if(data==null){
  data= @json($data);
}


// format to currency
Number.prototype.toCurrencyString = function(){
  return this.toFixed(2).replace(/(\d)(?=(\d{3})+\b)/g, '$1 ');
}



// sessions/messages
if("{{session('message')}}"){
  if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
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
  else if ("{{session('message')}}" == "reload") {
    window.location.reload();
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
$('#evaluation_table thead tr').clone(true).appendTo( '#evaluation_table thead' );
$('#evaluation_table thead tr:eq(1)').removeClass('bg-primary');

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

var table=  $('#evaluation_table').DataTable({
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
    @if(in_array('add',$user_privilege))
    {
      text: 'Add',
      attr: {
        id: 'add_data'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-danger text-white filter_btn',
      action: function ( e, dt, node, config ) {
        $("#project_title").prop('readonly',false);
        $("#contractor").prop('readonly',false);
        $("#opening_date").prop('readonly',false);
        $("#opening_date").datepicker();
        $("#project_title").autocomplete({ disabled: false });
        $("#contractor").autocomplete({ disabled: false });
        $("#project_title").autocomplete(project_title_autocomplete_init).focus(function() {
          $("#project_title").autocomplete('search', $("#project_title").val())
        });

        $("#contractor").autocomplete(contractor_autocomplete_init).focus(function() {
          $("#contractor").autocomplete('search', $("#contractor").val())
        });


        $(".error-msg").each(function () {
          $(this).html("");
        });
        $("#evaluation_form")[0].reset();
        $("#evaluation_title").html("Add Evaluation");
        $('#id').val('');
        $("#existing_attachments").html('');
        $("#attachment_div").find('.attachment_group').each(function(index) {
          if(index!=0){
            $(this).remove();
          }
        });
        $("#evaluation").modal('show');
      }
    },
    @endif
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
    { "data":"with_attachment",
    render: function ( data, type, row ) {
      return "<div style='white-space: nowrap'>@if(in_array('update',$user_privilege))<button  data-toggle='tooltip' data-placement='top' title='Edit' class='btn btn-sm shadow-0 border-0 btn-success edit-btn  '><i class='ni ni-ruler-pencil'></i></button> @endif @if(in_array('delete',$user_privilege)) <button data-toggle='tooltip' data-placement='top' title='Delete'  class='btn btn-sm shadow-0 border-0 btn-danger delete-btn  '><i class='ni ni-basket text-white'></i></button> @endif <a data-toggle='tooltip' data-placement='top' title='View' target='_blank' href='/view_lce_evaluation_attachments/"+row.id+"' class='btn btn-sm shadow-0 border-0 btn-primary text-white'><i class='ni ni-tv-2'></i></a></div>";
    }},

    {"data":"id"},
    {"data":"project_no"},
    {"data":"project_title"},
    {"data":"business_name"},
    {"data":"lce_evaluation_date"},
    {"data":"lce_evaluation_status"},
    {"data":"lce_evaluation_reason"},
    {"data":"lce_evaluation_remarks"},
    {"data":"lce_contractor_date_received"},
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

});

// inputs
if("{{old('project_year')}}"==""){
  $("#project_year").datepicker("update","{{$year}}");
}
else{
  $("#project_year").datepicker("update","{{old('project_year')}}");
}

$("#project_year").change(function () {
  $("#app_filter").submit();
});

$("#app_group").change(function () {
  $("#app_filter").submit();
});

$("#fund_category").change(function () {
  $("#app_filter").submit();
});

// events
$('#evaluation_table thead tr:eq(1) th').each( function (i) {
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

@if(in_array('update',$user_privilege))
$('#evaluation_table tbody').on('click', '.edit-btn', function(e) {
  $("#evaluation_title").html("Update Evaluation");
  var row = table.row($(this).parents('tr')).data();
  $("#id").val(row.id);
  $("#plan_id").val(row.plan_id);
  $("#project_number").val(row.project_no);
  $("#project_title").val(row.project_title);
  $("#project_bid").val(row.project_bid);
  $("#contractor_id").val(row.contractor_id);
  $("#contractor").val(row.business_name);
  $("#evaluation_date").datepicker('update',moment(row.lce_evaluation_date,'Y-MM-DD').format('L'));
  $("#contractor_date_received").datepicker('update',moment(row.lce_contractor_date_received,'Y-MM-DD').format('L'));
  $("#reason").val(row.lce_evaluation_reason);
  $("#status").val(row.lce_evaluation_status);
  $("#remarks").val(row.lce_evaluation_remarks);
  $("#opening_date").datepicker('update',moment(row.open_bid,'Y-MM-DD').format('L'));


  $("#project_title").prop('readonly',true);
  $("#contractor").prop('readonly',true);
  $("#opening_date").prop('readonly',true);
  $("#opening_date").datepicker('destroy');
  $("#project_title").autocomplete({ disabled: true });
  $("#contractor").autocomplete({ disabled: true });

  $("#existing_attachments").html('');
  $("#attachment_div").find('.attachment_group').each(function(index) {
    if(index!=0){
      $(this).remove();
    }
  });

  $.ajax({
    'url': "{{route('get_lce_evaluation_attachments')}}",
    'data': {
      "_token": "{{ csrf_token() }}",
      "id" : row.id,
    },
    'method': "post",
    'success': function(data) {
      if(data.length>0){
        data.forEach((item, i) => {
          let existing_attachment='<div class="row existing_attachment_group">';
          existing_attachment=existing_attachment+'<div class="col-md-11">';
          existing_attachment=existing_attachment+'<div class="form-control attachment">';
          existing_attachment=existing_attachment+'<a href="/lce_evaluation_attachment/'+item.id+'" target="_blank"> Attachment '+(i+1)+'</a>';
          existing_attachment=existing_attachment+'</div> </div> <div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_existing_attachment" attachment_id="'+item.id+'"><i class="ni ni-fat-remove"></i></button></div> </div>';
          $("#existing_attachments").html($("#existing_attachments").html()+existing_attachment);
        });

        $("#existing_attachments").html($("#existing_attachments").html()+"<hr/>");
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
                'url': "{{route('delete_lce_evaluation_attachment')}}",
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
  $("#evaluation").modal('show');
});
@endif

@if(in_array('delete',$user_privilege))
$('#evaluation_table tbody').on('click', '.delete-btn', function (e) {
  var row = table.row($(this).parents('tr')).data();
  let this_button=$(this);
  Swal.fire({
    title:'Delete Data',
    text: 'Are you sure to delete the data and all its attachments?',
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
        'url': "{{route('delete_lce_evaluation')}}",
        'data': {
          "_token": "{{ csrf_token() }}",
          "id" :row.id,
        },
        'method': "post",
        'success': function(data) {
          if(data=="success"){
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
            window.location.reload();
          }
        }
      });
    }
  });

});
@endif


$("#add_more_attachment").click(function() {
  $("#attachment_div .row").last().after('<div class="row attachment_group"><div class="col-md-11"><input  type="file"  name="attachments[]" accept="application/pdf" class="form-control attachment"></div><div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_attachment"><i class="ni ni-fat-remove"></i></button></div></div>');
  $(".remove_attachment").click(function() {
    $(this).parents('.attachment_group').remove();
  });
});


$("input").click(function functionName() {
  $(this).siblings('.error-msg').html("");
});

$(".custom-radio").click(function functionName() {
  $(this).parent().siblings('.error-msg').html("");
});

$("select").click(function functionName() {
  $(this).siblings('.error-msg').html("");
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
