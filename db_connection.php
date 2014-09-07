<?php
    // establish connection with my_db
    $HOST = "localhost";
    $USER = "sec_user";
    $PASSWORD = "hello";
    $DATABASE = "my_db";
    $link = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
?>