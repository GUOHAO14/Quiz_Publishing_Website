<?php
session_start();
$_SESSION['quizRedirect'] = "no";
if (!isset($_SESSION['teacher_id'])) {
    header("Location: index.php");
    session_write_close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="teacher.css">
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <title>Teacher Homepage</title>
</head>
<body>
    <?php include "header.php"; ?>
    <?php include "quizMenu.php"; ?>
</body>
</html>