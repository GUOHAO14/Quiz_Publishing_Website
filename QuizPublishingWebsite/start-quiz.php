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

$sql = "SELECT * FROM class WHERE class_id LIKE '".$class_id."'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$class_name = $row[1];
$grade = $row[2];
$year = $row[3];

$sql = "SELECT * FROM quiz WHERE quiz_id LIKE '".$quiz_id."'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$quiz_name = $row[1];
$quiz_type = $row[3];
$quiz_desc = $row[2];

if($quiz_type == "assessment"){
    $sql = "SELECT * FROM assessment WHERE quiz_id LIKE '".$quiz_id."'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $assess_id = $row[0];
    $subject_id = $row[3];

    $quiz_editor = "All Admin";

    $sql = "SELECT * FROM question WHERE quiz_id = '".$quiz_id."'";
    $result = mysqli_query($conn, $sql);
    $total_question = mysqli_num_rows($result);

    $sql = "SELECT * FROM assessment_session WHERE student_id = '".$student_id."' AND assess_id = '".$assess_id."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) <= 0){
        $quiz_accuracy = '-';
        $comment = 'No Attempts';  //will affect past attempt
        $btntext = 'START';  //will affect the quiz to start
    } else {
        $row = mysqli_fetch_array($result);
        $quiz_accuracy = $row[2];
        $score = round(($quiz_accuracy / 100) * $total_question, 0);
        $comment = 'Well Done !';
        $time_taken = $row[3];
        $btntext = 'PREVIEW'; 
    }
    $attempt = '1';

}else if($quiz_type == "test"){
    $sql = "SELECT * FROM test WHERE quiz_id LIKE '".$quiz_id."'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $test_id = $row[0];

    $subject_id = $_SESSION["subject_id"];
    $sql = "SELECT * FROM subject WHERE subject_id LIKE '".$subject_id."'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $subject_name = $row[1];

    $course_id = $_SESSION["course_id"];
    $sql = "SELECT * FROM course_teacher WHERE course_id = '".$course_id."'";
    $result = mysqli_query($conn, $sql);
    $teacher_id = [];
    while($row = mysqli_fetch_array($result)){
        $teacher_id[] = $row[0];
    }
    $username = [];
    for($j = 0; $j < count($teacher_id); $j++){
        $sql = "SELECT * FROM teacher WHERE teacher_id = '".$teacher_id[$j]."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $user_id = $row[1];
        $sql = "SELECT * FROM user WHERE user_id = '".$user_id."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $username[] = $row[1];
    }
    $quiz_editor = implode(', ', $username);

    $sql = "SELECT * FROM question WHERE quiz_id = '".$quiz_id."'";
    $result = mysqli_query($conn, $sql);
    $total_question = mysqli_num_rows($result);

    $sql = "SELECT * FROM test_session WHERE student_id = '".$student_id."' AND test_id = '".$test_id."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) <= 0){
        $quiz_accuracy = '-';
        $comment = 'No Attempts';  //will affect past attempt
        $btntext = 'START';  //will affect the quiz to start
    } else {
        $row = mysqli_fetch_array($result);
        $quiz_accuracy = $row[2];
        $score = round(($quiz_accuracy / 100) * $total_question, 0);
        $comment = 'Well Done !';
        $time_taken = $row[3];
        $btntext = 'PREVIEW';
    }
    $attempt = '1';

}else if($quiz_type == "exercise"){
    $sql = "SELECT * FROM exercise WHERE quiz_id LIKE '".$quiz_id."'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $exe_id = $row[0];

    $subject_id = $_SESSION["subject_id"];
    $sql = "SELECT * FROM subject WHERE subject_id LIKE '".$subject_id."'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $subject_name = $row[1];

    $folder_id = $_SESSION["folder_id"];
    $sql = "SELECT * FROM folder WHERE folder_id LIKE '".$folder_id."'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $folder_name = $row[1];

    $course_id = $_SESSION["course_id"];
    $sql = "SELECT * FROM course_teacher WHERE course_id = '".$course_id."'";
    $result = mysqli_query($conn, $sql);
    $teacher_id = [];
    while($row = mysqli_fetch_array($result)){
        $teacher_id[] = $row[0];
    }
    $username = [];
    for($j = 0; $j < count($teacher_id); $j++){
        $sql = "SELECT * FROM teacher WHERE teacher_id = '".$teacher_id[$j]."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $user_id = $row[1];
        $sql = "SELECT * FROM user WHERE user_id = '".$user_id."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $username[] = $row[1];
    }
    $quiz_editor = implode(', ', $username);

    $sql = "SELECT * FROM question WHERE quiz_id = '".$quiz_id."'";
    $result = mysqli_query($conn, $sql);
    $total_question = mysqli_num_rows($result);

    $sql = "SELECT * FROM exercise_session WHERE student_id = '".$student_id."' AND exe_id = '".$exe_id."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) <= 0){
        $quiz_accuracy = '-';
        $comment = 'No Attempts';  //will affect past attempt
        $btntext = 'START';  //will affect the quiz to start
    } else {
        while($row = mysqli_fetch_array($result)){
            $session_id[] = $row[0];
            $score[] = round(($row[1] / 100) * $total_question, 0);
        }
        $average = array_sum($score) / count($score);
        $quiz_accuracy = round(($average / $total_question) * 100, 2);
        $comment = 'Well Done !';
        $btntext = 'START';
        
    }
    $attempt = '∞';
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

<body>
    <?php include "bgVideo.php"?>
    <?php include("studentHeader.php"); ?>

    <main>
        <nav class="main">
            <button class="back" title="Back" onclick="back()"><img src="media/back.png" alt="Back" width="40px" height= "40px"></button>
            <a href="stu-home.php" class="main">
                <h1>
                    <?php
                    echo $grade; echo $class_name;
                    ?>
                </h1>
            </a>
            <?php 
            if($quiz_type == "assessment"){
                $content = '
                    <h1 class="nav">></h1>
                    <h1 class="nav">'.$quiz_name.'</h1>';
                echo $content;

            }else if($quiz_type == "test"){
                $content = '
                    <h1 class="nav">></h1>
                    <a href="stu-subject.php" class="main">
                        <h1>'.$subject_name.'</h1>
                    </a>
                    <h1 class="nav">></h1>
                    <h1 class="nav">'.$quiz_name.'</h1>';
                echo $content;

            }else if($quiz_type == "exercise"){
                $content = '
                    <h1 class="nav">></h1>
                    <a href="stu-subject.php" class="main">
                        <h1>'.$subject_name.'</h1>
                    </a>
                    <h1 class="nav">></h1>
                    <a href="stu-folder.php" class="main">
                        <h1>'.$folder_name.'</h1>
                    </a>
                    <h1 class="nav">></h1>
                    <h1 class="nav">'.$quiz_name.'</h1>';
                echo $content;
            }
            ?>
        </nav>
                
        <section id="start-quiz">
            <div class="title">
                <h2 class="start-quiz-title"><?php echo $quiz_name ?></h2>
                <button class="share" title="Share"><img src="media/share.png" alt="Share" width="20px" height="20px"></button>
            </div>
            <p class="start-quiz-editor">Edited by: <?php echo $quiz_editor ?></p>
            <p class="start-quiz-desc">
                <?php echo $quiz_desc ?>
                <button class="MoreText" title="Show More" onclick="MoreText()">...More</button>
            </p>
            <div class="tag">
                <p class="tag-quiz"><?php echo $total_question ?> Questions</p>
                <p class="tag-quiz"><?php echo $attempt ?> Attempt Allowed</p>
            </div>
        </section>

        <section id="overall-result">
            <div class="title">
                <h2 class="start-quiz-title" title="Overall Results">Overall Results</h2>
                <button class="share" title="Share"><img src="media/share.png" alt="Share" width="20px" height="20px"></button>
            </div>
            <div class="quiz-accuracy">
                <p class="quiz-accuracy" style="background: linear-gradient(to right, #5ced73 <?php echo $quiz_accuracy ?>%, red 0);"><?php echo $comment ?></p>
                <p class="percentage"><?php echo $quiz_accuracy ?>%</p>
            </div>
            <h2 class="title1">Past Attempts</h2>
            <div class="past-attempt">
                <table class="attempt">
                    <tr>
                        <th>No.</th>
                        <th colspan="3">Score</th>
                        <th>Time Taken</th>
                    </tr>

                </table>
                <button class="MoreResult" title="Show More" onclick="MoreResult()"><img src="media/down.png" alt="Show More" width="30px" height="30px"></button>
            </div> 
        </section>
        
        <button class="start-quiz" title="Start Quiz" onclick="start()"><img src="media/run.png" alt="Run" width="35px" height="35px"><?php echo $btntext; ?></button>

        <script>
            //Check Overflow
            let desc = document.getElementsByClassName("start-quiz-desc")[0];
            let moretext = document.getElementsByClassName("MoreText")[0];

            function check_overflow() {
                let group = ["start-quiz-title", "start-quiz-editor", "tag-quiz"]
                for (let i = 0; i < group.length; i++) {
                    let overflow = document.getElementsByClassName(group[i]);
                    for (let e = 0; e < overflow.length; e++) {
                        if (overflow[e].scrollWidth > overflow[e].clientWidth){
                            overflow[e].setAttribute("title", overflow[e].textContent);
                        }else{
                            overflow[e].removeAttribute("title");
                        }
                    }
                }

                desc.style.display = "-webkit-box";
                if (desc.scrollHeight <= desc.clientHeight){
                    moretext.style.display = "none";
                }else{
                    moretext.style.display = "block";
                }
            }

            function MoreText() {
                moretext.style.display = "none";
                desc.style.display = "block";
            }

            check_overflow();
            window.addEventListener("resize", check_overflow)

            //Pass Attempt
            let moreresult = document.getElementsByClassName("MoreResult")[0];
            let area = document.getElementsByClassName("attempt")[0];
            area.innerHTML += `<?php
            if($comment == "No Attempts"){
                $total = 0;
                $content = '';
                echo $content;
            }else{
                if($quiz_type == "assessment" || $quiz_type == "test"){
                    $total = 1;
                    $content = '<tr>
                        <th>1</th>
                        <td class="score1"><div class="result" style="background: linear-gradient(to right, #5ced73 '.$quiz_accuracy.'%, red 0);"></div></td>
                        <td class="score2">'.$quiz_accuracy.'%</td>
                        <td class="score2">'.$score.'/'.$total_question.'</td>
                        <td>'.$time_taken.'</td>
                    </tr>';
                    echo $content;

                }else if($quiz_type == "exercise"){
                    $total = count($session_id);
                    $time_taken = '-';

                    for($i = 0; $i < 3 && $i < $total; $i++){
                        $attempt_accuracy = round(($score[$i] / $total_question) * 100, 2);
    
                        $content = '<tr>
                            <th>'.($i+1).'</th>
                            <td class="score1"><div class="result" style="background: linear-gradient(to right, #5ced73 '.$attempt_accuracy.'%, red 0);"></div></td>
                            <td class="score2">'.$attempt_accuracy.'%</td>
                            <td class="score2">'.$score[$i].'/'.$total_question.'</td>
                            <td>'.$time_taken.'</td>
                        </tr>';
                        echo $content;
                    }
                }
            }?>`;

            if(<?php echo $total ?> <= 3){
                moreresult.style.display = "none";
            }

            function MoreResult() {
                area.innerHTML += `<?php
                for($i = 3; $i < $total; $i++){
                    $attempt_accuracy = round(($score[$i] / $total_question) * 100, 2);

                    $content = '<tr>
                        <th>` + ('.$i.'+1) + `</th>
                        <td class="score1"><div class="result" style="background: linear-gradient(to right, #5ced73 '.$attempt_accuracy.'%, red 0);"></div></td>
                        <td class="score2">'.$attempt_accuracy.'%</td>
                        <td class="score2">'.$score[$i].'/'.$total_question.'</td>
                        <td>'.$time_taken.'</td>
                    </tr>';
                    echo $content;
                }?>`;
                moreresult.style.display = "none";
            }


            //Functions
            function back() {
                <?php 
                if($quiz_type == "assessment"){
                    echo 'window.location.href = "stu-home.php"';
                }else if($quiz_type == "test"){
                    echo 'window.location.href = "stu-subject.php"';
                }else if($quiz_type == "exercise"){
                    echo 'window.location.href = "stu-folder.php"';
                }
                ?>
                
            }

            function start(){
                <?php $_SESSION['btntext'] = $btntext; ?>
                window.location.href = "question.php";
            }

            //Animations
            let startquiz = document.getElementById("start-quiz");
            let overallresult = document.getElementById("overall-result");
            let buttonquiz = document.getElementsByClassName("start-quiz")[0];
            display2(startquiz);
            setTimeout(() => {
                display2(overallresult);
            }, 800);
            setTimeout(() => {
                buttonquiz.style.opacity = "1";
            }, 1600)

            function display2(element) {
                element.style.opacity = "1";
                element.style.transform = "scale(1)";
            }
        </script>
    </main>

</body>
</html>