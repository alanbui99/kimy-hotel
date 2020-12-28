<?php
    require('../config/db.php');

    $q = $_REQUEST['q'];

    $data;
    if ($q != '') {
        $data = $q == 'month' ? getDataByMonth() : getDataByDay();
        echo json_encode($data);
    }



    function getDataByDay() {
        $days = [];
        $timestamp = time() - 24 * 3600 * 13;
        for ($i = 0 ; $i < 21 ; $i++) {
            $days[] = date('Y-m-d', $timestamp);
            $timestamp += 24 * 3600;
        }

        foreach ($days as $day) {
            $query = "SELECT  Date period, COUNT(*) value FROM occupancies WHERE Date = '$day';";
            $result = mysqli_query($GLOBALS['conn'], $query);
            $data[] = $result->fetch_assoc();
            mysqli_free_result($result);
        }
        
        return $data;
    }

    function getDataByMonth() {
        $months = [];

        for ($j = 2; $j > 0; $j--) {
            $months[] = date("Y-m", strtotime( date( 'Y-m-01' )." +$j months"));
        }

        for ($i = 0; $i <= 11; $i++) {
            $months[] = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
        }

        $months = array_reverse($months);
        foreach ($months as $month) {
            $query = "
            SELECT Date period, ROUND(COUNT(*)/DAY(LAST_DAY('$month-01')), 1) value 
            FROM occupancies 
            WHERE Date LIKE '$month%';
            ";
            $result = mysqli_query($GLOBALS['conn'], $query);
            $data[] = $result->fetch_assoc();
            mysqli_free_result($result);
        }

        return $data;
    }
    
?>