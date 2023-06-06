@extends('layouts.app')
@section('content')
  @include('layouts.headers.cards2')
  <div class="container-fluid mt-1">
    <div id="app">
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
              action="{{route('archive.submit_invitation_to_bid')}}" enctype="multipart/form-data">
              @csrf
              <div class="row d-flex">

                <!--ID -->
                <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                  <label for="id">ID <span class="text-red">*</span></label>
                  <input type="text" id="id" name="id" class="form-control form-control-sm" readonly value="{{old('id')}}">
                  <label class="error-msg text-red">@error('id'){{$message}}@enderror
                  </label>
                </div>

                <!--ID -->
                <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                  <label for="edit-id">Edit ID <span class="text-red">*</span></label>
                  <input type="text" id="edit-id" name="edit-id" class="form-control form-control-sm" readonly value="{{old('edit-id')}}">
                  <label class="error-msg text-red">@error('edit-id'){{$message}}@enderror
                  </label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-11">
                  <label for="project_title">Project Title<span class="text-red"></span></label>
                  <input type="text" id="project_title" name="project_title" class="form-control form-control-sm" readonly value="{{old('project_title')}}">
                  <label class="error-msg text-red">@error('project_title'){{$message}}@enderror
                  </label>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-11">
                  <label for="opening_date">Opening Date<span class="text-red"></span></label>
                  <input type="text" id="opening_date" name="opening_date" class="form-control form-control-sm" readonly value="{{old('opening_date')}}">
                  <label class="error-msg text-red">@error('opening_date'){{$message}}@enderror
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
            <form class="row" id="app_filter" method="post" action="{{route('archive.filter_invitation_to_bid')}}">
              @csrf

              <!-- APP TYPE -->
              <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                <label for="app_type">APP Type <span class="text-red">*</span></label>
                <input type="text" id="app_type" name="app_type" class="form-control" value="{{$project_type}}" >
                <label class="error-msg text-red" >@error('app_type'){{$message}}@enderror
                </label>
              </div>

              <!-- POW -->
              <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                <label for="pow">POW<span class="text-red">*</span></label>
                <input type="text" id="pow" name="pow" class="form-control" value="" >
                <label class="error-msg text-red" >@error('pow'){{$message}}@enderror
                </label>
              </div>



              <!-- project year -->
              <div class="form-group col-xs-12 col-sm-2 col-lg-2 mb-0">
                <label for="project_year" class="input-sm">Project Year </label>
                <input  class="form-control form-control-sm yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}" >
                <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror
                </label>
              </div>

              <!-- status -->
              <div class="form-group col-xs-12 col-sm-2 col-lg-2 mb-0">
                <label for="status">Status</label>
                <select type="text" id="status" name="status" class="form-control form-control-sm" >
                  <option {{ old('status') === "with_or_without_itb_attachment" ? "selected" : "" }} value="with_or_without_itb_attachment">All</option>
                  <option  {{ old('status') === "with_itb_attachment" ? "selected" : "" }} value="with_itb_attachment">With Attachment</option>
                  <option  {{ old('status') === "without_itb_attachment" ? "selected" : "" }} value="without_itb_attachment">Without Attachment</option>
                </select>

                <label class="error-msg text-red" >@error('status'){{$message}}@enderror
                </label>
              </div>
              <!-- Fund Category  -->
              <div class="form-group col-xs-12 col-sm-2 col-lg-2 mb-0">
                <label for="mode_of_procurement">Mode of Procurement</label>
                <select type="" id="mode_of_procurement" name="mode_of_procurement" class="form-control form-control-sm" value="{{old('mode_of_procurement')}}" >
                  <option value="1" >Bidding</option>
                </select>
                <label class="error-msg text-red" >@error('mode_of_procurement'){{$message}}@enderror
                </label>
              </div>


              <!-- Fund Category  -->
              <div class="form-group col-xs-12 col-sm-2 col-lg-2 mb-0">
                <label for="fund_category">Fund Category</label>
                <select type="" id="fund_category" name="fund_category" class="form-control form-control-sm" value="{{old('fund_category')}}" >
                  <option value=""  {{ old('fund_category') == '' ? 'selected' : ''}} >Select Fund Category</option>
                  @foreach($fund_categories as $category)
                    <option value="{{$category->fund_category_id}}"  {{ old('fund_category') == $category->fund_category_id ? 'selected' : ''}} >{{$category->title}}</option>
                  @endforeach
                </select>
                <label class="error-msg text-red" >@error('fund_category'){{$message}}@enderror
                </label>
              </div>

              <!-- Account account_classification -->
              <div class="form-group col-xs-12 col-sm-2 col-lg-2 mb-0">
                <label for="account_classification">Account Classication</label>
                <select type="" id="account_classification" name="account_classification" class="form-control form-control-sm" value="{{old('account_classification')}}" >
                  <option value=""  {{ old('account_classification') == '' ? 'selected' : ''}} >Select Account Classification</option>
                  @foreach($classifications as $classification)
                    <option value="{{$classification->account_id}}"  {{ old('account_classification') == $classification->account_id ? 'selected' : ''}} >{{$classification->classification}}</option>
                  @endforeach
                </select>
                <label class="error-msg text-red" >@error('account_classification'){{$message}}@enderror
                </label>
              </div>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered wrap" id="archive_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">ID</th>
                  <th class="text-center">APP/SAPP No.</th>
                  <th class="text-center">Project No.</th>
                  <th class="text-center">Project Title</th>
                  <th class="text-center">Location</th>
                  <th class="text-center">Posting Start</th>
                  <th class="text-center">Posting</th>
                  <th class="text-center">Opening of Bids</th>
                  <th class="text-center">Source of Fund</th>
                  <th class="text-center">Account Code</th>
                  <th class="text-center">Classification</th>
                  <th class="text-center">Approved Budget Cost</th>
                  <th class="text-center">Actual Project Cost</th>
                  <th class="text-center">Project Year</th>
                  <th class="text-center">Year Funded</th>
                  <th class="text-center">Rebid Count</th>
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

  // change filter account classification
  let account_classification="{{old('account_classification')}}";
  $("#account_classification").val(account_classification);

  // table data
  let data= @json(session('filtered_data'));

  if(data==null){
    data= @json($project_plans);
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
        text: 'Successfully deleted from database',
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
  $('#archive_table thead tr').clone(true).appendTo( '#archive_table thead' );
  $('#archive_table thead tr:eq(1)').removeClass('bg-primary');

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

  var table=  $('#archive_table').DataTable({
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
      { "data":"itb_attachment",
      render: function ( data, type, row ) {
        if(data=="0"){
          return "@if(in_array('add',$user_privilege))<div style='white-space: nowrap'><button  data-toggle='tooltip' data-placement='top' title='Add'  class='btn btn-sm shadow-0 border-0 btn-primary add-btn'><i class='ni ni-fat-add'></i></button></div> @endif";
        }
        else{
          return "<div style='white-space: nowrap'>@if(in_array('update',$user_privilege))<button  data-toggle='tooltip' data-placement='top' title='Edit' class='btn btn-sm shadow-0 border-0 btn-success edit-btn  '><i class='ni ni-ruler-pencil'></i></button> @endif @if(in_array('delete',$user_privilege)) <button data-toggle='tooltip' data-placement='top' title='Delete'  class='btn btn-sm shadow-0 border-0 btn-danger delete-btn  '><i class='ni ni-basket text-white'></i></button>@endif <a data-toggle='tooltip' data-placement='top' title='View' target='_blank' href='/archive/view_invitation_to_bid_attachments/"+row.procact_id+"' class='btn btn-sm shadow-0 border-0 btn-primary text-white'><i class='ni ni-tv-2'></i></a></div>";
        }
      }},
      {"data":"plan_id"},
      {"data":"app_group_no"},
      {"data":"project_no"},
      {"data":"project_title"},
      {"data":"municipality_name"},
      {"data":"advertisement_start"},
      {"data":"advertisement_end",  render: function ( data, type, row ) {
        return moment(row.advertisement_start).format('MMM DD, YYYY')+" - "+moment(data).format('MMM DD, YYYY');
      }},
      {"data":"bid_submission_start",  render: function ( data, type, row ) {
        return moment(data).format('MMM DD, YYYY');
      }},
      {"data":"source"},
      {"data":"account_code"},
      {"data":"classification"},
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
      targets: [1,6],
    }],
    order: [[ 6, "desc" ]],

  });

  // inputs
  var oldInputs='{{ count(session()->getOldInput()) }}';
  if(oldInputs==0){
    $("#project_year").datepicker("update","{{$year}}");
  }

  $("#project_year").change(function () {
    $("#app_filter").submit();
  });

  $("#mode_of_procurement").change(function () {
    $("#app_filter").submit();
  });

  $("#fund_category").change(function () {
    $("#app_filter").submit();
  });

  $("#account_classification").change(function () {
    $("#app_filter").submit();
  });

  $("#status").change(function () {
    $("#app_filter").submit();
  });

  // events

  $('#archive_table thead tr:eq(1) th').each( function (i) {
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


  // $("#project_year").change(function functionName() {
  //   $("#date_added").val("");
  //   $("#month_added").val("");
  // });

  // $("#date_added").change(function functionName() {
  //   $("#project_year").val("");
  //   $("#month_added").val("");
  // });
  //
  // $("#month_added").change(function functionName() {
  //   $("#project_year").val("");
  //   $("#date_added").val("");
  // });
@if(in_array('add',$user_privilege))
  $('#archive_table tbody').on('click', '.add-btn', function(e) {

    $(".error-msg").each(function () {
      $(this).html("");
    });
    var row = table.row($(this).parents('tr')).data();
    $("#archive_form")[0].reset();
    $("#archive_title").html("Add Attachment/s");
    $("#project_title").val(row.project_title);
    $("#opening_date").val(moment(row.bid_submission_start).format('MMMM DD, YYYY'));
    $('#id').val(row.procact_id);
    $('#edit-id').val('');
    $("#existing_attachments").html('');
    $("#attachment_div").find('.attachment_group').each(function(index) {
      if(index!=0){
        $(this).remove();
      }
    });
    $("#archive").modal('show');
  });
@endif

@if(in_array('update',$user_privilege))
  $('#archive_table tbody').on('click', '.edit-btn', function(e) {

    var row = table.row($(this).parents('tr')).data();
    $("#archive_form")[0].reset();
    $("#archive_title").html("Update Attachment/s");
    $("#opening_date").val(moment(row.bid_submission_start).format('MMMM DD, YYYY'));
    $('#id').val(row.procact_id);
    $("#project_title").val(row.project_title);
    $('#edit-id').val(row.procact_id);
    $("#existing_attachments").html('');
    $("#attachment_div").find('.attachment_group').each(function(index) {
      if(index!=0){
        $(this).remove();
      }
    });

    $.ajax({
      'url': "{{route('archive.get_invitation_to_bid_attachments')}}",
      'data': {
        "_token": "{{ csrf_token() }}",
        "procact_id" : row.procact_id,
      },
      'method': "post",
      'success': function(data) {
        if(data.length>0){
          data.forEach((item, i) => {
            let existing_attachment='<div class="row existing_attachment_group">';
            existing_attachment=existing_attachment+'<div class="col-md-11">';
            existing_attachment=existing_attachment+'<div class="form-control attachment">';
            existing_attachment=existing_attachment+'<a href="/archive/view_invitation_to_bid_attachment/'+item.id+'" target="_blank"> Attachment '+(i+1)+'</a>';
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
                  'url': "{{route('archive.delete_invitation_to_bid_attachment')}}",
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

@if(in_array('delete',$user_privilege))
  $('#archive_table tbody').on('click', '.delete-btn', function (e) {
    var row = table.row($(this).parents('tr')).data();
    let this_button=$(this);
    Swal.fire({
      title:'Delete Data',
      text: 'Are you sure to delete all attachments?',
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
          'url': "{{route('archive.delete_invitation_to_bid_attachments')}}",
          'data': {
            "_token": "{{ csrf_token() }}",
            "id" :row.procact_id,
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
