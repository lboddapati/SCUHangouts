<?php
    $user = $_GET['user'];
    session_start();
    if(!isset($_SESSION[$user])) {
        die("You are not logged in");
    }

    $con = mysqli_connect('localhost', 'root', 'password', 'webchat');
    if (mysqli_connect_errno()) {
        die ("Failed to connect to MySQL: " . mysqli_connect_error());
    }

    $friend = $_GET['friend'];
    $query = "SELECT * FROM chats WHERE receiver='$user' AND sender='$friend' ORDER BY id";
    $result = mysqli_query($con, $query);
    $return = "";
    while ($row = mysqli_fetch_array($result)) {
        $timestamp = $row['timestamp'];
        $msg = $row['message'];
        $return = $return."<p class='received'> (".$timestamp.") ".$friend.":<br>".$msg."</p>"; 
    }

    mysqli_close($con);
    echo $return;
?>
