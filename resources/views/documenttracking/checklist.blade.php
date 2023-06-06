@extends($role==1 ? 'layouts.app' : 'layouts.app2');



@section('css')

<link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet" />
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
.w-5{
width: 5%!important;
}
.w-10{
width: 15%!important;
}
.w-25{
width: 25%!important;
}
.w-95{
width: 95%!important;
}
.w-80{
    width: 80%!important;
}
.form-control:focus,.custom-select:focus {
outline: none !important;
border: 1px solid rgb(0, 184, 40)!important;
box-shadow: 0 0 15px #719ECE!important;
}
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
table.dataTable td {
    word-break: break-word;
    vertical-align: middle;
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
    background-color: #222!important;
    color:rgb(255, 255, 255)!important;
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
                    <div class="card-header border border-info text-light rounded-0 pt-3 pl-4 pb-2">
                        <h5 class="text-dark">Project Document Checklist <span id="tab-title"></span></h5>
                    </div>
                    <div class="card-body pt-2 pl-4 pr-4 pb-2 border border-info border-top-0">
                        <div class="row">
                            <div class="col-lg-8">
                                {{-- <h5>Project Plans</h5> --}}
                                <table class="table table-striped display" width="100%" id="showall">
                                    <thead>
                                        <tr class="bg-secondary">
                                            {{-- <td class="text-center border border-info border-right-0 text-dark w-5 p-2"></td> --}}
                                            <td class="text-center border border-info border-right-0 text-dark pt-2 pb-2 w-10">Number</td>
                                            <td class="text-center border border-info border-right-0 text-dark p-2 w-80">Project Title</td>
                                            <td class="text-center border border-info text-dark p-2 w-10">ABC</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-secondary">
                                            {{-- <td class="text-center border border-info border-right-0 text-dark w-5 p-2"></td> --}}
                                            <td class="text-center border border-info border-right-0 text-dark p-2">Number</td>
                                            <td class="text-center border border-info border-right-0 text-dark p-2">Project Title</td>
                                            <td class="text-center border border-info text-dark p-2">ABC</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="pt-2">Document Checklist</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="project_document_checklist">
                                            <div class="jumbotron border border-info border-top-0 rounded-0 mb-1 p-0" style="height: 478px!important">
                                                <div class="input-group">
                                                    <input type="text" placeholder="Select Contractor" id="contractor" name="contractor" class="form-control rounded-0 contractor border border-info border-left-0 pt-3 pb-3" value="{{ old('contractor') }}" onkeyup="this.value = this.value.toUpperCase();" onkeypress="return /[a-z- - ]/i.test(event.key)">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-secondary border border-danger rounded-0 w-100" id="contractor_clear_btn" type="button" onclick="$.clearvalue(contractor_clear_btn)"><i class="fa fa-times text-danger"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <small>Completion Status: <span id="totallabel">0/100</span></small>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <div class="progress rounded-0 mb-0 border border-info border-top-0" style="width: 100%!important">
                                            <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" id="donetotalbar" role="progressbar" style="width: 0%; height: 55px!important;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            <div class="progress-bar progress-bar-striped bg-info progress-bar-animated" id="ongoingtotalbar" role="progressbar" style="width: 0%; height: 55px!important;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            <div class="progress-bar progress-bar-striped bg-danger progress-bar-animated" id="canceledtotalbar" role="progressbar" style="width: 0%; height: 55px!important;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            <div class="progress-bar progress-bar-striped bg-dark progress-bar-animated" id="notroutedtotalbar" role="progressbar" style="width: 0%; height: 55px!important;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <code class="text-dark">Generate Transmittal Documentary Requirement List</code>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="btn-group btn-block btn-block shadow-none">
                                            <button class="btn btn-secondary border border-light rounded-0 w-50 shadow-none" disabled="disabled" id="generateword"><i class="fa fa-file-word text-info"></i> <small>Word Format</small></button>
                                            <button class="btn btn-secondary border border-light rounded-0 w-50 shadow-none" disabled="disabled" id="generatepdf"><i class="fa fa-file-pdf text-danger"></i> <small>PDF Format</small></button>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 pr-0">
                                        <div class="card rounded-0 border border-light border-right-0 border-top-0">
                                            <div class="card-header p-2 bg-secondary rounded-0">
                                                <small><code class="text-dark">Done</code></small>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" id="donebar" style="width: 0%; height: 15px!important;" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="card-footer pr-2 pl-2 pt-1 pb-0">
                                                <small class="text-center"><h6 id="donelabel">0/44</h6></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-0 pl-0">
                                        <div class="card rounded-0 rounded-0 border border-light border-right-0 border-top-0">
                                            <div class="card-header p-2 bg-secondary rounded-0">
                                                <small><code class="text-dark">Ongoing</code></small>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="progress-bar progress-bar-striped bg-info progress-bar-animated" id="ongoingbar" style="width: 0%; height: 15px!important;" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="card-footer pr-2 pl-2 pt-1 pb-0">
                                                <small class="text-center"><h6 id="ongoinglabel">0/44</h6></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-0 pl-0">
                                        <div class="card rounded-0 rounded-0 border border-light border-right-0 border-top-0">
                                            <div class="card-header p-2 bg-secondary rounded-0">
                                                <small><code class="text-dark">Canceled</code></small>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="progress-bar progress-bar-striped bg-danger progress-bar-animated" id="canceledbar" style="width: 0%; height: 15px!important;" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="card-footer pr-2 pl-2 pt-1 pb-0">
                                                <small class="text-center"><h6 id="canceledlabel">0/44</h6></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <div class="card rounded-0 rounded-0 border border-light border-top-0">
                                            <div class="card-header p-2 bg-secondary rounded-0">
                                                <small><code class="text-dark">Not Routed</code></small>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="progress-bar progress-bar-striped bg-dark progress-bar-animated" id="notroutedbar" style="width: 0%; height: 15px!important;" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="card-footer pr-2 pl-2 pt-1 pb-0">
                                                <small class="text-center"><h6 id="notroutedlabel">0/44</h6></small>
                                            </div>
                                        </div>
                                    </div>
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
<script>
    $(document).ready(function(){
        var table;
        $.initializetable = function(){
            table = $('#showall').DataTable( {
            "ajax": "/getprojectplans?t=all&f=",
            "select": {
                style: 'single'
            },
            "autoWidth": false,
            "columns": [
                {
                    "className": 'pt-1 pb-1 pl-2 pr-2 m-0 w-10',
                    "width": "10%",
                    "data": {
                        project_no: 'project_no'
                    },
                    "render": function(data, type, row){
                        return data.project_no;
                    }
                },
                {
                    "className": 'pt-1 pb-1 pl-2 pr-2 m-0 w-80',
                    "width": "80%!important",
                    "data": {
                        project_title: 'project_title',
                        plan_id: 'plan_id'
                    },
                    "render": function(data, type, row){
                        return data.project_title;
                    }
                },
                {
                    "className": 'pt-1 pb-1 pl-2 pr-2 m-0 w-10',
                    "width": "10%",
                    "data": {
                        abc: 'abc'
                    },
                    "render": function(data, type, row){
                        return data.abc.toLocaleString('fil-PH', {maximumFractionDigits:2, style:'currency', currency:'PHP', useGrouping:true});;
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
                "targets": [-1,0,1,2],
                "orderable": false,
                },
                {
                'targets': 0,
                'checkboxes': {
                    'selectRow': true
                }
                }
            ],
            "ordering": false,
            "orderCellsTop": true,
            "fixedHeader": true,
            "aLengthMenu": [[20,100,150,200,250,-1], [20,100,150,200,250,"All"]],
            //"sDom": '<"top"Pr>rt<"bottom"<"toolbar w-50">><"clear">p',
            "sDom": '<"top"Pr>rt<"bottom">p<"clear">',
            //"dom": 'frt<"toolbar">p'
            });
            $('#showall thead tr').clone(true).appendTo('#showall thead');
            $('#showall thead tr:eq(1) td').each( function (i) {
                var title = $(this).text();
                $(this).attr('class', 'pb-0 pl-0 pr-0 pt-0 m-0');
                if(i == 0 || i == 1) $(this).html('<input type="text" class="form-control p-2 rounded-0 h-100 w-100 border border-info border-right-0" style="border-left: solid 1px #aaaaaa; border-right: solid 1px #aaaaaa; font-size: 0.9em!important" placeholder="Search '+title+'" />');
                if(i == 2) $(this).html('<input type="text" class="form-control p-2 rounded-0 h-100 w-100 border border-info" style="border-left: solid 1px #aaaaaa; border-right: solid 1px #aaaaaa; font-size: 0.9em!important" placeholder="Search '+title+'" />');
                // if(i == 0 || i == 1) $(this).html( '' );
                $( 'input', this ).on( 'keyup change', function () {
                if ( table.column(i).search() !== this.value ) {
                    table.column(i).search( this.value ).draw();
                }
                });
            });
            // Add event listener for opening and closing details
            $('#showall tbody').on('click', 'td.details-control', function (){
            var tr = $(this).closest('tr');
            var row = table.row( tr );

            if ( row.child.isShown() ) {
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
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
        }

        $.initializetable();
        var documentlisttable;
        $('#showall tbody').on( 'click', 'tr', function () {
            if($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                var d = table.row( this ).data();
                d.counter++;
                var html = '';
                html += '<div class="jumbotron border border-info border-top-0 rounded-0 mb-1 p-0" style="height: 478px!important">';
                html += '<div class="input-group">';
                html += '<input type="text" placeholder="Select Contractor" id="contractor" name="contractor" class="form-control rounded-0 contractor border border-info border-left-0 pt-3 pb-3" value="{{ old('contractor') }}" onkeyup="this.value = this.value.toUpperCase();" onkeypress="return /[a-z- - ]/i.test(event.key)">';
                html += '<div class="input-group-append">';
                html += '<button class="btn btn-secondary border border-danger rounded-0 w-100" id="contractor_clear_btn" type="button" onclick="$.clearvalue(contractor_clear_btn)"><i class="fa fa-times text-danger"></i></button>';
                html += '</div>';
                html += '</div>';
                html += '<table class="table table-hover table-striped table-bordered border border-info border-top border-left-0 border-bottom-0 border-right-0 mt-0 border-top-0 w-100" id="document_list">';
                html += '<thead>';
                html += '<tr>';
                html += '<td class="pt--2 w-5">#</td>';
                html += '<td class="pt--2">Document Type</td>';
                html += '<td class="pt--2">Status</td>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                // $.each(data, function(key, value){
                //     html += '<tr>';
                //     html += '<td>'+value.document_type+'</td>';
                //     html += '<td>'+value.status+'</td>';
                //     html += '</tr>';
                // });
                html += '</tbody>';
                html += '</table>';
                html += '</div>';
                //$("#project_document_checklist").html('');
                $("#project_document_checklist").html(html);
                $.documentlist = function(){
                    documentlisttable = $("#document_list").dataTable({
                        "ajax": '/getdocumentchecklist?project_name='+encodeURIComponent(d.project_title)+'&for=table&contractor='+$('.contractor').val(),
                        "lengthChange": false,
                        "searching": true,
                        "scrollY": "395px",
                        "scrollX": false,
                        "scrollCollapse": false,
                        "scrollResize": true,
                        "select": false,
                        "language": {
                            "zeroRecords": "Can't find the record you're looking for? Click here to <a href='#' type='button' class='btn no-border btn-sm'><i class='fa fa-file-signature'></i> Add Documents to Track</a>",
                            "processing": 'Loading',
                            "oPaginate": {
                            "sNext": '<i class="fa fa-caret-right"></i>', // or '→'
                            "sPrevious": '<i class="fa fa-caret-left"></i>' // or '←'
                            }
                        },
                        "initComplete": function(settings, json) {
                            $('.dataTables_scrollBody thead tr').css({visibility:'collapse'});
                            $('.dataTables_scrollBody tfoot tr').css({visibility:'collapse'});
                        },
                        "columns": [
                            {
                                "className": 'pt-1 pb-1 pl-2 pr-2 m-0',
                                "data": 'document_number'
                            },
                            {
                                "className": 'pt-1 pb-1 pl-1 pr-1 m-0 w-95',
                                "data": {
                                    document_type: 'document_type',
                                    project_document_id: 'project_document_id'
                                },
                                "render": function(data, type, row){
                                    if(data.project_document_id != 0){
                                        var markup = '<a class="rounded-0 text-dark" target="_blank" href="/documenttracking/'+data.project_document_id+'" type="button">'+data.document_type+'</a>';
                                    }else{
                                        var markup = '<a class=rounded-0 text-dark" href="#" type="button">'+data.document_type+'</a>';
                                    }
                                    return markup;
                                }
                            },
                            {
                                "className": 'pt-1 pb-1 pl-2 pr-2 m-0 w-5 text-center',
                                "data": {
                                    status: 'status'
                                },
                                "render": function(data, type, row){
                                    return '<span><i class="'+data.status+'"></i></span>';
                                }
                            }
                        ],
                        "columnDefs":[
                            {
                            "targets": [-1,0,1],
                            "orderable": false,
                            },
                            {
                            'targets': 0,
                            'checkboxes': {
                                'selectRow': true
                            }
                            }
                        ],
                        "paging": false,
                        "ordering": false,
                        "orderCellsTop": true,
                        "fixedHeader": true,
                        "aLengthMenu": [[100,120,150,200,250,-1], [100,120,150,200,250,"All"]],
                        //"sDom": '<"top"Pr>rt<"bottom"<"toolbar w-50">><"clear">p',
                        "sDom": '<"top"Pr>rt<"bottom"><"toolbar">p<"clear">',
                    });
                }
                $.documentlist();
                $.ajax({
                    'url': '/getdocumentchecklist?project_name='+encodeURIComponent(d.project_title)+'&for=progress&contractor='+$('.contractor').val(),
                    'data': {
                        "_token": "{{ csrf_token() }}"
                    },
                    'method': "get",
                    'dataType': "json",
                    'success': function(data) {
                        // if(data.mode == 'svp'){
                        //     var divisor =
                        // }
                        $("#donelabel").text(data.done_cnt+"/"+data.document_cnt);
                        $("#donebar").attr('style', 'width: '+((data.done_cnt/data.document_cnt)*100).toFixed(2)+'%; height: 15px!important');
                        $("#ongoinglabel").text(data.ongoing_cnt+"/"+data.document_cnt);
                        $("#ongoingbar").attr('style', 'width: '+((data.ongoing_cnt/data.document_cnt)*100).toFixed(2)+'%; height: 15px!important');
                        $("#canceledlabel").text(data.unsent_cnt+"/"+data.document_cnt);
                        $("#canceledbar").attr('style', 'width: '+((data.unsent_cnt/data.document_cnt)*100).toFixed(2)+'%; height: 15px!important');
                        $("#notroutedlabel").text(data.notrouted_cnt+"/"+data.document_cnt);
                        $("#notroutedbar").attr('style', 'width: '+((data.notrouted_cnt/data.document_cnt)*100).toFixed(2)+'%; height: 15px!important');
                        $("#totallabel").text((data.done_cnt+data.ongoing_cnt)+"/"+data.document_cnt);
                        $("#donetotalbar").attr('style', 'width: '+((data.done_cnt/data.document_cnt)*100).toFixed(2)+'%; height: 15px!important');
                        $("#ongoingtotalbar").attr('style', 'width: '+((data.ongoing_cnt/data.document_cnt)*100).toFixed(2)+'%; height: 15px!important');
                        $("#canceledtotalbar").attr('style', 'width: '+((data.unsent_cnt/data.document_cnt)*100).toFixed(2)+'%; height: 15px!important');
                        $("#notroutedtotalbar").attr('style', 'width: '+((data.notrouted_cnt/data.document_cnt)*100).toFixed(2)+'%; height: 15px!important');
                    }
                });
                // $.destroythis = function(){
                //     documentlisttable.destroy();
                //     $.documentlist();
                // }
                var contractor_autocomplete_init = {
                    minLength: 0,
                    autocomplete: true,
                    select: function(event, ui){

                        console.log($('.contractor').val());
                        if(ui.item.id != ''){
                            $(this).val(ui.item.value);
                            //console.log(documentlisttable);
                            //documentlisttable.clear().draw();
                            //$.destroythis();
                        }else{
                            $(this).val('');
                        }
                        return false;
                    },
                    source: function(request, response){
                        $.ajax({
                            'url': '/getallcontractors',
                            'data': {
                                "_token" : "{{ csrf_token() }}",
                                "term" : request.term,
                                "project_title" : d.project_title
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
                $(".contractor").autocomplete(contractor_autocomplete_init).focus(function() {
                    $(this).autocomplete('search', $(this).val())
                });
                $("#generateword").attr('disabled', false);
                $("#generatepdf").attr('disabled', false);
                $("#generateword").attr('onclick', '$.generatedocumentchecklist("word", "'+(d.project_title)+'")');
                $("#generatepdf").attr('onclick', '$.generatedocumentchecklist("pdf", "'+(d.project_title)+'")');
            }else{
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                $("#generateword").attr('disabled', 'disabled');
                $("#generatepdf").attr('disabled', 'disabled');
            }
        } );
        $.generatedocumentchecklist = function(src, project_title){
            if(src == 'word'){
                $.ajax({
                    url: '/generatedocumentchecklist?src='+encodeURIComponent(src)+'&project_title='+encodeURIComponent(project_title),
                    method: 'get',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (data) {
                        var a = document.createElement('a');
                        var url = window.URL.createObjectURL(data);
                        a.href = url;
                        a.download = 'COATSVP.docx';
                        document.body.append(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                    }
                });
            }
            if(src == 'pdf'){
                $.ajax({
                    url: '/generatedocumentchecklist',
                    method: 'GET',
                    data: {src:encodeURIComponent(src), project_title:project_title},
                    dataType: 'json',
                    success: function(data){
                        var path = 'data:application/pdf;base64,'+data;
                        // console.log(path);
                        // var win = window.open(path, '_blank');
                        // win.focus();
                        // $("#display-mock").attr('style', 'display:none');
                        // var html = '<embed class="p-0 m-0" id="display-thisdoc" src="'+path+'" type="application/pdf" width="100%" height="950px" />';
                        // $("#display-document").attr('style', 'display:block');
                        // $("#display-document").html(html);
                        // $('#clearance_id').val(data[1]);

                        const win = window.open("","_blank");
                        let html = '';

                        html += '<html>';
                        html += '<body style="margin:0!important">';
                        html += '<embed width="100%" height="100%" src="'+path+'" type="application/pdf" />';
                        html += '</body>';
                        html += '</html>';

                        setTimeout(() => {
                            win.document.write(html);
                        }, 0);
                    }
                });
            }
        }

        $.clearvalue = function(id){
            if(id.id == 'contractor_clear_btn'){
                $('#contractor').val('');
            }
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
