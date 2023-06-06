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
          <h2 id="title">Generate Bid Evaluation</h2>
        </div>
        <div class="card-body">
          <form class="col-sm-8 mx-auto" method="POST" id="release_form" action="/submit_generate_bid_evaluation">
            @csrf
            <div class="row d-flex">

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 ">
                <label for="date_opened">Date Opened</label>
                <input type="text" id="date_opened" name="date_opened" class="form-control form-control-sm datepicker" value="{{old('date_opened')}}" >
                <label class="error-msg text-red" >@error('date_opened'){{$message}}@enderror</label>
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-lg-6 mt-4">
                <button class="btn btn-primary text-center">Submit</button>
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

$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
});


</script>
@endpush
