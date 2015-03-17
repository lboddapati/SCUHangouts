<?php
    $user = $_GET['user'];
    session_start();
    if(!isset($_SESSION[$user])) {
        //header("Location: index.php");
        die("you are not logged in");
    }

    $friend = $_GET['friend'];
    $connect = mysqli_connect("localhost", "root", "password", "webchat");
    if (mysqli_connect_errno()) {
        die ("Failed to connect to MySQL: " . mysqli_connect_error());
    }

    // Retrieve all entries from table 'chats' which correspond to user and friend.
    $query = "SELECT * FROM chats WHERE (sender='$user' AND receiver='$friend') OR (sender='$friend' AND receiver='$user') ORDER BY id";
    $result = mysqli_query($connect, $query);
    $return = "";
    while ($row = mysqli_fetch_array($result)) {
        $sender = $row['sender'];
        $receiver = $row['receiver'];
        $timestamp = $row['timestamp'];
        $msg = $row['message'];
        $class = "";
        if($sender == $user) {
            $class = "sent";
        } else {
            $class = "received";
        }
        $return = $return."<p class='".$class."'><span class='time'>(".$timestamp.")</span> ".$sender.":<br>".$msg."</p>";
    }
    echo $return;
?>
