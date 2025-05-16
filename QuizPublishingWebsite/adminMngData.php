<?php
session_start();
$_SESSION['quizRedirect'] = "no";
$userId = $_SESSION['user_id'];
$userId = $_SESSION['admin_id'];

if (!isset($_SESSION['admin_id'])) {
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
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="adminMngData.css">
</head>
    <div id="headerContainer">
        <?php include "adminHeader.php"?>
    </div>
    <div id="contentContainer">
        <div id="mngList">
            <a href="assignTeacher.php" class="mngSelection" id="assignTeacher">Assign Teacher Course</a>
            <a href="enrolStudent.php" class="mngSelection" id="enrolStudent">Enrol Student Class</a>
            <a href="manageCourse.php" class="mngSelection" id="mngCourse">Manage Course</a>
            <a href="manageClass.php" class="mngSelection" id="mngClass">Manage Class</a>
            <a href="manageSubject.php" class="mngSelection" id="mngSubject">Manage Subject</a>
            <a href="manageFolder.php" class="mngSelection" id="mngFolder">Manage Subject</a>
        </div>
    </div>
</body>
</html>