<?php
    require('config/db.php');

    function getRoomsData() {
        $bsClasses = [
            'confirmed' => 'warning',
            'checked-in' => 'success',
            'no-show warning' => 'danger',
            'checked-out' => 'secondary'
        ];

        $query = "
        SELECT r.CustomerID, RoomNo 'Room no', Fname 'First name', Lname 'Last name', NumGuests 'No. of guests', StartDate 'Start date', EndDate 'End date', r.Status
        FROM occupancies o, reservations r, customers c
        WHERE o.ResID = r.ID AND r.CustomerID = c.ID 
        AND o.Date = CURRENT_DATE()
        ORDER BY o.RoomNo;
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            if ($row['Status'] == 'confirmed' && $row['Start date'] < date('Y-m-d')) {
                $row['Status'] = 'no-show warning';
            }
            $row['BsClass'] = $bsClasses[$row['Status']];
            $data[] = $row;
        }
        mysqli_free_result($result);

        return $data;
    }

    function getCheckinsData() {
        $bsClasses = [
            'confirmed' => 'warning',
            'checked-in' => 'success',
            'checked-out' => 'secondary'
        ];

        $query = "
        SELECT r.CustomerID, RoomNo 'Room no', Fname 'First name', Lname 'Last name', StartDate 'Start date', EndDate 'End date', r.Status
        FROM occupancies o, reservations r, customers c
        WHERE o.ResID = r.ID AND r.CustomerID = c.ID 
        AND r.StartDate = CURRENT_DATE()
        GROUP BY r.ID
        ORDER BY o.RoomNo;
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            $row['BsClass'] = $bsClasses[$row['Status']];
            $data[] = $row;
        }
        mysqli_free_result($result);
        
        return $data;
    }

    function getCheckoutsData() {
        $bsClasses = [
            'confirmed' => 'danger',
            'checked-in' => 'secondary',
            'checked-out' => 'success'
        ];

        $query = "
        SELECT r.CustomerID, RoomNo 'Room no', Fname 'First name', Lname 'Last name', StartDate 'Start date', EndDate 'End date', r.Status
        FROM occupancies o, reservations r, customers c
        WHERE o.ResID = r.ID AND r.CustomerID = c.ID 
        AND r.EndDate = CURRENT_DATE()
        GROUP BY r.ID
        ORDER BY o.RoomNo;
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            $row['BsClass'] = $bsClasses[$row['Status']];
            $data[] = $row;
        }
        mysqli_free_result($result);
        
        return $data;
    }
    
    function getRevenueData() {
        $revStatuses = [
            'initiated' => 'accrued',
            'completed' => 'received', 
            '' => 'scheduled'
        ];

        $bsClasses = [
            'accrued' => 'warning',
            'received' => 'success',
            'scheduled' => 'secondary'
        ];

        $query = "
        SELECT r.CustomerID, o.RoomNo 'room number', c.Fname 'first name', c.Lname 'last name', ROUND(rt.Rate, 0) 'amount', b.Status 'status' 
        FROM occupancies o
        JOIN reservations r ON o.ResID = r.ID
        JOIN customers c ON r.CustomerID = c.ID
        JOIN rooms ro ON ro.Number = o.RoomNo
        JOIN roomtypes rt ON ro.TypeCode = rt.Code
        LEFT JOIN bills b ON b.ResID = o.ResID 
        WHERE Date = CURRENT_DATE();
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            $row['status'] = $revStatuses[$row['status']];
            $row['BsClass'] = $bsClasses[$row['status']];
            $row['amount'] = '$'.$row['amount'];
            $data[] = $row;
        }
        mysqli_free_result($result);
        
        return $data;
    }

    function getRevByStatus() {
        $revStatuses = [
            'initiated' => 'accrued',
            'completed' => 'received', 
            '' => 'scheduled'
        ];

        $bgColors = [
            'scheduled' => '#6c757d',
            'received' => '#28a745',
            'accrued' => '#ffc107'
        ];

        $hbgColors = [
            'scheduled' => '#343a40',
            'received' => '#17a673',
            'accrued' => '#ffc107'
        ];

        $query = "
        SELECT b.Status 'status', ROUND(SUM(rt.Rate), 0) 'value'
        FROM occupancies o
        JOIN rooms ro ON ro.Number = o.RoomNo
        JOIN roomtypes rt ON ro.TypeCode = rt.Code
        LEFT JOIN bills b ON b.ResID = o.ResID 
        WHERE o.Date = CURRENT_DATE()
        GROUP BY b.Status
        ORDER BY b.Status;
        ";

        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            $row['status'] = $revStatuses[$row['status']];
            $row['bgColor'] = $bgColors[$row['status']];
            $row['hbgColor'] = $hbgColors[$row['status']];
            $data[] = $row;
        }
        mysqli_free_result($result);
        return $data;
    }
?>