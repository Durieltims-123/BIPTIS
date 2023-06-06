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
    <div class="card shadow mt-4 mb-5">
      <div class="card shadow border-0" style="background:#F7F5F5">
        <div class="card-header" style="background:#F7F5F5">
          <h2 id="title">{{$title}}</h2>
        </div>
      </div>
      <form class="col-sm-12" method="POST" id="submit_schedule" action="{{route('submit_bac')}}">
        @csrf
        <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
          <label for="chair_ids">CHAIR ID <span class="text-red">*</span></label>
          <input type="text" id="chair_ids" name="chair_ids" class="form-control" value="{{old('chair_ids')}}">
          <label class="error-msg text-red">@error('chair_ids'){{$message}}@enderror</label>
        </div>

        <div class="card shadow">
          <div class="card-body row">
            <h2 class="col-sm-12">DURATION</h2>
            <!-- Start Date -->
            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="start_date">Start Date<span class="text-red">*</span></label>
              <input class="form-control datepicker" id="start_date" name="start_date" value="{{old('start_date')}}">
              <label class="error-msg text-red">@error('start_date'){{$message}}@enderror</label>
            </div>

            <!-- End Date -->
            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="end_date">End Date</label>
              <input class="form-control datepicker" id="end_date" name="end_date" value="{{old('end_date')}}">
              <label class="error-msg text-red">@error('end_date'){{$message}}@enderror</label>
            </div>

          </div>
        </div>
        <div class="card mt-3 shadow">
          <div class="row card-body">
            <h2 class="col-sm-12">BAC INFRASTRUCTURE</h2>
            <!-- ID -->
            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_id">ID <span class="text-red">*</span></label>
              <input type="text" id="bac_id" name="bac_id" class="form-control" value="{{old('bac_id')}}">
              <label class="error-msg text-red">@error('bac_id'){{$message}}@enderror</label>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_chairman_id">BAC Chairman ID<span class="text-red">*</span></label>
              <input class="form-control" id="bac_chairman_id" name="bac_chairman_id" value="{{old('bac_chairman_id')}}">
              <label class="error-msg text-red">@error('bac_chairman_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_chairman">BAC Infrastructure Chairman<span class="text-red">*</span></label>
              <input class="form-control member_autofill" id="bac_chairman" name="bac_chairman" value="{{old('bac_chairman')}}">
              <label class="error-msg text-red">@error('bac_chairman'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_vice_chairman_id">BAC Infrastructure Vice Chairman ID<span class="text-red">*</span></label>
              <input class="form-control " id="bac_vice_chairman_id" name="bac_vice_chairman_id" value="{{old('bac_vice_chairman_id')}}">
              <label class="error-msg text-red">@error('bac_vice_chairman_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_vice_chairman">BAC Infrastructure Vice Chairman<span class="text-red">*</span></label>
              <input class="form-control member_autofill" id="bac_vice_chairman" name="bac_vice_chairman" value="{{old('bac_vice_chairman')}}">
              <label class="error-msg text-red">@error('bac_vice_chairman'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_alternate_vice_chairman_id">BAC Infrastructure Alternate Vice Chairman ID</label>
              <input class="form-control" id="bac_alternate_vice_chairman_id" name="bac_alternate_vice_chairman_id" value="{{old('bac_alternate_vice_chairman_id')}}">
              <label class="error-msg text-red">@error('bac_alternate_vice_chairman_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_alternate_vice_chairman">BAC Infrastructure Alternate Vice Chairman</label>
              <input class="form-control member_autofill" id="bac_alternate_vice_chairman" name="bac_alternate_vice_chairman" value="{{old('bac_alternate_vice_chairman')}}">
              <label class="error-msg text-red">@error('bac_alternate_vice_chairman'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_infra_members_id">IDS<span class="text-red">*</span></label>
              <input type="text" id="bac_infra_members_id" name="bac_infra_members_id" class="form-control" value="{{old('bac_infra_members_id')}}">
              <label class="error-msg text-red">@error('bac_infra_members_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_infra_members_name">Names<span class="text-red">*</span></label>
              <input type="text" id="bac_infra_members_name" name="bac_infra_members_name" class="form-control" value="{{old('bac_infra_members_name')}}">
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_infra_members">Add BAC Infra Members</label>
              <input class="form-control bac_members" id="bac_infra_members" name="bac_infra_members" value="{{old('bac_infra_members')}}">
              <label class="error-msg text-red">@error('bac_infra_members'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-12">
              <label for="">Selected BAC Infra Members<span class="text-red">*</span></label>
              <br/>
              <label class="error-msg text-red">@error('bac_infra_members_id')This Field is Required @enderror</label>
              <div class="table-responsive">
                <table class="table table-bordered" id="bac_infra_members_table">
                  <thead class="thead-light">
                    <tr class="bg-primary" >
                      <th class="text-center"></th>
                      <th class="text-center"></th>
                      <th class="text-center"><h5 class="">ID</h5></th>
                      <th class="text-center"><h5 class="">Name</h5></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="card mt-3  shadow">
          <div class="row card-body">
            <h2 class="col-sm-12">BAC SECRETARIAT</h2>
            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_secretariat_chairman_id">BAC Secretariat Chairman ID<span class="text-red">*</span></label>
              <input class="form-control " id="bac_secretariat_chairman_id" name="bac_secretariat_chairman_id" value="{{old('bac_secretariat_chairman_id')}}">
              <label class="error-msg text-red">@error('bac_secretariat_chairman_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_secretariat_chairman">BAC Secretariat Chairman<span class="text-red">*</span></label>
              <input class="form-control member_autofill" id="bac_secretariat_chairman" name="bac_secretariat_chairman" value="{{old('bac_secretariat_chairman')}}">
              <label class="error-msg text-red">@error('bac_secretariat_chairman'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_secretariat_vice_chairman_id">BAC Secretariat Vice Chairman ID<span class="text-red">*</span></label>
              <input class="form-control " id="bac_secretariat_vice_chairman_id" name="bac_secretariat_vice_chairman_id" value="{{old('bac_secretariat_vice_chairman_id')}}">
              <label class="error-msg text-red">@error('bac_secretariat_vice_chairman_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_secretariat_vice_chairman">BAC Secretariat Vice Chairman<span class="text-red">*</span></label>
              <input class="form-control member_autofill" id="bac_secretariat_vice_chairman" name="bac_secretariat_vice_chairman" value="{{old('bac_secretariat_vice_chairman')}}">
              <label class="error-msg text-red">@error('bac_secretariat_vice_chairman'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_sec_members_id">IDS<span class="text-red">*</span></label>
              <input type="text" id="bac_sec_members_id" name="bac_sec_members_id" class="form-control" value="{{old('bac_sec_members_id')}}">
              <label class="error-msg text-red">@error('bac_sec_members_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_sec_members_name">Names<span class="text-red">*</span></label>
              <input type="text" id="bac_sec_members_name" name="bac_sec_members_name" class="form-control" value="{{old('bac_sec_members_name')}}">
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_sec_members">Add BAC Secretariat Members</label>
              <input class="form-control bac_members" id="bac_sec_members" name="bac_sec_members" value="{{old('bac_sec_members')}}">
              <label class="error-msg text-red">@error('bac_sec_members'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-12">
              <label for="">Selected BAC Secretariat Members<span class="text-red">*</span></label>
              <br/>
              <label class="error-msg text-red">@error('bac_sec_members_id')This Field is Required @enderror</label>
              <div class="table-responsive">
                <table class="table table-bordered" id="bac_sec_members_table">
                  <thead class="thead-light">
                    <tr class="bg-primary " >
                      <th class="text-center"></th>
                      <th class="text-center"></th>
                      <th class="text-center"><h5 class="">ID</h5></th>
                      <th class="text-center"><h5 class="">Name</h5></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="card mt-3  shadow">
          <div class="row card-body">
            <h2 class="col-sm-12">BAC SUPPORT</h2>
            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0  d-none">
              <label for="bac_support_members_id">IDS<span class="text-red">*</span></label>
              <input type="text" id="bac_support_members_id" name="bac_support_members_id" class="form-control" value="{{old('bac_support_members_id')}}">
              <label class="error-msg text-red">@error('bac_support_members_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_support_members_name">Names<span class="text-red">*</span></label>
              <input type="text" id="bac_support_members_name" name="bac_support_members_name" class="form-control" value="{{old('bac_support_members_name')}}">
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_support_members">Add BAC Support Members</label>
              <input class="form-control bac_members" id="bac_support_members" name="bac_support_members" value="{{old('bac_support_members')}}">
              <label class="error-msg text-red">@error('bac_support_members'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-12">
              <label for="">Selected BAC Support Members<span class="text-red">*</span></label>
              <br/>
              <label class="error-msg text-red">@error('bac_support_members_id')This Field is Required @enderror</label>

              <div class="table-responsive">
                <table class="table table-bordered" id="bac_support_members_table">
                  <thead class="thead-light">
                    <tr class="bg-primary " >
                      <th class="text-center"></th>
                      <th class="text-center"></th>
                      <th class="text-center"><h5 class="">ID</h5></th>
                      <th class="text-center"><h5 class="">Name</h5></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="card mt-3  shadow">
          <div class="row card-body">
            <h2 class="col-sm-12">BAC TECHNICAL WORKING GROUP</h2>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0  d-none">
              <label for="bac_twg_chairman_id">BAC TWG Chairman ID<span class="text-red">*</span></label>
              <input class="form-control " id="bac_twg_chairman_id" name="bac_twg_chairman_id" value="{{old('bac_twg_chairman_id')}}">
              <label class="error-msg text-red">@error('bac_twg_chairman_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 ">
              <label for="bac_twg_chairman">BAC TWG Chairman<span class="text-red">*</span></label>
              <input class="form-control member_autofill" id="bac_twg_chairman" name="bac_twg_chairman" value="{{old('bac_twg_chairman')}}">
              <label class="error-msg text-red">@error('bac_twg_chairman'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="bac_twg_vice_chairman_id">BAC TWG Vice Chairman ID<span class="text-red">*</span></label>
              <input class="form-control" id="bac_twg_vice_chairman_id" name="bac_twg_vice_chairman_id" value="{{old('bac_twg_vice_chairman_id')}}">
              <label class="error-msg text-red">@error('bac_twg_vice_chairman_id'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_twg_vice_chairman">BAC TWG Vice Chairman<span class="text-red">*</span></label>
              <input class="form-control member_autofill" id="bac_twg_vice_chairman" name="bac_twg_vice_chairman" value="{{old('bac_twg_vice_chairman')}}">
              <label class="error-msg text-red">@error('bac_twg_vice_chairman'){{$message}}@enderror</label>
            </div>


            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0  d-none">
              <label for="bac_twg_members_id">IDS<span class="text-red">*</span></label>
              <input type="text" id="bac_twg_members_id" name="bac_twg_members_id" class="form-control" value="{{old('bac_twg_members_id')}}">
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0  d-none">
              <label for="bac_twg_members_id">Names<span class="text-red">*</span></label>
              <input type="text" id="bac_twg_members_name" name="bac_twg_members_name" class="form-control" value="{{old('bac_twg_members_name')}}">
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="bac_twg_members">Add BAC TWG Members</label>
              <input class="form-control bac_members" id="bac_twg_members" name="bac_twg_members" value="{{old('bac_twg_members')}}">
              <label class="error-msg text-red">@error('bac_twg_members'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-12">
              <label for="bac_alternate_vice_chairman">Selected BAC TWG Members<span class="text-red">*</span></label>
              <br/>
              <label class="error-msg text-red">@error('bac_twg_members_id')This Field is Required @enderror</label>
              <div class="table-responsive">
                <table class="table table-bordered" id="bac_twg_members_table">
                  <thead class="thead-light">
                    <tr class="bg-primary " >
                      <th class="text-center"></th>
                      <th class="text-center"></th>
                      <th class="text-center"><h5 class="">ID</h5></th>
                      <th class="text-center"><h5 class="">Name</h5></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="card mt-3  shadow">
          <div class="row card-body">
            <h2 class="col-sm-12">OBSERVERS</h2>
            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="observers_id">IDS<span class="text-red">*</span></label>
              <input type="text" id="observers_id" name="observers_id" class="form-control" value="{{old('observers_id')}}">
              <label class="error-msg text-red">@error('observers_id'){{$message}}@enderror</label>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0 d-none">
              <label for="observers_name">Names<span class="text-red">*</span></label>
              <input type="text" id="observers_name" name="observers_name" class="form-control" value="{{old('observers_name')}}">
              <label class="error-msg text-red">@error('observers_name'){{$message}}@enderror</label>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-lg-4 mb-0">
              <label for="observers">Add Observer</label>
              <input class="form-control observers" id="observers" name="observers" value="{{old('observers')}}">
              <label class="error-msg text-red">@error('observers'){{$message}}@enderror</label>
            </div>

            <div class="form-group col-xs-12 col-sm-12">
              <label for="">Selected  Observers<span class="text-red">*</span></label>
              <br/>
              <label class="error-msg text-red">@error('observers_id')This Field is Required @enderror</label>
              <div class="table-responsive">
                <table class="table table-bordered" id="observers_table">
                  <thead class="thead-light">
                    <tr class="bg-primary " >
                      <th class="text-center"></th>
                      <th class="text-center"></th>
                      <th class="text-center"><h5 class="">ID</h5></th>
                      <th class="text-center"><h5 class="">Name</h5></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-center col-sm-12">
          <button id="submit_btn" class="btn btn-sm btn-primary text-center" >Submit</button>
        </div>
      </form>

    </div>
  </div>
</div>

@endsection

@push('custom-scripts')
<script>

jQuery.event.special.touchstart = {
  setup: function( _, ns, handle ){
    if ( ns.includes("noPreventDefault") ) {
      this.addEventListener("touchstart", handle, { passive: false });
    } else {
      this.addEventListener("touchstart", handle, { passive: true });
    }
  }
};

// sweetalerts
if ("{{session('message')}}") {
  if ("{{session('message')}}" == "duplicate") {
    swal.fire({
      title: `Duplicate`,
      text: 'We already have the same entry in the database!',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-warning',
      icon: 'warning'
    });
  } else if ("{{session('message')}}" == "success") {
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  } else {
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact your system developer',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'warning'
    });
  }
}

let bac_infra_members_table=$('#bac_infra_members_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  rowReorder: true,
});

let bac_sec_members_table=$('#bac_sec_members_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  rowReorder: true,
});

let bac_support_members_table=$('#bac_support_members_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  rowReorder: true,
});

let bac_twg_members_table=$('#bac_twg_members_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  rowReorder: true,
});

let observers_table=$('#observers_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  rowReorder: true,
});

observers_table.on( 'row-reorder', function ( e, diff, edit ) {
  var ids=[];
  var names=[];
  observers_table.on( 'draw', function () {
    observers_table.rows().every(function (rowIdx, tableLoop, rowLoop) {
      ids.push(this.data()[2]);
      names.push(this.data()[3]);
    });
    $("#observers_id").val(ids.toString());
    $("#observers_name").val(names.toString());
  });

});

var member_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_members',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term
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
    let id="#"+$(this).prop('id')+"_id";
    let chair_ids=$("#chair_ids").val();
    let bac_infra_members_id=$("#bac_infra_members_id").val();
    let bac_sec_members_id=$("#bac_sec_members_id").val();
    let bac_support_members_id=$("#bac_support_members_id").val();
    let bac_twg_members_id=$("#bac_twg_members_id").val();
    if(chair_ids==null || chair_ids==""){
      chair_ids=[];
    }
    else{
      chair_ids=chair_ids.split(',');
    }

    if(bac_infra_members_id==null || bac_infra_members_id==""){
      bac_infra_members_id=[];
    }
    else{
      bac_infra_members_id=bac_infra_members_id.split(',');
    }

    if(bac_sec_members_id==null || bac_sec_members_id==""){
      bac_sec_members_id=[];
    }
    else{
      bac_sec_members_id=bac_sec_members_id.split(',');
    }

    if(bac_support_members_id==null || bac_support_members_id==""){
      bac_support_members_id=[];
    }
    else{
      bac_support_members_id=bac_support_members_id.split(',');
    }

    if(bac_twg_members_id==null || bac_twg_members_id==""){
      bac_twg_members_id=[];
    }
    else{
      bac_twg_members_id=bac_twg_members_id.split(',');
    }

    if($(id).val()!=null && $(id).val()!=""){
      chair_ids = chair_ids.filter(function(item) {
        return item !== $(id).val()
      });

      $("#chair_ids").val(chair_ids.toString());
    }

    var member=ui.item;

    if (member==null||member=="") {
      if($(this).val()==null || $(id).val()==null || $(this).val()=="" || $(id).val()==""){
        $(id).val('');
        $(this).val('');
      }
      else{
        console.log("in here");
        if(id=="#bac_chairman_id"){
          old_id="{{old('bac_chairman_id')}}";
          old_name="{{old('bac_chairman')}}";

        }
        if(id=="#bac_vice_chairman_id"){
          old_id="{{old('bac_vice_chairman_id')}}";
          old_name="{{old('bac_vice_chairman')}}";

        }
        if(id=="#bac_alternate_vice_chairman_id"){
          old_id="{{old('bac_alternate_vice_chairman_id')}}";
          old_name="{{old('bac_alternate_vice_chairman')}}";

        }
        if(id=="#bac_secretariat_chairman_id"){
          old_id="{{old('bac_secretariat_chairman_id')}}";
          old_name="{{old('bac_secretariat_chairman')}}";

        }
        if(id=="#bac_secretariat_vice_chairman_id"){
          old_id="{{old('bac_secretariat_vice_chairman_id')}}";
          old_name="{{old('bac_secretariat_vice_chairman')}}";

        }
        if(id=="#bac_twg_chairman_id"){
          old_id="{{old('bac_twg_chairman_id')}}";
          old_name="{{old('bac_twg_chairman')}}";

        }
        if(id=="#bac_twg_vice_chairman_id"){
          old_id="{{old('bac_twg_vice_chairman_id')}}";
          old_name="{{old('bac_twg_vice_chairman')}}";

        }

        if(old_id!=""){
          if($(this).val()!=old_name ||$(id).val()!=old_id){
            $(id).val('');
            $(this).val('');
          }
        }
        else{
          $(id).val('');
          $(this).val('');
        }
      }

    }
    else{
      if(chair_ids.includes(member.id.toString())||bac_infra_members_id.includes(member.id.toString())||bac_sec_members_id.includes(member.id.toString())||bac_support_members_id.includes(member.id.toString())||bac_twg_members_id.includes(member.id.toString())){
        swal.fire({
          title: `Position Error`,
          text: $(this).val()+' is already assigned to a position',
          buttonsStyling: false,
          confirmButtonClass: 'btn btn-sm btn-danger',
          icon: 'warning'
        });
        $(id).val('');
        $(this).val('');
      }
      else{
        chair_ids.push(member.id.toString());
        $("#chair_ids").val(chair_ids.toString());
        $(id).val(member.id);
      }
    }
  }
}

var bac_members_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_members',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term
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
    let id="#"+$(this).prop('id')+"_id";
    let names=$("#"+$(this).prop('id')+"_name").val();
    let table=$(this).prop('id')+"_table";
    let chair_ids=$("#chair_ids").val();
    let bac_infra_members_id=$("#bac_infra_members_id").val();
    let bac_sec_members_id=$("#bac_sec_members_id").val();
    let bac_support_members_id=$("#bac_support_members_id").val();
    let bac_twg_members_id=$("#bac_twg_members_id").val();
    let all_ids=$(id).val();
    if(all_ids==null || all_ids==""){
      all_ids=[];
    }
    else{
      all_ids=all_ids.split(',');
    }
    if(names==null || names==""){
      all_names=[];
    }
    else{
      all_names=names.split(',');
    }
    if(chair_ids==null || chair_ids==""){
      chair_ids=[];
    }
    else{
      chair_ids=chair_ids.split(',');
    }

    if(bac_infra_members_id==null || bac_infra_members_id==""){
      bac_infra_members_id=[];
    }
    else{
      bac_infra_members_id=bac_infra_members_id.split(',');
    }

    if(bac_sec_members_id==null || bac_sec_members_id==""){
      bac_sec_members_id=[];
    }
    else{
      bac_sec_members_id=bac_sec_members_id.split(',');
    }

    if(bac_support_members_id==null || bac_support_members_id==""){
      bac_support_members_id=[];
    }
    else{
      bac_support_members_id=bac_support_members_id.split(',');
    }

    if(bac_twg_members_id==null || bac_twg_members_id==""){
      bac_twg_members_id=[];
    }
    else{
      bac_twg_members_id=bac_twg_members_id.split(',');
    }
    var member=ui.item;
    if (member==null||member=="") {

    }
    else{
      if(chair_ids.includes(member.id.toString())||bac_infra_members_id.includes(member.id.toString())||bac_sec_members_id.includes(member.id.toString())||bac_support_members_id.includes(member.id.toString())||bac_twg_members_id.includes(member.id.toString())){
        swal.fire({
          title: `Position Error`,
          text: $(this).val()+' is already assigned to a position',
          buttonsStyling: false,
          confirmButtonClass: 'btn btn-sm btn-danger',
          icon: 'warning'
        });
        $(this).val('');
      }
      else{
        all_ids.push(member.id.toString());
        all_names.push(member.value.toString());
        $("#"+$(this).prop('id')+"_name").val(all_names);
        $(id).val(all_ids.toString());
        if(table=="bac_infra_members_table"){
          table=bac_infra_members_table;
        }
        else if(table=="bac_sec_members_table"){
          table=bac_sec_members_table;
        }
        else if(table=="bac_support_members_table"){
          table=bac_support_members_table;
        }
        else if(table=="bac_twg_members_table"){
          table=bac_twg_members_table;
        }
        else{

        }

        table.on( 'order.dt search.dt', function () {
          table.column(0).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
          } );
        } ).draw(false);

        table.row.add( [
          all_ids.length+1,
          '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
          member.id.toString(),
          member.value
        ]).draw( false );

        $(this).val('');
      }
    }
  }
}

var observers_autocomplete_init = {
  minLength: 0,
  autocomplete: true,
  source: function(request, response){

    $.ajax({
      'url': '/autocomplete_observers',
      'data': {
        "_token": "{{ csrf_token() }}",
        "term" : request.term
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
    if($(this).val()!=""||$(this).val()!=null){
      let id="#"+$(this).prop('id')+"_id";
      let table="#"+$(this).prop('id')+"_table";
      let all_ids=$(id).val();
      let all_names=$("#"+$(this).prop('id')+"_name").val();
      if(all_ids==null || all_ids==""){
        all_ids=[];
      }
      else{
        all_ids=all_ids.split(',');
      }

      if(all_names==null || all_names==""){
        all_names=[];
      }
      else{
        all_names=all_names.split(',');
      }

      var observer=ui.item;
      if (observer==null||observer=="") {
      }
      else{
        if(all_ids.includes(observer.id.toString())){
          swal.fire({
            title: `Position Error`,
            text: $(this).val()+' is already selected',
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-sm btn-danger',
            icon: 'warning'
          });
          $(this).val('');
        }
        else{
          all_ids.push(observer.id.toString());
          all_names.push(observer.value.toString());
          $(id).val(all_ids.toString());
          $("#"+$(this).prop('id')+"_name").val(all_names.toString());
          let table=observers_table;

          table.on( 'order.dt search.dt', function () {
            table.column(0).nodes().each( function (cell, i) {
              cell.innerHTML = i+1;
            } );
          } ).draw(false);

          table.row.add( [
            all_ids.length+1,
            '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
            observer.id.toString(),
            observer.value
          ]).draw( false );
          $(this).val('');
        }
      }
    }
  }
}


function removeBtn(table,id,names,this_remove) {
  let all_ids=$(id).val().split(',');
  let all_names=$(names).val().split(',');
  let row=table.row(this_remove.parents('tr')).data();
  var filtered_array = all_ids.filter(function(value, index, arr){
    return value != row[2];
  });
  var filtered_names = all_names.filter(function(value, index, arr){
    return value != row[3];
  });

  table.on( 'order.dt search.dt', function () {
    table.column(0).nodes().each( function (cell, i) {
      cell.innerHTML = i+1;
    } );
  } ).draw(false);

  $(id).val(filtered_array.toString());
  $(names).val(filtered_names.toString());
  table.row(this_remove.parents('tr')).remove().draw(false);
}

function reOrder(table,id,names) {
  var ids_array=[];
  var names_array=[];
  table.on( 'draw', function () {
    table.rows().every(function (rowIdx, tableLoop, rowLoop) {
      ids_array.push(this.data()[2]);
      names_array.push(this.data()[3]);
    });

    $(id).val(ids_array.toString());
    $(names).val(names_array.toString());
  });
}

bac_infra_members_table.on('click', '.remove-btn',function functionName() {
  let this_remove=$(this);
  removeBtn(bac_infra_members_table,"#bac_infra_members_id","#bac_infra_members_name",this_remove);
});

bac_sec_members_table.on('click', '.remove-btn',function functionName() {
  let this_remove=$(this);
  removeBtn(bac_sec_members_table,"#bac_sec_members_id","#bac_sec_members_name",this_remove);
});

bac_support_members_table.on('click', '.remove-btn',function functionName() {
  let this_remove=$(this);
  removeBtn(bac_support_members_table,"#bac_support_members_id","#bac_support_members_name",this_remove);
});

bac_twg_members_table.on('click', '.remove-btn',function functionName() {
  let this_remove=$(this);
  removeBtn(bac_twg_members_table,"#bac_twg_members_id","#bac_twg_members_name",this_remove);
});

bac_infra_members_table.on( 'row-reorder', function ( e, diff, edit ) {
  reOrder(bac_infra_members_table,"#bac_infra_members_id","#bac_infra_members_name");
});

observers_table.on('click', '.remove-btn',function functionName() {
  let all_ids=$("#observers_id").val();
  let all_names=$("#observers_name").val().split(',');
  all_ids=all_ids.split(',');
  let row=observers_table.row($(this).parents('tr')).data();
  if(row!=null){
    let filtered_array = all_ids.filter(function(value, index, arr){
      return value != row[2];
    });

    var filtered_names = all_names.filter(function(value, index, arr){
      return value != row[3];
    });

    $("#observers_name").val(filtered_names.toString());
    $('#observers_id').val(filtered_array.toString());
    observers_table.row($(this).parents('tr')).remove().draw(false);
  }
});

$(".member_autofill").change(function functionName() {
  let id="#"+$(this).prop('id')+"_id";
  if($(this).val()==""||$(this).val()==null){
    let chair_ids=$("#chair_ids").val();
    chair_ids=chair_ids.split(',');

    var filtered_array = chair_ids.filter(function(value, index, arr){
      return value != $(id).val();
    });
    $("#chair_ids").val(filtered_array.toString());

    $(id).val("");
  }
});

$(".member_autofill").autocomplete(member_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});

$(".bac_members").autocomplete(bac_members_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});

$(".observers").autocomplete(observers_autocomplete_init).focus(function() {
  $(this).autocomplete('search', $(this).val())
});

// ajax header
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
})

jQuery.event.special.touchstart = {
  setup: function( _, ns, handle ){
    if ( ns.includes("noPreventDefault") ) {
      this.addEventListener("touchstart", handle, { passive: false });
    } else {
      this.addEventListener("touchstart", handle, { passive: true });
    }
  }
};
jQuery.event.special.touchmove = {
  setup: function( _, ns, handle ){
    if ( ns.includes("noPreventDefault") ) {
      this.addEventListener("touchmove", handle, { passive: false });
    } else {
      this.addEventListener("touchmove", handle, { passive: true });
    }
  }
};

// return input

if({!! json_encode(old('bac_infra_members_id')) !!}!=null){
  let counter=1;
  let ids={!! json_encode(old('bac_infra_members_id'))!!};
  let names={!! json_encode(old('bac_infra_members_name'))!!};
  ids=ids.toString().split(',');
  names=names.toString().split(',');
  ids.forEach((item, i) => {
    bac_infra_members_table.row.add( [
      counter,
      '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
      ids[i],
      names[i],
    ]).draw( false );
    counter++;
  });
}

if({!! json_encode(old('bac_sec_members_id')) !!}!=null){
  let counter=1;
  let ids={!! json_encode(old('bac_sec_members_id'))!!};
  let names={!! json_encode(old('bac_sec_members_name'))!!};
  ids=ids.toString().split(',');
  names=names.toString().split(',');
  ids.forEach((item, i) => {
    bac_sec_members_table.row.add( [
      counter,
      '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
      ids[i],
      names[i],
    ]).draw( false );
    counter++;
  });
}

if({!! json_encode(old('bac_support_members_id')) !!}!=null){
  let counter=1;
  let ids={!! json_encode(old('bac_support_members_id'))!!};
  let names={!! json_encode(old('bac_support_members_name'))!!};
  ids=ids.toString().split(',');
  names=names.toString().split(',');
  ids.forEach((item, i) => {
    bac_support_members_table.row.add( [
      counter,
      '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
      ids[i],
      names[i],
    ]).draw( false );
    counter++;
  });
}

if({!! json_encode(old('bac_twg_members_id')) !!}!=null){
  let counter=1;
  let ids={!! json_encode(old('bac_twg_members_id'))!!};
  let names={!! json_encode(old('bac_twg_members_name'))!!};
  ids=ids.toString().split(',');
  names=names.toString().split(',');
  ids.forEach((item, i) => {
    bac_twg_members_table.row.add( [
      counter,
      '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
      ids[i],
      names[i],
    ]).draw( false );
    counter++;
  });
}

if({!! json_encode(old('observers_id')) !!}!=null){
  let counter=1;
  let ids={!! json_encode(old('observers_id'))!!};
  let names={!! json_encode(old('observers_name'))!!};
  ids=ids.toString().split(',');
  names=names.toString().split(',');
  ids.forEach((item, i) => {
    observers_table.row.add( [
      counter,
      '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
      ids[i],
      names[i],
    ]).draw( false );
    counter++;
  });
}

$("input").change(function functionName() {
  $(this).siblings('.error-msg').html("");
});

$(".custom-radio").change(function functionName() {
  $(this).parent().siblings('.error-msg').html("");
});

$("select").change(function functionName() {
  $(this).siblings('.error-msg').html("");
});

$(document).ready(function() {
  if({!! json_encode(old('start_date')) !!}==null){
    // edit inputs
    let bac={!! json_encode($bac) !!};
    let members={!! json_encode($members) !!};
    let observers={!! json_encode($observers) !!};
    console.log(bac);

    if(bac!=null){
      let chair_ids="";
      $("#bac_id").val(bac[0].bac_id);
      $("#start_date").datepicker('setDate',bac[0].bac_start_date);
      $("#bac_chairman_id").val(bac[0].bac_chairman);
      $("#bac_chairman").val(bac[0].bac_chairman_name);
      $("#bac_vice_chairman_id").val(bac[0].bac_vice_chairman);
      $("#bac_vice_chairman").val(bac[0].bac_vice_chairman_name);
      $("#bac_alternate_vice_chairman_id").val(bac[0].bac_alternate_vice_chairman);
      $("#bac_alternate_vice_chairman").val(bac[0].bac_alt_vice_chairman_name);
      $("#bac_secretariat_chairman_id").val(bac[0].bac_sec_chairman);
      $("#bac_secretariat_chairman").val(bac[0].bac_sec_chairman_name);
      $("#bac_secretariat_vice_chairman_id").val(bac[0].bac_sec_vice_chairman);
      $("#bac_secretariat_vice_chairman").val(bac[0].bac_sec_vice_chairman_name);
      $("#bac_twg_chairman_id").val(bac[0].bac_twg_chairman);
      $("#bac_twg_chairman").val(bac[0].bac_twg_chairman_name);
      $("#bac_twg_vice_chairman_id").val(bac[0].bac_twg_vice_chairman);
      $("#bac_twg_vice_chairman").val(bac[0].bac_twg_vice_chairman_name);
      if(bac[0].bac_alternate_vice_chairman!=null){
        chair_ids=bac[0].bac_chairman+","+bac[0].bac_vice_chairman+","+bac[0].bac_alternate_vice_chairman+","+bac[0].bac_sec_chairman+","+bac[0].bac_sec_vice_chairman+","+bac[0].bac_twg_chairman+","+bac[0].bac_twg_vice_chairman;
      }
      else{
        chair_ids=bac[0].bac_chairman+","+bac[0].bac_vice_chairman+","+bac[0].bac_sec_chairman+","+bac[0].bac_sec_vice_chairman+","+bac[0].bac_twg_chairman+","+bac[0].bac_twg_vice_chairman;
      }
      console.log(chair_ids);
      $("#chair_ids").val(chair_ids);

      // Members
      let bac_infra_members_id="";
      let bac_infra_members_name="";
      let bac_sec_members_id="";
      let bac_sec_members_name="";
      let bac_support_members_id="";
      let bac_support_members_name="";
      let bac_twg_members_id="";
      let bac_twg_members_name="";
      let observers_id="";
      let observers_name="";
      let observers_counter=1;
      let bac_infra_members_counter=1;
      let bac_sec_members_counter=1;
      let bac_support_members_counter=1;
      let bac_twg_members_counter=1;

      console.log(members);

      members.forEach((item, i) => {
        if(members[i].bac_member_type=='BAC Infrastructure Member'){
          if(bac_infra_members_id==""){
            bac_infra_members_id=members[i].member_id;
            bac_infra_members_name=members[i].member_name;
          }
          else{
            bac_infra_members_id=bac_infra_members_id+","+members[i].member_id;
            bac_infra_members_name=bac_infra_members_name+","+members[i].member_name;
          }
          bac_infra_members_table.row.add( [
            bac_infra_members_counter,
            '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
            members[i].member_id,
            members[i].member_name,
          ]).draw( false );

          bac_infra_members_counter=bac_infra_members_counter+1;
        }
        if(members[i].bac_member_type=='BAC Secretariat Member'){
          if(bac_sec_members_id==""){
            bac_sec_members_id=members[i].member_id;
            bac_sec_members_name=members[i].member_name;
          }
          else{
            bac_sec_members_id=bac_sec_members_id+","+members[i].member_id;
            bac_sec_members_name=bac_sec_members_name+","+members[i].member_name;
          }

          bac_sec_members_table.row.add( [
            bac_sec_members_counter,
            '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
            members[i].member_id,
            members[i].member_name,
          ]).draw( false );

          bac_sec_members_counter=bac_sec_members_counter+1;
        }
        if(members[i].bac_member_type=='BAC Support Member'){
          if(bac_support_members_id==""){
            bac_support_members_id=members[i].member_id;
            bac_support_members_name=members[i].member_name;
          }
          else{
            bac_support_members_id=bac_support_members_id+","+members[i].member_id;
            bac_support_members_name=bac_support_members_name+","+members[i].member_name;
          }

          bac_support_members_table.row.add( [
            bac_support_members_counter,
            '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
            members[i].member_id,
            members[i].member_name,
          ]).draw( false );

          bac_support_members_counter=bac_support_members_counter+1;
        }
        if(members[i].bac_member_type=='BAC Technical Working Group Member'){
          if(bac_twg_members_id==""){
            bac_twg_members_id=members[i].member_id;
            bac_twg_members_name=members[i].member_name;
          }
          else{
            bac_twg_members_id=bac_twg_members_id+","+members[i].member_id;
            bac_twg_members_name=bac_twg_members_name+","+members[i].member_name;
          }

          bac_twg_members_table.row.add( [
            bac_twg_members_counter,
            '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
            members[i].member_id,
            members[i].member_name,
          ]).draw( false );

          bac_twg_members_counter=bac_twg_members_counter+1;
        }
      });

      $("#bac_infra_members_id").val(bac_infra_members_id);
      $("#bac_infra_members_name").val(bac_infra_members_name);
      $("#bac_sec_members_id").val(bac_sec_members_id);
      $("#bac_sec_members_name").val(bac_sec_members_name);
      $("#bac_support_members_id").val(bac_support_members_id);
      $("#bac_support_members_name").val(bac_support_members_name);
      $("#bac_twg_members_id").val(bac_twg_members_id);
      $("#bac_twg_members_name").val(bac_twg_members_name);

      observers.forEach((item, i) => {
        let name=observers[i].observer_name;
        if(name==null){
          name=observers[i].observer_office+" Representative";
        }
        if(observers_name==""){
          observers_id=observers[i].observer_id;
          observers_name=name;
        }
        else{
          observers_id=observers_id+","+observers[i].observer_id;
          observers_name=observers_name+","+name;
        }

        observers_table.row.add( [
          observers_counter,
          '<button class ="btn btn-sm remove-btn btn-sm btn-danger" type="button">Remove</button>',
          observers[i].observer_id,
          name,
        ]).draw( false );
        observers_counter=observers_counter+1;
      });

      $("#observers_id").val(observers_id);
      $("#observers_name").val(observers_name);

    }
  }
});

</script>
@endpush
