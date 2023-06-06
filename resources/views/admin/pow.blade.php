@extends('layouts.app')
<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }

</style>

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
    <div id="">
        <div class="col-sm-12">
            <div class="modal" tabindex="-1" role="dialog" id="pow_remarks_modal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Program of Work Remarks</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="col-sm-12" method="POST" id="pow_remarks_form" action="{{route('submit_pow_remarks')}}">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-xs-12 col-sm-6 col-lg-12 d-none">
                                        <label for="">Plan ID</label>
                                        <input type="text" id="pow_remarks_plan_id" name="pow_remarks_plan_id" class="form-control  " readonly value="{{old('pow_remarks_plan_id')}}">
                                        <label class="error-msg text-red">@error('pow_remarks_plan_id'){{$message}}@enderror
                                        </label>
                                    </div>

                                    <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                                        <label for="reason">Reason<span class="text-red">*</span></label>
                                        <textarea type="text" id="reason" name="reason" class="form-control ">{{old('reason')}}</textarea>
                                        <label class="error-msg text-red">@error('reason'){{$message}}@enderror
                                        </label>
                                    </div>

                                    <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                                        <label for="remarks">Remarks</label>
                                        <textarea type="text" id="remarks" name="remarks" class="form-control ">{{old('remarks')}}</textarea>
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
        </div>

        <div class="modal" tabindex="-1" role="dialog" id="pow_modal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="pow_modal_title">Program of Work</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="col-sm-12" method="POST" id="pow_form" action="{{route('submit_pow')}}">
                            @csrf
                            <div class="row">
                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                                    <label for="date_pow_added">Plan Id/s</label>
                                    <input type="text" id="plan_ids" name="plan_ids" class="form-control  " readonly value="{{old('plan_ids')}}">
                                    <label class="error-msg text-red">@error('plan_ids'){{$message}}@enderror
                                    </label>
                                </div>
                                <!-- date pow added -->
                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="date_pow_added">POW Received Date<span class="text-red">*</span></label>
                                    <input type="text" id="date_pow_added" name="date_pow_added" class="form-control datepicker" value="{{old('date_pow_added')}}">
                                    <label class="error-msg text-red">@error('date_pow_added'){{$message}}
                                        @enderror
                                    </label>
                                </div>

                                <!--Project Cost -->
                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="project_cost">Project Cost<span class="text-red">*</span></label>
                                    <input type="" id="project_cost" name="project_cost" class="form-control  money2" value="{{old('project_cost')}}">
                                    <label class="error-msg text-red">@error('project_cost'){{$message}}@enderror
                                    </label>
                                </div>

                                <!-- date pow added -->
                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="project_engineer">Project Engineer<span class="text-red">*</span></label>
                                    <input type="text" id="project_engineer" name="project_engineer" class="form-control" value="{{old('project_engineer')}}">
                                    <label class="error-msg text-red">@error('project_engineer'){{$message}}
                                        @enderror
                                    </label>
                                </div>


                                <!-- Duration -->
                                <div class="form-group col-xs-6 col-sm-6 col-lg-6">
                                    <label for="date_added">Duration (CD) <span class="text-red">*</span></label>
                                    <input type="text" id="duration" name="duration" class="form-control " value="{{old('duration')}}">
                                    <label class="error-msg text-red">@error('duration'){{$message}}@enderror
                                    </label>
                                </div>

                                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                                    <label for="additional_remarks">Remarks</label>
                                    <textarea type="text" id="additional_remarks" name="additional_remarks" class="form-control ">{{old('additional_remarks')}}</textarea>
                                    <label class="error-msg text-red">@error('additional_remarks'){{$message}}@enderror
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
                    <form class="row" id="app_filter" method="post" action="{{route('filter_app')}}">
                        @csrf


                        <!-- POW -->
                        <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                            <label for="pow">POW<span class="text-red">*</span></label>
                            <input type="text" id="pow" name="pow" class="form-control" value="{{$pow}}">
                            <label class="error-msg text-red">@error('pow'){{$message}}@enderror
                            </label>
                        </div>

                        <!-- Status -->
                        <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                            <label for="status">Status<span class="text-red">*</span></label>
                            <input type="text" id="status" name="status" class="form-control" value="{{$status}}">
                            <label class="error-msg text-red">@error('status'){{$message}}@enderror
                            </label>
                        </div>

                        <!-- project year -->
                        <div class="form-group col-xs-3 col-sm-3 col-lg-3 mb-0">
                            <label for="project_year" class="input-sm">Project Year </label>
                            <input class="form-control  yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}">
                            <label class="error-msg text-red">@error('project_year'){{$message}}@enderror
                            </label>
                        </div>

                        <!-- Fund Category  -->
                        <div class="form-group col-xs-3 col-sm-3 col-lg-3 mb-0">
                            <label for="mode_of_procurement">Mode of Procurement</label>
                            <select type="" id="mode_of_procurement" name="mode_of_procurement" class="form-control " value="{{old('mode_of_procurement')}}">
                                <option value="" {{ old('mode_of_procurement') == '' ? 'selected' : ''}}>Select mode of procurement</option>
                                @foreach($modes as $mode)
                                <option value="{{$mode->mode_id}}" {{ old('mode_of_procurement') == $mode->mode_id ? 'selected' : ''}}>{{$mode->mode}}</option>
                                @endforeach
                            </select>
                            <label class="error-msg text-red">@error('mode_of_procurement'){{$message}}@enderror
                            </label>
                        </div>


                        <!-- Fund Category  -->
                        <div class="form-group col-xs-3 col-sm-3 col-lg-3 mb-0">
                            <label for="fund_category">Fund Category</label>
                            <select type="" id="fund_category" name="fund_category" class="form-control " value="{{old('fund_category')}}">
                                <option value="" {{ old('fund_category') == '' ? 'selected' : ''}}>Select Fund Category</option>
                                @foreach($fund_categories as $category)
                                <option value="{{$category->fund_category_id}}" {{ old('fund_category') == $category->fund_category_id ? 'selected' : ''}}>{{$category->title}}</option>
                                @endforeach
                            </select>
                            <label class="error-msg text-red">@error('fund_category'){{$message}}@enderror
                            </label>
                        </div>

                        <!-- Account account_classification -->
                        <div class="form-group col-xs-3 col-sm-3 col-lg-3 mb-0">
                            <label for="account_classification">Account Classication</label>
                            <select type="" id="account_classification" name="account_classification" class="form-control " value="{{old('account_classification')}}">
                                <option value="" {{ old('account_classification') == '' ? 'selected' : ''}}>Select Account Classification</option>
                                @foreach($classifications as $classification)
                                <option value="{{$classification->account_id}}" {{ old('account_classification') == $classification->account_id ? 'selected' : ''}}>{{$classification->classification}}</option>
                                @endforeach
                            </select>
                            <label class="error-msg text-red">@error('account_classification'){{$message}}@enderror
                            </label>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="app_table">
                        <thead class="">
                            <tr class="bg-primary text-white">
                                <th class="text-center"></th>
                                <th class="text-center">ID</th>
                                <th class="text-center">APP/SAPP No.</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Project No.</th>
                                <th class="text-center">Project Title</th>
                                <th class="text-center">POW Received Date</th>
                                <th class="text-center">Duration</th>
                                <th class="text-center">Location</th>
                                <th class="text-center">Mode of Procurement</th>
                                <th class="text-center">Project Engineer</th>
                                <th class="text-center">Posting</th>
                                <th class="text-center">Sub/Open of Bids</th>
                                <th class="text-center">Notice of Award</th>
                                <th class="text-center">Contract Signing</th>
                                <th class="text-center">Source of Fund</th>
                                <th class="text-center">Account Code</th>
                                <th class="text-center">Classification</th>
                                <th class="text-center">Approved Budget Cost</th>
                                <th class="text-center">Actual Project Cost</th>
                                <th class="text-center">Project Year</th>
                                <th class="text-center">Year Funded</th>
                                <th class="text-center">Rebid Count</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">POW Remarks</th>
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
    let account_classification = "{{old('account_classification')}}";
    $("#account_classification").val(account_classification);

    // table data
    let data = @json(session('filtered_data'));

    if (data == null) {
        data = @json($project_plans);
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

    $(".money2").click(function() {
        $('.money2').mask("#,##0.00", {
            reverse: true
        });
    });

    $(".money2").keyup(function() {
        $('.money2').mask("#,##0.00", {
            reverse: true
        });
    });


    $(".money2").focusout(function() {
        $('.money2').unmask();
        $('.money2').mask("###0.00", {
            reverse: true
        })
    });

    var table = $('#app_table').DataTable({
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
            },

            {
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
                "data": "date_pow_added"
                , render: function(data, type, row) {
                    if (data != null) {
                        return "<div style='white-space: nowrap'>@if(in_array('update',$user_privilege))<button class='btn btn-sm shadow-0 border-0 text-white btn-success edit_pow' data-toggle='tooltip' data-placement='top' title='Edit' value='" + row.plan_id + "' ><i class='ni ni-ruler-pencil'></i></button> @endif @if(in_array('delete',$user_privilege)) <button value='" + row.plan_id + "' class='btn btn-sm  shadow-0 border-0 btn-danger delete_btn' data-toggle='tooltip' data-placement='top' title='Delete'><i class='ni ni-basket text-white'></i></button>@endif</div>";
                    } else {
                        return "@if(in_array('add',$user_privilege))<div style='white-space: nowrap'><button  class='btn btn-sm shadow-0 border-0 btn-primary text-white add_pow' data-toggle='tooltip' data-placement='top' title='Add'  value='" + row.plan_id + "'>Add Pow</button><button  class='btn btn-sm shadow-0 border-0 btn-warning text-white add_reason' data-toggle='tooltip' data-placement='top' title='Add Reason'  value='" + row.plan_id + "'>Add Reason</button></div> @endif";
                    }
                }
            }
            , {
                "data": "plan_id"
            }
            , {
                "data": "app_group_no"
            }
            , {
                "data": "project_type"
            }
            , {
                "data": "project_no"
                , render: function(data, type, row) {

                    return "<a  class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank'  href='/view_project/" + row.plan_id + "'>" + data + "</i></a>";

                }
            }
            , {
                "data": "project_title"
            }
            , {
                "data": "date_pow_added"
            }
            , {
                "data": "duration"
            }
            , {
                "data": "municipality_display"
            }
            , {
                "data": "mode"
            }
            , {
                "data": "project_engineer"
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
                "data": "pow_remarks"
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
            targets: [1, 11, 12, 13, 14]
            , visible: false
            , searchable: false
        }]
    , });

    // show inputs/messages on load
    var oldInputs = '{{ count(session()->getOldInput()) }}';
    console.log(@json(old('plan_ids')));
    if (@json(old('plan_ids')) != null && @json(old('plan_ids')) != "null") {
        $("#pow_modal").modal('show');
    } else if (@json(old('pow_remarks_plan_id')) != null && @json(old('pow_remarks_plan_id')) != "null") {
        $("#pow_remarks_modal").modal('show');
    } else if (oldInputs > 6) {
        $('#filter').removeClass('d-none');
        $('#filter_btn').removeClass('d-none');
        $("#show_filter").html("Hide Filter");
    } else {
        $("#project_year").val("{{$year}}");
    }

    if ("{{session('message')}}") {
        if ("{{session('message')}}" == "duplicate") {
            swal.fire({
                title: `Duplicate`
                , text: 'We already have the same entry in the database!'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-warning'
                , icon: 'warning'
            });
        } else if ("{{session('message')}}" == "success") {
            swal.fire({
                title: `Success`
                , text: 'Successfully saved to database'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'success'
            });
            $("#pow_modal").modal('hide');
        } else if ("{{session('message')}}" == "remarks_success") {
            swal.fire({
                title: `Success`
                , text: 'Successfully Saved Reason'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'success'
            });
            $("#pow_modal").modal('hide');
        } else if ("{{session('message')}}" == "delete_success") {
            Swal.fire({
                title: `Delete Success`
                , text: 'Successfully deleted POW'
                , confirmButtonText: 'Ok'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'success'
            });
            $("#pow_modal").modal('hide');
        } else if ("{{session('message')}}" == "multiple pow") {
            swal.fire({
                title: `Error`
                , text: 'Sorry, Some of the selected rows already have POW!'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'warning'
            });
        } else if ("{{session('message')}}" == "all true") {
            swal.fire({
                title: `Error`
                , text: 'Sorry, All of the rows already have POW!'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'warning'
            });
        } else if ("{{session('message')}}" == "delete_error") {
            swal.fire({
                title: `Error`
                , text: 'You cannot delete POW for APP with Defined Schedules'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'warning'
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

    // show delete
    @if(in_array('delete', $user_privilege))
    $('#app_table tbody').on('click', '.delete_btn', function(e) {

        Swal.fire({
            text: 'Are you sure to delete POW?'
            , showCancelButton: true
            , confirmButtonText: 'Delete'
            , cancelButtonText: "Don't Delete"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-danger'
            , cancelButtonClass: 'btn btn-sm btn-default'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                window.location.href = "/delete_pow/" + $(this).val();
            }
        });

    });
    @endif

    @if(in_array('add', $user_privilege))
    $('#app_table tbody').on('click', '.add_pow', function(e) {
        table.rows().deselect();
        var project_cost = table.row($(this).parents('tr')).data().abc;
        project_cost = project_cost.replaceAll(",", "");
        $("#pow_form")[0].reset();
        $("#pow_modal_title").html("Program of Work");
        $("#duration").val("");
        $("#plan_ids").val($(this).val());
        $("#date_pow_added").datepicker('setDate', moment().format('MM/DD/YYYY'));
        $("#pow_modal").modal('show');
        $("#project_cost").val(project_cost);
    });

    $('#app_table tbody').on('click', '.add_reason', function(e) {
        table.rows().deselect();
        var project_cost = table.row($(this).parents('tr')).data().abc;
        project_cost = project_cost.replaceAll(",", "");
        $("#pow_remarks_form")[0].reset();
        $("#pow_remarks_plan_id").val($(this).val());
        $("#remarks").val("");
        $("#pow_remarks_modal").modal('show');
        $("#project_cost").val(project_cost);
    });
    @endif

    @if(in_array('update', $user_privilege))
    $('#app_table tbody').on('click', '.edit_pow', function(e) {
        table.rows().deselect();
        var str = table.row($(this).parents('tr')).data().duration;
        str = str.replace(" CD", "");
        var project_cost = table.row($(this).parents('tr')).data().project_cost;
        if (project_cost != null) {
            var project_cost = table.row($(this).parents('tr')).data().abc;
            project_cost = project_cost.replaceAll(",", "");
        }
        $("#project_engineer").val(table.row($(this).parents('tr')).data().project_engineer);
        $("#project_cost").val(project_cost);
        $("#pow_modal_title").html("Program of Work");
        $("#plan_ids").val($(this).val());
        $("#duration").val(str);
        $("#additional_remarks").val(table.row($(this).parents('tr')).data().pow_remarks);

        $("#date_pow_added").datepicker('setDate', moment(table.row($(this).parents('tr')).data().date_pow_added).format('MM/DD/YYYY'));
        $("#pow_modal").modal('show');
    });
    @endif


    $("#add_pows").click(function functionName() {
        if (table.rows({
                selected: true
            }).count() == 0) {
            Swal.fire({
                title: "Warning"
                , text: 'Please select rows to add POW '
                , confirmButtonText: 'Ok'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-info'
                , icon: 'info'
            });
        } else {
            $("#pow_form")[0].reset();
            let rows = table.rows({
                selected: true
            });
            var plan_ids = table.cells(rows.nodes(), 1).data().toArray()
            $("#plan_ids").val(plan_ids.toString());
            $("#pow_modal").modal('show');

        }
    });

    var project_engineer_autocomplete_init = {
        minLength: 0
        , autocomplete: true
        , source: function(request, response) {

            $.ajax({
                'url': '/autocomplete_project_engineers'
                , 'data': {
                    "_token": "{{ csrf_token() }}"
                    , "term": request.term
                }
                , 'method': "post"
                , 'dataType': "json"
                , 'success': function(data) {
                    console.log(data);
                    response(data);
                }
            });
        }
        , select: function(event, ui) {
            if (ui.item.id != '') {
                $(this).val(ui.item.value);
            } else {
                // $(this).val('');
            }
            return false;
        }
        , change: function(event, ui) {


        }
    }


    $("#project_engineer").autocomplete(project_engineer_autocomplete_init).focus(function() {
        $(this).autocomplete('search', $(this).val())
    });

    $("#project_year").change(function functionName() {
        $("#date_added").val("");
        $("#month_added").val("");
    });

    $("#date_added").change(function functionName() {
        $("#project_year").val("");
        $("#month_added").val("");
    });

    $("#month_added").change(function functionName() {
        $("#project_year").val("");
        $("#date_added").val("");
    });


    $("#filter_btn").click(function() {
        $("#app_filter").submit();
    });


    $("#project_year").change(function() {
        $("#app_filter").submit();
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

    $("#plan_ids").keyup(function() {
        var str = $(this).val();
        str = str.replace(/[^0-9\-\a-zA-Z \@\#\.\/\,]/g, '');
        $(this).val(str);
    });

</script>
@endpush
