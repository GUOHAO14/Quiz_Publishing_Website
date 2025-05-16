<?php
$username = "root";
$password = "";
$dBname = "morningkdb";
$servername = "localhost";

$conn = mysqli_connect($servername, $username, $password, $dBname);

if (mysqli_connect_errno()) {
    die("<script>alert('Database connection failed.');</script>");
}
?>