<?php
    require('../config/db.php');

    $q = $_REQUEST['q'];
    $by = $_REQUEST['by'];

    $data;
    if ($q != '' && $by != '') {
        $data = getData($q, $by);
        echo json_encode($data);
        // echo $q;
    }

    function getData($q, $by) {
        $periods = $by == 'month' ? getMonths() : getDays();
        switch ($q) {
            case 'total':
                $values = getTotalRev($periods);
                break;
            case 'per-room':
                $values = getRevPerRoom($periods);
                break;
            case 'per-stay':
                $values = getRevPerStay($periods);
                break;
        };

        // $periods = $by == 'month' ? array_map(function($x) {return date_format(date_create($x), "M");}, $periods) 
        // : array_map(function($x) {return date_format(date_create($x), "M d");}, $periods);

        $data['periods'] = $periods;
        $data['values'] = $values;

        return $data;
    }

    function getTotalRev($periods) {
        $values = [];
        foreach($periods as $p) {
            $query ="
            SELECT ROUND(SUM(rt.Rate), 0) sum
            FROM occupancies o, rooms r, roomtypes rt 
            WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code 
            AND o.Date LIKE '$p%';    
            ";
            $result = mysqli_query($GLOBALS['conn'], $query);
            $count = $result->fetch_assoc()['sum'];
            mysqli_free_result($result);
            $values[] = $count ? $count : "0";
        }

        return $values;
    }
    
    function getRevPerStay($periods) {
        $values = [];
        foreach($periods as $p) {
            $query ="
            SELECT ROUND(SUM(rt.Rate)/COUNT(DISTINCT o.ResID), 0) val
            FROM occupancies o, rooms r, roomtypes rt 
            WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code 
            AND o.Date LIKE '$p%';    
            ";
            $result = mysqli_query($GLOBALS['conn'], $query);
            $count = $result->fetch_assoc()['val'];
            mysqli_free_result($result);
            $values[] = $count ? $count : "0";
        }
        
        return $values;        
    }

    function getRevPerRoom($periods) {
        $values = [];
        foreach($periods as $p) {
            $query ="
            SELECT ROUND(SUM(rt.Rate)/(SELECT COUNT(*) FROM rooms), 0) val
            FROM occupancies o, rooms r, roomtypes rt 
            WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code 
            AND o.Date LIKE '$p%';    
            ";
            $result = mysqli_query($GLOBALS['conn'], $query);
            $count = $result->fetch_assoc()['val'];
            mysqli_free_result($result);
            $values[] = $count ? $count : "0";
        }
        
        return $values;
    }

    function getMonths() {
        $months = [];

        for ($j = 2; $j > 0; $j--) {
            $months[] = date("Y-m", strtotime( date( 'Y-m-01' )." +$j months"));
        }

        for ($i = 0; $i <= 11; $i++) {
            $months[] = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
        }
        return array_reverse($months);
    }

    function getDays() {
        $days = [];
        $timestamp = time() - 24 * 3600 * 13;
        for ($i = 0 ; $i < 21 ; $i++) {
            $days[] = date('Y-m-d', $timestamp);
            $timestamp += 24 * 3600;
        }
        return $days;
    }

?>