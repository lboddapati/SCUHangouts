<?php
    session_start();
    $username = $_POST['username'];
    if(!isset($_SESSION[$username])) {
        //header("Location: index.php");
        die("Error:: You are not logged in");
    }

    $mysqli = mysqli_connect("localhost", "root", "password", "webchat");
    if (mysqli_connect_errno()) {
  		die ("Error:: Failed to connect to MySQL: " . mysqli_connect_error());
  	}

  	$upload_success = true;
  	$err = "";
  	$picture = NULL;
	if (isset($_FILES['picture']) && !empty( $_FILES['picture']['name'])) {
		$target_dir = "/Library/WebServer/Documents/webchat/uploads/";
		$imageFileType = pathinfo(basename($_FILES['picture']['name']),PATHINFO_EXTENSION);
		$imageFileType = strtolower($imageFileType);
		$target_file = $target_dir.$username."_profile_picture.jpeg";//.$imageFileType;
		//echo $target_file;
		$picture = "./uploads/".$username."_profile_picture.jpeg";//.$imageFileType;	
		$uploadOk = 1;
		// Check if image file is a actual image or fake image
	  	$check = getimagesize($_FILES['picture']['tmp_name']);
	   	if($check !== false) {
	       	$uploadOk = 1;
	   	} else {
	       	$err = $err." File is not an image.<br>";
	       	var_dump($_FILES);
	       	$uploadOk = 0;
	   	}
		// Check file size
		/*if ($_FILES["picture"]["size"] > 500000) {
		    echo "Sorry, your file is too large.";
		    $uploadOk = 0;
		}*/
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
		    $err = $err." Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    $err = $err." Sorry, your file was not uploaded.<br>";
		    $upload_success = false;
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
		    } else {
		        $err = $err." Sorry, there was an error uploading your file.<br>";
		        $upload_success = false;
		    }
		}
	} else {
		$upload_success = false;
		$err = $err." Please Choose a file";
  	}


	if ($upload_success) {
  		$query = "UPDATE users SET picture='$picture' where username='$username'";
  		if (!mysqli_query($mysqli, $query)) {
  			die ("Error:: Could not update profile pic: ".mysqli_error($mysqli));
  		} else {
  			echo "Success:: Profile picture updated! :: ".$picture;
  		}

	} else {
		echo "Error:: ".$err;
	}

	mysqli_close($mysqli);

?>