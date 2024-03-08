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
<head>
</head>
@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
    <div id="app">
        <div class="card shadow">
            <div class="card shadow border-0">
                <div class="card-header">
                    <h2 id="title">Data Visualization Report</h2>
                </div>
                <div class="card-body">
                    <div class="reload_content">

                        <form id="Update_Mon_Yr" class='trigger_form row'>
                            <div class="col-md-3 col-sm-3">
                                <span>Select Month: </span>
                                <select name="month" class='activate_jquery form-control' id="month_summary">
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>

                            </div>
                            <div class="col-md-3 col-sm-3">
                                <span>Select Year: </span>
                                <input class='year_pick form-control' name="year_summary" id="year_summary" type="text">
                            </div>
                        </form>
                        <div class="d-flex justify-content-center">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input table_switch" id="summarySwitch">
                                <label class="label_chart custom-control-label" for="summarySwitch">Table View</label>
                            </div>
                        </div>
                        <div class="row col-lg-9 mx-auto"><canvas id="summaryreport" class="present_chart" style="width:60%; display:inline-block; position: relative;"></canvas></div>
                        <div class="present_table d-none">
                            <div class="col-sm-12 my-2 mx-auto">
                                <h1 class="mx-auto text-center">Project Summary</h1>
                            </div>

                            <table class="table table-bordered " id="table">
                                <thead class="">
                                    <tr class="bg-primary text-white">
                                        <th class="text-center">Project Number</th>
                                        <th class="text-center">Title</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="reload_content">
                            <form id="Select_Start_End_Year" class='trigger_form'>
                                <span>Select Start-End Year: </span>
                                <input class="year_pick " name="select_start_year" id="select_start_year" style="width: 75px;" type="text">
                                <span>-</span>
                                <input class="year_pick " name="select_end_year" id="select_end_year" style="width: 75px;" type="text">
                            </form>
                            <div class="d-flex justify-content-center">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input table_switch" id="unprocuredSwitch">
                                    <label class="label_chart custom-control-label" for="unprocuredSwitch">Switch View</label>
                                </div>
                            </div>
                            <center><canvas id="unprocured_project" class="present_chart" style="width:100%;display:inline-block; position: relative;"></canvas></center>
                            <div class="present_table d-none">
                                <table class="table table-bordered" id="unprocuredTable">
                                    <thead class="">
                                        <tr class="bg-primary text-white">
                                            <th class="text-center">Project Number</th>
                                            <th class="text-center">Title</th>
                                            <th class="text-center">Year</th>
                                            <th class="text-center">Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <form id="Select_Start_End_Year_1">
                                <span>Select Start-End Year: </span>
                                <input class="year_pick " name="select_start_year_1" id="select_start_year_1" style="width: 75px;" type="text">
                                <span>-</span>
                                <input class="year_pick " name="select_end_year_1" id="select_end_year_1" style="width: 75px;" type="text">
                            </form>
                            <center><canvas id="reg_and_supp_project" style="width:100%;display:inline-block; position: relative;"></canvas></center>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <form id="Select_Start_End_Year_2">
                                <span>Select Start-End Year: </span>
                                <input class="year_pick " name="select_start_year_2" id="select_start_year_2" style="width: 75px;" type="text">
                                <span>-</span>
                                <input class="year_pick " name="select_end_year_2" id="select_end_year_2" style="width: 75px;" type="text">
                            </form>
                            <center><canvas id="mode_project" style="width:100%;display:inline-block; position: relative;"></canvas></center>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="reload_content">
                            <form id="Select_Year_Municipal" class='trigger_form'>
                                <span>Select Year: </span>
                                <input class="year_pick " name="select_start_year_3" id="select_year" style="width: 75px;" type="text">
                                <span>Status: </span>
                                <select name="status" id="status">
                                    <option value="complete">Complete</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="unprocured">Unprocured</option>
                                    <option value="all">All</option>
                                </select>
                                <span>Municipality: </span>
                                <select name="municipality" id="municipality">
                                    <option value="Atok">Atok</option>
                                    <option value="Bakun">Bakun</option>
                                    <option value="Bokod">Bokod</option>
                                    <option value="Buguias">Buguias</option>
                                    <option value="Itogon">Itogon</option>
                                    <option value="Kabayan">Kabayan</option>
                                    <option value="Kapangan">Kapangan</option>
                                    <option value="Kibungan">Kibungan</option>
                                    <option value="La Trinidad">La Trinidad</option>
                                    <option value="Mankayan">Mankayan</option>
                                    <option value="Sablan">Sablan</option>
                                    <option value="Tuba">Tuba</option>
                                    <option value="Tublay">Tublay</option>
                                    <option value="ADH">ADH</option>
                                    <option value="IDH">IDH</option>
                                    <option value="KDH">KDH</option>
                                    <option value="NBDH">NBDH</option>
                                    <option value="DMDH">DMDH</option>
                                    <option value="all">All</option>
                                </select>
                            </form>
                            <div class="d-flex justify-content-center">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input table_switch" id="municipalSwitch">
                                    <label class="label_chart custom-control-label" for="municipalSwitch">Switch View</label>
                                </div>
                            </div>
                            <center><canvas id="municipal_project" class="present_chart" style="width:60%; display:inline-block; position: relative; "></canvas></center>
                            <div class="present_table d-none">
                                <table class="table table-bordered " id="table_1">
                                    <thead class="">
                                        <tr class="bg-primary text-white">
                                            <th class="text-center">Project Number</th>
                                            <th class="text-center">Title</th>
                                            <th class="text-center">Project Type</th>
                                            <th class="text-center">Municipality</th>
                                            <th class="text-center">Status</th>
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
    </div>
</div>

@endsection
@push('custom-scripts')
<!--import Chart.js library -->

<script>
    // $('#unprocuredTable thead tr').clone(true).appendTo('#unprocuredTable thead');
    // $('#unprocuredTable thead tr:eq(1)').removeClass('bg-primary');

    var switcher = 0;

    function changeSwitcher(newVal) {
        switcher = newVal; // updating the value of the global variable
    }


    $('.table_switch').click(function() {

        if ($(this).is(":checked")) {
            $(this).parents('.reload_content').find('.present_chart').addClass('d-none');
            $(this).parents('.reload_content').find('.present_table').removeClass('d-none');

            changeSwitcher(1);
            $(this).parents('.reload_content').find('.trigger_form').trigger('change');

        } else {
            $(this).parents('.reload_content').find('.present_table').addClass('d-none');
            $(this).parents('.reload_content').find('.present_chart').removeClass('d-none');

            changeSwitcher(0);
            $(this).parents('.reload_content').find('.trigger_form').trigger('change');
        }

    });

    // Chart 1
    // Summary Report - pie Graph 
    var xValues = [];
    var yValues = [];
    var barColors = [];
    var chart = new Chart("summaryreport", {
        type: "pie"
        , data: {
            labels: xValues
            , datasets: [{
                backgroundColor: barColors
                , data: yValues
            }]
        }
        , options: {
            responsive: true
            , maintainAspectRatio: false
            , plugins: {
                title: {
                    display: true
                    , text: "Project Summary"
                    , font: {
                        size: 24
                    }
                }
                , legend: {
                    position: 'bottom'
                }
            }
        , }
    });

    function update_graph(xValues, yValues, barColors) {
        chart.destroy();
        chart = new Chart("summaryreport", {
            type: "pie"
            , data: {
                labels: xValues
                , datasets: [{
                    backgroundColor: barColors
                    , data: yValues
                }]
            }
            , options: {
                responsive: true
                , maintainAspectRatio: false
                , plugins: {
                    title: {
                        display: true
                        , text: "Project Summary"
                        , font: {
                            size: 24
                        }
                    }
                    , legend: {
                        position: 'bottom'
                    }
                }
            , }
        });
    }

    function get_data(year, switcher) {
        var switcher = window.switcher;
        if ($("#year_summary").val() == "") {
            $("#year_summary").val(year);
        }

        var request = $.ajax({
            url: "/get_month_year_report"
            , type: "POST"
            , data: {
                year: $("#year_summary").val()
                , month: $("#month_summary").val()
                , switcher: switcher
            },
            // dataType: "JSON",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(data) {

                if (switcher == 0) {
                    barColors = dynamicColors(5);
                    xValues = data.columns;
                    yValues = data.data;

                    update_graph(xValues, yValues, barColors);

                } else {
                    if ($.fn.dataTable.isDataTable('#table')) {
                        var table = $('#table').DataTable();
                        table.destroy();
                    }
                    var month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    var selected_month = month[parseInt($("#month_summary").val()) - 1];
                    var table = $('#table').DataTable({
                        data: data
                            // , dataType: 'json'
                        , dom: 'Bfrtip'
                        , buttons: [{
                                extend: 'excel'
                                , filename: `Summary Report for the Month of ${selected_month}`
                            }
                            , {
                                extend: 'pdf'
                                , filename: `Summary Report for the Month of ${selected_month}`
                            }
                        ]
                        , columns: [{
                                "data": "project_no"
                                , render: function(data, type, row) {
                                    return "<a  class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank'  href='/view_project/" + row.plan_id + "'>" + data + "</i></a>";
                                }
                            }
                            , {
                                "data": "project_title"
                            }
                            , {
                                "data": "current_status"
                            }
                        ]
                        , language: {
                            paginate: {
                                next: '<i class="fas fa-angle-right">'
                                , previous: '<i class="fas fa-angle-left">'
                            }
                        }
                        // , order: [2, "asc"]
                        // , paging: true
                        // , columnDefs: [{
                        //      visible: true
                        // }]
                        // , rowsGroup: [3]
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

                }

            }
            //   error: function(data) { 
            //     alert("Status: " +data); 

            // }       
        });
    }



    $("#Update_Mon_Yr").on("change", function() {
        get_data();
    });

    //Chart 2
    //Unprocured Projects - Trend Lines
    var xValues1 = [];
    var yValues1 = [];
    var barColors1 = [];
    var chart1 = new Chart("unprocured_project", {
        type: "line"
        , data: {
            labels: xValues1
            , datasets: [{
                data: yValues
                , pointBackgroundColor: barColors1
                , fill: false
                , borderColor: barColors1
                , tension: 0
            }]
        }
        , options: {
            responsive: true
            , maintainAspectRatio: false
            , plugins: {
                title: {
                    display: true
                    , text: "Unprocured Projects"
                    , font: {
                        size: 24
                    }
                }
                , legend: {
                    display: false
                    , position: 'bottom'
                }
            }
        , }
    });

    function update_unprocured_project(xValues, yValues, barColors) {
        chart1.destroy();
        chart1 = new Chart("unprocured_project", {
            type: "line"
            , data: {
                labels: xValues
                , datasets: [{
                    data: yValues
                    , pointBackgroundColor: barColors
                    , fill: false
                    , borderColor: barColors
                    , tension: 0
                }]
            }
            , options: {
                responsive: true
                , maintainAspectRatio: false
                , plugins: {
                    title: {
                        display: true
                        , text: "Unprocured Projects"
                        , font: {
                            size: 24
                        }
                    }
                    , legend: {
                        display: false
                        , position: 'bottom'
                    }
                }
                , elements: {
                    point: {
                        radius: 7
                    }
                }
            }
        });


    }

    function get_unprocured_projects(year) {
        //validates null input and compares start_year and end_year
        if ($("#select_start_year").val() == "" && $("#select_end_year").val() == "" && $("#select_start_year").val() <= $("#select_end_year").val()) {
            $("#select_start_year").val(year - 10);
            $("#select_end_year").val(year);
        }



        var request = $.ajax({
            url: "/get_unprocured_project"
            , type: "POST"
            , data: {
                start_year: $("#select_start_year").val()
                , end_year: $("#select_end_year").val()
                , table_format: $("#unprocuredSwitch").is(':checked')

            }
            , dataType: "JSON"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(data) {
                if ($("#unprocuredSwitch").is(':checked')) {

                    if ($.fn.dataTable.isDataTable('#unprocuredTable')) {
                        var unprocuredTable = $('#unprocuredTable').DataTable();
                        unprocuredTable.destroy();
                    }

                    var unprocuredTable = $('#unprocuredTable').DataTable({
                        data: data.dataTable
                        , dom: 'lftipr'
                        , buttons: [{
                                extend: 'excel'
                                // , filename: `Summary Report for the Month of ${selected_month}`
                            }
                            , {
                                extend: 'pdf'
                                // , filename: `Summary Report for the Month of ${selected_month}`
                            }
                        ]
                        , columns: [{
                                "data": "project_no"
                                , render: function(data, type, row) {
                                    return "<a class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank' href='/view_project/" + row.plan_id + "'>" + data + "</i></a>";
                                }
                            }
                            , {
                                "data": "project_title"
                            }
                            , {
                                "data": "project_year"
                            }
                            , {
                                "data": "municipality_name"

                            }

                        ]
                        , language: {
                            paginate: {
                                next: '<i class="fas fa-angle-right">'
                                , previous: '<i class="fas fa-angle-left">'
                            }
                        }
                        // , order: [2, "asc"]
                        // , paging: true
                        // , columnDefs: [{
                        // visible: true
                        // }]
                        // , rowsGroup: [3]
                    });



                    $('#unprocuredTable thead tr:eq(1) th').each(function(i) {
                        var title = $(this).text();
                        if (title != "") {
                            $(this).html('<input type="text" placeholder="Search" />');
                            $(this).addClass('sorting_disabled');
                            var index = 0;

                            $('input', this).on('keyup change', function() {
                                if (unprocuredTable.column(':contains(' + title + ')').search() !== this.value) {
                                    unprocuredTable
                                        .column(':contains(' + title + ')')
                                        .search(this.value)
                                        .draw();
                                }

                            });
                        }
                    });

                } else {
                    var chart_color = [];
                    chart_color = dynamicColors(1);

                    barColors1 = chart_color;
                    xValues1 = data.project_year;
                    yValues1 = data.project_count;

                    update_unprocured_project(xValues1, yValues1, barColors1);
                }
            }


        });
    }

    $("#Select_Start_End_Year").on("change", function() {
        get_unprocured_projects();
    });

    //Chart 3
    //Regular & Supplemental Projects - Bar Graph
    var xValues2 = [];
    var yValues2 = [];
    var zValues2 = [];
    var aValues2 = [];
    var bValues2 = [];
    var barColors1 = [];
    chart2 = new Chart("reg_and_supp_project", {
        type: "bar"
        , data: {
            labels: xValues2
            , datasets: [{
                label: 'Regular Projects'
                , data: []
                , backgroundColor: 'rgba(99, 132, 0, 0.6)'
                , borderColor: 'rgba(99, 132, 0, 1)'
            , }, {
                label: 'Supplemental Projects'
                , data: []
                , backgroundColor: 'rgba(0, 99, 132, 0.6)'
                , borderColor: 'rgba(0, 99, 132, 1)'
            , }]
        }
        , options: {
            responsive: true
            , maintainAspectRatio: false
            , plugins: {
                title: {
                    display: true
                    , text: "Accomplished Regular and Supplemental Projects"
                    , font: {
                        size: 24
                    }
                }
                , legend: {

                    position: 'bottom'
                }
            , }
        , }
    });


    function update_reg_supp_project(xValues, yValues, zValues, aValues, bValues) {
        chart2.destroy();
        chart2 = new Chart("reg_and_supp_project", {
            type: "bar"
            , data: {
                labels: xValues,

                datasets: [{
                    label: 'Total Project'
                    , data: aValues
                    , backgroundColor: '#DC136C'
                    , borderColor: '#DC136C'
                }, {
                    label: 'Total Project Completed'
                    , data: bValues
                    , backgroundColor: '#026C7C'
                    , borderColor: '#026C7C'
                }, {
                    label: 'Regular Projects'
                    , data: yValues
                    , backgroundColor: '#548C2F'
                    , borderColor: '#548C2F'
                , }, {
                    label: 'Supplemental Projects'
                    , data: zValues
                    , backgroundColor: '#53599A'
                    , borderColor: '#53599A'
                , }]
            }
            , options: {
                responsive: true
                , maintainAspectRatio: false
                , plugins: {
                    title: {
                        display: true
                        , text: " Accomplished Projects"
                        , font: {
                            size: 24
                        }
                    }
                    , legend: {
                        position: 'bottom'
                    }
                }
            , }
        });
    }

    function get_reg_supp_projects(year) {
        //validates null input and compares start_year and end_year
        if ($("#select_start_year_1").val() == "" && $("#select_end_year_1").val() == "" && $("#select_start_year_1").val() <= $("#select_end_year_1").val()) {
            $("#select_start_year_1").val(year - 10);
            $("#select_end_year_1").val(year);
        }

        var request = $.ajax({
            url: "/get_reg_supp_project"
            , type: "POST"
            , data: {
                start_year: $("#select_start_year_1").val()
                , end_year: $("#select_end_year_1").val()
            }
            , dataType: "JSON"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(data) {
                xValues2 = data.project_year;
                yValues2 = data.project_reg;
                zValues2 = data.project_supp;
                aValues2 = data.project_total;
                bValues2 = data.project_actual;
                update_reg_supp_project(xValues2, yValues2, zValues2, aValues2, bValues2);
            }
        });
    }

    $("#Select_Start_End_Year_1").on("change", function() {
        get_reg_supp_projects();
    });

    //Chart 4
    //SVP, Bidding, Negotiated, Procurement Projects - Multiple Line Graph

    var xValues3 = [];
    var yValues3 = [];
    var zValues3 = [];
    var aValues3 = [];
    var barColors3 = [];
    var chart3 = new Chart("mode_project", {
        type: "line"
        , data: {
            labels: xValues3
            , datasets: [{
                label: 'SVP'
                , fill: false
                , borderColor: barColors3
                , backgroundColor: barColors3
                , tension: 0
                , data: yValues3
            }, {
                label: 'Bidding'
                , fill: false
                , borderColor: barColors3
                , tension: 0
                , data: zValues3
            }, {
                label: 'Negotiated'
                , fill: false
                , borderColor: barColors3
                , tension: 0
                , data: aValues3
            }]
        }
        , options: {

            responsive: true
            , maintainAspectRatio: false
            , plugins: {
                title: {
                    display: true
                    , text: "Total Modes of Projects"
                    , font: {
                        size: 24
                    }
                }
                , legend: {
                    position: 'bottom'
                }
                , elements: {
                    point: {
                        radius: 7
                    }
                }
            }
        , }
    });

    function update_mode_project(xValues, yValues, zValues, aValues, barColors) {
        chart3.destroy();
        chart3 = new Chart("mode_project", {
            type: "line"
            , data: {
                labels: xValues
                , datasets: [{
                    label: 'SVP'
                    , fill: false
                    , borderColor: barColors[0]
                    , backgroundColor: barColors[0]
                    , tension: 0
                    , data: yValues
                }, {
                    fill: false
                    , label: 'Bidding'
                    , borderColor: barColors[1]
                    , backgroundColor: barColors[1]
                    , tension: 0
                    , data: zValues
                }, {
                    fill: false
                    , label: 'Negotiated'
                    , borderColor: barColors[2]
                    , backgroundColor: barColors[2]
                    , tension: 0
                    , data: aValues
                }]
            }
            , options: {

                responsive: true
                , maintainAspectRatio: false
                , plugins: {
                    title: {
                        display: true
                        , text: "Total Modes of Projects"
                        , font: {
                            size: 24
                        }
                    }
                    , legend: {
                        position: 'bottom'
                    }
                }
                , elements: {
                    point: {
                        radius: 7
                    }
                }
            }
        });
    }

    function get_mode_projects(year) {
        //validates null input and compares start_year and end_year
        if ($("#select_start_year_2").val() == "" && $("#select_end_year_2").val() == "" && $("#select_start_year_2").val() <= $("#select_end_year_2").val()) {
            $("#select_start_year_2").val(year - 10);
            $("#select_end_year_2").val(year);
        }

        var request = $.ajax({
            url: "/get_mode_project"
            , type: "POST"
            , data: {
                start_year: $("#select_start_year_2").val()
                , end_year: $("#select_end_year_2").val()
            }
            , dataType: "JSON"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(data) {

                var chart_color = [];
                xValues3 = data.project_year;
                yValues3 = data.project_SVP;
                zValues3 = data.project_bidding;
                aValues3 = data.project_procurement;

                chart_color = dynamicColors(3);

                barColors3 = chart_color;

                update_mode_project(xValues3, yValues3, zValues3, aValues3, barColors3);
            }
        });
    }

    $("#Select_Start_End_Year_2").on("change", function() {
        get_mode_projects();
    });

    //Chart 5
    //Project Type per Municipality with Status - Pie Chart

    var xValues4 = [];
    var yValues4 = [];
    var barColors4 = [];
    var chart4 = new Chart("municipal_project", {
        type: "pie"
        , data: {
            labels: xValues4
            , datasets: [{
                backgroundColor: barColors4
                , data: yValues4
            }]
        }
        , options: {
            responsive: true
            , maintainAspectRatio: false
            , plugins: {
                title: {
                    display: true
                    , text: "Projects Status per Municipality"
                    , font: {
                        size: 24
                    }
                }
                , legend: {
                    position: 'bottom'
                }
            }
        , }
    });

    function update_graph_municipal(xValues, yValues, barColors) {
        chart4.destroy();
        chart4 = new Chart("municipal_project", {
            type: "pie"
            , data: {
                labels: xValues
                , datasets: [{
                    backgroundColor: barColors
                    , data: yValues
                }]
            }
            , options: {
                responsive: true
                , maintainAspectRatio: false
                , plugins: {
                    title: {
                        display: true
                        , text: "Projects Status per Municipality"
                        , font: {
                            size: 24
                        }
                    }
                    , legend: {
                        position: 'bottom'
                    }
                }
            , }
        });
    }

    function get_proj_status_mun(year) {
        var switcher = window.switcher;

        if ($("#select_year").val() == "") {
            $("#select_year").val(year);

        }
        var request = $.ajax({
            url: "/get_proj_status_mun"
            , type: "POST"
            , data: {
                year: $("#select_year").val()
                , status: $("#status").val()
                , municipal: $("#municipality").val()
                , switcher: switcher
            }
            , dataType: "JSON"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(data) {
                if (switcher == 0) {
                    var chart_color = [];
                    xValues4 = data.labels;
                    yValues4 = data.type_count;
                    let length = yValues4.length;
                    barColors4 = dynamicColors(length);

                    update_graph_municipal(xValues4, yValues4, barColors4);
                } else {
                    if ($.fn.dataTable.isDataTable('#table_1')) {
                        var table = $('#table_1').DataTable();
                        table.destroy();
                    }
                    var municipality = $("#municipality").val();
                    var table = $('#table_1').DataTable({
                        data: data
                            // , dataType: 'json'
                        , dom: 'Bfrtip'
                        , buttons: [{
                                extend: 'excel'
                                , filename: `Summary Report for the Municipality of ${municipality}`
                            }
                            , {
                                extend: 'pdf'
                                , filename: `Summary Report for the Municipality of ${municipality}`
                            }
                        ]
                        , columns: [{
                                "data": "project_no"
                                , render: function(data, type, row) {
                                    return "<a  class='btn btn-sm shadow-0 border-0 btn-primary text-white' target='_blank'  href='/view_project/" + row.plan_id + "'>" + data + "</i></a>";
                                }
                            }
                            , {
                                "data": "project_title"
                            }, {
                                "data": "type"
                            }
                            , {
                                "data": "municipality_display"
                            }
                            , {
                                "data": "current_status"
                            }
                        ]
                        , language: {
                            paginate: {
                                next: '<i class="fas fa-angle-right">'
                                , previous: '<i class="fas fa-angle-left">'
                            }
                        }
                        // , order: [2, "asc"]
                        // , paging: true
                        // , columnDefs: [{
                        //      visible: true
                        // }]
                        // , rowsGroup: [3]
                    });

                    $('#table_1 thead tr:eq(1) th').each(function(i) {
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

                }

            }


        });
    }

    $("#Select_Year_Municipal").on("change", function() {
        get_proj_status_mun();
    });

    //Customizes Data Picker
    $('.year_pick').datepicker({
        minViewMode: 2
        , format: 'yyyy'
    });
    //Loads all functions
    $(document).ready(function() {
        const month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        const d = new Date();
        let year = d.getFullYear();

        get_data(year);
        get_unprocured_projects(year);
        get_reg_supp_projects(year);
        get_mode_projects(year);
        get_proj_status_mun(year);
    });

    //Generate random color
    function dynamicColors(total) {
        chart_color = [];
        var color_set = ['#ffc0cb', '#f08080', '#7fffd4', '#98fb98', '#ff1493'
            , '#dda0dd', '#f0e68c', '#1e90ff', '#0000ff'
            , '#f4a460', '#00bfff', '#dc143c', '#00ff7f', '#8a2be2'
            , '#ffd700', '#ff8c00', '#00ced1', '#ff4500', '#b03060'
            , '#008000', '#008080', '#00ff00', '#ffff54', '#32CD32'
            , '#000080', '#228B22', '#8B4513', '#fdf5e6', '#DC143C', '1b998b'
            , '93b5c6', 'BD4F6C', '4B4A67', 'f98948', '9b8816'
            , 'ceb5b7', '9CF6F6', '3f0d12', 'D5BF86', 'd34f73'
        ];
        chart_color = color_set.slice(0, total);
        return chart_color;
    };

</script>

@endpush
