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

    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Generate File</h2>
        </div>
        <div class="card-body">
          <form class="col-sm-12" method="POST" id="release_form" action="/submit_generate_file">
            @csrf
            <div class="row d-flex">

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                <label for="rfq_id">RFQ ID</label>
                <input type="text" id="rfq_id" name="rfq_id" class="form-control form-control-sm" value="{{old('rfq_id')}}" >
                <label class="error-msg text-red" >@error('rfq_id'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                <label for="plan_id">Plan Id/s</label>
                <input type="text" id="plan_id" name="plan_id" class="form-control form-control-sm" value="{{old('plan_id')}}" >
                <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
              </div>

              <!-- project_title -->
              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="plan_title">Project Title</label>
                <input list="titles" type="text" id="plan_title" name="plan_title" class="form-control form-control-sm" value="{{old('plan_title')}}" >
                <label class="error-msg text-red" >@error('plan_title'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="project_number">Project Number</label>
                <input type="text" id="project_number" name="project_number" class="form-control form-control-sm" value="{{old('project_number')}}" readonly>
                <label class="error-msg text-red" >@error('project_number'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="project_type">Project Type</label>
                <input type="text" id="project_type" name="project_type" class="form-control form-control-sm" value="{{old('project_type')}}" readonly>
                <label class="error-msg text-red" >@error('project_type'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="document_type">Document Type</label>
                <select type="text" id="document_type" name="document_type" class="form-control form-control-sm" value="{{old('document_type')}}">
                  <option value="">Select a Document Type</option>
                  <option value="notice_of_award">Notice of Award</option>
                  <option value="contract">Contract</option>
                  <option value="notice_to_proceed">Notice To Proceed</option>
                  <select>
                    <label class="error-msg text-red" >@error('document_type'){{$message}}@enderror</label>
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

    @endsection

    @push('custom-scripts')
    <script>
    var project_title_autocomplete_init = {
      minLength: 0,
      autocomplete: true,
      source: function(request, response){

        $.ajax({
          'url': '/autocomplete_project',
          'data': {
            "_token": "{{ csrf_token() }}",
            "term" : request.term,
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

        if (ui.item == null || ui.item=="") {
          if("{{old('plan_id')}}"!=''){
            $(this).val("{{old('plan_title')}}");
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

    $("#plan_title").autocomplete(project_title_autocomplete_init).focus(function() {
      $(this).autocomplete('search', $(this).val())
    });

  </script>
  @endpush
