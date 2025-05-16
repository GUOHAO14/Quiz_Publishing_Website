<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    session_start();
    header("Location: index.php");
    session_write_close();
    exit();
}
include("conn.php");

//Will be trigged after start button
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["quiz_id"])) {
    $_SESSION["quiz_id"] = $_POST["quiz_id"];

    if (isset($_POST["subject_id"])) {
        $_SESSION["subject_id"] = $_POST["subject_id"];
    }
    if (isset($_POST["course_id"])) {
        $_SESSION["course_id"] = $_POST["course_id"];
    }
    if (isset($_POST["folder_id"])) {
        $_SESSION["folder_id"] = $_POST["folder_id"];
    }

    header("Location: start-quiz.php");
    exit();
}


// Will be trigged after tosubject()
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subject_id"])) {
    $_SESSION["subject_id"] = $_POST["subject_id"];
    header("Location: stu-subject.php");
    exit();
}

$class_id = $_SESSION["class_id"];
$student_id = $_SESSION["student_id"];

$sql = "SELECT * FROM class WHERE class_id LIKE '".$class_id."'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$class_name = $row['class_name'];
$grade = $row['grade'];
$year = $row['year'];
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
        <h1 class="home">Class - 
            <?php
            echo $grade; echo $class_name;
            ?>
        </h1>
        <section id="FT">
            <div class="title">
                <h2 class="title">Features Today</h2>
                <a href="" class="info" title="About Features Today"><img src="media/info.png" alt="About Features Today" width="25px" height="25px"></a>
            </div>
            
            <div class="quiz-area1">
                
            </div>
        </section>

        <section id="RK">
            <div class="title">
                <h2 class="title">Ranked Assessment</h2>
                <a href="" class="info" title="About Ranked Assessment"><img src="media/info.png" alt="About Ranked Assessment" width="25px" height="25px"></a>
            </div>
            
            <div class="quiz-area1">
                
            </div>
            <button class="ShowMore" title="Show More" onclick="ShowMore()"><img src="media/down.png" alt="Show More" width="30px" height="30px"></button>
        </section>

        <section id="subject">
            <div class="title">
                <h2 class="title">Class Materials & Quizzes</h2>
                <a href="" class="info" title="About Subject"><img src="media/info.png" alt="About Subject" width="25px" height="25px"></a>
            </div>
            <p class="title"><i>Choose a subject</i></p>
            
            <div class="quiz-area2">
                
            </div>
        </section>
    </main>

    <footer>
        
    </footer>

    <script>
        //Feature Today
        let area_FT = document.getElementsByClassName("quiz-area1")[0];
        area_FT.innerHTML += `<?php
        //Random 2 Assessment
        $sql = "SELECT * FROM assessment WHERE grade = '".$grade."' AND year = '".$year."'";
        $result = mysqli_query($conn, $sql);

        while($row = mysqli_fetch_array($result)){
            $assess_idr1[] = $row[0];
            $subject_idr1[] = $row[3];
            $quiz_idr1[] = $row[4];
        }

        if (count($assess_idr1) < 2) {
            $num1 = count($assess_idr1);
        } else {
            $num1 = 2;
        }

        if($num1 != 0){
            $randomKey = array_rand($assess_idr1, $num1);
            if ($num1 == 1) {
                $randomKey = [$randomKey];
            }
            foreach ($randomKey as $key) {
                $assess_id1[] = $assess_idr1[$key];
                $subject_id1[] = $subject_idr1[$key];
                $quiz_id1[] = $quiz_idr1[$key];
            }

            for($i = 0; $i < $num1; $i++){
                $sql = "SELECT * FROM subject WHERE subject_id = '".$subject_id1[$i]."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $subject_name = $row[1];
    
                $sql = "SELECT * FROM quiz WHERE quiz_id = '".$quiz_id1[$i]."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $quiz_name = $row[1];
                $creation_date = $row[4];
                $background = '';
                $quiz_editor = 'All Admin';
    
                if($creation_date <= date("Y-m-d") && $creation_date >= date("Y-m-d", strtotime("-1 week"))){
                    $tag = '<p class="tag-a1">New</p>';
                } else {
                    $tag = '';
                }
    
                $sql = "SELECT * FROM question WHERE quiz_id = '".$quiz_id1[$i]."'";
                $result = mysqli_query($conn, $sql);
                $total_question = mysqli_num_rows($result);
    
                $sql = "SELECT * FROM assessment_session WHERE student_id = '".$student_id."' AND assess_id = '".$assess_id1[$i]."'";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) <= 0){
                    $quiz_accuracy1 = 'No Attempted';
                    $quiz_accuracy2 = 'No Attempted';
                } else {
                    $row = mysqli_fetch_array($result);
                    $quiz_accuracy1 = $row[2];
                    $score = round(($quiz_accuracy1 / 100) * $total_question, 0);
                    $quiz_accuracy2 = $quiz_accuracy1.'% Accuracy';
                }
        
                $content = '
                <div class="a">
                    <div class="a1" '.$background.'>
                        <div class="a1-1">
                            '.$tag.'
                        </div>
                        <div class="a1-1">
                            <p class="tag-a2">'.$total_question.' Questions</p>
                            <p class="tag-a2">'.$subject_name.'</p>
                        </div>
                    </div>
                    <div class="a2">
                        <h3 class="name-a">'.$quiz_name.'</h3>
                        <p class="editor">Edited by: '.$quiz_editor.'</p>
                        <p class="accuracy" style="background: linear-gradient(to right, #5ced73 '.$quiz_accuracy1.'%, red 0);">'.$quiz_accuracy2.'</p>
                        <button class="start" title="Start Quiz" onclick="toquiz('.$quiz_id1[$i].')">START</button>
                    </div>
                </div>';
                echo $content;
            }
        }

        //Random 3 Test
        $sql = "SELECT * FROM course WHERE class_id = '".$class_id."'";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            $course_id2[] = $row[0];
        }

        for($i = 0; $i < count($course_id2); $i++){
            $sql = "SELECT * FROM test WHERE course_id = '".$course_id2[$i]."'";
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_array($result)){
                $test_idr2[] = $row[0];
                $quiz_idr2[] = $row[2];
            }
        }

        if (count($test_idr2) < 3) {
            $num2 = count($test_idr2);
        } else {
            $num2 = 3;
        }

        if($num2 != 0){
            $randomKey = array_rand($test_idr2, $num2);
            if ($num2 == 1) {
                $randomKey = [$randomKey];
            }
            foreach ($randomKey as $key) {
                $test_id2[] = $test_idr2[$key];
                $quiz_id2[] = $quiz_idr2[$key];
            }

            for($i = 0; $i < $num2; $i++){
                $sql = "SELECT * FROM test WHERE test_id = '".$test_id2[$i]."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $course_ida = $row[1];
                $sql = "SELECT * FROM course WHERE course_id = '".$course_ida."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $subject_ida = $row[1];
                $sql = "SELECT * FROM subject WHERE subject_id = '".$subject_ida."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $subject_name = $row[1];
                
                $sql = "SELECT * FROM quiz WHERE quiz_id = '".$quiz_id2[$i]."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $quiz_name = $row[1];
                $creation_date = $row[4];
                $background = '';

                if($creation_date <= date("Y-m-d") && $creation_date >= date("Y-m-d", strtotime("-1 week"))){
                    $tag = '<p class="tag-a1">New</p>';
                } else {
                    $tag = '';
                }

                $sql = "SELECT * FROM question WHERE quiz_id = '".$quiz_id2[$i]."'";
                $result = mysqli_query($conn, $sql);
                $total_question = mysqli_num_rows($result);

                $sql = "SELECT * FROM test_session WHERE student_id = '".$student_id."' AND test_id = '".$test_id2[$i]."'";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) <= 0){
                    $quiz_accuracy1 = 'No Attempted';
                    $quiz_accuracy2 = 'No Attempted';
                } else {
                    $row = mysqli_fetch_array($result);
                    $quiz_accuracy1 = $row[2];
                    $score = round(($quiz_accuracy1 / 100) * $total_question, 0);
                    $quiz_accuracy2 = $quiz_accuracy1.'% Accuracy';
                }
                
                $sql = "SELECT * FROM course_teacher WHERE course_id = '".$course_ida."'";
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
        
                $content = '
                <div class="a">
                    <div class="a1" '.$background.'>
                        <div class="a1-1">
                            '.$tag.'
                        </div>
                        <div class="a1-1">
                            <p class="tag-a2">'.$total_question.' Questions</p>
                            <p class="tag-a2">'.$subject_name.'</p>
                        </div>
                    </div>
                    <div class="a2">
                        <h3 class="name-a">'.$quiz_name.'</h3>
                        <p class="editor">Edited by: '.$quiz_editor.'</p>
                        <p class="accuracy" style="background: linear-gradient(to right, #5ced73 '.$quiz_accuracy1.'%, red 0);">'.$quiz_accuracy2.'</p>
                        <button class="start" title="Start Quiz" onclick="textquiz('.$quiz_id2[$i].', '.$subject_ida.', '.$course_ida.')">START</button>
                    </div>
                </div>';
                echo $content;
            }
        }

        //Random 3 Exercise
        for($i = 0; $i < count($course_id2); $i++){
            $sql = "SELECT * FROM folder WHERE course_id = '".$course_id2[$i]."'";
            $result = mysqli_query($conn, $sql);
            $folder_id3 = [];
            while($row = mysqli_fetch_array($result)){
                $folder_id3[] = $row[0];
            }
            for($j = 0; $j < count($folder_id3); $j++){
                $sql = "SELECT * FROM exercise WHERE folder_id = '".$folder_id3[$j]."'";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_array($result)){
                    $exe_idr3[] = $row[0];
                    $quiz_idr3[] = $row[2];
                }
            }
        }

        if (count($exe_idr3) < 3) {
            $num3 = count($exe_idr3);
        } else {
            $num3 = 3;
        }

        if($num3 != 0){
            $randomKey = array_rand($exe_idr3, $num3);
            if ($num3 == 1) {
                $randomKey = [$randomKey];
            }
            foreach ($randomKey as $key) {
                $exe_id3[] = $exe_idr3[$key];
                $quiz_id3[] = $quiz_idr3[$key];
            }

            for($i = 0; $i < $num3; $i++){
                $sql = "SELECT * FROM exercise WHERE exe_id = '".$exe_id3[$i]."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $folder_idb = $row[1];
                $sql = "SELECT * FROM folder WHERE folder_id = '".$folder_idb."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $course_idb = $row[2];
                $sql = "SELECT * FROM course WHERE course_id = '".$course_idb."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $subject_idb = $row[1];
                $sql = "SELECT * FROM subject WHERE subject_id = '".$subject_idb."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $subject_name = $row[1];

                $sql = "SELECT * FROM quiz WHERE quiz_id = '".$quiz_id3[$i]."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $quiz_name = $row[1];
                $creation_date = $row[4];
                $background = '';

                if($creation_date <= date("Y-m-d") && $creation_date >= date("Y-m-d", strtotime("-1 week"))){
                    $tag = '<p class="tag-a1">New</p>';
                } else {
                    $tag = '';
                }

                $sql = "SELECT * FROM question WHERE quiz_id = '".$quiz_id3[$i]."'";
                $result = mysqli_query($conn, $sql);
                $total_question = mysqli_num_rows($result);

                $sql = "SELECT * FROM exercise_session WHERE student_id = '".$student_id."' AND exe_id = '".$exe_id3[$i]."'";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) <= 0){
                    $quiz_accuracy1 = 'No Attempted';
                    $quiz_accuracy2 = 'No Attempted';
                } else {
                    while($row = mysqli_fetch_array($result)){
                        $score3[] = round(($row[1] / 100) * $total_question, 0);
                    }
                    $average = array_sum($score3) / count($score3);
                    $quiz_accuracy1 = round(($average / $total_question) * 100, 2);
                    $quiz_accuracy2 = $quiz_accuracy1.'% Accuracy';
                }

                $sql = "SELECT * FROM course_teacher WHERE course_id = '".$course_idb."'";
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

                $content = '
                <div class="c">
                    <div class="a1" '.$background.'>
                        <div class="a1-1">
                            '.$tag.'
                        </div>
                        <div class="a1-1">
                            <p class="tag-a2">'.$total_question.' Questions</p>
                            <p class="tag-a2">'.$subject_name.'</p>
                        </div>
                    </div>
                    <div class="a2">
                        <h3 class="name-a">'.$quiz_name.'</h3>
                        <p class="editor">Edited by: '.$quiz_editor.'</p>
                        <p class="accuracy" style="background: linear-gradient(to right, #5ced73 '.$quiz_accuracy1.'%, red 0);">'.$quiz_accuracy2.'</p>
                        <button class="start" title="Start Quiz" onclick="exequiz('.$quiz_id3[$i].', '.$subject_idb.', '.$course_idb.', '.$folder_idb.')">START</button>
                    </div>
                </div>';
                echo $content;
                
            }
        }
        ?> `;

        //Ranked Assessment
        let area_RK = document.getElementsByClassName("quiz-area1")[1];
        area_RK.innerHTML += `<?php
        $sql = "SELECT * FROM assessment WHERE grade = '".$grade."' AND year = '".$year."'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) <= 0){
            $total = 0;
            echo '<p class="no">No Assessment Yet</p>';
        } else {
            $total = mysqli_num_rows($result);

            while($row = mysqli_fetch_array($result)){
                $assess_id[] = $row[0];
                $subject_id[] = $row[3];
                $quiz_id[] = $row[4];
            }
            for($i = 0; $i < $total && $i < 8; $i++){
                
                $sql = "SELECT * FROM subject WHERE subject_id = '".$subject_id[$i]."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $subject_name = $row[1];

                $sql = "SELECT * FROM quiz WHERE quiz_id = '".$quiz_id[$i]."'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);
                $quiz_name = $row[1];
                $creation_date = $row[4];
                $background = '';

                if($creation_date <= date("Y-m-d") && $creation_date >= date("Y-m-d", strtotime("-1 week"))){
                    $tag = '<p class="tag-a1">New</p>';
                } else {
                    $tag = '';
                }

                $sql = "SELECT * FROM question WHERE quiz_id = '".$quiz_id[$i]."'";
                $result = mysqli_query($conn, $sql);
                $total_question = mysqli_num_rows($result);

                $sql = "SELECT * FROM assessment_session WHERE student_id = '".$student_id."' AND assess_id = '".$assess_id[$i]."'";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) <= 0){
                    $quiz_accuracy1 = 'No Attempted';
                    $quiz_accuracy2 = 'No Attempted';
                } else {
                    $row = mysqli_fetch_array($result);
                    $quiz_accuracy1 = $row[2];
                    $score = round(($quiz_accuracy1 / 100) * $total_question, 0);
                    $quiz_accuracy2 = $quiz_accuracy1.'% Accuracy';
                }
        
                $content = '
                <div class="a">
                    <div class="a1" '.$background.'>
                        <div class="a1-1">
                            '.$tag.'
                        </div>
                        <div class="a1-1">
                            <p class="tag-a2">'.$total_question.' Questions</p>
                            <p class="tag-a2">'.$subject_name.'</p>
                        </div>
                    </div>
                    <div class="a2">
                        <h3 class="name-a">'.$quiz_name.'</h3>
                        <p class="accuracy" style="background: linear-gradient(to right, #5ced73 '.$quiz_accuracy1.'%, red 0);">'.$quiz_accuracy2.'</p>
                        <button class="start" title="Start Quiz" onclick="toquiz('.$quiz_id[$i].')">START</button>
                    </div>
                </div>';
                echo $content;
            }
        }?> `;
        
        if(<?php echo $total ?> <= 8){
            document.getElementsByClassName("ShowMore")[0].style.display = "none";
        }
    
        //Subject
        let area_subject = document.getElementsByClassName("quiz-area2")[0];
        area_subject.innerHTML += `<?php
        $sql = "SELECT * FROM course WHERE class_id = '".$class_id."'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) <= 0){
            echo '<p class="no">No Subject Yet</p>';
        } else {
            while($row = mysqli_fetch_array($result)){
                $subject_id2[] = $row[1];
            }
            $repeat_subject = count($subject_id2);

            for($i = 0; $i < $repeat_subject; $i++){
                $sql = "SELECT * FROM subject WHERE subject_id = '".$subject_id2[$i]."'";
                $result = mysqli_query($conn, $sql);

                $row = mysqli_fetch_array($result);
                $subject_name = $row[1];
                $background = '';
                
                
                $content = '
                <div class="b" onclick="tosubject('.$subject_id2[$i].')" '.$background.'>
                    <div class="b1">
                        <a href="" class="more-b" title="More"><img src="media/more.png" alt="More" width="20px" height="20px"></a>
                    </div>
                    <h3 class="name-b">'.$subject_name.'</h3>
                </div>';
                echo $content;
            }
        }?> `;

        //Check Overflow
        let group = ["tag-a1","tag-a2", "name-a", "name-b"]
        for (let i = 0; i < group.length; i++) {
            let overflow = document.getElementsByClassName(group[i]);
            for (let e = 0; e < overflow.length; e++) {
                if (overflow[e].scrollWidth > overflow[e].clientWidth){
                    overflow[e].setAttribute("title", overflow[e].textContent);
                }
            }
        }

        overflow = document.getElementsByClassName("editor");
        for (let e = 0; e < overflow.length; e++) {
            if (overflow[e].scrollHeight > overflow[e].clientHeight){
                overflow[e].setAttribute("title", overflow[e].textContent);
            }
        }

        //Functions
        function toquiz(id) {
            let form = document.createElement("form");
            form.method = "POST";
            form.action = "stu-home.php";

            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "quiz_id";
            input.value = id;
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }

        function textquiz(id1, id2, id3) {
            let form = document.createElement("form");
            form.method = "POST";
            form.action = "stu-home.php";

            let input1 = document.createElement("input");
            input1.type = "hidden";
            input1.name = "quiz_id";
            input1.value = id1;
            form.appendChild(input1);

            let input2 = document.createElement("input");
            input2.type = "hidden";
            input2.name = "subject_id";
            input2.value = id2;
            form.appendChild(input2);

            let input3 = document.createElement("input");
            input3.type = "hidden";
            input3.name = "course_id";
            input3.value = id3;
            form.appendChild(input3);

            document.body.appendChild(form);
            form.submit();
        }

        function exequiz(id1, id2, id3, id4) {
            let form = document.createElement("form");
            form.method = "POST";
            form.action = "stu-home.php";

            let input1 = document.createElement("input");
            input1.type = "hidden";
            input1.name = "quiz_id";
            input1.value = id1;
            form.appendChild(input1);

            let input2 = document.createElement("input");
            input2.type = "hidden";
            input2.name = "subject_id";
            input2.value = id2;
            form.appendChild(input2);

            let input3 = document.createElement("input");
            input3.type = "hidden";
            input3.name = "course_id";
            input3.value = id3;
            form.appendChild(input3);

            let input4 = document.createElement("input");
            input4.type = "hidden";
            input4.name = "folder_id";
            input4.value = id4;
            form.appendChild(input4);

            document.body.appendChild(form);
            form.submit();
        }

        function tosubject(id) {
            let form = document.createElement("form");
            form.method = "POST";
            form.action = "stu-home.php";

            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "subject_id";
            input.value = id;
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }

        function ShowMore() {
            let ShowMore = document.getElementsByClassName("ShowMore")[0];

            ShowMore.style.transform = "scale(1.5) translateY(50px)";
            ShowMore.style.opacity = "0";
            setTimeout(() => {
                ShowMore.style.display = "none";
                area_RK.innerHTML += `<?php
                for($i = 8; $i < $total; $i++){
                    $sql = "SELECT * FROM subject WHERE subject_id = '".$subject_id[$i]."'";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($result);
                    $subject_name = $row[1];

                    $sql = "SELECT * FROM quiz WHERE quiz_id = '".$quiz_id[$i]."'";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($result);
                    $quiz_name = $row[1];
                    $creation_date = $row[4];
                    $background = '';                    

                    if($creation_date <= date("Y-m-d") && $creation_date >= date("Y-m-d", strtotime("-1 week"))){
                        $tag = '<p class="tag-a1">New</p>';
                    } else {
                        $tag = '';
                    }

                    $sql = "SELECT * FROM question WHERE quiz_id = '".$quiz_id[$i]."'";
                    $result = mysqli_query($conn, $sql);
                    $total_question = mysqli_num_rows($result);

                    $sql = "SELECT * FROM assessment_session WHERE student_id = '".$student_id."' AND assess_id = '".$assess_id[$i]."'";
                    $result = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($result) <= 0){
                        $quiz_accuracy1 = 'No Attempted';
                        $quiz_accuracy2 = 'No Attempted';
                    } else {
                        $row = mysqli_fetch_array($result);
                        $quiz_accuracy1 = $row[2];
                        $score = round(($quiz_accuracy1 / 100) * $total_question, 0);
                        $quiz_accuracy2 = $quiz_accuracy1.'% Accuracy';
                    }
                    
                    $content = '
                    <div class="a">
                        <div class="a1" '.$background.'>
                            <div class="a1-1">
                                '.$tag.'
                            </div>
                            <div class="a1-1">
                                <p class="tag-a2">'.$total_question.' Questions</p>
                                <p class="tag-a2">'.$subject_name.'</p>
                            </div>
                        </div>
                        <div class="a2">
                            <h3 class="name-a">'.$quiz_name.'</h3>
                            <p class="accuracy" style="background: linear-gradient(to right, #5ced73 '.$quiz_accuracy1.'%, red 0);">'.$quiz_accuracy2.'</p>
                            <button class="start" title="Start Quiz" onclick="toquiz('.$quiz_id[$i].')">START</button>
                        </div>
                    </div>';
                    echo $content;
                }?>`;
            }, 200);
        }

        //Animations
        let FT = document.getElementById("FT");
        let RK = document.getElementById("RK");
        let subject = document.getElementById("subject");

        function display(element) {
            let position = element.getBoundingClientRect();
            if(position.top < window.innerHeight && position.bottom > 0) {
                element.style.opacity = "1";
                element.style.transform = "scale(1)";
            }
        }

        display(FT);
        window.addEventListener("scroll", function(){
            display(FT);
            display(RK);
            display(subject);
        })
    </script>
</body>

</html>