@extends('layouts.app')

@section('content')
  @include('layouts.headers.cards2')
  <div class="container-fluid mt-1">
    <div id="app">
      <div class="card shadow mt-4 mb-5">
        <div class="card shadow border-0">
          <div class="card-header">
            <h2 id="title">{{$project_details->project_title}}</h2>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                <label class="text-black"><strong clas>Project Number</strong>
                </label>
                <input readonly class="form-control bg-white text-black" value="{{$project_details->project_no}}">
              </div>
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                <label class="text-black"><strong clas>APP Number</strong>
                </label>
                <input readonly class="form-control bg-white text-black" value="{{$project_details->app_group_no}}">
              </div>

              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                <label class="text-black"><strong clas>Location</strong>
                </label>
                @if($project_details->barangay_name!=null)
                  <input readonly class="form-control bg-white text-black" value="{{ $project_details->barangay_name }},{{ $project_details->municipality_name }}">
                @else
                  <input readonly class="form-control bg-white text-black" value="{{ $project_details->municipality_name }}">

                @endif
              </div>
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                <label class="text-black"><strong clas>Mode of Procurement</strong>
                </label>
                <input readonly class="form-control bg-white text-black" value="{{ $project_details->mode }}">
              </div>

              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                <label class="text-black"><strong clas>Date Pow Added</strong>
                </label>
                <input readonly class="form-control bg-white text-black" value="{{$project_details->date_pow_added}}">
              </div>
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                <label class="text-black"><strong clas>Duration</strong>
                </label>
                <input readonly class="form-control bg-white text-black" value="{{$project_details->duration}}">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                <label class="text-black"><strong clas>Contractor</strong>
                </label>
                <input readonly class="form-control bg-white text-black" value="{{$project_details->business_name}}">
              </div>
              <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                <label class="text-black"><strong clas>Bid as Read @if($project_details->plan_cluster_id)<span class="badge bg-yellow">Cluster {{$project_details->plan_cluster_id}}</span>@endif</strong>
                </label>
                @if($project_details->detailed_bid_as_read!=null)
                  <input readonly class="form-control bg-white text-black" value="@if($project_details->business_name){{number_format($project_details->detailed_bid_as_read,2,'.',',')}}@endif">
                  @else
                    <input readonly class="form-control bg-white text-black" value="@if($project_details->business_name){{number_format($project_details->proposed_bid,2,'.',',')}}@endif">

                    @endif
                  </div>
                  <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                    <label class="text-black"><strong clas>Bid as Evaluated @if($project_details->plan_cluster_id)<span class="badge bg-yellow">Cluster {{$project_details->plan_cluster_id}}</span>@endif</strong>
                    </label>
                    @if($project_details->detailed_bid_as_evaluated!=null)
                      <input readonly class="form-control bg-white text-black" value="@if($project_details->business_name){{number_format($project_details->detailed_bid_as_evaluated,2,'.',',')}}@endif">
                      @else
                        <input readonly class="form-control bg-white text-black" value="@if($project_details->business_name){{number_format($project_details->bid_as_evaluated,2,'.',',')}}@endif">

                        @endif
                      </div>

                      <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                        <label class="text-black"><strong clas>Bid as Calculated @if($project_details->plan_cluster_id)<span class="badge bg-yellow">Cluster {{$project_details->plan_cluster_id}}</span>@endif</strong>
                        </label>
                        <input readonly class="form-control bg-white text-black" value="@if($project_details->business_name){{number_format($project_details->twg_final_bid_evaluation,2,'.',',')}}@endif">

                        </div>

                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Planned Posting</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{date('F Y',strtotime($project_details->abc_post_date))}}">
                        </div>
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Planned Opening</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{date('F Y',strtotime($project_details->sub_open_date))}}">
                        </div>
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Planned Notice of Award</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{date('F Y',strtotime($project_details->award_notice_date))}}">
                        </div>
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Planned Contract Signing</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{date('F Y',strtotime($project_details->contract_signing_date))}}">
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>ABC</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{ number_format($project_details->abc,2,'.',',') }}">
                        </div>
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Project Cost</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="@if($project_details->project_cost){{ number_format($project_details->project_cost,2,'.',',') }}@else {{ number_format($project_details->abc,2,'.',',') }}
                          @endif">
                        </div>
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Source of Fund</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{$project_details->source}}">
                        </div>
                      </div>

                      <div class="row">
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Project Year</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{$project_details->project_year}}">
                        </div>
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Status</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{$project_details->project_status}}">
                        </div>
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Rebid Count</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{$project_details->re_bid_count}}">
                        </div>
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Resolution Number</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{$project_details->resolution_number}}">
                        </div>
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Remarks</strong>
                          </label>
                          <input readonly class="form-control bg-white text-black" value="{{$project_details->remarks}}">
                        </div>
                        @if($project_details->parent_id!=null)
                          <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                            <label class="text-black"><strong clas>Parent Project</strong>
                            </label>
                          </br>
                          <a href="/view_project/{{ $project_details->parent_id }}" class="col-xs-12 bg-white text-black">{{$project_details->project_title}}</a>
                        </div>
                      @endif

                      @if($project_details->child_id!=null)
                        <div class="form-group col-xs-12 col-sm-4 col-lg-4 mb-2 ">
                          <label class="text-black"><strong clas>Child Project</strong>
                          </label>
                        </br>
                        <a href="/view_project/{{ $project_details->child_id }}" class="col-xs-12 bg-white text-black">{{$project_details->child_title}}</a>
                      </div>
                    @endif
                    </div>

                  </div>
                </div>
              </div>

              <div class="card shadow mt-4 mb-5">
                <div class="card shadow border-0">
                  <div class="card-header">
                    <h2 id="title">Project Timeline/s</h2>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="timelines_table">
                        <thead class="">
                          <tr class="bg-primary text-white" >
                            <th class="text-center"></th>
                            <th class="text-center">Ads/Posting</th>
                            <th class="text-center">Pre-bid</th>
                            <th class="text-center">Submission of Bid</th>
                            <th class="text-center">Bid Evaluation</th>
                            <th class="text-center">Post Qualification</th>
                            <th class="text-center">Issuance of NOA</th>
                            <th class="text-center">Contract Signing</th>
                            <th class="text-center">Approval by Higher Authority</th>
                            <th class="text-center">Notice to Proceed</th>
                            <th class="text-center">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if(count($project_timelines)>0)
                            @php
                            $row_number=1;
                            @endphp
                            @if($project_timelines[0]->timeline_status=='set')
                              @foreach($project_timelines as $project_timeline)
                                <tr>
                                  <td>{{$row_number}}</td>
                                  <td>@if($project_timeline->advertisement_start){{ date("M d,Y", strtotime($project_timeline->advertisement_start))}} - {{ date("M d,Y", strtotime($project_timeline->advertisement_end))}}
                                  @endif</td>
                                  <td>@if($project_timeline->pre_bid_start) {{ date("M d,Y", strtotime($project_timeline->pre_bid_start))}} - {{ date("M d,Y", strtotime($project_timeline->pre_bid_end))}}
                                  @endif</td>
                                  <td>@if($project_timeline->bid_submission_start){{ date("M d,Y", strtotime($project_timeline->bid_submission_start))}} - {{ date("M d,Y", strtotime($project_timeline->bid_submission_end))}}
                                  @endif</td>
                                  <td>@if($project_timeline->bid_evaluation_start){{ date("M d,Y", strtotime($project_timeline->bid_evaluation_start))}} - {{ date("M d,Y", strtotime($project_timeline->bid_evaluation_end))}}
                                  @endif</td>
                                  <td>@if($project_timeline->post_qualification_start){{ date("M d,Y", strtotime($project_timeline->post_qualification_start))}} - {{ date("M d,Y", strtotime($project_timeline->post_qualification_end))}}
                                  @endif</td>
                                  <td>@if($project_timeline->award_notice_start){{ date("M d,Y", strtotime($project_timeline->award_notice_start))}} - {{ date("M d,Y", strtotime($project_timeline->award_notice_end))}}
                                  @endif</td>
                                  <td>@if($project_timeline->contract_signing_start){{ date("M d,Y", strtotime($project_timeline->contract_signing_start))}} - {{ date("M d,Y", strtotime($project_timeline->contract_signing_end))}}
                                  @endif</td>
                                  <td>@if($project_timeline->authority_approval_start) {{ date("M d,Y", strtotime($project_timeline->authority_approval_start))}} - {{ date("M d,Y", strtotime($project_timeline->authority_approval_end))}}
                                  @endif</td>
                                  <td>@if($project_timeline->proceed_notice_start){{ date("M d,Y", strtotime($project_timeline->proceed_notice_start))}} - {{ date("M d,Y", strtotime($project_timeline->proceed_notice_end))}}
                                  @endif</td>
                                  <td>{{ $project_timeline->main_status }}</td>
                                </tr>
                                @php
                                $row_number=$row_number+1;
                                @endphp
                              @endforeach
                            @endif

                          @endif
                        </tbody>

                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow mt-4 mb-5">
                <div class="card shadow border-0">
                  <div class="card-header">
                    <h2 id="title">Actual Procurement Activities</h2>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="procacts_table">
                        <thead class="">
                          <tr class="bg-primary text-white" >
                            <th class="text-center"></th>
                            <th class="text-center">Pre Procurement</th>
                            <th class="text-center">Advertisement</th>
                            <th class="text-center">Pre-bid</th>
                            <th class="text-center">Opening Date</th>
                            <th class="text-center">Opening Time</th>
                            <th class="text-center">Bid Evaluation</th>
                            <th class="text-center">Post Qualification</th>
                            <th class="text-center">Notice of Awards</th>
                            <th class="text-center">Contract Preparation</th>
                            <th class="text-center">Approval by Higher Authority</th>
                            <th class="text-center">Notice to Proceed</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if(count($procacts)>0)
                            @php
                            $row_number=1;
                            @endphp
                            @if($project_timelines[0]->timeline_status=='set')
                              @foreach($procacts as $procact)
                                <tr>
                                  <td>{{$row_number}}</td>
                                  <td>@if($procact->pre_proc){{ date("M d,Y", strtotime($procact->pre_proc))}}
                                  @endif</td>
                                  <td>@if($procact->advertisement) {{ date("M d,Y", strtotime($procact->advertisement))}}
                                  @endif</td>
                                  <td>@if($procact->pre_bid){{ date("M d,Y", strtotime($procact->pre_bid))}}
                                  @endif</td>
                                  <td>@if($procact->open_bid){{ date("M d,Y", strtotime($procact->open_bid))}}
                                  @endif</td>
                                  <td>@if($procact->open_time){{ date("h:i a", strtotime($procact->open_time))}}
                                  @endif</td>
                                  <td>@if($procact->bid_evaluation){{ date("M d,Y", strtotime($procact->bid_evaluation))}}
                                  @endif</td>
                                  <td>@if($procact->post_qual){{ date("M d,Y", strtotime($procact->post_qual))}}
                                  @endif</td>
                                  <td>@if($procact->award_notice) {{ date("M d,Y", strtotime($procact->award_notice))}}
                                  @endif</td>
                                  <td>@if($procact->contract_signing){{ date("M d,Y", strtotime($procact->contract_signing))}}
                                  @endif</td>
                                  <td>@if($procact->authority_approval){{ date("M d,Y", strtotime($procact->authority_approval))}}
                                  @endif</td>
                                  <td>@if($procact->proceed_notice){{ date("M d,Y", strtotime($procact->proceed_notice))}}
                                  @endif</td>

                                </tr>
                                @php
                                $row_number=$row_number+1;
                                @endphp
                              @endforeach

                            @endif

                          @endif
                        </tbody>

                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow mt-4 mb-5">
                <div class="card shadow border-0">
                  <div class="card-header">
                    <h2 id="title">Project Bidder/s</h2>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="bidders_table">
                        <thead class="">
                          <tr class="bg-primary text-white" >
                            <th class="text-center">Bid Order</th>
                            <th class="text-center">Mode</th>
                            <th class="text-center">Rank</th>
                            <th class="text-center">Business Name</th>
                            <th class="text-center">Owner</th>
                            <th class="text-center">Proposed Bid/ Bid as Read</th>
                            <th class="text-center">Bid as Evaluated</th>
                            <th class="text-center">Bid as Calculated</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Post Qual Start</th>
                            <th class="text-center">Post Qual End</th>
                            <th class="text-center">TWG Evaluation</th>
                            <th class="text-center">Date Updated</th>
                            <th class="text-center">TWG Remarks</th>

                          </tr>
                        </thead>
                        <tbody>
                          @if(count($project_bidders)>0)
                            @php
                            $row_number=1;
                            $old=$project_bidders[0]->procact_id;
                            @endphp
                            @foreach($project_bidders as $bidder)
                              <tr>
                                <td>{{$bidder->bid_order}}</td>
                                <td>{{$bidder->mode}}</td>
                                @php
                                if($bidder->proposed_bid!=null && $bidder->proposed_bid>0){
                                  if($old!=$bidder->procact_id){
                                    $row_number=1;
                                  }
                                  echo "<td>".$row_number."</td>";
                                  $row_number=$row_number+1;
                                }
                                else{
                                  echo "<td></td>";
                                }
                                @endphp
                                <td>{{$bidder->business_name}}
                                </td>
                                <td>{{$bidder->owner}}</td>
                                <td>{{number_format($bidder->proposed_bid,2,'.',',')}}</td>
                                <td>{{number_format($bidder->bid_as_evaluated,2,'.',',')}}</td>
                                <td>{{number_format($bidder->twg_final_bid_evaluation,2,'.',',')}}</td>
                                <td>@if($bidder->bid_status!="" || $bidder->bid_status!=null){{$bidder->bid_status}}@else DNS
                                @endif</td>
                                <td>{{$bidder->post_qual_start}}</td>
                                <td>{{$bidder->post_qual_end}}</td>
                                <td>{{$bidder->twg_evaluation_status}}</td>
                                <td>{{$bidder->date_updated}}</td>
                                <td>{{$bidder->twg_evaluation_remarks}}</td>
                              </tr>
                            @endforeach

                          @endif
                        </tbody>

                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow mt-4 mb-5">
                <div class="card shadow border-0">
                  <div class="card-header">
                    <h2 id="title">Bidders Records/Logs</h2>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="disqualification_records">
                        <thead class="">
                          <tr class="bg-primary text-white" >
                            <th class="text-center">ID</th>
                            <th class="text-center">Username</th>
                            <th class="text-center">Business Name</th>
                            <th class="text-center">Owner</th>
                            <th class="text-center">Date Updated</th>
                            <th class="text-center">Remarks</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($disqualification_records as $disqualification_record)
                            <tr>

                              <td>{{$disqualification_record->record_id}}</td>
                              <td>{{$disqualification_record->name}}</td>
                              <td>{{$disqualification_record->business_name}}</td>
                              <td>{{$disqualification_record->owner}}</td>
                              <td>{{$disqualification_record->date_updated}}</td>
                              <td>{{$disqualification_record->disqualification_remarks}}</td>

                            </tr>
                          @endforeach
                        </tbody>

                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow mt-4 mb-5">
                <div class="card shadow border-0">
                  <div class="card-header">
                    <h2 id="title">Project Logs</h2>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="project_logs">
                        <thead class="">
                          <tr class="bg-primary text-white" >
                            <th class="text-center">ID</th>
                            <th class="text-center">User</th>
                            <th class="text-center">Log</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Date</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($project_logs as $project_log)
                            <tr>
                              <td>{{$project_log->project_log_id}}</td>
                              <td>{{$project_log->name}}</td>
                              <td>{{$project_log->project_log_type}}</td>
                              <td>{{$project_log->project_log_remarks}}</td>
                              <td>{{$project_log->date_updated}}</td>

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
          $('#bidders_table').DataTable({
            language: {
              paginate: {
                next: '<i class="fas fa-angle-right">',
                previous: '<i class="fas fa-angle-left">'
              }
            },
            orderCellsTop: true,
            responsive:true,
            order: [[ 1, "asc" ]],
            rowGroup: {
              startRender: function ( rows, group ) {
                var group_title=group;
                return group_title;
              },
              endRender: null,
              dataSrc: 0
            }
          });

          $('#procacts_table').DataTable({
            language: {
              paginate: {
                next: '<i class="fas fa-angle-right">',
                previous: '<i class="fas fa-angle-left">'
              }
            }
          });

          $('#timelines_table').DataTable({
            language: {
              paginate: {
                next: '<i class="fas fa-angle-right">',
                previous: '<i class="fas fa-angle-left">'
              }
            }
          });


          $('#project_logs').DataTable({
            language: {
              paginate: {
                next: '<i class="fas fa-angle-right">',
                previous: '<i class="fas fa-angle-left">'
              }
            }
          });

          $('#disqualification_records').DataTable({
            language: {
              paginate: {
                next: '<i class="fas fa-angle-right">',
                previous: '<i class="fas fa-angle-left">'
              }
            }
          });

          </script>
        @endpush
