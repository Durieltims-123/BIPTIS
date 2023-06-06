@extends('layouts.app')
<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }

    .text-wrap {
        word-break: break-all !important;
    }

    tbody:nth-child(odd) {
        background: #CCC;
    }

</style>
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
    <div id="app" class="row">
        <div class="card shadow col-sm-12">
            <div class="card shadow border-0">
                <div class="card-header">
                    <h2 id="title">Summary Of Bidding Documents</h2>
                </div>
                <div class="card-body">
                    <form class="col-sm-12 mx-auto" method="POST" id="" action="{{route('submit_summary_of_bidding_documents')}}">
                        @csrf
                        <div class="row d-flex">
                            <div class="form-group col-xs-3 col-sm-3 col-lg-3 ">
                                <label for="bidder_status">Bidder Status</label>
                                <select type="text" id="bidder_status" name="bidder_status" class="form-control form-control-sm" value="{{old('bidder_status')}}">
                                    <option value="0" {{ old('bidder_status') == "0" ? 'selected' : ''}}>Did Not Submit</option>
                                    <option value="1" {{ old('bidder_status') == "1" ? 'selected' : ''}}>Disqualified/Ineligible/Disapproved</option>
                                    <option value="2" {{ old('bidder_status') == "2" ? 'selected' : ''}}>Non-Responsive</option>
                                    <option value="3" {{ old('bidder_status') == "3" ? 'selected' : ''}}>Awarded/Responsive</option>
                                    <option value="4" {{ old('bidder_status') == "4" ? 'selected' : ''}}>Ongoing</option>
                                    <option value="5" {{ old('bidder_status') == "5" ? 'selected' : ''}}>Loosing</option>
                                    <option value="6" {{ old('bidder_status') == "6" ? 'selected' : ''}}>Widthrawn</option>
                                    <option value="7" {{ old('bidder_status') == "7" ? 'selected' : ''}}>All</option>
                                </select>
                                <label class="error-msg text-red">@error('bidder_status'){{$message}}@enderror</label>
                            </div>


                            <div class="form-group col-xs-3 col-sm-3 col-lg-3 ">
                                <label for="date_start">Date Start</label>
                                <input type="text" id="date_start" name="date_start" class="form-control form-control-sm datepicker" value="{{old('date_start')}}">
                                <label class="error-msg text-red">@error('date_start'){{$message}}@enderror</label>
                            </div>

                            <div class="form-group col-xs-3 col-sm-3 col-lg-3 ">
                                <label for="date_end">Date End</label>
                                <input type="text" id="date_end" name="date_end" class="form-control form-control-sm datepicker" value="{{old('date_end')}}">
                                <label class="error-msg text-red">@error('date_end'){{$message}}@enderror</label>
                            </div>

                            <div class="form-group col-xs-3 col-sm-3 col-lg-3 mt-4">
                                <button class="btn btn-primary text-center mx-auto">Submit</button>
                                @if(session('project_bidders'))
                                <a class="btn btn-info text-center" target="_blank" href="/download_summary_of_bidding_documents/{{date('Y-m-d',strtotime(old('date_start')))}}/{{date('Y-m-d',strtotime(old('date_end')))}}/{{old('bidder_status')}}/{{old('procurement_mode')}}">Download</a>
                                @endif
                            </div>

                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered " id="table">
                            <thead class="">
                                <tr class="bg-primary text-white">
                                    <th class="text-center">Group</th>
                                    <th class="text-center">Date Opened</th>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Project Title</th>
                                    <th class="text-center">ABC</th>
                                    <th class="text-center">Prospective Bidders</th>
                                    <th class="text-center">Awarded Total</th>
                                    <th class="text-center">Awarded</th>
                                    <th class="text-center">Not Awarded Total</th>
                                    <th class="text-center">Failure/DNS/Late/Loosing</th>
                                    <th class="text-center">Ongoing Total</th>
                                    <th class="text-center">For Post Qualification</th>
                                    <th class="text-center">Remarks</th>
                                    <th class="text-center">OR</th>
                                    <th class="text-center">Winning Bidder</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>

                                </tr>
                            </tfoot>

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
    var data = {!!json_encode(session('project_bidders')) !!};
    $(".datepicker").datepicker({
        format: 'mm/dd/yyyy'
    , });


    // $('#table thead tr').clone(true).appendTo( '#table thead' );
    // $('#table thead tr:eq(1)').removeClass('bg-primary');

    var table = $('#table').DataTable({
        data: data
        , dataType: 'json'
        , columns: [{
                "data": "group"
            }
            , {
                "data": "bidding_date"
            }
            , {
                "data": "count"
            }
            , {
                "data": "project_title"
            }
            , {
                "data": "project_cost"
            }
            , {
                "data": "bidder"
            }
            , {
                "data": "total_fees"
                , "render": function(data, type, row, meta) {
                    if (row.bid_status == "responsive") {
                        return data;
                    } else {
                        return 0.00;
                    }
                }
            }
            , {
                "data": "fees"
                , "render": function(data, type, row, meta) {
                    if (row.bid_status == "responsive") {
                        return data;
                    } else {
                        return "";
                    }
                }
            }
            , {
                "data": "total_fees"
                , "render": function(data, type, row, meta) {
                    if (row.bid_status == "Did Not Submit" || row.bid_status == "disqualified" || row.bid_status == "non-responsive") {
                        return data;
                    } else {
                        return 0.00;
                    }
                }
            }
            , {
                "data": "fees"
                , "render": function(data, type, row, meta) {
                    if (row.bid_status == "Did Not Submit" || row.bid_status == "disqualified" || row.bid_status == "non-responsive" || row.bid_status == "Loosing Bid" || row.bid_status == "disapproved") {
                        return data;
                    } else {
                        return "";
                    }
                }
            }
            , {
                "data": "total_fees"
                , "render": function(data, type, row, meta) {
                    if (row.bid_status == "ongoing") {
                        return data;
                    } else {
                        return 0.00;
                    }
                }
            }
            , {
                "data": "fees"
                , "render": function(data, type, row, meta) {
                    if (row.bid_status == "ongoing") {
                        return data;
                    } else {
                        return "";
                    }
                }
            }
            , {
                "data": "remarks"
                , "render": function(data, type, row, meta) {

                    return data;

                }
            }
            , {
                "data": "bid_doc_release_date"
                , "render": function(data, type, row, meta) {


                    return row.control_number + "  -  " + data;

                }
            }
            , {
                "data": "winning_bidder"
            },

        ]
        , order: [2, "asc"]
        , paging: false
        , columnDefs: [{
            targets: [0, 2, 6, 8, 10]
            , visible: false
        }]
        , rowsGroup: [3]
        , footerCallback: function(row, data, start, end, display) {
            var api = this.api()
                , data

            // Remove the formatting to get integer data for summation
            var intVal = function(i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                    i : 0;
            };



            total_awarded = api.cells(null, 6).render('display').reduce(function(a, b) {
                return intVal(a) + intVal(b);
            }, 0);

            total_not_responsive = api.cells(null, 8).render('display').reduce(function(a, b) {
                return intVal(a) + intVal(b);
            }, 0);

            total_ongoing = api.cells(null, 10).render('display').reduce(function(a, b) {
                return intVal(a) + intVal(b);
            }, 0);



            // Update footer
            $(api.column(7).footer()).html(
                '<h3>TOTAL AWARDED: <span class="text-red"> PHP ' + total_awarded.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + "</span></h3>"
            );

            $(api.column(9).footer()).html(
                '<h3>TOTAL DNS/Disqualified/Non-Responsive/Loosing Bids: <span class="text-red"> PHP ' + total_not_responsive.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + "</span></h3>"
            );

            $(api.column(11).footer()).html(
                '<h3>TOTAL ONGOING: <span class="text-red"> PHP ' + total_ongoing.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + "</span></h3>"
            );

        }

    });

    $('#table thead tr:eq(1) th').each(function(i) {
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


    // remove duplicate group header
    var seen = {};
    $('.dtrg-group').each(function() {
        var txt = $(this).text();
        if (seen[txt])
            $(this).remove();
        else
            seen[txt] = true;
    });

</script>
@endpush
