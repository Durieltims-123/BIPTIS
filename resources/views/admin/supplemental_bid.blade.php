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
      <div class="modal" tabindex="-1" role="dialog" id="supplemental_modal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="supplemental_modal_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="supplemental_form" action="{{route('submit_supplemental_bid')}}" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex">

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                    <label for="supplemental_bid_id">ID <span class="text-red">*</span></label>
                    <input  type="text" id="supplemental_bid_id" name="supplemental_bid_id" class="form-control form-control-sm" readonly value="{{old('supplemental_bid_id')}}" >
                    <label class="error-msg text-red" >@error('supplemental_bid_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="title">Title <span class="text-red">*</span></label>
                    <input  type="text" id="title" name="title" class="form-control form-control-sm" value="{{old('title')}}" >
                    <label class="error-msg text-red" >@error('title'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- Opening Date -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="opening_date">Opening Date <span class="text-red">*</span></label>
                    <input  type="text" id="opening_date" name="opening_date" class="form-control form-control-sm datepicker2" value="{{old('opening_date')}}" >
                    <label class="error-msg text-red" >@error('opening_date'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- Date Issued -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                    <label for="date_issued">Date Issued <span class="text-red">*</span></label>
                    <input  type="text" id="date_issued" name="date_issued" class="form-control form-control-sm datepicker2" value="{{old('date_issued')}}" >
                    <label class="error-msg text-red" >@error('date_issued'){{$message}}@enderror
                    </label>
                  </div>



                  <!-- project_title -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="plan_title">Select Project Title</label>
                    <input list="plan_title" type="text" id="plan_title" name="plan_title" class="form-control form-control-sm" value="" >
                    <label class="error-msg text-red" >@error('plan_title'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- plan_ids  -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <label for="plan_ids">Selected Plan Ids</label>
                    <input type="text" id="plan_ids" name="plan_ids" class="form-control form-control-sm" value=""  readonly>
                    <label class="error-msg text-red" >@error('plan_ids'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="">Selected Projects<span class="text-red">*</span></label>
                    <ul class="border rounded pb-1" id="selected_plan_titles">

                    </ul>
                    <label class="error-msg text-red" >@error('plan_ids') Please Select Project Plans @enderror
                    </label>
                  </div>

                  <div id="attachment_div" class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="attachment">Attachment <span class="text-red">*</span></label>
                    <!-- Attachment -->
                    <div id="existing_attachments">
                    </div>

                    <div class="row attachment_group">
                      <div class="col-md-11">
                        <input  type="file"  name="attachments[]" accept="application/pdf" class="form-control" value="{{old('attachment')}}" >
                      </div>
                    </div>
                    <button type="button" id="add_more_attachment" class="btn btn-sm btn btn-sm btn-primary m    mt-1">Add More Attachments</button>
                    <label class="error-msg text-red" >@error('attachment'){{$message}}@enderror
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
          <div class="col-sm-12 " id="filter">
            <form class="row" id="filter_form" method="post" action="filter_sb">
              @csrf
              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="year" class="input-sm">Year </label>
                <input  class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}" >
                <label class="error-msg text-red" >@error('year'){{$message}}@enderror
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
                  <th class="text-center">Title</th>
                  <th class="text-center">Date of Opening</th>
                  <th class="text-center">Date Issued</th>
                  <th class="text-center">Plan ID</th>
                  <th class="text-center">Project Title</th>
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

function SupplementalBidSearch(json,search) {
  var data=json;
  return data.filter(
    function(data){ return data.supplemental_bid_id == search }
  );
}


var project_title_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_project_titles',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term,
        "mode_id":'1',
        "opening_date":$("#opening_date").val()
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
      var selected_project=ui.item;
      var plan_ids_array;
      var project_title="<li plan_id='"+selected_project.id+"'><button type='button' class='btn btn-sm btn btn-sm btn-danger mr-2 remove_plan_title'><i class='ni ni-basket'></i></button>"+selected_project.value+"</li>";

      var plan_ids=$("#plan_ids").val();
      if(plan_ids==""){
        $("#plan_ids").val(selected_project.id);
        $("#selected_plan_titles").html($("#selected_plan_titles").html()+project_title);
      }
      else{
        plan_ids_array=plan_ids.split(",");
        if(plan_ids.includes(selected_project.id)===false){
          $("#plan_ids").val($("#plan_ids").val()+","+selected_project.id);
          $("#selected_plan_titles").html($("#selected_plan_titles").html()+project_title);
        }
      }

      $(".remove_plan_title").click(function functionName() {
        $(this).parent().remove();
        console.log("test");
        var plan_ids=$("#plan_ids").val();
        var plan_ids_text="";
        $("#selected_plan_titles li").each(function (index){
          if(plan_ids_text==""){
            plan_ids_text=$(this).attr('plan_id');
          }
          else{
            plan_ids_text=plan_ids_text+","+$(this).attr('plan_id');
          }
        });

        $("#plan_ids").val(plan_ids_text);
        $("#plan_title").val("");
      });
    }else{
      $(this).val('');
    }
    return false;
  },
}

$("#plan_title").autocomplete(project_title_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});


$("#opening_date").change(function () {
  $("#selected_plan_titles").html('');
  $("#plan_ids").val('');
  $("#plan_title").val('');
});


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
  else if("{{session('message')}}"=="missing_attachment"){
    swal.fire({
      title: `Missing Attachment`,
      text: 'Please attach your document in pdf format',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-warning',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="opening_error"){
    swal.fire({
      title: `Opening Error`,
      text: 'No projects were scheduled on the selected opening date.',
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
    $("#supplemental_modal").modal('hide');
  }

  else if("{{session('message')}}"=="duplicate_control_number"){
    swal.fire({
      title: `Error`,
      text: 'Duplicate Control Number',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }

  else if("{{session('message')}}"=="delete_success"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Successfully deleted Supplemental Bid',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#supplemental_modal").modal('hide');
  }

  else if("{{session('message')}}"=="bid_opening_done"){
    swal.fire({
      title: `Error`,
      text: 'Sorry, You cannot manipulate Bid Doc after Submission/Opening of bids!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }

  else if("{{session('message')}}"=="delete_error"){
    swal.fire({
      title: `Error`,
      text: 'You cannot delete Bid Doc for Received Bid Doc',
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
var data = {!! json_encode(session('supplemental_bids')) !!};

if(data==null){
  data = {!! json_encode($supplemental_bids) !!};
}

var table=  $('#app_table').DataTable({
  dom: 'Bfrtip',
  buttons: [
    {
      text: 'Hide Filter',
      attr: {
        id: 'show_filter'
      },
      className: 'btn btn-sm btn btn-sm shadow-0 border-0 bg-dark text-white',
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
      className: 'btn btn-sm btn btn-sm shadow-0 border-0 bg-warning text-white'
    },
    @if(in_array("add",$user_privilege))
    {
      text: 'Add SB',
      attr: {
        id: 'add_supplemental_bid'
      },
      className: 'btn btn-sm btn btn-sm shadow-0 border-0 bg-danger text-white add_supplemental_bid'
    },
    @endif
    {
      text: 'Excel',
      extend: 'excel',
      className: 'btn btn-sm btn btn-sm shadow-0 border-0 bg-success text-white'
    },
    {
      text: 'Print',
      extend: 'print',
      className: 'btn btn-sm btn btn-sm shadow-0 border-0 bg-info text-white'
    }

  ],
  data:data,
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
  columns: [
    { data:"",
    render: function ( data, type, row ) {
      var  supplemental_bid_id=row.supplemental_bid_id;
      return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button class="btn btn-sm btn btn-sm btn-success edit-btn btn-smmr-0" data-toggle="tooltip" data-placement="top" title="Edit" id="edit-btn-'+supplemental_bid_id+'"> <i class="ni ni-ruler-pencil"></i></button>@endif @if(in_array("delete",$user_privilege)) <button class="btn btn-sm btn btn-sm btn-danger delete-btn btn-smmr-0" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button>@endif <a  class="btn btn-sm btn btn-sm shadow-0 border-0 btn-primary text-white" target="_blank" data-toggle="tooltip" data-placement="top" title="View File"  href="/view_supplemental_bid/'+supplemental_bid_id+'"><i class="ni ni-tv-2"></i></a></div>';
    }
  },
  { "data": "supplemental_bid_id" },
  { "data": "title" },
  { "data": "date_opened" },
  { "data": "date_issued" },
  { "data": "plan_id" },
  { "data": "project_title",render: function ( data, type, row ) {
    if(data!=null){
      return "<a  data-toggle='tooltip' data-placement='top' title='Edit Project' href='/edit_app/"+row.plan_id+"' target='_blank' class=' shadow-0 border-0 '>"+data+"</a>";
    }
    else{
      return "";
    }
  }}
],
order: [[ 1, "desc" ]]
});

$("#add_more_attachment").click(function functionName() {
  $("#attachment_div .attachment_group").last().after('<div class="row attachment_group more_attachment_group"><div class="col-md-11"><input  type="file"  name="attachments[]" accept="application/pdf" class="form-control attachment"></div><div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_attachment"><i class="ni ni-fat-remove"></i></button></div></div>');
  $(".remove_attachment").click(function functionName() {
    $(this).parents('.attachment_group').remove();
  });
});



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

@if(in_array("add",$user_privilege))
$(".add_supplemental_bid").click(function functionName() {

  table.rows().deselect();
  $("#title").val('');
  $("#opening_date").val('');
  $('.more_attachment_group').remove();
  $('.existing_attachment_group').remove();
  $("#date_issued").val('');
  $("#plan_title").val('');
  $("#selected_plan_titles").html('');
  $("#plan_ids").val('');
  $("#supplemental_form")[0].reset();
  $("#supplemental_modal_title").html("Add Supplemental ");
  $("#supplemental_modal").modal('show');
});
@endif

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit-btn', function (e) {
  table.rows().deselect();
  var plan_ids="";
  var row=table.row($(this).parents('tr')).data();
  $("#supplemental_modal_title").html("Update Supplemental Bid");
  $("#opening_date").datepicker('setDate',moment(row.date_opened).format('MM/DD/YYYY'));
  $("#date_issued").datepicker('setDate',moment(row.date_issued).format('MM/DD/YYYY'));
  $("#supplemental_bid_id").val(row.supplemental_bid_id);
  $("#title").val(row.title);
  $('.more_attachment_group').remove();
  $('.existing_attachment_group').remove();
  var supplemental_procacts=SupplementalBidSearch(data,row.supplemental_bid_id);
  supplemental_procacts.forEach((item, i) => {
    var project_title="<li plan_id='"+item.plan_id+"'><button type='button' class='btn btn-sm btn btn-sm btn-danger mr-2 remove_plan_title'><i class='ni ni-basket'></i></button>"+item.project_title+"</li>";
    if(plan_ids==""){
      plan_ids=item.plan_id;
      $("#selected_plan_titles").html($("#selected_plan_titles").html()+project_title);
    }
    else{
      plan_ids=plan_ids+","+item.plan_id;
      $("#selected_plan_titles").html($("#selected_plan_titles").html()+project_title);
    }
  });
  $("#plan_ids").val(plan_ids);
  $(".remove_plan_title").click(function functionName() {
    $(this).parent().remove();
    var plan_ids=$("#plan_ids").val();
    var plan_ids_text="";
    $("#selected_plan_titles li").each(function (index){
      if(plan_ids_text==""){
        plan_ids_text=$(this).attr('plan_id');
      }
      else{
        plan_ids_text=plan_ids_text+","+$(this).attr('plan_id');
      }
    });

    $("#plan_ids").val(plan_ids_text);

  });

  $.ajax({
    'url': "{{route('get_supplemental_bid_attachments')}}",
    'data': {
      "_token": "{{ csrf_token() }}",
      "supplemental_bid_id": row.supplemental_bid_id,
    },
    'method': "post",
    'success': function(data) {
      if(data.length>0){
        $("#existing_attachments").html('');
        data.forEach((item, i) => {
          let existing_attachment='<div class="row existing_attachment_group">';
          existing_attachment=existing_attachment+'<div class="col-md-11">';
          existing_attachment=existing_attachment+'<div class="form-control attachment">';
          existing_attachment=existing_attachment+'<a href="view_supplemental_bid_attachment/'+item.supplemental_bid_attachment_id+'" target="_blank"> Attachment '+(i+1)+'</a>';
          existing_attachment=existing_attachment+'</div> </div> <div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_existing_attachment" attachment_id="'+item.supplemental_bid_attachment_id+'"><i class="ni ni-fat-remove"></i></button></div> </div>';
          $("#existing_attachments").html($("#existing_attachments").html()+existing_attachment);
        });

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
                'url': "{{route('delete_supplemental_bid_attachment')}}",
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



  $("#supplemental_modal").modal('show');
});
@endif

@if(in_array("delete",$user_privilege))
$('#app_table tbody').on('click', '.delete-btn', function (e) {
  var row=table.row($(this).parents('tr')).data();
  Swal.fire({
    text: 'Are you sure to delete this Supplemental Bid?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_supplemental_bid/"+row.supplemental_bid_id;
    }
  });

});
@endif

// show inputs/messages on load
var oldInputs='{{ count(session()->getOldInput()) }}';

if (oldInputs>=5) {
  if("{{old('supplemental_bid_id')}}"){
    $("#edit-btn-"+"{{old('supplemental_bid_id')}}").trigger("click");
    $("#supplemental_modal_title").html("Edit Supplemental ");
    $("#supplemental_modal").modal('show');
  }
  else{
    $("#supplemental_modal_title").html("Add Supplemental ");
    $("#supplemental_modal").modal('show');
  }
}
else if (oldInputs<5 && oldInputs>0) {
}
else{
  $("#year").val("{{$year}}");
}



$("#filter_btn").click(function () {
  $("#filter_form").submit();
});

$("#year").change(function () {
  $("#filter_form").submit();
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
