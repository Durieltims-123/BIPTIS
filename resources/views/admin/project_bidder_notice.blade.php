@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

    <div class="modal" tabindex="-1" role="dialog" id="bidder_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="bidder_modal_title"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="col-sm-12" method="POST" id="bidders_form" action="{{route('submit_notice')}}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                                <label for="notice_id">Notice ID
                                </label>
                                <input type="text" id="notice_id" name="notice_id" class="form-control form-control-sm" readonly value="{{old('notice_id')}}">
                                <label class="error-msg text-red">@error('notice_id'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                                <label for="project_bid">Project Bid
                                </label>
                                <input type="text" id="project_bid" name="project_bid" class="form-control form-control-sm" readonly value="{{old('project_bid')}}">
                                <label class="error-msg text-red">@error('project_bid'){{$message}}@enderror

                                </label>
                            </div>


                            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                <label for="date_generated">Date Generated<span class="text-red">*</span>
                                </label>
                                <input type="text" id="date_generated" name="date_generated" class="form-control form-control-sm bg-white datepicker" value="{{old('date_generated')}}">
                                <label class="error-msg text-red">@error('date_generated'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                <label for="date_released">Date Released to Contractor<span class="text-red"></span>
                                </label>
                                <input type="text" id="date_released" name="date_released" class="form-control form-control-sm bg-white datepicker" value="{{old('date_released')}}">
                                <label class="error-msg text-red">@error('date_released'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                <label for="date_received_by_contractor">Date Received by Contractor<span class="text-red"></span>
                                </label>
                                <input type="text" id="date_received_by_contractor" name="date_received_by_contractor" class="form-control form-control-sm bg-white datepicker" value="{{old('date_received_by_contractor')}}">
                                <label class="error-msg text-red">@error('date_received_by_contractor'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 {{ ($notice_type == 'NOD'||$notice_type == 'NOI'||$notice_type == 'NOPD') ? '' : 'd-none'}}">
                                <label for="mr_due_date">MR Due Date<span class="text-red"></span>
                                </label>
                                <input type="text" id="mr_due_date" name="mr_due_date" class="form-control form-control-sm bg-white " readonly value="{{old('mr_due_date')}}">
                                <label class="error-msg text-red">@error('mr_due_date'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                <label for="date_received_by_bac">Date Received by BAC Infra/Support<span class="text-red"></span>
                                </label>
                                <input type="text" id="date_received_by_bac" name="date_received_by_bac" class="form-control form-control-sm bg-white datepicker" value="{{old('date_received_by_bac')}}">
                                <label class="error-msg text-red">@error('date_received_by_bac'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                                <label for="notice_type">Notice type<span class="text-red">*</span>
                                </label>
                                <select type="text" id="notice_type" name="notice_type" class="form-control form-control-sm bg-white" value="{{old('notice_type')}}">
                                    <option value="NOD" {{ $notice_type == 'NOD' ? 'selected' : ''}}>Notice of Disqualification</option>
                                    <option value="NOI" {{ $notice_type == 'NOI' ? 'selected' : ''}}>Notice of Ineligibility</option>
                                    <option value="NOPQ" {{ $notice_type == 'NOPQ' ? 'selected' : ''}}>Notice of Post Qualification</option>
                                    <option value="NOPD" {{ $notice_type == 'NOPD' ? 'selected' : ''}}>Notice of Post Disqualification</option>
                                    <option value="NTLB" {{ $notice_type == 'NTLB' ? 'selected' : ''}}>Notice to Loosing Bidders</option>
                                </select>
                                <label class="error-msg text-red">@error('notice_type'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                                <label for="remarks">Remarks
                                </label>
                                <textarea type="text" id="remarks" name="remarks" class="form-control form-control-sm">{{old('remarks')}}</textarea>
                                <label class="error-msg text-red">@error('remarks'){{$message}}@enderror
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


    <div id="app">
        <div class="card shadow">
            <div class="card shadow border-0">
                <div class="card-header">
                    <h2 id="title">{{ $title  }}</h2>
                </div>
                <div class="card-body">
                    <div class="col-sm-12" id="filter">
                        <form class="row" id="filter_bidder_notices" method="post" action="{{route('prepare.filter_bidder_notices')}}">
                            @csrf
                            <!-- year -->
                            <div class="form-group col-xs-6 col-sm-4 col-lg-2 mb-0">
                                <label for="year" class="input-sm">Year
                                </label>
                                <input class="form-control form-control-sm yearpicker" id="year" name="year" format="yyyy" minimum-view="year" value="{{old('year')}}">
                                <label id="year_error" class="error-msg text-red">@error('year'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-4 col-lg-2 mb-0 d-none">
                                <label for="filter_notice_type" class="input-sm">Notice Type
                                </label>
                                <input class="form-control form-control-sm" id="filter_notice_type" name="filter_notice_type" value="{{$notice_type}}">
                                <label id="filter_notice_type_error" class="error-msg text-red">@error('filter_notice_type'){{$message}}@enderror
                                </label>
                            </div>

                            <div class="form-group col-xs-6 col-sm-4 col-lg-2 mb-0">
                                <label for="notice_status" class="input-sm">Notice Status
                                </label>
                                <select type="text" id="notice_status" name="notice_status" class="form-control form-control-sm bg-white" value="{{old('notice_status')}}">
                                    <option value="all" {{ old('notice_status') == 'all' ? 'selected' : ''}}>All</option>
                                    <option value="prepared" {{ old('notice_status') == 'prepared' ? 'selected' : ''}}>Prepared/Finished</option>
                                    <option value="for_preparation" {{ old('notice_status') == 'for_preparation' ? 'selected' : ''}}>For Preparation</option>
                                </select>
                                <label id="filter_notice_type_error" class="error-msg text-red">@error('notice_status'){{$message}}@enderror
                                </label>
                            </div>


                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered wrap" id="bidders_table">
                            <thead class="">
                                <tr class="bg-primary text-white">
                                    <th class="text-center"></th>
                                    <th class="text-center">Project Bid</th>
                                    <th class="text-center">Cluster</th>
                                    <th class="text-center">Opening Date</th>
                                    <th class="text-center">Project No.</th>
                                    <th class="text-center">Project Title</th>
                                    <th class="text-center">Business Name</th>
                                    <th class="text-center">Owner</th>
                                    <th class="text-center">Date Generated</th>
                                    <th class="text-center">Date Released To Contractor</th>
                                    <th class="text-center">Date Received by Contractor</th>
                                    <th class="text-center">Date Received by BAC Infra/Support</th>
                                    <th class="text-center">MR Due Date</th>
                                    <th class="text-center">Bid as Read</th>
                                    <th class="text-center">Bid as Evaluated</th>
                                    <th class="text-center">Bid as Calculated</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Post Qual Start</th>
                                    <th class="text-center">Post Qual End</th>
                                    <th class="text-center">TWG Evaluation</th>
                                    <th class="text-center">TWG Remarks</th>
                                    <th class="text-center">Notice Remarks</th>
                                    <th class="text-center">Notice Type</th>

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
    $('#bidders_table thead tr').clone(true).appendTo('#bidders_table thead');
    $('#bidders_table thead tr:eq(1)').removeClass('bg-primary');


    $(".yearpicker").datepicker({
        format: 'yyyy'
        , viewMode: "years"
        , minViewMode: "years"
    });


    let data = @json(session('project_bidders'));

    if (data == null) {
        data = @json($project_bidders);
    }
    let target_invisible = [1, 7];

    if (@json($notice_type) == "NOI" || @json($notice_type) == "NOD") {
        target_invisible = [1, 7, 13, 14, 15, 16, 17, 18];
    } else if (@json($notice_type) == "NOPQ") {
        target_invisible = [1, 7, 18];
    } else {}
    var table = $('#bidders_table').DataTable({
        language: {
            paginate: {
                next: '<i class="fas fa-angle-right">'
                , previous: '<i class="fas fa-angle-left">'
            }
        }
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
        , dataType: 'json'
        , columns: [{
                data: "project_bidder_notice_id"
                , render: function(data, type, row) {

                    if (data != null && row.notice_type == @json($notice_type)) {
                        return '<div style="white-space: nowrap">@if(in_array("update",$user_privilege))<button data-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-sm btn btn-sm btn-success shadow-0 border-0 edit-notice"><i class="ni ni-ruler-pencil text-white"></i></button>@endif <a  class="btn btn-sm btn btn-sm shadow-0 border-0 btn-primary text-white"  href="/generate_notice/' + data + '"><i class="ni ni-cloud-download-95"></i></a></div>';
                    } else {
                        return '@if(in_array("add",$user_privilege))<button data-toggle="tooltip" data-placement="top" title="Create" class="btn btn-sm btn btn-sm btn-primary shadow-0 border-0 create-notice"><i class="ni ni-fat-add text-white"></i></button>@endif';
                    }
                }
            }
            , {
                data: "main_id"
            }
            , {
                data: "plan_cluster_id"
            }
            , {
                data: "open_bid"
            }
            , {
                data: "project_no"
            }
            , {
                data: "project_title"
            }
            , {
                data: "business_name"
            }
            , {
                data: "owner"
            }
            , {
                data: "notice_date_generated"
            }
            , {
                data: "notice_date_released"
            }
            , {
                data: "date_received_by_contractor"
            }
            , {
                data: "notice_date_received"
            }
            , {
                data: "mr_due_date"
            }
            , {
                data: "proposed_bid"
                , render: function(data, type, row) {
                    if (data != null) {
                        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                    return "";
                }
            }
            , {
                data: "bid_as_evaluated"
                , render: function(data, type, row) {
                    if (data != null) {
                        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                    return "";
                }
            }
            , {
                data: "twg_final_bid_evaluation"
                , render: function(data, type, row) {
                    if (data != null) {
                        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                    return "";
                }
            }
            , {
                data: "bid_status"
            }
            , {
                data: "post_qual_start"
            }
            , {
                data: "post_qual_end"
            }
            , {
                data: "twg_evaluation_status"
            }
            , {
                data: "twg_evaluation_remarks"
            }
            , {
                data: "notice_remarks"
            }
            , {
                data: "notice_type"
            }
        ]
        , orderCellsTop: true
        , select: {
            style: 'multi'
            , selector: 'td:not(:first-child)'
        }
        , order: [
            [2, "asc"]
        ]
        , columnDefs: [{
                targets: 17
                , render: function(data, type, row) {
                    if (data != null) {
                        return data.substr(0, 100) + "...";
                    }
                    return data;
                }
            }
            , {
                targets: 0
                , orderable: false
            }, {
                targets: target_invisible
                , visible: false
            }
        ]
        , rowGroup: {
            startRender: function(rows, group) {
                var group_title = "Cluster" + " " + group;
                return group_title;
            }
            , endRender: null
            , dataSrc: 'plan_cluster_id'
        }
    });

    let old_year = @json(old('year'));
    if (old_year == null) {
        $("#year").val(@json($year));
    }

    var notice_id = @json(old('project_bid'));
    if (notice_id != null && "{{session('message')}}" == "") {
        $("#bidder_modal_title").html("Add/Edit Notice");
        $("#bidder_modal").modal('show');
    }



    if ("{{session('message')}}") {
        if ("{{session('message')}}" == "success") {
            swal.fire({
                title: `Success`
                , text: 'Successfully Saved'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'success'
            });
            $("#bidder_modal").modal('hide');
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

    $('#bidders_table thead tr:eq(1) th').each(function(i) {
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

    @if(in_array("add", $user_privilege))
    $('#bidders_table tbody').on('click', '.create-notice', function(e) {
        var data = table.row($(this).parents(' tr')).data();
        $("#mr_due_date").val('');
        if (data.project_bidder_notice_id == "" || data.project_bidder_notice_id == null) {
            $("#date_generated").val('');
            $("#date_released").val('');
            $("#date_received_by_bac").val('');
            $("#notice_id").val('');
            $("#remarks").val('');
        } else {
            $("#date_generated").val('');
            $("#date_released").val('');
            $("#date_received_by_bac").val('');
            $("#notice_id").val(data.project_bidder_notice_id);
            $("#remarks").val(data.notice_remarks);
        }

        $("#project_bid").val(data.main_id);
        var data = table.row($(this).parents(' tr')).data();
        $("#bidder_modal_title").html('Create Notice');
        $("#bidder_modal").modal('show');
    });
    @endif
    @if(in_array("update", $user_privilege))
    $('#bidders_table tbody').on('click', '.edit-notice', function(e) {
        $("#mr_due_date").val('');
        var data = table.row($(this).parents('tr')).data();
        if (data.project_bidder_notice_id == "" || data.project_bidder_notice_id == null) {
            $("#date_generated").val('');
            $("#date_released").val('');
            $("#date_received_by_contractor").val('');
            $("#date_received_by_bac").val('');
            $("#notice_id").val('');
            $("#bidder_modal_title").html('Add Notice');
        } else {
            $("#date_generated").datepicker('setDate', moment(data.notice_date_generated, 'Y-MM-DD').format("MM/DD/YYYY"));
            if (data.notice_date_released != null) {
                $("#date_released").datepicker('setDate', moment(data.notice_date_released, 'Y-MM-DD').format("MM/DD/YYYY"));
            } else {
                $("#date_released").val('');
            }
            if (data.notice_date_received != null) {
                $("#date_received_by_contractor").datepicker('setDate', moment(data.date_received_by_contractor, 'Y-MM-DD').format("MM/DD/YYYY"));
                $("#date_received_by_bac").datepicker('setDate', moment(data.notice_date_received, 'Y-MM-DD').format("MM/DD/YYYY"));
            } else {
                $("#date_received_by_bac").val('');
                $("#date_received_by_contractor").val('');
            }
            $("#notice_id").val(data.project_bidder_notice_id);
            $("#remarks").val(data.notice_remarks);
        }

        if (data.mr_due_date != null) {
            $("#mr_due_date").val(moment($('#date_released').val()).add(3, 'days').format("MM/DD/YYYY"));
            // $("#mr_due_date").val(moment(data.notice_date_received, 'Y-MM-DD').format("MM/DD/YYYY"));
        }

        $("#project_bid").val(data.main_id);

        $("#bidder_modal_title").html('Edit Notice');
        $("#bidder_modal").modal('show');
    });
    @endif

    $("#date_released").change(function() {
        if ($("#mr_due_date").parents('.form-group').hasClass('d-none') === false && $(this).val() != "") {
            $("#mr_due_date").val(moment($('#date_released').val()).add(3, 'days').format("MM/DD/YYYY"));
            // $.ajax({
            //     'url': '/calculate_due_date'
            //     , 'data': {
            //         "_token": "{{ csrf_token() }}"
            //         , "date": $(this).val()
            //         , "days": 3
            //         , "date_type": "Working Days"
            //     , }
            //     , 'method': "post"
            //     , 'success': function(data) {
            //         if (data != null) {
            //             $("#mr_due_date").val(data);
            //         } else {
            //             $("#mr_due_date").val("");
            //         }
            //     }
            // });
        } else {
            $("#mr_due_date").val("");
        }
    });

    $("#notice_status").change(function() {
        $("#filter_bidder_notices").submit();
    });

    $("#year").change(function() {
        $("#filter_bidder_notices").submit();
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


    $("#filter_btn").click(function functionName() {
        $("#filter_bidder_notices").submit();
    });

</script>
@endpush
