@extends($role==1 ? 'layouts.app' : 'layouts.app2');


@section('css')
    {{-- <link href="{{ asset('assets/select2/dist/css/select2.min.css') }}" rel="stylesheet" /> --}}
    {{-- <link href="{{ asset('css/select2-bootstrap.min.css') }}" rel="stylesheet" /> --}}
    <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet" />
    <!-- <link href="{{ asset('argon/vendor/jquery/dist/jquery-ui.min.css') }}" rel="stylesheet" /> -->
    <!-- <link href="{{ asset('argon/vendor/dropzone/dist/dropzone.css') }}" rel="stylesheet" /> -->
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
        .w-94{
            width: 94%!important;
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
        table.table.dataTable.floatThead-table {
            margin-top: 0px !important;
        }
        .bottomarrow{
            border-right: solid 50px transparent;
            border-left: solid 50px transparent;
            border-top: solid 50px #e15915;
            transform: translateX(-50%);
            position: absolute;
            z-index: -1;
            content: '';
            top: 100%;
            left: 50%;
            height: 0;
            width: 0;
        }
        .dataTables_scrollBody{
            overflow-x:hidden !important;
            overflow-y:auto !important;
        }
        /* .nav>li>a.active,
        .nav>li>a:hover,
        .nav>li>a:focus {
            background-color: #222!important;
            color:rgb(255, 255, 255)!important;
        } */
    </style>
@endsection
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-4">
    <div class="fade-in">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header  text-light rounded-0 pt-3 pl-3 pb-3">
                                <h2 class="text-dark mb-0">Document Tracking Settings</h2>
                            </div>
                            <div class="card-body p-0  border-top-0">
                                <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <li class="nav-item p-0">
                                        <a class="nav-link active rounded-0 border-0" id="v-pills-documentary-requirements-tab" data-toggle="pill" href="#v-pills-documentary-requirements" role="tab" aria-controls="v-pills-documentary-requirements" aria-selected="true">Documentary Requirements</a>
                                    </li>
                                    <li class="nav-item p-0">
                                        <a class="nav-link rounded-0 border-0" id="v-pills-process-documents-tab" data-toggle="pill" href="#v-pills-process-documents" role="tab" aria-controls="v-pills-process-documents" aria-selected="false">Process Documents</a>
                                    </li>
                                    {{-- <li class="nav-item p-0">
                                        <a class="nav-link rounded-0 border-0" id="v-pills-notifications-tab" data-toggle="pill" href="#v-pills-notifications" role="tab" aria-controls="v-pills-messages-notifications" aria-selected="false">Notifications</a>
                                    </li>
                                    <li class="nav-item p-0">
                                        <a class="nav-link rounded-0 border-0" id="v-pills-activity-log-tab" data-toggle="pill" href="#v-pills-activity-log" role="tab" aria-controls="v-pills-settings-activity-log" aria-selected="false">Activity Log</a>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header  text-light rounded-0 pt-3 pl-3 pb-3">
                                <h5 class="text-dark mb-0">Settings > <span id="settings-title">Documentary Requirements</span></h5>
                            </div>
                            <div class="card-body pt-3 pl-3 pr-3 pb-2  border-top-0">
                                {!! Form::open(['action' => 'DocumentTrackingController@savesettings', 'enctype' => 'multipart/data', 'files' => 'true', 'method' => 'POST', 'id' =>'createform', 'autocomplete' => 'off', 'name' => 'createform']) !!}
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="tab-content" id="v-pills-tabContent">
                                            <div class="tab-pane fade show active" id="v-pills-documentary-requirements" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text rounded-0  bg-secondary ">
                                                                    <small class="text-dark">Mode of Procurement</small>
                                                                </div>
                                                            </div>
                                                            <select id="project-type" name="project-type" class="custom-select rounded-0 small text-dark  small" requied>
                                                                <option value="svp">Small Value Procurement</option>
                                                                <option value="bidding">Bidding</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-2" id="filter_selected">
                                                    <div class="col-lg-12">
                                                        <div id="add-document-form">
                                                            <div class="btn-group" style='height: calc(2.55rem + 2px)!important;'>
                                                                <button type="button" class="btn btn-secondary  bg-secondary rounded-0 border-top-0" id="receive_selected" onclick="$.showform()"><i class="fa fa-plus text-success"></i></button>
                                                                <button type="button" class="btn btn-secondary  bg-secondary rounded-0 border-top-0" id="receive_selected" onclick="$.enableselected()"><i class="fa fa-check text-info"></i><small>Enable Selected</small></button>
                                                                <button type="button" class="btn btn-secondary  bg-secondary rounded-0 border-top-0" id="receive_selected" onclick="$.disableselected()"><i class="fa fa-trash-alt text-danger"></i><small>Disable Selected</small></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 table-responsive" id="items_table">
                                                        <table class="table table-striped display w-100" id="documentlist">
                                                            <thead>
                                                                <tr class="bg-secondary">
                                                                    <td class="text-center  border-right-0 text-dark p-2">#</td>
                                                                    <td class="text-center  border-right-0 text-dark p-2">Document Name</td>
                                                                    <td class="text-center  border-right-0 text-dark p-2">Document Classification</td>
                                                                    <td class="text-center  border-right-0 text-dark p-2">Date Created</td>
                                                                    <td class="text-center  border-right-0 text-dark p-2">Date Updated</td>
                                                                    <td class="text-center  text-dark p-2">Status</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr class="bg-secondary">
                                                                    <td class="text-center  border-right-0 text-dark p-2">#</td>
                                                                    <td class="text-center  border-right-0 text-dark p-2">Document Name</td>
                                                                    <td class="text-center  border-right-0 text-dark p-2">Document Classification</td>
                                                                    <td class="text-center  border-right-0 text-dark p-2">Date Created</td>
                                                                    <td class="text-center  border-right-0 text-dark p-2">Date Updated</td>
                                                                    <td class="text-center  text-dark p-2">Status</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="v-pills-process-documents" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text rounded-0 ">
                                                                    <small class="text-dark">Select Mode of Procurement</small>
                                                                </div>
                                                            </div>
                                                            <select id="project-type2" name="project-type2" class="custom-select rounded-0 small text-dark  small" requied>
                                                                <option value="svp" selected>Small Value Procurement</option>
                                                                <option value="bidding">Bidding</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 table-responsive" id="items_table">
                                                        <table class="table display w-100" id="processlist">
                                                            <thead>
                                                                <tr>
                                                                    <td class=" text-dark p-2">Procurement Processes</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td class=" text-dark p-2">Procurement Processes</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="v-pills-notifications" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        Notifications
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="v-pills-activity-log" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <table class="table display w-100" id="activity_log">
                                                            <thead>
                                                                <tr>
                                                                    <td class=" text-dark p-2">Activity</td>
                                                                    <td class=" text-dark p-2">Data/Time Commited</td>
                                                                    <td class=" text-dark p-2">By:</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td class=" text-dark p-2">Activity</td>
                                                                    <td class=" text-dark p-2">Data/Time Commited</td>
                                                                    <td class=" text-dark p-2">By:</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
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
    {{-- <script src="{{ asset('argon/vendor/jquery/dist/jquery.min.js') }}"></script> --}}
    <script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
    {{-- <script src="{{ asset('argon/vendor/jquery/dist/jquery-ui.min.js') }}"></script> --}}
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/additional-methods.min.js') }}"></script>
    <script type="text/javascript">
        var curr_row = 0;
        var curr_col = 1;
        $(document).ready(function (){
            var table;
            var table2;
            var currenttab;
            var currenttabid;
            // $('#items_table').formNavigation();
            filter_table();
            table2.search($('#project-type2').val()).draw();
            $('a[data-toggle="pill"]').on('show.bs.tab', function(e) {
                localStorage.setItem('activetab', $(e.target).attr('href'));
                currenttab = $(e.target).attr("href");
                currenttabid = $(e.target).attr("id");
                table.destroy();
                table2.destroy();
                filter_table(currenttabid);
            });
            var activetab = localStorage.getItem('activetab');
            if(activetab){
                $('#v-pills-tab a[href="' + activetab + '"]').tab('show');
            }
            function reinitialize_table(t){
                table.destroy();
                table2.destroy();
                console.log(t);
                filter_table(currenttabid, t);
                if(t && t != 'undefined'){
                    table2.search($('#project-type2').val()).draw();
                }else{
                    table2.search('svp').draw();
                }

            }
            $("#project-type").on('change', function(){
                reinitialize_table($(this).val());
            });

            $("#project-type2").on('change', function(){
                reinitialize_table($(this).val());
            });

            function filter_table(currenttabid, t){
                var filter;
                var project_type = $("#project-type").val();
                if(currenttabid && currenttabid != 'v-pills-documentary-requirements-tab'){
                    filter = currenttabid;
                }else{
                    filter = 'v-pills-documentary-requirements-tab';
                }
                if(filter){
                    if(filter == 'v-pills-documentary-requirements-tab'){
                        $('#settings-title').text('Documentary Requirements');
                    }else if(filter == 'v-pills-process-documents-tab'){
                        $('#settings-title').text('Process Documents');
                    }else if(filter == 'v-pills-notifications-tab'){
                        $('#settings-title').text('Notifications');
                    }else if(filter == 'v-pills-activity-log-tab'){
                        $('#settings-title').text('Activity Log');
                    }
                }

                table = $('#documentlist').DataTable({
                    "ajax": {
                        url: "/getdocumentslist?t="+t+"&f="+filter
                    },
                    "scrollY": "500px",
                    "scrollCollapse": false,
                    "scrollResize": true,
                    "select": {
                        style: 'multi'
                    },
                    "initComplete": function(settings, json) {
                        $('.dataTables_scrollBody thead tr').css({visibility:'collapse'});
                        $('.dataTables_scrollBody tfoot tr').css({visibility:'collapse'});
                    },
                    "columns": [
                        {
                            "className": 'p-0 m-0 text-center',
                            "data": 'document_number'
                        },
                        {
                            "className":        'pt-0 pb-0 pl-1 pr-1 m-0',
                            "data": {
                                document_type: "document_type"
                            },
                            "render": function(data, type, row){
                                return data.document_type;
                            }
                        },
                        {
                            "className":        'pt-0 pb-0 pl-1 pr-1 m-0',
                            "data": {
                                document_type: "documentary_classification"
                            },
                            "render": function(data, type, row){
                                return data.documentary_classification;
                            }
                        },
                        {
                            "className":        'pt-0 pb-0 pl-1 pr-1 m-0',
                            "data": "created_at",
                            "render": function(data, type, row){
                                return $.date(data);
                            }
                        },
                        {
                            "className":        'pt-0 pb-0 pl-1 pr-1 m-0',
                            "data": "updated_at",
                            "render": function(data, type, row){
                                return $.date(data);
                            }
                        },
                        {
                            "className":        'pt-0 pb-0 pl-1 pr-1 m-0 text-center',
                            "data": {
                                status: "document_status",
                                project_type: "project_type"
                            },
                            "render": function(data, type, row){
                                var html = '';
                                if(data.project_type == 'svp'){
                                    html += '<span class="badge badge-primary rounded-0">'+data.project_type+'</span>';
                                }else{
                                    html += '<span class="badge badge-info rounded-0">'+data.project_type+'</span>';
                                }
                                if(data.document_status == 'active'){
                                    html += '<span class="badge badge-success rounded-0">'+data.document_status+'</span>';
                                }else{
                                    html += '<span class="badge badge-danger rounded-0">'+data.document_status+'</span>';
                                }
                                return html;
                            }
                        },
                    ],
                    "lengthChange": false,
                    "searching": true,
                    //"pagingType": "full_numbers",
                    "paging": false,
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
                            "targets": [-1,0,1,2,3],
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
                    "aLengthMenu": [[50,100,150,200,250,-1], [50,100,150,200,250,"All"]],
                    //"sDom": '<"top"Pr>rt<"bottom"<"toolbar w-50">><"clear">p',
                    "sDom": '<"top"Pr>rt<"bottom"><"toolbar">p<"clear">',
                });

                // Add event listener for opening and closing details
                $('#documentlist tbody').on('click', 'td.details-control', function (){
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
                $('#documentlist tbody').on( 'click', 'tr', function () {
                    $(this).toggleClass('selected');
                });
                var project_type = $("#project-type2").val();
                if(project_type == 'undefined') project_type = 'svp';
                // Process Documents
                table2 = $('#processlist').DataTable({
                    "ajax": {
                        url: "/getprocesseslist?project_type="+project_type+"&f="+filter
                    },
                    "scrollY": "500px",
                    "scrollCollapse": false,
                    "scrollResize": true,
                    "select": false,
                    "initComplete": function(settings, json) {
                        $('.dataTables_scrollBody thead tr').css({visibility:'collapse'});
                        $('.dataTables_scrollBody tfoot tr').css({visibility:'collapse'});
                    },
                    "columns": [
                        {
                            "className": 'pt-2 pb-2 pl-0 pr-0 m-0',
                            "data": {
                                id: "id",
                                process_name: "process_name",
                                mode_of_procurement: "mode_of_procurement",
                                status: "status",
                                documents: "documents"
                            },
                            "render": function(data, type, row){
                                var html = '<div class="card rounded-0 ">';
                                html += '<div class="card-header p-2  border-top-0 border-left-0 border-right-0 bg-secondary rounded-0">';
                                html += '<div class="row">';
                                html += '<div class="col-lg-12"><b>';
                                html += data.process_name;
                                html += '</b></div>';
                                html += '</div>';
                                html += '<div class="row">';
                                html += '<div class="col-lg-12">';
                                html += 'Mode of Procurement: '+data.mode_of_procurement;
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '<div class="card-body bg-secondary p-0">';
                                if(data.documents && data.documents.length > 0){
                                    html += '<table class="table border-0">';
                                    html += '<thead>';
                                    html += '<tr>';
                                    html += '<td class="w-5"></td>';
                                    html += '<td class="w-94"></td>';
                                    html += '</tr>';
                                    html += '</thead>';
                                    html += '<tbody>';
                                    for(var i = 0; i < data.documents.length; i++) {
                                        if(data.documents[i].document_type != null){
                                            html += '<tr>';
                                            html += '<td class="p-0">';
                                            html += '<div class="btn-group">';
                                            // html += '<button class="btn btn-secondary  rounded-0 btn-sm"><i class="fa fa-edit text-success"></i></button>';
                                            html += '<button type="button" class="btn btn-secondary  border-left-0 border-top-0 border-bottom-0 rounded-0 btn-sm" onclick="$.deletedocuments(\''+data.documents[i].document_type+'\', \''+data.process_name+'\', \''+$('#project-type2').val()+'\')"><i class="fa fa-trash-alt text-danger"></i></button>';
                                            html += '</div>';
                                            html += '</td>';
                                            html += '<td class="pt-0 pb-0 pl-0 pr-0 ml-0 align-middle w-94">'+data.documents[i].document_type+'</td>';
                                            html += '</tr>';
                                        }else{
                                            html += '<tr>';
                                            html += '<td class="p-2">No documents included for the Process</td>';
                                            html += '</tr>';
                                        }
                                    }
                                    html += '</tbody>';
                                    html += '</table>';
                                }
                                html += '</div>';
                                html += '<div class="card-footer pl-0 pt-0 pb-0 pr-0  border-bottom-0 border-left-0 border-right-0">';
                                html += '<div id="add-documents-to-process'+data.id+'">';
                                html += '<div class="input-group">';
                                html += '<div class="input-group-prepend">';
                                html += '<button type="button" class="btn btn-secondary rounded-0 btn-sm  border-bottom-0 border-left-0 border-top-0" onclick="$.showadddocuments('+data.id+', \''+data.process_name+'\')"><i class="fa fa-plus text-success"></i></button>';
                                html += '</div>';
                                html += '<input type="text" class="form-control form-control-sm rounded-0 border border-0" disabled>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                return html;
                            }
                        }
                    ],
                    "lengthChange": false,
                    "searching": true,
                    //"pagingType": "full_numbers",
                    "paging": false,
                    "language": {
                        "zeroRecords": "Can't find the record you're looking for? Click here to <a href='#' type='button' class='btn no-border btn-sm'><i class='fa fa-file-signature'></i> Add Documents to Track</a>",
                        "processing": 'Loading',
                        "oPaginate": {
                            "sNext": '<i class="fa fa-caret-right"></i>', // or '→'
                            "sPrevious": '<i class="fa fa-caret-left"></i>' // or '←'
                        }
                    },
                    // "columnDefs":[
                    //     {
                    //         "targets": [-1,0,1,2,3],
                    //         "orderable": false,
                    //     },
                    //     {
                    //         'targets': 0,
                    //         'checkboxes': {
                    //             'selectRow': true
                    //         }
                    //     }
                    // ],
                    "ordering": false,
                    "orderCellsTop": true,
                    "fixedHeader": true,
                    "aLengthMenu": [[50,100,150,200,250,-1], [50,100,150,200,250,"All"]],
                    //"sDom": '<"top"Pr>rt<"bottom"<"toolbar w-50">><"clear">p',
                    "sDom": '<"top"Pr>rt<"bottom"><"toolbar">p<"clear">',
                });
                // Add event listener for opening and closing details
                $('#processlist tbody').on('click', 'td.details-control', function (){
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
                // $('#processlist tbody').on( 'click', 'tr', function () {
                //     $(this).toggleClass('selected');
                // });

                // activity log
                // table3 = $('#activity_log').DataTable({
                //     "ajax": {
                //         url: "/getactivitylog"
                //     },
                //     "scrollY": "500px",
                //     "scrollCollapse": false,
                //     "scrollResize": true,
                //     "select": {
                //         style: 'multi'
                //     },
                //     "initComplete": function(settings, json) {
                //         $('.dataTables_scrollBody thead tr').css({visibility:'collapse'});
                //         $('.dataTables_scrollBody tfoot tr').css({visibility:'collapse'});
                //     },
                //     "columns": [
                //         {
                //             "className": 'p-0 m-0 text-center',
                //             "data": 'document_number'
                //         },
                //         {
                //             "className":        'pt-0 pb-0 pl-1 pr-1 m-0',
                //             "data": {
                //                 document_type: "document_type"
                //             },
                //             "render": function(data, type, row){
                //                 return data.document_type;
                //             }
                //         },
                //         {
                //             "className":        'pt-0 pb-0 pl-1 pr-1 m-0',
                //             "data": "created_at",
                //             "render": function(data, type, row){
                //                 return $.date(data);
                //             }
                //         }
                //     ],
                //     "lengthChange": false,
                //     "searching": true,
                //     //"pagingType": "full_numbers",
                //     "paging": false,
                //     "language": {
                //         "zeroRecords": "Can't find the record you're looking for? Click here to <a href='#' type='button' class='btn no-border btn-sm'><i class='fa fa-file-signature'></i> Add Documents to Track</a>",
                //         "processing": 'Loading',
                //         "oPaginate": {
                //             "sNext": '<i class="fa fa-caret-right"></i>', // or '→'
                //             "sPrevious": '<i class="fa fa-caret-left"></i>' // or '←'
                //         }
                //     },
                //     "columnDefs":[
                //         {
                //             "targets": [-1,0,1,2,3],
                //             "orderable": false,
                //         },
                //         {
                //             'targets': 0,
                //             'checkboxes': {
                //                 'selectRow': true
                //             }
                //         }
                //     ],
                //     "ordering": false,
                //     "orderCellsTop": true,
                //     "fixedHeader": true,
                //     "aLengthMenu": [[50,100,150,200,250,-1], [50,100,150,200,250,"All"]],
                //     //"sDom": '<"top"Pr>rt<"bottom"<"toolbar w-50">><"clear">p',
                //     "sDom": '<"top"Pr>rt<"bottom"><"toolbar">p<"clear">',
                // });
                //
                // // Add event listener for opening and closing details
                // $('#activity_log tbody').on('click', 'td.details-control', function (){
                //     var tr = $(this).closest('tr');
                //     var row = table.row( tr );
                //
                //     if ( row.child.isShown() ) {
                //         row.child.hide();
                //         tr.removeClass('shown');
                //     }
                //     else {
                //         row.child( format(row.data()) ).show();
                //         tr.addClass('shown');
                //     }
                // });
                // $('#activity_log tbody').on( 'click', 'tr', function () {
                //     $(this).toggleClass('selected');
                // });
                // // $('#showall').DataTable().searchPanes.rebuildPane();
                $.fn.dataTableExt.oJUIClasses;
                $.fn.dataTableExt.oJUIClasses.sPaging = 'mx-auto text-center mt-0 w-50';
                $.fn.dataTable.ext.classes.sInfo = 'mx-auto text-center p-4';
                $.fn.dataTable.ext.classes.c = 'paginate_button btn btn-primary rounded-0 text-white mt-0 border text-center';
                $.fn.dataTable.ext.classes.sPageButtonActive = 'current btn rounded-0 text-white mt-0 border text-center';
                $.fn.dataTable.ext.classes.sPageButtonDisabled = 'btn btn-secondary rounded-0 text-white mt-0 border text-center';
                $.fn.dataTable.ext.classes.sInfo = 'dataTables_info btn btn-secondary rounded-0 text-white mt-0 border text-center';
            }
            $.submitform = function(){
                var project_type = $('#project-type').val();
                var document_type = $('#document_name').val();
                var src = 'insert';
                $.ajax({
                    url: '/savesettings',
                    dataType: 'json',
                    method: 'get',
                    data: {project_type:project_type,document_type:document_type,src:src},
                    success: function(data){
                        console.log(data);
                        $.confirm({
                            theme: 'modern',
                            icon: data.icon,
                            title: data.title,
                            content: data.message,
                            autoClose: 'Ok|10000',
                            buttons: {
                                Ok:{
                                    text: 'Ok',
                                    btnClass: data.confirm_button,
                                    action: function(){
                                        reinitialize_table(project_type);
                                    }
                                }
                            }
                        });
                    }
                });
            }
            $.submitnewdocument = function(id, process_name){
                var project_type = $('#project-type2').val();
                var document_type = $('#process_document_type'+id).val()
                var src = 'insert';
                $.ajax({
                    url: '/saveprocessdoccuments',
                    dataType: 'json',
                    method: 'get',
                    data: {project_type:project_type,document_type:document_type,src:src,process_name:process_name},
                    success: function(data){
                        console.log(data);
                        $.confirm({
                            theme: 'modern',
                            icon: data.icon,
                            title: data.title,
                            content: data.message,
                            autoClose: 'Ok|10000',
                            buttons: {
                                Ok:{
                                    text: 'Ok',
                                    btnClass: data.confirm_button,
                                    action: function(){
                                        reinitialize_table(project_type);
                                    }
                                }
                            }
                        });
                    }
                });
            }
            $.disableselected = function(){
                $.confirm({
                    theme: 'modern',
                    icon: 'fa fa-trash-alt text-danger',
                    title: 'Disable Document Type/s',
                    content: 'Do you want to disable these/this document/s?',
                    autoClose: 'Cancel|10000',
                    buttons: {
                        Yes:{
                            text: 'Disable',
                            btnClass: 'btn-red',
                            action: function(){
                                var id = '';
                                var i = 0;
                                var project_type = $("#project-type").val();
                                var totaldocuments = table.rows('.selected').data().length;
                                var html = '<div class="table-responsive">';
                                html += '<table class="table table-bordered" style="width: 100%!important;">';
                                html += '<thead>';
                                html += '<tr>';
                                html += '<th>Document Number</th>';
                                html += '<th>Document Type:</th>';
                                html += '<th>Project Type:</th>';
                                html += '<th>Date Created:</th>';
                                html += '<th>Date Updated:</th>';
                                html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                if(totaldocuments > 0){
                                    table.rows('.selected').every( function () {
                                        var d = this.data();
                                        html += '<tr>';
                                        html += '<td class="text-left">'+d.document_number + '</td>';
                                        html += '<td class="text-left">'+d.document_type + '</td>';
                                        html += '<td class="text-left">'+project_type + '</td>';
                                        html += '<td class="text-left">'+$.date(d.created_at)+'</td>';
                                        html += '<td class="text-left">'+$.date(d.updated_at)+'</td>';
                                        html += '</tr>';
                                        id += d.id;
                                        i++;
                                        if(totaldocuments > i){
                                            id += '|';
                                        }
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
                                        icon: 'fa fa-trash-alt text-danger',
                                        title: 'Check Document types to be disabled',
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
                                                        title: 'Disabled',
                                                        autoClose: 'Ok|15000',
                                                        content: 'Documents has been successfully disabled!',
                                                        buttons: {
                                                            Ok: {
                                                                text: 'Ok',
                                                                btnClass: 'btn-green'
                                                            }
                                                        }
                                                    });
                                                    // var self = this;
                                                    // self.setContent(html);
                                                    var src = 'disabled';
                                                    return $.ajax({
                                                        url: '/managedocumenttypes',
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
                                                },
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
                                            reinitialize_table(project_type);
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
                                content: 'Disabling has been Canceled',
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
            $.enableselected = function(){
                $.confirm({
                    theme: 'modern',
                    icon: 'fa fa-clipboard-check text-success',
                    title: 'Enable Document Type/s',
                    content: 'Do you want to enable these/this document/s?',
                    autoClose: 'Cancel|10000',
                    buttons: {
                        Yes:{
                            text: 'Enable',
                            btnClass: 'btn-green',
                            action: function(){
                                var id = '';
                                var i = 0;
                                var project_type = $("#project-type").val();
                                var totaldocuments = table.rows('.selected').data().length;
                                var html = '<div class="table-responsive">';
                                html += '<table class="table table-bordered" style="width: 100%!important;">';
                                html += '<thead>';
                                html += '<tr>';
                                html += '<th>Document Number</th>';
                                html += '<th>Document Type:</th>';
                                html += '<th>Project Type:</th>';
                                html += '<th>Date Created:</th>';
                                html += '<th>Date Updated:</th>';
                                html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                if(totaldocuments > 0){
                                    table.rows('.selected').every( function () {
                                        var d = this.data();
                                        html += '<tr>';
                                        html += '<td class="text-left">'+d.document_number+'</td>';
                                        html += '<td class="text-left">'+d.document_type+'</td>';
                                        html += '<td class="text-left">'+project_type+'</td>';
                                        html += '<td class="text-left">'+$.date(d.created_at)+'</td>';
                                        html += '<td class="text-left">'+$.date(d.updated_at)+'</td>';
                                        html += '</tr>';
                                        id += d.id;
                                        i++;
                                        if(totaldocuments > i){
                                            id += '|';
                                        }
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
                                        icon: 'fa fa-clipboard-check text-success',
                                        title: 'Check Document types to be enabled',
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
                                                        title: 'Enabled',
                                                        autoClose: 'Ok|15000',
                                                        content: 'Documents has been successfully enabled!',
                                                        buttons: {
                                                            Ok: {
                                                                text: 'Ok',
                                                                btnClass: 'btn-green'
                                                            }
                                                        }
                                                    });
                                                    // var self = this;
                                                    // self.setContent(html);
                                                    var src = 'enabled';
                                                    return $.ajax({
                                                        url: '/managedocumenttypes',
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
                                                },
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
                                            reinitialize_table(project_type);
                                        }
                                    });
                                }else{
                                    $.confirm({
                                        theme: 'modern',
                                        icon: 'fa fa-times text-danger',
                                        title: 'Invalid!',
                                        autoClose: 'Ok|15000',
                                        content: 'You haven\'t selected any Documents to be enabled.',
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
                                content: 'Disabling has been Canceled',
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
            $.deletedocuments = function(document_type, process_name, mode_of_procurement){
                $.confirm({
                    theme: 'modern',
                    icon: 'fa fa-trash text-danger',
                    title: 'Remove Documents from Procurement Process?',
                    content: 'Do you want to remove this document?',
                    autoClose: 'Cancel|10000',
                    buttons: {
                        Yes:{
                            text: 'Remove',
                            btnClass: 'btn-red',
                            action: function(){
                                $.confirm({
                                    theme: 'modern',
                                    icon: 'fa fa-trash text-danger',
                                    title: 'Removed',
                                    autoClose: 'Ok|15000',
                                    content: 'Document has been successfully Removed!',
                                    buttons: {
                                        Ok: {
                                            text: 'Ok',
                                            btnClass: 'btn-green'
                                        }
                                    },
                                    onDestroy: function(){
                                        reinitialize_table($('#project_type2').val());
                                    }
                                });
                                // var self = this;
                                // self.setContent(html);
                                var src = 'enabled';
                                return $.ajax({
                                    url: '/removeprocessdocuments',
                                    dataType: 'json',
                                    method: 'get',
                                    data: {document_type:document_type, process_name:process_name, mode_of_procurement:mode_of_procurement}
                                }).done(function (response) {
                                    // $.alert('Success!')
                                    // self.setContentAppend('<div>Done!</div>');
                                }).fail(function(){
                                    // $.alert('Something went wrong!')
                                    // self.setContentAppend('<div>Fail!</div>');
                                });
                            }
                        },
                        Cancel: function () {
                            $.confirm({
                                theme: 'modern',
                                icon: 'fa fa-times text-danger',
                                title: 'Canceled!',
                                autoClose: 'Ok|15000',
                                content: 'Removing of Documents has been Canceled',
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
            $("body").on('keydown', function(e){
                if(e.type == 'keydown' && e.which == 45){ // insert key
                    $.showform();
                }
                if(e.type == 'keydown' && e.which == 46){ // delete key
                    $.closeform();
                }
            });
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
        $.showform = function(){
            var html = "<div class='input-group'>";
            html += "<div class='input-group-prepend'>";
            html += "<button type='button' onclick='$.closeform()' class='btn btn-secondary  bg-secondary rounded-0 small border-top-0' style='height: calc(2.55rem + 2px)!important;'><i class='fa fa-times text-danger'></i></button>";
            html += "</div>";
            html += "<input name='document_name' id='document_name' type='text' class='form-control pl-3 pb-2 pt-2 small  border-top-0' placeholder='Enter Document Name' required style='height: calc(2.55rem + 2px)!important;'>";
            html += "<div class='input-group-append'>";
            html += "<button type='button' class='btn btn-secondary  bg-secondary rounded-0 small border-top-0' onclick='$.submitform()' style='height: calc(2.55rem + 2px)!important;'><small>Submit</small></button>";
            html += "</div>";
            html += "</div>";
            $("#add-document-form").html(html);
        }
        $.closeform = function(){
            var html = '<div class="btn-group" style="height: calc(2.55rem + 2px)!important;">';
            html += '<button type="button" class="btn btn-secondary  bg-secondary rounded-0 border-top-0" id="receive_selected" onclick="$.showform()"><i class="fa fa-plus text-success"></i></button>';
            html += '<button type="button" class="btn btn-secondary  bg-secondary rounded-0 border-top-0" id="receive_selected" onclick="$.enableselected()"><i class="fa fa-check text-info"></i><small>Enable Selected</small></button>';
            html += '<button type="button" class="btn btn-secondary  bg-secondary rounded-0 border-top-0" id="receive_selected" onclick="$.disableselected()"><i class="fa fa-trash-alt text-danger"></i><small>Disable Selected</small></button>';
            html += '</div>';
            $("#add-document-form").html(html);
        }
        $.showadddocuments = function(id, process_name){
            var html = '<div class="input-group">';
            html += '<div class="input-group-prepend">';
            html += '<button type="button" onclick="$.closeadddocuments('+id+', \''+process_name+'\')" class="btn btn-secondary rounded-0 btn-sm  border-bottom-0 border-left-0 border-top-0"><i class="fa fa-trash text-danger"></i></button>';
            html += '</div>';
            html += '<input type="text" class="form-control form-control-sm rounded-0 border border-0 pl-3 small adddocumentstoprocess" id="process_document_type'+id+'" placeholder="Enter document name '+process_name+'" name="document">';
            html += '<div class="input-group-append">';
            html += '<button type="button" onclick="$.submitnewdocument('+id+', \''+process_name+'\')" class="btn btn-secondary rounded-0 btn-sm  border-bottom-0 border-right-0 border-top-0"><i class="fa fa-check text-success"></i> Submit</button>';
            html += '</div>';
            html += '</div>';
            $('#add-documents-to-process'+id).html(html);
            // !- Document Types Autocomplete Initialization
            var project_type = $('#project-type').val();
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
                        'url': '/getdocumenttypes',
                        'data': {
                            "_token" : "{{ csrf_token() }}",
                            "term" : request.term,
                            "project_type" : project_type
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
            $(".adddocumentstoprocess").autocomplete(document_type_autocomplete_init).focus(function() {
                $(this).autocomplete('search', $(this).val())
            });
            // -!
        }
        $.closeadddocuments = function(id, process_name){
            var html = '<div class="input-group">';
            html += '<div class="input-group-prepend">';
            html += '<button type="button" onclick="$.showadddocuments('+id+', \''+process_name+'\')" class="btn btn-secondary rounded-0 btn-sm  border-bottom-0 border-left-0 border-top-0"><i class="fa fa-plus text-success"></i></button>';
            html += '</div>';
            html += '<input type="text" class="form-control form-control-sm rounded-0 border border-0 pl-2 small" placeholder="" name="document" disabled>';
            html += '</div>';
            $('#add-documents-to-process'+id).html(html);
        }
    </script>
@endsection
