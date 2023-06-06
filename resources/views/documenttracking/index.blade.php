@extends($role==1 ? 'layouts.app' : 'layouts.app2');

@section('css')

<link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet" />
<link href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css" rel="stylesheet" />
<link href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.9/css/dataTables.checkboxes.css" rel="stylesheet" />

<style>
label {
display: inline-block;
width: 300px;
}
/*
#pr_date:invalid+span:after {
content: '✖';
padding-left: 5px;
color: red;
font-size: 1em!important;
vertical-align: middle;
}

#pr_date:valid+span:after {
content: '✓';
padding-left: 5px;
color: green;
font-size: 1em!important;
}
*/
.w-2{
width: 2%!important;
}
.w-3{
width: 3%!important;
}
.w-4{
width: 4%!important;
}
.w-5{
width: 5%!important;
}
.w-10{
width: 15%!important;
}
.w-10b{
width: 15.1%!important;
}
.w-25{
width: 25%!important;
}
.w-95{
width: 95%!important;
}
.w-98{
width: 98%!important;
}
.w-99{
width: 99.95%!important;
}
/* .form-control:focus,.custom-select:focus {
outline: none !important;
border: 1px solid rgb(0, 184, 40)!important;
box-shadow: 0 0 15px #719ECE!important;
} */
.buttons{
height: calc(2.75rem + 2px)!important;
line-height: 1.5!important;
width: 100%;
}
.soft-copy{
overflow: hidden!important;
}
input[type="file"]{
visibility: visible;
content: 'Choose File'!important;
background: #ffffff none repeat scroll 0 0;
border: 2px dotted #ccc!important;
box-shadow: none;
color: rgb(150, 150, 150);
vertical-align: middle!important;
}
input::-webkit-file-upload-button{
opacity: 0!important;
font-size: 0px;
}
form .error {
color: #d60909;
}
form .error:before {
content: ' ✖ ';
color: #d60909;
}

form .valid:before{
display: none;
/* content: ' ✓'; */
color: #0eff06;
}
#field{
margin-left:.5em;
float:left;
}
#field,label{
float:left;font-family:Arial,Helvetica,sans-serif;font-size:small}
br{
clear:both
}
input{
overflow: hidden!important;
border:1px solid #000;
margin-bottom: 0em;
}
input.error, select.error{
border:1px solid red
}
input.valid, select.valid{
border:1px solid green
}

label.error{

}
.page-item .page-link, .page-item span {
border-radius: 0%!important;
/* border: 1px rgb(7, 150, 194) solid!important;  */
/* background-color: rgba(255, 255, 255, 0)!important;  */
/* color: #12808f!important; */
}
.myTabContent{
overflow-x: scroll!important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button{
min-width: 0.2em; !important;
}
.toolbar{
display: inline!important;
}
.div.dataTables_wrapper div.dataTables_paginate{
width: 50%!important;
display: inline-flex!important;
}
button .disabled{
    background-color:rgb(8, 8, 8)!important;
}
ul.ui-autocomplete {
z-index: 999999999!important;
}
.toast{
z-index: 999999999!important;
}
.nav>li>a.active,
.nav>li>a:hover,
.nav>li>a:focus {
    /* background: repeating-linear-gradient(
        /* -55deg,
        0deg,
        #222,
        #222 5px,
        #333 5px,
        #333 10px
    ); */
    background-color: rgb(94, 94, 94)!important;
    color:rgb(255, 255, 255)!important;
}
.nav>li>a{
    color:rgb(8, 8, 8)!important;
}
#filter_selected>div{
    background: repeating-linear-gradient(
        /* -55deg, */
        to right,
        #222,
        #222 5px,
        #333 5px,
        #333 10px
    );
}
/* div.dataTables_wrapper div.dataTables_paginate{
    display: none!important;
} */
input[type='radio'], input[type='checkbox']{
    margin-top: 6.5px!important;
}
#showall tbody tr.selected {
    color: rgb(0, 0, 0)!important;
    background-color: #c2ecff!important;
}
.page-item.active .page-link {
    z-index: 1;
    color: #fff;
    border-color: #47acff;
    background-color: #47acff;
}
.document_type_title{
    color: rgb(10, 10, 10);
    border-color: #47acff;
    background-color: #47acff;
}
</style>
@endsection
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-4">
    <div class="fade-in">
        <div class="row">
            <div class="col-lg-12">
                <div class="notifications"></div>
                <div class="card">
                    <div class="card-header  text-light rounded-0 pt-2 pl-3 pb-0">
                        <div class="row">
                            <div class="col-lg-6">
                                <h2 class="text-dark">Document Tracking - <span id="tab-title"></span></h2>
                            </div>
                            <div class="col-lg-6">
                                <a class="btn btn-sm float-right"><i class="fa fa-refresh text-dark"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2 pl-3 pr-2 pb-2  border-top-0">
                        <div class="row mb-0">
                            <div class="col-lg-12">
                                <ul class="nav nav-pills  bg-primary nav-fill flex-sm-row w-100" id="tabs-text" role="tablist">
                                    <li class="nav-item pr-0">
                                        <a class="nav-link  bg-transparent mb-sm-3 mb-md-0 active  border-right-0 rounded-0 border-bottom-0 pt-2 pb-2 text-white" id="all-documents-tab" data-toggle="tab" href="#alldocuments" role="tab" aria-controls="tabs-text-1" aria-selected="true">All</a>
                                    </li>
                                    <li class="nav-item pr-0 pl-0">
                                        <a class="nav-link  bg-transparent mb-sm-3 mb-md-0  border-right-0 rounded-0 border-bottom-0 pt-2 pb-2" id="forwarded-tab" data-toggle="tab" href="#forwarded" role="tab" aria-controls="tabs-text-2" aria-selected="false">Forwarded</a>
                                    </li>
                                    <li class="nav-item pr-0 pl-0">
                                        <a class="nav-link  bg-transparent mb-sm-3 mb-md-0  border-right-0 rounded-0 border-bottom-0 pt-2 pb-2" id="forreceiving-tab" data-toggle="tab" href="#forreceiving" role="tab" aria-controls="tabs-text-3" aria-selected="false">For Receiving</a>
                                    </li>
                                    <li class="nav-item pr-0 pl-0">
                                        <a class="nav-link  bg-transparent mb-sm-3 mb-md-0  border-right-0 rounded-0 border-bottom-0 pt-2 pb-2" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="tabs-text-4" aria-selected="false">Pending</a>
                                    </li>
                                    <li class="nav-item pr-0 pl-0">
                                        <a class="nav-link  bg-transparent mb-sm-3 mb-md-0  border-right-0 rounded-0 border-bottom-0 pt-2 pb-2" id="unsent-tab" href="#unsent" data-toggle="tab" role="tab" aria-control="tab-text-5" aria-sected="false">Unsent</a>
                                    </li>
                                    <li class="nav-item pr-0 pl-0">
                                        <a class="nav-link  bg-transparent mb-sm-3 mb-md-0  rounded-0 border-bottom-0 pt-2 pb-2" id="ended-tab" href="#ended" data-toggle="tab" role="tab" aria-control="tab-text-5" aria-sected="false">Ended</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row pt-0 pb-0">
                            <div class="col-lg-12" id="filter_selected">
                                <div class="btn-group  bg-gradient-light p-2 w-100">
                                    <button type="button" class="btn btn-secondary  bg-gradient-light text-dark rounded-0 pt-1 pb-1" id="no_action_selected" readonly style="display: none!important;"><i class="fa fa-exclamation-triangle"></i> No Actions Available.</button>
                                    <button type="button" class="btn btn-secondary  bg-gradient-light text-dark rounded-0 pt-1 pb-1" id="receive_selected" onclick="$.receive_documents()" style="display: none!important"><i class="fa fa-calendar-check text-success"></i> Receive Selected</button>
                                    <button type="button" class="btn btn-secondary  bg-gradient-light text-dark rounded-0 pt-1 pb-1" id="unsend_selected" onclick="$.discard_documents()" style="display: none!important"><i class="fa fa-trash-alt text-danger"></i> Unsend Selected</button>
                                    <button type="button" class="btn btn-secondary  bg-gradient-light text-dark rounded-0 pt-1 pb-1" id="forward_selected" onclick="$.forward_documents()" style="display: none!important"><i class="fa fa-envelope-open-text text-info"></i> Forward Selected</button>
                                    <button type="button" class="btn btn-secondary  bg-gradient-light text-dark rounded-0 pt-1 pb-1"  id="end_selected" onclick="$.end_documents()" style="display: none!important"><i class="fa fa-flag text-danger"></i> End Transaction</button>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-0 pt-2">
                            <div class="col-lg-3">
                                <div class="input-group document_type_group">
                                    <div class="input-group-prepend">
                                        <p class="input-group-text rounded-0  border-right-0 text-dark w-100"><small>Document Type</small></p>
                                    </div>
                                    <input class="form-control rounded-0  pl-2" type="text" id="search_document_type" name="search_document_type" placeholder="Search Document Type" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-danger rounded-0" id="clear_document_type"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-group project_title_group">
                                    <div class="input-group-prepend">
                                        <p class="input-group-text rounded-0  border-right-0 text-dark w-100"><small>Project Title</small></p>
                                    </div>
                                    <input class="form-control rounded-0  pl-2" type="text" id="search_project_title" name="search_project_title" placeholder="Search Project Title" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-danger rounded-0" id="clear_project_title"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-group contractor_group">
                                    <div class="input-group-prepend">
                                        <p class="input-group-text rounded-0  border-right-0 text-dark w-100"><small>Contractor</small></p>
                                    </div>
                                    <input class="form-control rounded-0  pl-2" type="text" id="search_contractor" name="search_contractor" placeholder="Search Contractor" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-danger rounded-0" id="clear_contractor"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="btn-group w-100" style="display: flex!important">
                                    <button type="button" class="btn btn-dark dropdown-toggle rounded-0 w-100" style="padding-top: 12px!important; flex: 1!important;"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" style="flex: 1!important;">
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="2">Date Added</a>
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="3">Sender</a>
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="4">Receiver</a>
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="5">Document Type</a>
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="6">Classification</a>
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="7" id="project_title_toggle">Project Title</a>
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="8">Contractor</a>
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="9">Procurement Process</a>
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="10">ABC</a>
                                        <a class="toggle-vis rounded-0 dropdown-item" data-column="11">Detailed Estimates</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="btn-group" style="display: flex!important">
                                    <button class="btn btn-secondary btn-md bg-gradient-dark rounded-0 text-white border-dark" style="padding-top: 14px!important; padding-bottom: 14px!important; flex: 1!important;" id="previous"><i class="fa fa-caret-left"></i> </button>
                                    <button class="btn btn-secondary btn-md bg-gradient-dark rounded-0 text-white border-dark" style="padding-top: 14px!important; padding-bottom: 14px!important; flex: 1!important;" id="reload"><i class="fa fa-sync"></i> </button>
                                    <button class="btn btn-secondary btn-md bg-gradient-dark rounded-0 text-white border-dark" style="padding-top: 14px!important; padding-bottom: 14px!important; flex: 1!important;" id="reload"><i class="fa fa-print"></i> </button>
                                    <button class="btn btn-secondary btn-md bg-gradient-dark rounded-0 text-white border-dark" style="padding-top: 14px!important; padding-bottom: 14px!important; flex: 1!important;" id="next"><i class="fa fa-caret-right"></i> </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tab-content w-100" id="myTabContent" style="overflow-x: scroll!important;">
                                <table class="table display table-hover  border-left-0 border-right-0" width="100%" id="showall">
                                    <thead>
                                        <tr class="bg-secondary">
                                            <td class="text-center bg-light text-dark  border-right-0 w-5 p-2"></td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Status</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Date Added</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Sender</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Receiver</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Document Type</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Classification</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Project Title</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Contractor</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Procurement Process</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">ABC</td>
                                            <td class="text-center bg-light text-dark  p-2">Detailed Estimate</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-secondary">
                                            <td class="text-center bg-light text-dark  border-right-0 w-5 p-2"></td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Status</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Date Added</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Sender</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Receiver</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Document Type</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Classification</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Project Title</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Contractor</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">Procurement Process</td>
                                            <td class="text-center bg-light text-dark  border-right-0 p-2">ABC</td>
                                            <td class="text-center bg-light text-dark  p-2">Detailed Estimate</td>
                                        </tr>
                                    </tfoot>
                                </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-lg-12">
                                <div id="custom-pagination" class="pt-2 pb-0 mb-0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
<script src="{{ asset('js/jquery-number-master/jquery.number.js') }}"></script>
<script src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.9/js/dataTables.checkboxes.min.js"></script>


<script>
function format ( d ) {
// `d` is the original data object for the row
// console.log(d.i_request_stock_supplier.length);
// markup = '';
// markup +=   '<table class="w-100" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
//                 '<thead>'+
//                     '<tr>'+
//                         '<td>Generic Name</td>'+
//                         '<td>Item Description</td>'+
//                         '<td>Requested Quantity</td>'+
//                         '<td>At Cost</td>'+
//                     '</tr>'+
//                 '</thead>';
// $.each( d.i_request_stock_supplier, function( key, value ) {
//     console.log(key + ": " + value.requested_qty);
//     markup += '<tr>'+
//                     '<td>'+value.i_generic_name.generic_name+'</td>'+
//                     '<td>'+value.i_generic_name.generic_name_description+'</td>'+
//                     '<td>'+value.requested_qty+'</td>'+
//                     '<td>'+value.at_cost+'</td>'+
//                 '</tr>';
// });
// markup += '</table>';
// return markup;
}

// var currtab = $("#currenttab").val();
// console.log(currtab);
// var activetab = localStorage.getItem('activetab');
// $(document).on('keypress',function(e) {
//     if(e.which == 13) {
//         alert('You pressed enter!');
//     }
//     if(e.which == 112){
//         alert('up!');
//     }
//     if(e.which == 113){
//         alert('down');
//     }
// });
$(document).ready(function() {
    $('body').on('keydown', function(e){
        if(e.which == 39){
            $('#next').click();
        }
        if(e.which == 37){
            $('#previous').click();
        }
        if(e.which == 49){
            $('#all-documents-tab').click();
        }
        if(e.which == 50){
            $('#forwarded-tab').click();
        }
        if(e.which == 51){
            $('#forreceiving-tab').click();
        }
        if(e.which == 52){
            $('#pending-tab').click();
        }
        if(e.which == 53){
            $('#unsent-tab').click();
        }
        if(e.which == 54){
            $('#ended-tab').click();
        }
    });
var table;
var currenttab;
var currenttabid = 'all-documents-tab';
// !- Recipient Autocomplete Initialization
var recipient_autocomplete_init = {
    minLength: 0,
    autocomplete: true,
    select: function(event, ui){
    if(ui.item.id != ''){
        $(this).val(ui.item.value);
    }else{
        $(this).val('');
    }
    return false;
    },
    source: function(request, response){
    $.ajax({
        'url': '/getusers',
        'data': {
            "_token": "{{ csrf_token() }}",
            "term" : request.term
        },
        'method': "get",
        'dataType': "json",
        'success': function(data) {
            response(data);
        }
    });
    },
    change: function (event, ui) {
    if (ui.item === null) {
        $(this).val('');
    }
    }
}
// -!
filter_table();
$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
    localStorage.setItem('activetab', $(e.target).attr('href'));
    currenttab = $(e.target).attr("href");
    currenttabid = $(e.target).attr("id");
    table.destroy();
    filter_table(currenttabid);
});
var activetab = localStorage.getItem('activetab');
if(activetab){
    $('#tabs-text a[href="' + activetab + '"]').tab('show');
}


function reinitialize_table(){
    table.destroy();
    filter_table(currenttabid);
}
function filter_table(currenttabid){
    var filter;
    if(currenttabid && currenttabid != 'all-documents-tab'){
        filter = currenttabid;
    }else{
        filter = 'all-documents-tab';
    }
    if(filter){
        if(filter == 'forwarded-tab'){
            $('#unsend_selected').attr('style', 'display: block!important');
            $('#receive_selected').attr('style', 'display: none!important');
            $('#forward_selected').attr('style', 'display: none!important');
            $('#no_action_selected').attr('style', 'display: none!important');
            $('#end_selected').attr('style', 'display: none!important');
            $('#filter_selected').attr('style', 'display: block!important');
            $('#tab-title').text('Forwarded Documents');
            $('#others_dropdown').text('');
            $('#others_dropdown').text('');
        }else if(filter == 'forreceiving-tab'){
            $('#receive_selected').attr('style', 'display: block!important');
            $('#forward_selected').attr('style', 'display: none!important');
            $('#unsend_selected').attr('style', 'display: none!important');
            $('#end_selected').attr('style', 'display: none!important');
            $('#filter_selected').attr('style', 'display: block!important');
            $('#no_action_selected').attr('style', 'display: none!important');
            $('#tab-title').text('For Receiving Documents');
            $('#others_dropdown').text('');
            $('#others_dropdown').text('');
        }else if(filter == 'pending-tab'){
            $('#forward_selected').attr('style', 'display: block!important');
            $('#receive_selected').attr('style', 'display: none!important');
            $('#unsend_selected').attr('style', 'display: none!important');
            $('#end_selected').attr('style', 'display: block!important');
            $('#filter_selected').attr('style', 'display: block!important');
            $('#no_action_selected').attr('style', 'display: none!important');
            $('#tab-title').text('Pending Documents');
            $('#others_dropdown').text('');
            $('#others_dropdown').text('');
        }else if(filter == 'all-documents-tab'){
            $('#tab-title').text('All Documents');
            $('#others_dropdown').text('');
            $('#others_dropdown').text('');
            $('#receive_selected').attr('style', 'display: none!important');
            $('#unsend_selected').attr('style', 'display: none!important');
            $('#forward_selected').attr('style', 'display: none!important');
            $('#filter_selected').attr('style', 'display: block!important');
            $('#no_action_selected').attr('style', 'display: block!important');
        }else if(filter == 'ended-tab'){
            $('#tab-title').text('Complete/Ended Documents');
            $('#others_dropdown').text('Complete/Ended Documents');
            $('#receive_selected').attr('style', 'display: none!important');
            $('#unsend_selected').attr('style', 'display: none!important');
            $('#forward_selected').attr('style', 'display: none!important');
            $('#filter_selected').attr('style', 'display: block!important');
            $('#end_selected').attr('style', 'display: none!important');
            $('#no_action_selected').attr('style', 'display: block!important');
        }else if(filter == 'unsent-tab'){
            $('#tab-title').text('Unsent Documents');
            $('#others_dropdown').text('Unsent Documents');
            $('#receive_selected').attr('style', 'display: none!important');
            $('#unsend_selected').attr('style', 'display: none!important');
            $('#forward_selected').attr('style', 'display: none!important');
            $('#filter_selected').attr('style', 'display: block!important');
            $('#end_selected').attr('style', 'display: none!important');
            $('#no_action_selected').attr('style', 'display: block!important');
        }else{
            $('#receive_selected').attr('style', 'display: none!important');
            $('#unsend_selected').attr('style', 'display: none!important');
            $('#forward_selected').attr('style', 'display: none!important');
            $('#filter_selected').attr('style', 'display: block!important');
            $('#others_dropdown').text('');
            $('#others_dropdown').text('');
            $('#no_action_selected').attr('style', 'display: block!important');
        }
    }


    table = $('#showall').DataTable( {
    "ajax": "/getdocuments?t=all&f="+filter,
    "select": {
        style: 'multi'
    },
    "columns": [
        {
            "className": 'p-0 m-0  border-top-0 border-bottom-0 border-right-0 w-1',
            "data": {
                id: "id",
                sender_name: "sender_name",
                file_status: "file_status",
                active_status: "active_status",
                status: "status"
            },
            'checkboxes': {
               'selectRow': true
            }
        },
        {
            "className": 'pt-1 pb-1 pl-1 pr-1 m-0 text-center  border-top-0 border-bottom-0 border-right-0',
            "data": {
                project_documents_status: "project_documents_status",
                mode_id: "mode_id"
            },
            "render": function(data, type, row){
                var spanclass = 'primary';
                var spanvalue = 'svp';
                var spanvalueclass = 'danger';
                if(data.project_documents_status == 'send') spanclass = 'success';
                if(data.project_documents_status == 'unsent') spanclass = 'danger';
                if(data.project_documents_status == 'forwarded') spanclass = 'primary';
                if(data.mode_id == 1){
                    spanvalue = 'bidding';
                    spanvalueclass = 'success';
                }
                return '<span class="badge badge-'+spanclass+' rounded-0">'+data.project_documents_status+'</span><span class="badge badge-'+spanvalueclass+' rounded-0">'+spanvalue+'</span>';
            }
        },
        {
            "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0 border-right-0',
            "data": "created_at",
            "render": function(data, type, row){
                return $.date(data);
            }
        },
        {
            "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0 border-right-0',
            "data": "sender_name"
        },
        {
            "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0 border-right-0',
            "data": "receiver_name"
        },
        {
            "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0 border-right-0',
            "data": {
                document_type: 'document_type',
                id: 'id'
            },
            "render": function(data, type, row){
                return data.document_type;
            }
        },
        {
            "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0 border-right-0',
            "data": {
                documentary_classification: 'documentary_classification'
            },
            "render": function(data, type, row){
                return data.documentary_classification;
            }
        },
        {
            "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0 border-right-0',
            "data": {
                project_title: 'project_title',
                mode_id: 'mode_id',
                plan_id: 'plan_id'
            },
            "width": '0%',
            "render": function(data, type, row){
                return data.project_title;
            }
        },
        {
            "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0 border-right-0',
            "data": {
                business_name: 'business_name',
                contractor_id: 'contractor_id'
            },
            "render": function(data, type, row){
                var business_name = '';
                if(data.business_name != null) business_name = data.business_name;
                else business_name = 'No contractor';
                return business_name;
            }
        },
        {
            "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0 border-right-0',
            "data": "process_name"
        },
        {
            "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0 border-right-0',
            "data": {
                abc: 'abc'
            },
            "render": function(data, type, row){
                return '&#8369;'+$.number(data.abc, 2);
            }
        },
        {
        "className": 'pt-1 pb-0 pl-1 pr-1 m-0  border-top-0 border-bottom-0',
        "data": {
            business_name: 'business_name',
            bid_docs_proposed: 'bid_docs_proposed',
            rfq_proposed_bid: 'rfq_proposed_bid'
        },
        "render": function(data, type, row){
            var dat = 'No Proposed Bid Yet';
            if(data.business_name != null){
                if(data.bid_docs_proposed != null){
                    dat = '&#8369;'+$.number(data.bid_docs_proposed, 2);
                }
                if(data.rfq_proposed_bid != null){
                    dat = '&#8369;'+$.number(data.rfq_proposed_bid, 2);
                }
            }
            return dat;
        }
        }
    ],
    "lengthChange": false,
    "searching": true,
    "pagingType": "full_numbers",
    "language": {
        "zeroRecords": "Can't find the record you're looking for? Click here to <a href='#' type='button' class='btn no-border btn-sm'><i class='fa fa-file-signature'></i> Add Documents to Track</a>",
        "processing": 'Loading',
        "oPaginate": {
        "sNext": '<i class="fa fa-caret-right"></i>', // or '→'
        "sPrevious": '<i class="fa fa-caret-left"></i>' // or '←'
        }
    },
    "columnDefs":[
        {
            "targets": [-1,0,1,2,3,4,5,6,7],
            "orderable": false,
        },
        {
            'targets': 0,
            'checkboxes': {
                'selectRow': true
            }
        },
        {
            "target": [1],
            "visible": false
        }
    ],
    "ordering": false,
    "orderCellsTop": true,
    "fixedHeader": true,
    "aLengthMenu": [[15,100,150,200,250,-1], [15,100,150,200,250,"All"]],
    "sDom": '<"top"Pr>rt<"bottom">p<"clear">',
    initComplete: function(settings, json){
        $('#showall_paginate').appendTo('#custom-pagination');
        //$('#project_title_toggle').click();
        table.column(7).visible(false);
        table.columns.adjust().draw( false );
    },
    drawCallback: function(settings){
        $('#custom-pagination').children().not(":nth-last-child(1)").remove();
        $('#showall_paginate').appendTo('#custom-pagination');
    },
    rowGroup: [6],
    rowGroup: {
        className: 'bg-gradient-info text-dark ',
        dataSrc: 'project_title',
        startRender: function ( rows, group ) {
            // Assign class name to all child rows
            var groupName = 'group-' + group.replace(/[^A-Za-z0-9]/g, '');
            var rowNodes = rows.nodes();
            rowNodes.to$().addClass(groupName);

            // Get selected checkboxes
            var checkboxesSelected = $('.dt-checkboxes:checked', rowNodes);

            // Parent checkbox is selected when all child checkboxes are selected
            var isSelected = (checkboxesSelected.length == rowNodes.length);

            var html = '<div class="form-check">';
            html += '<input type="checkbox" class="form-check-input group-checkbox" id="exampleCheck1" data-group-name="'+ groupName +'"'+ (isSelected ?'checked':'')+'" style="margin-top: 1px!important;">';
            html += '<label class="form-check-label" for="exampleCheck1"><strong>'+group +'</strong> <span class="badge badge-primary">'+rows.count()+'</span></label>';
            html += '</div>';
            return html;
            // return '<input type="checkbox" class="group-checkbox form-control" data-group-name="'+ groupName +'"'+ (isSelected ?'checked':'') +'>'+
            // '<span class="text-white pb-0 mb-0">'+group +'('+rows.count()+')</span>';
            // return "<small class='text-dark'>Document Group: </small><p class='text-white pb-0 mb-0'>"+group +' ('+rows.count()+')</p>';
        }
    }
    });

    // Add event listener for opening and closing details
    $('#showall tbody').on('click', 'td.details-control', function (){
    var tr = $(this).closest('tr');
    var row = table.row( tr );

    if ( row.child.isShown() ) {
        row.child.hide();
        tr.removeClass('shown');
    }else {
        row.child( format(row.data()) ).show();
        tr.addClass('shown');
    }
    });
    $('#showall tbody').on( 'click', 'tr', function () {
    $(this).toggleClass('selected');
    });
    // $('#showall').DataTable().searchPanes.rebuildPane();
    $.fn.dataTableExt.oJUIClasses;
    $.fn.dataTableExt.oJUIClasses.sPaging = 'mx-auto text-center mt-0 w-50';
    $.fn.dataTable.ext.classes.sInfo = 'mx-auto text-center p-4';
    $.fn.dataTable.ext.classes.c = 'paginate_button btn btn-primary rounded-0 text-white mt-0 border text-center';
    $.fn.dataTable.ext.classes.sPageButtonActive = 'current btn rounded-0 text-white mt-0 border text-center';
    $.fn.dataTable.ext.classes.sPageButtonDisabled = 'btn btn-secondary rounded-0 text-white mt-0 border text-center';
    $.fn.dataTable.ext.classes.sInfo = 'dataTables_info btn btn-secondary rounded-0 text-white mt-0 border text-center';

    var document_type_filter = createFilter(table, [5], '#search_document_type');
    var project_title_filter = createFilter(table, [7], '#search_project_title');
    var contractor_filter = createFilter(table, [8], '#search_contractor');

    document_type_filter.appendTo("#document_type_group");
    project_title_filter.appendTo("#project_title_group");
    contractor_filter.appendTo("#contractor_group");
    //project_title_filter.appendTo("body");
}
function createFilter(table, columns, field) {
    var input = $(field).on("keyup", function() {
        table.draw();
    });
    $.fn.dataTable.ext.search.push(function(settings,searchData,index,rowData,counter){
        var val = input.val().toLowerCase();
        for (var i = 0, ien = columns.length; i < ien; i++) {
            if (searchData[columns[i]].toLowerCase().indexOf(val) !== -1) {
                return true;
            }
        }
        return false;
    });
    return input;
}
$("#clear_document_type").on('click', function(){
    $("#search_document_type").val('');
    $("#search_document_type").focus();
    var e = jQuery.Event("keypress");
    e.which = 8;
    $("#search_document_type").trigger(e);
});
$("#clear_project_title").on('click', function(){
    $("#search_project_title").val('');
    $("#search_project_title").focus();
    var e = jQuery.Event("keypress");
    e.which = 8;
    $("#search_project_title").trigger(e);
});
$("#clear_contractor").on('click', function(){
    $("#search_contractor").val('');
    $("#search_contractor").focus();
    var e = jQuery.Event("keypress");
    e.which = 8;
    $("#search_contractor").trigger(e);
});
$('a.toggle-vis').on('click', function (e){
    e.preventDefault();
    var column = table.column($(this).attr('data-column'));
    column.visible(! column.visible());
    table.columns.adjust().draw( false );
});
$('#showall').on('click', '.group-checkbox', function(e){
    var groupName = $(this).data('group-name');
    table.cells('tr.' + groupName, 0).checkboxes.select(this.checked);
});
// Handle click event on "Select all" checkbox
$('#showall').on('click', 'thead .dt-checkboxes-select-all', function(e){
    var $selectAll = $('input[type="checkbox"]', this);
    setTimeout(function(){
        // Select group checkbox based on "Select all" checkbox state
        $('.group-checkbox').prop('checked', $selectAll.prop('checked'));
    }, 0);
});
$('#next').on( 'click', function () {
    table.page( 'next' ).draw( 'page' );
} );

$('#previous').on( 'click', function () {
    table.page( 'previous' ).draw( 'page' );
} );
// individual column searching
// $('#showall thead tr').clone(true).appendTo('#showall thead');
// $('#showall thead tr:eq(1) td').each( function (i) {
//     var title = $(this).text();
//     $(this).attr('class', 'pb-0 pl-0 pr-0 pt-0 m-0');
//     if(i != 0 && i != 1 && i != 10) $(this).html('<input type="text" class="form-control p-2 rounded-0 h-100 w-100  border-right-0" style="border-left: solid 1px #aaaaaa; border-right: solid 1px #aaaaaa; font-size: 0.9em!important" placeholder="Search '+title+'" />');
//     if(i == 10) $(this).html('<input type="text" class="form-control p-2 rounded-0 h-100 w-100 " style="border-left: solid 1px #aaaaaa; border-right: solid 1px #aaaaaa; font-size: 0.9em!important" placeholder="Search '+title+'" />');
//     if(i == 0 || i == 1) $(this).html( '<input type="text" class="form-control p-2 rounded-0 h-100 w-100  border-right-0" style="border-left: solid 1px #aaaaaa; border-right: solid 1px #aaaaaa; font-size: 0.9em!important" disabled/>' );
//     $('input', this).on( 'keyup change', function () {
//         if ( table.column(i).search() !== this.value ) {
//             table.column(i).search( this.value ).draw();
//         }
//     });
// });
// table.column(7).visible(false);
table.columns.adjust().draw( false );
// $.fn.dataTable.ext.search.push(
//     function( settings, data, dataIndex ) {
//         var search_document_type = $("#search_document_type").val().toLowerCase();
//         var search_project_title = $("#search_project_title").val().toLowerCase();
//         var search_contractor = $("#search_contractor").val().toLowerCase();
//         if(data[5].toLowerCase().indexOf(search_document_type) !== -1){
//             return true;
//         }
//         if(data[8].toLowerCase().indexOf(search_project_title) !== -1){
//             return true;
//         }
//         return false;
//     }
// );
$("div.toolbar").html('<span>Custom tool bar! Text/images etc.</span>');
$.receive_documents = function(){
    $.confirm({
    theme: 'modern',
    icon: 'fa fa-calendar-check text-success',
    title: 'Receive Documents',
    content: 'Do you want to receive these/this document/s?',
    autoClose: 'Cancel|10000',
    buttons: {
        Yes:{
        text: 'Receive',
        btnClass: 'btn-green',
        action: function(){
            var id = Array();
            var i = 0;
            var totaldocuments = table.rows('.selected').data().length;
            var html = '<div class="table-responsive">';
            html += '<table class="table table-bordered" style="width: 100%!important;">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>Document</th>';
            html += '<th>Project:</th>';
            html += '<th>Contractor:</th>';
            html += '<th>Sent By:</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            if(totaldocuments > 0){
            table.rows('.selected').every( function () {
                var d = this.data();
                html += '<tr>';
                html += '<td class="text-left">'+d.document_type + '</td>';
                html += '<td class="text-left">'+d.project_title + '</td>';
                html += '<td class="text-left">'+d.business_name + '</td>';
                html += '<td class="text-left">'+d.sender_name + '</td>';
                html += '</tr>';
                id.push(d.id);
                i++;
                d.counter++; // update data source for the row
                this.invalidate(); // invalidate the data DataTables has cached for this row
            });
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            // Draw once all updates are done
            table.draw();
            $.confirm({
                theme: 'modern',
                icon: 'fa fa-search text-info',
                title: 'Check Documents to be Received',
                columnClass: 'col-lg-12',
                useBootstrap: true,
                content: html,
                buttons: {
                Confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-green',
                    action: function(){
                    $.confirm({
                        theme: 'modern',
                        icon: 'fa fa-check text-success',
                        title: 'Recieved',
                        autoClose: 'Ok|15000',
                        content: 'Documents has been successfully Received!',
                        buttons: {
                        Ok: {
                            text: 'Ok',
                            btnClass: 'btn-green'
                        }
                        },
                    });
                    var src = 'receive';
                    return $.ajax({
                        url: '/managedocuments',
                        dataType: 'json',
                        method: 'get',
                        data: {id:id,src:src},
                    }).done(function (response) {

                    }).fail(function(){
                        //$.alert('Something went wrong!')
                        // self.setContentAppend('<div>Fail!</div>');
                    });
                    // var self = this;
                    // self.setContent(html);

                    }
                },
                Cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-red',
                    action: function(){
                    $.confirm({
                        theme: 'modern',
                        icon: 'fa fa-trash-alt text-danger',
                        title: 'Canceled!',
                        autoClose: 'Ok|15000',
                        content: 'Receiving has been Canceled',
                        buttons: {
                        Ok: {
                            text: 'Ok',
                            btnClass: 'btn-green'
                        }
                        }
                    });
                    }
                }
                },
                onDestroy: function(){
                reinitialize_table();
                }
            });
            }else{
            $.confirm({
                theme: 'modern',
                icon: 'fa fa-times text-danger',
                title: 'Invalid!',
                autoClose: 'Ok|15000',
                content: 'You haven\'t selected any Documents to be Received.',
                buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-green'
                }
                }
            });
            }
        },
        },
        Cancel: function () {
        $.confirm({
            theme: 'modern',
            icon: 'fa fa-times text-danger',
            title: 'Canceled!',
            autoClose: 'Ok|15000',
            content: 'Receiving has been Canceled',
            buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-green'
            }
            }
        });
        }
    }
    });
}

// !- Contractor Autocomplete Initialization
var contractor_autocomplete_init = {
    minLength: 0,
    autocomplete: true,
    select: function(event, ui){
        if(ui.item.id != ''){
            $(this).val(ui.item.value);
        }else{
            $(this).val('');
        }
        return false;
    },
    source: function(request, response){
        $.ajax({
            'url': '/getcontractors',
            'data': {
                "_token": "{{ csrf_token() }}",
                "term" : request.term
            },
            'method': "get",
            'dataType': "json",
            'success': function(data) {
                response(data);
            }
        });
    },
    change: function (event, ui) {
        if (ui.item === null) {
            $(this).val('');
        }
    }
}
// -!
$.add_contractor_row = function(i){
    contractor_cnt++;
    class_number.push([i,contractor_cnt]);
    var html = '';
    html += '<div class="input-group">';
    html += '<div class="input-group-prepend w-10b">';
    html += '<p class="input-group-text rounded-0  border-right-0 text-dark w-100 border-top-0"><small>Add Contractor</small></p>';
    html += '</div>';
    html += '<input class="form-control rounded-0  border-top-0 contractor contractor'+contractor_cnt+' border-right-0 pl-2" type="text" name="contractor[]" placeholder="Enter Contractor" required>';
    html += '<div class="input-group-prepend">';
    html += '<button type="button" class="btn btn-danger border-0" onclick="$.remove_contractor_row(this)"><i class="fa fa-trash"></i></button>';
    html += '</div>';
    html += '</div>';
    $('#contractor_container'+i).append(html);
    $.activate_contractor_autocomplete(contractor_cnt);
    return contractor_cnt;
}
$.activate_contractor_autocomplete = function(contractor_cnt){
    $(".contractor"+contractor_cnt).autocomplete(contractor_autocomplete_init).focus(function() {
        $(this).autocomplete('search', $(this).val())
    });
    $.remove_contractor_row = function(elem){
        $(elem).parent('div').parent('div').remove();
    }
}

// !- Document Type Autocomplete Initialization
var document_type_autocomplete_init = {
    minLength: 0,
    autocomplete: true,
    select: function(event, ui){
        if(ui.item.id != ''){
            $(this).val(ui.item.value);
        }else{
            $(this).val('');
        }
        return false;
    },
    source: function(request, response){
        $.ajax({
            'url': '/getdocumenttypes?project_name='+encodeURIComponent(current_project),
            'data': {
                "_token": "{{ csrf_token() }}",
                "term" : request.term
            },
            'method': "get",
            'dataType': "json",
            'success': function(data) {
                response(data);
            }
        });
    },
    change: function (event, ui) {
        if (ui.item === null) {
            $(this).val('');
        }
    }
}
// -!
$.add_document_type_row = function(i, project_title){
    document_type_cnt++;
    document_type_class_number.push([i,document_type_cnt]);
    var html = '';
    html += '<div class="input-group input-sm">';
    html += '<div class="input-group-prepend w-10b">';
    html += '<p class="input-group-text rounded-0  border-right-0 text-dark w-100"><small>Add New Document</small></p>';
    html += '</div>';
    html += '<input onfocus="$.set_autocomplete(\''+project_title+'\')" class="form-control rounded-0  document_type document_type'+document_type_cnt+' pl-2 border-right-0 pt-0 pb-0" type="text" name="document_type[]" placeholder="Enter Document Type">';
    html += '<div class="input-group-prepend">';
    html += '<button type="button" class="btn btn-danger border-0" onclick="$.remove_document_type_row(this)"><i class="fa fa-trash"></i></button>';
    html += '</div>';
    html += '</div>';
    $('#document_type_container'+i).append(html);
    $.set_project_title(project_title);
    $.activate_document_type_autocomplete(document_type_cnt);
    return document_type_cnt;
}
$.set_autocomplete = function(project_title){
    $.set_project_title(project_title);
    $.activate_document_type_autocomplete(document_type_cnt);
}
$.set_project_title = function(project_title){
    current_project = project_title;
    return current_project;
}
$.activate_document_type_autocomplete = function(document_type_cnt){
    $(".document_type"+document_type_cnt).autocomplete(document_type_autocomplete_init).focus(function() {
        $(this).autocomplete('search', $(this).val())
    });
    $.remove_document_type_row = function(elem){
        $(elem).parent('div').parent('div').remove();
    }
}

var contractor_cnt = 0;
var contractor_batch_counter = 0;
var contractor_cnt_first_load = 0;
var document_type_cnt = 0;
var document_type_batch_counter = 0;
var document_type_cnt_first_load = 0;
var class_number = Array();
var forwarddata = Array();
var document_type_class_number = Array();
var document_type_forwarddata = Array();
var current_project = '';
$.forward_documents = function(){
    $.confirm({
    theme: 'modern',
    icon: 'fa fa-envelope-open-text text-info',
    title: 'Forward Documents',
    content: 'Do you want to forward these/this document/s?',
    autoClose: 'Cancel|10000',
    buttons: {
        Yes:{
        text: 'Forward',
        btnClass: 'btn-blue',
        action: function(){
            var id = Array();
            var i = 0;
            var totaldocuments = table.rows('.selected').data().length;
            var html = '';
            html += '<div class="w-50">';
            html += '<div class="input-group">';
            html += '<div class="input-group-prepend w-20">';
            html += '<p class="input-group-text rounded-0  border-right-0 text-dark w-100"><small>Recipient</small></p>';
            html += '</div>';
            html += '<input class="form-control rounded-0  recipient pl-2" id="recipient" type="text" name="recipient" placeholder="Enter Recipient">';
            html += '</div>';
            html += '</div>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-bordered" style="width: 100%!important;">';
            html += '<tbody>';
            if(totaldocuments > 0){
            var project_data = new Array();
            var project_data_duplicates = new Array();
            $.each(table.rows('.selected').data(), function (index, value) {
                if(!project_data_duplicates[[value.project_title,value.business_name,value.process_name,value.mode_id]]){
                    project_data_duplicates[[value.project_title,value.business_name,value.process_name,value.mode_id]] = true;
                    project_data.push([value.project_title,value.business_name,value.process_name,value.mode_id]);
                }
            });
            $.each(project_data, function (index, value){
                var mode_of_procurement;
                if(value[3] == 1) mode_of_procurement = 'bidding';
                else mode_of_procurement = 'svp';
                html += '<tr>';
                html += '<td>';
                html += '</td>';
                html += '</tr>';
                html += '<tr class="bg-gradient-info">';
                html += '<td class="text-left text-white" colspan="5">Project Title: '+value[0]+' ('+mode_of_procurement+')</td>';
                html += '</tr>';
                if(value[1] != null){
                    html += '<tr class="bg-gradient-success">';
                    html += '<td class="text-left text-white" colspan="5">Contractor: '+value[1]+'</td>';
                    html += '</tr>';
                    // document_type_forwarddata.push([value[0], value[1], document_type_batch_counter, value[2]]);
                }else{
                    html += '<tr class="bg-gradient-warning">';
                    html += '<td colspan="5" class="p-1">';
                    html += '<div id="contractor_container'+contractor_batch_counter+'">';
                    html += '<div class="input-group input-sm">';
                    html += '<div class="input-group-prepend w-10b">';
                    html += '<p class="input-group-text rounded-0  border-right-0 text-dark w-100"><small>Add Contractor</small></p>';
                    html += '</div>';
                    html += '<input class="form-control rounded-0  contractor contractor'+contractor_cnt_first_load+' pl-2 border-right-0 pt-0 pb-0" type="text" name="contractor[]" placeholder="Enter Contractor">';
                    html += '<div class="input-group-prepend">';
                    html += '<button type="button" class="btn btn-success border-0" onclick="$.add_contractor_row('+contractor_batch_counter+')"><i class="fa fa-plus"></i></button>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</td>';
                    html += '</tr>';
                    class_number.push([contractor_batch_counter, contractor_cnt_first_load]);
                    document_type_forwarddata.push([value[0], value[1], document_type_batch_counter, value[2]]);
                }
                html += '<tr>';
                html += '<th>Document</th>';
                html += '<th>Classification</th>';
                html += '<th>Procurement Process</th>';
                html += '<th>ABC</th>';
                html += '<th>Sent By:</th>';
                html += '</tr>';
                table.rows('.selected').every( function () {
                    var d = this.data();
                    if(d.project_title == value[0]){
                        if(d.business_name == value[1]){
                            html += '<tr>';
                            html += '<td class="text-left">'+d.document_type+'</td>';
                            html += '<td class="text-left">'+d.documentary_classification+ '</td>';
                            html += '<td class="text-left">'+d.process_name+'</td>';
                            html += '<td class="text-left">'+d.abc+ '</td>';
                            html += '<td class="text-left">'+d.sender_name+'</td>';
                            html += '</tr>';
                            forwarddata.push([d.id, contractor_batch_counter]);
                            id.push(d.id);
                        }
                    }
                    d.counter++; // update data source for the row
                    this.invalidate(); // invalidate the data DataTables has cached for this row
                });
                html += '<tr class="bg-gradient-warning">';
                html += '<td colspan="5" class="p-0">';
                html += '<div id="document_type_container'+document_type_batch_counter+'">';
                html += '<div class="input-group input-sm">';
                html += '<div class="input-group-prepend w-10b">';
                html += '<p class="input-group-text rounded-0  border-right-0 text-dark w-100"><small>Add New Document</small></p>';
                html += '</div>';
                html += '<input onfocus="$.set_autocomplete(\''+value[0]+'\')" class="form-control rounded-0  document_type document_type'+document_type_cnt_first_load+' pl-2 border-right-0 pt-0 pb-0" type="text" name="document_type[]" placeholder="Enter Document Type">';
                html += '<div class="input-group-prepend">';
                html += '<button type="button" class="btn btn-success border-0" onclick="$.add_document_type_row('+document_type_batch_counter+', \''+value[0]+'\')"><i class="fa fa-plus"></i></button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</td>';
                html += '</tr>';
                document_type_class_number.push([document_type_batch_counter, document_type_cnt_first_load]);

                contractor_batch_counter++;
                contractor_cnt_first_load++;
                document_type_batch_counter++;
                document_type_cnt_first_load++;
            });
            contractor_cnt = contractor_cnt_first_load;
            document_type_cnt = document_type_cnt_first_load;
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            // Draw once all updates are done
            table.draw();
            $.confirm({
                theme: 'modern',
                // icon: 'fa fa-search text-info',
                title: 'Check Documents to be Forwarded',
                columnClass: 'col-lg-12',
                useBootstrap: true,
                content: html,
                onContentReady: function(){
                    $(".recipient").autocomplete(recipient_autocomplete_init).focus(function() {
                        $(this).autocomplete('search', $(this).val())
                    });
                    $(".contractor").autocomplete(contractor_autocomplete_init).focus(function() {
                        $(this).autocomplete('search', $(this).val())
                    });
                    $(".document_type").autocomplete(document_type_autocomplete_init).focus(function() {
                        $(this).autocomplete('search', $(this).val())
                    });
                },
                buttons: {
                Confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-green',
                    action: function(){
                        var recipient = this.$content.find('.recipient').val();
                        var contractors = new Array();
                        var document_types = new Array();
                        $.each(class_number, function(index, value){
                            var contractor = $('.contractor'+value[1]).val();
                            if(contractor !== null){
                                contractors.push([contractor, value[0]]);
                            }
                        });
                        if(contractors.length === 0){
                            contractors.push([false, 0]);
                        }
                        $.each(document_type_class_number, function(index, value){
                            var document_type = $('.document_type'+value[1]).val();
                            if(document_type !== undefined && document_type !== null){
                                document_types.push([document_type, value[0]]);
                            }
                        });
                        if(!recipient){
                            $.alert('Please provide a valid recipient.');
                            return false;
                        }
                        if(!contractors){
                            $.alert('Please provide a valid Contractor.');
                            return false;
                        }
                        $.confirm({
                            theme: 'modern',
                            icon: 'fa fa-check text-success',
                            title: 'Sent',
                            autoClose: 'Ok|15000',
                            content: 'Documents has been successfully Forwarded!',
                            buttons: {
                            Ok: {
                                text: 'Ok',
                                btnClass: 'btn-green'
                            }
                            },
                        });
                        var src = 'forward';
                        return $.ajax({
                            url: '/managedocuments',
                            dataType: 'json',
                            method: 'get',
                            data: {id:id,forwarddata:forwarddata,src:src,recipient:recipient,contractors:contractors,document_type_forwarddata:document_type_forwarddata,document_types:document_types},
                        }).done(function (response) {

                        }).fail(function(){

                        });
                    }
                },
                Cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-red',
                    action: function(){
                    $.confirm({
                        theme: 'modern',
                        icon: 'fa fa-times text-danger',
                        title: 'Canceled!',
                        autoClose: 'Ok|15000',
                        content: 'Forwading has been Canceled',
                        buttons: {
                        Ok: {
                            text: 'Ok',
                            btnClass: 'btn-green'
                        }
                        }
                    });
                    }
                }
                },
                onDestroy: function(){
                reinitialize_table();
                }
            });
            }else{
            $.confirm({
                theme: 'modern',
                icon: 'fa fa-times text-danger',
                title: 'Invalid!',
                autoClose: 'Ok|15000',
                content: 'You haven\'t selected any Documents to be Forwarded.',
                buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-green'
                }
                }
            });
            }
        },
        },
        Cancel: function () {
        $.confirm({
            theme: 'modern',
            icon: 'fa fa-times text-danger',
            title: 'Canceled!',
            autoClose: 'Ok|15000',
            content: 'Receiving has been Canceled',
            buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-green'
            }
            }
        });
        }
    }
    });
}
$.end_documents = function(){
    $.confirm({
    theme: 'modern',
    icon: 'fa fa-flag text-info',
    title: 'End Document/s Transaction',
    content: 'Do you want to end these/this document/s\' transaction?',
    autoClose: 'Cancel|10000',
    buttons: {
        Yes:{
        text: 'End Transaction',
        btnClass: 'btn-blue',
        action: function(){
            var id = Array();
            var i = 0;
            var totaldocuments = table.rows('.selected').data().length;
            var html = '';
            html += '<div class="table-responsive">';
            html += '<table class="table table-bordered" style="width: 100%!important;">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>Document</th>';
            html += '<th>Project:</th>';
            html += '<th>Contractor:</th>';
            html += '<th>Sent By:</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            if(totaldocuments > 0){
            table.rows('.selected').every( function () {
                var d = this.data();
                html += '<tr>';
                html += '<td class="text-left">'+d.document_type + '</td>';
                html += '<td class="text-left">'+d.project_title + '</td>';
                html += '<td class="text-left">'+d.business_name + '</td>';
                html += '<td class="text-left">'+d.sender_name + '</td>';
                html += '</tr>';
                id.push(d.id);
                i++;
                d.counter++; // update data source for the row
                this.invalidate(); // invalidate the data DataTables has cached for this row
            });
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            // Draw once all updates are done
            table.draw();
            $.confirm({
                theme: 'modern',
                icon: 'fa fa-search text-info',
                title: 'Check Documents Transactions to be Ended',
                columnClass: 'col-lg-12',
                useBootstrap: true,
                content: html,
                onContentReady: function(){
                //
                },
                buttons: {
                Confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-green',
                    action: function(){
                    $.confirm({
                        theme: 'modern',
                        icon: 'fa fa-check text-success',
                        title: 'Document Transaction Ended',
                        autoClose: 'Ok|15000',
                        content: 'Documents Transaction has been successfully Ended!',
                        buttons: {
                        Ok: {
                            text: 'Ok',
                            btnClass: 'btn-green'
                        }
                        },
                    });
                    var src = 'end';
                    return $.ajax({
                        url: '/managedocuments',
                        dataType: 'json',
                        method: 'get',
                        data: {id:id,src:src},
                    }).done(function (response) {

                    }).fail(function(){

                    });
                    }
                },
                Cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-red',
                    action: function(){
                    $.confirm({
                        theme: 'modern',
                        icon: 'fa fa-times text-danger',
                        title: 'Canceled!',
                        autoClose: 'Ok|15000',
                        content: 'Ending of Document Transaction has been Canceled',
                        buttons: {
                        Ok: {
                                text: 'Ok',
                                btnClass: 'btn-green'
                            }
                        }
                    });
                    }
                }
                },
                onDestroy: function(){
                reinitialize_table();
                }
            });
            }else{
            $.confirm({
                theme: 'modern',
                icon: 'fa fa-times text-danger',
                title: 'Invalid!',
                autoClose: 'Ok|15000',
                content: 'You haven\'t selected any Documents Transaction to be Ended.',
                buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-green'
                }
                }
            });
            }
        },
        },
        Cancel: function () {
        $.confirm({
            theme: 'modern',
            icon: 'fa fa-times text-danger',
            title: 'Canceled!',
            autoClose: 'Ok|15000',
            content: 'Ending of Document Transaction has been Canceled',
            buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-green'
            }
            }
        });
        }
    }
    });
}
$.discard_documents = function(){
    $.confirm({
    theme: 'modern',
    icon: 'fa fa-trash-alt text-danger',
    title: 'Unsend Documents',
    content: 'Do you want to unsend these/this document/s?',
    autoClose: 'Cancel|10000',
    buttons: {
        Yes:{
        text: 'Unsend',
        btnClass: 'btn-red',
        action: function(){
            var id = Array();
            var i = 0;
            var totaldocuments = table.rows('.selected').data().length;
            var html = '<div class="table-responsive">';
            html += '<table class="table table-bordered" style="width: 100%!important;">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>Document</th>';
            html += '<th>Project:</th>';
            html += '<th>Contractor:</th>';
            html += '<th>Sent By:</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            if(totaldocuments > 0){
            table.rows('.selected').every( function () {
                var d = this.data();
                html += '<tr>';
                html += '<td class="text-left">'+d.document_type + '</td>';
                html += '<td class="text-left">'+d.project_title + '</td>';
                html += '<td class="text-left">'+d.business_name + '</td>';
                html += '<td class="text-left">'+d.sender_name + '</td>';
                html += '</tr>';
                id.push(d.id);
                i++;
                d.counter++; // update data source for the row
                this.invalidate(); // invalidate the data DataTables has cached for this row
            });
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            // Draw once all updates are done
            table.draw();
            $.confirm({
                theme: 'modern',
                icon: 'fa fa-search text-info',
                title: 'Check Documents to be Unsent',
                columnClass: 'col-lg-12',
                useBootstrap: true,
                content: html,
                buttons: {
                Confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-red',
                    action: function(){
                    $.confirm({
                        theme: 'modern',
                        icon: 'fa fa-check text-success',
                        title: 'Unsent',
                        autoClose: 'Ok|15000',
                        content: 'Documents has been successfully Unsent!',
                        buttons: {
                        Ok: {
                            text: 'Ok',
                            btnClass: 'btn-green'
                        }
                        }
                    });
                    // var self = this;
                    // self.setContent(html);
                    var src = 'discard';
                    return $.ajax({
                        url: '/managedocuments',
                        dataType: 'json',
                        method: 'get',
                        data: {id:id,src:src}
                    }).done(function (response) {
                        //$.alert('Success!')
                        // self.setContentAppend('<div>Done!</div>');
                    }).fail(function(){
                        //$.alert('Something went wrong!')
                        // self.setContentAppend('<div>Fail!</div>');
                    });
                    }
                },
                Cancel: {
                    text: 'Cancel',
                    //btnClass: 'btn-red',
                    action: function(){
                    $.confirm({
                        theme: 'modern',
                        icon: 'fa fa-times text-danger',
                        title: 'Canceled!',
                        autoClose: 'Ok|15000',
                        content: 'Document Transaction has been Canceled',
                        buttons: {
                        Ok: {
                            text: 'Ok',
                            btnClass: 'btn-green'
                        }
                        }
                    });
                    }
                }
                },
                onDestroy: function(){
                reinitialize_table();
                }
            });
            }else{
            $.confirm({
                theme: 'modern',
                icon: 'fa fa-times text-danger',
                title: 'Invalid!',
                autoClose: 'Ok|15000',
                content: 'You haven\'t selected any Documents to be Received.',
                buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-green'
                }
                }
            });
            }
        },
        },
        Cancel: function () {
        $.confirm({
            theme: 'modern',
            icon: 'fa fa-times text-danger',
            title: 'Canceled!',
            autoClose: 'Ok|15000',
            content: 'Receiving has been Canceled',
            buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-green'
            }
            }
        });
        }
    }
    });
}

$.edit_document = function(){
    $.confirm({
    theme: 'modern',
    icon: 'fa fa-edit text-info',
    title: 'Edit Document',
    content: 'Do you want to edit this document?',
    autoClose: 'Cancel|10000',
    buttons: {
        Yes:{
        text: 'Edit',
        btnClass: 'btn-blue',
        action: function(){
            var id = Array();
            var i = 0;
            var totaldocuments = table.rows('.selected').data().length;
            var html = '';
            html += '<div class="row w-100">';
            html += '<div class="col-lg-6">';
            html += '<input class="form-control rounded-0  document_type" id="document_type" type="text" name="document_type" placeholder="Enter Document Type">';
            html += '</div>';
            html += '<div class="col-lg-6">';
            html += '<input class="form-control rounded-0  document_type" id="document_type" type="text" name="document_type" placeholder="Enter Document Type">';
            html += '</div>';
            html += '</div>';

            if(totaldocuments > 0){


            // Draw once all updates are done
            table.draw();
            $.confirm({
                theme: 'modern',
                icon: 'fa fa-edit text-info',
                title: 'Fill up fields to be edited',
                columnClass: 'col-lg-12',
                useBootstrap: true,
                content: html,
                onContentReady: function(){
                $(".recipient").autocomplete(recipient_autocomplete_init).focus(function() {
                    $(this).autocomplete('search', $(this).val())
                });
                },
                buttons: {
                Confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-green',
                    action: function(){
                    table.rows('.selected').every( function () {
                        var d = this.data();

                        var document_type = this.$content.find('.document_type').val(d.document_type);
                        id.push(d.id);
                        i++;
                        d.counter++; // update data source for the row
                        this.invalidate(); // invalidate the data DataTables has cached for this row
                    });
                    if(!document_type){
                        $.alert('Please provide a valid recipient.');
                        return false;
                    }
                    $.confirm({
                        theme: 'modern',
                        icon: 'fa fa-check text-success',
                        title: 'Recieved',
                        autoClose: 'Ok|15000',
                        content: 'Documents has been successfully Forwarded!',
                        buttons: {
                        Ok: {
                            text: 'Ok',
                            btnClass: 'btn-green'
                        }
                        },
                    });
                    var src = 'forward';
                    return $.ajax({
                        url: '/managedocuments',
                        dataType: 'json',
                        method: 'get',
                        data: {id:id,src:src,recipient:recipient},
                    }).done(function (response) {

                    }).fail(function(){

                    });
                    }
                },
                Cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-red',
                    action: function(){
                    $.confirm({
                        theme: 'modern',
                        icon: 'fa fa-times text-danger',
                        title: 'Canceled!',
                        autoClose: 'Ok|15000',
                        content: 'Forwading has been Canceled',
                        buttons: {
                        Ok: {
                            text: 'Ok',
                            btnClass: 'btn-green'
                        }
                        }
                    });
                    }
                }
                },
                onDestroy: function(){
                reinitialize_table();
                }
            });
            }else{
            $.confirm({
                theme: 'modern',
                icon: 'fa fa-times text-danger',
                title: 'Invalid!',
                autoClose: 'Ok|15000',
                content: 'You haven\'t selected any Documents to be Forwarded.',
                buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-green'
                }
                }
            });
            }
        },
        },
        Cancel: function () {
        $.confirm({
            theme: 'modern',
            icon: 'fa fa-times text-danger',
            title: 'Canceled!',
            autoClose: 'Ok|15000',
            content: 'Receiving has been Canceled',
            buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-green'
            }
            }
        });
        }
    }
    });
}
});

$.date = function(dateObject) {
var d = new Date(dateObject);
var day = d.getDate();
var month = d.getMonth() + 1;
var year = d.getFullYear();
var hour = d.getHours();
var minute = d.getMinutes();
if (day < 10) day = "0" + day;
if (month < 10) month = "0" + month;
if (hour < 10) hour = "0" + hour;
if (minute < 10) minute = "0" + minute;
var date = month + "/" + day + "/" + year + " " + hour + ":" + minute;
return date;
};
</script>
@endsection
