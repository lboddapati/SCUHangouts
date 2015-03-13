<?php
	$username = $_GET['username'];
	session_start();
	if(!isset($_SESSION[$username])) {
		//header("Location: index.php");
		die("You are not logged in");
	}

	$con = mysqli_connect("localhost", "root", "password", "webchat");
	if(mysqli_connect_errno()) {
		die("Failed to connect to MySQL: ".mysqli_connect_error());
	}

	$query = "SELECT user2, relation FROM relationship WHERE user1='$username' AND (relation!='friend' AND relation!='blocked')";
	$result = mysqli_query($con, $query);
	if (!$result) {
		die(mysqli_error($con));
	}
	$return = "";
	if(mysqli_num_rows($result)>0) {
		$return = array();
		while($row = mysqli_fetch_assoc($result)) {
			$return[] = $row;
		}
		echo json_encode($return);
	} else {
		echo json_encode("none");
	}
	mysqli_close($con);
?>