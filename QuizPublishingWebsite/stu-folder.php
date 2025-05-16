<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    session_start();
    header("Location: index.php");
    session_write_close();
    exit();
}
include("conn.php");

// Will be trigged after toquiz()
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["quiz_id"])) {
    $_SESSION["quiz_id"] = $_POST["quiz_id"];
    header("Location: start-quiz.php");
    exit();
}

$class_id = $_SESSION["class_id"];
$student_id = $_SESSION["student_id"];
$subject_id = $_SESSION["subject_id"];
$course_id = $_SESSION["course_id"];
$folder_id = $_SESSION["folder_id"];

$sql = "SELECT * FROM class WHERE class_id LIKE '".$class_id."'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$class_name = $row[1];
$grade = $row[2];

$sql = "SELECT * FROM subject WHERE subject_id LIKE '".$subject_id."'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$subject_name = $row[1];

$sql = "SELECT * FROM folder WHERE folder_id LIKE '".$folder_id."'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$folder_name = $row[1];
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
            <h1 class="nav">></h1>
            <a href="stu-subject.php" class="main">
                <h1>
                    <?php
                    echo $subject_name;
                    ?>
                </h1>
            </a>
            <h1 class="nav">></h1>
            <h1 class="nav">
                <?php
                echo $folder_name;
                ?>
            </h1>
        </nav>

        <section id="exercise">
            <div class="title">
                <h2 class="title1">Exercises</h2>
                <a href="" class="info" title="About Exercises"><img src="media/info.png" alt="About Exercises" width="25px" height="25px"></a>
            </div>
            <div class="button-area">
                <button class="left" onclick="previous()"><img src="media/left.png" alt="Left" width="40px" height="40px"></button>
                <div class="quiz-area3">
                    
                </div>
                <button class="right" onclick="next()"><img src="media/right.png" alt="Right" width="40px" height="40px"></button>
            </div>
        </section>
        
    </main>

    <script>
        //Exercise
        let area_exercise = document.getElementsByClassName("quiz-area3")[0];
        let left = document.getElementsByClassName("left")[0];
        let right = document.getElementsByClassName("right")[0];
        
        area_exercise.innerHTML += `<?php
        $sql = "SELECT * FROM exercise WHERE folder_id = '".$folder_id."'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) <= 0){
            $total = 0;
            echo '<p class="no">No Exercise Yet</p>';
        } else {
            $total = mysqli_num_rows($result);

            while($row = mysqli_fetch_array($result)){
                $exe_id[] = $row[0];
                $quiz_id[] = $row[2];
            }
            for($i = 0; $i < $total; $i++){
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

                $sql = "SELECT * FROM exercise_session WHERE student_id = '".$student_id."' AND exe_id = '".$exe_id[$i]."'";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) <= 0){
                    $quiz_accuracy1 = 'No Attempted';
                    $quiz_accuracy2 = 'No Attempted';
                } else {
                    while($row = mysqli_fetch_array($result)){
                        $score[] = round(($row[1] / 100) * $total_question, 0);
                    }
                    $average = array_sum($score) / count($score);
                    $quiz_accuracy1 = round(($average / $total_question) * 100, 2);
                    $quiz_accuracy2 = $quiz_accuracy1.'% Accuracy';
                }
                
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

                $content = '
                <div class="c">
                    <div class="a1" '.$background.'>
                        <div class="a1-1">
                            '.$tag.'
                        </div>
                        <div class="a1-1">
                            <p class="tag-a2">'.$total_question.' Questions</p>
                        </div>
                    </div>
                    <div class="a2">
                        <h3 class="name-a">'.$quiz_name.'</h3>
                        <p class="editor">Edited by: '.$quiz_editor.'</p>
                        <p class="accuracy" style="background: linear-gradient(to right, #5ced73 '.$quiz_accuracy1.'%, red 0);">'.$quiz_accuracy2.'</p>
                        <button class="start" title="Start Quiz" onclick="toquiz('.$quiz_id[$i].')">START</button>
                    </div>
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
        function back() {
            window.location.href = "stu-subject.php";
        }

        function toquiz(id) {
            let form = document.createElement("form");
            form.method = "POST";
            form.action = "stu-folder.php";

            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "quiz_id";
            input.value = id;
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }

        //Animation1
        function check() {
            if(sleft == 0){
                left.style.pointerEvents = "none";
                left.style.opacity = "0";
                setTimeout(() => {
                    left.style.visibility = "hidden";
                }, 500);
            }
            else{
                left.style.opacity = "1";
                left.style.visibility = "visible";
                left.style.pointerEvents = "auto";
            }

            if(sright <= area_exercise.clientWidth){
                right.style.pointerEvents = "none";
                right.style.opacity = "0";
                setTimeout(() => {
                    right.style.visibility = "hidden";
                }, 500);
            }
            else{
                right.style.opacity = "1";
                right.style.visibility = "visible";
                right.style.pointerEvents = "auto";
            }
        }

        function next() {
            move -= 200;
            sleft += 200;
            sright -= 200;
            check();
            show();
        }

        function previous() {
            move += 200;
            sleft -= 200;
            sright += 200;
            check();
            show();
        }

        function show() {
            for(let i = 0; i < <?php echo $total ?>; i++){
                quiz[i].style.transform = "translateX(" + move + "px)";
            }   
        }

        let move = 0;
        let quiz = document.getElementsByClassName("c");
        let sleft = 0;
        let sright = area_exercise.scrollWidth;

        check();
        window.addEventListener("resize", check)

        //Animation2
        let exercise = document.getElementById("exercise");
        display(exercise);
        
        function display(element) {
            element.style.opacity = "1";
            element.style.transform = "scale(1)";
        }
    </script>
    
</body>
</html>