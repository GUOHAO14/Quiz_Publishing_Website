<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    session_start();
    header("Location: index.php");
    session_write_close();
    exit();
}
include("conn.php");

$class_id = $_SESSION["class_id"];
$student_id = $_SESSION["student_id"];
$quiz_id = $_SESSION["quiz_id"];
$btntext = $_SESSION["btntext"];

$sql = "SELECT * FROM quiz WHERE quiz_id LIKE '".$quiz_id."'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$quiz_name = $row[1];
$quiz_type = $row[3];

$sql = "SELECT * FROM question WHERE quiz_id LIKE '".$quiz_id."'";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($result)){
    $quest_id[] = $row[0];
    $q_text[] = $row[1];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morning Quiznos</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="stu.css">
</head>

<!-- add confirmation for header navigation or don't let user exit, the window back button too-->

<body>
    <?php include "bgVideo.php"?>
    <?php include("studentHeader.php"); ?>

    <main>
        <nav class="main">
            <button class="exit" title="Exit" onclick="exit()"><img src="media/exit.png" alt="Exit" width="40px" height= "40px"></button>
            <h1 class="nav">
                <?php
                echo $quiz_name;
                ?>
            </h1>
        </nav>
    </main>

    <section id="question">
        <form class="qform" action="end-quiz.php" method="post">
            <input type="hidden" name="timeTaken" id="timeTaken">

        </form>

        <nav class="quest-nav">
            <?php
            if($quiz_type != "exercise" && $btntext != "PREVIEW"){
                echo '<div id="timer">Time taken: 0s</div>';
            }
            ?>
            <p class="quest-title">Questions</p>

            <div class="quest-box">
                
            </div>

            <?php
            if($btntext == "START"){
                echo '<a href="#submit">Finish Attempt...</a>';
            }
            ?>
        </nav>

    </section>


    <script>
        //Questions
        let area_question = document.getElementsByClassName("qform")[0];
        area_question.innerHTML += `<?php
        for($i = 0; $i < count($quest_id); $i++){
            $sql = "SELECT * FROM answer_selection WHERE quest_id LIKE '".$quest_id[$i]."'";
            $result = mysqli_query($conn, $sql);
            $select_id = [];
            $s_text = [];
            while($row = mysqli_fetch_array($result)){
                $select_id[] = $row[0];
                $s_text[] = $row[1];
            }

            if(count($select_id) > 1){
                echo '
                <div class ="quest-area"  id="'.$quest_id[$i].'">
                    <p class="question">
                        '.($i+1).') '.$q_text[$i].'
                    </p>';
                for($j = 0; $j < count($select_id); $j++){
                    echo '
                    <label class="mcq">
                        <input type="radio" name="'.$quest_id[$i].'" value="'.$select_id[$j].'" class="mcq" required> '.$s_text[$j].'
                    </label>';
                }
                if($btntext == "START"){
                    echo '<button type="button" class="clear" onclick="clear_mcq(\''.$quest_id[$i].'\')">Clear</button>';
                }
                echo '</div>';

            }else if(count($select_id) == 1){
                echo '
                <div class ="quest-area" id="'.$quest_id[$i].'">
                    <p class="question">
                        '.($i+1).') '.$q_text[$i].'
                    </p>
                    <input type="text" name="'.$quest_id[$i].'" class="text" placeholder="Type your answer" required>';
                if($btntext == "START"){
                    echo '<button type="button" class="clear" onclick="clear_text(\''.$quest_id[$i].'\')">Clear</button>';
                }
                echo '</div>';
            }else if(count($select_id) == 0){
                echo '
                <div class ="quest-area" id="'.$quest_id[$i].'">
                    <p class="question">
                        '.($i+1).') '.$q_text[$i].'
                    </p>
                </div>';
            }
        }
        if($btntext == "START"){
            echo '
            <div class="sub-res">
                <input class="reset" type="reset" value="Clear All" onclick="return con_clear()">
                <input class="submit" type="submit" value="Submit" onclick="return con_submit()" id="submit">
            </div>';
        }
        ?>`;


        //Question Navigation
        let area_QB = document.getElementsByClassName("quest-box")[0];
        area_QB.innerHTML += `<?php

        for($i = 0; $i < count($quest_id); $i++){
            $content = '
            <div class="boxa" onclick="goquest(\''.$quest_id[$i].'\')">
                <div class="boxa1">
                    <p class="q">'.($i+1).'</p>
                </div>
                <div class="boxa2"></div>
            </div>';

            echo $content;
        }?>`;

        function goquest(id) {
            window.location.href = "#" + id;
        }
        
        //////////////
        function clear_mcq(name) {
            let option = document.querySelectorAll('input[name="' + name + '"]');
            option.forEach(radio => {
                radio.checked = false;
            });
            save_ans();
        }

        function clear_text(name) {
            document.querySelector('input[name="' + name + '"]').value = '';
            save_ans();
        }

        function con_submit() {
            if (!window.confirm("Are you sure you want to submit the answers?")) {
                return false;
            }
            return true;
        }

        function con_clear() {
            if (!window.confirm("Are you sure you want to clear all the answers?")) {
                return false;
            }
            document.querySelector("form").reset();
            save_ans();
            return true;
        }

        function save_ans() {
            let answer1 = {};
            let input1 = document.querySelector("form").querySelectorAll("input[type='radio']");
            input1.forEach(input => {
                if (input.checked) {
                    answer1[input.name] = input.value;
                }
            });

            let answer2 = {};
            let input2 = document.querySelector("form").querySelectorAll("input[type='text']");
            input2.forEach(input => {
                answer2[input.name] = input.value;
            });

            sessionStorage.setItem("answer1", JSON.stringify(answer1));
            sessionStorage.setItem("answer2", JSON.stringify(answer2));
        }

        function load_ans() {
            let answer1 = JSON.parse(sessionStorage.getItem("answer1")) || {};
            for (let name in answer1) {
                let input = document.querySelector("form").querySelector(`input[name="${name}"][value="${answer1[name]}"]`);
                input.checked = true;
            }
            
            let answer2 = JSON.parse(sessionStorage.getItem("answer2")) || {};
            for (let name in answer2) {
                let input = document.querySelector("form").querySelector(`input[name="${name}"]`);
                input.value = answer2[name];
            }
        }

        document.querySelector("form").addEventListener("input", save_ans);
        document.querySelector("form").addEventListener("change", save_ans);
        document.addEventListener("DOMContentLoaded", load_ans);
        

        //Functions
        function exit() {
            if (!window.confirm("Are you sure you want to exit the quiz? (All the answers will be cleared)")) {
                return;
            }else{
                sessionStorage.removeItem("answer1");
                sessionStorage.removeItem("answer2");
                window.location.href = "start-quiz.php";
            }
        }

        //Animations
        let QA = document.getElementsByClassName("quest-area");  //if no question???
        let QN = document.getElementsByClassName("quest-nav")[0];
        <?php
        if($btntext == "START"){
            echo '
            let but_submit = document.getElementsByClassName("submit")[0];
            let but_reset = document.getElementsByClassName("reset")[0];
            ';
        }
        ?>

        function display(element) {
            let position = element.getBoundingClientRect();
            if(position.top < window.innerHeight && position.bottom > 0) {
                element.style.opacity = "1";
                element.style.transform = "scale(1)";
            }
        }

        for (let i = 0; i < QA.length; i++) {
            display(QA[i]);
            display(QN);
            <?php
            if($btntext == "START"){
                echo '
                display(but_submit);
                display(but_reset);
                ';
            }
            ?>
        }
        window.addEventListener("scroll", function(){
            for (let i = 0; i < QA.length; i++) {
                display(QA[i]);
                display(QN);
                <?php
                if($btntext == "START"){
                    echo '
                    display(but_submit);
                    display(but_reset);
                    ';
                }
                ?>
            }
        })

        //Timer
        <?php
        if($quiz_type == "exercise" || $btntext == "PREVIEW"){
            echo '
            document.getElementById("timeTaken").value = null;';
        }else{
            echo '
            let startTime = new Date().getTime();
            let timerElement = document.getElementById("timer");

            let timerInterval = setInterval(updateTimer, 1000);

            document.getElementsByClassName("qform")[0].addEventListener("submit", function (event) {
                let endTime = new Date().getTime();
                let totalTime = Math.floor((endTime - startTime) / 1000);
                document.getElementById("timeTaken").value = totalTime;

                clearInterval(timerInterval);
            });

            function updateTimer() {
                let currentTime = new Date().getTime();
                let timeTaken = Math.floor((currentTime - startTime) / 1000);
                timerElement.textContent = `Time taken: ${timeTaken}s`;
            }';
        }?>
        
    </script>

</body>
</html>