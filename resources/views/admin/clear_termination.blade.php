@extends('layouts.app')
<style>
ul.ui-autocomplete {
  z-index: 1100;
}
</style>
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

  <div id="row">

    <div class=" col-sm-8 shadow mx-auto">
      <div class="card border-0">
        <div class="card-header">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body">
          <form method="post" action="{{route('submit_clear_termination_of_contract')}}">
            @csrf
            <div class="col-sm-12 row mx-auto">

              <!-- Opening date -->
              <div class="form-group col-xs-3 col-sm-3 col-lg-3">
                <label for="opening_date">Opening Date </label>
                <input  type="text" id="opening_date" name="opening_date" class="form-control datepicker2" value="{{old('opening_date')}}" >
                <label class="error-msg text-red" >@error('opening_date'){{$message}}@enderror
                </label>
              </div>


            </div>
            <div class="col-sm-12 row mx-auto">

              <div class="form-group col-xs-3 col-sm-3 col-lg-3 d-none">
                <label for="termination_id">Termination Id <span class="text-red">*</span></label>
                <input type="text" id="termination_id" name="termination_id" class="form-control" value="{{old('termination_id')}}" >
                <label class="error-msg text-red" >@error('termination_id'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-3 col-sm-3 col-lg-3">
                <label for="project_number">Project Number<span class="text-red">*</span></label>
                <input type="text" id="project_number" name="project_number" class="form-control bg-white" value="{{old('project_number')}}" readonly >
                <label class="error-msg text-red" >@error('project_number'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-9 col-sm-9 col-lg-9">
                <label for="project_title">Project Title<span class="text-red">*</span></label>
                <input type="text" id="project_title" name="project_title" class="form-control" value="{{old('project_title')}}" >
                <label class="error-msg text-red" >@error('project_title'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-3 col-sm-3 col-lg-3 d-none">
                <label for="project_bid">Project Bid Id <span class="text-red">*</span></label>
                <input type="text" id="project_bid" name="project_bid" readonly class="form-control bg-white" value="{{old('project_bid')}}" >
                <label class="error-msg text-red" >@error('project_bid'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                <label for="contractor">Contractor<span class="text-red">*</span></label>
                <input type="text" id="contractor" name="contractor" readonly class="form-control bg-white"  >
                <label class="error-msg text-red" >@error('contractor'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                <label for="remarks">Remarks<span class="text-red">*</span></label>
                <textarea type="text" id="remarks" name="remarks" class="form-control" >{{old('remarks')}}</textarea>
                <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror</label>
              </div>


              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="password">Password<span class="text-red">*</span></label>
                <input type="password" id="password" name="password" class="form-control" value="{{old('password')}}"  >
                <label class="error-msg text-red" >@error('password'){{$message}}@enderror</label>
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
  }

  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Clear Termination Success`,
      text: 'Successfully Clear Termination of Contract',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'warning'
    });
  }

  else if("{{session('message')}}"=="termination_error"){
    swal.fire({
      title: `Termination Error`,
      text: 'Sorry you cannot Clear this Termination of Contract!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'danger'
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
      'url': '/autocomplete_terminated_project',
      'data': {
        "_token":"{{ csrf_token() }}",
        "term" : request.term,
        "opening_date":$("#opening_date").val(),
      },
      'method':"post",
      'dataType':"json",
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

    if (ui.item == null || ui.item=="") {
      if("{{old('termination_id')}}"!=''){
        $(this).val("{{old('project_title')}}");
        $("#termination_id").val("{{old('termination_id')}}");
        $("#contractor").val("{{old('contractor')}}");
        $("#project_bid").val("{{old('project_bid')}}");
        $("#project_number").val("{{old('project_number')}}");
      }
      else{
        $(this).val('');
        $("#termination_id").val('');
        $("#project_number").val('');
        $("#project_bid").val('');
        $("#contractor").val('');
      }
    }
    else{
      var selected_project=ui.item;
      $("#termination_id").val(selected_project.termination_id);
      $("#contractor").val(selected_project.contractor);
      $("#project_bid").val(selected_project.project_bid);
      $("#project_number").val(selected_project.project_number);
    }

  }
}

$("#project_title").autocomplete(project_title_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});


</script>
@endpush
