<?php
    require('utils/checkin-out.utils.php');

    if ($_POST) {
        
        session_start();
        $firstName = $_SESSION['firstName'];
        $lastName = $_SESSION['lastName'];
        session_destroy();

        $action = htmlentities($_POST['action']);
        $resID = htmlentities($_POST['resID']);
        $roomNo = htmlentities($_POST['roomNo']);

        if ($action == 'check-in') {
            processCheckIn($resID);
        } else {
            processCheckOut($resID);
            $billDetails = getBillDetails($resID);

        }
    }
    
?>
<?php include 'includes/header.php';?>
<div class="container">
    <?php if ($_POST and $action == 'check-in'): ?>
        <div class="page-heading display-4 mb-4">
            <img src="./images/check-in (1).png" width="64px" height="64px" class="page-icon mr-2">Check In Confirmation
        </div>
        
        <div class="alert alert-success">
            <b><?php echo $firstName.' '.$lastName ?></b> has been checked into room <b><?php echo $roomNo ?></b>, reservation ID <b><?php echo $resID ?></b>.
        </div>

    <?php elseif ($_POST and $action == 'check-out'): ?>
        <div class="page-heading display-4 mb-4">
            <img src="./images/check-in (1).png" width="64px" height="64px" class="page-icon mr-2">Check Out Confirmation
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header lead">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Bill Information
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <?php foreach (array_keys($billDetails['items'][0]) as $fieldName) : ?>
                                        <th scope="col"><?php echo $fieldName ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($billDetails['items'] as $item) : ?>
                                    <tr>
                                    <?php foreach ($item as $field=>$val) : ?>
                                        <td><?php echo $val ?></td>
                                    <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="4"><strong>Total</strong></td>
                                    <td><strong><?php echo $billDetails['total']?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col">
                <div class="card">
                    <div class="card-header lead">
                        <i class="far fa-credit-card mr-2"></i>Payment
                    </div>
                    <div class="card-body">
                        <form class="form-inline" method="POST" action="./process.pay.php">
                            <select class="custom-select form-control mb-2 mr-sm-2" id="pay-method" name="pay-method">
                                <option selected disabled>Select payment method</option>
                                <option value="cash">Cash</option>
                                <option value="credit">Credit card</option>
                                <option value="debit">Debit card</option>
                            </select>
                            <input type="hidden" name="resID" value="<?php echo $resID?>">                    
                            <input type="submit" name="submit" class="btn btn-success form-control mb-2 mr-sm-2" value="Pay">
                        </form>
                    </div>
                </div>
            </div>
        </div>


        



    <?php endif; ?>

</div>
<?php include 'includes/footer.php';?>
