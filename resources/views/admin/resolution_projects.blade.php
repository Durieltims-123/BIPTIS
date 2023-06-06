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
                        <form class="row" id="filter_resolution" method="post" action="{{route('filter_resolution_projects')}}">
                            @csrf
                            <!-- project year -->
                            <div class="form-group col-xs-3 col-sm-2 col-lg-2 mb-0">
                                <label for="year" class="input-sm">Year </label>
                                <input class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}">
                                <label class="error-msg text-red">@error('year'){{$message}}@enderror
                                </label>
                            </div>
                        </form>
                    </div>

                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="data_table">
                                <thead class="">
                                    <tr class="bg-primary text-white">
                                        <th class="text-center">Resolution Type</th>
                                        <th class="text-center">Date Opened</th>
                                        <th class="text-center">Resolution Number</th>
                                        <th class="text-center">Resolution Date</th>
                                        <th class="text-center">Project Number</th>
                                        <th class="text-center">Project Title</th>
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
        let data = @json($resolution_projects);

        if (@json(old('year')) != null) {
            data = @json(session('resolution_projects'));
        }

        console.log(data);

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
                , {
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
                    data: "type"
                    , render: function(data, type, row) {
                        let display = null;
                        console.log(data);
                        if (data == "RRA") {
                            display = "Resolution Recommending Award";
                        }
                        if (data == "RDF") {
                            display = "Resolution Declaring Failure";
                        }
                        if (data == "RDMR") {
                            display = "Resolution Denying The Motion for Reconsideration";
                        }
                        if (data == "RGMR") {
                            display = "Resolution Granting The Motion for Reconsideration";
                        }
                        if (data == "RRRC") {
                            display = "Resolution Recommending Recall/Cancellation";
                        }

                        return display;
                    }
                }
                , {
                    "data": "open_bid"
                }
                , {
                    "data": "resolution_number"
                }
                , {
                    "data": "resolution_date"
                }
                , {
                    "data": "project_no"
                }
                , {
                    "data": "project_title"
                }
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
            // , columnDefs: [{
            //     targets: 0
            //     , orderable: false
            // }]
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
