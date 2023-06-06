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
                        <form class="row" id="app_filter" method="post" action="{{route('filter_app')}}">
                            @csrf

                            <!-- APP TYPE -->
                            <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                                <label for="app_type">APP Type <span class="text-red">*</span></label>
                                <input type="text" id="app_type" name="app_type" class="form-control" value="{{$project_type}}">
                                <label class="error-msg text-red">@error('app_type'){{$message}}@enderror
                                </label>
                            </div>

                            <!-- POW -->
                            <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                                <label for="pow">POW<span class="text-red">*</span></label>
                                <input type="text" id="pow" name="pow" class="form-control" value="">
                                <label class="error-msg text-red">@error('pow'){{$message}}@enderror
                                </label>
                            </div>

                            <!-- status -->
                            <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0 d-none">
                                <label for="status">Status<span class="text-red">*</span></label>
                                <input type="text" id="status" name="status" class="form-control" value="{{$status}}">
                                <label class="error-msg text-red">@error('status'){{$message}}@enderror
                                </label>
                            </div>


                            <!-- project year -->
                            <div class="form-group col-xs-2 col-sm-2 col-lg-2 mb-0">
                                <label for="project_year" class="input-sm">Project Year </label>
                                <input class="form-control form-control-sm yearpicker" id="project_year" name="project_year" format="yyyy" minimum-view="year" value="{{old('project_year')}}">
                                <label class="error-msg text-red">@error('project_year'){{$message}}@enderror
                                </label>
                            </div>

                            <!-- Fund Category  -->
                            <div class="form-group col-xs-2 col-sm-2 col-lg-2 mb-0">
                                <label for="mode_of_procurement">Mode of Procurement</label>
                                <select type="" id="mode_of_procurement" name="mode_of_procurement" class="form-control form-control-sm" value="{{old('mode_of_procurement')}}">
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
                                <select type="" id="fund_category" name="fund_category" class="form-control form-control-sm" value="{{old('fund_category')}}">
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
                                <select type="" id="account_classification" name="account_classification" class="form-control form-control-sm" value="{{old('account_classification')}}">
                                    <option value="" {{ old('account_classification') == '' ? 'selected' : ''}}>Select Account Classification</option>
                                    @foreach($classifications as $classification)
                                    <option value="{{$classification->account_id}}" {{ old('account_classification') == $classification->account_id ? 'selected' : ''}}>{{$classification->classification}}</option>
                                    @endforeach
                                </select>
                                <label class="error-msg text-red">@error('account_classification'){{$message}}@enderror
                                </label>
                            </div>

                            @if($project_type==="supplemental")
                            <div class="form-group col-xs-2 col-sm-2 col-lg-2 mb-0">
                                <label for="sapp_number" class="input-sm">SAPP Number</label>
                                <input class="form-control form-control-sm " id="sapp_number" name="sapp_number" value="{{old('sapp_number')}}">
                                <label class="error-msg text-red">@error('sapp_number'){{$message}}@enderror
                                </label>
                            </div>
                            @endif

                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered wrap" id="app_table">
                            <thead class="">
                                <tr class="bg-primary text-white">
                                    <th class="text-center"></th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">APP/SAPP No.</th>
                                    <th class="text-center">Project No.</th>
                                    <th class="text-center">Project Title</th>
                                    <th class="text-center">Location</th>
                                    <th class="text-center">Mode of Procurement</th>
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
                                    <th class="text-center">Remarks</th>
                                    <th class="text-center">Parent</th>
                                    <th class="text-center">Child</th>
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

    // format to currency
    Number.prototype.toCurrencyString = function() {
        return this.toFixed(2).replace(/(\d)(?=(\d{3})+\b)/g, '$1 ');
    }


    // sessions/messages
    if ("{{session('message')}}") {
        if ("{{session('message')}}" == "success") {
            swal.fire({
                title: `Success`
                , text: 'Successfully deleted from database'
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
                    $("#app_filter").submit();
                }
            }
            , @if(in_array("add", $user_privilege)) {
                text: 'Add APP'
                , className: 'btn btn-sm shadow-0 border-0 bg-danger text-white'
                , action: function(e, dt, node, config) {
                    let type = @json($project_type);
                    var currentUrl = window.location.href;
                    currentUrl = currentUrl.replace('http://', '');
                    currentUrl = currentUrl.split('/');

                    if (type == "regular") {
                        window.open('http://' + currentUrl[0] + "/add_regular_app", '_blank');
                    }
                    if (type == "supplemental") {
                        window.open('http://' + currentUrl[0] + "/add_supplemental_app", '_blank');
                    }
                }
            }
            , @endif {
                text: 'Excel'
                , className: 'btn btn-sm shadow-0 border-0 bg-success text-white'
                , action: function(e, dt, node, config) {
                    let type = @json($project_type);
                    $("#app_filter").prop("action", "{{route('download_app')}}");
                    $("#app_filter").submit();
                    $("#app_filter").prop("action", "{{route('filter_app')}}");


                    let timerInterval
                    Swal.fire({
                        title: 'Generating Excel!'
                        , html: 'Please Wait'
                        , timer: 15000
                        , timerProgressBar: true
                        , didOpen: () => {
                            Swal.showLoading()
                        }
                        , willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {

                    });
                }
            }
            , {
                text: 'Print'
                , extend: 'print'
                , className: 'btn btn-sm shadow-0 border-0 bg-info text-white'
            }
        ]
        , data: data
        , columns: [{
                "data": "advertisement"
                , render: function(data, type, row) {
                    let additional = "";
                    if (row.parent_id != "" && row.parent_id != null) {
                        additional = "  <a data-toggle='tooltip' data-placement='top' title='Parent Project'  class='btn btn-sm shadow-0 border-0 bg-purple text-white' target='_blank'  href='/view_project/" + row.parent_id + "'><i class='ni ni-bold-up'></i></a></div>";
                    }

                    if (row.child_id != "" && row.child_id != null) {
                        additional = "  <a data-toggle='tooltip' data-placement='top' title='Child Project'  class='btn btn-sm shadow-0 border-0 bg-yellow text-white' target='_blank'  href='/view_project/" + row.child_id + "'><i class='ni ni-bold-down'></i></a></div>";
                    }

                    if (data == "pending") {
                        console.log(row.is_old);
                        if (row.is_old == 1) {

                            return "<div style='white-space: nowrap'>@if(in_array('update',$user_privilege)) <a  data-toggle='tooltip' data-placement='top' title='Edit' href='/edit_app/" + row.plan_id + "' target='_blank' class='btn btn-sm shadow-0 border-0 btn-success'><i class='ni ni-ruler-pencil'></i></a> @endif @if(in_array('delete',$user_privilege)) <button data-toggle='tooltip' data-placement='top' title='Delete' value='" + row.plan_id + "' class='btn btn-sm shadow-0 border-0 btn-warning pb-1 text-white delete_btn'><i class='ni ni-basket text-white'></i></button> @endif <a data-toggle='tooltip' data-placement='top' title='View'  class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank'  href='/view_project/" + row.plan_id + "'><i class='ni ni-tv-2'></i></a>" + additional + "</div>";
                        } else {

                            return "<div style='white-space: nowrap'>@if(in_array('create sapp',$user_privilege))<a data-toggle='tooltip' data-placement='top' title='Create SAPP' class='btn btn-sm shadow-0 border-0 btn-danger text-white'  href='/add_sapp/" + row.plan_id + "'><i class='ni ni-fat-add'></i></a> @endif @if(in_array('update',$user_privilege)) <a  data-toggle='tooltip' data-placement='top' title='Edit' href='/edit_app/" + row.plan_id + "' target='_blank' class='btn btn-sm shadow-0 border-0 btn-success'><i class='ni ni-ruler-pencil'></i></a> @endif @if(in_array('delete',$user_privilege)) <button data-toggle='tooltip' data-placement='top' title='Delete' value='" + row.plan_id + "' class='btn btn-sm shadow-0 border-0 btn-warning pb-1 text-white delete_btn'><i class='ni ni-basket text-white'></i></button> @endif <a data-toggle='tooltip' data-placement='top' title='View'  class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank'  href='/view_project/" + row.plan_id + "'><i class='ni ni-tv-2'></i></a>" + additional + "</div>";
                        }
                    } else {
                        let project_type = @json($project_type);
                        if (project_type === "supplemental") {
                            return "<div style='white-space: nowrap'>@if(in_array('update',$user_privilege))<a data-toggle='tooltip' data-placement='top' title='Edit SAPP' class='btn btn-sm shadow-0 border-0 btn-success text-white'  href='/edit_app/" + row.plan_id + "'><i class='ni ni-ruler-pencil'></i></a> @endif @if(in_array('create sapp',$user_privilege)) <a data-toggle='tooltip' data-placement='top' title='Create SAPP' class='btn btn-sm shadow-0 border-0 btn-danger text-white'  href='/add_sapp/" + row.plan_id + "'><i class='ni ni-fat-add'></i></a> @endif  <a data-toggle='tooltip' data-placement='top' title='View' class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank'  href='/view_project/" + row.plan_id + "'><i class='ni ni-tv-2'></i></a>" + additional + "</div>";
                        } else {
                            return "<div style='white-space: nowrap'>@if(in_array('create sapp',$user_privilege))<a data-toggle='tooltip' data-placement='top' title='Create SAPP' class='btn btn-sm shadow-0 border-0 btn-danger text-white'  href='/add_sapp/" + row.plan_id + "'><i class='ni ni-fat-add'></i></a> @endif  <a data-toggle='tooltip' data-placement='top' title='View' class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank'  href='/view_project/" + row.plan_id + "'><i class='ni ni-tv-2'></i></a>" + additional + "</div>";
                        }
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
                "data": "project_no"
            }
            , {
                "data": "project_title"
            }
            , {
                "data": "municipality_display"
            }
            , {
                "data": "mode"
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
            , {
                "data": "parent_id"
            }
            , {
                "data": "child_id"
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
        , columnDefs: [{
                targets: 0
                , width: "100px"
            }
            , {
                width: 200
                , visible: false
                , targets: [1, 21, 22]
            , }
        ],

    });

    // inputs
    var oldInputs = '{{ count(session()->getOldInput()) }}';
    if (oldInputs == 0) {
        $("#project_year").datepicker("update", "{{$year}}");
    }

    $("#project_year").change(function() {
        $("#app_filter").submit();
    });

    $("#mode_of_procurement").change(function() {
        $("#app_filter").submit();
    });

    $("#fund_category").change(function() {
        $("#app_filter").submit();
    });

    $("#account_classification").change(function() {
        $("#app_filter").submit();
    });

    $("#sapp_number").change(function() {
        $("#app_filter").submit();
    });


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

    // show delete Success
    $('#app_table tbody').on('click', '.delete_btn', function(e) {
        Swal.fire({
            text: 'Are you sure to delete this Project Plan?'
            , showCancelButton: true
            , confirmButtonText: 'Delete'
            , cancelButtonText: "Don't Delete"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-danger'
            , cancelButtonClass: 'btn btn-sm btn-default'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                window.location.href = "/delete_app/" + $(this).val();
            }
        });

    });


    // $("#project_year").change(function functionName() {
    //   $("#date_added").val("");
    //   $("#month_added").val("");
    // });

    // $("#date_added").change(function functionName() {
    //   $("#project_year").val("");
    //   $("#month_added").val("");
    // });
    //
    // $("#month_added").change(function functionName() {
    //   $("#project_year").val("");
    //   $("#date_added").val("");
    // });


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
