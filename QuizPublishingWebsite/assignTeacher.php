<?php
session_start();
// session_destroy();
if (!isset($_SESSION['admin_id'])) {
    session_start();
    header("Location: index.php");
    session_write_close();
    exit();
}
//signs or status variables below are for showing dialog box (user feedback)
$repeatSign = 0; //2nd usage (verifcation) -> value = 1 means skip insert or update process
$createStatus = 0;
$editStatus = 0;
$deleteStatus = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "conn.php";
    $repeatSign = 0; //usage -> value = 1 means stop insert or update process, then show dialog box
    $duplicateClassYearSign = 0; //for verification

    if ($_POST['form'] === "create") {
        //post values to create subject
        $teacherId = (int) $_POST["teacherId"];
        $courseId = (int) $_POST["courseId"];
        
        //retrieve data for data verification
        $sqlCourseTeacher = "SELECT * FROM course_teacher;";
        $courseTeacherForRepeatCheck = mysqli_query($conn, $sqlCourseTeacher);
        
        while ($row = mysqli_fetch_array($courseTeacherForRepeatCheck)) {
            $existingTeacher = (int) $row['teacher_id'];
            $existingCourse = (int) $row['course_id'];
            
            //verification : ensure no duplicate teacher and course
            if ($teacherId === $existingTeacher && $courseId === $existingCourse) {
                $repeatSign = 1;
                break;
            }
        }
        
        if ($repeatSign !== 1) {
            //data verified
            echo "<script>console.log('Inserting new course teacher into database \'morningkdb\' table \'course teacher\'...');</script>";
            $sqlCreate = "INSERT INTO course_teacher (teacher_id, course_id) VALUES ('$teacherId', '$courseId');";
            mysqli_query($conn, $sqlCreate);

            if (mysqli_affected_rows($conn) === 0) {
                echo "<script>console.log('Failed to insert new course teacher into database \'morningkdb\' table \'course teacher\'!');</script>";
                echo "<script>alert('Unable to insert data.');</script>";
            } else {
                echo "<script>console.log('Successfully inserted new course teacher into database \'morningkdb\' table \'course teacher\'!');</script>";
                $createStatus = 1;
            }
        } else {
            //data repetition
            echo "<script>console.log('Invalid course teacher. Failed to insert into database \'morningkdb\' table \'course teacher\'.');</script>";
        }
    }
    
    else if ($_POST['form'] === "edit") {
        //post values to edit selected subject
        $teacherId = (int) $_POST["teacherIdForEdit"];
        $newCourseId = (int) $_POST["newCourseId"];
        $oldCourseId = (int) $_POST["oldCourseId"];

        //retrieve data for data verification
        $sqlCoureTeacher = "SELECT * FROM course_teacher";
        $courseTeacherForRepeatCheck = mysqli_query($conn, $sqlCoureTeacher);
        
        while ($row = mysqli_fetch_array($courseTeacherForRepeatCheck)) {
            $existingTeacher = (int) $row['teacher_id'];
            $existingCourseId = (int) $row['course_id'];

            //verification : ensure no duplicate teacher and course
            if ($teacherId === $existingTeacher && $newCourseId === $existingCourseId) {
                $repeatSign = 1;
                break;
            }
        }

        if ($repeatSign !== 1) {
            $sqlUpdate = "UPDATE course_teacher SET course_id = '$newCourseId' WHERE teacher_id = '$teacherId' AND course_id = '$oldCourseId';";

            mysqli_query($conn, $sqlUpdate);
            if (mysqli_affected_rows($conn) === 0) {
                echo "<script>console.log('Failed to edit course teacher in database \'morningkdb\' table \'course_teacher\'!');</script>";
                echo "<script>alert('Unable to edit data.');</script>";
            } else {
                echo "<script>console.log('Successfully edited course teacher in database \'morningkdb\' table \'course_teacher\'!');</script>";
                $editStatus = 1;
            }
        } else {
            //data repetition
            echo "<script>console.log('Invalid course teacher. Failed to update database \'morningkdb\' table \'course_teacher\'.');</script>";
        }
    }

    else if ($_POST['form'] === "delete") {
        $teacherId = (int) $_POST["teacherIdForDelete"];
        $courseId = (int) $_POST["courseIdForDelete"];

        $sqlDelete = "DELETE FROM course_teacher WHERE teacher_id = '$teacherId' AND course_id = '$courseId';";
        mysqli_query($conn, $sqlDelete);

        if (mysqli_affected_rows($conn) === 0) {
            echo "<script>console.log('Failed to delete course teacher from database \'morningkdb\' table \'course_teacher\'!');</script>";
            echo "<script>alert('Unable to delete data.');</script>";
        } else {
            echo "<script>console.log('Successfully deleted course teacher from database \'morningkdb\' table \'course_teacher\'!');</script>";
            $deleteStatus = 1;
        }
    }
}

include "conn.php";

//get courses
$sqlCourseForAssign = 
"SELECT 
    course.course_id AS courseid,
    subject.subject_name AS subjectname, 
    class.grade AS classgrade, 
    class.class_name AS classname, 
    class.year AS classyear 
FROM 
    course 
JOIN 
    subject ON course.subject_id = subject.subject_id 
JOIN 
    class ON course.class_id = class.class_id 
ORDER BY 
    class.year DESC, class.grade ASC, class.class_name ASC;";
$allCoursesForAssign = mysqli_query($conn, $sqlCourseForAssign);

$sqlCourseForEdit = 
"SELECT 
    course.course_id AS courseid,
    subject.subject_name AS subjectname, 
    class.grade AS classgrade, 
    class.class_name AS classname, 
    class.year AS classyear 
FROM 
    course 
JOIN 
    subject ON course.subject_id = subject.subject_id 
JOIN 
    class ON course.class_id = class.class_id 
ORDER BY 
    class.year DESC, class.grade ASC, class.class_name ASC;";
$allCoursesForEdit = mysqli_query($conn, $sqlCourseForEdit);

//get teacher id from teacher table, and name from user table
$sqlTeacherForAssign = 
"SELECT 
    teacher.teacher_id, 
    user.username AS teacher_name
FROM 
    teacher 
JOIN 
    user ON teacher.user_id = user.user_id
ORDER BY 
    teacher_id DESC;";
$allTeachersForAssign = mysqli_query($conn, $sqlTeacherForAssign);

//get course teachers from db
$sqlCourseTeacherForEdit = 
"SELECT 
    *
FROM 
    course_teacher 
JOIN 
    (SELECT 
        teacher.teacher_id, 
        user.username AS teacher_name
    FROM 
        teacher 
    JOIN 
        user ON teacher.user_id = user.user_id
    ORDER BY 
        teacher_id DESC) as teacherTable
ON 
    course_teacher.teacher_id = teacherTable.teacher_id
JOIN 
    (SELECT 
    course.course_id AS courseid,
    subject.subject_name AS subjectname, 
    class.grade AS classgrade, 
    class.class_name AS classname, 
    class.year AS classyear 
FROM 
    course 
JOIN 
    subject ON course.subject_id = subject.subject_id 
JOIN 
    class ON course.class_id = class.class_id 
ORDER BY 
    class.year DESC, class.grade ASC, class.class_name ASC) as fullCourseTable
ON 
    course_teacher.course_id = fullCourseTable.courseid
ORDER BY 
    course_teacher.teacher_id DESC;";
$allCourseTeachersForEdit = mysqli_query($conn, $sqlCourseTeacherForEdit);

$sqlCourseTeacherForDelete = 
"SELECT 
    *
FROM 
    course_teacher 
JOIN 
    (SELECT 
        teacher.teacher_id, 
        user.username AS teacher_name
    FROM 
        teacher 
    JOIN 
        user ON teacher.user_id = user.user_id
    ORDER BY 
        teacher_id DESC) as teacherTable
ON 
    course_teacher.teacher_id = teacherTable.teacher_id
JOIN 
    (SELECT 
    course.course_id AS courseid,
    subject.subject_name AS subjectname, 
    class.grade AS classgrade, 
    class.class_name AS classname, 
    class.year AS classyear 
FROM 
    course 
JOIN 
    subject ON course.subject_id = subject.subject_id 
JOIN 
    class ON course.class_id = class.class_id 
ORDER BY 
    class.year DESC, class.grade ASC, class.class_name ASC) as fullCourseTable
ON 
    course_teacher.course_id = fullCourseTable.courseid
ORDER BY 
    course_teacher.teacher_id DESC;";
$allCourseTeachersForDelete = mysqli_query($conn, $sqlCourseTeacherForDelete);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="assignTeacher.css">
    <link rel="stylesheet" href="adminMngPages.css">
</head>
<body>
    <div class="blurOverlay"></div>
    <div id="parentBody">
        <div id="bodyContainer">
            <div id="headerContainer">
                <?php include "adminHeader.php"?>
            </div>
            <div id="contentContainer">
                <div id="mainBody">
                    <a href="adminMngData.php" id="back">back</a>
                    <div class="tabMenu">
                        <button class="tabButton active" data-tab="create">Create</button>
                        <button class="tabButton" data-tab="edit">Edit</button>
                        <button class="tabButton" data-tab="delete">Delete</button>
                    </div>
                    <div class="content">
                        <div class="contentBody" id="create">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Assign Course to Teacher</h1>
                            </div>
                            <form id="createCourseTeacherForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="create">
                                <table>
                                    <tr>
                                        <th colspan="2">Teacher:</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="teacherDropdownContainer">
                                                <button id="teacherDropdown" onclick="controlDropdown('teacherOptions')">
                                                    <div id="teacherDpText">Select</div>
                                                    <span class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="teacherContainer">
                                                    <input type="text" name="" id="teacherSearch" onkeyup="filterSearch(id, 'teacherOptions')" placeholder="Search">
                                                    <div class="fullTableDpImg">
                                                        <img src="media/up.png" alt="Up Arrow" onclick="controlDropdown('teacherOptions')">
                                                    </div>
                                                </div>
                                                <div class="teacherOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allTeachersForAssign)) { ?>
                                                        <div class="option" data-teacherid="<?php echo $rows['teacher_id'];?>" onclick="chooseTeacher(this)">
                                                            <?php echo "TEC".$rows['teacher_id']." - ".$rows['teacher_name']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="teacherId" id="teacherId" value="">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2"><br>Course to be Assigned:</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="courseDropdownContainer">
                                                <button id="courseDropdown" onclick="controlDropdown('courseOptions')">
                                                    <div id="courseDpText">Select</div>
                                                    <div class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="courseContainer">
                                                    <input type="text" name="" id="courseSearch" onkeyup="filterSearch(id, 'courseOptions')" placeholder="Search &quot;Course ID (Subject, Class)&quot;">
                                                    <div class="fullTableDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('courseOptions')">
                                                    </div>
                                                </div>
                                                <div class="courseOptions">
                                                    <?php while ($rows = mysqli_fetch_array($allCoursesForAssign)) { ?>
                                                        <div class="option" data-courseid="<?php echo $rows['courseid'];?>" onclick="chooseCourse(this)">
                                                            <?php echo $rows['courseid']." (".$rows['subjectname'].", ".$rows['classgrade']."-".$rows['classname']."-".$rows['classyear'].")"; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="courseId" id="courseId" value="">
                                        </th>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForCreate" value="Assign" onclick="validateCreateForm()">
                            </form>      
                        </div>
                        <div class="contentBody" id="edit">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Update Course Assignment</h1>
                            </div>
                            <br><br>
                            <em>You can change the class enrolled by students, but not the students themselves.</em>
                            <form id="editCourseTeacherForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="edit">
                                <table>
                                    <tr>
                                        <th colspan="2">Course Teacher:</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="editTeacherDropdownContainer">
                                                <button id="editCourseTeacherDropdown" onclick="controlDropdown('editCourseTeacherOptions')">
                                                    <div id="editCourseTeacherDpText">Select</div>
                                                    <div class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="editCourseTeacherContainer">
                                                    <input type="text" name="" id="editCourseTeacherSearch" onkeyup="filterSearch(id, 'editCourseTeacherOptions')" placeholder="Search Course Teacher &quot;Teacher > Course&quot;">
                                                    <div class="fullTableDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editCourseTeacherOptions')">
                                                    </div>
                                                </div>
                                                <div class="editCourseTeacherOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allCourseTeachersForEdit)) { ?>
                                                        <div class="option" data-editteacherid="<?php echo $rows['teacher_id'];?>" data-editteachername="<?php echo $rows['teacher_name'];?>" data-editcourseid="<?php echo $rows['course_id'];?>" onclick="editCourseTeacher(this)">
                                                            <?php echo "TEC".$rows['teacher_id']." - ".$rows['teacher_name']." > ".$rows['courseid']." (".$rows['subjectname'].", ".$rows['classgrade']."-".$rows['classname']."-".$rows['classyear'].")"; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="teacherIdForEdit" id="teacherIdForEdit" value="">
                                            <input type="hidden" name="oldCourseId" id="oldCourseId" value="">
                                        </th>
                                    </tr>
                                    <tbody id="editCourseTeacherToggle">
                                        <tr>
                                            <th colspan="2">Selected Teacher:</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2"><p id="selectedTeacherForEdit"></p></th>
                                        </tr>
                                        <tr>
                                            <th colspan="2">New Course:</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2">
                                                <div class="customDropdown" id="editCourseDropdownContainer">
                                                    <button id="editCourseDropdown" onclick="controlDropdown('editCourseOptions')">
                                                        <div id="editCourseDpText">Select</div>
                                                        <span class="fullTableDpImg">
                                                            <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                        </span>
                                                    </button>
                                                    <div class="editCourseContainer">
                                                        <input type="text" name="" id="editCourseSearch" onkeyup="filterSearch(id, 'editCourseOptions')" placeholder="Search &quot;Course ID (Subject, Class)&quot;">
                                                        <div class="fullTableDpImg">
                                                            <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editCourseOptions')">
                                                        </div>
                                                    </div>
                                                    <div class="editCourseOptions">
                                                        <?php while ($rows = mysqli_fetch_array($allCoursesForEdit)) { ?>
                                                        <div class="option" data-editcourseid="<?php echo $rows['courseid'];?>" onclick="editCourse(this)">
                                                            <?php echo $rows['courseid']." (".$rows['subjectname'].", ".$rows['classgrade']."-".$rows['classname']."-".$rows['classyear'].")"; ?>
                                                        </div>
                                                    <?php } ?>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="newCourseId" id="newCourseId">
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForEdit" value="Save Changes" onclick="validateEditForm()">
                            </form>  
                        </div>
                        <div class="contentBody" id="delete">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Delete Enrolment</h1>
                            </div>
                            <br><br>
                            <form id="deleteCourseTeacherForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="delete">    
                                <table>
                                <tr>
                                        <th colspan="2">Course Teacher:</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="deleteCourseTeacherDropdownContainer">
                                                <button id="deleteCourseTeacherDropdown" onclick="controlDropdown('deleteCourseTeacherOptions')">
                                                    <div id="deleteCourseTeacherDpText">Select</div>
                                                    <div class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="deleteCourseTeacherContainer">
                                                    <input type="text" name="" id="deleteCourseTeacherSearch" onkeyup="filterSearch(id, 'deleteCourseTeacherOptions')" placeholder="Search Course Teacher &quot;Teacher > Course&quot;">
                                                    <div class="fullTableDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('deleteCourseTeacherOptions')">
                                                    </div>
                                                </div>
                                                <div class="deleteCourseTeacherOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allCourseTeachersForDelete)) { ?>
                                                        <div class="option" data-deleteteacherid="<?php echo $rows['teacher_id'];?>" data-deletecourseid="<?php echo $rows['course_id'];?>" onclick="deleteCourseTeacher(this)">
                                                            <?php echo "TEC".$rows['teacher_id']." - ".$rows['teacher_name']." > ".$rows['courseid']." (".$rows['subjectname'].", ".$rows['classgrade']."-".$rows['classname']."-".$rows['classyear'].")"; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="teacherIdForDelete" id="teacherIdForDelete" value="">
                                            <input type="hidden" name="courseIdForDelete" id="courseIdForDelete" value="">
                                        </th>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForDelete" value="Delete Course" onclick="validateDeleteForm()">
                            </form>
                                
                        </div>
                            
                    </div>
                </div>
            </div>
        </div>
        
        <dialog id="repeatData">Course teacher cannot be saved because it already exist.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="repeatEditData">Data is not submitted because no changes are made.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="createFail">Choose a teacher and a course before submitting course assignment.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="editFail">Choose a course teacher to edit.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="editFail2">Choose a new course to be assigned to the course teacher.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="deleteFail">Choose a course teacher to delete.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="editMsg">Course teacher change is saved.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="createMsg">Course teacher is created and saved successfully.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="deleteMsg">Course teacher is permanently deleted.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>
    </div>
    

    <script>
        document.querySelectorAll('.tabButton').forEach(button => {
            button.addEventListener('click', () => {

                document.querySelectorAll('.tabButton').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                let tabContents = document.querySelectorAll('.contentBody');
                tabContents.forEach(content => content.style.display = 'none');

                let tabId = button.getAttribute('data-tab');
                document.getElementById(tabId).style.display = 'block';
            });
        });

        let repeatSign = <?php echo $repeatSign?>;
        let createStatus = <?php echo $createStatus?>;
        let editStatus = <?php echo $editStatus?>;
        let deleteStatus = <?php echo $deleteStatus?>;

        if (repeatSign === 1) {
            document.querySelector(".blurOverlay").style.visibility = "visible";
            document.getElementById("repeatData").showModal();
        }

        if (createStatus === 1) {
            document.querySelector(".blurOverlay").style.visibility = "visible";
            document.getElementById("createMsg").showModal();
        }

        if (editStatus === 1) {
            document.querySelector(".blurOverlay").style.visibility = "visible";
            document.getElementById("editMsg").showModal();
        }

        if (deleteStatus === 1) {
            document.querySelector(".blurOverlay").style.visibility = "visible";
            document.getElementById("deleteMsg").showModal();
        }

        
        function okayExit() {
            document.querySelectorAll('dialog').forEach(dialog => dialog.close());
            document.querySelector(".blurOverlay").style.visibility = "hidden";
        }

        function controlDropdown(optionList) {
            let text, input, options;

            if (optionList === "teacherOptions") {
                text = document.getElementById("teacherDropdown");
                input = document.getElementsByClassName("teacherContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("teacherSearch").value = "";
            }

            if (optionList === "courseOptions") {
                text = document.getElementById("courseDropdown");
                input = document.getElementsByClassName("courseContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("courseSearch").value = "";
            }

            if (optionList === "editCourseTeacherOptions") {
                text = document.getElementById("editCourseTeacherDropdown");
                input = document.getElementsByClassName("editCourseTeacherContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editCourseTeacherSearch").value = "";
            }

            if (optionList === "editCourseOptions") {
                text = document.getElementById("editCourseDropdown");
                input = document.getElementsByClassName("editCourseContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editCourseSearch").value = "";
            }

            if (optionList === "deleteCourseTeacherOptions") {
                text = document.getElementById("deleteCourseTeacherDropdown");
                input = document.getElementsByClassName("deleteCourseTeacherContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("deleteCourseTeacherSearch").value = "";
            }
       
            if (options.style.display === "block") {
                options.style.display = "none";
                text.style.visibility = "visible";
                input.style.visibility = "hidden";
                const div = options.getElementsByTagName("div");
                for (let i = 0; i < div.length; i++) {
                    div[i].style.display = "block";
                }
                
            } else {
                options.style.display = "block";
                text.style.visibility = "hidden";
                input.style.visibility = "visible";
            }
        }


        function chooseCourse(element) {
            let newText = element.innerText;
            document.getElementById("courseDpText").textContent = newText;
            controlDropdown("courseOptions");

            let courseId = element.dataset.courseid;

            let course = document.getElementById("courseId");

            course.value = courseId;
        }


        function chooseTeacher(element) {
            let newText = element.innerText;
            document.getElementById("teacherDpText").textContent = newText;
            controlDropdown("teacherOptions");

            let teacherId = element.dataset.teacherid;

            let chosenTeacher = document.getElementById("teacherId");

            chosenTeacher.value = teacherId;
        }


        function editCourseTeacher(element) {
            let newText = element.innerText;
            document.getElementById("editCourseTeacherDpText").textContent = newText;
            controlDropdown("editCourseTeacherOptions");

            let teacherId = element.dataset.editteacherid;
            let courseId = element.dataset.editcourseid;
            let teacherName = element.dataset.editteachername;

            let chosenTeacherId = document.getElementById("teacherIdForEdit");
            let oldCourseId = document.getElementById("oldCourseId");

            chosenTeacherId.value = teacherId;
            oldCourseId.value = courseId;

            document.getElementById("editCourseTeacherToggle").style.display = "block";
            document.getElementById("selectedTeacherForEdit").textContent = "TEC"+teacherId+" - "+teacherName;

            
            console.log(chosenTeacherId.value);
            console.log(oldCourseId.value);
        }


        function editCourse(element) {
            let newText = element.innerText;
            document.getElementById("editCourseDpText").textContent = newText;
            controlDropdown("editCourseOptions");

            let courseId = element.dataset.editcourseid;

            let newCourseId = document.getElementById("newCourseId");

            newCourseId.value = courseId;
            
            console.log(newCourseId.value);
        }


        function deleteCourseTeacher(element) {
            let newText = element.innerText;
            document.getElementById("deleteCourseTeacherDpText").textContent = newText;
            controlDropdown("deleteCourseTeacherOptions");

            let teacherId = element.dataset.deleteteacherid;
            let courseId = element.dataset.deletecourseid;

            let chosenTeacherId = document.getElementById("teacherIdForDelete");
            let chosenClassId = document.getElementById("courseIdForDelete");

            chosenTeacherId.value = teacherId;
            chosenClassId.value = courseId;
        }


        function filterSearch(id, optionList) {
            const input = document.getElementById(id);
            let filter = input.value.toUpperCase();
            const options = document.getElementsByClassName(optionList)[0];
            const div = options.getElementsByTagName("div");
            if (filter !== "") {
                for (let i = 0; i < div.length; i++) {
                    txtValue = div[i].textContent || div[i].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) >= 0) {
                        div[i].style.display = "";
                    } else {
                        div[i].style.display = "none";
                    }
                }
            }
        }
        

        function validateDeleteForm() {
            let teacherId = document.getElementById("teacherIdForDelete").value;
            let courseId = document.getElementById("courseIdForDelete").value;
            if (teacherId && courseId) {
                document.getElementById("deleteCourseTeacherForm").submit(); 
            } else {
                document.querySelector(".blurOverlay").style.visibility = "visible";
                document.getElementById("deleteFail").showModal();
            }
        }


        function validateEditForm() {
            let teacherId = document.getElementById("teacherIdForEdit").value;
            let oldCourseId = document.getElementById("oldCourseId").value;
            if (teacherId && oldCourseId) {
                let newCourseId = document.getElementById("newCourseId").value;
                if (newCourseId) {
                    if (oldCourseId === newCourseId) {
                        document.querySelector(".blurOverlay").style.visibility = "visible";
                        document.getElementById("repeatEditData").showModal();
                    } else {
                        document.getElementById("editCourseTeacherForm").submit();
                    } 
                } else {
                    document.querySelector(".blurOverlay").style.visibility = "visible";
                    document.getElementById("editFail2").showModal();
                }
            } else {
                document.querySelector(".blurOverlay").style.visibility = "visible";
                document.getElementById("editFail").showModal();
            }
            
        }
            

        function validateCreateForm() {
            let teacherId = document.getElementById("teacherId").value;
            let courseId = document.getElementById("courseId").value;
            if (teacherId && courseId) {
                document.getElementById("createCourseTeacherForm").submit(); 
            } else {
                document.querySelector(".blurOverlay").style.visibility = "visible";
                document.getElementById("createFail").showModal();
            }
        }


        function preventSubmission(event) {
            event.preventDefault();
        }


        // document.addEventListener('DOMContentLoaded', function() {
        //     const courseSubjectDp = document.getElementById('courseSubjectDropdown');
        //     const courseSubjectDp1 = document.getElementsByClassName('courseSubjectContainer')[0];
        //     const courseSubjectOpt = document.getElementsByClassName('courseSubjectOptions')[0];
        //     const courseClassDp = document.getElementById('courseClassDropdown');
        //     const courseClassDp1 = document.getElementsByClassName('courseClassContainer')[0];
        //     const courseClassOpt = document.getElementsByClassName('courseClassOptions')[0];
        //     const editCourseDp = document.getElementById('editCourseDropdown');
        //     const editCourseDp1 = document.getElementsByClassName('editCourseContainer')[0];
        //     const editCourseOpt = document.getElementsByClassName('editCourseOptions')[0];
        //     const editCourseSubjectDp = document.getElementById('editCourseSubjectDropdown');
        //     const editCourseSubjectDp1 = document.getElementsByClassName('editCourseSubjectContainer')[0];
        //     const editCourseSubjectOpt = document.getElementsByClassName('editCourseSubjectOptions')[0];
        //     const editCourseClassDp = document.getElementById('editCourseClassDropdown');
        //     const editCourseClassDp1 = document.getElementsByClassName('editCourseClassContainer')[0];
        //     const editCourseClassOpt = document.getElementsByClassName('editCourseClassOptions')[0];
        //     const deleteCourseDp = document.getElementById('deleteCourseDropdown');
        //     const deleteCourseDp1 = document.getElementsByClassName('deleteCourseContainer')[0];
        //     const deleteCourseOpt = document.getElementsByClassName('deleteCourseOptions')[0];

        //     // Click outside closes dropdown
        //     document.addEventListener('click', function(event) {
        //         if (courseSubjectOpt.style.display === "block" && !courseSubjectDp.contains(event.target) && !courseSubjectDp1.contains(event.target) && !courseSubjectOpt.contains(event.target)) {
        //             controlDropdown('courseSubjectOptions');
        //         }

        //         if (courseClassOpt.style.display === "block" && !courseClassDp.contains(event.target) && !courseClassDp1.contains(event.target) && !courseClassOpt.contains(event.target)) {
        //             controlDropdown('courseClassOptions');
        //         }

        //         if (editCourseOpt.style.display === "block" && !editCourseDp.contains(event.target) && !editCourseDp1.contains(event.target) && !editCourseOpt.contains(event.target)) {
        //             controlDropdown('editCourseOptions');
        //         }

        //         if (editCourseSubjectOpt.style.display === "block" && !editCourseSubjectDp.contains(event.target) && !editCourseSubjectDp1.contains(event.target) && !editCourseSubjectOpt.contains(event.target)) {
        //             controlDropdown('editCourseSubjectOptions');
        //         }

        //         if (editCourseClassOpt.style.display === "block" && !editCourseClassDp.contains(event.target) && !editCourseClassDp1.contains(event.target) && !editCourseClassOpt.contains(event.target)) {
        //             controlDropdown('editCourseClassOptions');
        //         }

        //         if (deleteCourseOpt.style.display === "block" && !deleteCourseDp.contains(event.target) && !deleteCourseDp1.contains(event.target) && !deleteCourseOpt.contains(event.target)) {
        //             controlDropdown('deleteCourseOptions');
        //         }
        //     });
        // });

        
        //styling section
        function inpDesign(id) {
            let inputID = document.getElementById(id);
            inputID.style.border = "2px solid rgb(52, 216, 57)";
            inputID.style.boxShadow = "none";
            inputID.style.outline = "none";
            inputID.style.backgroundColor = "rgb(244, 255, 240)";
        }

        function inpRevert(id) {
            let inputID = document.getElementById(id);
            inputID.style.border = "2px solid grey";
            inputID.style.backgroundColor = "white";  
        }

        function toggleDropdown(optionList) {
            let options, up, down;
            options = document.querySelector("."+optionList);

            if (options.style.display === "block") {
                options.style.display = "none";
                down.style.display = "block";
                up.style.display = "none";  
            }
            else {
                options.style.display = "block";
                down.style.display = "none";
                up.style.display = "block";
            }
        }


        function selectOption(element, dpText, dataID, optionList) {
            let text = element.innerText;
            let dataValue = element.getAttribute("dataValue");

            document.getElementById(dpText).textContent = text; // Update button text
            document.getElementById(dataID).value = dataValue; // Set the hidden input's value

            toggleDropdown(optionList); // close the dropdown after selection
        }
        
    </script>
    </body>
</html>
