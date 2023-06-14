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
              <select name="month" id="month">
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
              <select name="year" id="year">
                <option value="2020">2020</option>
                <option value="2021">2021</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
                <option value="2029">2029</option>
                <option value="2030">2030</option>
              </select>
            </form>
            <center><canvas id="myChart" style="width:100%;max-width:700px"></canvas></center>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@push('custom-scripts')

<!--import Chart.js library -->
<script>src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.1.0/chart.min.js"</script>
<script>
var xValues = [];
var yValues = [];
var barColors = ["#fd7f6f", "#7eb0d5", "#b2e061", "#bd7ebe", "#ffb55a", "#ffee65", "#beb9db", "#fdcce5", "#8bd3c7"];
 var chart = new Chart("myChart", {
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

function get_data(){
  var date = $("#Update_Mon_Yr").serialize();
  console.log(date);

  var request = $.ajax({
  url: "/get_month_year_report",
  type: "POST",
  data: {
    year:$("#year").val(),
    month:$("#month").val()
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

function update_graph(xValues,yValues,barColors){
  chart.destroy();
  chart = new Chart("myChart", {
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


$("#month").on("change", function(){
   get_data();
});

$("#year").on("change", function(){
   get_data();
});

</script>

<script>
$(".datepicker").datepicker({
  format: 'mm/dd/yyyy',
});

var table= $('#app_table').DataTable(
  {
    language: {
      paginate: {
        next: '<i class="fas fa-angle-right">',
        previous: '<i class="fas fa-angle-left">'
      }
    },
    pageLength: 100,
    rowGroup: {
      startRender: function ( rows, group ) {
        return group;
      },
      dataSrc: 1
    },
    columnDefs: [
      {
        targets: [0,1,2,3,4,5,6,7,8,9],
        orderable: false
      },
      {
        targets: [0,1],
        visible: false
      }
    ],
    rowsGroup: [0,1,2,3,4,5,6,7],
  }
);

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
