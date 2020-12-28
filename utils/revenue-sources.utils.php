<?php
    require('../config/db.php');

    $q = $_REQUEST['q'];

    $data;
    if ($q != '') {
        $data = getData($q);
        echo json_encode($data);
    }

    function getData($source) {
        $data = [];

        $bgColors = [
            'standard' => '#007bff',
            'agency' => '#007bff',
            'deluxe' => '#1cc88a',
            'corporate' => '#1cc88a',
            'family suite' => '#36b9cc',
            'direct' => '#36b9cc',
            'business suite' => '#6c757d',
            'OTA' => '#6c757d'
        ];

        $hbgColors = [
            'standard' => '#007bff',
            'agency' => '#007bff',
            'deluxe' => '#17a673',
            'corporate' => '#17a673',
            'family suite' => '#2c9faf',
            'direct' => '#2c9faf',
            'business suite' => '#343a40',
            'OTA' => '#343a40'
        ];

        $bsClasses = [
            'agency' => 'primary',
            'corporate' => 'success',
            'direct' => 'info',
            'OTA' => 'secondary',
            'standard' => 'primary',
            'deluxe' => 'success',
            'family suite' => 'info',
            'business suite' => 'secondary'
        ];

        $query = "";
        if ($source == 'room-type') {
            $query = "
            SELECT rt.Style src, ROUND(SUM(rt.Rate), 0) val 
            FROM occupancies o, rooms r, roomtypes rt
            WHERE o.RoomNo = r.Number and r.TypeCode = rt.Code
            GROUP BY rt.Code;
            ";
        } else {
            $query = "
            SELECT re.Source src, ROUND(SUM(rt.Rate), 0) val 
            FROM occupancies o, rooms r, roomtypes rt,  reservations re
            WHERE o.RoomNo = r.Number and r.TypeCode = rt.Code AND re.ID = o.ResID
            GROUP BY re.Source;
            ";
        }

        $result = mysqli_query($GLOBALS['conn'], $query);

        while ($row = $result->fetch_assoc()) {
            $row['bgColor'] = $bgColors[$row['src']];
            $row['hbgColor'] = $hbgColors[$row['src']];
            $row['bsClass'] = $bsClasses[$row['src']];
            $data[] = $row;
        }

        mysqli_free_result($result);

        return $data;

    }

?>


