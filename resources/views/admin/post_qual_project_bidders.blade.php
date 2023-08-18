@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">

    <div id="app">
        <div class="modal" tabindex="-1" role="dialog" id="bidder_modal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="bidder_modal_title">Set Bidder as Non Responsive</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="col-sm-12" method="POST" id="bidders_form" action="/non_responsive_bidder">
                            @csrf
                            <div class="row">

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                                    <label for="action_type">Action Type</label>
                                    <input type="text" id="action_type" name="action_type" class="form-control form-control-sm" readonly value="{{old('action_type')}}">
                                    <label class="error-msg text-red">@error('action_type'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                                    <label for="bidder_id">Bidder ID</label>
                                    <input type="text" id="bidder_id" name="bidder_id" class="form-control form-control-sm" readonly value="{{old('bidder_id')}}">
                                    <label class="error-msg text-red">@error('bidder_id'){{$message}}@enderror</label>
                                </div>

                                <!-- Business Name -->
                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="business_name">Business Name</label>
                                    <input type="text" id="business_name" name="business_name" class="form-control form-control-sm bg-white" readonly value="{{old('business_name')}}">
                                    <label class="error-msg text-red">@error('business_name'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="owner">Owner</label>
                                    <input type="text" id="owner" name="owner" class="form-control form-control-sm bg-white" readonly value="{{old('owner')}}">
                                    <label class="error-msg text-red">@error('owner'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                                    <label for="remarks">Remarks<span class="text-red">*</span></label>
                                    <textarea type="text" id="remarks" name="remarks" class="form-control form-control-sm" value="{{old('remarks')}}"></textarea>
                                    <label class="error-msg text-red">@error('remarks'){{$message}}@enderror</label>
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

        <div class="card shadow">
            <div class="card shadow border-0">
                <div class="card-header">
                    <h2 id="title">Project Bidders</h2>
                    <label class="text-sm">Project Number: <span class="">{{$data->project_number}}</span></label>
                    <br />
                    <label class="text-sm">Project Title: <span class="">{{$data->title2}}</span></label>
                    <br />
                    <label class="text-sm">Date Bid Opened: <span class="">{{$data->open_bid}}</span></label>
                    <br />
                    <label class="text-sm">Procurement Mode: <span class="">{{$data->mode}}</span></label>
                    <br />
                    <label class="text-sm">Project Cost: <span class="text-red">Php {{number_format($data->project_cost,2,'.',',')}}</span></label>
                </div>
                <div class="card-body">

                    <div class="col-sm-12">
                        <!-- <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button> -->
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered wrap" id="bidders_table">
                            <thead class="">
                                <tr class="bg-primary text-white">
                                    <th class="text-center"></th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Rank</th>
                                    <th class="text-center">Business Name</th>
                                    <th class="text-center">Owner</th>
                                    <th class="text-center">Proposed Bid/ Bid as Read</th>
                                    <th class="text-center">Bid as Evaluated</th>
                                    <th class="text-center">Bid as Calculated</th>
                                    <th class="text-center">Date Received</th>
                                    <th class="text-center">Time Received</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">TWG Evaluation</th>
                                    <th class="text-center">TWG Remarks</th>
                                    <th class="text-center">Post Qual Start</th>
                                    <th class="text-center">Post Qual End</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $row_number=1;
                                @endphp
                                @foreach($data->project_bidders as $bidder)
                                <tr>
                                    <td style="white-space: nowrap">
                                        @if($bidder->bid_status==='non-responsive')
                                        <button class="btn btn-sm btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        @elseif($bidder->bid_status==='responsive')
                                        <button class="btn btn-sm btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        <span class="badge bg-yellow">Responsive</span>
                                        @else
                                        <button class="btn btn-sm btn btn-sm btn-danger non-responsive-btn">Non-responsive</button>
                                        <button class="btn btn-sm btn btn-sm btn-success responsive-btn">Responsive</button>
                                        @endif
                                    </td>
                                    <td>{{$bidder->project_bid}} </td>
                                    @php
                                    if($bidder->proposed_bid!=null && $bidder->proposed_bid>0){
                                    echo "<td>".$row_number."</td>";
                                    $row_number=$row_number+1;
                                    }
                                    else{
                                    echo "<td></td>";
                                    }
                                    @endphp
                                    <td>{{$bidder->business_name}}
                                        @if($bidder->date_received>$data->open_bid)
                                        <span class="badge badge-danger">Late</span>
                                        @endif
                                    </td>
                                    <td>{{$bidder->owner}}</td>
                                    <td>{{number_format($bidder->proposed_bid,2,'.',',')}}</td>
                                    <td>{{number_format($bidder->bid_as_evaluated,2,'.',',')}}</td>
                                    <td>{{number_format($bidder->twg_final_bid_evaluation,2,'.',',')}}</td>
                                    <td>{{$bidder->date_received}}</td>
                                    <td>{{$bidder->time_received}}</td>
                                    <td>{{$bidder->bid_status}}</td>
                                    <td>{{$bidder->twg_evaluation_status}}</td>
                                    <td>{{$bidder->twg_evaluation_remarks}}</td>
                                    <td>{{$bidder->post_qual_start}}</td>
                                    <td>{{$bidder->post_qual_end}}</td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mt-2">
            <div class="card shadow border-0">
                <div class="card-header">
                    <h2 id="title">Detailed Project Bidders</h2>
                </div>
                <div class="card-body">

                    <div class="col-sm-12">
                        <!-- <button class="btn btn-sm btn-success text-white float-right mb-2 btn btn-sm ml-2">Print PDF</button>
            <button class="btn btn-sm btn-info text-white float-right mb-2 btn btn-sm ml-2">Print Word</button> -->
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered wrap" id="detailed_bidders_table">
                            <thead class="">
                                <tr class="bg-primary text-white">
                                    <th class="text-center"></th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Project Title</th>
                                    <th class="text-center">Project Cost</th>
                                    <th class="text-center">Business Name</th>
                                    <th class="text-center">Owner</th>
                                    <th class="text-center">Bid in Words</th>
                                    <th class="text-center">Bid as Read</th>
                                    <th class="text-center">Bid as Evaluated</th>
                                    <th class="text-center">Bid as Calculated</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Evaluation Status</th>
                                    <th class="text-center">Post Qual Start Date</th>
                                    <th class="text-center">Post Qual End Date</th>
                                    <th class="text-center">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $row_number=1;
                                @endphp
                                @foreach($data->detailed_bids as $detailed_bid)
                                <tr>
                                    <td style="white-space: nowrap">
                                        @if($detailed_bid->bid_status==='non-responsive')
                                        <button class="btn btn-sm btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        @elseif($detailed_bid->bid_status==='responsive')
                                        <button class="btn btn-sm btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        <span class="badge bg-yellow">Responsive</span>
                                        @else
                                        <button class="btn btn-sm btn btn-sm btn-danger non-responsive-btn">Non-responsive</button>
                                        <button class="btn btn-sm btn btn-sm btn-success responsive-btn">Responsive</button>
                                        @endif
                                    </td>
                                    <td>{{$detailed_bid->bid_id}} </td>
                                    <td>{{$detailed_bid->project_title}}</td>
                                    <td>{{$detailed_bid->project_cost}}</td>
                                    <td>{{$detailed_bid->business_name}}</td>
                                    <td>{{$detailed_bid->owner}}</td>
                                    <td>{{number_format($detailed_bid->detailed_bid_in_words,2,'.',',')}}</td>
                                    <td>{{number_format($detailed_bid->detailed_bid_as_read,2,'.',',')}}</td>
                                    <td>{{number_format($detailed_bid->detailed_bid_as_evaluated,2,'.',',')}}</td>
                                    <td>@if($detailed_bid->detailed_bid_as_calculated!=null){{number_format($detailed_bid->detailed_bid_as_calculated,2,'.',',')}}@endif</td>
                                    <td>{{$detailed_bid->bid_status}}</td>
                                    <td>{{$detailed_bid->twg_evaluation_status}}</td>
                                    <td>{{$detailed_bid->post_qual_start}}</td>
                                    <td>{{$detailed_bid->post_qual_end}}</td>
                                    <td style="white-space: nowrap">{{$detailed_bid->twg_evaluation_remarks}}</td>

                                </tr>
                                @endforeach
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


    var table = $('#bidders_table').DataTable({
        language: {
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
            [2, "asc"]
        ]
        , columnDefs: [{
            targets: 0
            , orderable: false
        }],

    });

    var detailed_table = $('#detailed_bidders_table').DataTable({
        language: {
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
            [2, "asc"]
        ]
        , columnDefs: [{
            targets: 0
            , orderable: false
        }]
    , });

    var oldInputs = '{{ count(session()->getOldInput()) }}';
    if (oldInputs > 2) {
        var action_type = "{{old('action_type')}}";
        if (action_type == "responsive") {

        } else if (action_type == "clear-post-qual") {
            $("#bidders_form").prop('action', '/clear_post_qualification_evaluation');
            $("#post_qual_start_date").parent().removeClass('d-none');
            $("#post_qual_start_date").parent().addClass('d-none');
            $("#post_qual_end_date").parent().removeClass('d-none');
            $("#post_qual_end_date").parent().addClass('d-none');
            $("#bid_as_evaluated").parent().removeClass('d-none');
            $("#bid_as_evaluated").parent().addClass('d-none');
        } else {
            $("#bidders_form").prop('action', '/non_responsive_bidder');
        }
        $("#bidder_modal").modal('show');
    }



    if ("{{session('message')}}") {
        if ("{{session('message')}}" == "success") {
            swal.fire({
                title: `Success`
                , text: 'Successfully Saved Project Bidder'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-success'
                , icon: 'success'
            });
            $("#bidder_modal").modal('hide');
        } else if ("{{session('message')}}" == "bidder_chosen") {
            swal.fire({
                title: `Error`
                , text: 'This project has already one responsive bidder!'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });
            $("#proposed_bid").modal('hide');
        } else if ("{{session('message')}}" == "range_error") {
            swal.fire({
                title: `Range Error`
                , text: 'The  TWG Evaluation Date is  not in the range of Post Qualification Period. Please Extend'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });
            $("#proposed_bid").modal('hide');
        } else if ("{{session('message')}}" == "twg_evaluation_error") {
            swal.fire({
                title: `TWG Evaluation Error`
                , text: 'Please Request TWG to update Bid Evaluation for this bidder'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });
            $("#proposed_bid").modal('hide');
        } else if ("{{session('message')}}" == "zero_bid") {
            swal.fire({
                title: `Error`
                , text: 'Sorry! Project Bidder has zero proposed bid'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });
            $("#proposed_bid").modal('hide');
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
            $(this).html('<input class="px-0 mx-0" type="text" placeholder="Search ' + title + '" />');
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

    @if(in_array("update", $user_privilege))
    // Disqualify
    $('#bidders_table tbody').on('click', '.non-responsive-btn', function(e) {
        $("#action_type").val("non-responsive");
        Swal.fire({
            title: 'Set Project Bidder as Non-responsive'
            , text: 'Are you sure to Set Bidder Status as Non-responsive?'
            , showCancelButton: true
            , confirmButtonText: 'Yes'
            , cancelButtonText: "No"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm'
            , cancelButtonClass: 'btn btn-sm btn-default btn btn-sm'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                var data = table.row($(this).parents('tr')).data();
                $("#bidder_id").val(data[1]);
                var owner = data[3];
                owner = owner.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[4]);
                $("#remarks").val(data[12]);
                $("#bidder_modal").modal('show');
            }
        });

    });

    $('#detailed_bidders_table tbody').on('click', '.non-responsive-btn', function(e) {
        $("#action_type").val("non-responsive");
        Swal.fire({
            title: 'Set Project Bidder as Non-responsive'
            , text: 'Are you sure to Set Bidder Status as Non-responsive?'
            , showCancelButton: true
            , confirmButtonText: 'Yes'
            , cancelButtonText: "No"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm'
            , cancelButtonClass: 'btn btn-sm btn-default btn btn-sm'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                var data = detailed_table.row($(this).parents('tr')).data();
                $("#bidder_id").val(data[1]);
                var business_name = data[4];
                business_name = business_name.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(business_name.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(business_name.replace('                                       <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[5]);
                $("#remarks").val(data[14]);
                $("#bidder_modal").modal('show');
            }
        });

    });

    $('#bidders_table tbody').on('click', '.responsive-btn', function(e) {
        $("#action_type").val("responsive");
        Swal.fire({
            title: 'Set Project Bidder as Responsive'
            , text: 'Are you sure to Set Bidder Status as Responsive?'
            , showCancelButton: true
            , confirmButtonText: 'Yes'
            , cancelButtonText: "No"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-success btn btn-sm'
            , cancelButtonClass: 'btn btn-sm btn-default btn btn-sm'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "/responsive_bidder/" + data[1];
            }
        });

    });

    $('#detailed_bidders_table tbody').on('click', '.responsive-btn', function(e) {
        $("#action_type").val("detailed_responsive");
        Swal.fire({
            title: 'Set Project Bidder as Responsive'
            , text: 'Are you sure to Set Bidder Status as Responsive?'
            , showCancelButton: true
            , confirmButtonText: 'Yes'
            , cancelButtonText: "No"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-success btn btn-sm'
            , cancelButtonClass: 'btn btn-sm btn-default btn btn-sm'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                var data = detailed_table.row($(this).parents('tr')).data();
                window.location.href = "/responsive_bidder/" + data[1];
            }
        });
    });
    @endif


    @if(in_array("delete", $user_privilege))

    $('#bidders_table tbody').on('click', '.clear-post-qual-btn', function(e) {
        Swal.fire({
            title: 'Clear Post Qualification Evaluation'
            , text: 'Are you sure to Clear Post Qualification Evaluation this Project Bidder?'
            , showCancelButton: true
            , confirmButtonText: 'Yes'
            , cancelButtonText: "No"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm'
            , cancelButtonClass: 'btn btn-sm btn-default btn btn-sm'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                $("#bidders_form").prop("action", "{{route('clear_post_qualification_evaluation')}}");
                var data = table.row($(this).parents('tr')).data();
                $("#bidder_id").val(data[1]);
                $("#action_type").val("clear-post-qual");
                var owner = data[3];
                $("#post_qual_start_date").parent().removeClass('d-none');
                $("#post_qual_start_date").parent().addClass('d-none');
                $("#post_qual_end_date").parent().removeClass('d-none');
                $("#post_qual_end_date").parent().addClass('d-none');
                $("#bid_as_evaluated").parent().removeClass('d-none');
                $("#bid_as_evaluated").parent().addClass('d-none');
                $("#bidder_modal_title").html("Clear Post Qualification Evaluation");
                owner = owner.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(owner.replace('                                        <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[4]);
                $("#bidder_modal").modal('show');
            }
        });
    });

    $('#detailed_bidders_table tbody').on('click', '.clear-post-qual-btn', function(e) {
        Swal.fire({
            title: 'Clear Post Qualification Evaluation'
            , text: 'Are you sure to Clear Post Qualification Evaluation this Project Bidder?'
            , showCancelButton: true
            , confirmButtonText: 'Yes'
            , cancelButtonText: "No"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-danger btn btn-sm'
            , cancelButtonClass: 'btn btn-sm btn-default btn btn-sm'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                $("#bidders_form").prop("action", "{{route('clear_post_qualification_evaluation')}}");
                var data = detailed_table.row($(this).parents('tr')).data();
                $("#bidder_id").val(data[1]);
                $("#action_type").val("clear-post-qual");
                var business_name = data[4];
                $("#post_qual_start_date").parent().removeClass('d-none');
                $("#post_qual_start_date").parent().addClass('d-none');
                $("#post_qual_end_date").parent().removeClass('d-none');
                $("#post_qual_end_date").parent().addClass('d-none');
                $("#bid_as_evaluated").parent().removeClass('d-none');
                $("#bid_as_evaluated").parent().addClass('d-none');
                $("#bidder_modal_title").html("Clear Post Qualification Evaluation");
                business_name = business_name.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(business_name.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[5]);
                $("#bidder_modal").modal('show');
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

</script>
@endpush
