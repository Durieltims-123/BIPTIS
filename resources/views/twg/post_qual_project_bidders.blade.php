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
                        <form class="col-sm-12" method="POST" id="bidders_form" action="/twg_non_responsive_bidder">
                            @csrf
                            <div class="row">

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 mx-auto d-none">
                                    <label for="process">Action Type</label>
                                    <input type="text" id="process" name="process" class="form-control form-control-sm" readonly value="{{old('process')}}">
                                    <label class="error-msg text-red">@error('process'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto d-none">
                                    <label for="bidder_id">Bidder ID</label>
                                    <input type="text" id="bidder_id" name="bidder_id" class="form-control form-control-sm" readonly value="{{old('bidder_id')}}">
                                    <label class="error-msg text-red">@error('bidder_id'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto">
                                    <label for="contractor_id">Contractor ID</label>
                                    <input type="text" id="contractor_id" name="contractor_id" class="form-control form-control-sm" readonly value="{{old('contractor_id')}}">
                                    <label class="error-msg text-red">@error('contractor_id'){{$message}}@enderror</label>
                                </div>


                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mx-auto mb-0">
                                    <label for="procurement_mode">Procurement Mode</label>
                                    <input type="text" id="procurement_mode" name="procurement_mode" class="form-control form-control-sm" readonly value={{$data->mode}}>
                                    <label class="error-msg text-red">@error('procurement_mode'){{$message}}@enderror</label>
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

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="post_qual_start_date">Post Qual Start Date <span class="text-red">*</span></label>
                                    <input type="text" id="post_qual_start_date" name="post_qual_start_date" class="form-control form-control-sm bg-white datepicker" value="{{old('post_qual_start_date')}}">
                                    <label class="error-msg text-red">@error('post_qual_start_date'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="post_qual_end_date">Post Qual End Date <span class="text-red">*</span></label>
                                    <input type="text" id="post_qual_end_date" name="post_qual_end_date" class="form-control form-control-sm bg-white datepicker" value="{{old('post_qual_end_date')}}">
                                    <label class="error-msg text-red">@error('post_qual_end_date'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0 d-none">
                                    <label for="detailed_bid_as_calculated">Detailed Bid as Calculated <span class="text-red">*</span></label>
                                    <input type="text" id="detailed_bid_as_calculated" name="detailed_bid_as_calculated" class="form-control form-control-sm bg-white money2" value="{{old('detailed_bid_as_calculated')}}">
                                    <label class="error-msg text-red">@error('detailed_bid_as_calculated'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="bid_as_calculated">Bid as Calculated <span class="text-red">*</span></label>
                                    <input type="text" id="bid_as_calculated" name="bid_as_calculated" class="form-control form-control-sm bg-white money2" value="{{old('bid_as_calculated')}}">
                                    <label class="error-msg text-red">@error('bid_as_calculated'){{$message}}@enderror</label>
                                </div>

                                @if($data->mode==="Bidding")

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="philgeps">Philgeps Certificate<span class="text-red">*</span></label>
                                    <input type="text" id="philgeps" name="philgeps" class="form-control form-control-sm bg-white" value="{{old('philgeps')}}">
                                    <label class="error-msg text-red">@error('philgeps'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="ongoing_projects">Ongoing Projects<span class="text-red">*</span></label>
                                    <input type="text" id="ongoing_projects" name="ongoing_projects" class="form-control form-control-sm bg-white" value="{{old('ongoing_projects')}}">
                                    <label class="error-msg text-red">@error('ongoing_projects'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="slcc">SLCC<span class="text-red">*</span></label>
                                    <input type="text" id="slcc" name="slcc" class="form-control form-control-sm bg-white " value="{{old('slcc')}}">
                                    <label class="error-msg text-red">@error('slcc'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="bsd">Bid Securing Declaration<span class="text-red">*</span></label>
                                    <input type="text" id="bsd" name="bsd" class="form-control form-control-sm bg-white " value="{{old('bsd')}}">
                                    <label class="error-msg text-red">@error('bsd'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="nfcc">Net Financial Contracting Capacity<span class="text-red">*</span></label>
                                    <input type="text" id="nfcc" name="nfcc" class="form-control form-control-sm bg-white money2" value="{{old('nfcc')}}">

                                    <label class="error-msg text-red">@error('nfcc'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="spcab">SPCAB<span class="text-red">*</span></label>
                                    <input type="text" id="spcab" name="spcab" class="form-control form-control-sm bg-white " value="{{old('spcab')}}">

                                    <label class="error-msg text-red">@error('spcab'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="orgchart">Organizational Chart<span class="text-red">*</span></label>
                                    <input type="text" id="orgchart" name="orgchart" class="form-control form-control-sm bg-white " value="{{old('orgchart')}}">

                                    <label class="error-msg text-red">@error('orgchart'){{$message}}@enderror</label>
                                </div>


                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="key_personnel">Key Personnel<span class="text-red">*</span></label>
                                    <input type="text" id="key_personnel" name="key_personnel" class="form-control form-control-sm bg-white " value="{{old('key_personnel')}}">

                                    <label class="error-msg text-red">@error('key_personnel'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="major_equipment">Major Equipment<span class="text-red">*</span></label>
                                    <input type="text" id="major_equipment" name="major_equipment" class="form-control form-control-sm bg-white " value="{{old('major_equipment')}}">

                                    <label class="error-msg text-red">@error('major_equipment'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="oss">Omnibus Sworn Statement<span class="text-red">*</span></label>
                                    <input type="text" id="oss" name="oss" class="form-control form-control-sm bg-white " value="{{old('oss')}}">
                                    <label class="error-msg text-red">@error('oss'){{$message}}@enderror</label>
                                </div>


                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="jva">Joint Venture Agreement<span class="text-red">*</span></label>
                                    <input type="text" id="jva" name="jva" class="form-control form-control-sm bg-white " value="{{old('jva')}}">
                                    <label class="error-msg text-red">@error('jva'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="boq">Bill of Quantities<span class="text-red">*</span></label>
                                    <input type="text" id="boq" name="boq" class="form-control form-control-sm bg-white " value="{{old('boq')}}">
                                    <label class="error-msg text-red">@error('boq'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="detailed_estimates">Detailed Estimates<span class="text-red">*</span></label>
                                    <input type="text" id="detailed_estimates" name="detailed_estimates" class="form-control form-control-sm bg-white " value="{{old('detailed_estimates')}}">
                                    <label class="error-msg text-red">@error('detailed_estimates'){{$message}}@enderror</label>

                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="cash_flow">Cash Flow by Quarter<span class="text-red">*</span></label>
                                    <input type="text" id="cash_flow" name="cash_flow" class="form-control form-control-sm bg-white " value="{{old('cash_flow')}}">
                                    <label class="error-msg text-red">@error('cash_flow'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="provincial_permit">Provincial Permit<span class="text-red">*</span></label>
                                    <input type="text" id="provincial_permit" name="provincial_permit" class="form-control form-control-sm bg-white " value="{{old('provincial_permit')}}">
                                    <label class="error-msg text-red">@error('provincial_permit'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="construction_shedule">Construction Schedule and S-Curve<span class="text-red">*</span></label>
                                    <input type="text" id="construction_shedule" name="construction_shedule" class="form-control form-control-sm bg-white " value="{{old('construction_shedule')}}">
                                    <label class="error-msg text-red">@error('construction_shedule'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="man_power">Man Power<span class="text-red">*</span></label>
                                    <input type="text" id="man_power" name="man_power" class="form-control form-control-sm bg-white " value="{{old('man_power')}}">
                                    <label class="error-msg text-red">@error('man_power'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="construction_methods">Construction Methods<span class="text-red">*</span></label>
                                    <input type="text" id="construction_methods" name="construction_methods" class="form-control form-control-sm bg-white " value="{{old('construction_methods')}}">
                                    <label class="error-msg text-red">@error('construction_methods'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="eus">Equipment Utilization Schedule<span class="text-red">*</span></label>
                                    <input type="text" id="eus" name="eus" class="form-control form-control-sm bg-white " value="{{old('eus')}}">
                                    <label class="error-msg text-red">@error('eus'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="chsp">CHSP<span class="text-red">*</span></label>
                                    <input type="text" id="chsp" name="chsp" class="form-control form-control-sm bg-white " value="{{old('chsp')}}">
                                    <label class="error-msg text-red">@error('chsp'){{$message}}@enderror</label>
                                </div>

                                <div class="form-group col-xs-6 col-sm-6 col-lg-6 mb-0">
                                    <label for="pert_cpm">PERT-CPM<span class="text-red">*</span></label>
                                    <input type="text" id="pert_cpm" name="pert_cpm" class="form-control form-control-sm bg-white " value="{{old('pert_cpm')}}">
                                    <label class="error-msg text-red">@error('pert_cpm'){{$message}}@enderror</label>
                                </div>
                                @endif
                                <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
                                    <label id="remarks_label" for="remarks">Remarks<span class="text-red"></span></label>
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
                                    <th class="text-center">Proposed Bid</th>
                                    <th class="text-center">Bid as Evaluated</th>
                                    <th class="text-center">Bid as Calculated</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Evaluation Status</th>
                                    <th class="text-center">Post Qual Start Date</th>
                                    <th class="text-center">Post Qual End Date</th>
                                    <th class="text-center">Remarks</th>
                                    <th class="text-center">Lowest Bid</th>
                                    <th class="text-center">Contractor ID</th>

                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $row_number=1;
                                @endphp
                                @foreach($data->project_bidders as $bidder)
                                <tr>
                                    <td style="white-space: nowrap">
                                        @if(in_array('update',$user_privilege))
                                        @if($bidder->twg_evaluation_status==='non-responsive')
                                        <button class="btn btn-sm btn-warning pqer-button">PQER</button>
                                        <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        @elseif($bidder->twg_evaluation_status==='responsive')
                                        <button class="btn btn-sm btn-warning pqer-button">PQER</button>
                                        <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        <span class="badge bg-yellow">Responsive</span>
                                        @elseif($bidder->twg_evaluation_status==='active')
                                        <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        @else
                                        <button class="btn btn-sm btn-primary add-bid-as-calculated-btn">Add Bid as Calculated</button>
                                        <button class="btn btn-sm btn-danger non-responsive-btn">Non-responsive</button>
                                        <button class="btn btn-sm btn-success responsive-btn">Responsive</button>
                                        @endif
                                        @endif
                                    </td>
                                    <td>{{$bidder->project_bid}} </td>
                                    @php

                                    if($bidder->proposed_bid!=null && $bidder->proposed_bid>0 && $bidder->twg_evaluation_status!="non-responsive"){
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
                                    <td>{{$bidder->bid_status}}</td>
                                    <td>{{$bidder->twg_evaluation_status}}</td>
                                    <td>{{$bidder->post_qual_start}}</td>
                                    <td>{{$bidder->post_qual_end}}</td>
                                    <td style="white-space: nowrap">{{$bidder->twg_evaluation_remarks}}</td>
                                    <td>{{number_format($bidder->minimum_cost,2,'.',',')}}</td>
                                    <td>{{$bidder->contractor_id}}</td>

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
                                        @if(in_array("update",$user_privilege))
                                        @if($detailed_bid->twg_evaluation_status==='non-responsive')
                                        <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        @elseif($detailed_bid->twg_evaluation_status==='responsive')
                                        <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        <span class="badge bg-yellow">Responsive</span>
                                        @elseif($detailed_bid->twg_evaluation_status==='active')
                                        <button class="btn btn-sm btn-info clear-post-qual-btn">Clear Post Qualification</button>
                                        @else
                                        <button class="btn btn-sm btn-primary add-bid-as-calculated-btn">Add Bid as Calculated</button>
                                        <button class="btn btn-sm btn-danger non-responsive-btn">Non-responsive</button>
                                        <button class="btn btn-sm btn-success responsive-btn">Responsive</button>
                                        @endif
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
    function getLatestPQER(id) {
        var request = $.ajax({
            url: "/get_latest_pqer"
            , type: "POST"
            , data: {
                contractor_id: id
            , }
            , dataType: "JSON"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(data) {
                if (data.pqer != null) {
                    $("#philgeps").val(data.pqer.philgeps);
                    $("#ongoing_projects").val(data.pqer.ongoing_projects);
                    $("#slcc").val(data.pqer.slcc);
                    $("#nfcc").val(data.pqer.nfcc);
                }
            }
        });

    }


    $(".datepicker").datepicker({
        format: 'mm/dd/yyyy'
        , endDate: 'now'
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
        }],

    });



    var oldInputs = '{{ count(session()->getOldInput()) }}';
    if (oldInputs > 2) {
        var process = "{{old('process')}}";
        if (process == "responsive") {
            $("#bidders_form").prop('action', '/twg_responsive_bidder');
            $("#bidder_modal_title").html("Set Responsive Bidder");
        } else if (process == "detailed_responsive") {
            $("#detailed_bid_as_calculated").parent().removeClass("d-none");
            $("#bidders_form").prop('action', '/twg_responsive_bidder');
            $("#bidder_modal_title").html("Set Responsive Bidder");
        } else if (process == "clear-post-qual") {
            $("#bidders_form").prop('action', '/twg_clear_post_qualification_evaluation');
            $("#post_qual_start_date").parent().removeClass('d-none');
            $("#post_qual_start_date").parent().addClass('d-none');
            $("#post_qual_end_date").parent().removeClass('d-none');
            $("#post_qual_end_date").parent().addClass('d-none');
            $("#bid_as_calculated").parent().removeClass('d-none');
            $("#bid_as_calculated").parent().addClass('d-none');
            $("#bidder_modal_title").html("Clear Post Qualification");
        } else if (process == "bid_as_calculated") {
            $("#bidders_form").prop('action', '/twg_bid_as_calculated_bidder');
            $("#bidder_modal_title").html("Add Bid as Calculated");
        } else if (process == "detailed_bid_as_calculated") {
            $("#detailed_bid_as_calculated").parent().removeClass("d-none");
            $("#bidders_form").prop('action', '/twg_bid_as_calculated_bidder');
            $("#bidder_modal_title").html("Add Bid as Calculated");
        } else if (process == "detailed_non_responsive") {
            $("#detailed_bid_as_calculated").parent().removeClass("d-none");
            $("#bidders_form").prop('action', '/twg_non_responsive_bidder');
            $("#bidder_modal_title").html("Set as Non Responsive");
        } else {
            $("#bidders_form").prop('action', '/twg_non_responsive_bidder');
            $("#bidder_modal_title").html("Set as Non Responsive");
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
        } else if ("{{session('message')}}" == "zero_bid") {
            swal.fire({
                title: `Error`
                , text: 'Sorry! Project Bidder has zero proposed bid'
                , buttonsStyling: false
                , confirmButtonClass: 'btn btn-sm btn-danger'
                , icon: 'warning'
            });
            $("#proposed_bid").modal('hide');
        } else if ("{{session('message')}}" == "range_error") {
            swal.fire({
                title: `Error`
                , text: 'Sorry! Post Qual Start - End Date should be in the range of Post Qualification Timeline.'
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


    function clearpostqual(bidder_id, business_name, owner) {
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
                $("#bidders_form").prop("action", "{{route('twg_clear_post_qualification_evaluation')}}");
                $("#bidder_id").val(bidder_id);
                $("#detailed_bid_as_calculated").parent().removeClass("d-none");
                $("#detailed_bid_as_calculated").parent().addClass("d-none");
                $("#process").val("clear-post-qual");
                $("#post_qual_start_date").parent().removeClass('d-none');
                $("#post_qual_start_date").parent().addClass('d-none');
                $("#post_qual_end_date").parent().removeClass('d-none');
                $("#post_qual_end_date").parent().addClass('d-none');
                $("#bid_as_calculated").parent().removeClass('d-none');
                $("#bid_as_calculated").parent().addClass('d-none');
                $("#bidder_modal_title").html("Clear Post Qualification Evaluation");
                business_name = business_name.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(business_name.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(business_name.replace('                                        <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(owner);
                $("#bidder_modal").modal('show');
                $("#philgeps").parent().addClass("d-none");
                $("#ongoing_projects").parent().addClass("d-none");
                $("#slcc").parent().addClass("d-none");
                $("#bsd").parent().addClass("d-none");
                $("#nfcc").parent().addClass("d-none");
                $("#spcab").parent().addClass("d-none");
                $("#orgchart").parent().addClass("d-none");
                $("#key_personnel").parent().addClass("d-none");
                $("#major_equipment").parent().addClass("d-none");
                $("#oss").parent().addClass("d-none");
                $("#jva").parent().addClass("d-none");
                $("#boq").parent().addClass("d-none");
                $("#detailed_estimates").parent().addClass("d-none");
                $("#cash_flow").parent().addClass("d-none");
                $("#provincial_permit").parent().addClass("d-none");
                $("#construction_shedule").parent().addClass("d-none");
                $("#man_power").parent().addClass("d-none");
                $("#construction_methods").parent().addClass("d-none");
                $("#eus").parent().addClass("d-none");
                $("#chsp").parent().addClass("d-none");
                $("#pert_cpm").parent().addClass("d-none");

            }
        });
    }


    // pqer
    $('#bidders_table tbody').on('click', '.pqer-button', function(e) {
        var data = table.row($(this).parents('tr')).data();
        var bidder_id = data[1];
        window.open("/download_pqer/" + bidder_id, '_blank').focus();



    });

    // Non-responsive
    $('#bidders_table tbody').on('click', '.non-responsive-btn', function(e) {
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
                $("#detailed_bid_as_calculated").parent().removeClass("d-none");
                $("#detailed_bid_as_calculated").parent().addClass("d-none");
                $("#philgeps").parent().removeClass("d-none");
                $("#ongoing_projects").parent().removeClass("d-none");
                $("#slcc").parent().removeClass("d-none");
                $("#bsd").parent().removeClass("d-none");
                $("#nfcc").parent().removeClass("d-none");
                $("#spcab").parent().removeClass("d-none");
                $("#orgchart").parent().removeClass("d-none");
                $("#key_personnel").parent().removeClass("d-none");
                $("#major_equipment").parent().removeClass("d-none");
                $("#oss").parent().removeClass("d-none");
                $("#jva").parent().removeClass("d-none");
                $("#boq").parent().removeClass("d-none");
                $("#detailed_estimates").parent().removeClass("d-none");
                $("#cash_flow").parent().removeClass("d-none");
                $("#provincial_permit").parent().removeClass("d-none");
                $("#construction_shedule").parent().removeClass("d-none");
                $("#man_power").parent().removeClass("d-none");
                $("#construction_methods").parent().removeClass("d-none");
                $("#eus").parent().removeClass("d-none");
                $("#chsp").parent().removeClass("d-none");
                $("#pert_cpm").parent().removeClass("d-none");

                $("#philgeps").val("Valid until");
                $("#ongoing_projects").val("No ongoing government and  private contracts");
                $("#slcc").val(" Bidder has no Single Largest Completed Contract but allowed to bid. The Contractor's PCAB license is Category  D and a size range of Small B for Road Projects and the project cost is not more than the ARCC.");
                $("#bsd").val("Signed and duly notarized on ");
                $("#nfcc").val("");
                $("#spcab").val("Not Applicable");
                $("#orgchart").val("Complied");
                $("#key_personnel").val("Complied");
                $("#major_equipment").val("Complied");
                $("#oss").val("Complied and duly notarized");
                $("#jva").val("Complied");
                $("#boq").val(data[13]);
                $("#detailed_estimates").val("Submitted");
                $("#cash_flow").val("Submitted");
                $("#provincial_permit").val("Submitted");
                $("#construction_shedule").val("Submitted");
                $("#man_power").val("Submitted");
                $("#construction_methods").val("Submitted");
                $("#eus").val("Submitted");
                $("#chsp").val("Submitted");
                $("#pert_cpm").val("Submitted");
                $("#bidders_form").prop('action', '/twg_responsive_bidder');
                $("#bidder_modal_title").html("Set Responsive Bidder");
                $("#process").val("responsive");
                $("#bidder_id").val(data[1]);
                $("#bid_as_calculated").val(data[13]);
                $("#contractor_id").val(data[14]);

                $("#process").val("non-responsive");
                $("#bidders_form").prop('action', '/twg_non_responsive_bidder');
                $("#bidder_modal_title").html("Set Non-Responsive Bidder");
                $("#bid_as_calculated").val(data[13]);
                $("#bidder_id").val(data[1]);
                var owner = data[3];
                getLatestPQER(data[14]);
                owner = owner.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[4]);
                $("#bidder_modal").modal('show');
            }
        });
    });

    $('#detailed_bidders_table tbody').on('click', '.non-responsive-btn', function(e) {
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
                $("#process").val("detailed_non_responsive");
                $("#bidders_form").prop('action', '/twg_non_responsive_bidder');
                $("#bidder_modal_title").html("Set Non-Responsive Bidder");
                var data = detailed_table.row($(this).parents('tr')).data();
                $("#bidder_id").val(data[1]);
                var owner = data[4];
                $("#detailed_bid_as_calculated").parent().removeClass('d-none');
                owner = owner.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[5]);
                $("#detailed_bid_as_calculated").parent().removeClass("d-none");
                $("#bidder_modal").modal('show');
            }
        });

    });

    @if(in_array("update", $user_privilege))
    $('#bidders_table tbody').on('click', '.responsive-btn', function(e) {
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
                $("#detailed_bid_as_calculated").parent().removeClass("d-none");
                $("#detailed_bid_as_calculated").parent().addClass("d-none");
                $("#philgeps").parent().removeClass("d-none");
                $("#ongoing_projects").parent().removeClass("d-none");
                $("#slcc").parent().removeClass("d-none");
                $("#bsd").parent().removeClass("d-none");
                $("#nfcc").parent().removeClass("d-none");
                $("#spcab").parent().removeClass("d-none");
                $("#orgchart").parent().removeClass("d-none");
                $("#key_personnel").parent().removeClass("d-none");
                $("#major_equipment").parent().removeClass("d-none");
                $("#oss").parent().removeClass("d-none");
                $("#jva").parent().removeClass("d-none");
                $("#boq").parent().removeClass("d-none");
                $("#detailed_estimates").parent().removeClass("d-none");
                $("#cash_flow").parent().removeClass("d-none");
                $("#provincial_permit").parent().removeClass("d-none");
                $("#construction_shedule").parent().removeClass("d-none");
                $("#man_power").parent().removeClass("d-none");
                $("#construction_methods").parent().removeClass("d-none");
                $("#eus").parent().removeClass("d-none");
                $("#chsp").parent().removeClass("d-none");
                $("#pert_cpm").parent().removeClass("d-none");

                $("#philgeps").val("Valid until");
                $("#ongoing_projects").val("No ongoing government and private contracts");
                $("#slcc").val(" Bidder has no Single Largest Completed Contract but allowed to bid. The Contractor's PCAB license is Category D and a size range of Small B for Road Projects and the project cost is not more than the ARCC.");
                $("#bsd").val("Signed and duly notarized on ");
                $("#nfcc").val("");
                $("#spcab").val("Not Applicable");
                $("#orgchart").val("Complied");
                $("#key_personnel").val("Complied");
                $("#major_equipment").val("Complied");
                $("#oss").val("Complied and duly notarized");
                $("#jva").val("Complied");
                $("#boq").val(data[13]);
                $("#detailed_estimates").val("Submitted");
                $("#cash_flow").val("Submitted");
                $("#provincial_permit").val("Submitted");
                $("#construction_shedule").val("Submitted");
                $("#man_power").val("Submitted");
                $("#construction_methods").val("Submitted");
                $("#eus").val("Submitted");
                $("#chsp").val("Submitted");
                $("#pert_cpm").val("Submitted");
                $("#bidders_form").prop('action', '/twg_responsive_bidder');
                $("#bidder_modal_title").html("Set Responsive Bidder");
                $("#process").val("responsive");
                $("#bidder_id").val(data[1]);
                $("#bid_as_calculated").val(data[13]);
                $("#contractor_id").val(data[14]);


                var owner = data[3];
                owner = owner.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[4]);
                getLatestPQER(data[14]);
                $("#bidder_modal").modal('show');
            }
        });
    });
    $('#detailed_bidders_table tbody').on('click', '.responsive-btn', function(e) {
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
                $("#detailed_bid_as_calculated").parent().removeClass("d-none");
                $("#bidders_form").prop('action', '/twg_responsive_bidder');
                $("#bidder_modal_title").html("Set Responsive Bidder");
                $("#process").val("detailed_responsive");

                var data = detailed_table.row($(this).parents('tr')).data();
                $("#bidder_id").val(data[1]);
                var owner = data[4];
                owner = owner.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[5]);
                $("#bidder_modal").modal('show');
            }
        });
    });
    $('#bidders_table tbody').on('click', '.add-bid-as-calculated-btn', function(e) {
        Swal.fire({
            title: 'Add Bid as Calculated'
            , text: 'Are you sure to Add Bid as Calculated'
            , showCancelButton: true
            , confirmButtonText: 'Yes'
            , cancelButtonText: "No"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-success btn btn-sm'
            , cancelButtonClass: 'btn btn-sm btn-default btn btn-sm'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                $("#detailed_bid_as_calculated").parent().removeClass('d-none');
                $("#detailed_bid_as_calculated").parent().addClass('d-none');
                $("#philgeps").parent().addClass("d-none");
                $("#ongoing_projects").parent().addClass("d-none");
                $("#slcc").parent().addClass("d-none");
                $("#bsd").parent().addClass("d-none");
                $("#nfcc").parent().addClass("d-none");
                $("#spcab").parent().addClass("d-none");
                $("#orgchart").parent().addClass("d-none");
                $("#key_personnel").parent().addClass("d-none");
                $("#major_equipment").parent().addClass("d-none");
                $("#oss").parent().addClass("d-none");
                $("#jva").parent().addClass("d-none");
                $("#boq").parent().addClass("d-none");
                $("#detailed_estimates").parent().addClass("d-none");
                $("#cash_flow").parent().addClass("d-none");
                $("#provincial_permit").parent().addClass("d-none");
                $("#construction_shedule").parent().addClass("d-none");
                $("#man_power").parent().addClass("d-none");
                $("#construction_methods").parent().addClass("d-none");
                $("#eus").parent().addClass("d-none");
                $("#chsp").parent().addClass("d-none");
                $("#pert_cpm").parent().addClass("d-none");

                $("#bidders_form").prop('action', '/twg_bid_as_calculated_bidder');
                $("#bidder_modal_title").html("Add Bid as Calculated");
                $("#process").val("bid_as_calculated");
                var data = table.row($(this).parents('tr')).data();
                $("#bidder_id").val(data[1]);
                var owner = data[3];
                $("#bid_as_calculated").val(data[13]);
                owner = owner.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[4]);
                $("#bidder_modal").modal('show');
            }
        });

    });
    $('#detailed_bidders_table tbody').on('click', '.add-bid-as-calculated-btn', function(e) {
        Swal.fire({
            title: 'Add Bid as Calculated'
            , text: 'Are you sure to Add Bid as Calculated'
            , showCancelButton: true
            , confirmButtonText: 'Yes'
            , cancelButtonText: "No"
            , buttonsStyling: false
            , confirmButtonClass: 'btn btn-sm btn-success btn btn-sm'
            , cancelButtonClass: 'btn btn-sm btn-default btn btn-sm'
            , icon: 'warning'
        }).then((result) => {
            if (result.value == true) {
                $("#bidders_form").prop('action', '/twg_bid_as_calculated_bidder');
                $("#bidder_modal_title").html("Add Bid as Calculated");
                $("#detailed_bid_as_calculated").parent().removeClass('d-none');
                $("#process").val("detailed_bid_as_calculated");
                var data = detailed_table.row($(this).parents('tr')).data();
                $("#bidder_id").val(data[1]);
                var owner = data[4];
                owner = owner.replace('                                                            <span class="badge badge-danger">Late</span>', '');
                $("#business_name").val(owner.replace('                                        <span class="badge badge-success">Winner</span>', ''));
                $("#proposed_bid_business_name").val(owner.replace('                                       <span class="badge badge-success">1st</span>', ''));
                $("#owner").val(data[5]);
                $("#bidder_modal").modal('show');
            }
        });

    });
    @endif

    @if(in_array("delete", $user_privilege))
    $('#bidders_table tbody').on('click', '.clear-post-qual-btn', function(e) {
        var data = table.row($(this).parents('tr')).data();
        var bidder_id = data[1];
        var business_name = data[3];
        var owner = data[4];
        clearpostqual(bidder_id, business_name, owner);
    });
    $('#detailed_bidders_table tbody').on('click', '.clear-post-qual-btn', function(e) {
        var data = detailed_table.row($(this).parents('tr')).data();
        var bidder_id = data[1];
        var business_name = data[4];
        var owner = data[5];
        clearpostqual(bidder_id, business_name, owner);
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
