<?php
    require('config/db.php');

    function getInfo($id) {
        $query = "SELECT * FROM customers WHERE ID = $id;";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $info = $result->fetch_assoc();
        mysqli_free_result($result);

        return $info;
    }
    
    function getStats($id) {
        $query = "
        SELECT COUNT(DISTINCT r.ID) numStays, COUNT(*) numNights, ROUND(SUM(rt.Rate), 0) revenue
        FROM customers c, reservations r, occupancies o, rooms ro, roomtypes rt
        WHERE c.ID = r.CustomerID AND r.ID = o.ResID AND o.RoomNo = ro.Number AND ro.TypeCode = rt.Code
        AND c.ID = $id; 
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        $stats = $result->fetch_assoc();
        mysqli_free_result($result);

        $srcQuery = "
        SELECT Source
        FROM reservations
        WHERE customerID = $id
        GROUP BY Source
        ORDER BY COUNT(Source) DESC
        LIMIT 1;
        ";
        $srcResult = mysqli_query($GLOBALS['conn'], $srcQuery);
        $stats['source'] = $srcResult->fetch_assoc()['Source'];
        mysqli_free_result($srcResult);

        return $stats;
    }

    function getActivities($id) {

        $bsClasses = [
            'confirmed' => 'primary',
            'checked-in' => 'success',
            'checked-out' => 'secondary',
            'no-show' => 'danger'
        ];
        $query = "
        SELECT *, r.Status ResStatus, b.Status PayStatus, ROUND(SUM(rt.Rate), 0) BillVal
        FROM reservations r
        JOIN occupancies o ON r.ID = o.ResID
        JOIN rooms ro ON o.RoomNo = ro.Number
        JOIN roomtypes rt ON ro.TypeCode = rt.Code 
        LEFT JOIN bills b ON r.ID = b.resID
        WHERE r.CustomerID = $id
        GROUP BY r.ID
        ORDER BY o.Date DESC;    
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            $row['BsClass'] = $bsClasses[$row['ResStatus']];
            $activities[] = $row;

        }
        mysqli_free_result($result);

        return $activities;
    }
?>