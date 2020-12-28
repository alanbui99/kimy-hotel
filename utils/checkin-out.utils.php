<?php
    require('config/db.php');

    function searchRes($firstName, $lastName) {
        $reservations = [];
        //find customer reservations at check-in/out
        $query = "
        SELECT r.ID, r.StartDate, r.EndDate, r.BookedAt, r.NumGuests, o.RoomNo, r.Status
        FROM customers c, reservations r, occupancies o
        WHERE c.ID = r.CustomerID AND o.ResID = r.ID
        AND Fname = '$firstName' AND Lname='$lastName'
        AND EndDate >= CURDATE()
        AND r.Status NOT IN ('checked-out', 'canceled') 
        GROUP BY r.ID;
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);

        while ($row = $result->fetch_assoc()) {
            $reservations[] = $row;
        }

        mysqli_free_result($result);
        return $reservations;
    }

    function processCheckIn($resID) {
        //update reservation
        $resQuery = "UPDATE reservations SET Status = 'checked-in', CheckinTime = NOW() WHERE ID=$resID";

        if (mysqli_query($GLOBALS['conn'], $resQuery)) {
            //update occupancy
            $occQuery = "UPDATE occupancies SET Status = 'in progress' WHERE ResID=$resID";
            if (mysqli_query($GLOBALS['conn'], $occQuery) and initBill($resID)) {
                return true;
            }
        } else {
            echo "Error updating reservation: " . mysqli_error($GLOBALS['conn']);
        }
    }

    function processCheckOut($resID) {
        //update reservation
        $resQuery = "UPDATE reservations SET Status = 'checked-out', CheckoutTime = NOW() WHERE ID=$resID";
        if (mysqli_query($GLOBALS['conn'], $resQuery)) {
            //update occupancy
            $occQuery = "UPDATE occupancies SET Status = 'finished' WHERE ResID=$resID";
            if (mysqli_query($GLOBALS['conn'], $occQuery)) {
                return true;
            }
        } else {
            echo "Error updating reservation: " . mysqli_error($GLOBALS['conn']);
        }
    }

    function initBill($resID) {
        // get amount
        $amount = getAmount($resID);
        $cusID = getCusID($resID);
        $query = "INSERT INTO bills(Amount, ResID) VALUES($amount, $resID)";
        if (mysqli_query($GLOBALS['conn'], $query)) {
            return true;
        }       
    }

    function getBillDetails($resID) {
        $details = [];
        $items = [];
        $query = "
        SELECT o.ResID ,o.RoomNo, rt.Style, COUNT(*) NumNights, rt.Rate 
        FROM occupancies o, rooms r, roomtypes rt
        WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code
        AND o.ResID = $resID
        GROUP BY rt.Code;        
        ";

        $result = mysqli_query($GLOBALS['conn'], $query); 
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        mysqli_free_result($result);

        $details['items'] = $items;

        $totalQuery = "SELECT Amount FROM bills WHERE ResID = $resID";
        $totalResult = mysqli_query($GLOBALS['conn'], $totalQuery);
        $total = $totalResult->fetch_assoc();
        mysqli_free_result($totalResult);

        $details['total'] = $total['Amount'];

        return $details;
    }

    function makePayment($resID, $payMethod) {
        $query = "UPDATE bills SET Status = 'completed', PaidAt = NOW(), PayMethod = '$payMethod' WHERE ResID=$resID";
        if (mysqli_query($GLOBALS['conn'], $query)) {
            return true;
        }
        echo "Error making payment: " . mysqli_error($GLOBALS['conn']);

    }

    function getAmount($resID) {
        $query = "
        SELECT SUM(rt.Rate) AS amount
        FROM occupancies o, rooms r, roomtypes rt
        WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code
        AND o.ResID = $resID;
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        $row = $result->fetch_assoc();
        mysqli_free_result($result);
        $amount = $row['amount'];

        return $amount;
    }

    function getCusID($resID) {
        $query = "SELECT r.ID FROM customers c, reservations r WHERE c.ID = r.CustomerID AND r.ID = $resID";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $cus = $result->fetch_assoc();
        mysqli_free_result($result);

        return $cus['ID'];
    }






?>