@extends($role==1 ? 'layouts.app' : 'layouts.app2');


@section('css')
    {{-- <link href="{{ asset('assets/select2/dist/css/select2.min.css') }}" rel="stylesheet" /> --}}
    {{-- <link href="{{ asset('css/select2-bootstrap.min.css') }}" rel="stylesheet" /> --}}

    <link href="{{ asset('argon/vendor/jquery/dist/jquery-ui.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('argon/vendor/dropzone/dist/dropzone.css') }}" rel="stylesheet" />
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
        /* #showall_info{
            color: red!important;
            text-align: center!important;
        }
        .pagination{
            margin: 0 2px!important;
        } */
        p{
            font-size: 13px!important;
        }
        a>p{
            text-decoration: none;
            color: rgb(58, 58, 58);
        }
        a>p:hover{
            text-decoration: underline;
            color: rgb(7, 150, 194);
        }
        .page-item .page-link, .page-item span {
            border-radius: 0%!important;
            /* border: 1px rgb(7, 150, 194) solid!important;  */
            /* background-color: rgba(255, 255, 255, 0)!important;  */
            /* color: #12808f!important; */
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
    <div id="values">
        @php
            dd($project_document);
        @endphp
        <input type="hidden" id="document_type_id" value="{{ $project_document->document_type_id }}">
        <input type="hidden" id="plan_id" value="{{ $project_document->plan_id }}">
        <input type="hidden" id="contractor_id" value="{{ $project_document->contractor_id }}">
    </div>
    <div class="fade-in">
        <div class="row">
            <div class="col-lg-12">
                <div class="btn-group">
                    <a href="{{ route('documenttracking.index') }}" class="btn btn-secondary text-dark rounded-0 "><i class="fa fa-caret-left"></i> Return</a>
                    <a href="{{ route('documenttracking.index') }}" class="btn btn-secondary text-dark rounded-0 "><i class="fa fa-home"></i> Home</a>
                    <a href="{{ route('documenttracking.index') }}" class="btn btn-secondary text-dark rounded-0 "><i class="fa fa-redo"></i> Refresh</a>
                    <a href="{{ route('documenttracking.index') }}" class="btn btn-secondary text-dark rounded-0 ">Forward <i class="fa fa-caret-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header text-light rounded-0 pt-2 pl-3 pb-0 ">
                        <h3 class="text-dark">Document Tracking</h3>
                    </div>
                    <div class="card-body pt-2 pl-3 pb-3  border-top-0">
                        <div class="row">
                            <div class="col-md-2">
                                <p class="mt-3 mb-0">Project Title:</p>
                            </div>
                            <div class="col-md-10">
                                <a href="#"><p class="mt-3 mb-0"><b>{{ $project_document->project_title }}</b></p></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <p class="mt-0 mb-3">Contractor:</p>
                            </div>
                            <div class="col-md-10">
                                <a href="#"><p class="mt-0 mb-3"><b>{{ $project_document->business_name }}</b></p></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-9 pb-0 pt-0">
                                <h3 class="text-dark pt-0 pb-0 mt-0 mb-0">Routing History</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-append">
                                        <div class="input-group-text text-dark  border-right-0 p-3">
                                            <i class="fa fa-search"></i>
                                        </div>
                                    </div>
                                    <input type="text" id="search_table" class="form-control rounded-0 pl-2 pr-2  p-3" placeholder="Enter Keyword">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped" width="100%" id="showall">
                                    <thead>
                                        <tr class="bg-secondary">
                                            <td class="text-center  border-right-0 text-dark w-5 p-2">Actions</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Status</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Document Type</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Date Routed</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Sender</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Receiver</td>
                                            <td class="text-center  text-dark p-2">File Attachment</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-secondary">
                                            <td class="text-center  border-right-0 text-dark w-5 p-2">Actions</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Status</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Document Type</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Date Routed</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Sender</td>
                                            <td class="text-center  border-right-0 text-dark p-2">Receiver</td>
                                            <td class="text-center  text-dark p-2">File Attachment</td>
                                        </tr>
                                    </tfoot>
                                </table>
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
    {{-- <script src="{{ asset('argon/vendor/jquery/dist/jquery-ui.min.js') }}"></script> --}}

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

        $(document).ready(function() {
            var document_type_id = $("#document_type_id").val();
            var plan_id = $("#plan_id").val();
            var contractor_id = $("#contractor_id").val();
            var table = $('#showall').DataTable( {
                "ajax": "/getdocuments?f=&t=projectonly&d="+document_type_id+"&p="+plan_id+"&c="+contractor_id,
                "columns": [
                    {
                        "className": 'p-0 m-0 details-control',
                        "data": "id",
                        "render": function(data, type, row){
                            return '<div class="btn-group w-100">'+
                                        '<a class="btn btn-sm bg-secondary w-100 rounded-0 text-center" href="#"><i class="fa fa-eye text-info"></i></a>'+
                                        '<a class="btn btn-sm bg-secondary w-100 rounded-0 text-center" href="#"><i class="fa fa-pen text-success"></i></a>'+
                                //    '<a class="btn btn-sm bg-secondary w-100 rounded-0 text-center" href="#"><i class="fa fa-eye"></i></a>'+
                                   '</div>';
                        }
                    },
                    {
                        "className": 'pt-1 pb-0 pl-1 pr-0 m-0 text-center',
                        "data": "status",
                        "render": function(data, type, row){
                            var html = '';
                            if(data == 'sent'){
                                html = '<span class="badge badge-primary text-center">'+data+'</span>';
                            }
                            if(data == 'received'){
                                html = '<span class="badge badge-info text-center">'+data+'</span>';
                            }
                            if(data == 'ended'){
                                html = '<span class="badge badge-success text-center">'+data+'</span>';
                            }
                            return html;
                        }
                    },
                    {
                        "className":        'pt-1 pb-0 pl-1 pr-1 m-0',
                        "data": "document_type"
                    },
                    {
                        "className":        'pt-1 pb-0 pl-1 pr-1 m-0',
                        "data": "created_at",
                        "render": function(data, type, row){
                            return $.date(data);
                        }
                    },
                    {
                        "className": 'pt-1 pb-0 pl-1 pr-0 m-0',
                        "data": "sender_name"
                    },
                    {
                        "className": 'pt-1 pb-0 pl-1 pr-0 m-0',
                        "data": "receiver_name"
                    },
                    {
                        "className":      'p-0 m-0',
                        "data": {
                            file_status: 'file_status',
                            file_directory: 'file_directory'
                        },
                        "render": function(data, type, row){
                            if(data.file_status == 'has attachment'){
                                return '<a class="btn btn-sm bg-secondary w-100 rounded-0 text-center" href="/'+data.file_directory+'">See Attachment</a>';
                            }else{
                                return '<a class="btn btn-sm bg-secondary w-100 rounded-0 text-center" disabled href="#">No Attachment</a>';
                            }
                        }
                    }
                ],
                "lengthChange": false,
                "searching": true,
                //"pagingType": "full_numbers",
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
                        "targets": [-1,0,1,2,3,4,5],
                        "orderable": false,
                    },
                ],
                "orderCellsTop": true,
                "fixedHeader": true,
                "aLengthMenu": [[15,100,150,200,250,-1], [15,100,150,200,250,"All"]],
                // for advance filtering
                // searchPanes:{
                //     panes: [
                //         {
                //             header:'Document Types',
                //             options:[
                //                 {
                //                     label:'Accountants from Tokyo',
                //                     value: function(rowData, rowIdx){
                //                         return rowData[1] === 'Accountant' && rowData[2] === 'Tokyo';
                //                     },
                //                     className: 'tokyo'
                //                 }
                //             ],
                //             dtOpts:{
                //                 searching: false,
                //                 order: [[1, 'desc']]
                //             }
                //         }
                //     ],
                //     layout: 'columns-3',
                // },
                "dom": 'lrtp',
                //"sDom": '<"top"Pr>rt<"bottom">pi<"clear">',
                //"scrollX": "100%",
            });

            // Add event listener for opening and closing details
            $('#showall tbody').on('click', 'td.details-control', function (){
                var tr = $(this).closest('tr');
                var row = table.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    row.child( format(row.data()) ).show();
                    tr.addClass('shown');
                }
            });
            // $('#showall thead tr').clone(true).appendTo('#showall thead');
            // $('#showall thead tr:eq(1) td').each( function (i) {
            //     var title = $(this).text();
            //     $(this).attr('class', 'pb-1 pl-1 pr-1 pt-1 m-0');
            //     if(i != 0 && i != 5) $(this).html('<input type="text" class="form-control p-2 rounded-0 h-100 w-100 border" style="border-left: solid 1px #aaaaaa; border-right: solid 1px #aaaaaa; font-size: 0.9em!important" placeholder="Search '+title+'" />');
            //     if(i == 0 || i == 5) $(this).html( '' );
            //     $( 'input', this ).on( 'keyup change', function () {
            //         if ( table.column(i).search() !== this.value ) {
            //             table.column(i).search( this.value ).draw();
            //         }
            //     });
            // });
            $('#showall tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
            });
            // $('#showall').DataTable().searchPanes.rebuildPane();
            $.fn.dataTableExt.oJUIClasses;
            $.fn.dataTable.ext.classes.sPaging = 'mx-auto text-center mt-0  primary_button';
            $.fn.dataTable.ext.classes.sInfo = 'mx-auto text-center';
            $.fn.dataTable.ext.classes.c = 'paginate_button btn btn-primary rounded-0 text-white mt-0 border text-center';
            $.fn.dataTable.ext.classes.sPageButtonActive = 'current btn rounded-0 text-white mt-0 border text-center';
            $.fn.dataTable.ext.classes.sPageButtonDisabled = 'btn btn-primary rounded-0 text-dark mt-0 border text-center';
            $.fn.dataTable.ext.classes.sInfo = 'dataTables_info btn btn-secondary rounded-0 text-white mt-0 border text-center';
            $('#search_table').keyup(function(){
                table.search($(this).val()).draw() ;
            })

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
        });
    </script>
@endsection
