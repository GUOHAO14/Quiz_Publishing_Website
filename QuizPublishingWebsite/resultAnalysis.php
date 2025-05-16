<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['teacher_id'])) {
    header("Location: index.php");
    session_write_close();
    exit();
}

if (isset($_GET['id'])) {
    $quizId = (int)$_GET['id']; // Sanitize the input
    $_SESSION['quizResultId'] = $quizId;
    if (isset($_SESSION['admin_id'])) {
        header("Location: adminResultAnalysis.php");
    } else {
        header("Location: teacherResultAnalysis.php");
    }
} else {
    if (isset($_SESSION['quizResultId'])) {
        $quizId = (int)$_SESSION['quizResultId']; // Sanitize the input
    } else {
        die("Quiz ID is not provided.");
    }
}



include "conn.php";

//assuming quizId is posted here
$sql = "SELECT quiz_type FROM quiz WHERE quiz_id = '$quizId'";
$result = mysqli_query($conn, $sql);
$quizTypeRow = mysqli_fetch_array($result);
$quizType = $quizTypeRow['quiz_type'];

$metadataRow = 0;
$totalAvgScore = 0;
$totalAvgTime = 0;
$studentsAnswered = 0;
$studentsNotAnswered = 0;
$grade = "-";
$year = "-";
if ($quizType === "exercise") {
    //metadata of quiz for quiz info
    $sqlMetadata = 
    "SELECT 
        subject.subject_name AS subjectname, 
        class.grade AS classgrade, 
        class.class_name AS classname, 
        class.year AS classyear, 
        class.class_id, 
        exe_id, 
        folder_id, 
        folder_name, 
        table1.course_id, 
        quiz_name, 
        description, 
        creation_date
    FROM 
        course 
    JOIN 
        subject ON course.subject_id = subject.subject_id 
    JOIN 
        class ON course.class_id = class.class_id 
    JOIN 
        (SELECT 
            exe_id, 
            exercise.folder_id, 
            folder_name, 
            course_id, 
            quiz_name, 
            description, 
            creation_date
        FROM 
            exercise 
        JOIN 
            folder ON exercise.folder_id = folder.folder_id 
        JOIN quiz ON exercise.quiz_id = quiz.quiz_id
        WHERE 
            exercise.quiz_id = $quizId) as table1 
    ON course.course_id = table1.course_id;";
    $metadataResult = mysqli_query($conn, $sqlMetadata);
    $metadataRow = mysqli_fetch_array($metadataResult);

    //used by upcoming queries
    $exeId = (int) $metadataRow['exe_id'];
    $classId = (int) $metadataRow['class_id'];
    $className = $metadataRow['classname'];
    $classGrade = (int) $metadataRow['classgrade'];
    $classYear = (int) $metadataRow['classyear'];
    $class = $classGrade."-".$className."-".$classYear;

    //calculate average score
    $sql = 
    "SELECT 
        score
    FROM 
        exercise_session 
    WHERE exe_id = $exeId;";
    $totalAvgScore = mysqli_query($conn, $sql);

    //find number of students who have answered quiz
    $sql = 
    "SELECT DISTINCT
        student_id
    FROM 
        exercise_session 
    WHERE exe_id = $exeId;";
    $response = mysqli_query($conn, $sql);
    $studentsAnswered = mysqli_num_rows($response);

    //find all students in the class that are supposed to answer quiz
    //regardless answered or haven't answered quiz
    $sqlStudentInClass = "SELECT 
            student.user_id, 
            photo, 
            student.student_id, 
            username 
        FROM 
            student 
        JOIN 
            user
        ON 
            student.user_id = user.user_id
        JOIN 
            enrolment 
        ON 
            student.student_id = enrolment.student_id 
        WHERE 
            enrolment.class_id = $classId";
    $studentResults = mysqli_query($conn, $sqlStudentInClass);
    $studentsNotAnswered = mysqli_num_rows($studentResults);

    //put in list to be displayed later
    $students = [];
    while ($row = mysqli_fetch_array($studentResults)) {
        //find score of each student
        //calculate number of attempts of each student
        $studentId = (int)$row['student_id'];
        $sql = "SELECT score FROM exercise_session WHERE exe_id = $exeId AND student_id = $studentId;";
        $result = mysqli_query($conn, $sql);
        $attempts = mysqli_num_rows($result);
        $avgScore = 0;
        if ($attempts >= 1) {
            $counter = 0;
            while ($score = mysqli_fetch_array($result)) {
                $avgScore += $score['score'];
                $counter++;
            }
            $avgScore /= $counter;
            $avgScore = $avgScore;
        } else {
            $avgScore = "-";
        }
        //if student haven't answered, score set as dash
        
        //later handle null in js
        $students[] = [
            'no' => null, 
            'pfp' => $row['photo'],
            'username' => $row['username'],
            'score' => $avgScore,
            'attempts' => $attempts
        ];
    }   
} 
else if ($quizType === "test") {
    //different from exercise
    //test has 'time_taken' attribute, and no 'attempts' attribute
    
    //metadata of quiz for quiz info
    $sqlMetadata = 
    "SELECT 
        subject.subject_name AS subjectname, 
        class.grade AS classgrade, 
        class.class_name AS classname, 
        class.year AS classyear, 
        class.class_id, 
        test_id, 
        table1.course_id, 
        quiz_name, 
        description, 
        creation_date 
    FROM 
        course 
    JOIN 
        subject ON course.subject_id = subject.subject_id 
    JOIN 
        class ON course.class_id = class.class_id 
    JOIN 
        (SELECT 
            test_id, 
            course_id, 
            quiz_name, 
            description, 
            creation_date
        FROM 
            test 
        JOIN quiz ON test.quiz_id = quiz.quiz_id
        WHERE 
            test.quiz_id = $quizId) as table1 
    ON course.course_id = table1.course_id;";
    $metadataResult = mysqli_query($conn, $sqlMetadata);
    $metadataRow = mysqli_fetch_array($metadataResult);

    //for other sql use
    $testId = (int) $metadataRow['test_id'];
    $classId = (int) $metadataRow['class_id'];
    $classId = (int) $metadataRow['class_id'];
    $className = $metadataRow['classname'];
    $classGrade = (int) $metadataRow['classgrade'];
    $classYear = (int) $metadataRow['classyear'];
    $class = $classGrade."-".$className."-".$classYear;
    
    //calculate average score and time taken
    $sql = 
    "SELECT 
        score, 
        time_taken 
    FROM 
        test_session 
    WHERE test_id = $testId;";
    $totalAvgScore = mysqli_query($conn, $sql);
    $totalAvgTime = mysqli_query($conn, $sql);

    //find all students in the class that are supposed to answer quiz
    //regardless answered or not answered
    $sqlStudentInClass = "SELECT 
            student.user_id, 
            photo, 
            student.student_id, 
            username 
        FROM 
            student 
        JOIN 
            user
        ON 
            student.user_id = user.user_id
        JOIN 
            enrolment 
        ON 
            student.student_id = enrolment.student_id 
        WHERE 
            enrolment.class_id = $classId";
    $studentResults = mysqli_query($conn, $sqlStudentInClass);
    $studentsNotAnswered = mysqli_num_rows($studentResults);

    //put in list to be displayed later
    $students = [];
    $counter = 0;
    while ($row = mysqli_fetch_array($studentResults)) {
        //calculate attempts for each student
        $studentId = (int) $row['student_id'];
        echo "<script>console.log($studentId);</script>";
        $sql = "SELECT score, time_taken FROM test_session WHERE test_id = $testId AND student_id = $studentId;";
        $result = mysqli_query($conn, $sql);
        $finalTimeTaken = "";
        $score = "";
        if (mysqli_num_rows($result) == 1) {
            $output = mysqli_fetch_array($result);
            $timeTaken = $output['time_taken'];
            list($hours, $minutes, $seconds) = explode(':', $timeTaken);
            $timeInSeconds = ((int)$hours * 3600) + ((int)$minutes * 60) + (int)$seconds;
            $minutes = floor($timeInSeconds / 60);
            $seconds = round($timeInSeconds) % 60;
            $finalTimeTaken = $minutes."min ".$seconds."sec";
            $score = (float) $output['score'];
            $score = $score;
            $counter++;
        } else {
            $finalTimeTaken = "-";
            $score ="-";
        }
        //students who did no answer test, set time and score as dash
        
        //later handle null in js
        $students[] = [
            'no' => null, 
            'pfp' => $row['photo'],
            'username' => $row['username'],
            'score' => $score,
            'time' => $finalTimeTaken
        ];
    }
    //number of students who answered test
    $studentsAnswered = $counter;
}
else if ($quizType === "assessment") {
    //similar to test, where 'time_taken' attribute is needed, and no 'attempts' attribute
    //but doesn't need course and folder
    //need to retrieve grade and year for display

    //metadata of quiz for quiz info
    $sqlMetadata = 
    "SELECT 
        subject.subject_name AS subjectname, 
        assess_id, 
        grade, 
        year, 
        quiz_name, 
        description, 
        creation_date
    FROM 
        assessment 
    JOIN 
        subject ON assessment.subject_id = subject.subject_id 
    JOIN 
        quiz ON assessment.quiz_id = quiz.quiz_id;";
    $metadataResult = mysqli_query($conn, $sqlMetadata);
    $metadataRow = mysqli_fetch_array($metadataResult);

    //for other sql use
    $assessId = (int) $metadataRow['assess_id'];
    $grade = (int) $metadataRow['grade'];
    $year = (int) $metadataRow['year'];
    
    //calculate average score and time taken
    $sql = 
    "SELECT 
        score, 
        time_taken 
    FROM 
        assessment_session 
    WHERE assess_id = $assessId;";
    $totalAvgScore = mysqli_query($conn, $sql);
    $totalAvgTime = mysqli_query($conn, $sql);

    //find all students in the grade that are supposed to answer quiz
    //regardless answered or not answered
    $sqlStudentInClass = "SELECT 
            student.user_id, 
            photo, 
            student.student_id, 
            username, 
            class_id, 
            class_name, 
            year, 
            grade
        FROM 
            student 
        JOIN 
            user
        ON 
            student.user_id = user.user_id
        JOIN 
            (SELECT 
                enrolment.student_id, 
                class.class_id, 
                class_name, 
                year, 
                grade 
            FROM 
                enrolment
            JOIN 
                class
            ON 
                enrolment.class_id = class.class_id
            WHERE grade = $grade AND year = $year) as table1
        ON 
            student.student_id = table1.student_id;";
    $studentResults = mysqli_query($conn, $sqlStudentInClass);
    $studentsNotAnswered = mysqli_num_rows($studentResults);

    //put in list to be displayed later
    $students = [];
    $counter = 0;
    while ($row = mysqli_fetch_array($studentResults)) {
        //calculate attempts for each student
        $studentId = (int) $row['student_id'];
        $classGrade = (int) $row['grade'];
        $className = $row['class_name'];
        $class = $classGrade."-".$className;
        $sql = "SELECT score, time_taken FROM assessment_session WHERE assess_id = $assessId AND student_id = $studentId;";
        $result = mysqli_query($conn, $sql);
        $finalTimeTaken = "";
        $score = "";
        if (mysqli_num_rows($result) == 1) {
            $output = mysqli_fetch_array($result);
            $timeTaken = $output['time_taken'];
            list($hours, $minutes, $seconds) = explode(':', $timeTaken);
            $timeInSeconds = ((int)$hours * 3600) + ((int)$minutes * 60) + (int)$seconds;
            $minutes = floor($timeInSeconds / 60);
            $seconds = round($timeInSeconds) % 60;
            $finalTimeTaken = $minutes."min ".$seconds."sec";
            $score = (float) $output['score'];
            $score = $score;
            $counter++;
        } else {
            $finalTimeTaken = "-";
            $score ="-";
        }
        //students who did no answer test, set time and score as dash
        
        //later handle null in js
        $students[] = [
            'no' => null, 
            'pfp' => $row['photo'],
            'username' => $row['username'],
            'class' => $class, 
            'score' => $score,
            'time' => $finalTimeTaken
        ];
    }
    //number of students who answered test
    $studentsAnswered = $counter;
}
else {
    die("Error encountered. Reload the website or log in again.");
}

usort($students, function ($a, $b) {
    return $b['score'] <=> $a['score']; // Descending order
});

$jsonStudents = json_encode($students);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Analysis</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="resultAnalysis.css">
</head>
<body>
    <div id="contentContainer">
        <div id="contentHeader">
            <img id="back" onclick="window.history.back()" src="media/back.png" alt="Back">
            <h1 id="summaryHeading">Quiz Summary</h1>
        </div>
        <div id="quizInfo">
            <div>
                <h1><?php echo $metadataRow['quiz_name']?></h1>
            </div>
            <div>
                <p id="quizDescription"><span id="description"><?php echo $metadataRow['description']?></span><span id="seeMoreButton" onclick="moreDescription()">... See More</span></p>
            </div>
            <div id="quizMetadata">
                <div class="attribute" id="quizType">
                    <div class="attributeLabel" id="quizTypeLabel">Quiz Type</div>
                    <div class="attributeValue" id="quizTypeValue">
                        <?php echo $quizType?>
                    </div>
                </div>
                <div class="attribute" id="course">
                    <div class="attributeLabel" id="courseLabel">Course</div>
                    <div class="attributeValue" id="courseValue">
                        <?php if ($quizType === "exercise" || $quizType === "test") {
                             echo $metadataRow['course_id']." (".$metadataRow['subjectname'].", ".$metadataRow['classgrade']."-".$metadataRow['classname']."-".$metadataRow['classyear'];
                        }?>
                    </div>
                </div>
                <div class="attribute" id="folder">
                    <div class="attributeLabel" id="folderLabel">Folder</div>
                    <div class="attributeValue" id="folderValue">
                        <?php if ($quizType === "exercise") {
                             echo $metadataRow['folder_name'];
                        }?>
                    </div>
                </div>
                <div class="attribute" id="grade">
                    <div class="attributeLabel" id="gradeLabel">Folder</div>
                    <div class="attributeValue" id="gradeValue">
                        <?php if ($quizType === "assessment") {
                             echo $metadataRow['grade'];
                        }?>
                    </div>
                </div>
                <div class="attribute" id="year">
                    <div class="attributeLabel" id="yearLabel">Folder</div>
                    <div class="attributeValue" id="yearValue">
                        <?php if ($quizType === "assessment") {
                             echo $metadataRow['year'];
                        }?>
                    </div>
                </div>
                
            </div>
            <div id="averageScore">
                <h2>Average Score</h2>
                    <div id="scoreDisplay">
                        <div id="scoreBar">
                            <div id="greenBar"></div>
                        </div>
                        <div id="score">
                            <?php 
                            $sum = 0; $counter = 0; 
                            if (mysqli_num_rows($totalAvgScore) <= 0) {
                                echo "- %";
                            } else {
                                while ($row = mysqli_fetch_array($totalAvgScore)) {
                                    $score = (float) $row['score'];
                                    echo "<script>console.log($score)</script>";
                                    if ($score !== null) {
                                        $sum += $score;
                                        $counter++;
                                    }
                                } 
                                $sum /= $counter;
                                echo $sum."%";
                            }
                            ?>
                        </div>
                    </div>
            </div>
            <div id="completionAndTime">
                <div class="CTBox" id="completion">
                    <h3>Students Completed</h3>
                    <div class="CTText" id="studentCount"><?php echo $studentsAnswered?>/<?php echo $studentsNotAnswered?></div>
                </div>
                <div class="CTBox" id="time"> <!-- uses display: block/none; to hide & unhide -->
                    <h3>Average Time Taken</h3>
                    <div class="CTText" id="studentCount">
                        <?php 
                        if ($quizType !== "exercise") {
                            $timeInSeconds = 0; $averageTime = 0; $counter = 0; 
                            if (mysqli_num_rows($totalAvgTime) <= 0) {
                                echo "-";
                            } else {
                                while ($row = mysqli_fetch_array($totalAvgTime)) {
                                    $time = $row['time_taken'];
                                    list($hours, $minutes, $seconds) = explode(':', $time);
                                    $timeInSeconds += ((int)$hours * 3600) + ((int)$minutes * 60) + (int)$seconds;
                                    $counter++;
                                }
                                $averageTime = $timeInSeconds / $counter;
                                $minutes = floor($averageTime / 60);
                                $seconds = round($averageTime % 60);
                                echo $minutes."min ".$seconds."sec";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="studentResults">
            <h2 id="className">Results of Class 2A</h2>
            <table id="resultsTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Username</th>
                        <th id="class">Class</th>
                        <th>Average Score</th>
                        <th id="time2">Time Taken</th>
                        <th id="attempt">Attempt No.</th>
                    </tr>
                </thead>
                <tbody id="resultsTableBody">
                    <!-- Results will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        let score = <?php echo $sum?>;
        let quizType = "<?php echo $quizType?>";
        let fullClass = "<?php echo $class?>";

        if (quizType == "exercise") {
            document.getElementById("time").style.display = "none";
            document.getElementById("class").style.display = "none";
            document.getElementById("time2").style.display = "none";
            document.getElementById("grade").style.display = "none";
            document.getElementById("year").style.display = "none";

            document.getElementById("className").textContent = "Results of "+fullClass;
            
            let users = [];
            let num = 1;
            //without time, with attempts count
            //display results with php variables
            let students = <?php echo $jsonStudents; ?>;

            students.forEach(student => {
                let refinedScore = "-";
                if (student.score !== "-") {
                    refinedScore = student.score+"%";
                }
                users.push({
                    no: num++, 
                    pfp: student.pfp, //profile picture
                    username: student.username, 
                    score: refinedScore, 
                    attempts: student.attempts 
                });
            });

            //send users dic into this function
            function displayResults(filteredUsers) {
                const tableBody = document.getElementById('resultsTableBody');
                tableBody.innerHTML = '';

                filteredUsers.forEach((user, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td data-label="Column 1">${user.no}</td>
                        <td data-label="Column 2" style="display: flex; align-items: center;">
                            <div class="profileContainer">
                                <img id="profile" class="profile${user.no}" src="" alt="Profile Picture">
                            </div>
                            <ul class="noBullets">
                                <li>${user.username}</li>
                            </ul>
                        </td>
                        <td data-label="Column 3">${user.score}</td>
                        <td data-label="Column 4">${user.attempts}</td>
                    `;
                    if (user.attempts == "0")
                        row.style.backgroundColor = "rgb(255 108 108)";
                    else 
                        row.style.backgroundColor = "#84fe5f";
                    tableBody.appendChild(row);
                    let profileImage = document.querySelector(".profile"+user.no);
                    if (user.pfp == null) {
                        profileImage.src = "media/profileDefault.png";
                    } else {
                        profileImage.src = user.pfp;
                    }
                    profileImage.alt = "Profile Picture";
                });
            }

            displayResults(users);
        }
        else if (quizType == "test") {
            document.getElementById("folder").style.display = "none";
            document.getElementById("class").style.display = "none";
            document.getElementById("grade").style.display = "none";
            document.getElementById("year").style.display = "none";
            document.getElementById("attempt").style.display = "none";

            document.getElementById("className").textContent = "Results of Class "+fullClass;

            let users = [];
            let num = 1;

            //with time, no attempt
            //display results with php variables
            let students = <?php echo $jsonStudents; ?>;

            students.forEach(student => {
                users.push({
                    no: num++, 
                    pfp: student.pfp, //profile picture
                    username: student.username, 
                    score: student.score, 
                    time: student.time 
                });
            });

            //send users dic into this function
            function displayResults(filteredUsers) {
                const tableBody = document.getElementById('resultsTableBody');
                tableBody.innerHTML = '';

                filteredUsers.forEach((user, index) => {
                    let originalScore = user.score;
                    let refinedScore = "-";
                    if (originalScore !== "-") {
                        refinedScore = originalScore+"%";
                    }
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td data-label="Column 1">${user.no}</td>
                        <td data-label="Column 2" style="display: flex; align-items: center; border-radius: 3px;">
                            <div class="profileContainer">
                                <img id="profile" class="profile${user.no}" src="" alt="Profile Picture">
                            </div>
                            <ul class="noBullets">
                                <li>${user.username}</li>
                            </ul>
                        </td>
                        <td data-label="Column 3">${refinedScore}</td>
                        <td data-label="Column 4">${user.time}</td>
                    `;
                    if (originalScore === "-")
                        row.style.backgroundColor = "rgb(255 108 108)";
                    else 
                        row.style.backgroundColor = "#84fe5f";
                    tableBody.appendChild(row);
                    let profileImage = document.querySelector(".profile"+user.no);
                    if (user.pfp == null) {
                        profileImage.src = "media/profileDefault.png";
                    } else {
                        profileImage.src = user.pfp;
                    }
                    profileImage.alt = "Profile Picture";
                });
            }

            displayResults(users);

        } else if (quizType == "assessment") {
            document.getElementById("folder").style.display = "none";
            document.getElementById("course").style.display = "none";
            document.getElementById("attempt").style.display = "none";


            let gradeDisplay = "<?php echo $grade?>";
            let yearDisplay = "<?php echo $year?>";
            document.getElementById("className").textContent = "Results of Students of Grade "+gradeDisplay+" ("+yearDisplay+")";

            let users = [];
            let num = 1;

            //with time and student class, no attempts
            //display results with php variables
            let students = <?php echo $jsonStudents; ?>;

            students.forEach(student => {
                let refinedScore = "-";
                if (student.score !== "-") {
                    refinedScore = student.score+"%";
                }
                users.push({
                    no: num++, 
                    pfp: student.pfp, //profile picture
                    class: student.class, 
                    username: student.username, 
                    score: refinedScore, 
                    time: student.time 
                });
            });

            //send users dic into this function
            function displayResults(filteredUsers) {
                const tableBody = document.getElementById('resultsTableBody');
                tableBody.innerHTML = '';

                filteredUsers.forEach((user, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td data-label="Column 1">${user.no}</td>
                        <td data-label="Column 2" style="display: flex; align-items: center;">
                            <div class="profileContainer">
                                <img id="profile" class="profile${user.no}" src="" alt="Profile Picture">
                            </div>
                            <ul class="noBullets">
                                <li>${user.username}</li>
                            </ul>
                        </td>
                        <td data-label="Column 3">${user.class}</td>
                        <td data-label="Column 4">${user.score}</td>
                        <td data-label="Column 5">${user.time}</td>
                    `;
                    if (user.score === "-")
                        row.style.backgroundColor = "rgb(255 108 108)";
                    else 
                        row.style.backgroundColor = "#84fe5f";
                    tableBody.appendChild(row);
                    let profileImage = document.querySelector(".profile"+user.no);
                    if (user.pfp == null) {
                        profileImage.src = "media/profileDefault.png";
                    } else {
                        profileImage.src = user.pfp;
                    }
                    profileImage.alt = "Profile Picture";
                });
            }

            displayResults(users);
        }

        document.getElementById("greenBar").style.width = score+"%";
        
        //see more or see less for long description
        function showDescription() {
            let description = document.getElementById("description");
            let descriptionContent = description.textContent;
            if (descriptionContent.length > 220) {
                shortText = descriptionContent.substr(0, 260);
                description.textContent = shortText;
                document.getElementById("seeMoreButton").style.visibility = "visible";
                document.getElementById("seeMoreButton").textContent = "... See More";
            }
            return descriptionContent;
        }

        let descriptionText = showDescription();

        function moreDescription() {
            let seeMore = document.getElementById("seeMoreButton");
            let text = seeMore.textContent;
            if (text === "... See More") {
                document.getElementById("description").textContent = descriptionText;
                document.getElementById("seeMoreButton").textContent = "... See Less";
            } else {
                showDescription();
            }
        }

    </script>
</body>
</html>