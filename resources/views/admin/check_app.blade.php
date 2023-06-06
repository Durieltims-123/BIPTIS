@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="app">
    <div class="card shadow mt-4 mb-5">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Check APP Status</h2>
        </div>
        <div class="card-body">
          <form method="post" name="fix_app" id="fix_app" action="{{route('submit_check_app')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 mx-auto">
              <input type="file" class="form-control" id="file" name="file" placeholder="">
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 mx-auto d-flex justify-content-center">
              <button class="btn btn-sm btn-primary float-center">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('custom-scripts')
@endpush
