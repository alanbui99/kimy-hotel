<?php
    require('../config/db.php');
    
    $q = $_REQUEST['q'];

    if ($q != '') {
        $query = "SELECT * FROM customers WHERE Fname LIKE '%$q%' OR Lname LIKE '%$q%';";
        $result = mysqli_query($conn, $query);
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        mysqli_free_result($result);
        echo json_encode($data);
    }

?>