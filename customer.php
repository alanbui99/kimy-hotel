<?php include './includes/header.php';?>
<?php
    require('./utils/customer.utils.php');
    $id = htmlentities($_GET['id']);

    $info = getInfo($id);
    $stats = getStats($id);
    $activities = getActivities($id);
?>

<h1><span class="badge badge-pill badge-secondary">Customer Profile</span></h1>

<div class="mb-4">
    <div class="display-4 text-center mb-3"><?php echo $info['Fname'] .' '. $info['Lname']; ?></div>
    <div class="row d-flex justify-content-center">
        <div class="col-lg-3"></div>
        <div class="row col-lg-6 d-flex justify-content-center text-muted">
            <div class="mr-3"><i class="fas fa-id-badge mx-2"></i>ID: <?php echo $info['ID']; ?></div>
            <div class="mr-3"><i class="fas fa-mobile-alt mx-2"></i><?php echo $info['Phone']; ?></div>
            <div class="mr-3"><i class="fas fa-envelope mx-2"></i><?php echo $info['Email']; ?></div>

        </div>
        <div class="col-lg-3"></div>

    </div>
    
</div>
<div class="row container-fluid m-0">
    <div class="col-xl-5 mb-4">
        <div class="card p-0 ml-0">
            <div class="card-header lead">Stats</div>
            <div class="card-body">
                <div class="row d-flex justify-content-between">
                    <div class="card stat-card border-left-primary shadow m-2 ">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Number of stays</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['numStays']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card stat-card border-left-info shadow m-2 ">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Number of nights</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['numNights']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="row d-flex justify-content-between">
                    <div class="card stat-card border-left-success shadow m-2" >
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Revenue created</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $stats['revenue']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card stat-card border-left-warning shadow m-2" >
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Booking source</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['source']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
                
        </div>    
    </div>


    <div class="col-xl-7 mb-4">
        <div class="card">
            <div class="card-header lead">Activity stream</div>
            <div class="card-body">
                <?php foreach ($activities as $activity): ?>
                <div class="row">
                    <div class="col-auto text-center flex-column d-none d-sm-flex">
                        <div class="row h-50">
                            <div class="col border-right">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                        <h5 class="m-2">
                            <span class="badge badge-pill bg-<?php echo $activity['BsClass'] ?>">&nbsp;</span>
                        </h5>
                        <div class="row h-50">
                            <div class="col border-right">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                    </div>
                    <div class="col py-2">
                        <div class="card border-<?php echo $activity['BsClass'] ?> shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-12 card-text float-right order-2 mb-2"><span class="badge badge-pill badge-<?php echo $activity['BsClass'] ?>"><?php echo $activity['ResStatus'] ?></span></div>
                                    <div class="col-md-9 col-12 row card-title order-1 ml-1 mb-0 text-<?php echo $activity['BsClass'] ?>">
                                        <h6><?php echo date_format(date_create($activity['StartDate']), 'M d, Y') ?> - </h6>
                                        <h6><?php echo date_format(date_create($activity['EndDate']), 'M d, Y') ?> </h6>
                                    </div> 
                                </div>
                                
                                <button class="btn btn-sm btn-outline-secondary mb-3" type="button" data-target="#details-<?php echo $activity['ResID']?>" data-toggle="collapse">Details ▼</button>
                                <div class="collapse border" id="details-<?php echo $activity['ResID']?>">
                                    <div class="row p-2 text-xs">
                                        <div class="col-md-4">
                                            <div class="font-weight-bold mb-1"><u>Stay Info</u></div>
                                            <div><span class="font-weight-bold mr-1">Reservation ID:</span><span><?php echo $activity['ResID'] ?></span></div>
                                            <div><span class="font-weight-bold mr-1">Room no:</span><span><?php echo $activity['RoomNo'] ?></span></div>
                                            <div><span class="font-weight-bold mr-1">Number of guests:</span><span><?php echo $activity['NumGuests'] ?></span></div>
                                            <div><span class="font-weight-bold mr-1">Checked in at:</span><span><?php echo $activity['CheckinTime'] ?></span></div>
                                            <div><span class="font-weight-bold mr-1">Checked out at:</span><span><?php echo $activity['CheckoutTime'] ?></span></div>
                                            <br>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="font-weight-bold mb-1"><u>Payment Info</u></div>
                                            <div><span class="font-weight-bold mr-1">Bill value:</span><span><?php echo $activity['BillVal'] ?></span></div>
                                            <div><span class="font-weight-bold mr-1">Payment status:</span><span><?php echo $activity['PayStatus'] ?></span></div>
                                            <div><span class="font-weight-bold mr-1">Paid at:</span><span><?php echo $activity['PaidAt'] ?></div>
                                            <div><span class="font-weight-bold mr-1">Payment method:</span><span><?php echo $activity['PayMethod'] ?></span></div> 
                                            <br>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="font-weight-bold mb-1"><u>Booking Info</u></div>
                                            <div><span class="font-weight-bold mr-1">Booked at:</span><span><?php echo $activity['BookedAt'] ?></span></div>
                                            <div><span class="font-weight-bold mr-1">Booking source:</span><span><?php echo $activity['Source'] ?></span></div>                                            
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            
            </div>
        </div>
    </div>  
</div>  

</div>
<?php include './includes/footer.php';?>
