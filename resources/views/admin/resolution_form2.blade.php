@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
    <div class="card shadow mt-4 mb-5">
        <div class="card shadow border-0" style="background:#F7F5F5">
            <div class="card-header" style="background:#F7F5F5">
                <h2 id="title">{{$title}}</h2>
            </div>
            <div class="card-body ">
                <form class="col-sm-12" method="POST" id="resolution_form" action="{{route('submit_resolution')}}">
                    @csrf
                    <div class="row">
                        <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                            <label for="procact_ids">Procacts</label>
                            <input type="text" id="procact_ids" name="procact_ids" class="form-control form-control-sm" readonly value="{{old('procact_ids')}}">
                            <label class="error-msg text-red"></label>
                        </div>

                        <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                            <label for="project_bids">Project_bids</label>
                            <input type="text" id="project_bids" name="project_bids" class="form-control form-control-sm" readonly value="{{old('project_bids')}}">
                            <label class="error-msg text-red"></label>
                        </div>


                        <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                            <label for="resolution_id">ID</label>
                            <input type="text" id="resolution_id" name="resolution_id" class="form-control form-control-sm" readonly value="{{old('resolution_id')}}">
                            <label class="error-msg text-red">@error('resolution_id'){{$message}}@enderror</label>
                        </div>

                        <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                            <label for="resolution_number">Resolution Number</label>
                            <input type="text" id="resolution_number" name="resolution_number" class="form-control form-control-sm" value="{{old('resolution_number')}}">
                            <label class="error-msg text-red">@error('resolution_number'){{$message}}@enderror</label>
                        </div>


                        <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                            <label for="resolution_date">Resolution Date</label>
                            <input type="text" id="resolution_date" name="resolution_date" class="form-control form-control-sm datepicker" value="{{old('resolution_date')}}">
                            <label class="error-msg text-red">@error('resolution_date'){{$message}}@enderror</label>
                        </div>

                        <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0 d-none" id="reason_container">
                            <label for="reason">Reason</label>
                            <input type="text" id="reason" name="reason" class="form-control form-control-sm " value="{{old('reason')}}">
                            <label class="error-msg text-red">@error('reason'){{$message}}@enderror</label>
                        </div>

                        <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0 bg-white">
                            <label for="resolution_date">Select Projects:</label>
                            <label class="error-msg text-red mx-auto"> @error('procact_ids') Please select Projects @enderror </label>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="project_table">
                                    <thead class="">
                                        <tr class="bg-primary text-white">
                                            <th class="text-center"></th>
                                            <th class="text-center">Plan ID</th>
                                            <th class="text-center">Date Opened</th>
                                            <th class="text-center">Project Number</th>
                                            <th class="text-center">Bidder</th>
                                            <th class="text-center">Project Title</th>
                                            <th class="text-center">ITB Arrangement</th>
                                            <th class="text-center">Selected</th>
                                            <th class="text-center">Mode</th>
                                            <th class="text-center">Cluster</th>
                                            <th class="text-center">Project Bids</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project_plans as $project_plan)
                                        <tr>
                                            <td></td>
                                            <td>{{$project_plan->procact_id}}</td>
                                            <td>{{$project_plan->open_bid}}</td>
                                            <td>{{$project_plan->project_no}}</td>
                                            <td>{{$project_plan->responsive_bidder}}</td>
                                            <td>{{$project_plan->project_title}}</td>
                                            <td>{{$project_plan->itb_arrangement}}</td>
                                            <td></td>
                                            <td>{{$project_plan->mode}}</td>
                                            <td>{{$project_plan->plan_cluster_id}}</td>
                                            <td>{{$project_plan->responsive_bid}}</td>


                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>


                        <!-- <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
            <label for="date_opened">Date Opened ("Projects") </label>
            <input type="text" id="date_opened" name="date_opened" class="form-control form-control-sm datepicker" value="{{old('date_opened')}}" >
            <label class="error-msg text-red" >@error('date_opened'){{$message}}@enderror</label>
          </div> -->

                        <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                            <label for="resolution_type">Resolution Type</label>
                            <select type="text" id="resolution_type" name="resolution_type" class="form-control form-control-sm">
                                <option value="RRRC" @if($resolution_type==="RRRC" ) selected @endif>Resolution Declaring Recall/ Cancellation</option>
                            </select>
                            <label class="error-msg text-red">@error('resolution_type'){{$message}}@enderror</label>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center col-sm-12 mt-3">
                        <button class="btn btn-primary text-center">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection

@push('custom-scripts')
<script>

      $(".datepicker").datepicker({
      format: 'mm/dd/yyyy'
      });

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
        } else if ("{{session('message')}}" == "multiple_modes") {
            swal.fire({
                title: `Mode of Procurement Error`
                , text: 'You cannot mix SVP,Bidding,Negotiated in 1 Resolution Recommending Award.'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });
        } else if ("{{session('message')}}" == "multiple_opening") {
            swal.fire({
                title: `Opening Error`
                , text: 'You cannot mix projects with various opening in 1 Resolution '
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });
        } else if ("{{session('message')}}" == "multiple_failure_status") {
            swal.fire({
                title: `Failure Status`
                , text: 'You cannot mix no bidders,failure upon opening and failure after post qual in 1 Resolution Declaring Failure.'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });
        } else if ("{{session('message')}}" == "duplicate") {
            swal.fire({
                title: `Duplicate`
                , text: 'Data already exist in the database'
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



    var table = $('#project_table').DataTable({
        language: {
            paginate: {
                next: '<i class="fas fa-angle-right">'
                , previous: '<i class="fas fa-angle-left">'
            }
        }
        , orderCellsTop: true
        , select: {
            style: 'multi'
            , selector: 'td'
        }
        , responsive: true
        , columnDefs: [{
                targets: [1, 6, 7, 10]
                , visible: false
            }
            , {
                className: 'select-checkbox'
                , targets: 0
            }
        ]
    });


    table.on('select', function(e, dt, type, indexes) {

        cluster_id = dt.data()[9];
        let rows = table.rows({
            selected: true
        });
        var procact_ids = [];
        var project_bids = [];


        table.rows({
            selected: false
        }).every(function(rowIdx, tableLoop, rowLoop) {
            table.cell(rowIdx, 7).data("");
            if (cluster_id != null && cluster_id != "" && table.cell(rowIdx, 9).data() == cluster_id) {
                this.select();
            }
        }).order([7, 'desc'], [2, 'asc'], [5, 'asc']).draw();

        table.rows({
            selected: true
        }).every(function(rowIdx, tableLoop, rowLoop) {
            table.cell(rowIdx, 7).data("1");
            if (cluster_id != null && cluster_id != "" && table.cell(rowIdx, 9).data() == cluster_id) {
                this.deselect();
            }
        }).order([7, 'desc'], [2, 'asc'], [5, 'asc']).draw();

        table.rows({
            selected: true
        }).every(function(rowIdx, tableLoop, rowLoop) {
            procact_ids.push(table.row(rowIdx).data()[1]);
            project_bids.push(table.row(rowIdx).data()[10]);
        }).order([7, 'desc'], [2, 'asc'], [5, 'asc']).draw();


        table.rows({
            selected: true
        }).every(function(rowIdx, tableLoop, rowLoop) {
            procact_ids.push(table.row(rowIdx).data()[1]);
            project_bids.push(table.row(rowIdx).data()[10]);
        }).order([7, 'desc'], [2, 'asc'], [5, 'asc']).draw();




        $("#project_bids").val(project_bids.toString());
        $("#procact_ids").val(procact_ids.toString());
    });

    table.on('deselect', function(e, dt, type, indexes) {
        if ("{{$resolution_type}}" == "RDF") {
            showOrHideReason();
        }
        cluster_id = dt.data()[9];
        let rows = table.rows({
            selected: true
        });
        var procact_ids = [];
        var project_bids = [];


        table.rows({
            selected: false
        }).every(function(rowIdx, tableLoop, rowLoop) {
            table.cell(rowIdx, 7).data("");
        }).order([7, 'desc'], [2, 'asc'], [5, 'asc']).draw();

        table.rows({
            selected: true
        }).every(function(rowIdx, tableLoop, rowLoop) {
            table.cell(rowIdx, 7).data("1");
            if (cluster_id != null && cluster_id != "" && table.cell(rowIdx, 9).data() == cluster_id) {
                this.deselect();
            }
        }).order([7, 'desc'], [2, 'asc'], [5, 'asc']).draw();

        table.rows({
            selected: true
        }).every(function(rowIdx, tableLoop, rowLoop) {
            procact_ids.push(table.row(rowIdx).data()[1]);
            project_bids.push(table.row(rowIdx).data()[10]);
        }).order([7, 'desc'], [2, 'asc'], [5, 'asc']).draw();


        $("#project_bids").val(project_bids.toString());
        $("#procact_ids").val(procact_ids.toString());


    });

    var procact_ids = "{{old('procact_ids')}}";

    if (procact_ids != "") {
        var procact_ids_array = procact_ids.split(",");
        procact_ids_array.forEach(function(item, index) {
            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                if (table.cells(this.nodes(), 1).data().to$()[0] == item) {
                    this.select();
                }
            });
        });
    } else {
        $("#resolution_id").val('{{$resolution->resolution_id ?? ""}}');
        $("#resolution_number").val('{{$resolution->resolution_number ?? ""}}');
        $("#resolution_date").val('@if($resolution) {{date("m/d/Y", strtotime($resolution->resolution_date )) }}@endif');
        var procact_ids = "{{$resolution_projects ??''}}";
        var procact_ids_array = procact_ids.split(",");
        $("#procact_ids").val(procact_ids);
        procact_ids_array.forEach(function(item, index) {
            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                if (table.cell(rowIdx, 1).data() == item) {
                    this.select();
                }
            });
        });

    }

</script>
@endpush
