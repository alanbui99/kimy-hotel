<?php include './includes/header.php';?>
<?php
    require('./utils/reports.utils.php');

    $customers = getAllCustomers();
    $revPerCus = getRevPerCustomer();
    $staysPerCus = getStaysPerCustomer();
    $lenPerStay = getLenPerStay();
    $returnCusRate = getReturnCusRate();
?>

<h1 class="mb-4"><span class="badge badge-pill badge-secondary">Customer Report</span></h1>

<!-- Content Row -->
<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Revenue per Customer</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $revPerCus?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Stays per Customer</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $staysPerCus?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Nights per Stay</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $lenPerStay?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ruler fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Return Customer Rate
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $returnCusRate?>%</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?php echo number_format($returnCusRate, 0)?>%" aria-valuenow="<?php echo number_format($returnCusRate, 0)?>" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-undo-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="card mb-4">
    <div class="card-header lead">
        <i class="fas fa-table mr-2"></i>All Customers
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <?php foreach (array_keys($customers[0]) as $fieldName) : ?>
                        <th scope="col"><?php echo $fieldName ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($customers as $cus) : ?>
                    <tr onclick="toDetailPage(<?php echo $cus['ID']?>)" style="cursor: pointer;">
                        <?php foreach ($cus as $field=>$val) : ?>
                            <td><?php echo $field=='Revenue created' ? '$' : ''?><?php echo $val ?></td>
                        <?php endforeach; ?>
                    </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<script type="text/javascript">
function toDetailPage(id) {
    document.location.href = './customer.php?id=' + id;
}
</script>


<?php include './includes/footer.php';?>
