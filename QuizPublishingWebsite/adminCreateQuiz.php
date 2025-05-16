<?php 
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="create.css">
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <title>Create Quiz</title>
    <style>
        #headerContainer {
            position: absolute;
            width: 100%;
            height: 100vh;
            left: 0;  /* Positioning the left compartment on the left side */
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

        @media screen and (max-width: 768px) {
            #contentContainer {
                padding-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php include "adminHeader.php"; ?>
    <div id="contentContainer">
        <?php include "sharedCreateQuiz.php"; ?>
    </div>
</body>
</html>