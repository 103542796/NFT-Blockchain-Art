<?php
    session_start();
    session_unset(); // unset the session
    session_destroy(); // remove all sessions

    header("location: ../user_info/login.php"); // header to index to log in again 
    exit();
?>