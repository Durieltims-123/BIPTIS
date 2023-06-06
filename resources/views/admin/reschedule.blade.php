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
          <form method="post" action="{{route('submit_reschedule')}}">
            @csrf
            <div class="col-sm-12 row mx-auto">

              <!-- Opening date -->
              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="opening_date">Opening Date<span class="text-red">*</span></label>
                <input  type="text" id="opening_date" name="opening_date" class="form-control datepicker2" value="{{old('opening_date')}}" >
                <label class="error-msg text-red" >@error('opening_date'){{$message}}@enderror
                </label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                <label for="new_opening_date">New Opening Date<span class="text-red">*</span></label>
                <input  type="text" id="new_opening_date" name="new_opening_date" class="form-control datepicker2" value="{{old('new_opening_date')}}" >
                <label class="error-msg text-red" >@error('new_opening_date'){{$message}}@enderror
                </label>
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
      title: `Reschedule Success`,
      text: 'Successfully Rescheduled Projects',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'warning'
    });
  }

  else if("{{session('message')}}"=="project_error"){
    swal.fire({
      title: `Project Error`,
      text: 'No Projects were scheduled for the selected opening date!',
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
