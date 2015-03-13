<?php
	$user = $_GET['user'];
	session_start();
	if(!isset($_SESSION[$user])) {	// Check if user is logged in
		die("You are not logged in");
	}

	$con = mysqli_connect("localhost", "root", "password", "webchat");
	if(mysqli_connect_errno()) {
		die("Failed to connect to MySQL: ".mysqli_connect_error());
	}
	$from = $_GET['from'];
	$action = $_GET['action'];

	if ($action == "accept") {
		$query1 = "UPDATE relationship SET relation='accepted' WHERE user1='$from' AND user2='$user'";
		$query2 = "INSERT INTO relationship values('$user', '$from', 'friend')";
		if(mysqli_query($con, $query1) && mysqli_query($con, $query2)) {
			echo "Friend request from ".$from." accepted";
		} else {
			die("Could not process friend request ".mysqli_error($con));
		}
	} else if($action == "reject") {
		$query1 = "UPDATE relationship SET relation='rejected' WHERE user1='$from' AND user2='$user'";
		if(mysqli_query($con, $query1)) {
			echo "Friend request from ".$from." rejected";
		} else {
			die ("Could not process friend request ".mysqli_error($con));
		}
	} else if($action == "block") {
		$query1 = "UPDATE relationship SET relation='blocked' WHERE user1='$from' AND user2='$user'";
		$query2 = "INSERT INTO relationship values('$user', '$from', 'blocked')";
		if(mysqli_query($con, $query1) && mysqli_query($con, $query2)) {
			echo "Friend request from ".$from." blocked";
		} else {
			die("Could not process friend request ".mysqli_error($con));
		}
	} else {
		die ("Invalid action: "+$action);
	}

	mysqli_close($con);
?>