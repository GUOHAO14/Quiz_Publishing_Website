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
    <title>Quiz Management</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <style>
        #headerContainer {
            position: absolute;
            width: 100%;
            height: 100vh;
            left: 0;
            top: 0;
            bottom: 0;
        }

        #contentContainer {
            height: fit-content;
            overflow: hidden;
            margin-bottom: 20px;
            padding-top: calc(11vh + 1rem);
            padding-left: 14%;
        }

        .userManagement {
            width: 50%;
            background-color: #fff;
            padding: 20px;
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 2rem auto;
        }

        .userManagement h2 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .createUserBtn {
            margin: 1.5rem auto;
        }

        .createUserBtn button {
            background-color: #e7e7e7;
            color: #333;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .createUserBtn button::before {
            content: '+';
            font-size: 20px;
            margin-right: 5px;
        }

        @media screen and (max-width: 768px) {
            #contentContainer {
                padding-left: 0;
            }
        }
                
    </style>
</head>
    <div id="headerContainer">
        <?php include "adminHeader.php"?>
    </div>

    <div id="contentContainer">
        <div class="userManagement">
            <h2>Create Quiz</h2>
            <div class="createUserBtn">
                <button onclick="window.location.href = 'adminCreateQuiz.php'">New Quiz</button>
            </div>
        </div>
        <hr>
        <?php include "quizMenu.php"?>
    </div>
</body>
</html>