<?php
    // connect to database
    $conn = mysqli_connect("localhost", "root", "", "hotel");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " .mysqli_connect_errno();
    }

    date_default_timezone_set("America/New_York");


