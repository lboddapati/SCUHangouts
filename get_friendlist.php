<?php
    session_start();
    $username = $_GET['username'];
    if(!isset($_SESSION[$username])) {
        header("Location: index.php");
    }

    $connect = mysqli_connect('localhost', 'root', 'password', 'webchat');
    if (mysqli_connect_errno()) {
        die ("Failed to connect to MySQL: " . mysqli_connect_error());
    }

    // Get user's friends list and their status(online/offline).
    $query = "SELECT relationship.user2, users.status, users.picture FROM relationship INNER JOIN users ON relationship.user2 = users.username WHERE relationship.user1='$username' AND (relationship.relation='friend' OR relationship.relation='accepted')";
    $result = mysqli_query($connect, $query);
    if ($result) {
        $array  = array();
        while ($row = mysqli_fetch_row($result)) {
            $array[] = $row;
        }
        echo json_encode($array);
    } else {
        echo json_encode(mysqli_error($connect));
    }

    mysqli_close($connect);
?>
