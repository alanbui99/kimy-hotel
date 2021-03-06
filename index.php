<?php include 'includes/header.php';?>
<?php
    require('utils/dashboard.utils.php');
    /* Today's Stats */
    $numRooms = getTodayNumRooms();
    $numCheckIns = getTodayCheckIns();
    $numCheckOuts = getTodayCheckOuts();
    $revenue = getTodayRevenue();

    /* Occupancy Chart*/
    $occChartData = getOccupancyChartData();
    /* Booking sources Chart*/
    $sourcesChartData = getSourcesChartData();
    $totalBookings = getTotalBookings();
?>

<div class="page-heading display-4 mb-4">
    <img src="./images/chart.png" width="64px" height="64px" class="page-icon mr-2">Dashboard
</div>

<div class="card mb-4">
    <div class="card-header lead">
        <i class="fas fa-compass"></i>
        Important Shortcuts
    </div>
    <div class="card-body">
        <div class="row justify-content-around">
            <div class="d-flex justify-content-center col-12 col-md-4 col-lg-2">
                <a href="./avail.res.php">
                    <button class="shortcut btn btn-lg btn-light m-3">
                        <!-- <i class="fas fa-calendar-plus"></i><br> -->
                        <img src="./images/online-book-room.png" width="32px" height="32px" class="page-icon mr-2"><br>
                        Book a room
                    </button>
                </a>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-4 col-lg-2">
                <a href="./search.checkin-out.php">
                    <button type="button" class="shortcut btn btn-lg btn-light m-3">
                        <!-- <i class="fas fa-user-check"></i><br> -->
                        <img src="./images/check-in (1).png" width="32px" height="32px" class="page-icon mr-2"><br>
                        Check in/out
                    </button>
                </a>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-4 col-lg-2">   
                <a href="./rooms.report.php">
                    <button type="button" class="shortcut btn btn-lg btn-light m-3">
                        <!-- <i class="fas fa-hotel"></i><br> -->
                        <img src="./images/hotel.png" width="32px" height="32px" class="page-icon mr-2"><br>
                        Rooms
                    </button>
                </a>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-4 col-lg-2">
                <a href="./customers.report.php">
                    <button type="button" class="shortcut btn btn-lg btn-light m-3">
                        <!-- <i class="fas fa-users"></i><br> -->
                        <img src="./images/group.png" width="32px" height="32px" class="page-icon mr-2"><br>
                        Customers
                    </button>
                </a>
            </div>
            <div class="d-flex justify-content-center col-12 col-md-4 col-lg-2">
                <a href="./revenue.report.php">
                    <button type="button" class="shortcut btn btn-lg btn-light m-3">
                        <!-- <i class="fas fa-hand-holding-usd"></i><br> -->
                        <img src="./images/profit-graph.png" width="32px" height="32px" class="page-icon mr-2"><br>
                        Revenue
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header lead">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg>
        Today's Stats
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="card-title">Rooms Occupied</div>
                        <div class="card-text d-flex justify-content-between">
                            <h5><?php echo $numRooms['occupied']. '/' .$numRooms['total']?></h5>
                            <a class="small text-white stretched-link" href="./rooms.today.php">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-secondary text-white mb-4">
                    <div class="card-body">
                        <div class="card-title">Check-ins Made</div>
                        <div class="card-text d-flex justify-content-between">
                            <h5><?php echo $numCheckIns['made']. '/' .$numCheckIns['expected']?></h5>
                            <a class="small text-white stretched-link" href="./checkins.today.php">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body">
                        <div class="card-title">Check-outs Made</div>
                        <div class="card-text d-flex justify-content-between">
                            <h5><?php echo $numCheckOuts['made']. '/' .$numCheckOuts['expected']?></h5>
                            <a class="small text-white stretched-link" href="./checkouts.today.php">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="card-title">Revenue Expected</div>
                        <div class="card-text d-flex justify-content-between">
                            <h5>$<?php echo $revenue?></h5>
                            <a class="small text-white stretched-link" href="./revenue.today.php">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row d-flex" >
    <div class="col-xl-8">
        <div class="card mb-4">
            <div class="card-header lead">
                <i class="fas fa-chart-area mr-1"></i>
                Daily Occupancy
            </div>
            <div class="card-body">
                <canvas id="myAreaChart" style="min-height:250px" width="100%" height="30"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header lead">
                <i class="fas fa-chart-pie mr-1"></i>
                Booking Sources
            </div>
            <div class="card-body">
                <div class="text-center text-xs font-weight-bold">Total: <?php echo $totalBookings;?> bookings</div>
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart" width="100%" height="159"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-secondary"></i> Agency
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Corporate
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Direct
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> OTA
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

//Get data
const labels = [];
const futureData = [];
const pastData = [];

const todayDate = '<?php echo date('Y-m-d') ?>'
let curDate;

<?php foreach ($occChartData as $day) : ?>
    labels.push('<?php echo date_format(date_create($day['date']), "M d") ?>');
    curDate = '<?php echo $day['date'] ?>';
    if (curDate < todayDate) {
        pastData.push(<?php echo $day['occupied'] ?>);
        futureData.push(null);
    } else if (curDate > todayDate)  {
        futureData.push(<?php echo $day['occupied'] ?>);
        pastData.push(null);
    } else {
        pastData.push(<?php echo $day['occupied'] ?>);
        futureData.push(<?php echo $day['occupied'] ?>);
    }

<?php endforeach; ?>

// Area Chart Example
const ctxArea = document.getElementById("myAreaChart");
const myLineChart = new Chart(ctxArea, {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
        label: 'completed/ongoing',
        backgroundColor: "rgba(2,117,216,0.2)",
        borderColor: "rgba(2,117,216,1)",
        pointRadius: 5,
        pointBackgroundColor: "rgba(2,117,216,1)",
        pointBorderColor: "rgba(255,255,255,0.8)",
        pointHoverRadius: 5,
        pointHoverBackgroundColor: "rgba(2,117,216,1)",
        pointHitRadius: 50,
        pointBorderWidth: 1,
        data: pastData,
    }, {
        label: 'scheduled',
        backgroundColor: "rgb(102, 102, 153, 0.2)",
        borderDash: [5],
        borderColor: "rgb(102, 102, 153)",
        pointRadius: 5,
        pointBackgroundColor: "rgb(102, 102, 153)",
        pointBorderColor: "rgba(255,255,255,0.8)",
        pointHoverRadius: 5,
        pointHoverBackgroundColor: "rgb(102, 102, 153)",
        pointHitRadius: 50,
        pointBorderWidth: 1,
        data: futureData,
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: 30,
          maxTicksLimit: 5
        },
        gridLines: {
          color: "rgba(0, 0, 0, .125)",
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
        displayColors: false,
        callbacks: {
            label: function(tooltipItem, chart) {
                if (tooltipItem.label == '<?php echo date('M d') ?>' && tooltipItem.datasetIndex == 1) return;
                return tooltipItem.yLabel + ' rooms';
            }
        }
    },
    maintainAspectRatio: false,
    responsive: true,
  }
});
</script>

<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';
    //get data
    const pieLabels = [];
    const pieData = [];
    const bgColors = [];
    const hbgColors = [];
    <?php foreach ($sourcesChartData as $data) : ?>
        pieLabels.push('<?php echo $data['Source'] ?>');
        pieData.push('<?php echo $data['count'] ?>');
        bgColors.push('<?php echo $data['bgColor'] ?>');
        hbgColors.push('<?php echo $data['hbgColor'] ?>');
    <?php endforeach; ?>

    // Pie Chart Example
    const ctxPie = document.getElementById("myPieChart");
    const myPieChart = new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: pieLabels,
        datasets: [{
        data: pieData,
        backgroundColor: bgColors,
        hoverBackgroundColor: hbgColors,
        hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        caretPadding: 10,
        },
        legend: {
        display: false
        },
        cutoutPercentage: 80,
    },
    });

</script>

<?php include 'includes/footer.php';?>
