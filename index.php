<?php
// Login page : Validate username and password with database
$err = "";
if(isset($_POST['login'])) {
    if ($_POST['username']!="" && $_POST['password']!="") {
        $username=$_POST['username'];
        $password=$_POST['password'];

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
			$err = "You are already logged in";
		} else {
			$pic = $row['picture'];
                	//$pic = mysqli_fetch_assoc($result)['picture'];
                	$_SESSION[$username] = uniqid();    // start session and assign a session id for the user and
                	mysqli_query($connect, "UPDATE users SET status='online' WHERE username='$username'");  // update user's status to 'online' and
                	header("Location: welcome.php?username=".$username."&pic=".$pic);   // redirect to welcome page
		}
            } else $err = "Incorrect username/password";
        //}
    } else $err = "Username/Password cannot be empty!";
}
?>


<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body class="container text-center">
    <h1>SCU Hangouts</h1>
    <p class="error"><?php echo $err ?></p>
    <div id="form">
        <form role="form" action="index.php" method="post">
            <div class="form-group">
                <!--<label for="username">Username: </label>-->
                <input name="username" type="text" placeholder="Username" required/> <br>
                <!--<label for="password">Password: </label>-->
                <input name="password" type="password" placeholder="Password" required/> <br>
                <input type="submit" name="login" value="Login" id="login_button"/>
            </div>
        </form>
        <div id="links" class="row">
            <div class="col-sm-4 text-right">
                <a id="forgot" href="send_username_password.php">Forgot username/password?</a>    <!-- redirect to password recovery page-->
            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4 text-left">
                <a id="register" href="register.php">Sign up!</a>    <!-- redirect to registration page -->
            </div>
        </div>
    </div>
    <div class="blur">
        <img src="images/SCU-campus.jpg" class="bg">
    </div>
</body>
</html>
