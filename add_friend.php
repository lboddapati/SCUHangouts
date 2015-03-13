<?php
    session_start();
    $username = $_POST['username'];
    if(!isset($_SESSION[$username])) {
        //header("Location: index.php");
        die("You are not logged in!");
    } 

    $connect = mysqli_connect('localhost', 'root', 'password', 'webchat');
    if(mysqli_connect_errno()) {
        die ("Failed to connect to MySQL: " . mysqli_connect_error());
    }

    if($_POST['add']==true) {
        $friend = $_POST['friendname'];
        $msg = "";
        $success = "true";
        if($username == $friend) {
            $success = "false";
            $msg = "Cannot add yourself as friend";
        } else if(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM users WHERE username='$friend'"))<=0) {
            $success = "false";
            $msg = "No ".$friend." found";
        } else {
            $result1 = mysqli_query($connect, "SELECT relation FROM relationship WHERE user1='$username' AND user2='$friend'");
            if(mysqli_num_rows($result1)>0) {
                $success = "false";
                $relation = mysqli_fetch_assoc($result1)["relation"];
                if($relation == 'pending') {
                    $msg = "Friend request to ".$friend." already sent!";
                } else if($relation == 'accepted' or $relation == 'friend') {
                    $msg = $friend." is already your friend";
                } else if($relation == 'blocked') {
                    $msg = "You cannot sent requests to ".$friend;
                } else { // rejected
                    $result2 = mysqli_query($connect, "UPDATE relationship SET relation='pending' WHERE user1='$username' AND user2='$friend'");
                    if ($result2) {
                        $success = "true";
                        $msg = "Friend request re-sent to ".$friend;
                    } else {
                        $success = "false";
                        $msg = "Could not add ".$friend;
                    }
                }
            } else{
                $result1 = mysqli_query($connect, "INSERT INTO relationship VALUES('$username', '$friend', 'pending')");
                if ($result1) {
                    $success = "true";
                    $msg = "Friend request sent to ".$friend;
                } else {
                    $success = "false";
                    $msg = "Could not add ".$friend;
                }
            }  
        }

        echo '{"status":"'.$success.'","message":"'.$msg.'"}';
    }

    mysqli_close($connect);
?>