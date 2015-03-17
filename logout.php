<?php
    session_start();
    $username = $_GET['username'];
    $connect = mysqli_connect('localhost', 'root', 'password', 'webchat');
    if (mysqli_connect_errno()) {
        die ("Failed to connect to MySQL: " . mysqli_connect_error());
    }
    // Upldate user status to offline,
    if(!mysqli_query($connect, "UPDATE users SET status='offline' WHERE username='$username'")) {
        die("Error: ".mysqli_error($connect));
    }
    unset($_SESSION[$username]);    // and unset session id for user.
    mysqli_close($connect);
    header("Location: index.php");
    //TODO : Fix browser back button issue
?>
