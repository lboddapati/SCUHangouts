<?php
    session_start();
    $sender = $_POST['sender'];
    if(!isset($_SESSION[$sender])) {
        die("You are not logged in!");
    }

    $connect = mysqli_connect('localhost', 'root', 'password', 'webchat');
    if(mysqli_connect_errno()) {
        die("Failed to connect to MYSQL: ".mysqli_connect_error());
    }

    $receiver = $_POST['receiver'];
    $timestamp = $_POST['timestamp'];
    $msg = $_POST['msg'];
    $msg = mysqli_real_escape_string($connect, $msg);
    $query = "INSERT INTO chats VALUES(DEFAULT, '$sender', '$receiver', '$timestamp', '$msg')";
    if(mysqli_query($connect, $query)) {
         echo "success";
    } else {
         echo "failed ".mysqli_error($connect);
    }

    mysqli_close($connect);
?>
