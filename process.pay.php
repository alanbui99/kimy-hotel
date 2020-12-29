<?php include 'includes/header.php';?>
<?php
    require('utils/checkin-out.utils.php');

    echo "
    <div class='container'>
        <div class='page-heading display-4 mb-4'>
            <img src='./images/payment.png' width='64px' height='64px' class='page-icon mr-2'>Payment Confirmation
        </div>
    ";

    if (isset($_POST['submit'])) {
        $payMethod = $_POST['pay-method'];
        $resID = $_POST['resID'];

        if (makePayment($resID, $payMethod)) {
            echo "
                <div class='alert alert-success'>Payment Successfully Made!</div>
            </div>
            ";
        } else {
            echo "
                <div class='alert alert-danger'>Payment Failed!</div>
            </div>            
            ";
        }

    }    

?>


<?php include 'includes/footer.php';?>
