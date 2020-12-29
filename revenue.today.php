<?php include 'includes/header.php';?>

<?php
    require('./utils/today.utils.php');
    require('utils/dashboard.utils.php');

    $data = getRevenueData();
    $pieData = getRevByStatus(); 
    $total = getTodayRevenue();
?>

<div class='page-heading display-4 mb-4'>
    <img src='./images/schedule-meeting.png' width='64px' height='64px' class='page-icon mr-2'>Today's Revenue
</div>

<div class="row ">
    <div class="col-12 col-md-7 m-3">
        <div class="card p-0">
            <div class="card-header lead">
                <i class="fas fa-table mr-2"></i>All Revenue Expected
            </div>
            <div class="card-body table-responsive" style="overflow-x: scroll">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <?php foreach (array_keys($data[0]) as $fieldName) : ?>
                                <?php if (!in_array($fieldName, ["CustomerID", "BsClass"])): ?>
                                <th class="text-xs font-weight-bold text-uppercase" scope="col"><?php echo $fieldName ?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $occ) : ?>
                            <tr class="text-muted">
                                <?php foreach ($occ as $field=>$val) : ?>
                                    <?php if ($field == "status"):?>
                                        <td><span class="badge badge-pill badge-<?php echo $occ['BsClass']; ?>"><?php echo $val ?></span></td>
                                    <?php elseif (!in_array($field, ["CustomerID", "BsClass"])): ?>
                                        <td><?php $id=$occ['CustomerID']; echo in_array($field, ["first name", "last name"]) ? "<a href='./customer.php?id=$id'>$val</a>" : $val ?></td>
                                    <?php endif; ?>

                                <?php endforeach; ?>
                            </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="col-12 col-md-4 m-3">
        <div class="card p-0">
            <div class="card-header lead">
            <i class="fas fa-chart-pie mr-2"></i>Revenue by Status
            </div>
            <div class="card-body">
                <div class="text-center text-xs font-weight-bold text-uppercase">Total: $<?php echo $total;?></div>
                <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Received
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> Accrued
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-secondary"></i> Scheduled
                    </span>
                </div>
            </div>
        </div>
    </div>


</div>

<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';
    //get data
    const pieLabels = [];
    const pieData = [];
    const bgColors = [];
    const hbgColors = [];
    <?php
    ?>

    <?php foreach($pieData as $data): ?>
        pieLabels.push('<?php echo $data['status']; ?>');
        pieData.push('<?php echo $data['value']; ?>');
        bgColors.push('<?php echo $data['bgColor']; ?>');
        hbgColors.push('<?php echo $data['hbgColor']; ?>');
    <?php endforeach; ?>
    

    // Pie Chart Example
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
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
            maintainAspectRatio: true,
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
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.labels[tooltipItem.index] || '';
                    var datasetNum = chart.datasets[0].data[tooltipItem.index] || '';
                    return datasetLabel + ': $' + number_format(datasetNum);
                }
            },
            },
            legend: {
            display: false
            },
            cutoutPercentage: 80,
        }
    })

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

</script>

<?php include 'includes/footer.php';?>
