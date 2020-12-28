<?php
    require('config/db.php');
    /* Today's Stats */
    function getTodayNumRooms() {
        $numRooms = [];
        $occupiedQuery = "SELECT COUNT(*) occupied FROM occupancies WHERE Date = CURRENT_DATE();";
        $occupiedResult = mysqli_query($GLOBALS['conn'], $occupiedQuery);
        $numRooms['occupied'] = $occupiedResult->fetch_assoc()['occupied'];
        mysqli_free_result($occupiedResult);

        $totalQuery = "SELECT COUNT(*) total FROM rooms";
        $totalResult = mysqli_query($GLOBALS['conn'], $totalQuery);
        $numRooms['total'] = $totalResult->fetch_assoc()['total'];
        mysqli_free_result($totalResult);

        return $numRooms;
    }

    function getTodayCheckIns() {
        $numCheckIns = [];
        $expectedQuery = "SELECT COUNT(*) expected FROM reservations WHERE StartDate = CURDATE();";
        $expectedResult = mysqli_query($GLOBALS['conn'], $expectedQuery);
        $numCheckIns['expected'] = $expectedResult->fetch_assoc()['expected'];
        mysqli_free_result($expectedResult);

        $madeQuery = "SELECT COUNT(*) made FROM reservations WHERE StartDate = CURDATE() AND Status = 'checked-in';";
        $madeResult = mysqli_query($GLOBALS['conn'], $madeQuery);
        $numCheckIns['made'] = $madeResult->fetch_assoc()['made'];
        mysqli_free_result($madeResult);

        return $numCheckIns;
    }

    function getTodayCheckOuts() {
        $numCheckOuts = [];
        $expectedQuery = "SELECT COUNT(*) expected FROM reservations WHERE EndDate = CURDATE()";
        $expectedResult = mysqli_query($GLOBALS['conn'], $expectedQuery);
        $numCheckOuts['expected'] = $expectedResult->fetch_assoc()['expected'];
        mysqli_free_result($expectedResult);

        $madeQuery = "SELECT COUNT(*) made FROM reservations WHERE EndDate = CURDATE() AND Status = 'checked-out';";
        $madeResult = mysqli_query($GLOBALS['conn'], $madeQuery);
        $numCheckOuts['made'] = $madeResult->fetch_assoc()['made'];
        mysqli_free_result($madeResult);

        return $numCheckOuts;
    }

    function getTodayRevenue() {
        $query = "
        SELECT ROUND(SUM(rt.Rate), 0) revenue
        FROM occupancies o, roomtypes rt, rooms r
        WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code
        AND o.Date = CURRENT_DATE();
        ";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $revenue = $result->fetch_assoc()['revenue'];
        mysqli_free_result($result);

        return $revenue;
    }

    /* Occupancy Line Chart*/

    function getOccupancyChartData() {
        $timestamp = time() - 24 * 3600 * 7;
        for ($i = 0 ; $i < 11 ; $i++) {
            $days[] = date('Y-m-d', $timestamp);
            $timestamp += 24 * 3600;
        }

        foreach ($days as $day) {
            $query = "SELECT COUNT(*) occupied, Date date FROM occupancies WHERE Date = Date('$day');";
            $result = mysqli_query($GLOBALS['conn'], $query);
            $data[] = $result->fetch_assoc();
            mysqli_free_result($result);
        }

        return $data;
    }

    /* Booking Sources Pie Chart*/

    function getSourcesChartData() {
        $data = [];
        $bgColors = [
            'OTA' => '#007bff',
            'direct' => '#1cc88a',
            'corporate' => '#36b9cc',
            'agency' => '#6c757d'
        ];

        $hbgColors = [
            'OTA' => '#2e59d9',
            'direct' => '#17a673',
            'corporate' => '#2c9faf',
            'agency' => '#343a40'
        ];

        $query = "
        SELECT DISTINCT Source, COUNT(*) count 
        FROM reservations
        GROUP BY Source
        ORDER BY Source DESC
        ";
        $result = mysqli_query($GLOBALS['conn'], $query);
        while ($row = $result->fetch_assoc()) {
            $row['bgColor'] = $bgColors[$row['Source']];
            $row['hbgColor'] = $hbgColors[$row['Source']];
            $data[] = $row;
        }
        mysqli_free_result($result);
        
        return $data;
    }

    function getTotalBookings() {
        $query = "SELECT COUNT(*) total FROM reservations;";
        $result = mysqli_query($GLOBALS['conn'], $query);
        $total = $result->fetch_assoc()['total'];
        mysqli_free_result($result);

        return $total;
    }

?>