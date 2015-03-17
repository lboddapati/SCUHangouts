<?php
// Login page : Validate username and password with database
$username=$_POST['username'];
$password=$_POST['password'];
//echo "$username and $password";
session_start();
//if(isset($_SESSION[$username])) {   //Check if user is already logged in
//    $err = "You are already logged in";
//} else {
    $connect = mysqli_connect('localhost', 'root', 'password', 'webchat');
    if (mysqli_connect_errno()) {
        die ("Failed to connect to MySQL: " . mysqli_connect_error());
    }
    $result = mysqli_query($connect, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    if(mysqli_num_rows($result)>0){   // If username and password match,
        $row = mysqli_fetch_assoc($result);
        $status = $row['status'];
        if($status == 'online') {
           echo "You are already logged in";
        } else {
           $pic = $row['picture'];
           $_SESSION[$username] = uniqid();    // start session and assign a session id for the user and
           mysqli_query($connect, "UPDATE users SET status='online' WHERE username='$username'");  // update user's status to 'online' and
           echo "success::$pic";
        }
    } else echo "Incorrect username/password";
?>