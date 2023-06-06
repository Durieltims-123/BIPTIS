@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

    <div id="app">
        <div class="col-sm-12">
            <div class="modal" tabindex="-1" role="dialog" id="modal_form">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="">Update Date of Posting</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <form class="col-sm-12" method="POST" id="posting_form" action="{{old('action')}}" enctype="multipart/form-data">

                                @csrf
                                <div class="row d-flex">

                                    <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                                        <label for="action">Action<span class="text-red">*</span></label>
                                        <input type="text" id="action" name="action" class="form-control form-control-sm" readonly value="{{old('action')}}">
                                        <label class="error-msg text-red">@error('action'){{$message}}@enderror
                                        </label>
                                    </div>

                                    {{-- download --}}
                                    <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                                        <label for="download">Download<span class="text-red">*</span></label>
                                        <input type="text" id="download" name="download" class="form-control form-control-sm" readonly value="{{old('download')}}">

                                        <label class="error-msg text-red">@error('id'){{$message}}@enderror
                                        </label>
                                    </div>


                                    <!--ID -->
                                    <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                                        <label for="id">ID <span class="text-red">*</span></label>
                                        <input type="text" id="id" name="id" class="form-control form-control-sm" readonly value="{{old('id')}}">
                                        <label class="error-msg text-red">@error('id'){{$message}}@enderror
                                        </label>
                                    </div>

                                    <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                                        <label for="posting_date">Date of Posting<span class="text-red">*</span></label>
                                        <input type="text" id="posting_date" name="posting_date" class="form-control form-control-sm bg-white datepicker" value="{{old('posting_date')}}">
                                        <label class="error-msg text-red">@error('posting_date'){{$message}}@enderror
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center col-sm-12">
                                    <button class="btn btn-primary text-center">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="card shadow">
            <div class="card shadow border-0">
                <div class="card-header">
                    <h2 id="title">{{$title}}</h2>
                </div>
                <div class="card-body">
                    <div class="col-sm-12" id="filter">
                        <form class="row" id="filter_itb" method="post" action="{{route('posting.filter_itbs')}}">

                            @csrf
                            <!-- year -->
                            <div class="form-group col-xs-6 col-sm-4 col-lg-2 mb-0">
                                <label for="year" class="input-sm">Year </label>
                                <input class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}">
                                <label id="year_error" class="error-msg text-red">@error('year'){{$message}}@enderror</label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-4 col-lg-2 mb-0">
                                <label for="status" class="input-sm">Status</label>
                                <select class="form-control form-control-sm" id="status" name="status">
                                    <option value="" {{old('status')=="" ? 'selected' : ''}}>All</option>
                                    <option value="for_posting" {{old('status')=="for_posting" ? 'selected' : ''}}>For Posting</option>
                                    <option value="posted" {{old('status')=="posted" ? 'selected' : ''}}>Posted</option>s
                                </select>
                                <label id="status" class="error-msg text-red">@error('status'){{$message}}@enderror</label>

                            </div>

                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="contract_table">
                            <thead class="">
                                <tr class="bg-primary text-white">
                                    <th class="text-center"></th>
                                    <th class="text-center">ID</th>
                                      <th class="text-center">Posting Date</th>
                                    <th class="text-center">Project No.</th>
                                    <th class="text-center">Project Title</th>
                                    <th class="text-center">Source of Fund</th>
                                    <th class="text-center">Project Cost</th>
                                    <th class="text-center">Remarks</th>
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
    $(".yearpicker").datepicker({
        format: 'yyyy'
        , viewMode: "years"
        , minViewMode: "years"
    });

    $("#date_received_by_bac").datepicker({
        format: 'mm/dd/yyyy'
        , endDate: '{{$year}}'
        , autoclose: true
        , language: 'da'
        , enableOnReadonly: false
    });

    let year_error = $("#year_error").html();
    let posting_date_error = @json(old("id"));
    let old_year = @json(old('year'));
    let old_id = @json(old('id'));
    if (old_year == null) {
        $("#year").datepicker('update', @json($year));
    }

    if (posting_date_error != "" && posting_date_error != null) {
        $("#modal_form").modal('show');
    }

    // datatables
    $('#contract_table thead tr').clone(true).appendTo('#contract_table thead');
    $('#contract_table thead tr:eq(1)').removeClass('bg-primary');
    var data = @json(session('data'));
    if (data == null) {
        data = @json($data);
    }
    var table = $('#contract_table').DataTable({
        dom: 'Bfrtip'
        , buttons: [{
                text: 'Hide Filter'
                , attr: {
                    id: 'show_filter'
                }
                , className: 'btn btn-sm shadow-0 border-0 bg-dark text-white'
                , action: function(e, dt, node, config) {

                    if (config.text == "Show Filter") {
                        $('#filter').removeClass('d-none');
                        $('#filter_btn').removeClass('d-none');
                        config.text = "Hide Filter";
                        $("#show_filter").html("Hide Filter");
                    } else {
                        $('#filter').addClass('d-none');
                        $('#filter_btn').addClass('d-none');
                        config.text = "Show Filter";
                        $("#show_filter").html("Show Filter");
                    }
                }
            }
            , {
                text: 'Filter'
                , attr: {
                    id: 'filter_btn'
                }
                , className: 'btn btn-sm shadow-0 border-0 bg-warning text-white'
            }
            , {
                text: 'Select All'
                , attr: {
                    id: 'select_all'
                }
                , className: 'btn btn-sm shadow-0 border-0 bg-secondary'
                , action: function(e, dt, node, config) {
                    table.rows({
                        search: 'applied'
                    }).select()
                }
            }
            , {
                text: 'Deselect'
                , attr: {
                    id: 'deselect'
                }
                , className: 'btn btn-sm shadow-0 border-0 bg-secondary'
                , action: function(e, dt, node, config) {
                    table.rows({
                        search: 'applied'
                    }).select(false)
                }
            }
            , {
                text: 'Excel'
                , extend: 'excel'
                , className: 'btn btn-sm shadow-0 border-0 bg-success text-white'
            }
            , {
                text: 'Post'
                , className: 'btn btn-sm shadow-0 border-0 bg-info text-white'
                , action: function(e, dt, node, config) {
                    var rows = table.rows({
                        selected: true
                    }).indexes();

                    let posting_status = table.cells(rows, 0).data();
                    let unique_status = posting_status.filter((x, i, a) => a.indexOf(x) == i);
                    if (unique_status.length > 1) {
                        swal.fire({
                            title: `Multiple Status`
                            , text: 'Please select projects with For Posting status only.'
                            , buttonsStyling: false
                            , customClass: {
                                confirmButton: 'btn btn-sm btn-warning'
                            , }
                            , icon: 'warning'
                        });
                    } else {
                        let procact_ids = table.cells(rows, 1).data().toArray().toString();
                        $("#posting_form").prop('action', "{{route('posting.submit_itb')}}");
                        $("#action").val("{{route('posting.submit_itb')}}");
                        $("#id").val(procact_ids);
                        $("#download").val("");
                        $("#modal_form").modal('show');
                    }
                }

            }
            , {
                text: 'Download ZIP'
                , className: 'btn btn-sm shadow-0 border-0 bg-danger text-white'
                , action: function(e, dt, node, config) {
                    var rows = table.rows({
                        selected: true
                    }).indexes();
                    let posting_status = table.cells(rows, 0).data();
                    let unique_status = posting_status.filter((x, i, a) => a.indexOf(x) == i);

                    if (rows.length > 0) {
                        // if (unique_status.length > 1 || unique_status[0] == "posted") {
                        if (false) {
                            swal.fire({
                                title: `Selection Error`
                                , text: 'Sorry! You cannot select Posted Projects'
                                , buttonsStyling: false
                                , customClass: {
                                    confirmButton: 'btn btn-sm btn-warning'
                                , }
                                , icon: 'warning'
                            });
                        } else {
                            let procact_ids = table.cells(rows, 1).data().toArray().toString();
                            $("#posting_form").prop('action', "{{route('posting.submit_itb')}}");
                            $("#action").val("{{route('posting.submit_itb')}}");
                            $("#id").val(procact_ids);
                            $("#download").val("true");
                            $("#modal_form").modal('show');
                        }
                    } else {
                        swal.fire({
                            title: `Select Projects`
                            , text: 'Please select projects to Download.'
                            , buttonsStyling: false
                            , customClass: {
                                confirmButton: 'btn btn-sm btn-warning'
                            , }
                            , icon: 'info'
                        });

                    }
                }

            }
        ]
        , data: data
        , columns: [{
                "data": "posting_status"
                , render: function(data, type, row) {
                    let status = '<span class="btn btn-sm bg-success text-white"> Posted </span>';
                    if (data === "for_posting") {
                        status = '<span class="btn btn-sm bg-danger text-white">For Posting</span>'
                    }
                    return '<div style="white-space: nowrap">' + status + '<a class="btn btn-sm shadow-0 border-0 btn-primary text-white" target="_blank" data-toggle="tooltip" data-placement="top" title="View Attachment/s" href="/archive/view_invitation_to_bid_attachments/' + row.procact_id + '"><i class="ni ni-tv-2"></i></a></div>';


                }
            }
            , {
                "data": "procact_id"
            }
            , {
                "data": "posting_date"
            }
            , {
                "data": "project_no"
            }
            , {
                "data": "project_title"
            }
            , {
                "data": "source"
            }
            , {
                "data": "project_cost"
                , render: function(data, type, row) {
                    if (data != null) {
                        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                    return "";
                }
            }
            , {
                "data": "remarks"
            }
        ]
        , language: {
            paginate: {
                next: '<i class="fas fa-angle-right"></i>'
                , previous: '<i class="fas fa-angle-left"></i>'
            }
        }
        , orderCellsTop: true
        , select: {
            style: 'multi'
            , selector: 'td:not(:first-child)'
        }
        , responsive: true
        , columnDefs: [{
            targets: 0,

            orderable: false
        }]
        , order: [
            [1, "desc"]
        ]
    , });

    if (year_error != null && year_error != '') {
        $('#filter').removeClass('d-none');
        $('#filter_btn').removeClass('d-none');
        $('#show_filter').text('Hide Filter');
    }


    // messages
    if ("{{session('message')}}") {
        if ("{{session('message')}}" == "missing_attachment") {
            swal.fire({
                title: `Missing Attachment`
                , text: 'Please attach your document in pdf format'
                , buttonsStyling: false
                , customClass: {
                    confirmButton: 'btn btn-sm btn-warning'
                , }
                , icon: 'warning'
            });
        } else if ("{{session('message')}}" == "success") {
            let download = @json(old("download"));
            $("#modal_form").modal('hide');
            swal.fire({
                title: `Success`
                , text: 'Successfully saved to database'
                , buttonsStyling: false
                , icon: 'success'
                , buttonsStyling: false
                , customClass: {
                    confirmButton: 'btn btn-sm btn-success'
                , }
            });

            if (download == "true") {
                $("#posting_form").prop('action', "{{route('posting.download_itb_zip')}}");
                $("#posting_form").submit();

            }

        } else {
            swal.fire({
                title: `Error`
                , text: 'An error occured please contact your system developer'
                , buttonsStyling: false
                , icon: 'warning'
                , customClass: {
                    confirmButton: 'btn btn-sm btn-warning'
                , }
            , });
        }
    }

    // events
    $('#contract_table thead tr:eq(1) th').each(function(i) {
        var title = $(this).text();
        if (title != "") {
            $(this).html('<input type="text" placeholder="Search" />');
            $(this).addClass('sorting_disabled');
            var index = 0;

            $('input', this).on('keyup change', function() {
                if (table.column(':contains(' + title + ')').search() !== this.value) {
                    table
                        .column(':contains(' + title + ')')
                        .search(this.value)
                        .draw();
                }

            });
        }
    });

    $("#filter_btn").click(function() {
        $("#filter_itb").submit();
    });

    $("#year").change(function() {
        $("#filter_itb").submit();
    });

    $("#status").change(function() {
        $("#filter_itb").submit();
    });


    $("input").change(function() {
        $(this).siblings('.error-msg').html("");
    });

    $(".custom-radio").change(function() {
        $(this).parent().siblings('.error-msg').html("");
    });

    $("select").change(function() {
        $(this).siblings('.error-msg').html("");
    });

</script>
@endpush
