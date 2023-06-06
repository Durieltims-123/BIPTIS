@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="app">
    <div class="card shadow mt-4 mb-5" >
      <div class="card shadow border-0"  style="background:#F7F5F5">
        <div class="card-header" style="background:#F7F5F5">
          <h2 id="title">{{$title}}</h2>
        </div>
        <div class="card-body ">
          <form  id="app_form" name="app_form" type="POST" action="/submit_plan">
            <div class="row">
              <!-- ID -->
              <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                <label for="id">ID <span class="text-red">*</span></label>
                <input type="text" readonly id="id" name="id" class="form-control" value="{{old('id')}}" >
                <label class="error-msg text-red" >@error('id'){{$message}}@enderror</label>
              </div>


              @if($additional_sapp==true)
              <!-- Additional SAPP -->
              <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                <label for="additional_sapp"> <span class="text-red">*</span></label>
                <input type="text" readonly id="additional_sapp" name="additional_sapp" class="form-control" value="{{old('id')}}" >
                <label class="error-msg text-red" >@error('additional_sapp'){{$message}}@enderror</label>
              </div>
              @endif

              <!-- APP TYPE -->
              <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                <label for="app_type">APP Type <span class="text-red">*</span></label>
                <input type="text" readonly id="app_type" name="app_type" class="form-control" value="{{$project_type}}" >
                <label class="error-msg text-red" >@error('app_type'){{$message}}@enderror</label>
              </div>

              <!-- date added -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="date_added">Date Added <span class="text-red">*</span></label>
                <input type="text" readonly id="date_added" name="date_added" class="form-control bg-white" value="{{old('date_added')}}" >
                <label class="error-msg text-red" >@error('date_added'){{$message}}@enderror</label>
              </div>

              <!-- project year -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="project_year">Project Year <span class="text-red">*</span></label>
                <input  class="form-control bg-white" readonly id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}" >
                <label class="error-msg text-red" >@error('project_year'){{$message}}@enderror</label>
              </div>


              <!-- Year Funded -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="year_funded">Year Funded <span class="text-red">*</span></label>
                <input class="form-control bg-white" readonly id="year_funded" name="year_funded" value="{{old('year_funded')}}" >
                <label class="error-msg text-red" >@error('year_funded'){{$message}}@enderror</label>
              </div>

              <!-- Supplemental App Number -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0 {{$project_type=='regular'?'d-none':''}}">
                <label for="app_number">APP No.<span class="text-red">*</span></label>
                <input type="" id="app_number" name="app_number" class="form-control" value="{{old('app_number')}}" >
                <label class="error-msg text-red" >@error('app_number'){{$message}}@enderror</label>
              </div>

              <!-- Project Title -->
              <div class="form-group col-xs-12 col-sm-8 col-lg-8 mb-0">
                <label for="project_title">Project Title.<span class="text-red">*</span></label>
                <input type="" id="project_title" name="project_title" class="form-control bg-white" readonly value="{{old('project_title')}}" >
                <label class="error-msg text-red" >@error('project_title'){{$message}}@enderror</label>
              </div>

              <!-- Municipality -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="municipality">Municipality<span class="text-red">*</span></label>
                <select type="" id="municipality" name="municipality" class="form-control" >
                  <option value=""  {{ old('municipality') == '' ? 'selected' : ''}} >Select a Municipality</option>
                  @foreach($municipalities as $municipality)
                  <option value="{{$municipality->municipality_id}}"  {{ old('municipality') == $municipality->municipality_id ? 'selected' : ''}} >{{$municipality->municipality_name}}</option>
                  @endforeach
                </select>
                <label class="error-msg text-red" >@error('municipality'){{$message}}@enderror</label>
              </div>

              <!-- Barangay -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="barangay">Barangay<span class="text-red">*</span></label>
                <select type="" id="barangay" name="barangay" class="form-control" value="{{old('barangay')}}" >
                </select>
                <label class="error-msg text-red" >@error('barangay'){{$message}}@enderror</label>
              </div>
            </div>
            <div class="row">

              <!-- Project Number -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="project_number">Project No.<span class="text-red">*</span></label>
                <input type="" id="project_number" name="project_number" class="form-control bg-white" readonly value="{{old('project_number')}}" >
                <label class="error-msg text-red" >@error('project_number'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="account_code">Account Code<span class="text-red">*</span></label>
                <input type="" id="account_code" name="account_code" class="form-control" value="{{old('account_code')}}" >
                <label class="error-msg text-red" >@error('account_code'){{$message}}@enderror</label>
              </div>

              <!--Source of Fund-->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0 d-none">
                <label for="source_of_fund">Source of Fund<span class="text-red">*</span></label>
                <select type="" id="source_of_fund" name="source_of_fund" class="form-control" value="{{old('source_of_fund')}}" >
                  <option value=""  {{ old('source_of_fund') == '' ? 'selected' : ''}} >Select Source of Fund</option>
                  @foreach($funds as $fund)
                  <option value="{{$fund->fund_id}}"  {{ old('source_of_fund') == $fund->fund_id ? 'selected' : ''}} >{{$fund->source}}</option>
                  @endforeach
                </select>
                <label class="error-msg text-red" >@error('source_of_fund'){{$message}}@enderror</label>
              </div>

              <!-- Approved Budget Cost -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="approved_budget_cost">Approved Budget Cost<span class="text-red">*</span></label>
                <input type="" id="approved_budget_cost" name="approved_budget_cost" class="form-control bg-white" readonly value="{{old('approved_budget_cost')}}" >
                <label class="error-msg text-red" >@error('approved_budget_cost'){{$message}}@enderror</label>
              </div>

              <!-- Sector -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0 d-none">
                <label for="sector">Sector Type<span class="text-red">*</span></label>
                <div class="container-fluid">
                  <div class="custom-control-inline custom-radio ml-3">
                    <input type="radio" id="barangay_sector" value="barangay_development" {{ old('sector_type') == 'barangay_development' ? 'checked' : ''}} name="sector_type" class="custom-control-input">
                    <label class="custom-control-label" for="barangay_sector">Barangay Development</label>
                  </div>
                  <div class="custom-control-inline custom-radio ml-3">
                    <input type="radio" id="office_sector" value="office" {{ old('sector_type') == 'office' ? 'checked' : ''}}  name="sector_type" class="custom-control-input">
                    <label class="custom-control-label" for="office_sector">Office</label>
                  </div>
                </div>
                <label class="error-msg text-red" >@error('sector_type'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0 d-none">
                <label for="sector">Sector<span class="text-red">*</span></label>
                <select type="" id="sector" name="sector" class="form-control" value="{{old('sector')}}" >
                </select>
                <label class="error-msg text-red" >@error('sector'){{$message}}@enderror</label>
              </div>


              <!-- Type of Project  -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0 d-none">
                <label for="type_of_project">Type of Project<span class="text-red">*</span></label>
                <select type="" id="type_of_project" name="type_of_project" class="form-control" value="{{old('type_of_project')}}" >
                  <option value=""  {{ old('type_of_project') == '' ? 'selected' : ''}} >Select a Type</option>
                  @foreach($projtypes as $projtype)
                  <option value="{{$projtype->projtype_id}}"  {{ old('type_of_project') == $projtype->projtype_id ? 'selected' : ''}} >{{$projtype->type}}</option>
                  @endforeach
                </select>
                <label class="error-msg text-red" >@error('type_of_project'){{$message}}@enderror</label>
              </div>

              <!-- mode of procurement -->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0 d-none">
                <label for="mode_of_procurement">Mode of Procurement<span class="text-red">*</span></label>
                <select type="" id="mode_of_procurement" name="mode_of_procurement" class="form-control" value="{{old('mode_of_procurement')}}" >
                  <option value=""  {{ old('mode_of_procurement') == '' ? 'selected' : ''}} >Select Mode of Procurement</option>
                  @foreach($modes as $mode)
                  <option value="{{$mode->mode_id}}"  {{ old('mode_of_procurement') == $mode->mode_id ? 'selected' : ''}} >{{$mode->mode}}</option>
                  @endforeach
                </select>
                <label class="error-msg text-red" >@error('mode_of_procurement'){{$message}}@enderror</label>
              </div>
              <!-- Account account_classification -->
              <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                <label for="account_classification">Account Classication<span class="text-red">*</span></label>
                <select type="" id="account_classification" name="account_classification" class="form-control" value="{{old('account_classification')}}" >
                  <option value=""  {{ old('account_classification') == '' ? 'selected' : ''}} >Select Account Classification</option>
                  @foreach($classifications as $classification)
                  <option value="{{$classification->account_id}}"  {{ old('account_classification') == $classification->account_id ? 'selected' : ''}} >{{$classification->classification}}</option>
                  @endforeach
                </select>
                <label class="error-msg text-red" >@error('account_classification'){{$message}}@enderror</label>
              </div>



              <!--ABC POST Date-->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="ABC_post_date">ABC/Post of IB/REI<span class="text-red">*</span></label>
                <input type="text" readonly id="ABC_post_date" name="ABC_post_date" class="form-control bg-white" value="{{old('ABC_post_date')}}" >

                <label class="error-msg text-red" >@error('ABC_post_date'){{$message}}@enderror</label>
              </div>

              <!--Opening of Bid-->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="opening_of_bid">Opening of Bid<span class="text-red">*</span></label>
                <input type="" readonly id="opening_of_bid" name="opening_of_bid" class="form-control bg-white" value="{{old('opening_of_bid')}}" >
                <label class="error-msg text-red" >@error('opening_of_bid'){{$message}}@enderror</label>
              </div>

              <!--Notice of Award-->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="notice_of_award">Notice of Award<span class="text-red">*</span></label>
                <input type="" readonly id="notice_of_award" name="notice_of_award" class="form-control bg-white" value="{{old('notice_of_award')}}" >
                <label class="error-msg text-red" >@error('notice_of_award'){{$message}}@enderror</label>
              </div>

              <!--Contract Signing-->
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-0">
                <label for="contract_signing">Contract Signing<span class="text-red">*</span></label>
                <input type="" readonly id="contract_signing" name="contract_signing" class="form-control bg-white" value="{{old('contract_signing')}}" >
                <label class="error-msg text-red" >@error('contract_signing'){{$message}}@enderror</label>
              </div>


              <!--Remarks-->
              <div class="form-group col-xs-12 col-sm-8 col-lg-8 mb-0">
                <label for="remarks">Remarks<span class="text-red"></span></label>
                <input type="" id="remarks" name="remarks" class="form-control" value="{{old('remarks')}}" >
                <label class="error-msg text-red" >@error('remarks'){{$message}}@enderror</label>
              </div>


              <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0 text-center">
                <button type="submit" class="btn btn-primary mt-5">Submit</button>
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
// Variables
var data=@json($data);
data=JSON.parse(data);
// sweetalerts
if("{{session('message')}}"){
  if("{{session('message')}}"=="duplicate"){
    swal.fire({
      title: `Duplicate`,
      text: 'We already have the same entry in the database! \n Please Validate the Project Number.',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      icon: 'success'
    });
  }
  else{
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact your system developer',
      icon: 'warning'
    });
  }
}

// ajax header
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
})

// initialize components
$("#sector").html("<option value=''>Select a Sector</option>");


$(".datepicker").val(moment().format('MM/DD/YYYY'));
$(".yearpicker").val('{{$year}}');

$(".money2").click(function () {
  $('.money2').mask("#,##0.00", {reverse: true});
});

$(".money2").keyup(function () {
  $('.money2').mask("#,##0.00", {reverse: true});
});

$(".money2").focusout(function () {
  $('.money2').unmask();
  $('.money2').mask("###0.00", {reverse: true})
});


// initialize old input
var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>0){

  if(data.length==0){
    $("#app_form").prop('action','/submit_plan');
  }
  else{
    if("{{$additional_sapp}}"=="1"){
      $("#app_form").prop('action','/submit_plan');
    }
    else{
      $("#app_form").prop('action','/update_plan');
    }
  }
  $("#app_type").val("{{old('app_type')}}");
  $("#date_added").val("{{old('date_added')}}");
  $("#project_year").val("{{old('project_year')}}");
  $("#year_funded").val("{{old('year_funded')}}");
  getSector();
  $("#municipality").val("{{old('municipality')}}");
  getBarangays();
  $("#mode_of_procurement").val("{{old('mode_of_procurement')}}");
  $("#type_of_project").val("{{old('type_of_project')}}");
  $("#source_of_fund").val("{{old('source_of_fund')}}");
  $("#account_classification").val("{{old('account_classification')}}");
}
else{
  // fill edit initial value
  var passed_data=data[0];
  if(data.length>0){
    if("{{$additional_sapp}}"!="1"){
      $("#app_number").val(passed_data.app_group_no);
      $("#date_added").val(moment(moment(passed_data.date_added).format('Y-MM-DD')).format("MM/DD/YY"));
      $("#ABC_post_date").val(moment(moment(passed_data.abc_post_date).format('MMM-Y')).format('MM-Y'));
      $("#opening_of_bid").val(moment(moment(passed_data.sub_open_date).format('MMM-Y')).format('MM-Y'));
      $("#notice_of_award").val(moment(moment(passed_data.award_notice_date).format('MMM-Y')).format('MM-Y'));
      $("#contract_signing").val(moment(moment(passed_data.contract_signing_date).format('MMM-Y')).format('MM-Y'));
    }
    if("{{$additional_sapp}}"=="1"){
      $("#app_form").prop('action','/submit_plan');
    }
    else{
      $("#app_form").prop('action','/update_plan');
    }
    $("#id").val(passed_data.plan_id);
    $("#project_number").val(passed_data.project_no);
    $("#project_title").val(passed_data.project_title);
    $("#approved_budget_cost").val(passed_data.abc);
    $("#project_year").val(moment('01-01-'+passed_data.project_year).format('Y'));
    $("#year_funded").val(moment('01-01-'+passed_data.year_funded).format('Y'));
    $("#sector_type").val(passed_data.sector_type);
    $("#account_classification").val(passed_data.account_id);
    $("#account_code").val(passed_data.account_code);
    if(passed_data.sector_type=="barangay_development"){
      $("#barangay_sector").prop('checked',true);
    }
    if(passed_data.sector_type=="office"){
      $("#office_sector").prop('checked',true);
    }
    getSector();
    $("#municipality").val(passed_data.municipality_id);
    getBarangays();
    $("#mode_of_procurement").val(passed_data.mode_id);
    $("#type_of_project").val(passed_data.projtype_id);
    $("#source_of_fund").val(passed_data.fund_id);
    $("#remarks").val(passed_data.remarks);
  }



}


// functions
function getSector() {
  var formData=$("#app_form").serialize();
  $.ajax({
    url: "{{route('get_sector')}}",
    data:formData,
    type: 'POST',
    success: function (sectors) {
      $("#sector").html("<option value=''>Select a Sector</option>");
      sectors.forEach((sector) => {
        $("#sector").html(  $("#sector").html()+'<option value="'+sector.sector_id+'"  >'+sector.sector_name+'</option>');
      });
      if('{{ count(session()->getOldInput()) }}'>0){
        $("#sector").val("{{old('sector')}}");
      }
      else{
        if(data.length>0){
          $("#sector").val(data[0].sector_id);
        }
      }

    }
  });
}

function getBarangays() {
  var formData=$("#app_form").serialize();
  $.ajax({
    url: "{{route('get_barangays')}}",
    data:formData,
    type: 'POST',
    success: function (barangays) {
      $("#barangay").html("<option value=''>Select a Barangay</option>");
      barangays.forEach((barangay) => {
        $("#barangay").html(  $("#barangay").html()+'<option value="'+barangay.barangay_id+'"  >'+barangay.barangay_name+'</option>');
      });
      if('{{ count(session()->getOldInput()) }}'>0){
        $("#barangay").val("{{old('barangay')}}");
      }
      else{
        if(data.length>0){
          $("#barangay").val(data[0].barangay_id);
        }
      }


    }
  });
}



// events

$("#barangay_sector").click(function(){
  getSector();
});

$("#office_sector").click(function(){
  getSector();
});

$("#municipality").change(function(){
  getBarangays();
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
