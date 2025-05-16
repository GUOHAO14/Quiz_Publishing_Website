<?php
include "conn.php";

$username = $_POST["user"];
$password = $_POST["password"];
$sql = "SELECT * FROM user WHERE username = '$username' and password = '$password';";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
} else {
    session_start();
     $_SESSION['loggedin'] = true;
    header("Location: viewProfile.php");
}

// session_start();
// $_SESSION['loggedin'] = true;
// header("Location: viewProfile.php");
?>