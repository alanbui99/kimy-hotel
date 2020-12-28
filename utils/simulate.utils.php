<?php
    require('config/db.php');
    require('utils/checkin-out.utils.php');
    require('utils/res.utils.php');
    require_once 'vendor/autoload.php';

    function massCheckIn($time) {
        $sign = $time == 'today' ? '=' : '<';
        $query = "
        SELECT ID FROM reservations
        WHERE StartDate $sign CURRENT_DATE() AND Status = 'confirmed';        
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        $attempted = 0;
        $succeeded = 0;
        while ($row = $result->fetch_assoc()) {
            $attempted++;
            if (processCheckIn($row['ID'])) {
                $succeeded++;
            } else {
                echo "Error:" . mysqli_error($GLOBALS['conn']);
            }
        }
        mysqli_free_result($result);
        echo "<div class='alert alert-success'>Checked in <b>$succeeded/$attempted</b> customers</div>";
    }

    function massCheckOut($option) {
        $sign = $option == 'today' ? '=' : '<';
        $query = "
        SELECT ID FROM reservations
        WHERE EndDate $sign CURRENT_DATE() AND Status = 'checked-in';        
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        $attempted = 0;
        $succeeded = 0;
        $payMethods = array('cash', 'credit', 'debit','credit', 'debit');

        while ($row = $result->fetch_assoc()) {
            $attempted++;
            $rand_key = array_rand($payMethods, 2)[0];
            if (processCheckOut($row['ID']) && makePayment($row['ID'], $payMethods[$rand_key])) {
                $succeeded++;
            } else {
                echo "Error: " . mysqli_error($GLOBALS['conn']);
            }
        }
        mysqli_free_result($result);
        echo "<div class='alert alert-success'>Checked out <b>$succeeded/$attempted</b> customers</div>";
    }

    function massBook($option) {
        $attempted = rand(1, 10);
        $succeeded = 0;
        foreach (range(0, $attempted - 1) as $_) {
            if (book($option)) $succeeded++;
        }
        echo "<div class='alert alert-success'>Booked <b>$succeeded/$attempted</b> room(s)</div>";
    }

    function book($option) {
        $faker = Faker\Factory::create();
        $timeMark = $option == 'soon' ? $faker->dateTimeBetween('0 day', '+1 week') : $faker->dateTimeBetween('+1 month', '+3 months');
        $startDT = clone $timeMark;
        $len = rand(1, 7);
        $endDT = $timeMark->add(new DateInterval('P' .$len. 'D'));
        
        $startDate = $startDT->format('Y-m-d');
        $endDate = $endDT->format('Y-m-d');
        $query = "
        SELECT ro.Number
        FROM rooms ro
        WHERE ro.Number NOT IN (
            SELECT r.Number
            FROM occupancies o, rooms r
            WHERE o.RoomNo = r.Number
            AND o.Date BETWEEN '$startDate' AND DATE_SUB('$endDate', INTERVAL 1 DAY) 
        );
        ";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row['Number'];
        }
        mysqli_free_result($result);

        if (count($rooms) == 0) return;

        $room = $rooms[rand(0, count($rooms) - 1)];
        $firstName = $faker->firstName();
        $lastName = $faker->lastName();
        $existedCusID = checkCustomerExistence($firstName, $lastName);
        $sources = ['direct', 'OTA','direct', 'OTA', 'corporate', 'agency'];

        if ($existedCusID) {
            $resID = makeReservation($existedCusID, $startDate, $endDate, rand(1, 5), $room, $sources[rand(0, count($sources) - 1)]);
            return $resID;
        }

        $phone = $faker->phoneNumber();
        $newCusID = registerCustomer($firstName, $lastName, $phone, "$firstName$lastName@gmail.com");
        $resID = makeReservation($newCusID, $startDate, $endDate, rand(1, 5), $room, $sources[rand(0, count($sources) - 1)]);
        return $resID;
    }

?>