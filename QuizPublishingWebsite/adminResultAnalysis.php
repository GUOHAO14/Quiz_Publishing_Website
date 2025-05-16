<?php
session_start();
$_SESSION['quizRedirect'] = "no";
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
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <title>Quiz Analysis</title>
    <style>
        #headerContainer {
            position: absolute;
            width: 100%;
            height: 100vh;
            left: 0;
            top: 0;
            bottom: 0;
        }

        #bodyContainer {
            position: relative;
            padding-top: calc(11vh);
            padding-left: 14%;
            z-index: 1;
        }
        
        @media screen and (max-width: 768px) {
            #bodyContainer {
                padding-left: 0;
            }
        }
    </style>
</head>
<body>
    <div id="headerContainer">
        <?php include "adminHeader.php"; ?>
    </div>
    <div id="bodyContainer">
        <?php include "resultAnalysis.php"; ?>
    </div>
</body>
</html>