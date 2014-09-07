<?php
    //initialize session
    session_start();
    // regenerates a new session_id to help prevent session hijacking
    session_regenerate_id();
    //destory all $_SESSION[] variables
    session_destroy();
    if(isset($_COOKIE['username'])){
        setcookie('username', '', time()-3600, '/');
    }
    header("Location: index.html");
?>