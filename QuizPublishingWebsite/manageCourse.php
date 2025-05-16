<?php
session_start();
// session_destroy();
if (!isset($_SESSION['admin_id'])) {
    session_start();
    header("Location: index.php");
    session_write_close();
    exit();
}
$loginFail = false;

//signs or status variables below are for showing dialog box (user feedback)
$repeatSign = 0; //2nd usage -> value = 1 means skip insert or update process
$createStatus = 0;
$editStatus = 0;
$deleteStatus = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "conn.php";
    $repeatSign = 0; //usage -> value = 1 means stop insert or update process, then show dialog box

    if ($_POST['form'] === "create") {
        //post values to create subject
        $subjectId = (int) $_POST["courseSubject"];
        $classId = (int) $_POST['courseClass'];
        
        //select from database to make sure no record repetition
        $sqlCourse = "SELECT * FROM course;";
        $coursesForRepeatCheck = mysqli_query($conn, $sqlCourse);
        
        while ($row = mysqli_fetch_array($coursesForRepeatCheck)) {
            $existingSubject = (int) $row['subject_id'];
            $existingClass = (int) $row['class_id'];
            
            if ($subjectId === $existingSubject && $classId === $existingClass) {
                $repeatSign = 1;
                break;
            }
        }
        
        if ($repeatSign !== 1) {
            //no data repetition
            echo "<script>console.log('Inserting new course into database \'morningkdb\' table \'course\'...');</script>";
            $sqlCreate = "INSERT INTO course (subject_id, class_id) VALUES ('$subjectId', '$classId');";
            mysqli_query($conn, $sqlCreate);

            if (mysqli_affected_rows($conn) === 0) {
                echo "<script>console.log('Failed to insert new course into database \'morningkdb\' table \'course\'!');</script>";
                echo "<script>alert('Unable to insert data.');</script>";
            } else {
                echo "<script>console.log('Successfully inserted new course into database \'morningkdb\' table \'course\'!');</script>";
                $createStatus = 1;
            }
        } else {
            //data repetition
            echo "<script>console.log('Course entered already exist in database \'morningkdb\' table \'course\'.');</script>";
        }
    }
    
    else if ($_POST['form'] === "edit") {
        //post values to edit selected subject
        $courseId = (int) $_POST["courseId"];
        $newSubjectId = (int) $_POST["newSubjectId"];
        $newClassId = (int) $_POST["newClassId"];

        //select from database to make sure no record repetition
        $sqlRepeatCourse = "SELECT * FROM course;";
        $coursesForRepeatCheck = mysqli_query($conn, $sqlRepeatCourse);
        
        while ($row = mysqli_fetch_array($coursesForRepeatCheck)) {
            $repeatSubjectId = (int) $row['subject_id'];
            $repeatClassId = (int) $row['class_id'];

            if ($repeatSubjectId === $newSubjectId && $repeatClassId === $newClassId) {
                $repeatSign = 1;
                break;
            }
        }

        if ($repeatSign !== 1) {
            $sqlUpdate = "UPDATE course SET subject_id = '$newSubjectId', class_id = '$newClassId' WHERE course_id = '$courseId';";

            mysqli_query($conn, $sqlUpdate);
            if (mysqli_affected_rows($conn) === 0) {
                echo "<script>console.log('Failed to edit subject name in database \'morningkdb\' table \'subject\'!');</script>";
                echo "<script>alert('Unable to edit data.');</script>";
            } else {
                echo "<script>console.log('Successfully edited class name in database \'morningkdb\' table \'subject\'!');</script>";
                $editStatus = 1;
            }
        }
    }

    else if ($_POST['form'] === "delete") {
        $deleteCourseId = (int) $_POST["deleteCourseId"];

        //to delete all folders of the course
        $sqlFolder = "SELECT folder_id FROM folder WHERE course_id = '$deleteCourseId';";
        $deleteFolder = mysqli_query($conn, $sqlFolder);
        while ($row = mysqli_fetch_array($deleteFolder)) {
            $folderId = (int) $row['folder_id'];
            $sql = "DELETE FROM quiz WHERE quiz_id = '$folderId'";
            mysqli_query($conn, $sql);

            //delete all quizzes in the folder
            $sqlQuiz = "SELECT quiz_id FROM exercise WHERE folder_id = '$folderId';";
            $deleteQuiz = mysqli_query($conn, $sqlQuiz);
            while ($row = mysqli_fetch_array($deleteQuiz)) {
                $quizId = (int) $row['quiz_id'];
                $sql = "DELETE FROM quiz WHERE quiz_id = '$quizId'";
                mysqli_query($conn, $sql);
            }
        }

        //finally delete the course itself from table
        $sqlDelete = "DELETE FROM course WHERE course_id = '$deleteCourseId';";
        mysqli_query($conn, $sqlDelete);

        if (mysqli_affected_rows($conn) === 0) {
            echo "<script>console.log('Failed to delete subject from database \'morningkdb\' table \'subject\'!');</script>";
            echo "<script>alert('Unable to delete data.');</script>";
        } else {
            echo "<script>console.log('Successfully deleted subject from database \'morningkdb\' table \'subject\'!');</script>";
            $deleteStatus = 1;
        }
    }
}

include "conn.php";

//get subjects from database
$sqlSubjectForCreate = "SELECT * FROM subject ORDER BY subject_name ASC;";
$allSubjectsForCreate = mysqli_query($conn, $sqlSubjectForCreate);

$sqlSubjectForEdit = "SELECT * FROM subject ORDER BY subject_name ASC;";
$allSubjectsForEdit = mysqli_query($conn, $sqlSubjectForEdit);

//get classes from database
$sqlClassForCreate = "SELECT * FROM class ORDER BY year DESC, grade ASC, class_name ASC;";
$allClassesForCreate = mysqli_query($conn, $sqlClassForCreate);

$sqlClassForEdit = "SELECT * FROM class ORDER BY year DESC, grade ASC, class_name ASC;";
$allClassesForEdit = mysqli_query($conn, $sqlClassForEdit);

//retrieve upcoming course id for new creation
$sqlCourseId = 
"SELECT AUTO_INCREMENT
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'morningkdb' AND TABLE_NAME = 'course';";
$courseIdForCreate = mysqli_query($conn, $sqlCourseId);
$row = mysqli_fetch_array($courseIdForCreate);
$nextAutoIncrement = isset($row['AUTO_INCREMENT']) ? $row['AUTO_INCREMENT'] : 0;

//retrieve courses, and their related subjects and classes
//all from 3 tables
$sqlCourseForEdit = 
"SELECT 
    course.course_id AS courseid, 
    course.subject_id AS subjectid, 
    course.class_id AS classid, 
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

$sqlCourseForDelete = 
"SELECT 
    course.course_id AS courseid, 
    course.subject_id AS subjectid, 
    course.class_id AS classid, 
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
$allCoursesForDelete = mysqli_query($conn, $sqlCourseForDelete);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="manageCourse.css">
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
                                <h1 style="text-align: center;">Create New Course</h1>
                            </div>
                            <form id="createSubjectForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="create">
                                <table>
                                    <tr>
                                        <th>Course ID:</th>
                                        <td style="padding-left: 20px; justify-content: left;"><strong><?php echo $nextAutoIncrement;?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Subject</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="courseSubjectDropdown" onclick="controlDropdown('courseSubjectOptions')">
                                                    <div id="courseSubjectdpText">Select</div>
                                                    <div class="inpTextDpImg">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="courseSubjectContainer">
                                                    <input type="text" name="" id="courseSubjectSearch" onkeyup="filterSearch(id, 'courseSubjectOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('courseSubjectOptions')">
                                                    </div>
                                                </div>
                                                <div class="courseSubjectOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allSubjectsForCreate)) { ?>
                                                        <div class="option" data-subjectid="<?php echo $rows['subject_id'];?>" onclick="chooseCourseSubject(this)">
                                                            <?php echo $rows['subject_name']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="courseSubject" id="courseSubject">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Class:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="courseClassDropdown" onclick="controlDropdown('courseClassOptions')">
                                                    <div id="courseClassdpText">Select</div>
                                                    <span class="inpTextDpImg">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="courseClassContainer">
                                                    <input type="text" name="" id="courseClassSearch" onkeyup="filterSearch(id, 'courseClassOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('courseClassOptions')">
                                                    </div>
                                                </div>
                                                <div class="courseClassOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allClassesForCreate)) { ?>
                                                        <div class="option" data-classid="<?php echo $rows['class_id'];?>" onclick="chooseCourseClass(this)">
                                                            <?php echo $rows['grade']."-".$rows['class_name']."-".$rows['year']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="courseClass" id="courseClass">
                                        </td>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForCreate" value="Save Course" onclick="validateCreateForm()">
                            </form>      
                        </div>
                        <div class="contentBody" id="edit">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Edit Course</h1>
                            </div>
                            <form id="editCourseForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="edit">
                                <table>
                                    <tr>
                                        <th colspan="2">Course To Edit</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="editCourseDropdownContainer">
                                                <button id="editCourseDropdown" onclick="controlDropdown('editCourseOptions')">
                                                    <div id="editCoursedpText">Select</div>
                                                    <div class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="editCourseContainer">
                                                    <input type="text" name="" id="editCourseSearch" onkeyup="filterSearch(id, 'editCourseOptions')" placeholder="Search &quot;Course ID (Subject, Class)&quot;">
                                                    <div class="fullTableDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editCourseOptions')">
                                                    </div>
                                                </div>
                                                <div class="editCourseOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allCoursesForEdit)) { ?>
                                                        <div class="option" data-courseid="<?php echo $rows['courseid'];?>" data-subjectid="<?php echo $rows['subjectid'];?>" data-classid="<?php echo $rows['classid'];?>" onclick="editCourse(this)">
                                                            <?php echo $rows['courseid']." (".$rows['subjectname'].", ".$rows['classgrade']."-".$rows['classname']."-".$rows['classyear'].")"; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="courseId" id="courseId" value="">
                                            <input type="hidden" name="oldSubjectId" id="oldSubjectId" value="">
                                            <input type="hidden" name="oldClassId" id="oldClassId" value="">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>New Subject</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="editCourseSubjectDropdown" onclick="controlDropdown('editCourseSubjectOptions')">
                                                    <div id="editCourseSubjectdpText">Select</div>
                                                    <span class="inpTextDpImg">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="editCourseSubjectContainer">
                                                    <input type="text" name="" id="editCourseSubjectSearch" onkeyup="filterSearch(id, 'editCourseSubjectOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editCourseSubjectOptions')">
                                                    </div>
                                                </div>
                                                <div class="editCourseSubjectOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allSubjectsForEdit)) { ?>
                                                        <div class="option" data-subjectid="<?php echo $rows['subject_id'];?>" onclick="editCourseSubject(this)">
                                                            <?php echo $rows['subject_name']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="newSubjectId" id="newSubjectId">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>New Class</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="editCourseClassDropdown" onclick="controlDropdown('editCourseClassOptions')">
                                                    <div id="editCourseClassdpText">Select</div>
                                                    <span class="inpTextDpImg">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="editCourseClassContainer">
                                                    <input type="text" name="" id="editCourseClassSearch" onkeyup="filterSearch(id, 'editCourseClassOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editCourseClassOptions')">
                                                    </div>
                                                </div>
                                                <div class="editCourseClassOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allClassesForEdit)) { ?>
                                                        <div class="option" data-classid="<?php echo $rows['class_id'];?>" onclick="editCourseClass(this)">
                                                        <?php echo $rows['grade']."-".$rows['class_name']."-".$rows['year']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="newClassId" id="newClassId">
                                        </td>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForEdit" value="Save Changes" onclick="validateEditForm()">
                            </form>  
                        </div>
                        <div class="contentBody" id="delete">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Delete Course</h1>
                            </div>
                            <br><br>
                            <em>You cannot delete a course that is already assigned to teachers. If you want to change or cancel courses assigned to teachers, go to Users section.</em>
                            <form id="deleteCourseForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="delete">    
                                <table>
                                <tr>
                                    <th colspan="2">Course To Delete</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="deleteCourseDropdownContainer">
                                                <button id="deleteCourseDropdown" onclick="controlDropdown('deleteCourseOptions')">
                                                    <div id="deleteCoursedpText">Select</div>
                                                    <div class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="deleteCourseContainer">
                                                    <input type="text" name="" id="deleteCourseSearch" onkeyup="filterSearch(id, 'deleteCourseOptions')" placeholder="Search &quot;Course ID (Subject, Class)&quot;">
                                                    <div class="fullTableDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('deleteCourseOptions')">
                                                    </div>
                                                </div>
                                                <div class="deleteCourseOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allCoursesForDelete)) { ?>
                                                        <div class="option" data-courseid="<?php echo $rows['courseid'];?>" onclick="deleteCourse(this)">
                                                            <?php echo $rows['courseid']." (".$rows['subjectname'].", ".$rows['classgrade']."-".$rows['classname']."-".$rows['classyear'].")"; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="deleteCourseId" id="deleteCourseId" value="">
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
    </div>

    <dialog id="repeatData">Course cannot be saved because it conflicts with existing data.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="repeatEditData">Data is not submitted because no changes are made.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="createFail">Choose a subject and a class to create a course.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="editFail">Choose a course to edit.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="editFail2">Choose a subject and a class to edit the chosen course.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="deleteFail">Choose a course to delete.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="editMsg">Course change(s) are saved.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="createMsg">Course is created and saved successfully.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="deleteMsg">Course is permanently deleted.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>
    
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
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById("repeatData").showModal();
        }

        if (createStatus === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById("createMsg").showModal();
        }

        if (editStatus === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById("editMsg").showModal();
        }

        if (deleteStatus === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById("deleteMsg").showModal();
        }


        function okayExit() {
            document.querySelectorAll('dialog').forEach(dialog => dialog.close());
            document.querySelector(".blurOverlay").style.visibility = "hidden";
        }


        function controlDropdown(optionList) {
            let text, input, options;
            if (optionList === "editCourseOptions") {
                text = document.getElementById("editCourseDropdown");
                input = document.getElementsByClassName("editCourseContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editCourseSearch").value = "";
            }

            if (optionList === "courseSubjectOptions") {
                text = document.getElementById("courseSubjectDropdown");
                input = document.getElementsByClassName("courseSubjectContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("courseSubjectSearch").value = "";
            }

            if (optionList === "courseClassOptions") {
                text = document.getElementById("courseClassDropdown");
                input = document.getElementsByClassName("courseClassContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("courseClassSearch").value = "";
            }

            if (optionList === "editCourseSubjectOptions") {
                text = document.getElementById("editCourseSubjectDropdown");
                input = document.getElementsByClassName("editCourseSubjectContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editCourseSubjectSearch").value = "";
            }

            if (optionList === "editCourseClassOptions") {
                text = document.getElementById("editCourseClassDropdown");
                input = document.getElementsByClassName("editCourseClassContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editCourseClassSearch").value = "";
            }

            if (optionList === "deleteCourseOptions") {
                text = document.getElementById("deleteCourseDropdown");
                input = document.getElementsByClassName("deleteCourseContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("deleteCourseSearch").value = "";
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

        
        function chooseCourseSubject(element) {
            let newText = element.innerText;
            document.getElementById("courseSubjectdpText").textContent = newText;
            controlDropdown("courseSubjectOptions");

            let subjectId = element.dataset.subjectid;

            let courseSubject = document.getElementById("courseSubject");

            courseSubject.value = subjectId;
        }


        function chooseCourseClass(element) {
            let newText = element.innerText;
            document.getElementById("courseClassdpText").textContent = newText;
            controlDropdown("courseClassOptions");

            let classId = element.dataset.classid;

            let courseClass = document.getElementById("courseClass");

            courseClass.value = classId;
        }


        function editCourse(element) {
            let newText = element.innerText;
            document.getElementById("editCoursedpText").textContent = newText;
            controlDropdown("editCourseOptions");

            let chosenCourseId = element.dataset.courseid;
            let subjectId = element.dataset.subjectid;
            let classId = element.dataset.classid;

            let courseId = document.getElementById("courseId");
            let oldSubjectId = document.getElementById("oldSubjectId");
            let oldClassId = document.getElementById("oldClassId");

            courseId.value = chosenCourseId;
            oldSubjectId.value = subjectId;
            oldClassId.value = classId;
        }


        function editCourseSubject(element) {
            let newText = element.innerText;
            document.getElementById("editCourseSubjectdpText").textContent = newText;
            controlDropdown("editCourseSubjectOptions");

            let subjectId = element.dataset.subjectid;

            let newSubjectId = document.getElementById("newSubjectId");

            newSubjectId.value = subjectId;
        }


        function editCourseClass(element) {
            let newText = element.innerText;
            document.getElementById("editCourseClassdpText").textContent = newText;
            controlDropdown("editCourseClassOptions");

            let classId = element.dataset.classid;

            let newClassId = document.getElementById("newClassId");

            newClassId.value = classId;
        }


        function deleteCourse(element) {
            let newText = element.innerText;
            document.getElementById("deleteCoursedpText").textContent = newText;
            controlDropdown("deleteCourseOptions");

            let courseId = element.dataset.courseid;

            let deleteCourseId = document.getElementById("deleteCourseId");

            deleteCourseId.value = courseId;
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
            let deleteCourseId = document.getElementById("deleteCourseId").value;
            if (deleteCourseId) {
                document.getElementById("deleteCourseForm").submit(); 
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("deleteFail").showModal();
            }
        }


        function validateEditForm() {
            let courseId = document.getElementById("courseId").value;
            let oldSubjectId = document.getElementById("oldSubjectId").value;
            let oldClassId = document.getElementById("oldClassId").value;
            if (courseId && oldSubjectId && oldClassId) {
                let newSubjectId = document.getElementById("newSubjectId").value;
                let newClassId = document.getElementById("newClassId").value;
                if (newSubjectId && newClassId) {
                    if (oldSubjectId === newSubjectId && oldClassId === newClassId) {
                        document.querySelector('.blurOverlay').style.visibility = 'visible';
                        document.getElementById("repeatEditData").showModal();
                    } else {
                        document.getElementById("editCourseForm").submit();
                    } 
                } else {
                    document.querySelector('.blurOverlay').style.visibility = 'visible';
                    document.getElementById("editFail2").showModal();
                }
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("editFail").showModal();
            }
            
        }
            

        function validateCreateForm() {
            let courseSubject = document.getElementById("courseSubject").value;
            let courseClass = document.getElementById("courseClass").value;
            if (courseSubject && courseClass) {
                document.getElementById("createSubjectForm").submit(); 
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("createFail").showModal();
            }
        }


        function preventSubmission(event) {
            event.preventDefault();
        }


        document.addEventListener('DOMContentLoaded', function() {
            const courseSubjectDp = document.getElementById('courseSubjectDropdown');
            const courseSubjectDp1 = document.getElementsByClassName('courseSubjectContainer')[0];
            const courseSubjectOpt = document.getElementsByClassName('courseSubjectOptions')[0];
            const courseClassDp = document.getElementById('courseClassDropdown');
            const courseClassDp1 = document.getElementsByClassName('courseClassContainer')[0];
            const courseClassOpt = document.getElementsByClassName('courseClassOptions')[0];
            const editCourseDp = document.getElementById('editCourseDropdown');
            const editCourseDp1 = document.getElementsByClassName('editCourseContainer')[0];
            const editCourseOpt = document.getElementsByClassName('editCourseOptions')[0];
            const editCourseSubjectDp = document.getElementById('editCourseSubjectDropdown');
            const editCourseSubjectDp1 = document.getElementsByClassName('editCourseSubjectContainer')[0];
            const editCourseSubjectOpt = document.getElementsByClassName('editCourseSubjectOptions')[0];
            const editCourseClassDp = document.getElementById('editCourseClassDropdown');
            const editCourseClassDp1 = document.getElementsByClassName('editCourseClassContainer')[0];
            const editCourseClassOpt = document.getElementsByClassName('editCourseClassOptions')[0];
            const deleteCourseDp = document.getElementById('deleteCourseDropdown');
            const deleteCourseDp1 = document.getElementsByClassName('deleteCourseContainer')[0];
            const deleteCourseOpt = document.getElementsByClassName('deleteCourseOptions')[0];

            // Click outside closes dropdown
            document.addEventListener('click', function(event) {
                if (courseSubjectOpt.style.display === "block" && !courseSubjectDp.contains(event.target) && !courseSubjectDp1.contains(event.target) && !courseSubjectOpt.contains(event.target)) {
                    controlDropdown('courseSubjectOptions');
                }

                if (courseClassOpt.style.display === "block" && !courseClassDp.contains(event.target) && !courseClassDp1.contains(event.target) && !courseClassOpt.contains(event.target)) {
                    controlDropdown('courseClassOptions');
                }

                if (editCourseOpt.style.display === "block" && !editCourseDp.contains(event.target) && !editCourseDp1.contains(event.target) && !editCourseOpt.contains(event.target)) {
                    controlDropdown('editCourseOptions');
                }

                if (editCourseSubjectOpt.style.display === "block" && !editCourseSubjectDp.contains(event.target) && !editCourseSubjectDp1.contains(event.target) && !editCourseSubjectOpt.contains(event.target)) {
                    controlDropdown('editCourseSubjectOptions');
                }

                if (editCourseClassOpt.style.display === "block" && !editCourseClassDp.contains(event.target) && !editCourseClassDp1.contains(event.target) && !editCourseClassOpt.contains(event.target)) {
                    controlDropdown('editCourseClassOptions');
                }

                if (deleteCourseOpt.style.display === "block" && !deleteCourseDp.contains(event.target) && !deleteCourseDp1.contains(event.target) && !deleteCourseOpt.contains(event.target)) {
                    controlDropdown('deleteCourseOptions');
                }
            });
        });

        
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
    </body  >
</html>
