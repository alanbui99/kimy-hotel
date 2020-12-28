<?php
    require('config/db.php');
    /* Room Report */
    function getRoomTypeData() {
        $data = [];
        $colors = [
            'business suite' => '#007bff',
            'deluxe' => '#dc3545',
            'family suite' => '#ffc107',
            'standard' => '#28a745'
        ];
        
        $query = "
        SELECT rt.Style style, COUNT(*) count
        FROM occupancies o, rooms r, roomtypes rt
        WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code
        GROUP BY rt.Style
        ORDER BY rt.Style;
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            $row['color'] = $colors[$row['style']];
            $data[] = $row;
        }
        mysqli_free_result($result);

        return $data;        
    } 

    function getTotalRoomsSold() {
        $query = "SELECT FORMAT(COUNT(*),0) total FROM occupancies;";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $total = $result->fetch_assoc()['total'];
        mysqli_free_result($result);
        return $total;
    }

    /* Customer Report */

    function getRevPerCustomer() {
        $query = "
        SELECT ROUND(SUM(rt.Rate)/(SELECT COUNT(*) FROM customers), 0) AS revPerCus
        FROM occupancies o, rooms r, roomtypes rt
        WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code
        ";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $data = $result->fetch_assoc()['revPerCus'];
        mysqli_free_result($result);

        return $data;
    }

    function getStaysPerCustomer() {
        $query = "SELECT ROUND((SELECT COUNT(*) FROM reservations)/(SELECT COUNT(*) FROM customers), 2) staysPerCus;";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $data = $result->fetch_assoc()['staysPerCus'];
        mysqli_free_result($result);

        return $data;
    }

    function getLenPerStay() {
        $query = "SELECT ROUND((SELECT COUNT(*) FROM occupancies)/(SELECT COUNT(*) FROM reservations), 2) lenPerStay;";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $data = $result->fetch_assoc()['lenPerStay'];
        mysqli_free_result($result);

        return $data;
    }

    function getReturnCusRate() {
        $query1 = "SELECT COUNT(*) 'return' FROM customers WHERE ID IN (SELECT CustomerID FROM reservations GROUP BY CustomerID HAVING COUNT(*) > 1) ;";
        $result1 = mysqli_query($GLOBALS['conn'], $query1);
        $return = $result1->fetch_assoc()['return'];
        mysqli_free_result($result1);

        $query2 = "SELECT COUNT(*) total FROM customers;";
        $result2 = mysqli_query($GLOBALS['conn'], $query2);
        $total = $result2->fetch_assoc()['total'];
        mysqli_free_result($result2);
        return number_format($return/$total, 4, '.', ',')*100;

    }

    function getAllCustomers() {
        $query = "
        SELECT c.ID 'ID', c.Fname 'First name', c.Lname 'Last name', COUNT(DISTINCT(r.ID)) 'Stays', SUM(rt.Rate) 'Revenue created'
        FROM customers c, reservations r, occupancies o, rooms ro, roomtypes rt
        WHERE c.ID = r.CustomerID AND r.ID = o.ResID AND o.RoomNo = ro.Number AND ro.TypeCode = rt.Code
        GROUP BY c.ID;                
        ";
        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        mysqli_free_result($result);

        return $data;
    }

    /* Revenue Report */
    function getTotalRev() {
        $query = "
        SELECT FORMAT(SUM(rt.Rate),0) totalRev
        FROM occupancies o, rooms r, roomtypes rt
        WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code
        ";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $data = $result->fetch_assoc()['totalRev'];
        mysqli_free_result($result);
        
        return $data;
    }



?>