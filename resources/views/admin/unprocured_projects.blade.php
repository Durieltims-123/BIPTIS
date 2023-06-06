@extends('layouts.app')
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
    <div id="app">
        <div class="col-sm-12">
            <div class="modal" tabindex="-1" role="dialog" id="revert_modal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="form_modal_title">Revert Project</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body pt-0">
                            <form class="col-sm-12" method="POST" id="submit_revert_project" action="{{route('submit_revert')}}">
                                @csrf
                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 d-none">
                                    <h5 for="">Plan ID</h5>
                                    <input type="text" id="revert_plan_id" name="revert_plan_id" class="form-control form-control-sm" value="{{old('revert_plan_id')}}" readonly>
                                    <label class="error-msg text-red">@error('revert_plan_id'){{$message}}@enderror</label>
                                </div>
                                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mx-auto">
                                    <h5 for="">Project Title</h5>
                                    <input type="text" id="revert_project_title" name="revert_project_title" class="form-control form-control-sm" readonly value="{{old('revert_project_title')}}">
                                    <label class="error-msg text-red">@error('revert_project_title'){{$message}}@enderror</label>
                                </div>
                                <div class="form-group col-xs-12 col-sm-12 col-lg-12">
                                    <label for="plan_id">Remarks<span class="text-red">*</span></label>
                                    <textarea type="text" id="revert_remarks" name="revert_remarks" class="form-control form-control-sm">
                  </textarea>
                                    <label class="error-msg text-red">@error('revert_remarks'){{$message}}@enderror</label>
                                </div>

                                <div class="d-flex justify-content-center col-sm-12">
                                    <button class="btn btn-primary text-center">Submit</button>
                                </div>

                            </form>
                        </div>
                    </div>
                    </di>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card shadow border-0">
                <div class="card-header">
                    <h2 id="title">{{$title}}</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered wrap" id="app_table">
                            <thead class="">
                                <tr class="bg-primary text-white">
                                    <th class="text-center"></th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">APP/SAPP No.</th>
                                    <th class="text-center">Project No.</th>
                                    <th class="text-center">Project Title</th>
                                    <th class="text-center">Location</th>
                                    <th class="text-center">Mode of Procurement</th>
                                    <th class="text-center">Classification</th>
                                    <th class="text-center">Scheduled Posting</th>
                                    <th class="text-center">Posting</th>
                                    <th class="text-center">Sub/Open of Bids</th>
                                    <th class="text-center">Notice of Award</th>
                                    <th class="text-center">Contract Signing</th>
                                    <th class="text-center">Source of Fund</th>
                                    <th class="text-center">Account Code</th>
                                    <th class="text-center">Classication</th>
                                    <th class="text-center">Approved Budget Cost</th>
                                    <th class="text-center">Actual Project Cost</th>
                                    <th class="text-center">Project Year</th>
                                    <th class="text-center">Year Funded</th>
                                    <th class="text-center">revert Count</th>
                                    <th class="text-center">Status</th>
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
    // change filter account classification
    let account_classification = "{{old('account_classification')}}";
    $("#account_classification").val(account_classification);

    // table data
    let data = @json(session('filtered_data'));

    if (data == null) {
        data = @json($project_plans);
    }

    // sessions/messages
    if ("{{session('message')}}") {
        if ("{{session('message')}}" == "revert_success") {
            swal.fire({
                title: `Success`
                , text: 'Successfully Reverted Projects'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'success'
            });
        } else {
            swal.fire({
                title: `Error`
                , text: 'An error occured please contact your system developer'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'warning'
            });
        }
    }

    if (@json(old('revert_plan_id')) != null && @json(old('revert_plan_id')) != "") {
        $("#revert_modal").modal('show');
    }

    // datatables
    $('#app_table thead tr').clone(true).appendTo('#app_table thead');
    $('#app_table thead tr:eq(1)').removeClass('bg-primary');

    $(".datepicker").datepicker({
        format: 'mm/dd/yyyy'
        , endDate: '{{$year}}'
    });

    $(".yearpicker").datepicker({
        format: 'yyyy'
        , viewMode: "years"
        , minViewMode: "years"
    });

    $(".monthpicker").datepicker({
        format: 'mm-yyyy'
        , startView: 'months'
        , minViewMode: 'months'
    , });

    var table = $('#app_table').DataTable({
        dom: 'Bfrtip'
        , buttons: [{
                text: 'Excel'
                , extend: 'excel'
                , className: 'btn btn-sm shadow-0 border-0 bg-success text-white'
            }
            , {
                text: 'Print'
                , extend: 'print'
                , className: 'btn btn-sm shadow-0 border-0 bg-blue text-white'
            }
        ]
        , data: data
        , columns: [{
                "data": "advertisement"
                , render: function(data, type, row) {
                    return '<div style="white-space: nowrap">@if(in_array("revert",$user_privilege))<button  data-toggle="tooltip" data-placement="top" title="Revert" class="btn btn-sm shadow-0 border-0 btn-warning text-white revert-btn   pt-1" type="button"><i class="ni ni-bold-left"></i></button> @endif @if(in_array("create sapp",$user_privilege))<a  data-toggle="tooltip" data-placement="top" title="Create SAPP" class="btn btn-sm shadow-0 border-0 btn-danger text-white" target="_blank" href="/add_sapp/' + row.plan_id + '"><i class="ni ni-fat-add"></i></a>@endif <a  data-toggle="tooltip" data-placement="top" title="View"  class="btn btn-sm shadow-0 border-0 btn-primary text-white"  href="/view_project/' + row.plan_id + '" target="_blank"><i class="ni ni-tv-2"></i></a></div>';
                }
            }
            , {
                "data": "plan_id"
            }
            , {
                "data": "project_type"
            }
            , {
                "data": "app_group_no"
            }
            , {
                "data": "project_no"
            }
            , {
                "data": "project_title"
            }
            , {
                "data": "municipality_name"
            }
            , {
                "data": "mode"
            }
            , {
                "data": "classification"
            }

            , {
                "data": "procact_advertisement"
                , render: function(data, type, row) {
                    let new_data = "";
                    if (data != null) {
                        new_data = moment(data).format('MMM-YYYY');
                    }
                    return new_data;
                }
            }
            , {
                "data": "abc_post_date"
                , render: function(data, type, row) {
                    return moment(data).format('MMM-YYYY');
                }
            }
            , {
                "data": "sub_open_date"
                , render: function(data, type, row) {
                    return moment(data).format('MMM-YYYY');
                }
            }
            , {
                "data": "award_notice_date"
                , render: function(data, type, row) {
                    return moment(data).format('MMM-YYYY');
                }
            }
            , {
                "data": "contract_signing_date"
                , render: function(data, type, row) {
                    return moment(data).format('MMM-YYYY');
                }
            }
            , {
                "data": "source"
            }
            , {
                "data": "account_code"
            }
            , {
                "data": "classification"
            }
            , {
                "data": "abc"
                , render: function(data, type, row) {
                    if (data != null) {
                        return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                    return "";
                }
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
                "data": "project_year"
            }
            , {
                "data": "year_funded"
            }
            , {
                "data": "re_bid_count"
            }
            , {
                "data": "project_status"
            }
            , {
                "data": "remarks"
            }
        , ]
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
                , width: "100px"
            }
            , {
                width: 200
                , visible: false
                , targets: 1
            , }
        ],

    });

    // inputs
    var oldInputs = '{{ count(session()->getOldInput()) }}';
    if (oldInputs > 0) {
        $('#filter').removeClass('d-none');
        $('#filter_btn').removeClass('d-none');
        $("#show_filter").html("Hide Filter");
    } else {
        $("#project_year").val("{{$year}}");
    }

    // events

    $('#app_table thead tr:eq(1) th').each(function(i) {
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


    @if(in_array('revert', $user_privilege))
    $('#app_table tbody').on('click', '.revert-btn', function(e) {
        var data = table.row($(this).parents('tr')).data();
        Swal.fire({
            title: "Revert Project"
            , text: 'Are you sure to Revert Project?'
            , showCancelButton: true
            , cancelButtonText: "No"
            , confirmButtonText: 'Yes'
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm'
            , cancelButtonClass: 'btn btn-sm btn-default btn btn-sm'
            , icon: 'warning'
        }).then((result) => {
            console.log(data);
            if (result.value == true) {
                $("#revert_remarks").val('');
                $("#revert_plan_id").val(data.plan_id);
                $("#revert_project_title").val(data.project_title);
                $("#revert_modal").modal('show');
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
