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
                    <h2 id="title">Progress of Projects</h2>
                </div>
                <div class="card-body">
                <form class="col-sm-8 mx-auto" method="POST" id="project_year_form" action="/get_project_table">
                        @csrf
                        <div class="row d-flex">
                            <div class="form-group col-xs-6 col-sm-6 col-lg-6 ">
                                <label for="pick_year">Project Year</label>
                                <input type="text" class="form-control form-control-sm" name="year" id="datepicker" />
                                <label class="error-msg text-red">@error('year'){{$message}}@enderror</label>
                            </div>
                            <div class="form-group col-xs-3 col-sm-3 col-lg-3 mt-4">
                                <button class="btn btn-primary text-center mx-auto">Submit</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered " id="table">
                            <thead class="">
                                <tr class="bg-primary text-white">
                                    <th class="text-center">Project Number</th>
                                    <th class="text-center">Title</th>
                                    <th class="text-center">Opening Date</th>
                                    <th class="text-center">Process</th>
                                    <th class="text-center">Days</th>
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
    
    var data = {!!json_encode(session('tabledata')) !!};

    $("#datepicker").datepicker({
    format: "yyyy",
    viewMode: "years", 
    minViewMode: "years",
    autoclose:true //to close picker once year is selected
    , });

    // $('#table thead tr').clone(true).appendTo( '#table thead' );
    // $('#table thead tr:eq(1)').removeClass('bg-primary');

    var table = $('#table').DataTable({
        data: data
        , dataType: 'json'
        , columns: [{
                "data": "project_no", 
                render: function(data, type, row) {
                    return "<a  class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank'  href='/view_project/" + row.plan_id + "'>" + data + "</i></a>";
                }
            }
            , {
                "data": "project_title"
            }
            , {
                "data": "open_bid"
            }
            , {
                "data": "process"
            }
            , {
                "data": "progress"
            }
        ], 
        language: {
            paginate: {
                next: '<i class="fas fa-angle-right">'
                , previous: '<i class="fas fa-angle-left">'
            }
        }
        , order: [2, "asc"]
        , paging: true
        , columnDefs: [{
             visible: true
        }]
        , rowsGroup: [3]
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
