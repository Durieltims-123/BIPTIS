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
                "data": "project_number"
            }
            , {
                "data": "project_title"
            }
            , {
                "data": "opening_date"
            }
            , {
                "data": "process"
            }
            , {
                "data": "days"
            }
        ]
        , order: [2, "asc"]
        , paging: false
        , columnDefs: [{
            targets: [0, 2, 6, 8, 10]
            , visible: false
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
