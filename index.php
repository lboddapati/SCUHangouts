<?php
// Login page : Validate username and password with database
$err = "";
if(isset($_POST['login'])) {
    if ($_POST['username']!="" && $_POST['password']!="") {
        $username=$_POST['username'];
        $password=$_POST['password'];

        session_start();
        if(isset($_SESSION[$username])) {   //Check if user is already logged in
            $err = "You are already logged in";
        } else {
            $connect = mysqli_connect('localhost', 'root', 'password', 'webchat');
            if (mysqli_connect_errno()) {
                die ("Failed to connect to MySQL: " . mysqli_connect_error());
            }
            $user = mysqli_query($connect, "SELECT * FROM users WHERE username='$username' AND password='$password'");
            if(mysqli_num_rows($user)>0){   // If username and password match,
                $pic = mysqli_fetch_assoc($user)['picture'];
                $_SESSION[$username] = uniqid();    // start session and assign a session id for the user and
                mysqli_query($connect, "UPDATE users SET status='online' WHERE username='$username'");  // update user's status to 'online' and
                header("Location: welcome.php?username=".$username."&pic=".$pic);   // redirect to welcome page
            } else $err = "Incorrect username/password";
        }
    } else $err = "Username/Password cannot be empty!";
}
?>


<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>
    <h1>SCU Hangouts</h1>
    <!--<div id="vertical"></div>-->
    <p><?php echo $err ?></p>
    <div id="form">
        <form action="index.php" method="post">
            <!--<label for="username">Username: </label>-->
            <input name="username" type="text" placeholder="Username" required/> <br>
            <!--<label for="password">Password: </label>-->
            <input name="password" type="password" placeholder="Password" required/> <br>
            <input type="submit" name="login" value="Login" id="login_button"/>
        </form>
        <a id="forgot" href="send_username_password.php">Forgot username/password?</a>
        <a id="register" href="register.php">Sign up!</a>
    </div>
</body>
</html>
