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
          <div class="col-sm-12 d-none" id="filter">
            <form class="row" id="app_filter" method="post" action="{{route('filter_app')}}">
              @csrf

              <!-- project year -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                <label for="project_year" class="input-sm">Project Year </label>
                <input  class="form-control form-control-sm yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}" >
                <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror</label>
              </div>

              <!-- Month added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="month_added">Month Added </label>
                <input type="text" id="month_added" name="month_added" class="form-control form-control-sm monthpicker" value="{{old('month_added')}}" >
                <label class="error-msg text-red" >@error('month_added'){{$message}}@enderror</label>
              </div>

              <!-- date added -->
              <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                <label for="date_added">Date Added </label>
                <input type="text" id="date_added" name="date_added" class="form-control form-control-sm datepicker" value="{{old('date_added')}}" >
                <label class="error-msg text-red" >@error('date_added'){{$message}}@enderror</label>
              </div>

            </form>
          </div>

          <div class="row">
            <div class="table-responsive col-sm-6">
              <table class="table table-bordered" id="documents_table">
                <h2>Documents</h2>
                <thead class="">
                  <tr class="bg-primary text-white" >
                    <th class="text-center">Project Document ID</th>
                    <th class="text-center">Document Name</th>
                    <th class="text-center"></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach( $project_documents as $project_document)
                  <tr>
                    <td>{{$project_document->id}}</td>
                    <td>{{$project_document->document_type}}</td>
                    <td>@if(in_array("add",$user_privilege))<button class="btn btn-sm btn-primary add_btn" >Add</button>@endif</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

            </div>

            <div class="table-responsive col-sm-6">
              <h2>Additional Documents</h2>
              <table class="table table-bordered" id="additional_documents_table">
                <thead class="">
                  <tr class="bg-primary text-white" >
                    <th class="text-center">Sequence</th>
                    <th class="text-center">ID</th>
                    <th class="text-center">Document Name</th>
                    <th class="text-center"></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach( $additional_documents as $additional_document)
                  <tr>

                    <td>{{$additional_document->sequence}}</td>
                    <td>{{$additional_document->id}}</td>
                    <td>{{$additional_document->document_type}}</td>
                    <td>@if(in_array("delete",$user_privilege))<button class="btn btn-sm btn-danger remove_btn" >Remove</button>@endif</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

            </div>

          </div>


          <form class="col-sm-12 d-flex " method="POST" id="submit_additional_documents" action="{{route('submit_additional_documents')}}">
            @csrf
            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
              <label for="document_type_ids" class="input-sm">Document Types</label>
              <input  class="form-control form-control-sm" id="document_type_ids" name="document_type_ids"   value="{{old('document_type_ids')}}" >
              <label class="error-msg text-red" >@error('document_type_ids'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
              <label for="project_type" class="input-sm">Project Type</label>
              <input  class="form-control form-control-sm" id="project_type" name="project_type"   value="{{$project_type}}" >
              <label class="error-msg text-red" >@error('project_type'){{$message}}@enderror</label>
            </div>
            @if(in_array("update",$user_privilege)||in_array("add",$user_privilege))
            <button class="btn btn-sm btn-primary mx-auto mt-3" id="save_changes" type="submit">Save Changes</button>
            @endif
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('custom-scripts')
<script>

var table1=  $('#documents_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  orderCellsTop: true,
  responsive:true,
  columnDefs: [
    {
      targets:[0],
      visible:false
    }
  ]
});

var table2=  $('#additional_documents_table').DataTable({
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
      targets:[1],
      visible:false
    }
  ],
  rowReorder: true,
});

if("{{session('message')}}"){
  if("{{session('message')}}"=="no_documents_selected"){
    swal.fire({
      title: 'Document Error',
      text: 'Please select additional documents',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: 'Success',
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else{

  }
}



// initialize row order to document_type_ids
var document_type_id = $.map(table2.column(1).data(), function(value, index){
  return [value];
});
$("#document_type_ids").val(document_type_id.toString());

table2.on( 'row-reorder', function ( e, diff, edit ) {

  table2.on( 'draw', function () {
    var document_type_id = $.map(table2.column(1).data(), function(value, index){
      return [value];
    });
    $("#document_type_ids").val(document_type_id.toString());
  });

});

// hide/show Filter
$("#show_filter").click(function() {
  if($(this).html()=="Show Filter"){
    $('#filter').removeClass('d-none');
    $('#filter_btn').removeClass('d-none');
    $("#show_filter").html("Hide Filter");
  }
  else{
    $('#filter').addClass('d-none');
    $('#filter_btn').addClass('d-none');
    $("#show_filter").html("Show Filter");
  }
});

$('#documents_table tbody').on('click', '.add_btn', function (e) {
  var data = table1.row( $(this).parents('tr') ).data();
  table2.row.add( [
    table2.rows().count()+1,
    data[0],
    data[1],
    '<button class="btn btn-sm btn-danger remove_btn" >Remove</button></td>'
  ] ).draw( true );
  table1.row( $(this).parents('tr') ).remove().draw();

  var document_type_id = $.map(table2.column(1).data(), function(value, index){
    return [value];
  });
  $("#document_type_ids").val(document_type_id.toString());


});

$('#additional_documents_table tbody').on('click', '.remove_btn', function (e) {
  table2.rows().deselect();
  var data = table2.row( $(this).parents('tr') ).data();
  table1.row.add( [
    data[1],
    data[2],
    '<button class="btn btn-sm btn-primary add_btn" >Add</button>'
  ] ).draw( true );
  table2.row( $(this).parents('tr') ).remove().draw();

  var document_type_id = $.map(table2.column(1).data(), function(value, index){
    return [value];
  });
  $("#document_type_ids").val(document_type_id.toString());

});

</script>
@endpush
