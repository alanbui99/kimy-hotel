<?php include 'includes/header.php';?>
<?php
    require('utils/checkin-out.utils.php');

    if (isset($_POST['submit'])) {
        $payMethod = $_POST['pay-method'];
        $resID = $_POST['resID'];

        if (makePayment($resID, $payMethod)) {
            echo "
            <div class='container'>
                <h1 class='mb-4'><span class='badge badge-pill badge-secondary'>PaymentConfirmation</span></h1>
                <div class='alert alert-success'>Payment Successfully Made!</div>
            </div>
            ";
        } else {
            echo "
            <div class='container'>
                <h1 class='mb-4'><span class='badge badge-pill badge-secondary'>PaymentConfirmation</span></h1>
                <div class='alert alert-danger'>Payment Failed!</div>
            </div>            
            ";
        }

    }    

?>


<?php include 'includes/footer.php';?>
