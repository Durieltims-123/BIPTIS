@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

    <div id="app">

        <div class="card shadow">
            <div class="card shadow border-0">
                <div class="card-header">
                    <h2 id="title">{{$title}}</h2>
                </div>
                <div class="card-body">
                    <div class="col-sm-12" id="filter">
                        <form class="row" id="filter_resolution" method="post" action="{{route('filter_resolution')}}">
                            @csrf
                            <!-- project year -->
                            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                                <label for="year" class="input-sm">Year </label>
                                <input class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}">
                                <label class="error-msg text-red">@error('year'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0 d-none">
                                <label for="resolution_type" class="input-sm">Resolution Type </label>
                                <input class="form-control form-control-sm" name="resolution_type" id="resolution_type" value="{{$resolution_type}}">
                                <label class="error-msg text-red">@error('resolution_type'){{$message}}@enderror
                                </label>
                            </div>
                        </form>
                    </div>

                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="data_table">
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
        $(".datepicker").datepicker({
            format: 'mm/dd/yyyy'
            , endDate: '{{$year}}'
        });

        $(".yearpicker").datepicker({
            format: 'yyyy'
            , viewMode: "years"
            , minViewMode: "years"
        });


        if (@json(old('year')) == null) {
            $("#year").val(@json($year));
        }

        // datatables
        $('#data_table thead tr').clone(true).appendTo('#data_table thead');
        $('#data_table thead tr:eq(1)').removeClass('bg-primary');
        let data = @json($resolutions);
        let resolution_type = @json($resolution_type);

        if (@json(old('year')) != null) {
            data = @json(session('resolutions'));
        }

        var table = $('#data_table').DataTable({
            data: data
            , dom: 'Bfrtip'
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
                    , className: 'btn btn-sm shadow-0 border-0 bg-warning text-white filter_btn'
                    , action: function(e, dt, node, config) {
                        $("#filter_resolution").submit();
                    }
                }
                , @if(in_array("add", $user_privilege)) {
                    text: 'Add Resolution'
                    , className: 'btn btn-sm shadow-0 border-0 bg-danger text-white'
                    , action: function(e, dt, node, config) {
                        let type = @json($resolution_type);
                        var currentUrl = window.location.href;
                        currentUrl = currentUrl.replace('http://', '');
                        currentUrl = currentUrl.split('/');
                        if (type == 'RRA') {
                            window.open('http://' + currentUrl[0] + '/add_resolution_recommending_award', '_blank');
                        }

                        if (type == 'RDF') {
                            window.open('http://' + currentUrl[0] + '/add_resolution_declaring_failure', '_blank');
                        }

                        if (type == 'RDMR') {
                            window.open('http://' + currentUrl[0] + '/add_resolution_denying_the_motion_for_reconsideration', '_blank');
                        }

                        if (type == 'RGMR') {
                            window.open('http://' + currentUrl[0] + '/add_resolution_granting_the_motion_for_reconsideration', '_blank');
                        }

                        if (type == 'RRRC') {
                            window.open('http://' + currentUrl[0] + '/add_resolution_recommending_recall_cancellation', '_blank');
                        }

                    }
                }
                , @endif {
                    text: 'Excel'
                    , extend: 'excel'
                    , className: 'btn btn-sm shadow-0 border-0 bg-success text-white'
                , }
                , {
                    text: 'Print'
                    , extend: 'print'
                    , className: 'btn btn-sm shadow-0 border-0 bg-info text-white'
                }
            ]
            , dataType: 'json'
            , columns: [{
                    "data": "resolution_id"
                    , render: function(data, type, row) {
                        let edit = "";
                        let download = "";
                        if (resolution_type == 'RRA') {
                            edit = "/edit_resolution_recommending_award/" + data;
                            download = "/generate_cca/" + data;
                        }
                        if (resolution_type == 'RDF') {
                            edit = "/edit_resolution_declaring_failure/" + data;
                            download = "/generate_rdf/" + data;
                        }
                        if (resolution_type == 'RDMR') {
                            edit = "/edit_resolution_denying_the_motion_for_reconsideration/" + data;
                            download = "/generate_mr_resolution/" + data;
                        }
                        if (resolution_type == 'RRRC') {
                            edit = "/edit_resolution_recommending_recall_cancellation/" + data;
                            download = "/generate_rrrc/" + data;
                        }

                        if (resolution_type == 'RGMR') {
                            edit = "/edit_resolution_granting_the_motion_for_reconsideration/" + data;
                            download = "/generate_mr_resolution/" + data;
                        }
                        return "<div style='white-space: nowrap'>@if(in_array('update',$user_privilege))<a type='button' target='_blank' href='" + edit + "' class='btn btn-sm btn btn-sm btn-success edit-btn' data-toggle='tooltip' data-placement='top' title='Edit' ><i class='ni ni-ruler-pencil'></i></a>@endif @if(in_array('delete',$user_privilege)) <button data-toggle='tooltip' data-placement='top' title='Delete'  class='btn btn-sm btn btn-sm btn-danger delete-btn'><i class='ni ni-basket text-white'></i></button>@endif <a class='btn btn-sm btn btn-sm bg-info text-white' data-toggle='tooltip' data-placement='top' title='Download'  target='_blank' href='" + download + "'><i class='ni ni-cloud-download-95'></i></a> </div>";
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
            , responsive: true
            , columnDefs: [{
                targets: 0
                , orderable: false
            }]
        , });

        // messages
        if ("{{session('message')}}") {
            if ("{{session('message')}}" == "success") {
                swal.fire({
                    title: `Success`
                    , text: 'Successfully saved to database'
                    , buttonsStyling: false
                    , confirmButtonClass: 'btn btn-sm btn-success'
                    , icon: 'success'
                });
            } else if ("{{session('message')}}" == "duplicate") {
                swal.fire({
                    title: `Duplicate`
                    , text: 'Data already exist in the database'
                    , buttonsStyling: false
                    , confirmButtonClass: 'btn btn-sm btn-danger'
                    , icon: 'warning'
                });
            } else if ("{{session('message')}}" == "delete_success") {
                swal.fire({
                    title: `Success`
                    , text: 'Data was deleted successfully'
                    , buttonsStyling: false
                    , confirmButtonClass: 'btn btn-sm btn-success'
                    , icon: 'success'
                });
            } else if ("{{session('message')}}" == "delete_error") {
                swal.fire({
                    title: `Delete Error`
                    , text: 'You cannot delete this Role'
                    , buttonsStyling: false
                    , confirmButtonClass: 'btn btn-sm btn-danger'
                    , icon: 'warning'
                });
            } else if ("{{session('message')}}" == "date_opened_error") {
                swal.fire({
                    title: `Input Error`
                    , text: 'Sorry, The system could not find Projects Opened at {{old("date_opened")}}'
                    , buttonsStyling: false
                    , confirmButtonClass: 'btn btn-sm btn-danger'
                    , icon: 'warning'
                });
            } else if ("{{session('message')}}" == "unknown_resolution") {
                swal.fire({
                    title: `Unknown Resolution`
                    , text: 'Sorry! We cannot find the Resolution You are Looking for.'
                    , buttonsStyling: false
                    , confirmButtonClass: 'btn btn-sm btn-danger'
                    , icon: 'warning'
                });
            } else {
                swal.fire({
                    title: `Error`
                    , text: 'An error occured please contact system developer'
                    , buttonsStyling: false
                    , confirmButtonClass: 'btn btn-sm btn-danger'
                    , icon: 'warning'
                });
            }
        }


        // events

        $('#data_table thead tr:eq(1) th').each(function(i) {
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

        $("#year").change(function() {
            $("#filter_resolution").submit();
        });



        // show delete
        @if(in_array("delete", $user_privilege))
        $('#data_table tbody').on('click', '.delete-btn', function(e) {
            let this_button = $(this);
            Swal.fire({
                text: 'Are you sure to delete this Resolution?'
                , showCancelButton: true
                , confirmButtonText: 'Delete'
                , cancelButtonText: "Don't Delete"
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , cancelButtonClass: 'btn btn-sm btn-default'
                , icon: 'warning'
            }).then((result) => {
                if (result.value == true) {
                    var row = table.row($(this_button).parents('tr')).data();
                    $.ajax({
                        'url': "{{route('delete_resolution')}}"
                        , 'data': {
                            "_token": "{{ csrf_token() }}"
                            , "id": row.resolution_id
                        , }
                        , 'method': "post"
                        , 'success': function(data) {
                            if (data == "success") {
                                swal.fire({
                                    title: `Success`
                                    , text: 'Successfully Deleted Resolution'
                                    , buttonsStyling: false
                                    , icon: 'success'
                                    , buttonsStyling: false
                                    , customClass: {
                                        confirmButton: 'btn btn-sm btn-success'
                                    , }
                                , });
                                table.row($(this_button).parents('tr')).remove().draw();
                            } else {
                                swal.fire({
                                    title: `Delete Error`
                                    , text: 'Sorry, Deleting this resolution is prohibited!'
                                    , buttonsStyling: false
                                    , icon: 'warning'
                                    , buttonsStyling: false
                                    , customClass: {
                                        confirmButton: 'btn btn-sm btn-danger'
                                    , }
                                , });
                            }
                        }
                    });

                }
            });

        });
        @endif

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
