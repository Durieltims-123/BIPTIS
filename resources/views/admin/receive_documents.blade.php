@extends('layouts.app')
<style>
ul.ui-autocomplete {
  z-index: 1100;
}
.unwrap{
  white-space: nowrap !important
}

.cell {
  max-width: 50px; /* tweak me please */
  white-space : nowrap;
  overflow : hidden;
}

.expand-small-on-hover:hover {
  max-width : 200px;
  text-overflow: ellipsis;
}

.expand-maximum-on-hover:hover {
  max-width : initial;
}

</style>
@section('content')
  @include('layouts.headers.cards2')
  <div class="container-fluid mt-1">

    <div id="app">
      <div class="col-sm-12">
        <div class="modal" tabindex="-1" role="dialog" id="additional_docs_modal">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="modal-title" id="additional_docs_modal_title">Checklist</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form class="col-sm-12" method="POST" id="release_documents_form" action="{{route('submit_receive_documents')}}">
                  @csrf
                  <div class="row d-flex">

                    <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                      <label for="pbard_id">Additional Doc ID</label>
                      <input type="text" id="pbard_id" name="pbard_id" class="form-control form-control-sm" value="{{old('pbard_id')}}" >
                      <label class="error-msg text-red" >@error('pbard_id'){{$message}}@enderror
                      </label>
                    </div>

                    <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                      <label for="project_bid_id">Project bid</label>
                      <input type="text" id="project_bid_id" name="project_bid_id" class="form-control form-control-sm" value="{{old('project_bid_id')}}" >
                      <label class="error-msg text-red" >@error('project_bid_id'){{$message}}@enderror
                      </label>
                    </div>

                    <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                      <label for="mode">Mode</label>
                      <input type="text" id="mode" name="mode" class="form-control form-control-sm" value="{{old('mode')}}" >
                      <label class="error-msg text-red" >@error('mode'){{$message}}@enderror
                      </label>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                      <label for="plan_title">Project Title</label>
                      <input list="titles" type="text" id="plan_title" name="plan_title" class="form-control form-control-sm" value="{{old('plan_title')}}" readonly>
                      <label class="error-msg text-red" >@error('plan_title'){{$message}}@enderror
                      </label>
                    </div>

                    <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                      <label for="opening_date">Opening Date</label>
                      <input  type="text" id="opening_date" name="opening_date" class="form-control form-control-sm" value="{{old('opening_date')}}" readonly>
                      <label class="error-msg text-red" >@error('opening_date'){{$message}}@enderror
                      </label>
                    </div>

                    <!-- Contractor  -->
                    <div class="form-group col-xs-6 col-sm-6 col-lg-6 ">
                      <label for="contractor">Name of Firm</label>
                      <input  readonlytype="text" id="contractor" name="contractor" class="form-control form-control-sm" value="{{old('contractor')}}" readonly >
                      <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror
                      </label>
                    </div>

                    <!-- Date Released -->
                    <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                      <label for="date_released">Date Released *</label>
                      <input  type="text" id="date_released" name="date_released" class="form-control form-control-sm datepicker" value="{{old('date_released')}}" readonly >
                      <label class="error-msg text-red" >@error('date_released'){{$message}}@enderror
                      </label>
                    </div>

                    <!-- Date Received -->
                    <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                      <label for="date_received">Date Received <span class="text-red">*</span></label>
                      <input  type="text" id="date_received" name="date_received" class="form-control form-control-sm datepicker" value="{{old('date_received')}}" >
                      <label class="error-msg text-red" >@error('date_received'){{$message}}@enderror
                      </label>
                    </div>

                    <!-- Days -->
                    <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                      <label for="duration">Duration (Days)</label>
                      <input  type="text" id="duration" name="duration" class="form-control form-control-sm" value="{{old('duration')}}" readonly >
                      <label class="error-msg text-red" >@error('duration'){{$message}}@enderror
                      </label>
                    </div>


                    <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                      <label for="status">Status</label>
                      <select type="" id="status" name="status" class="form-control form-control-sm" readonly>
                        <option value="DNS"  {{ old('status') == 'DNS' ? 'selected' : ''}} >Did Not Submit</option>
                        <option value="Ontime"  {{ old('status') == 'Ontime' ? 'selected' : ''}} >Ontime</option>
                        <option value="Late"  {{ old('status') == 'Late' ? 'selected' : ''}} >Late</option>
                      </select>
                      <label class="error-msg text-red" >@error('status'){{$message}}@enderror
                      </label>
                    </div>


                    <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                      <label for="remarks">Remarks</label>
                      <input  type="text" id="remarks" name="remarks" class="form-control form-control-sm " value="{{old('remarks')}}" >
                      <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror
                      </label>
                    </div>



                    <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                      <label for="present_documents">Present Documents</label>
                      <input type="text" id="present_documents" name="present_documents" class="form-control form-control-sm" value="{{old('present_documents')}}" >
                      <label class="error-msg text-red" >@error('present_documents'){{$message}}@enderror
                      </label>
                    </div>

                    <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                      <label for="missing_documents">Missing Documents</label>
                      <input type="text" id="missing_documents" name="missing_documents" class="form-control form-control-sm" value="{{old('missing_documents')}}" >
                      <label class="error-msg text-red" >@error('missing_documents'){{$message}}@enderror
                      </label>
                    </div>

                    <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                      <label for="na_documents">Not Applicable</label>
                      <input type="text" id="na_documents" name="na_documents" class="form-control form-control-sm" value="{{old('na_documents')}}" >
                      <label class="error-msg text-red" >@error('na_documents'){{$message}}@enderror
                      </label>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-lg-12" id=svp_checklist>
                      <table class="table" id="svp_checklist_table">
                        <thead>
                          <tr>
                            <th></th>
                            <th class="p-2">Document</th>
                            <th class="p-2">Present</th>
                            <th class="p-2">Missing</th>
                            <th class="p-2">N/A</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-lg-12" id=bidding_checklist>
                      <table class="table" id="bidding_checklist_table">
                        <thead>
                          <tr>
                            <th></th>
                            <th class="p-2">Document</th>
                            <th class="p-2">Present</th>
                            <th class="p-2">Missing</th>
                            <th class="p-2">N/A</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
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
              <form class="row" id="filter_project_bidder_additional_docs" method="post" action="{{route('filter_receive_project_bidder_additional_docs')}}">
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
                    <th class="text-center text-nowrap"></th>
                    <th class="text-center">ID</th>
                    <th class="text-center">project_bid_id</th>
                    <th class="text-center">Cluster</th>
                    <th class="text-center">Opening Date</th>
                    <th class="text-center">Business Name</th>
                    <th class="text-center">Date Released</th>
                    <th class="text-center">Date Received</th>
                    <th class="text-center">Project No.</th>
                    <th class="text-center" style="width:150px">Project Title</th>
                    <th class="text-center">procurement mode</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Present</th>
                    <th class="text-center">Missing</th>
                    <th class="text-center">Not Applicable</th>
                    <th class="text-center">Project Cost</th>
                    <th class="text-center">Status</th>
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


  let data = @json($project_plans);
  if(@json(old('project_year'))!=null){
    data = @json(session('project_plans'));
  }
  else{
    $("#project_year").val(@json($year));
  }


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
        text: 'Successfully saved to database',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-success',
        icon: 'success'
      });
      $("#additional_docs_modal").modal('hide');
    }

    else if("{{session('message')}}"=="multiple_modes"){
      swal.fire({
        title: `Error`,
        text: 'Sorry you cannot mix up SVP,Negotiated,Bidding in one Resolution',
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-sm btn-danger',
        icon: 'warning'
      });
    }

    else if("{{session('message')}}"=="delete_error"){
      swal.fire({
        title: `Error`,
        text: 'You cannot delete RFQ for Received RFQ',
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
          $("#filter_project_bidder_additional_docs").submit();
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
    dataType: 'json',
    columns: [
      { data:"pbard_id",
      render: function ( data, type, row ) {

        if(row.date_received==null){
          return "@if(in_array('receive',$user_privilege))<button class='btn btn-sm btn btn-sm btn-primary edit-btn' data-toggle='tooltip' data-placement='top' title='Receive'><i class='ni ni-collection'></i></button>@endif";
        }
        else{
          return "@if(in_array('update',$user_privilege))<button class='btn btn-sm btn btn-sm btn-success edit-btn' data-toggle='tooltip' data-placement='top' title='Edit'><i class='ni ni-ruler-pencil'></i></button>@endif";
        }
      }
    },
    { "data": "pbard_id" },
    { "data": "project_bid" },
    { "data": "plan_cluster_id" },
    { "data": "open_bid" },
    { "data": "business_name" },
    { "data": "date_released" },
    { "data": "date_received" },
    { "data": "project_no" },
    { "data": "project_title" },
    { "data": "mode" },
    { "data": "additional_docs_status" },
    { "data": "present_docs" },
    { "data": "missing_docs" },
    { "data": "na_docs" },
    { "data": "project_cost" },
    { "data": "pbard_status" },
    { "data": "pbard_remarks" }
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
  columnDefs: [
    {
      targets: [11,12,13],
      visible:false,
    },
    { className: "unwrap", targets: [0] },
    // { className: "wrap cell expand-small-on-hover", targets: [ 11,12,13 ] },
    {targets: 0,orderable: false},
    {targets: [1,2],visible: false}
  ],
  order: [[ 1, "desc" ],[3 , "desc"],[7 , "asc"]],
  rowGroup: {
    startRender: function ( rows, group ) {
      if(group=="No group"||group==null){
        var group_title="Non-Clustered Project";
      }
      else{
        var group_title="Cluster "+group;
      }
      return group_title;
    },
    endRender: null,
    dataSrc:"plan_cluster_id"
  }

});



$("#svp_checklist_table").DataTable({
  dom:"",
  data:{!!json_encode($svp_requirements)!!},
  columns:[
    {data:"id"},
    {data:"document_type"},
    { data:"id",render: function ( data, type, row ) {
      return '<div class="custom-control custom-checkbox ml-3">'+
      '<input type="checkbox" class="custom-control-input" id="svp'+data+'present" checked disabled>'+
      '<label class="custom-control-label" for="svp'+data+'present"></label>'+
      '</div>';
    }},
    { data:"id",render: function ( data, type, row ) {
      return '<div class="custom-control custom-checkbox ml-3">'+
      '<input type="checkbox" class="custom-control-input" id="svp'+data+'missing" disabled>'+
      '<label class="custom-control-label" for="svp'+data+'missing"></label>'+
      '</div>';
    }},
    { data:"id",render: function ( data, type, row ) {
      return '<div class="custom-control custom-checkbox ml-3">'+
      '<input type="checkbox" class="custom-control-input" id="svp'+data+'na" disabled>'+
      '<label class="custom-control-label" for="svp'+data+'na"></label>'+
      '</div>';
    }},
  ],
  columnDefs: [
    {
      targets: 0,
      visible: false
    },
    {
      targets: [0,1,2,3,4],
      orderable: false
    }]
  });

  $("#bidding_checklist_table").DataTable({
    dom:"",
    data:{!!json_encode($bidding_requirements)!!},
    columns:[
      {data:"id"},
      {data:"document_type"},
      { data:"id",render: function ( data, type, row ) {
        return '<div class="custom-control custom-checkbox ml-3">'+
        '<input type="checkbox" class="custom-control-input" id="bidding'+data+'present" checked disabled>'+
        '<label class="custom-control-label" for="bidding'+data+'present"></label>'+
        '</div>';
      }},
      { data:"id",render: function ( data, type, row ) {
        return '<div class="custom-control custom-checkbox ml-3">'+
        '<input type="checkbox" class="custom-control-input" id="bidding'+data+'missing" disabled>'+
        '<label class="custom-control-label" for="bidding'+data+'missing"></label>'+
        '</div>';
      }},
      { data:"id",render: function ( data, type, row ) {
        return '<div class="custom-control custom-checkbox ml-3">'+
        '<input type="checkbox" class="custom-control-input" id="svp'+data+'na" disabled>'+
        '<label class="custom-control-label" for="svp'+data+'na"></label>'+
        '</div>';
      }}
    ],
    columnDefs: [
      {
        targets: 0,
        visible: false
      },
      {
        targets: [0,1,2,3,4],
        orderable: false
      }]
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

        } );
      }
    });

    function checkPresentMissingNADocs(mode) {
      var present=$("#present_documents").val();
      var missing=$("#missing_documents").val();
      var na=$("#na_documents").val();
      $("#present_documents").val();
      $("#missing_documents").val();
      $("#na_documents").val();

      if(present==""||present==null){
        present=[];
      }
      else{
        present=present.split(',');
      }
      if(missing==""||missing==null){
        missing=[];
      }
      else{
        missing=missing.split(',');
      }
      if(na==""||na==null){
        na=[];
      }
      else{
        na=na.split(',');
      }

      if(mode=="Bidding"){
        var source=$("#bidding_checklist_table tbody tr");
      }
      else{
        var source=$("#svp_checklist_table tbody tr");
      }

      source.each(function functionName(index,value) {
        var document=$($(source[index]).find("td")[0]).html();
        var row=$(source[index]).find("td .custom-checkbox .custom-control-input");
        $(row[0]).prop('checked',false)
        $(row[1]).prop('checked',false)
        $(row[2]).prop('checked',false)
        if(present.includes(document)){
          $(row[0]).prop('checked',true);
        }
        else if(missing.includes(document)){
          $(row[1]).prop('checked',true);
        }
        else{
          $(row[2]).prop('checked',true);
        }
      });
    }

    $("#svp_checklist_table").removeAttr('style');
    $("#bidding_checklist_table").removeAttr('style');

    function getPresentMissingNADocs(mode) {
      var present=[];
      var missing=[];
      var na=[];
      if(mode=="Bidding"){
        var source=$("#bidding_checklist_table tbody tr");
      }
      else{
        var source=$("#svp_checklist_table tbody tr");
      }

      source.each(function functionName(index,value) {
        var document=$($(source[index]).find("td")[0]).html();
        var row=$(source[index]).find("td .custom-checkbox .custom-control-input");
        if($(row[0]).prop('checked')==true){
          present.push(document);
        }
        else if($(row[1]).prop('checked')==true){
          missing.push(document);
        }
        else{
          na.push(document);
        }

      });
      $("#present_documents").val(present.toString());
      $("#missing_documents").val(missing.toString());
      $("#na_documents").val(na.toString());

    }

    let errors=@json($errors->first('date_received'));
    if(errors.length!=""){
      var mode= $("#mode").val();
      checkPresentMissingNADocs(mode);
      if(mode=="Bidding"){
        $("#svp_checklist").removeClass("d-none");
        $("#svp_checklist").addClass("d-none");
        $("#bidding_checklist").removeClass("d-none");
      }
      else{
        $("#bidding_checklist").removeClass("d-none");
        $("#bidding_checklist").addClass("d-none");
        $("#svp_checklist").removeClass("d-none");
      }
      $("#additional_docs_modal").modal('show');
    }



    $('#app_table tbody').on('click', '.add-btn', function (e) {
      table.rows().deselect();
      var data=table.row($(this).parents('tr')).data();
      $("#release_documents_form")[0].reset();
      $("#pbard_id").val(data.pbard_id);
      $("#project_bid_id").val(data.project_bid);
      $("#opening_date").val(moment(data.open_bid,"YYYY-MM-DD").format("LL"));
      $("#plan_title").val(data.project_title);
      $("#contractor").val(data.business_name);
      $("#mode").val(data.mode);
      $("#additional_docs_modal_title").html("Additional Documents Checklist");
      if(data.mode=="Bidding"){
        $("#svp_checklist").removeClass("d-none");
        $("#svp_checklist").addClass("d-none");
        $("#bidding_checklist").removeClass("d-none");
      }
      else{
        $("#bidding_checklist").removeClass("d-none");
        $("#bidding_checklist").addClass("d-none");
        $("#svp_checklist").removeClass("d-none");
      }
      getPresentMissingNADocs(data.mode);
      $("#additional_docs_modal").modal('show');
    });
    @if(in_array('update',$user_privilege)||in_array('receive',$user_privilege))
    $('#app_table tbody').on('click', '.edit-btn', function (e) {
      table.rows().deselect();
      var data=table.row($(this).parents('tr')).data();
      $("#release_documents_form")[0].reset();
      $("#pbard_id").val(data.pbard_id);
      $("#project_bid_id").val(data.project_bid);
      $("#opening_date").val(moment(data.open_bid,"YYYY-MM-DD").format("LL"));
      $("#plan_title").val(data.project_title);
      $("#contractor").val(data.business_name);
      $("#present_documents").val(data.present_docs);
      $("#missing_documents").val(data.missing_docs);
      $("#na_documents").val(data.na_docs);
      if(data.date_released!=null){
        $("#date_released").val(moment(data.date_released,"YYYY-MM-DD").format("MM/DD/YYYY"));
      }
      else{
        $("#date_released").val("");
      }


      if(data.date_received!=null){
        $("#date_received").datepicker('update',moment(data.date_received,"YYYY-MM-DD").format("MM/DD/YYYY"));
      }
      else{
        $("#date_received").val("");
      }

      if(data.pbard_status!=null){
        $("#status").val(data.pbard_status);
      }
      else{
        $("#status").val("DNS");
      }

      if(data.pbard_remarks!=null){
        $("#remarks").val(data.pbard_remarks);
      }
      else{
        $("#remarks").val("");
      }

      $("#mode").val(data.mode);
      $("#additional_docs_modal_title").html("Receive  Documents");
      if(data.mode=="Bidding"){

        $("#svp_checklist").removeClass("d-none");
        $("#svp_checklist").addClass("d-none");
        $("#bidding_checklist").removeClass("d-none");
      }
      else{
        $("#bidding_checklist").removeClass("d-none");
        $("#bidding_checklist").addClass("d-none");
        $("#svp_checklist").removeClass("d-none");
      }
      checkPresentMissingNADocs(data.mode);
      $("#additional_docs_modal").modal('show');
    });
    @endif
    
    $("#svp_checklist_table").removeAttr('style');
    $("#bidding_checklist_table").removeAttr('style');

    $("input").change(function functionName() {
      $(this).siblings('.error-msg').html("");
    });

    $(".custom-radio").change(function functionName() {
      $(this).parent().siblings('.error-msg').html("");
    });

    $("select").change(function functionName() {
      $(this).siblings('.error-msg').html("");
    });

    $("#date_received").change(function functionName() {
      var date_received=new Date($(this).val());
      var date_released=new Date($("#date_released").val());
      if($(this).val()==null||$(this).val()==""){
        $("#status").val('DNS');
        $("#duration").val('');
      }
      else if(date_received<date_released){
        $(this).val('');
        $("#status").val('DNS');
        $("#duration").val('');
      }
      else{
        var diff = new moment.duration(date_received-date_released);
        if(diff.asDays()<0){
          $(this).val('');
        }
        else{
          var mode=$("#mode").val();
          if(mode="Bidding"){
            if(diff.asDays()>5){
              $("#status").val('Late');
            }
            else{
              $("#status").val('Ontime');
            }
          }
          else{
            if(diff.asDays()>3){
              $("#status").val('Late');
            }
            else{
              $("#status").val('Ontime');
            }
          }
        }

        $("#duration").val(diff.asDays());
      }

    });

    $("#project_year").change(function () {
      $("#filter_project_bidder_additional_docs").submit();
    })

    $('#status').css('pointer-events','none');
    $('#date_released').css('pointer-events','none');


    </script>
  @endpush
