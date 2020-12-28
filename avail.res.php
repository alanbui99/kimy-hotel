<?php 
    require('utils/res.utils.php');

    $types = getRoomTypes();
    $msg = '';
    $availRooms = [];
    $searched = false;

    if (isset($_POST['submit'])) {
        $today = date("Y-m-d");
        
        if ($_POST['start-date'] < $today or $_POST['end-date'] < $today) {
            $msg = 'Check-in and check-out dates must be in the future';
            $msgClass = 'alert-danger';
        } 
        else if ($_POST['start-date'] >= $_POST['end-date']) {
            $msg = 'Check-out date must be after check-in date';
            $msgClass = 'alert-danger';
        }
        else {
            $availRooms = checkAvailability($_POST['room-type'], $_POST['start-date'], $_POST['end-date']);
            $searched = true;
            
            //store all input data in session for next steps
            session_start();
            $_SESSION['roomType'] = htmlentities($_POST['room-type']);
            $_SESSION['startDate'] = htmlentities($_POST['start-date']);
            $_SESSION['endDate'] = htmlentities($_POST['end-date']);
            $_SESSION['availRooms'] = $availRooms;
        }
    }
?>
<?php include 'includes/header.php';?>

    <h1 class="mb-4"><span class="badge badge-pill badge-secondary">Room Availability</span></h1>

    <?php if($searched): ?>
        <div class="card mb-4">
            <div class="card-header lead">
                <i class="fas fa-bed mr-1"></i>
                Available rooms
            </div>
            <div class="card-body">
                <div class="row justify-content-around">
                <?php if(count($availRooms) > 0): ?>
                    <?php foreach ($availRooms as $room) : ?>
                        <form method="POST" action="./book.res.php">
                            <input type="hidden" name="room" value="<?php echo $room?>">
                            <button type="submit" class="btn btn-success"><?php echo $room?></button>
                        </form>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="alert alert-danger">No available <?php echo $_POST['room-type'] ?> room for this period</div>
                <?php endif; ?>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row d-flex" >
        <div class="col-xl-4">
            <?php if($msg != ''): ?>
                <div class="alert <?php echo $msgClass ?>"><?php echo $msg ?></div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header lead"><i class="fas fa-search mr-1"></i>Search room availability</div>
                <div class="card-body">
                    <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
                        <div class="form-group">
                            <label>Room Type</label>
                            <select class="form-control" name="room-type" id="room-type" required >
                                <?php foreach ($types as $type) : ?>
                                    <option value="<?php echo $type?>" selected><?php echo ucfirst($type) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Check-in Date</label>
                            <input class="form-control" type="date" name="start-date" 
                            value="<?php echo $_POST['start-date']?>" required>
                        </div>
                        <div class="form-group">
                            <label>Check-out Date</label>
                            <input class="form-control" type="date" name="end-date" 
                            value="<?php echo $_POST['end-date']?>" required>
                        </div>
                        <input class="btn btn-primary" style="width: 100%;" type="submit" name="submit" value="Search">
                    </form>                    
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header lead d-flex justify-content-between">
                    <p><i class="fas fa-calendar-alt mr-1"></i>Room schedule</p>
                    
                    <form class="form-inline">
                        <input type="date" class="form-control mb-2 mr-sm-2" 
                        value="<?= date('Y-m-d', time()); ?>" onchange="getSchedule(this.value)">
                    </form>
                </div>
                <div class="card-body">
                    <div id="room-schedule"></div>
                    <div class="small row">
                        <div class="col-md-2"></div>
                        <div class="col-sm-12 col-md-4 d-flex justify-content-around">
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> Available
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-warning"></i> Reserved
                            </span>
                        </div>
                        <div class="col-sm-12 col-md-4 d-flex justify-content-around">
                            <span class="mr-2">
                                <i class="fas fa-circle text-danger"></i> Occupied
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-secondary"></i> Completed
                            </span>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

<script type="text/javascript">
    <?php if (isset($_POST['submit'])):?>
        document.getElementById('room-type').value = "<?php echo $_POST['room-type']?>";
    <?php endif; ?>

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
<?php include 'includes/footer.php';?>
