 <?php
	$user = $_GET['user'];
	session_start();
	if(!isset($_SESSION[$user])) {
		die("You are not logged in");
	}

	$con = mysqli_connect("localhost", "root", "password", "webchat");
	if(mysqli_connect_errno()) {
		die("Failed to connect to MySQL: ".mysqli_connect_error());
	}

	$to = $_GET['to'];
	$action = $_GET['action'];
	if ($action == "accepted") {
		$query1 = "UPDATE relationship SET relation='friend' WHERE user1='$user' AND user2='$to'";
		if(mysqli_query($con, $query1)) {
			echo "Relation with ".$to." updated to friend";
		} else {
			echo "Could not process friend request ".mysqli_error($con);
		}
	} else if ($action == "blocked") {
		//TODO
	} else {	// rejected or pending;
		$query1 = "DELETE FROM relationship WHERE user1='$user' AND user2='$to' AND (relation='rejected' OR relation='pending')";
		if(mysqli_query($con, $query1)) {
			echo "Friend request to ".$to." deleted";
		} else {
			echo "Could not process friend request ".mysqli_error($con);
		}
	}

	mysqli_close($con);
?>