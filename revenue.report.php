<?php include './includes/header.php';?>
<?php
    require('./utils/reports.utils.php');
    $totalRev = getTotalRev();
?>

<div class='page-heading display-4 mb-4'>
    <img src='./images/profit-graph.png' width='64px' height='64px' class='page-icon mr-2'>Revenue Report
</div>

<div class="row">

    <!-- Area Chart -->
    <div class="col-xl-7 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header lead">
                <div class="row">
                    <div class="m-0 font-weight-bold text-primary col-sm">
                        <select class="custom-select font-weight-bold text-primary" id="lineChartContent" onchange="updateLineChart()">
                            <option value="total"selected>Total Revenue</option>
                            <option value="per-room">Revenue per Room</option>
                            <option value="per-stay">Revenue per Stay</option>
                        </select>
                    </div>
                    <form class="col-sm d-flex justify-content-end">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input lineChartBy" type="radio" name="granularity" id="day" value="day" checked onchange="updateLineChart()">
                            <label class="form-check-label" for="day">Day</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input lineChartBy" type="radio" name="granularity" id="month" value="month" onchange="updateLineChart()">
                            <label class="form-check-label" for="month">Month</label>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-5 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header">
                <div class="row">
                    <h6 class="col-sm mt-2 font-weight-bold text-primary">Revenue Sources by</h6>
                    <div class="col-sm">
                        <select class="custom-select" id="pieChartContent" onchange="updatePieChart()">
                            <option value="room-type"selected>Room types</option>
                            <option value="channel">Booking channels</option>
                        </select>
                    </div>
                </div>

            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="text-center text-xs font-weight-bold text-uppercase">Total: $<?php echo $totalRev;?></div>
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart" height="214"></canvas>
                </div>
                <div id="rev-source-labels" class="small row">
                    <div class="col-2"></div>
                    <div class="col-8 ">
                        <div class="row justify-content-center">
                            <span class="mr-3 source-label"></span>
                            <span class="source-label"></span>
                        </div>
                        <div class="row justify-content-center">
                            <span class="mr-3 source-label"></span>
                            <span class="source-label"></span>
                        </div>

                    </div>
                    <div class="col-2"></div>
                        
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
updateLineChart();
function updateLineChart() {
    let chartBy, chartContent;

    chartContent = document.getElementById('lineChartContent').value;
    document.querySelectorAll('.lineChartBy').forEach(option => {
        if (option.checked == true) chartBy = option.id
    })    

    fetch(`utils/revenue-chart.utils.php?q=${chartContent}&by=${chartBy}`, {method: 'GET'})
    .then(response => response.json())
    .then(data => {
        createLineChart(data, chartBy);
    })
}
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

// Area Chart Example
var myLineChart;
function createLineChart(data, by) {
    if (myLineChart) {
        myLineChart.destroy(); 
    }

    const labels  = [];
    const pastData = [];
    const futureData = [];
    let today = moment().format(by == 'month' ? "YYYY-MM" :"YYYY-MM-DD");
    for (let i = 0; i < data.periods.length; i++) {
        labels.push(moment(data.periods[i]).format(by == 'month' ? 'MMM YYYY' : 'MMM DD'));
        if (data.periods[i] < today) {
            pastData.push(data.values[i]);
            futureData.push(null);
        } else if (data.periods[i] > today) {
            futureData.push(data.values[i]);
            pastData.push(null);
        } else {
            pastData.push(data.values[i]);
            futureData.push(data.values[i]);
        }
    }

    today = moment().format(by == 'month' ? "MMM YYYY" :"MMM DD");

    var ctx = document.getElementById("myAreaChart");
    myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: "completed/ongoing",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: pastData,
        },
        {
            label: "scheduled",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderDash: [5],
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: futureData,
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
        padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
        }
        },
        scales: {
        xAxes: [{
            time: {
            unit: 'date'
            },
            gridLines: {
            display: false,
            drawBorder: false
            },
            ticks: {
            maxTicksLimit: 7
            }
        }],
        yAxes: [{
            ticks: {
            maxTicksLimit: 5,
            padding: 10,
            // Include a dollar sign in the ticks
            callback: function(value, index, values) {
                return '$' + number_format(value);
            }
            },
            gridLines: {
            color: "rgb(234, 236, 244)",
            zeroLineColor: "rgb(234, 236, 244)",
            drawBorder: false,
            borderDash: [2],
            zeroLineBorderDash: [2]
            }
        }],
        },
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                boxWidth: 20,
                padding: 20
            }
        },
        tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        titleMarginBottom: 10,
        titleFontColor: '#6e707e',
        titleFontSize: 14,
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        intersect: false,
        mode: 'index',
        caretPadding: 10,
        callbacks: {
            label: function(tooltipItem, chart) {
                if (tooltipItem.label == today && tooltipItem.datasetIndex == 1) return;
                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                return '$' + number_format(tooltipItem.yLabel);
            }
        }
        }
    }
    });    
}
</script>


<script>

updatePieChart();

async function updatePieChart() {
    const source = document.getElementById('pieChartContent').value;
    let data;

    fetch(`utils/revenue-sources.utils.php?q=${source}`, {method: 'GET'})
    .then(response => response.json())
    .then(jsonRes => {
        data = jsonRes;
        createPieChart(data);

        const labels = document.getElementsByClassName('source-label')
        // const classes = ['primary', 'success', 'info', 'secondary'];

        for (let i = 0; i < data.length; i++) {
            labels[i].innerHTML = `<i class='fas fa-circle text-${data[i].bsClass} mr-1'></i>${data[i].src}`
        }
    })
}

// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example
var myPieChart;

function createPieChart(data) {
    if (myPieChart) {
        myPieChart.destroy(); 
    }

    const sources = [];
    const values = [];
    const bgColors = [];
    const hbgColors = [];

    data.forEach(d => {
        sources.push(d.src);
        values.push(d.val);
        bgColors.push(d.bgColor);
        hbgColors.push(d.hbgColor);
    })

    var ctx = document.getElementById("myPieChart");
    myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: sources,
        datasets: [{
        data: values,
        backgroundColor: bgColors,
        hoverBackgroundColor: hbgColors,
        hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        caretPadding: 10,
        callbacks: {
            label: function(tooltipItem, chart) {
                var datasetLabel = chart.labels[tooltipItem.index] || '';
                var datasetNum = chart.datasets[0].data[tooltipItem.index] || '';
                return datasetLabel + ': $' + number_format(datasetNum);
            }
            }
        },
        legend: {
        display: false
        },
        cutoutPercentage: 80,
    },
    });


}



</script>







<?php include './includes/footer.php';?>
