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

$time_seconds = $_POST['timeTaken'];
if($time_seconds == null){
    $time_taken = '';
}else{
    $time_taken = gmdate("H:i:s", $time_seconds);
}

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

$total_question = count($quest_id);
for($i = 0; $i < $total_question; $i++){
    $u_ans[] = $_POST[$quest_id[$i]];
    $sql = "SELECT * FROM answer_selection WHERE quest_id LIKE '".$quest_id[$i]."'";
    $result = mysqli_query($conn, $sql);
    $select_id = [];
    $s_text = [];
    $s_accuracy = [];
    while($row = mysqli_fetch_array($result)){
        $select_id[] = $row[0];
        $s_text[] = $row[1];
        $s_accuracy[] = $row[2];
    }

    if(count($select_id) > 1){
        for($j = 0; $j < count($select_id); $j++){
            if($s_accuracy[$j] == 1){
                $c_ans = $select_id[$j];
            }
        }
        if($u_ans[$i] == $c_ans){
            $v_ans[$quest_id[$i]] = 1;
        }else{
            $v_ans[$quest_id[$i]] = 0;
        }
        
    }else if(count($select_id) == 1){
        if($u_ans[$i] == $s_text[0]){
            $v_ans[$quest_id[$i]] = 1;
        }else{
            $v_ans[$quest_id[$i]] = 0;
        }
    }
}

$score = count(array_filter($v_ans, function ($value) {
    return $value == 1;
}));

$accuracy = round(($score / $total_question) * 100, 2);

if($quiz_type == "assessment"){
    $sql = "SELECT * FROM assessment WHERE quiz_id LIKE '".$quiz_id."'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $assess_id = $row[0];

    $sql = "SELECT * FROM assessment_session WHERE student_id = '".$student_id."' AND assess_id = '".$assess_id."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) <= 0){
        $pa_accuracy = "no attempt";
        $pa_time_taken = "";
        $pa_score = "";
    } else {
        $row = mysqli_fetch_array($result);
        $pa_accuracy = $row[2];
        $pa_time_taken = $row[3];
        $pa_score = round(($pa_accuracy / 100) * $total_question, 0);
    }

    $sql = "INSERT INTO assessment_session (assess_id, student_id, score, time_taken) VALUES ('$assess_id', '$student_id', '$accuracy', '$time_taken');";
    mysqli_query($conn, $sql);

}else if($quiz_type == "test"){
    $sql = "SELECT * FROM test WHERE quiz_id LIKE '".$quiz_id."'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $test_id = $row[0];

    $sql = "SELECT * FROM test_session WHERE student_id = '".$student_id."' AND test_id = '".$test_id."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) <= 0){
        $pa_accuracy = "no attempt";
        $pa_time_taken = "";
        $pa_score = "";
    } else {
        $row = mysqli_fetch_array($result);
        $pa_accuracy = $row[2];
        $pa_time_taken = $row[3];
        $pa_score = round(($pa_accuracy / 100) * $total_question, 0);
    }

    $sql = "INSERT INTO test_session (test_id, student_id, score, time_taken) VALUES ('$test_id', '$student_id', '$accuracy', '$time_taken');";
    mysqli_query($conn, $sql);

}else if($quiz_type == "exercise"){
    $sql = "SELECT * FROM exercise WHERE quiz_id LIKE '".$quiz_id."'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $exe_id = $row[0];

    $sql = "SELECT * FROM exercise_session WHERE student_id = '".$student_id."' AND exe_id = '".$exe_id."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) <= 0){
        $pa_accuracy = "no attempt";
        $pa_time_taken = "";
        $pa_score = "";
    } else {
        $pa_score = [];
        while($row = mysqli_fetch_array($result)){
            $session_id[] = $row[0];
            $pa_accuracy[] = $row[1];
        }
        for($i = 0; $i < count($session_id); $i++){
            $pa_score[] = round(($pa_accuracy[$i] / 100) * $total_question, 0);
        }
    }

    $sql = "INSERT INTO exercise_session (exe_id, student_id, score) VALUES ('$exe_id', '$student_id', '$accuracy');";
    mysqli_query($conn, $sql);
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

<!-- add confrimation for header navigation or don't let user exit -->

<body>
    <?php include "bgVideo.php"?>
    <?php include("studentHeader.php"); ?>

    <main>
        <button class="exit2" title="Exit" onclick="exit()"><img src="media/exit.png" alt="Exit" width="40px" height= "40px"></button>

        <section id="end-quiz">
            <div class="end-quiz-title">
                <h2 class="end-quiz-title">Congratulations ! ! !</h2>
                <p class="end-quiz-title">You successfully completed the quiz.</p>
            </div>
            
            <div class="title">
                <h2 class="result-title">Results</h2>
                <button class="share" title="Share"><img src="media/share.png" alt="Share" width="20px" height="20px"></button>
            </div>
            <div class="quiz-accuracy">
                <p class="quiz-accuracy" style="background: linear-gradient(to right, #5ced73 <?php echo $accuracy ?>%, red 0);">Well Done !</p>
                <p class="percentage"><?php echo $accuracy ?>%</p>
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

            <button class="re-attempt" title="Re-Attempt" onclick="start()"><img src="media/run.png" alt="Run" width="30px" height="30px">Re-Attempt</button>

        </section>

        <section id="review">
            <div class="title">
                <h2 class="result-title">Review Questions</h2>
            </div>

        </section>


        <script>
            //Review Questions
            let area_question = document.getElementById("review");
            area_question.innerHTML += `<?php
            for($i = 0; $i < $total_question; $i++){

                $u_ans[] = $_POST[$quest_id[$i]];
                $sql = "SELECT * FROM answer_selection WHERE quest_id LIKE '".$quest_id[$i]."'";
                $result = mysqli_query($conn, $sql);
                $select_id = [];
                $s_text = [];
                $s_accuracy = [];
                while($row = mysqli_fetch_array($result)){
                    $select_id[] = $row[0];
                    $s_text[] = $row[1];
                    $s_accuracy[] = $row[2];
                }
                
                if(count($select_id) > 1){
                    if($v_ans[$quest_id[$i]] == 0){
                        echo '
                        <div class="bar">
                            <div class="barw"></div>
                            <div class ="review-area">
                                <p class="question">
                                    '.($i+1).') '.$q_text[$i].'
                                </p>';
                            for($j = 0; $j < count($select_id); $j++){
                                if($s_accuracy[$j] == 1){
                                    echo '
                                    <div class="mcq">
                                        <img src="media/correct.png" alt="Check Box" width="15px" height= "15px">
                                        <p>'.$s_text[$j].'</p>
                                    </div>';
                                }else if($u_ans[$i] == $select_id[$j]){
                                    echo '
                                    <p class="u-ans-w">Your Answer</p>
                                    <div class="mcq">
                                        <img src="media/wrong.png" alt="Check Box" width="15px" height= "15px">
                                        <p>'.$s_text[$j].'</p>
                                    </div>';
                                }else{
                                    echo '
                                    <div class="mcq">
                                        <img src="media/box.png" alt="Check Box" width="15px" height= "15px">
                                        <p>'.$s_text[$j].'</p>
                                    </div>';
                                }
                            }
                        echo '</div>
                        </div>';
                    }else if($v_ans[$quest_id[$i]] == 1){
                        echo '
                        <div class="bar">
                            <div class="barc"></div>
                            <div class ="review-area">
                                <p class="question">
                                    '.($i+1).') '.$q_text[$i].'
                                </p>';
                            for($j = 0; $j < count($select_id); $j++){
                                if($s_accuracy[$j] == 1){
                                    echo '
                                    <p class="u-ans-c">Your Answer</p>
                                    <div class="mcq">
                                        <img src="media/correct.png" alt="Check Box" width="15px" height= "15px">
                                        <p>'.$s_text[$j].'</p>
                                    </div>';
                                }else{
                                    echo '
                                    <div class="mcq">
                                        <img src="media/box.png" alt="Check Box" width="15px" height= "15px">
                                        <p>'.$s_text[$j].'</p>
                                    </div>';
                                }
                            }
                        echo '</div>
                        </div>';  
                    }
                }else if(count($select_id) == 1){
                    if($v_ans[$quest_id[$i]] == 0){
                        echo '
                        <div class="bar">
                            <div class="barw"></div>
                            <div class ="review-area">
                                <p class="question">
                                    '.($i+1).') '.$q_text[$i].'
                                </p>
                                <p class="u-ans-w">Your Answer</p>
                                <p class="text">'.$u_ans[$i].'</p>
                                <p class="u-ans-c">Correct Answer</p>
                                <p class="text">'.$s_text[0].'</p>
                            </div>
                        </div>';
                    }else if($v_ans[$quest_id[$i]] == 1){
                        echo '
                        <div class="bar">
                            <div class="barc"></div>
                            <div class ="review-area">
                                <p class="question">
                                    '.($i+1).') '.$q_text[$i].'
                                </p>
                                <p class="u-ans-c">Your Answer</p>
                                <p class="text">'.$u_ans[$i].'</p>
                                <p class="u-ans-c">Correct Answer</p>
                                <p class="text">'.$s_text[0].'</p>
                            </div>
                        </div>';
                    }
                }        
            }?>`;


            //Pass Attempt
            let moreresult = document.getElementsByClassName("MoreResult")[0];
            let area = document.getElementsByClassName("attempt")[0];
            <?php
            if($pa_accuracy == "no attempt" || $pa_accuracy[0] == "no attempt"){
                echo 'moreresult.style.display = "none";';
                
            }else{
                if($quiz_type == "assessment"){
                    $total = 1;
                }else if($quiz_type == "test"){
                    $total = 1;
                }else if($quiz_type == "exercise"){
                    $total = count($session_id);
                    $pa_time_taken = '-';
                }
            }?>

            function MoreResult() {
                area.innerHTML += `<?php
                if($quiz_type == "assessment" || $quiz_type == "test"){                    
                    $content = '<tr>
                        <th>1</th>
                        <td class="score1"><div class="result" style="background: linear-gradient(to right, #5ced73 '.$pa_accuracy.'%, red 0);"></div></td>
                        <td class="score2">'.$pa_accuracy.'%</td>
                        <td class="score2">'.$pa_score.'/'.$total_question.'</td>
                        <td>'.$pa_time_taken.'</td>
                    </tr>';
                    echo $content;
                    
                }else if($quiz_type == "exercise"){
                    for($i = 0; $i < $total; $i++){                        
                        $content = '<tr>
                            <th>'.($i+1).'</th>
                            <td class="score1"><div class="result" style="background: linear-gradient(to right, #5ced73 '.$pa_accuracy[$i].'%, red 0);"></div></td>
                            <td class="score2">'.$pa_accuracy[$i].'%</td>
                            <td class="score2">'.$pa_score[$i].'/'.$total_question.'</td>
                            <td>'.$pa_time_taken.'</td>
                        </tr>';
                        echo $content;
                    }
                }
                ?>`;
                moreresult.style.display = "none";
            }

            //Functions
            function exit() {
                if (!window.confirm("Are you sure you want to exit the quiz?")) {
                    return;
                }else{
                    sessionStorage.removeItem("answer1");
                    sessionStorage.removeItem("answer2");
                    window.location.href = "start-quiz.php";
                }
            }

            function start(){
                sessionStorage.removeItem("answer1");
                sessionStorage.removeItem("answer2");
                window.location.href = "start-quiz.php";
            }

            //Animations
            let endquiz = document.getElementById("end-quiz");
            let RA = document.getElementsByClassName("bar");

            function display(element) {
                let position = element.getBoundingClientRect();
                if(position.top < window.innerHeight && position.bottom > 0) {
                    element.style.opacity = "1";
                    element.style.transform = "scale(1)";
                }
            }

            display(endquiz);
            for (let i = 0; i < RA.length; i++) {
                display(RA[i]);
            }

            window.addEventListener("scroll", function(){
                for (let i = 0; i < RA.length; i++) {
                    display(endquiz);
                    display(RA[i]);
                }
            })

        </script>
    </main>

</body>
</html>