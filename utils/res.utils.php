<?php
    require('config/db.php');

    function checkAvailability($type, $start, $end) {
        $period = getActualPeriod($start, $end);
        $periodParam = '';
        foreach ($period as $night) {
            $periodParam .= "'" .$night->format("Y-m-d"). "',";
        }
        $periodParam = trim($periodParam, ', ');

        //find room availability given a range of dates
        $query = "
        SELECT ro.Number
        FROM rooms ro, roomtypes rt
        WHERE ro.TypeCode = rt.Code
        AND rt.Style = '$type'
        AND ro.Number NOT IN (
            SELECT r.Number
            FROM occupancies o, rooms r
            WHERE o.RoomNo = r.Number
            AND o.Date IN ($periodParam));
        ";

        
        $availableRooms = [];
        $result = mysqli_query($GLOBALS['conn'], $query);

        while ($row = $result->fetch_assoc()) {
            $availableRooms[] = $row['Number'];
        }

        mysqli_free_result($result);

        
        return $availableRooms;
    }

    function checkCustomerExistence($firstName, $lastName) {
        $firstName = ucwords($firstName);
        $lastName = ucwords($lastName);
        $query = "SELECT ID FROM customers WHERE Fname = '$firstName' AND Lname = '$lastName'";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $customer = $result->fetch_assoc();
        mysqli_free_result($result);
        return $customer;
    }

    function registerCustomer($firstName, $lastName, $phone, $email) {
        $firstName = ucwords($firstName);
        $lastName = ucwords($lastName);

        $query = $email ? "INSERT INTO customers (Fname, Lname, Phone, Email)
        VALUES ('$firstName', '$lastName', '$phone', '$email')"
        : "INSERT INTO customers (Fname, Lname, Phone)
        VALUES ('$firstName', '$lastName', '$phone')";
        
        if (mysqli_query($GLOBALS['conn'], $query)) {
            $last_id = mysqli_insert_id($GLOBALS['conn']);
            return $last_id;
        } 
        
        echo "Error: " .mysqli_error($GLOBALS['conn']);
    }

    function makeReservation($cusID, $start, $end, $numGuests, $roomNo, $source) {
        $query = $numGuests ? "INSERT INTO reservations (StartDate, EndDate, NumGuests, CustomerID, Source)
        VALUES ('$start', '$end', $numGuests, $cusID, '$source')"
        : "INSERT INTO reservations (StartDate, EndDate, CustomerID, Source)
        VALUES ('$start', '$end', $cusID, '$source')";

        if (mysqli_query($GLOBALS['conn'], $query)) {
            $resID = mysqli_insert_id($GLOBALS['conn']);
            $period = getActualPeriod($start, $end);
            foreach ($period as $night) {
                $date = $night->format("Y-m-d");
                createOccupancy($roomNo, $date, $resID);
            }
            return $resID; 
        }
        echo "Error: " .mysqli_error($GLOBALS['conn']);
    }

    function createOccupancy($roomNo, $date, $resID) {
        $query = "INSERT INTO occupancies (RoomNo, Date, ResID) VALUES ($roomNo, '$date', $resID)";
        if (!mysqli_query($GLOBALS['conn'], $query)) {
            echo "Error: " .mysqli_error($GLOBALS['conn']);
        }
    }

    function getRoomTypes() {
        $query = 'SELECT Style FROM roomtypes';
        $result = mysqli_query($GLOBALS['conn'], $query);
        $types = [];

        while ($row = $result->fetch_assoc()) {
            $types[] = $row['Style'];
        }

        mysqli_free_result($result);

        return $types;
    }

    function getBookingSources() {
        $sources = [];
        $query = "SELECT DISTINCT Source FROM reservations;";
        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            $sources[] = $row['Source'];
        }
        mysqli_free_result($result);

        return $sources;
    }

    function getActualPeriod($start, $end) {
        $start = new DateTime($start);
        $end = new DateTime($end);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);

        return $period;
    }

?>