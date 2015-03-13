<?php
	if(isset($_POST['register'])) {
		$email = preg_replace('/\s+/', '', $_POST['email']);
		$username = preg_replace('/\s+/', '', $_POST['username']);
		$password = preg_replace('/\s+/', '', $_POST['password']);
		$confirm_password = preg_replace('/\s+/', '', $_POST['confirm_password']);
		$picture = 'DEFAULT';

		$php_msg="";
		
		$validation_success = true;

		// Validate entered email, username, password field values
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  			$err = "Invalid email format!<br>";
  			$validation_success = false;
		}
		if ($username == "") {
			$err = $err."Please enter a valid username!<br>";
			$validation_success = false;
		}
		if ($password == "") {
			$err = $err."Please enter a valid password!<br>";
			$validation_success = false;
		}
		if ($confirm_password == "") {
			$err = $err."Please confirm the password!<br>";
			$validation_success = false;
		}
		if ($password != $confirm_password) {
			$err = $err."Confirm password does not match password field!";
			$validation_success = false;
		}

		// If validation is success,
		if($validation_success == true) {
			$connect = mysqli_connect("localhost", "root", "password", "webchat");
			if(mysqli_connect_errno()) {
                die ("Failed to connect to MySQL: " . mysqli_connect_error());
			}
			$proceed = true;

			// then check if username/email are already registered.
			$username_exists=mysqli_query($connect, "SELECT * FROM users WHERE username='$username'");
			$email_exists=mysqli_query($connect, "SELECT * FROM users WHERE email='$email'");
			if(mysqli_num_rows($email_exists)>0) {
				$err = $err."Email already registered!<br>";
				$proceed = false;
			}
			if(mysqli_num_rows($username_exists)>0) {
				$err = $err."Username taken!<br>";
				$proceed = false;
			}

			// if user uploads a profile picture, validate the file and upload it to server
			$upload_success = true;
			if (isset($_FILES['picture']) && !empty( $_FILES['picture']['name']) && $proceed) {
				$target_dir = "/Library/WebServer/Documents/webchat/uploads/";
				$imageFileType = pathinfo(basename($_FILES['picture']['name']),PATHINFO_EXTENSION);
				$target_file = $target_dir.$username."_profile_picture.jpeg";
				$picture = "./uploads/".$username."_profile_picture.jpeg";	

				$uploadOk = 1;
				// Check if image file is a actual image or fake image
	    		$check = getimagesize($_FILES['picture']['tmp_name']);
		    	if($check !== false) {
		        	$uploadOk = 1;
		    	} else {
		        	$err = $err. "File is not an image.<br>";
		        	$uploadOk = 0;
		    	}
				/*// Check file size
				if ($_FILES["picture"]["size"] > 500000) {
				    echo "Sorry, your file is too large.";
				    $uploadOk = 0;
				}*/
				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
				    $err = $err. "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
				    $uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
				    $err = $err. "Sorry, your file was not uploaded.<br>";
				    $upload_success = false;
				// if everything is ok, try to upload file
				} else if (!(move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file))) {
				        $err = $err. "Sorry, there was an error uploading your file.<br>";
				        $upload_success = false;
				}
			}

			// If everything is ok, then register user in database
			if ($proceed && $upload_success) {
				$user = mysqli_query($connect, "INSERT INTO users VALUES ('$username', '$password', '$email', DEFAULT, $picture)");
				if($user) {
					$php_msg = "You have succesfully registered!<br>Your username is ".$username;
				} else {
					$err = $err."Registration failed! Try again<br>".mysqli_error($connect);
					$php_msg = $err;
				}
			} else {
				$php_msg = $err;
			}

			$email="";
			$username="";
			$password="";

			mysqli_close($connect);
		} else {
			//echo $err;
			$php_msg = $err;
		}
	}
?>

<html>
<head>
	<title>Register</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/register.css">
</head>
<body>
    <h1>SCU Hangouts</h1>
    <div id="vertical"></div>
    <p id="php_message"><?php echo $php_msg ?></p>

    <div id="form">
		<form action="register.php" method="post" enctype="multipart/form-data">
			<input required type="email" name="email" placeholder="E-mail" value=<?php echo $email?>> <br>
			<input required type="text" name="username" placeholder="Username" value=<?php echo $username?>> <br>
			<input required type="password" placeholder="Password" name="password"> <br>
			<input required type="password" placeholder="Confirm Password" name="confirm_password" > <br>
			
			<input type="text" id="picture_name" placeholder="No file chosen" disabled="disabled">
			<input id="uploadBtn" type="file" name="picture" accept="image/*"/>
			<span id="uploadBtnTrigger">Upload Profile Picture</span> <br>
			
			<input type="submit" name="register" value="Sign Up">
		</form>
		<button onclick="location.href='index.php'">Go Back to Login Page</button>
	</div>

	<script type="text/javascript">
		$("#uploadBtnTrigger").click(function() {
			//alert("clicked!");
			$("#uploadBtn").trigger('click');
		});

		$("#uploadBtn").change(function() {
			//alert($(this).val());
			$("#picture_name").val($(this).val());
		});
	</script>
</body>
</html>