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
    <div class="modal" tabindex="-1" role="dialog" id="roles_modal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="roles_modal_title">Users</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="col-sm-12" method="POST" id="sector_form" action="submit_user">
              @csrf
              <div class="row">
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="id">ID <span class="text-red">*</span> </label>
                  <input type="text" id="id" name="id" class="form-control form-control-sm" readonly value="{{old('id')}}" >
                  <label class="error-msg text-red" >@error('id'){{$message}}@enderror</label>
                </div>

                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                  <label for="id">Priveleges<span class="text-red">*</span> </label>
                  <input type="text" id="privileges" name="privileges" class="form-control form-control-sm" readonly value="{{old('privileges')}}" >
                  <label class="error-msg text-red" >@error('privileges'){{$message}}@enderror</label>
                </div>

                <!-- email_verified_at -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="email">Email <span class="text-red">*</span> </label>
                  <input type="email" id="email" name="email" class="form-control form-control-sm" value="{{old('email')}}" >
                  <label class="error-msg text-red" >@error('email'){{$message}}@enderror</label>
                </div>

                <!--  Name -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="name">Name <span class="text-red">*</span> </label>
                  <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{old('name')}}" >
                  <label class="error-msg text-red" >@error('name'){{$message}}@enderror</label>
                </div>
                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="administrator">Administrator<span class="text-red">*</span> </label>
                  <div class="d-flex flex-row">
                    <div class="custom-control custom-radio mb-3">
                      <input name="administrator" value="Yes" class="custom-control-input" id="administratorYes"  {{ old('administrator') === "Yes" ? "checked" : "" }}  type="radio">
                      <label class="custom-control-label" for="administratorYes">Yes</label>
                    </div>
                    <div class="custom-control custom-radio ml-3 mb-3">
                      <input name="administrator" value="No" class="custom-control-input" id="administratorNo" {{ old('administrator') === "No" ? "checked" : "" }} type="radio">
                      <label class="custom-control-label" for="administratorNo">No</label>
                    </div>
                  </div>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                  <label for="office">Office <span class="text-red">*</span> </label>
                  <select type="office" id="office" name="office" class="form-control form-control-sm" value="{{old('office')}}" >
                    <option value="">Select an Office</option>
                    @foreach($offices as $office)
                    <option value="{{$office->id}}">{{$office->name}}</option>
                    @endforeach
                  </select>
                  <label class="error-msg text-red" >@error('office'){{$message}}@enderror</label>
                </div>

                <!-- password -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="password">Password</label>
                  <input type="password" id="password" name="password" class="form-control form-control-sm" value="{{old('password')}}" >
                  <label class="error-msg text-red" >@error('password'){{$message}}@enderror</label>
                </div>

                <!-- password -->
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="verify_password">Verify Password</label>
                  <input type="password" id="verify_password" name="verify_password" class="form-control form-control-sm" value="{{old('verify_password')}}" >
                  <label class="error-msg text-red" >@error('verify_password'){{$message}}@enderror</label>
                </div>
              </div>

              <div class="row table-responsive">
                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                  <label for="verify_password">Assign Privilege <span class="text-red">*</span> </label>
                </div>
                <table class="table table-bordered" id="link_table">

                  <thead class="">
                    <tr class="bg-primary text-white" >
                      <th class="text-center">Order</th>
                      <th class="text-center">Module   /    Route</th>
                      <th class="text-center">Parent Link</th>
                      <th class="text-center">Privilege</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <div class="d-flex justify-content-center col-sm-12 mt-3">
                <button class="btn btn-primary text-center">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title">Users</h2>
        </div>
        <div class="card-body">

          <div class="table-responsive">
            <table class="table table-bordered" id="data_table">
              <thead class="">
                <tr class="bg-primary text-white" >
                  <th class="text-center"></th>
                  <th class="text-center">Id</th>
                  <th class="text-center">email</th>
                  <th class="text-center">name</th>
                  <th class="text-center">Office</th>
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
// datatables
$('#data_table thead tr').clone(true).appendTo( '#data_table thead' );
$('#data_table thead tr:eq(1)').removeClass('bg-primary');

let data=@json($users);
var table=  $('#data_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  dom: 'Bfrtip',
  buttons: [
    @if(in_array("add",$user_privilege))
    {
      text: 'Add User',
      attr: {
        id: 'add_user'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-danger text-white add_user'
    },
    @endif
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
    { "data": "id",render: function ( data, type, row ) {
      return '@if(in_array("update",$user_privilege))<button type="button" class="btn btn-sm btn btn-sm btn-success edit-btn" value="'+data+'" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="ni ni-ruler-pencil"></i></button>@endif @if(in_array("add",$user_privilege)||in_array("delete",$user_privilege))<button type="button" value="'+row.uid+'" class="btn btn-sm btn btn-sm btn-danger delete-btn" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ni ni-basket text-white"></i></button>@endif';
    }},
    { "data":"id"},
    { "data":"email"},
    { "data":"name"},
    { "data": "user_roles",render: function ( data, type, row ) {
      let role=data[0].role.name;
      return role;
    }}
  ],
  orderCellsTop: true,
  select: {
    style: 'multi',
    selector: 'td:not(:first-child)'
  },

  responsive:true,
  columnDefs: [ {
    targets: 0,
    orderable: false
  } ],
});

let links_data=@json($display_links);
let old_office=@json(old('office'));
$("#office").val(old_office);



var link_table=  $('#link_table').DataTable({
  language: {
    paginate: {
      next: '<i class="fas fa-angle-right">',
      previous: '<i class="fas fa-angle-left">'
    }
  },
  dom: 'Bfrtip',
  buttons: [
    {
      text: 'Select All',
      attr: {
        id: 'select_all'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-primary text-white'
    },
    {
      text: 'Deselect All',
      attr: {
        id: 'deselect_all'
      },
      className: 'btn btn-sm shadow-0 border-0 bg-danger text-white'
    },

  ],
  data:links_data,
  columns: [
    { "data": "id" },
    { "data": "link_name",render: function ( data, type, row ) {
      return '<div class="custom-control custom-checkbox ml-2 d-inline "> <input class="custom-control-input check_row"  id="check_row_'+row.id+'" type="checkbox"><label class="custom-control-label"  for="check_row_'+row.id+'">'+data+'</label></div>';
    }},
    { "data": "parent_name" },
    { "data": "get_link_privileges",render: function ( data, type, row ) {
      let privileges='<div class="d-flex flex-row">';
      data.forEach(function(lp){
        privileges=privileges+'<div class="custom-control custom-checkbox ml-2 d-inline"> <input class="custom-control-input check_privilege" id="'+lp.id+'" value="'+lp.id+'"type="checkbox"><label class="custom-control-label"  for="'+lp.id+'">'+lp.privilege+'</label></div>';
      });
      privileges=privileges+"</div>";
      return privileges;
    }}
  ],
  columnDefs: [
    { className: "unwrap", targets: [1] },
    {targets: 0,visible: false}
  ],
  orderCellsTop: true,
});





var oldInputs='{{ count(session()->getOldInput()) }}';
if(oldInputs>=2){
  $("#roles_modal").modal("show");
}

// messages
if("{{session('message')}}"){
  if("{{session('message')}}"=="success"){
    swal.fire({
      title: `Success`,
      text: 'Successfully saved to database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="duplicate"){
    swal.fire({
      title: `Duplicate`,
      text: 'Data already exist in the database',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else if("{{session('message')}}"=="delete_success"){
    swal.fire({
      title: `Success`,
      text: 'Data was deleted successfully',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-success',
      icon: 'success'
    });
  }
  else if("{{session('message')}}"=="delete_error"){
    swal.fire({
      title: `Delete Error`,
      text: 'You cannot delete this User',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
  else{
    swal.fire({
      title: `Error`,
      text: 'An error occured please contact system developer',
      buttonsStyling: false,
      confirmButtonClass: 'btn btn-sm btn-danger',
      icon: 'warning'
    });
  }
}


// events

$('#data_table thead tr:eq(1) th').each( function (i) {
  var title = $(this).text();
  if(title!=""){
    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    $(this).addClass('sorting_disabled');
    $( 'input', this ).on( 'keyup change', function () {
      if ( table.column(i).search() !== this.value ) {
        table
        .column(i)
        .search( this.value )
        .draw();
      }
    } );
  }
});



// show delete

@if(in_array("delete",$user_privilege))
$('#data_table tbody').on('click', '.delete-btn', function (e) {

  Swal.fire({
    text: 'Are you sure to delete this User?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: "Don't Delete",
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-sm btn-danger',
    cancelButtonClass: 'btn btn-sm btn-default',
    icon: 'warning'
  }).then((result) => {
    if(result.value==true){
      window.location.href = "/delete_user/"+$(this).val();
    }
  });

});
@endif
// add button

@if(in_array("add",$user_privilege))
$("#add_user").click(function () {
  $('.error-msg').html("");
  $("#sector_form")[0].reset();
  $("#roles_modal_title").html("Add User");
  link_table.$('td .d-flex .custom-checkbox input').each(function(){
    $(this).prop('checked',false);
  });
  $("#roles_modal").modal("show");
});
@endif


$('#link_table tbody').on('click', '.check_row', function (e) {
  $status=$(this).is(":checked");
  $(this).parents('tr').find('td .d-flex .custom-checkbox .check_privilege').each(function(){
    if($status){
      $(this).prop('checked',true);
    }
    else{
      $(this).prop('checked',false);
    }
  });
  $checked=link_table.$('.check_privilege:checked').map(function() {
    return this.value;
  }).get().toString();
  $("#privileges").val($checked);
});

link_table.$('.check_privilege').click(function (e) {
  let status=$(this).is(":checked");
  if(status){
    let all_input= $(this).parents('tr').find('td .d-flex .custom-checkbox .check_privilege').length;
    let checked= $(this).parents('tr').find('td .d-flex .custom-checkbox .check_privilege:checked').length;
    if(all_input===checked){
      $(this).parents('tr').find('td .custom-checkbox .check_row').prop('checked',true);
    }
  }
  else{
    $(this).parents('tr').find('td .custom-checkbox .check_row').prop('checked',false);
  }

  let checked=link_table.$('.check_privilege:checked').map(function() {
    return this.value;
  }).get().toString();
  $("#privileges").val(checked);
});

// edit button

$('#data_table tbody').on('click', '.edit-btn', function (e) {
  $('.error-msg').html("");
  link_table.$('td .d-flex .custom-checkbox input').each(function(){
    $(this).prop('checked',false);
  });
  $("#sector_form")[0].reset();
  $("#roles_modal_title").html("Edit User");
  table.rows().deselect();
  var data = table.row( $(this).parents('tr') ).data();
  var links=data.user_links;
  links.forEach(function(row){
    link_table.$("#"+row.link_privilege_id).trigger('click');
  });
  let selected=links.map(function(key, index) {
    return key.link_privilege_id;
  }).toString();
  let role=data.user_roles;
  $("#privileges").val(selected);
  $("#id").val(data.id);
  $("#name").val(data.name);
  $("#email").val(data.email);
  $("#office").val(role[0].role.id);
  if(data.administrator===1){
    $("#administratorYes").prop('checked',true);
  }
  else{
    $("#administratorNo").prop('checked',true);
  }
  $("#roles_modal").modal("show");
});
@if(in_array("update",$user_privilege))
@endif
let old_privileges=@json(old('privileges'));
if(old_privileges!=null){
  old_privileges=old_privileges.split(',');
  old_privileges.forEach(function(row){
    link_table.$("#"+row).trigger("click");
  });
}

$("#administratorYes").click(function(e){
  link_table.$('input[type="checkbox"]').map(function() {
    if($(this).prop('checked')===false){
      $(this).prop("checked",true);
    }
  });
  let checked=link_table.$('.check_privilege:checked').map(function() {
    return this.value;
  }).get().toString();
  $("#privileges").val(checked);
});

$("#select_all").click(function(e){
  link_table.$('input[type="checkbox"]').map(function() {
    if($(this).prop('checked')===false){
      $(this).prop("checked",true);
    }
  });
  let checked=link_table.$('.check_privilege:checked').map(function() {
    return this.value;
  }).get().toString();
  $("#privileges").val(checked);
});

$("#deselect_all").click(function(e){
  link_table.$('input[type="checkbox"]').map(function() {
    if($(this).prop('checked')===true){
      $(this).prop("checked",false);
    }
  });
  $("#privileges").val('');
});


$("#administratorNo").click(function(e){
  link_table.$('input[type="checkbox"]:checked').map(function() {
    if($(this).prop('checked')===true){
      $(this).prop("checked",false);
    }
  });
  $("#office").val("");
  $("#privileges").val("");
});

$("#office").change(function(){
  if($("#administratorYes").prop("checked")===false){

    link_table.$('input[type="checkbox"]:checked').map(function() {
      if($(this).prop('checked')===true){
        $(this).prop("checked",false);
      }
    });

    let privilege_array=[];
    if($(this).val()===""){
    }
    else if($(this).val()==="1"){
      privilege_array=[316,317,318,321,324,327,328,329,332,1,2,6,10,14,18,22,26,30,34,38,42,46,50,54,58,62,66,70,74,78,82,86,90,94,99,104,108,114,120,124,128,132,135,139,142,151,154,156,159,162,166,170,174,178,182,186,190,194,198,202,206,210,214,217,220,224,227,230,234,235,236,237,238,239,243,247,252,253,254,256,257,258,260,261,262,264,265,284,285,286,288,289,290,292,293,294,296,297,298];
    }
    else if($(this).val()==="2"){
      privilege_array=[162,163,164,165,1,2,6,10,14,18,22,26,30,34,38,42,46,50,54,58,62,66,70,74,78,82,86,90,94,99,104,108,114,120,124,142,143,144,145,146,147,148,149,150,154,155,234,235,236,237,238,239,243,247,316,317,318,321,324,327,328,329,332];
    }
    else if($(this).val()==="4"){
      privilege_array=[252,253,254,1,2,6,10,14,18,22,26,30,34,35,36,38,39,40,42,43,44,46,47,48,50,51,52,54,58,62,66,70,74,75,76,78,79,80,82,83,84,86,90,91,92,94,99,104,108,114,124,128,129,130,131,132,133,134,135,136,137,138,139,140,141,154,155,156,157,158,159,160,161,170,217,218,219,220,221,222,234,235,236,237,238,239,243,247,288,289,290,292,293,294,296,297,298,316,317,318,321,324,327,328,329,332];
    }
    else{
      privilege_array=[316,317,318,321,324,327,328,329,332,1,2,6,10,14,18,22,26,30,34,38,42,46,50,54,58,62,66,70,74,78,82,86,90,94,99,104,108,114,124,142,151,170,173,234,235,236,237,238,239,243,247];
    }

    privilege_array.forEach(function(data){
      link_table.$("#"+data).prop("checked",true);
    });

    let checked=link_table.$('.check_privilege:checked').map(function() {
      return this.value;
    }).get().toString();
    $("#privileges").val(checked);
  }

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
