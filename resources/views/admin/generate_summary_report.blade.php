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
          <h2 id="title">Project Summary Report</h2>
        </div>
        <div class="card-body">
          <div>
            <form id="Update_Mon_Yr">
            <span>Select Month: </span>
              <select name="month" id="month_summary">
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
              <span>Select Year: </span>
              <input class="year_pick" name ="year_summary" id="year_summary" style="width: 75px;" type="text">
            </form>
            <center><canvas id="summaryreport" style="width:100%;"></canvas></center>
          </div>
        </div>
        <div class="card-body">
          <div>
            <form id="Select_Start_End_Year">
            <span>Select Start-End Year: </span>
            <input class="year_pick " name ="select_start_year" id="select_start_year" style="width: 75px;" type="text">
            <span>-</span>
            <input class="year_pick " name="select_end_year" id="select_end_year" style="width: 75px;" type="text">
            </form>
            <center><canvas id="unprocured_project" style="width:100%;"></canvas></center>
          </div>
        </div>
        <div class="card-body">
          <div>
            <form id="Select_Start_End_Year_1">
            <span>Select Start-End Year: </span>
            <input class="year_pick " name ="select_start_year_1" id="select_start_year_1" style="width: 75px;" type="text">
            <span>-</span>
            <input class="year_pick " name="select_end_year_1" id="select_end_year_1" style="width: 75px;" type="text">
            </form>
            <center><canvas id="reg_and_supp_project" style="width:100%;"></canvas></center>
          </div>
        </div>
        <div class="card-body">
          <div>
            <form id="Select_Start_End_Year_2">
            <span>Select Start-End Year: </span>
            <input class="year_pick " name ="select_start_year_2" id="select_start_year_2" style="width: 75px;" type="text">
            <span>-</span>
            <input class="year_pick " name="select_end_year_2" id="select_end_year_2" style="width: 75px;" type="text">
            </form>
            <center><canvas id="mode_project" style="width:100%;"></canvas></center>
          </div>
        </div>
        <div class="card-body">
          <div>
            <form id="Select_Year_Municipal">
            <span>Select Year: </span>
            <input class="year_pick " name ="select_start_year_3" id="select_year" style="width: 75px;" type="text">
            <span>Status: </span>
            <select name="status" id="status">
              <option value = "complete">Complete</option>
              <option value="ongoing">Ongoing</option>
              <option value="unprocured">Unprocured</option>
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
              </select>
            </form>
            <center><canvas id="municipal_project" style="width:100%;"></canvas></center>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@push('custom-scripts')
<!--import Chart.js library -->
<script>src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.0/chart.min.js"</script>

<script>
// Chart 1
// Summary Report - Doughnut Graph 
var xValues = [];
var yValues = [];
var barColors = ["#fd7f6f", "#7eb0d5", "#b2e061", "#bd7ebe", "#ffb55a", "#ffee65", "#beb9db", "#fdcce5", "#8bd3c7"];
var chart = new Chart("summaryreport", {
  type: "doughnut",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "Project Summary Report"
    },
    cutoutPercentage: 60
  }
});

function update_graph(xValues,yValues,barColors){
  chart.destroy();
  chart = new Chart("summaryreport", {
  type: "doughnut",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "Project Summary Report"
    },
    cutoutPercentage: 60
  }
});
}

function get_data(year){

  if ($("#year_summary").val() ==""){
    $("#year_summary").val(year);

  }
  var request = $.ajax({
  url: "/get_month_year_report",
  type: "POST",
  data: {
    year:$("#year_summary").val(),
    month:$("#month_summary").val()
  },
  dataType: "JSON",
  headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
  success: function(data) {
        xValues = data.columns;
        yValues = data.data;
        update_graph(xValues,yValues,barColors);
      }
  });
}

$("#Update_Mon_Yr").on("change", function(){
  get_data();
});
//Chart 2
//Unprocured Projects - Trend Lines
var xValues1 = [];
var yValues1 = [];
var barColors1 = [];
var chart1 = new Chart("unprocured_project", {
  type: "line",
  data: {
    labels: xValues1,
    datasets: [{
      fill: false,
      borderColor: 'rgb(75, 192, 192)',
      tension: 0,
      data: yValues1
    }]
  },
  options: {
    title: {
      display: true,
      text: "Yearly Unprocured Projects"
    },
  }
});

function update_unprocured_project(xValues,yValues,barColors){
  chart1.destroy();
  chart1 = new Chart("unprocured_project", {
  type: "line",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues,
      fill: false,
      borderColor: 'black',
      tension: 0
    }]
  },
  options: {
    title: {
      display: true,
      text: "Yearly Unprocured Projects"
    },
    elements: {
      point:{
        radius: 7
      }
    }
  }
});
}

function get_unprocured_projects(year){
  //validates null input and compares start_year and end_year
  if ($("#select_start_year").val() =="" && $("#select_end_year").val() ==""&& $("#select_start_year").val() <= $("#select_end_year").val()){
    $("#select_start_year").val(year-4);
    $("#select_end_year").val(year+4);
  }

  var request = $.ajax({
  url: "/get_unprocured_project",
  type: "POST",
  data: {
    start_year:$("#select_start_year").val(),
    end_year:$("#select_end_year").val()
  },
  dataType: "JSON",
  headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
  success: function(data) {
        xValues1 = data.project_year;
        yValues1 = data.project_count;
        update_unprocured_project(xValues1,yValues1,barColors1);
      }
  });
}

$("#Select_Start_End_Year").on("change", function(){
  get_unprocured_projects();
});

//Chart 3
//Regular & Supplemental Projects - Bar Graph
var xValues2 = [];
var yValues2 = [];
var zValues2 = [];
var barColors1 = [];
chart2 = new Chart("reg_and_supp_project", {
  type: "bar",
  data: {
    labels: xValues2,
    datasets: [{
  label: 'Regular Projects',
  data: [],
  backgroundColor: 'rgba(99, 132, 0, 0.6)',
  borderColor: 'rgba(99, 132, 0, 1)',
  yAxisID: "y-axis-gravity"
  },{
  label: 'Supplemental Projects',
  data: [],
  backgroundColor: 'rgba(0, 99, 132, 0.6)',
  borderColor: 'rgba(0, 99, 132, 1)',
  yAxisID: "y-axis-density"
}]
  },
  options: {
    title: {
      display: true,
      text: "Yearly Accomplished Regular and Supplemental Projects"
    },
    scales: {
    xAxes: [{
      barPercentage: 1,
      categoryPercentage: 0.6
    }],
    yAxes: [{
      id: "y-axis-density"
    }, {
      id: "y-axis-gravity"
    }]
  }
  }
});
function update_reg_supp_project(xValues,yValues,zValues){
  chart2.destroy();
  chart2 = new Chart("reg_and_supp_project", {
  type: "bar",
  data: {
    labels: xValues,
    datasets: [{
  label: 'Regular Projects',
  data: yValues,
  backgroundColor: 'rgba(99, 132, 0, 0.6)',
  borderColor: 'rgba(99, 132, 0, 1)',
  },{
  label: 'Supplemental Projects',
  data: zValues,
  backgroundColor: 'rgba(0, 99, 132, 0.6)',
  borderColor: 'rgba(0, 99, 132, 1)',
}]
  },
  options: {
    title: {
      display: true,
      text: "Yearly Accomplished Projects"
    },
    scales: {
    xAxes: [{
      barPercentage: 1,
      categoryPercentage: 0.6
    }],
    yAxes: {
      beginAtZero: true
    }
  }
  }
});
}

function get_reg_supp_projects(year){
  //validates null input and compares start_year and end_year
  if ($("#select_start_year_1").val() =="" && $("#select_end_year_1").val() ==""&& $("#select_start_year_1").val() <= $("#select_end_year_1").val()){
    $("#select_start_year_1").val(year-4);
    $("#select_end_year_1").val(year+4);
  }

  var request = $.ajax({
  url: "/get_reg_supp_project",
  type: "POST",
  data: {
    start_year:$("#select_start_year_1").val(),
    end_year:$("#select_end_year_1").val()
  },
  dataType: "JSON",
  headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
  success: function(data) {
        xValues2 = data.project_year;
        yValues2 = data.project_reg;
        zValues2 = data.project_supp;
        update_reg_supp_project(xValues2,yValues2,zValues2);
      }
  });
}

$("#Select_Start_End_Year_1").on("change", function(){
  get_reg_supp_projects();
});

//Chart 4
//SVP, Bidding, Negotiated, Procurement Projects - Multiple Line Graph

var xValues3 = [];
var yValues3 = [];
var zValues3 = [];
var aValues3 = [];

var chart3 = new Chart("mode_project", {
  type: "line",
  data: {
    labels: xValues3,
    datasets: [{
      label: 'SVP',
      fill: false,
      borderColor: 'rgb(75, 192, 192)',
      tension: 0,
      data: yValues3
    },{
      label: 'Bidding',
      fill: false,
      borderColor: 'rgb(75, 192, 192)',
      tension: 0,
      data: zValues3
    },{
      label: 'Negotiated',
      fill: false,
      borderColor: 'rgb(75, 192, 192)',
      tension: 0,
      data: aValues3
    }]
  },
  options: {
    title: {
      display: true,
      text: "Total Modes of Projects"
    },
  }
});

function update_mode_project(xValues,yValues,zValues,aValues){
  chart3.destroy();
  chart3 = new Chart("mode_project", {
  type: "line",
  data: {
    labels: xValues,
    datasets: [{
      label: 'SVP',
      fill: false,
      borderColor: 'rgb(75, 192, 192)',
      tension: 0,
      data: yValues
    },{
      fill: false,
      label: 'Bidding',
      borderColor: 'rgb(75, 192, 192)',
      tension: 0,
      data: zValues
    },{
      fill: false,
      label: 'Negotiated',
      borderColor: 'rgb(75, 192, 192)',
      tension: 0,
      data: aValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "Total Modes of Projects"
    },
    elements: {
      point:{
        radius: 7
      }
    }
  }
});
}

function get_mode_projects(year){
  //validates null input and compares start_year and end_year
  if ($("#select_start_year_2").val() =="" && $("#select_end_year_2").val() ==""&& $("#select_start_year_2").val() <= $("#select_end_year_2").val()){
    $("#select_start_year_2").val(year-4);
    $("#select_end_year_2").val(year+4);
  }

  var request = $.ajax({
  url: "/get_mode_project",
  type: "POST",
  data: {
    start_year:$("#select_start_year_2").val(),
    end_year:$("#select_end_year_2").val()
  },
  dataType: "JSON",
  headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
  success: function(data) {
        xValues3 = data.project_year;
        yValues3 = data.project_SVP;
        zValues3 = data.project_bidding;
        aValues3 = data.project_procurement;
        update_mode_project(xValues3,yValues3,zValues3,aValues3);
      }
  });
}

$("#Select_Start_End_Year_2").on("change", function(){
  get_mode_projects();
});

//Chart 5
//Project Type per Municipality with Status - Pie Chart

var xValues4 = [];
var yValues4 = [];
var barColors4 = ["#fd7f6f", "#7eb0d5", "#b2e061", "#bd7ebe", "#ffb55a", "#ffee65", "#beb9db", "#fdcce5", "#8bd3c7"];
var chart4 = new Chart("municipal_project", {
  type: "pie",
  data: {
    labels: xValues4,
    datasets: [{
      backgroundColor: barColors4,
      data: yValues4
    }]
  },
  options: {
    title: {
      display: true,
      text: "Projects Status per Municipality"
    },
    cutoutPercentage: 60
  }
});

function update_graph_municipal(xValues,yValues,barColors){
  chart4.destroy();
  chart4 = new Chart("municipal_project", {
  type: "pie",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "Projects Status per Municipality"
    },
    cutoutPercentage: 60
  }
});
}

function get_proj_status_mun(year){

  if ($("#select_year").val() ==""){
    $("#select_year").val(year);

  }
  var request = $.ajax({
  url: "/get_proj_status_mun",
  type: "POST",
  data: {
    year:$("#select_year").val(),
    status:$("#status").val(),
    municipal:$("#municipality").val()
  },
  dataType: "JSON",
  headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
  success: function(data) {
        xValues4 = data.columns;
        yValues4 = data.data;
        update_graph_municipal(xValues4,yValues4,barColors4);
      }
  });
}

$("#Select_Year_Municipal").on("change", function(){
  get_proj_status_mun();
});

//Customizes Data Picker
$('.year_pick').datepicker({
         minViewMode: 2,
         format: 'yyyy'
       });
//Loads all functions
$( document ).ready(function() {
  const month = ["January","February","March","April","May","June","July","August","September","October","November","December"];

  const d = new Date();
  let year = d.getFullYear();

  get_data(year);
  get_unprocured_projects(year);
  get_reg_supp_projects(year);
  get_mode_projects(year);
});
</script>

@endpush
