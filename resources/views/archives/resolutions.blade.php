@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

    <div id="app">
        <div class="col-sm-12">
            <div class="modal" tabindex="-1" role="dialog" id="archive">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="archive_title"></h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="col-sm-12" method="POST" id="archive_form" action="{{route('archive.submit_resolution')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row d-flex">

                                    <!--ID -->
                                    <div class="form-group col-xs-12 col-sm-12 col-lg-12 d-none">
                                        <label for="id">ID <span class="text-red">*</span></label>
                                        <input type="text" id="id" name="id" class="form-control form-control-sm" readonly value="{{old('id')}}">
                                        <label class="error-msg text-red">@error('id'){{$message}}@enderror</label>
                                    </div>

                                    <!-- Attachment -->
                                    <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                                        <label for="attachment">Attachment/s <span class="text-red">*</span></label>
                                        <div id="existing_attachments">

                                        </div>
                                        <div id="attachment_div">
                                            <div class="row attachment_group">
                                                <div class="col-md-11">
                                                    <input type="file" name="attachments[]" accept="application/pdf" class="form-control attachment">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" id="add_more_attachment" class="btn btn-sm btn-primary mt-2">Add More
                                            Attachments</button>
                                        <label class="error-msg text-red">@error('attachment'){{$message}}@enderror</label>
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
                        <form class="row" id="filter_resolution" method="post" action="{{route('archive.filter_resolutions')}}">
                            @csrf
                            <!-- year -->
                            <div class="form-group col-xs-6 col-sm-4 col-lg-2 mb-0">
                                <label for="year" class="input-sm">Year </label>
                                <input class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}">
                                <label id="year_error" class="error-msg text-red">@error('year'){{$message}}@enderror</label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-4 col-lg-2 mb-0 d-none">
                                <label for="resolution_type" class="input-sm">Resolution Type </label>
                                <input class="form-control form-control-sm " id="resolution_type" name="resolution_type" minimum-view="resolution_type" value="{{$resolution_type}}">
                                <label id="resolution_type" class="error-msg text-red">@error('resolution_type'){{$message}}@enderror</label>
                            </div>

                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="archive_table">
                            <thead class="">
                                <tr class="bg-primary text-white">
                                    <th class="text-center"></th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Resolution Number</th>
                                    <th class="text-center">Resolution Date</th>
                                    @if($resolution_type==="RGMR"||$resolution_type==="RDMR")
                                    <th class="text-center">Contractor</th>
                                    @endif
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

    let year_error = $("#year_error").html();

    let old_year = @json(old('year'));

    if (old_year == null) {
        $("#year").val(@json($year));
    }

    // datatables
    $('#archive_table thead tr').clone(true).appendTo('#archive_table thead');
    $('#archive_table thead tr:eq(1)').removeClass('bg-primary');
    var data = @json(session('resolutions'));
    if (data == null) {
        data = @json($resolutions);
    }

    let resolution_type = @json($resolution_type);


    var table = $('#archive_table').DataTable({
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
                text: 'Excel'
                , extend: 'excel'
                , className: 'btn btn-sm shadow-0 border-0 bg-success text-white'
            }
            , {
                text: 'Print'
                , extend: 'print'
                , className: 'btn btn-sm shadow-0 border-0 bg-info text-white'
            }

        ]
        , data: data
        , columns: [{
                "data": "with_attachment"
                , render: function(data, type, row) {

                    if (data == 1) {
                        return '@if(in_array("update",$user_privilege))<button class="btn btn-sm btn-success edit-btn  " resolution_id="' + row.resolution_id + '"data-toggle="tooltip" data-placement="top" title="Edit" ><i class="ni ni-ruler-pencil"></i></button>@endif <a  class="btn btn-sm shadow-0 border-0 btn-primary text-white" target="_blank" data-toggle="tooltip" data-placement="top" title="View Attachment/s" href="view_resolution_attachments/' + row.resolution_id + '"><i class="ni ni-tv-2"></i></a>';
                    } else {
                        return "@if(in_array('add',$user_privilege))<button type='button' type='button' class='btn btn-sm btn-primary add-btn' resolution_id='" + row.resolution_id + "'data-toggle='tooltip' data-placement='top' title='Add Attachment'><i class='ni ni-fat-add'></i></button>@endif";
                    }
                }
            }
            , {
                "data": "resolution_id"
            }
            , {
                "data": "resolution_number"
            }
            , {
                "data": "resolution_date"
            }
            , @if($resolution_type === "RGMR" || $resolution_type === "RDMR") {
                "data": "bidder"
            }

            @endif
        ]
        , language: {
            paginate: {
                next: '<i class="fas fa-angle-right">'
                , previous: '<i class="fas fa-angle-left">'
            }
        }
        , orderCellsTop: true
        , select: {
            style: 'multi'
            , selector: 'td:not(:first-child)'
        }
        , order: [
            [1, "desc"]
        ]
        , responsive: true
        , columnDefs: [{
            targets: 0
            , orderable: false
        }]
    , });

    if (year_error != null && year_error != '') {
        $('#filter').removeClass('d-none');
        $('#filter_btn').removeClass('d-none');
        $('#show_filter').text('Hide Filter');
    }

    let old_id = @json(old('id'));
    if (old_id != null) {
        $("#archive").modal("show");
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
            swal.fire({
                title: `Success`
                , text: 'Successfully saved to database'
                , buttonsStyling: false
                , icon: 'success'
                , buttonsStyling: false
                , customClass: {
                    confirmButton: 'btn btn-sm btn-success'
                , }
            , });
            $("#archive").modal('hide');
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

    $('#archive_table thead tr:eq(1) th').each(function(i) {
        var title = $(this).text();
        if (title != "") {
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
            $(this).addClass('sorting_disabled');
            $('input', this).on('keyup change', function() {
                if (table.column(i).search() !== this.value) {
                    table
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        }
    });

    @if(in_array('add', $user_privilege))
    $('#archive_table tbody').on('click', '.add-btn', function(e) {
        let id = $(this).attr('resolution_id');
        $("#archive_form")[0].reset();
        $("#archive_title").html("Add Resolution Attachment/s");
        $('#id').val(id);
        $("#existing_attachments").html('');
        $("#attachment_div").find('.attachment_group').each(function(index) {
            if (index != 0) {
                $(this).remove();
            }
        });
        $("#archive").modal('show');
    });
    @endif

    @if(in_array('update', $user_privilege))
    $('#archive_table tbody').on('click', '.edit-btn', function(e) {
        let id = $(this).attr('resolution_id');
        var row = table.row($(this).parents('tr')).data();
        $("#archive_form")[0].reset();
        $("#archive_title").html("Update Resolution Attachment/s");
        $('#id').val(id);
        $("#existing_attachments").html('');
        $("#attachment_div").find('.attachment_group').each(function(index) {
            if (index != 0) {
                $(this).remove();
            }
        });

        $.ajax({
            'url': "{{route('archive.get_archive_resolution_attachments')}}"
            , 'data': {
                "_token": "{{ csrf_token() }}"
                , "resolution_id": row.resolution_id
            , }
            , 'method': "post"
            , 'success': function(data) {
                if (data.length > 0) {
                    data.forEach((item, i) => {
                        let existing_attachment = '<div class="row existing_attachment_group">';
                        existing_attachment = existing_attachment + '<div class="col-md-11">';
                        existing_attachment = existing_attachment + '<div class="form-control attachment">';
                        existing_attachment = existing_attachment + '<a href="view_resolution_attachment/' + item.id + '" target="_blank"> Attachment ' + (i + 1) + '</a>';
                        existing_attachment = existing_attachment + '</div> </div> <div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_existing_attachment" attachment_id="' + item.id + '"><i class="ni ni-fat-remove"></i></button></div> </div>';
                        $("#existing_attachments").html($("#existing_attachments").html() + existing_attachment);
                    });

                    // $("#existing_attachments").html($("#existing_attachments").html()+"<hr/>");
                    $(".remove_existing_attachment").click(function() {
                        let this_button = $(this);
                        Swal.fire({
                            title: 'Delete Attachment'
                            , text: 'Are you sure to delete this attachment?'
                            , showCancelButton: true
                            , confirmButtonText: 'Delete'
                            , cancelButtonText: "Don't Delete"
                            , buttonsStyling: false
                            , customClass: {
                                confirmButton: 'btn btn-sm btn-danger'
                                , cancelButton: 'btn btn-sm btn-default'
                            , }
                            , icon: 'warning'
                        }).then((result) => {
                            if (result.value == true) {
                                $.ajax({
                                    'url': "{{route('archive.delete_resolution_attachment')}}"
                                    , 'data': {
                                        "_token": "{{ csrf_token() }}"
                                        , "id": $(this).attr('attachment_id')
                                    , }
                                    , 'method': "post"
                                    , 'success': function(data) {
                                        if (data == "success") {
                                            swal.fire({
                                                title: `Success`
                                                , text: 'Successfully deleted attachment'
                                                , buttonsStyling: false
                                                , icon: 'success'
                                                , buttonsStyling: false
                                                , customClass: {
                                                    confirmButton: 'btn btn-sm btn-success'
                                                , }
                                            , });
                                            $(this_button).parents('.existing_attachment_group').remove();
                                        } else {
                                            swal.fire({
                                                title: `Success`
                                                , text: 'Successfully deleted attachment'
                                                , buttonsStyling: false
                                                , icon: 'success'
                                                , buttonsStyling: false
                                                , customClass: {
                                                    confirmButton: 'btn btn-sm btn-success'
                                                , }
                                            , });
                                            $(this_button).parents('.existing_attachment_group').remove();
                                            location.reload();
                                        }
                                    }
                                });
                            }
                        });
                    });

                }
            }
        });


        $("#archive").modal('show');
    });
    @endif

    $("#add_more_attachment").click(function functionName() {
        $("#attachment_div .row").last().after('<div class="row attachment_group"><div class="col-md-11"><input  type="file"  name="attachments[]" accept="application/pdf" class="form-control attachment"></div><div class="col-md-1 mt-2"><button type="button" class="btn btn-sm btn-danger p-1 remove_attachment"><i class="ni ni-fat-remove"></i></button></div></div>');
        $(".remove_attachment").click(function functionName() {
            $(this).parents('.attachment_group').remove();
        });
    });


    $("#filter_btn").click(function functionName() {
        $("#filter_resolution").submit();
    });

    $("#year").change(function() {
        $("#filter_resolution").submit();
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
