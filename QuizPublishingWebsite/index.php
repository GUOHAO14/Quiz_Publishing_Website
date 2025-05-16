<?php
session_start();
session_unset();
session_destroy();
$loginFail = false;
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "conn.php";
    echo "<script>console.log('Good');</script>";

    $username = $_POST["user"];
    $password = $_POST["password"];
    
    $loginFail = true;
    $userId = "";
    $sql = "SELECT * FROM user";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $existUser = $row['username'];
        $existPassword = $row['password'];
        if ($existUser == $username && password_verify($password, $existPassword)) {
            $userId = $row['user_id'];
            $loginFail = false;
            break;
        }
    }

    if (!$loginFail) {
        echo "<script>console.log('Good');</script>";
        $sql = "SELECT * FROM user WHERE user_id = '$userId';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        session_start();
        $_SESSION['user_id'] = $row['user_id'];
        $userId = $_SESSION['user_id'];
        $_SESSION['role'] = $row['role'];
        $role = $_SESSION['role'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        $photo = $row['photo'];
        $_SESSION['quizRedirect'] = "no";
        if ($photo == null) {
            $_SESSION['photo'] = "media/profileDefault.png";
        } else {
            $_SESSION['photo'] = $row['photo'];
        }

        if ($role === "student") {
            $sql = 
            "SELECT * 
            FROM student 
            JOIN enrolment ON student.student_id = enrolment.student_id
            WHERE student.user_id = '$userId';";
            $studentOutput = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($studentOutput);
            //check if student is already under a class or not
            if (!$row) {
                $sql = 
                "SELECT * 
                FROM student WHERE user_id = '$userId';";
                $studentOutput = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($studentOutput);
                $_SESSION['student_id'] = $row['student_id'];
                $_SESSION['class_id'] = "";
                // echo "<script>alert('No class ID');</script>";
            } else {
                $_SESSION['student_id'] = $row['student_id'];
                $_SESSION['class_id'] = $row['class_id'];
                echo "<script>alert('Has class ID');</script>";
            }
            // echo "<script>alert('Logged in successful as a student');</script>";
            header("Location: stu-home.php");
            //to wenyew's student homepage
        }

        else if ($role === "teacher") {
            $sql = "SELECT * FROM teacher WHERE user_id = '$userId';";
            $teacherOutput = mysqli_query($conn, $sql);
            $_SESSION['teacher_id'] = mysqli_fetch_array($teacherOutput)['teacher_id'];
            header("Location: teachhome.php");
            //to joshua's teacher homepage
        }

        else if ($role === "admin") {
            echo "<script>console.log('Good');</script>";
            $sql = "SELECT * FROM admin WHERE user_id = '$userId';";
            $adminOutput = mysqli_query($conn, $sql);
            $_SESSION['admin_id'] = mysqli_fetch_array($adminOutput)['admin_id'];
            echo "<script>alert('Admin');</script>";
            //to colwyn's admin dashboard page
            header("Location: adminMngData.php"); //temporary
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morning Quiznos</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="index.css">
    <script src="index.js"></script>
</head>
<body>
    <div id="bg">
        <?php include "bgVideo.php"?>
    </div>
    <div id="body">
        <img id="sunLogo" src="media/sun.png" alt="Website Logo">
        <div id="header">MORNING QUIZNOS</div>
        <div id="mainEntry">
            <form action="" onsubmit="vadForm(event)" method="post">
                <table>
                    <tr>
                        <td id="welcome"><strong>Welcome!</strong></td>
                    </tr>
                    <tr>
                        <td><p></p></td>
                    </tr>
                    <tr>    
                        <th><label for="us">Username</label></th>
                    </tr>
                    <tr>
                        <td class=""><input type="text" name="user" id="us" onfocus="inpDesign(id)" oninput="inpDesign(id)" onfocusout="inpRevert(id)" placeholder="e.g.: Sulaiman M."></td>
                    </tr>
                    <tr>
                        <td><p id="msgUS"></p></td>
                    </tr>
                    <tr>
                        <th><label for="pw">Login Password</label></th>
                    </tr>
                    <tr>
                        <td>
                            <div class="pwContainer">
                                <input type="password" name="password" id="pw" onfocus="inpDesign(id)" oninput="inpDesign(id)" onfocusout="inpRevert(id)">
                                <img id="pwVisible" src="media/visibilityOff.png" alt="VisibilityOff" onclick="hidePW()"><img id="pwNotVisible" src="media/visibilityOn.png" alt="VisibilityOn" onclick="hidePW()">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td id="fgPW_td"><a href="http://www.google.com" id="fgPW">Forgot Password?</a></td>
                    </tr>
                    <tr>
                        <td><p id="blankError"></p><p id="invalidError"></p></td>
                    </tr>
                    <tr>
                        <td id="login_td"><input type="submit" value="LOGIN" id="loginButt"></td>
                    </tr>
                    <tr>
                        <td style="height: 50px;"><p id="note"><strong>Note for students:</strong><br>Register an account with your class teacher if you don't have an account.</p></td>
                    </tr>
                    
                </table>
            </form>
        </div>
    </div>
    <dialog id="loginFailModal">Username or Password is incorrect. Try again.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

<script>
    <?php if ($loginFail): ?>
        document.getElementById("loginFailModal").showModal();
        document.getElementById("body").style.filter = "blur(5px)";
    <?php endif ?>

    function okayExit() { 
        document.getElementById("loginFailModal").close(); 
        document.getElementById("body").style.filter = "blur(0px)";
    } 
</script>
</body>
</html>