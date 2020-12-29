<?php include './includes/header.php';?>
<?php
    require('./utils/reports.utils.php');
    $pieChartData = getRoomTypeData();
    $totalRoomsSold = getTotalRoomsSold();
?>

<div class='page-heading display-4 mb-4'>
    <img src='./images/hotel.png' width='64px' height='64px' class='page-icon mr-2'>Room Report
</div>

<div class="row d-flex" >
    <div class="col-xl-12">
        <div class="card mb-4">
            <div class="card-header lead">
                <div class="row">
                    <p class="col-sm"><i class="fas fa-calendar-alt mr-2"></i>Room schedule</p>
                    <form class="form-inline col-sm d-flex justify-content-end">
                        <input type="date" class="form-control mb-2 mr-sm-2" 
                        value="<?= date('Y-m-d', time()); ?>" onchange="getSchedule(this.value)">
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div id="room-schedule">
                </div>
                <div class="small row">
                    <div class="col-md-3"></div>
                    <div class="col-sm-12 col-md-3 d-flex justify-content-around">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Available
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Reserved
                        </span>
                    </div>
                    <div class="col-sm-12 col-md-3 d-flex justify-content-around">
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Occupied
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-secondary"></i> Completed
                        </span>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-8 lead">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Average Daily Occupancy by:
                    </div>
                    <form class="col-sm-4 small mt-2 d-flex justify-content-center">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="occupancyBy" id="day" value="day" checked onchange="getAvgOcc(this.value)">
                            <label class="form-check-label" for="day">Day</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="occupancyBy" id="month" value="month" onchange="getAvgOcc(this.value)">
                            <label class="form-check-label" for="month">Month</label>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body px-0">
                <canvas id="myBarChart" width="100%" height="55"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header lead">
                <i class="fas fa-chart-pie mr-1"></i>
                Rooms Sold by Type
            </div>
            <div class="card-body px-0">
                <canvas id="myPieChart" width="100%" height="50"></canvas>
                <div class="text-center text-xs font-weight-bold text-uppercase mt-2">Total rooms sold: <?php echo $totalRoomsSold;?></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getSchedule(date) {
        fetch(`utils/schedule.utils.php?q=${date}`, {method: 'GET'})
        .then(response => response.json())
        .then(data => {
            document.getElementById('room-schedule').innerHTML = data;
        })
    }

    (function getTodaySchedule() {
        getSchedule('<?php echo date('Y-m-d', time()); ?>');
    })();
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script type="text/javascript">
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

//get data
var radios = document.querySelectorAll('input[type=radio][name="occupancyBy"]');

getAvgOcc('day');
function getAvgOcc(type) {
    document.getElementById("myBarChart").innerHTML = '';
    fetch(`utils/avg-occupancy.utils.php?q=${type}`, {method: 'GET'})
    .then(response => response.json())
    .then(data => {
        createChart(data, type);
    })
}

var myBarChart;
function createChart(data, occBy) {
    if (myBarChart) {
        myBarChart.destroy(); 
    }


    const labels = [];
    const futureData = [];
    const pastData = [];

    const curTime = moment().format("YYYY-MM-DD");
    let curDate;

    data.forEach(item => {
        labels.push(moment(item.period).format(occBy == 'month' ? "MMM YYYY" : "MMM DD"));
        if (item.period <= curTime) {
            pastData.push(item.value);
            futureData.push(null);
        } else {
            futureData.push(item.value);
            pastData.push(null);
        }
    })



    var ctx = document.getElementById("myBarChart");
    myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
        label: "Completed/Ongoing",
        backgroundColor: "rgba(2,117,216,1)",
        borderColor: "rgba(2,117,216,1)",
        data: pastData,
        }, 
        {
        label: "Scheduled",
        backgroundColor: "rgb(128, 193, 255)",
        borderColor: "rgb(128, 193, 255)",
        data: futureData,
        }],
    },
    options: {
        scales: {
        xAxes: [{
            time: {
            unit: occBy
            },
            gridLines: {
            display: false
            },
            ticks: {
            maxTicksLimit: labels.length - 1
            }
        }],
        yAxes: [{
            ticks: {
            min: 0,
            max: 30,
            maxTicksLimit: 4
            },
            gridLines: {
            display: true
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
        }
    }
    });
}

</script>


<script type="text/javascript">
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

const pieLabels = [];
const pieData = [];
const colors = [];
<?php foreach ($pieChartData as $type) : ?>
    pieLabels.push('<?php echo $type['style'] ?>');
    pieData.push(<?php echo $type['count'] ?>);
    colors.push('<?php echo $type['color'] ?>');
<?php endforeach; ?>

// Pie Chart Example
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: pieLabels,
        datasets: [{
            data: pieData,
            backgroundColor: colors,
        }],
    },
});

</script>



<?php include './includes/footer.php';?>
