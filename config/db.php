<?php
    // connect to localhost database
    // $conn = mysqli_connect("localhost", "root", "", "hotel");
    // remote db connection
    $conn = mysqli_connect("us-cdbr-east-02.cleardb.com", "b1761e58250ee3", "95162fb7", "heroku_1885a5e34ea9c26");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " .mysqli_connect_errno();
    }
    
    $query = "SET time_zone = 'America/New_York';";
    mysqli_query($conn, $query);

    date_default_timezone_set("America/New_York");


