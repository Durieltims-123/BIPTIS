@extends('layouts.app')
<style>
ul.ui-autocomplete {
  z-index: 1100;
}
</style>
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="row ">

    <div class=" col-sm-8 shadow mx-auto ">
      <div class="card border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
          <form method="post" id="clear_form">
            @csrf
            <div class="col-sm-12 row mx-auto">


              <!-- Opening date -->
              <div class="form-group col-xs-3 col-sm-3 col-lg-3">
                <label for="opening_date">Opening Date<span class="text-red">*</span>
                </label>
                <input  type="text" id="opening_date" name="opening_date" class="form-control datepicker2" value="{{old('opening_date')}}" >
                <label class="error-msg text-red" >@error('opening_date'){{$message}}@enderror
                </label>
              </div>


            </div>
            <div class="col-sm-12 row mx-auto">

              <div class="form-group col-xs-3 col-sm-3 col-lg-3 d-none">
                <label for="plan_id">Plan ID</label>
                <input type="text" id="plan_id" name="plan_id" class="form-control " value="{{old('plan_id')}}" >
                <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-3 col-sm-3 col-lg-3 d-none">
                <label for="procact_id">Procact ID</label>
                <input type="text" id="procact_id" name="procact_id" class="form-control " value="{{old('procact_id')}}" >
                <label class="error-msg text-red" >@error('procact_id'){{$message}}@enderror</label>
              </div>


              <div class="form-group col-xs-3 col-sm-3 col-lg-3 d-none">
                <label for="type">Type</label>
                <input type="text" id="type" name="type" class="form-control " value="{{$type}}" >
                <label class="error-msg text-red" >@error('type'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-3 col-sm-3 col-lg-3">
                <label for="project_number">Project Number<span class="text-red">*</span></label>
                <input type="text" id="project_number" name="project_number" class="form-control " value="{{old('project_number')}}" readonly >
                <label class="error-msg text-red" >@error('project_number'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-9 col-sm-9 col-lg-9">
                <label for="project_title">Project Title<span class="text-red">*</span></label>
                <input type="text" id="project_title" name="project_title" class="form-control " value="{{old('project_title')}}" >
                <label class="error-msg text-red" >@error('project_title'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-3 col-sm-3 col-lg-3 d-none">
                <label for="bidder_id">Contractor ID</label>
                <input type="text" id="bidder_id" name="bidder_id" class="form-control " value="{{old('bidder_id')}}" >
                <label class="error-msg text-red" >@error('plan_id'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="contractor">Contractor<span class="text-red">*</span></label>
                <input type="text" id="contractor" name="contractor" class="form-control " value="{{old('contractor')}}" >
                <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="remarks">Remarks<span class="text-red">*</span></label>
                <input type="text" id="remarks" name="remarks" class="form-control " value="{{old('remarks')}}" >
                <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror</label>
              </div>


              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="password">Password<span class="text-red">*</span></label>
                <input type="password" id="password" name="password" class="form-control " value="{{old('password')}}" >
                <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror</label>
              </div>

              <div class="d-flex justify-content-center col-sm-12">
                <button  class="btn btn-primary text-center">Submit</button>
              </div>

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

let type =@json($type);
if(type=="TWG"){
  $("#clear_form").prop('action',"{{route('twg_clear_post_qualification_evaluation')}}");
}
else{
  $("#clear_form").prop('action',"{{route('clear_post_qualification_evaluation')}}");
}


$(".datepicker2").datepicker({
  format: 'mm/dd/yyyy',
});


if("{{session('message')}}"){
  if("{{session('message')}}"=="password_error"){
    swal.fire({
      title: `Password Error`,
      text: 'Wrong Password!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
    $("#password").val('');
  }

  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Clear Success`,
      text: 'Successfully Cleared Post Qualification',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'warning'
    });
  }
  else{

  }
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
        "opening_date":$("#opening_date").val(),
      },
      'method': "post",
      'dataType': "json",
      'success': function(data) {
        if($("#opening_date").val()==""){
          response({
            'id' : '',
            'value' : 'No Match Found'
          });
        }
        else{
          response(data);
        }
      }
    });
  },
  select: function(event, ui){
    if(ui.item.id != '' && $("#opening_date").val()!=""){
      $(this).val(ui.item.value);
    }else{
      $(this).val('');
    }
    return false;
  },
  change: function (event, ui) {

    $("#contractor").val('');
    $("#bidder_id").val('');

    if (ui.item == null || ui.item=="") {
      if("{{old('plan_id')}}"!=''){
        $(this).val("{{old('project_title')}}");
        $("#plan_id").val("{{old('plan_id')}}");
        $("#procact_id").val("{{old('procact_id')}}");
        $("#project_number").val("{{old('project_number')}}");
        $("#project_type").val("{{old('project_type')}}");
      }
      else{
        $(this).val('');
        $("#plan_id").val('');
        $("#procact_id").val('');
        $("#project_number").val('');
        $("#project_type").val('');
      }
    }
    else{
      var selected_project=ui.item;
      $("#plan_id").val(selected_project.id);
      $("#procact_id").val(selected_project.procact_id);
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
      'url': '/autocomplete_post_qualified_contractors',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term,
        "procact_id":$("#procact_id").val(),
        "type":@json($type)
      },
      'method': "post",
      'dataType': "json",
      'success': function(data) {
        if($("#opening_date").val()==""){
          response({
            'id' : '',
            'value' : 'No Match Found'
          });
        }
        else{
          response(data);
        }
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
      if("{{old('bidder_id')}}"!=''){
        $(this).val("{{old('contractor')}}");
        $("#bidder_id").val("{{old('bidder_id')}}");
      }
      else{
        $(this).val('');
        $("#bidder_id").val('');
      }
    }
    else{
      var selected_contractor=ui.item;
      $("#bidder_id").val(selected_contractor.id);

    }

  }
}

$("#contractor").autocomplete(contractor_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});

</script>
@endpush
