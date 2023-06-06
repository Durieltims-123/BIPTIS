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
      <div class="modal" tabindex="-1" role="dialog" id="motion_for_reconsideration">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" id="motion_for_reconsideration_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="col-sm-12" method="POST" id="supplemental_form" action="{{route('submit_motion_for_reconsideration')}}" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex">

                  <!-- MR ID -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                    <label for="mr_id">ID <span class="text-red">*</span></label>
                    <input  type="text" id="mr_id" name="mr_id" class="form-control form-control-sm" readonly value="{{old('mr_id')}}" >
                    <label class="error-msg text-red" >@error('mr_id'){{$message}}@enderror
                    </label>
                  </div>


                  <!-- Opening Date -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="opening_date">Opening Date <span class="text-red">*</span></label>
                    <input  type="text" id="opening_date" name="opening_date" class="form-control form-control-sm datepicker2" value="{{old('opening_date')}}" >
                    <label class="error-msg text-red" >@error('opening_date'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- Date Received -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                    <label for="date_received">Date Received by BAC Infra/Support<span class="text-red">*</span></label>
                    <input  type="text" id="date_received" name="date_received" class="form-control form-control-sm datepicker2" value="{{old('date_received')}}" >
                    <label class="error-msg text-red" >@error('date_received'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- project_title -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="plan_title">Project Title <span class="text-red">*</span></label>
                    <input list="plan_title" type="text" id="plan_title" name="plan_title" class="form-control form-control-sm" value="" >
                    <label class="error-msg text-red" >@error('plan_title'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- plan_id  -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <label for="plan_id">Selected Plan Ids</label>
                    <input type="text" id="plan_id" name="plan_id" class="form-control form-control-sm"  readonly>
                    <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none" >
                    <label for="mr_type">MR Type <span class="text-red">*</span></label>
                    <select type="text" id="mr_type" name="mr_type" class="form-control form-control-sm" >
                      <option value="Ineligible" @if(old('mr_type')==="Ineligible") selected @endif>Ineligible
                      </option>
                      <option value="Disqualified" @if(old('mr_type')==="Disqualified") selected @endif>Disqualified
                      </option>
                      <option value="Post Disqualified" @if(old('mr_type')==="Post Disqualified") selected @endif>Post Disqualified
                      </option>
                      <option value="Disapproved" @if(old('mr_type')==="Disapproved") selected @endif>Disapproved
                      </option>
                    </select>
                    <label class="error-msg text-red" >@error('mr_type'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                    <label for="">Selected Projects<span class="text-red">*</span></label>
                    <ul class="border rounded pb-1" id="selected_plan_titles">

                    </ul>
                    <label class="error-msg text-red" >@error('plan_id') Please Select Project Plans @enderror
                    </label>
                  </div>


                  <!-- contractor_id -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                    <label for="contractor_id">Contractor ID</label>
                    <input type="text" id="contractor_id" name="contractor_id" class="form-control form-control-sm" readonly>
                    <label class="error-msg text-red" >@error('contractor_id'){{$message}}@enderror
                    </label>
                  </div>



                  <!-- Contractor  -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 ui-widget mb-0">
                    <label for="contractor">Contractor<span class="text-red">*</span></label>
                    <input type="text" id="contractor" name="contractor" class="form-control form-control-sm" >
                    <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- Status -->
                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                    <label for="status">Status<span class="text-red">*</span></label>
                    <select type="text" id="status" name="status" class="form-control form-control-sm" >
                      <option value="pending"  {{ old('status') == 'pending' ? 'selected' : ''}} >Pending
                      </option>
                    </select>
                    <label class="error-msg text-red" >@error('status'){{$message}}@enderror
                    </label>
                  </div>


                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 ">
                    <label for="mr_due_date">MR Due Date<span class="text-red">*</span></label>
                    <input type="text" id="mr_due_date" name="mr_due_date" class="form-control form-control-sm datepicker" value="{{old('mr_due_date')}}" >
                    <label class="error-msg text-red" >@error('mr_due_date'){{$message}}@enderror
                    </label>
                  </div>

                  <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 ">
                    <label for="resolution_due_date">Resolution Due Date<span class="text-red">*</span></label>
                    <input type="text" id="resolution_due_date" name="resolution_due_date" class="form-control form-control-sm datepicker" value="{{old('resolution_due_date')}}" >
                    <label class="error-msg text-red" >@error('resolution_due_date'){{$message}}@enderror
                    </label>
                  </div>

                  <!-- Remarks  -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 ui-widget mb-0">
                    <label for="remarks">Remarks</label>
                    <input type="text" id="remarks" name="remarks" class="form-control form-control-sm" value="{{old('remarks')}}" >
                    <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror
                    </label>
                  </div>


                  <!-- Attachment -->
                  <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                    <label for="attachment">Attachment/s <span class="text-red">*</span></label>
                    <div id="attachment_div">
                      <div class="row attachment_group">
                        <div class="col-md-11">
                          <input  type="file"  name="attachments[]" accept="application/pdf" class="form-control attachment">
                        </div>
                      </div>
                    </div>
                    <div id="existing_attachments">

                    </div>
                    <button type="button" id="add_more_attachment" class="btn btn-sm btn btn-sm btn-primary mt-2">Add More Attachments</button>
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
          <div class="col-sm-12" id="filter">
            <form class="row" id="filter_motion_for_reconsideration" method="post" action="{{route('filter_motion_for_reconsideration')}}">
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
                  <th class="text-center">Plan ID</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Date of Opening</th>
                  <th class="text-center">Date Received</th>
                  <th class="text-center">MR Due Date</th>
                  <th class="text-center">Resolution Due Date</th>
                  <th class="text-center">Project Bid ID</th>
                  <th class="text-center">Contractor</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">MR Type</th>
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

$("#mr_due_date").change(function(){

  if(!moment($("#date_received").val()).isAfter(moment($(this).val()))){
    $.ajax({
      'url': '/calculate_due_date',
      'data': {
        "_token": "{{ csrf_token() }}",
        "date" : $(this).val(),
        "days" : 7,
        "date_type" : "Working Days",
      },
      'method': "post",
      'success': function(data) {
        console.log(data);
        if(data!=null){
          $("#resolution_due_date").val(data);
        }
        else{
          $("#resolution_due_date").val("");
        }
      }
    });
  }
  else{
    $("#resolution_due_date").val("");
  }
});

function MotionBidSearch(json,search) {
  var data=json;
  return data.filter(
    function(data){ return data.mr_id == search }
  );
}


var project_title_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){
    if($("#opening_date").val()=="" || $("#opening_date").val()==null){
      response([{'id':'','value':'No Match Found'}]);
    }
    else{
      $.ajax({
        'url': '/autocomplete_project_titles',
        'data': {
          "_token": "{{ csrf_token() }}",
          "term" : request.term,
          "mode_id":null,
          "show_all":true,
          "opening_date":$("#opening_date").val()
        },
        'method': "post",
        'dataType': "json",
        'success': function(data) {
          response(data);
        }
      });

    }
  },
  select: function(event, ui){
    if(ui.item.id != ''){
      $("#contractor").val('');
      $(this).val(ui.item.value);
    }else{
      $("#contractor").val('');
      $(this).val('');
    }
    return false;
  },
  change: function (event, ui) {

    if (ui.item == null || ui.item=="") {
      if("{{old('plan_id')}}"!=''){
      }
      else{
      }
    }
    else{
      var selected_project=ui.item;
      var plan_ids_array;
      var project_title="<li plan_id='"+selected_project.id+"'><button type='button' class='btn btn-sm btn btn-sm btn-danger mr-2 remove_plan_title'><i class='ni ni-basket'></i></button>"+selected_project.value+"</li>";

      var plan_ids=$("#plan_id").val();
      if(plan_ids==""){
        $("#plan_id").val(selected_project.id);
        $("#selected_plan_titles").html($("#selected_plan_titles").html()+project_title);
      }
      else{
        plan_ids_array=plan_ids.split(",");
        if(plan_ids.includes(selected_project.id)===false){
          $("#plan_title").val('');
          $("#plan_id").val($("#plan_id").val()+","+selected_project.id);
          $("#selected_plan_titles").html($("#selected_plan_titles").html()+project_title);
        }
      }

      $(".remove_plan_title").click(function functionName() {
        $(this).parent().remove();
        var plan_ids=$("#plan_id").val();
        var plan_ids_text="";
        $("#selected_plan_titles li").each(function (index){
          if(plan_ids_text==""){
            plan_ids_text=$(this).attr('plan_id');
          }
          else{
            plan_ids_text=plan_ids_text+","+$(this).attr('plan_id');
          }
        });
        $("#plan_id").val(plan_ids_text);

      });
    }

  }
}

$("#plan_title").autocomplete(project_title_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});

var contractor_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_similar_bidders',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term,
        "opening_date":$("#opening_date").val(),
        "plan_id":$("#plan_id").val()
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
      console.log(ui.item);
      $(this).val(ui.item.value);
      $("#contractor_id").val(ui.item.id);
      $("#mr_due_date").val(ui.item.mr_due_date);
      $("#mr_due_date").trigger('change');
      if(ui.item.notice_type=="NOI"){
        $("#mr_type").val("Ineligible");
      }
      else if(ui.item.notice_type=="NOD"){
        $("#mr_type").val("Disqualified");
      }
      else if(ui.item.notice_type=="NODA"){
        $("#mr_type").val("Disapproved");
      }
      else if(ui.item.notice_type=="NOPD"){
        $("#mr_type").val("Post Disqualified");
      }
      else{
        $("#mr_type").val('');
      }

    }else{
      $(this).val('');
      $("#contractor_id").val('');
      $("#resolution_due_date").val('');
    }
    return false;
  },
  change: function (event, ui) {

    if (ui.item == null || ui.item=="") {
      var old_data="{{old('contractor')}}";
      if(old_data!="" || old_data!=null){
        $(this).val("{{old('contractor')}}");
        $("#contractor_id").val("{{old('contractor_id')}}");
      }
      else{
        $(this).val('');
        $("#contractor_id").val('');
      }
    }
    else{
      var selected_contractor=ui.item;
      $("#contractor_id").val(selected_contractor.id);

    }

  }
}

$("#contractor").autocomplete(contractor_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});



$("#opening_date").change(function () {
  $("#selected_plan_titles").html('');
  $("#plan_id").val('');
  $("#plan_title").val('');
  $("#contractor").val('');
  $("#contractor_id").val('');
});



$("#year").change(function () {
  $("#filter_motion_for_reconsideration").submit();
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

  else if("{{session('message')}}"=="update_delete_error"){
    swal.fire({
      title: `Update/Delete Error`,
      text: 'Sorry! you cannot update or delete motion for reconsideration with Resolution',
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
    $("#motion_for_reconsideration").modal('hide');
  }

  else if("{{session('message')}}"=="delete_success"){
    Swal.fire({
      title: `Delete Success`,
      text: 'Successfully deleted Motion for Reconsideration',
      confirmButtonText: 'Ok',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
    $("#motion_for_reconsideration").modal('hide');
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
let data = @json($motion_for_reconsiderations);
if(@json(old('year'))!=null){
  data = @json(session('motion_for_reconsiderations'));
}
else{
  $("#year").val(@json($year));
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
        $("#filter_motion_for_reconsideration").submit();
      }
    },
    @if(in_array('add',$user_privilege))
    {
      text: 'Add MR',
      className: 'btn btn-sm shadow-0 border-0 bg-danger text-white',
      action: function ( e, dt, node, config ) {
        $("#existing_attachments").html('');
        $("#selected_plan_titles").html('');
        table.rows().deselect();
        $("#mr_id").val('');
        $("#opening_date").val('');
        $("#date_received").val('');
        $("#plan_title").val('');
        $("#plan_id").val('');
        $("#contractor_id").val('');
        $("#contractor").val('');
        $("#status").val('pending');
        $("#remarks").val('');
        $("#attachment").val('');
        $("#motion_for_reconsideration_title").html("Add Motion for Reconsideration ");
        $("#motion_for_reconsideration").modal('show');
      }
    },
    @endif
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
      var  mr_id=row.mr_id;
      return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button class="btn btn-sm btn btn-sm btn-success edit-btn btn-smmr-0" data-toggle="tooltip" data-placement="top" title="Edit" id="edit-btn-'+mr_id+'"> <i class="ni ni-ruler-pencil"></i></button>@endif @if(in_array("delete",$user_privilege)) <button class="btn btn-sm btn btn-sm btn-danger delete-btn btn-smmr-0" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button>@endif <a  class="btn btn-sm btn btn-sm shadow-0 border-0 btn-primary text-white" target="_blank" data-toggle="tooltip" data-placement="top" title="View File"  href="/view_motion_for_reconsideration/'+mr_id+'"><i class="ni ni-tv-2"></i></a></div>';
    }
  },
  { "data": "mr_id" },
  { "data": "plan_id" },
  { "data": "project_title" },
  { "data": "open_bid" },
  { "data": "mr_date_received" },
  { "data": "mr_due_date" },
  { "data": "resolution_due_date" },
  { "data": "contractor_id" },
  { "data": "business_name" },
  { "data": "mr_status" },
  { "data": "mr_type" },
  { "data": "mr_remarks" }
],
order: [[ 1, "desc" ]]
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

@if(in_array("update",$user_privilege))
$('#app_table tbody').on('click', '.edit-btn', function (e) {
  table.rows().deselect();
  var row=table.row($(this).parents('tr')).data();
  table.rows().deselect();
  table.column(1).search(row.mr_id).draw();
  $("#mr_id").val(row.mr_id);
  $("#opening_date").datepicker('setDate',moment(row.open_bid).format('MM/DD/YYYY'));
  $("#date_received").datepicker('setDate',moment(row.mr_date_received).format('MM/DD/YYYY'));
  var selected_plan_ids="";
  var mr_items=table.rows({filter: 'applied'}).data().toArray();
  mr_items.forEach((item, i) => {
    var project_title="<li plan_id='"+item.plan_id+"'><button type='button' class='btn btn-sm btn btn-sm btn-danger mr-2 remove_plan_title'><i class='ni ni-basket'></i></button>"+item.project_title+"</li>";
    if(i==(mr_items.length-1)){
      selected_plan_ids=selected_plan_ids+item.plan_id;
    }
    else{
      selected_plan_ids=selected_plan_ids+item.plan_id+",";
    }
    $("#selected_plan_titles").html($("#selected_plan_titles").html()+project_title);
    $(".remove_plan_title").click(function functionName() {
      $(this).parent().remove();
      var plan_ids=$("#plan_id").val();
      var plan_ids_text="";
      $("#selected_plan_titles li").each(function (index){
        if(plan_ids_text==""){
          plan_ids_text=$(this).attr('plan_id');
        }
        else{
          plan_ids_text=plan_ids_text+","+$(this).attr('plan_id');
        }
      });
      $("#plan_id").val(plan_ids_text);
    });
  });
  $.ajax({
    'url': "{{route('get_mr_attachments')}}",
    'data': {
      "_token": "{{ csrf_token() }}",
      "mr_id" : row.mr_id,
    },
    'method': "post",
    'success': function(data) {
      if(data.length>0){
        $("#existing_attachments").html('');
        data.forEach((item, i) => {
          let existing_attachment='<div class="row existing_attachment_group">';
          existing_attachment=existing_attachment+'<div class="col-md-11">';
          existing_attachment=existing_attachment+'<div class="form-control attachment">';
          existing_attachment=existing_attachment+'<a href="view_mr_attachment/'+item.mr_attachment_id+'" target="_blank"> Attachment '+(i+1)+'</a>';
          existing_attachment=existing_attachment+'</div> </div> <div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_existing_attachment" attachment_id="'+item.mr_attachment_id+'"><i class="ni ni-fat-remove"></i></button></div> </div>';
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
                'url': "{{route('delete_mr_attachment')}}",
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

  $("#plan_id").val(selected_plan_ids);
  $("#contractor_id").val(row.contractor_id);
  $("#mr_due_date").val(row.mr_due_date);
  $("#mr_due_date").trigger('change');
  $("#contractor").val(row.business_name);
  $("#status").val(row.mr_status);
  $("#remarks").val(row.mr_remarks);
  $("#mr_type").val(row.mr_type);
  $("#attachment").val('');
  $("#motion_for_reconsideration_title").html("Edit Motion for Reconsideration ");
  $("#motion_for_reconsideration").modal('show');
  table.column(1).search('').draw();
});
@endif

@if(in_array("delete",$user_privilege))
$('#app_table tbody').on('click', '.delete-btn', function (e) {
  var row=table.row($(this).parents('tr')).data();
  Swal.fire({
    text: 'Are you sure to delete this Motion for Reconsideration?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_motion_for_reconsideration/"+row.mr_id;
    }
  });

});
@endif
var oldInputs='{{ count(session()->getOldInput()) }}';

if (oldInputs>=5) {
  if("{{old('mr_id')}}"){
    $("#motion_for_reconsideration_title").html("Edit Motion for Reconsideration ");
    $("#edit-btn-"+"{{old('mr_id')}}").trigger('click');
    $("#motion_for_reconsideration").modal('show');
  }
  else{
    $("#motion_for_reconsideration_title").html("Add Motion for Reconsideration ");
    $("#motion_for_reconsideration").modal('show');
  }
  $("#contractor_id").val('{{old("contractor_id")}}');
  $("#contractor").val('{{old("contractor")}}');
}



$("input").change(function functionName() {
  $(this).siblings('.error-msg').html("");
});

$(".custom-radio").change(function functionName() {
  $(this).parent().siblings('.error-msg').html("");
});

$("select").change(function functionName() {
  $(this).siblings('.error-msg').html("");
});

$("#add_more_attachment").click(function functionName() {
  $("#attachment_div .row").last().after('<div class="row attachment_group"><div class="col-md-11"><input  type="file"  name="attachments[]" accept="application/pdf" class="form-control attachment"></div><div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_attachment"><i class="ni ni-fat-remove"></i></button></div></div>');
  $(".remove_attachment").click(function functionName() {
    $(this).parents('.attachment_group').remove();
  });
});



</script>
@endpush
