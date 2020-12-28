<?php 
    require('utils/res.utils.php');
    session_start();
    $availRooms = $_SESSION['availRooms'];
    $startDate = $_SESSION['startDate'];
    $endDate = $_SESSION['endDate'];
    $roomType = $_SESSION['roomType'];

    $sources = getBookingSources();
    $roomNo = htmlentities($_POST['room']);

    if (isset($_POST['submit'])) {
        $fname = htmlentities($_POST['first-name']);
        $lname = htmlentities($_POST['last-name']);
        $phone = htmlentities($_POST['phone-number']);
        $email = htmlentities($_POST['email']);
        $numGuests = htmlentities($_POST['num-guests']);
        $source = htmlentities($_POST['source']);
        $roomNo = htmlentities($_POST['room-no']);

        $customer = checkCustomerExistence($fname, $lname);
        $customerID = $customer ? $customer['ID'] : registerCustomer($fname, $lname, $phone, $email);
        $newResID = makeReservation($customerID, $startDate, $endDate, $numGuests, $roomNo, $source);
        session_destroy();
    }
?>

<?php include 'includes/header.php';?>
    <h1><span class="badge badge-pill badge-secondary">Room Booking</span></h1>

    <?php if (isset($_POST['submit'])): ?>
        <div class="container">
        <?php if ($newResID): ?>
            <div class="alert alert-success text-center"> 
                <b><?php echo $fname. ' ' .$lname?></b>
                has made reservation <b><?php echo $newResID ?></b> for room <b><?php echo $_POST['room-no']?></b>
                between <i><?php echo $_POST['start-date']?></i> and <i><?php echo $_POST['end-date']?></i>.
            </div>
        <?php else: ?>
            <div class="alert alert-danger text-center">Reservation failed!</div>
        <?php endif; ?>
        </div>
    
    
    <?php elseif ($_POST): ?>
        <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="card-deck d-flex justify-content-center">
                <div class="card">
                    <h5 class="card-header lead"><i class="fas fa-user-plus mr-2"></i>Customer Information</h5>
                    <div class="card-body">
                        <div class="form-group">
                            <label>First Name</label>
                            <div class="res-search-box">
                                <input class="form-control" type="text" name="first-name" required onkeyup="suggestNames(event, this.value, 'suggestedNames1')">
                                <div class="res-suggestion-box">
                                    <ul id="suggestedNames1" class="list-group"></ul>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <div class="res-search-box">
                                <input class="form-control" type="text" name="last-name" required onkeyup="suggestNames(event, this.value, 'suggestedNames2')">
                                <div class="res-suggestion-box">
                                    <ul id="suggestedNames2" class="list-group"></ul>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input class="form-control" type="tel" name="phone-number" 
                            pattern="[0-9]{3}[0-9]{3}[0-9]{4}" minlength="10" maxlength="10" required>
                            <small>Format: 1234567890</small>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" type="email" name="email">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h5 class="card-header lead"><i class="fas fa-calendar-plus mr-2"></i>Reservation Information</h5>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Room type</label>
                            <input class="form-control" type="text" name="room-type" value="<?php echo $roomType ?>" readonly="readonly" required>
                        </div>
                        <div class="form-group">
                            <label>Room number</label>
                            <input class="form-control" type="text" name="room-no" value="<?php echo $roomNo ?>" readonly="readonly" required>
                        </div>
                        <div class="form-group">
                            <label>Check In Date</label>
                            <input class="form-control" type="date" name="start-date" value="<?php echo $startDate ?>" readonly="readonly" required>
                        </div>
                        <div class="form-group">
                            <label>Check Out Date</label>
                            <input class="form-control" type="date" name="end-date" value="<?php echo $endDate ?>" readonly="readonly" required>
                        </div>
                        <div class="form-group">
                            <label>Number of guests</label>
                            <input class="form-control" type="number" name="num-guests">
                        </div>
                        <div class="form-group">
                            <label>Booking source</label>
                            <select class="form-control" name="source" required >
                                <?php foreach ($sources as $idx=>$source) : ?>
                                    <option value="<?php echo $source?>" <?php echo $idx == 0 ? 'selected' : ''?>><?php echo ucfirst($source) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>                    
                </div>

            </div>
        
            <input class="btn btn-primary btn-lg my-2" style="width: 100%;" type="submit" name="submit" value="Book">
        </form>
    
    <?php else: ?>
        <div class="alert alert-danger text-center">No information received!</div>
    <?php endif; ?>


    <script type="text/javascript">
        document.addEventListener("keyup", e => {
            if (e.key === 'Escape') {
                document.getElementById('suggestedNames1').innerHTML = '';
                document.getElementById('suggestedNames2').innerHTML = '';            
            }
        })
        
        function suggestNames(event, val, targetID) {
            document.getElementById('suggestedNames1').innerHTML = '';
            document.getElementById('suggestedNames2').innerHTML = '';
            if (event.key !== 'Escape' && val.length > 2) fetchNames(val, targetID);
        }

        function fetchNames(val, targetID) {
            fetch(`utils/name-suggestion.utils.php?q=${val}`, {method: 'GET'})
            .then(response => response.json())
            .then(data => {
                viewNames(data, targetID)
            })
            .catch(e => console.log(e))
        }

        function viewNames(names, targetID) {
            const dataViewer = document.getElementById(targetID);
            for (let i = 0; i < names.length; i++) {
                const li = document.createElement("li");
                li.innerHTML = `${names[i]['Fname']} ${names[i]['Lname']}`;
                li.id = i;
                li.classList.add('suggested-item')
                li.classList.add('list-group-item');
                li.classList.add('list-group-item-action');
                li.addEventListener('click', event => {
                    const customer = names[event.target.id];
                    document.querySelector('input[name="first-name"]').value = customer['Fname'];
                    document.querySelector('input[name="last-name"]').value = customer['Lname'];
                    document.querySelector('input[name="phone-number"]').value = customer['Phone'];
                    document.querySelector('input[name="email"]').value = customer['Email'];
                    dataViewer.innerHTML = '';
                })
                dataViewer.appendChild(li);
            }
        }
    </script>
        

<?php include 'includes/footer.php';?>
