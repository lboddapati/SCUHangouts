<?php
    $phpmsg="";
    if(isset($_POST['email']) && isset($_POST['send_email'])) {
        $to = $_POST['email'];
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {    //validate email
              $phpmsg = "Invalid email format!"; 
        } else {
            $con = mysqli_connect('localhost', 'root', 'password', 'webchat');
            if(mysqli_connect_errno()) {
                die ("Failed to connect to MYSQL: ".mysqli_connect_error());
            }

            $result = mysqli_query($con, "SELECT username, password FROM users WHERE email='$to'");
            if(!$result) {
                die("MySQL error: ".mysqli_error($con));
            }
            if(mysqli_num_rows($result) <= 0) {     // check if email is registered
                $phpmsg = "No such registered email found!";
            } else {        // if yes then email the username and password to it
                $row = mysqli_fetch_assoc($result);
                $username = $row['username'];
                $password = $row['password'];
                $subject = "Forgotten Username/Password";
                $body = "Username: ".$username."\nPassword: ".$password;
                //from noreply.webchat@gmail.com
                $success = mail($to, $subject, $body);
                if($success) {
                    $phpmsg = "Your username and password have been emailed to you!<br>Please check your inbox for an email from noreply.webchat@gmail.com";
                } else {
                    $phpmsg = "Failed to send email! Try again.";
                }
            }
        }
    }
?>

<html>
<head>
    <title>Forgot username/password</title>
    <link rel="stylesheet" type="text/css" href="css/send_username_password.css">
</head>
<body>
    <h1>SCU Hangouts</h1>
    <p><?php echo $phpmsg ?></p>
    <div id="form">
    <form action="send_username_password.php" method="post">
        <input type="email" name="email" placeholder="Enter the email used to register" required/><br>
        <input type="submit" name="send_email" value="Email username/password"/>
    </form>
    <button onclick="location.href='index.php'">Go Back to Login Page</button>
    </div>
</body>
</html
