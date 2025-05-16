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
$duplicateClassYearSign = 0; //from verification
$createStatus = 0;
$editStatus = 0;
$deleteStatus = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "conn.php";
    $repeatSign = 0; //usage -> value = 1 means stop insert or update process, then show dialog box
    $duplicateClassYearSign = 0; //for verification

    if ($_POST['form'] === "create") {
        //post values to create subject
        $studentId = (int) $_POST["studentId"];
        $classId = (int) $_POST["classId"];
        $classYear = (int) $_POST["classYear"];
        
        //retrieve data for data verification
        $sqlEnrolment = 
        "SELECT 
            enrolment.student_id, 
            enrolment.class_id,
            class.year
        FROM 
            enrolment
        JOIN 
            class ON enrolment.class_id = class.class_id;";
        $enrolmentForRepeatCheck = mysqli_query($conn, $sqlEnrolment);
        
        while ($row = mysqli_fetch_array($enrolmentForRepeatCheck)) {
            $existingStudent = (int) $row['student_id'];
            $existingClass = (int) $row['class_id'];
            $existingClassYear = (int) $row['year'];
            
            //verification 1: ensure no duplicate student and class
            if ($studentId === $existingStudent && $classId === $existingClass) {
                $repeatSign = 1;
                break;
            }

            //verification 2: ensure student has only one class per year
            if ($studentId === $existingStudent && $classYear === $existingClassYear) {
                $duplicateClassYearSign = $classYear;
                break;
            }
        }
        
        if ($repeatSign !== 1 && $duplicateClassYearSign !== $classYear) {
            //data verified
            echo "<script>console.log('Inserting new enrolment into database \'morningkdb\' table \'enrolment\'...');</script>";
            $sqlCreate = "INSERT INTO enrolment (student_id, class_id) VALUES ('$studentId', '$classId');";
            mysqli_query($conn, $sqlCreate);

            if (mysqli_affected_rows($conn) === 0) {
                echo "<script>console.log('Failed to insert new enrolment into database \'morningkdb\' table \'enrolment\'!');</script>";
                echo "<script>alert('Unable to insert data.');</script>";
            } else {
                echo "<script>console.log('Successfully inserted new enrolment into database \'morningkdb\' table \'enrolment\'!');</script>";
                $createStatus = 1;
            }
        } else {
            //data repetition
            echo "<script>console.log('Invalid enrolment. Failed to insert into database \'morningkdb\' table \'enrolment\'.');</script>";
        }
    }
    
    else if ($_POST['form'] === "edit") {
        //post values to edit selected subject
        $studentId = (int) $_POST["studentIdForEdit"];
        $newClassYear = (int) $_POST["newClassYear"];
        $oldClassYear = (int) $_POST["oldClassYear"];
        $newClassId = (int) $_POST["newClassId"];
        $oldClassId = (int) $_POST["oldClassId"];

        echo "<script>console.log($studentId);</script>";
        echo "<script>console.log($newClassYear);</script>";
        echo "<script>console.log($newClassId);</script>";
        echo "<script>console.log($oldClassYear);</script>";

        //retrieve data for data verification
        $sqlEnrolment = 
        "SELECT 
            enrolment.student_id, 
            enrolment.class_id,
            class.year
        FROM 
            enrolment
        JOIN 
            class ON enrolment.class_id = class.class_id;";
        $enrolmentForRepeatCheck = mysqli_query($conn, $sqlEnrolment);
        
        while ($row = mysqli_fetch_array($enrolmentForRepeatCheck)) {
            $existingStudent = (int) $row['student_id'];
            $existingClass = (int) $row['class_id'];
            $existingClassYear = (int) $row['year'];
            echo "<script>console.log($existingStudent);</script>";
            echo "<script>console.log($existingClass);</script>";
            echo "<script>console.log($existingClassYear);</script>";

            //verification 1: ensure no duplicate student and class
            if ($studentId === $existingStudent && $newClassId === $existingClass) {
                $repeatSign = 1;
                break;
            }

            //verification 2: ensure student has only one class per year
            if ($studentId === $existingStudent && $existingClassYear !== $oldClassYear && $newClassYear === $existingClassYear) {
                $duplicateClassYearSign = $existingClassYear;
                break;
            }
        }

        if ($repeatSign !== 1 && $duplicateClassYearSign !== $existingClassYear) {
            $sqlUpdate = "UPDATE enrolment SET class_id = '$newClassId' WHERE student_id = '$studentId' AND class_id = '$oldClassId';";

            mysqli_query($conn, $sqlUpdate);
            if (mysqli_affected_rows($conn) === 0) {
                echo "<script>console.log('Failed to edit enrolment in database \'morningkdb\' table \'enrolment\'!');</script>";
                echo "<script>alert('Unable to edit data.');</script>";
            } else {
                echo "<script>console.log('Successfully edited enrolment in database \'morningkdb\' table \'enrolment\'!');</script>";
                $editStatus = 1;
            }
        } else {
            //data repetition
            echo "<script>console.log('Invalid enrolment. Failed to update database \'morningkdb\' table \'cenrolment\'.');</script>";
        }
    }

    else if ($_POST['form'] === "delete") {
        $studentId = (int) $_POST["studentIdForDelete"];
        $classId = (int) $_POST["classIdForDelete"];

        $sqlDelete = "DELETE FROM enrolment WHERE student_id = '$studentId' AND class_id = '$classId';";
        mysqli_query($conn, $sqlDelete);

        if (mysqli_affected_rows($conn) === 0) {
            echo "<script>console.log('Failed to delete enrolment from database \'morningkdb\' table \'enrolment\'!');</script>";
            echo "<script>alert('Unable to delete data.');</script>";
        } else {
            echo "<script>console.log('Successfully deleted enrolment from database \'morningkdb\' table \'enrolment\'!');</script>";
            $deleteStatus = 1;
        }
    }
}

include "conn.php";

//get classes
$sqlClassForEnrol = "SELECT * FROM class ORDER BY year DESC, grade ASC, class_name ASC;";
$allClassesForEnrol = mysqli_query($conn, $sqlClassForEnrol);

$sqlClassForEdit = "SELECT * FROM class ORDER BY year DESC, grade ASC, class_name ASC;";
$allClassesForEdit = mysqli_query($conn, $sqlClassForEdit);

//get student id from student table, and name from user table
$sqlStudentForEnrol = 
"SELECT 
    student.student_id, 
    user.username AS student_name
FROM 
    student 
JOIN 
    user ON student.user_id = user.user_id
ORDER BY 
    student_id DESC;";
$allStudentsForEnrol = mysqli_query($conn, $sqlStudentForEnrol);

//get enrolments from db for edit
$sqlEnrolmentForEdit = 
"SELECT 
    enrolment.*,
    studentTable.student_name,
    class.*
FROM 
    enrolment 
JOIN 
    (SELECT 
    student.student_id, 
    user.username AS student_name
    FROM 
        student 
    JOIN 
        user ON student.user_id = user.user_id
    ORDER BY 
        student_id DESC) as studentTable
ON 
    enrolment.student_id = studentTable.student_id
JOIN 
    class
ON 
    enrolment.class_id = class.class_id
ORDER BY enrolment.student_id DESC;";
$allEnrolmentsForEdit = mysqli_query($conn, $sqlEnrolmentForEdit);

//get enrolments from db for delete
$sqlEnrolmentForDelete = 
"SELECT 
    enrolment.*,
    studentTable.student_name,
    class.*
FROM 
    enrolment 
JOIN 
    (SELECT 
    student.student_id, 
    user.username AS student_name
    FROM 
        student 
    JOIN 
        user ON student.user_id = user.user_id
    ORDER BY 
        student_id DESC) as studentTable
ON 
    enrolment.student_id = studentTable.student_id
JOIN 
    class
ON 
    enrolment.class_id = class.class_id
ORDER BY enrolment.student_id DESC;";
$allEnrolmentsForDelete = mysqli_query($conn, $sqlEnrolmentForDelete);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="enrolStudent.css">
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
                                <h1 style="text-align: center;">Enrol Students to Classes</h1>
                            </div>
                            <br><br>
                            <em>Ensure that each student only has one class per year.</em>
                            <form id="createEnrolmentForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="create">
                                <table>
                                    <tr>
                                        <th>Student:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="enrolStudentDropdown" onclick="controlDropdown('enrolStudentOptions')">
                                                    <div id="enrolStudentdpText">Select</div>
                                                    <span class="inpTextDpImg">
                                                        <img src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="enrolStudentContainer">
                                                    <input type="text" name="" id="enrolStudentSearch" onkeyup="filterSearch(id, 'enrolStudentOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img src="media/up.png" alt="Up Arrow" onclick="controlDropdown('enrolStudentOptions')">
                                                    </div>
                                                </div>
                                                <div class="enrolStudentOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allStudentsForEnrol)) { ?>
                                                        <div class="option" data-studentid="<?php echo $rows['student_id'];?>" onclick="chooseStudent(this)">
                                                            <?php echo "STU".$rows['student_id']." - ".$rows['student_name']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="studentId" id="studentId" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Class:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="enrolClassDropdown" onclick="controlDropdown('enrolClassOptions')">
                                                    <div id="enrolClassdpText">Select</div>
                                                    <span class="inpTextDpImg">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="enrolClassContainer">
                                                    <input type="text" name="" id="enrolClassSearch" onkeyup="filterSearch(id, 'enrolClassOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('enrolClassOptions')">
                                                    </div>
                                                </div>
                                                <div class="enrolClassOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allClassesForEnrol)) { ?>
                                                        <div class="option" data-classid="<?php echo $rows['class_id'];?>" data-classyear="<?php echo $rows['year'];?>" onclick="chooseStudentClass(this)">
                                                            <?php echo $rows['grade']."-".$rows['class_name']."-".$rows['year']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="classId" id="classId" value="">
                                            <input type="hidden" name="classYear" id="classYear" value="">
                                        </td>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForCreate" value="Enrol" onclick="validateCreateForm()">
                            </form>      
                        </div>
                        <div class="contentBody" id="edit">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Update Enrolment</h1>
                            </div>
                            <br><br>
                            <em>You can change the class enrolled by students, but not the students themselves.</em>
                            <form id="editEnrolmentForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="edit">
                                <table>
                                    <tr>
                                        <th colspan="2">Enrolment:</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="editEnrolmentDropdownContainer">
                                                <button id="editEnrolmentDropdown" onclick="controlDropdown('editEnrolmentOptions')">
                                                    <div id="editEnrolmentdpText">Select</div>
                                                    <div class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="editEnrolmentContainer">
                                                    <input type="text" name="" id="editEnrolmentSearch" onkeyup="filterSearch(id, 'editEnrolmentOptions')" placeholder="Search &quot;Enrolment (Subject, Class)&quot;">
                                                    <div class="fullTableDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editEnrolmentOptions')">
                                                    </div>
                                                </div>
                                                <div class="editEnrolmentOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allEnrolmentsForEdit)) { ?>
                                                        <div class="option" data-editstudentid="<?php echo $rows['student_id'];?>" data-studentname="<?php echo $rows['student_name'];?>" data-classid="<?php echo $rows['class_id'];?>" data-classyear="<?php echo $rows['year'];?>" onclick="editEnrolment(this)">
                                                            <?php echo "STU".$rows['student_id']." - ".$rows['student_name']." (".$rows['grade']."-".$rows['class_name']."-".$rows['year'].")"; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="studentIdForEdit" id="studentIdForEdit" value="">
                                            <input type="hidden" name="oldClassId" id="oldClassId" value="">
                                            <input type="hidden" name="oldClassYear" id="oldClassYear" value="">
                                        </th>
                                    </tr>
                                    <tbody id="editEnrolmentToggle">
                                        <tr>
                                            <th>Selected Student:</th>
                                            <td style="padding-left: 20px; justify-content: left;"><strong id="selectedStudentForEdit"></strong></td>
                                        </tr>
                                        <tr>
                                            <th>New Class:</th>
                                            <td>
                                                <div class="customDropdown">
                                                    <button id="editStudentClassDropdown" onclick="controlDropdown('editStudentClassOptions')">
                                                        <div id="editStudentClassdpText">Select</div>
                                                        <span class="inpTextDpImg">
                                                            <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                        </span>
                                                    </button>
                                                    <div class="editStudentClassContainer">
                                                        <input type="text" name="" id="editStudentClassSearch" onkeyup="filterSearch(id, 'editStudentClassOptions')" placeholder="Search">
                                                        <div class="inpDpImg">
                                                            <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editStudentClassOptions')">
                                                        </div>
                                                    </div>
                                                    <div class="editStudentClassOptions">
                                                        <div></div>
                                                        <?php while ($rows = mysqli_fetch_array($allClassesForEdit)) { ?>
                                                            <div class="option" data-classid="<?php echo $rows['class_id'];?>" data-classyear="<?php echo $rows['year'];?>" onclick="editStudentClass(this)">
                                                            <?php echo $rows['grade']."-".$rows['class_name']."-".$rows['year']; ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="newClassId" id="newClassId">
                                                <input type="hidden" name="newClassYear" id="newClassYear">
                                            </td>
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
                            <form id="deleteEnrolmentForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="delete">    
                                <table>
                                    <tr>
                                        <th colspan="2">Enrolment:</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="deleteEnrolmentDropdownContainer">
                                                <button id="deleteEnrolmentDropdown" onclick="controlDropdown('deleteEnrolmentOptions')">
                                                    <div id="deleteEnrolmentdpText">Select</div>
                                                    <div class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="deleteEnrolmentContainer">
                                                    <input type="text" name="" id="deleteEnrolmentSearch" onkeyup="filterSearch(id, 'deleteEnrolmentOptions')" placeholder="Search &quot;Enrolment ID (Subject, Class)&quot;">
                                                    <div class="fullTableDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('deleteEnrolmentOptions')">
                                                    </div>
                                                </div>
                                                <div class="deleteEnrolmentOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allEnrolmentsForDelete)) { ?>
                                                        <div class="option" data-deletestudentid="<?php echo $rows['student_id'];?>" data-deleteclassid="<?php echo $rows['class_id'];?>" onclick="deleteEnrolment(this)">
                                                            <?php echo "STU".$rows['student_id']." - ".$rows['student_name']." (".$rows['grade']."-".$rows['class_name']."-".$rows['year'].")"; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="studentIdForDelete" id="studentIdForDelete" value="">
                                            <input type="hidden" name="classIdForDelete" id="classIdForDelete" value="">
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

        <dialog id="repeatData">Enrolment cannot be saved because it already exist.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="oneClassPerYearMsg">Enrolment cannot be saved because selected student already has a class in the year <?php echo $duplicateClassYearSign?>.<br><br>Go to edit section to change class.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="repeatEditData">Data is not submitted because no changes are made.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="createFail">Choose a student and a class before submitting enrolment.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="editFail">Choose an enrolment to edit.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="editFail2">Choose a new class to edit the chosen enrolment.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="deleteFail">Choose a enrolment to delete.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="editMsg">Enrolment change is saved.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="createMsg">Enrolment is created and saved successfully.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

        <dialog id="deleteMsg">Enrolment is permanently deleted.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>
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
        let duplicateClassYearSign = <?php echo $duplicateClassYearSign?>;
        let createStatus = <?php echo $createStatus?>;
        let editStatus = <?php echo $editStatus?>;
        let deleteStatus = <?php echo $deleteStatus?>;

        if (repeatSign === 1) {
            document.querySelector(".blurOverlay").style.visibility = "visible";
            document.getElementById("repeatData").showModal();
        }

        if (duplicateClassYearSign !== 0) {
            document.querySelector(".blurOverlay").style.visibility = "visible";
            document.getElementById("oneClassPerYearMsg").showModal();
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

            if (optionList === "enrolClassOptions") {
                text = document.getElementById("enrolClassDropdown");
                input = document.getElementsByClassName("enrolClassContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("enrolClassSearch").value = "";
            }

            if (optionList === "enrolStudentOptions") {
                text = document.getElementById("enrolStudentDropdown");
                input = document.getElementsByClassName("enrolStudentContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("enrolStudentSearch").value = "";
            }

            if (optionList === "editEnrolmentOptions") {
                text = document.getElementById("editEnrolmentDropdown");
                input = document.getElementsByClassName("editEnrolmentContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editEnrolmentSearch").value = "";
            }

            if (optionList === "editStudentClassOptions") {
                text = document.getElementById("editStudentClassDropdown");
                input = document.getElementsByClassName("editStudentClassContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editStudentClassSearch").value = "";
            }

            if (optionList === "deleteEnrolmentOptions") {
                text = document.getElementById("deleteEnrolmentDropdown");
                input = document.getElementsByClassName("deleteEnrolmentContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("deleteEnrolmentSearch").value = "";
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


        function chooseStudentClass(element) {
            let newText = element.innerText;
            document.getElementById("enrolClassdpText").textContent = newText;
            controlDropdown("enrolClassOptions");

            let classId = element.dataset.classid;
            let classYear = element.dataset.classyear;

            let enrolClass = document.getElementById("classId");
            let enrolClassYear = document.getElementById("classYear");

            enrolClass.value = classId;
            enrolClassYear.value = classYear;
        }


        function chooseStudent(element) {
            let newText = element.innerText;
            document.getElementById("enrolStudentdpText").textContent = newText;
            controlDropdown("enrolStudentOptions");

            let studentId = element.dataset.studentid;

            let enrolStudent = document.getElementById("studentId");

            enrolStudent.value = studentId;
        }


        function editEnrolment(element) {
            let newText = element.innerText;
            document.getElementById("editEnrolmentdpText").textContent = newText;
            controlDropdown("editEnrolmentOptions");

            let studentId = element.dataset.editstudentid;
            let studentName = element.dataset.studentname;
            let classId = element.dataset.classid;
            let classYear = element.dataset.classyear;

            let chosenStudentId = document.getElementById("studentIdForEdit");
            let oldClassId = document.getElementById("oldClassId");
            let oldClassYear = document.getElementById("oldClassYear");

            chosenStudentId.value = studentId;
            oldClassId.value = classId;
            oldClassYear.value = classYear;

            document.getElementById("editEnrolmentToggle").style.display = "block";
            document.getElementById("selectedStudentForEdit").textContent = studentId+" - "+studentName;
        }


        function editStudentClass(element) {
            let newText = element.innerText;
            document.getElementById("editStudentClassdpText").textContent = newText;
            controlDropdown("editStudentClassOptions");

            let classId = element.dataset.classid;
            let classYear = element.dataset.classyear;

            let newClassId = document.getElementById("newClassId");
            let newClassYear = document.getElementById("newClassYear");

            newClassId.value = classId;
            newClassYear.value = classYear;
        }


        function deleteEnrolment(element) {
            let newText = element.innerText;
            document.getElementById("deleteEnrolmentdpText").textContent = newText;
            controlDropdown("deleteEnrolmentOptions");

            let studentId = element.dataset.deletestudentid;
            let classId = element.dataset.deleteclassid;

            let chosenStudentId = document.getElementById("studentIdForDelete");
            let chosenClassId = document.getElementById("classIdForDelete");

            chosenStudentId.value = studentId;
            chosenClassId.value = classId;
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
            let studentId = document.getElementById("studentIdForDelete").value;
            let classId = document.getElementById("classIdForDelete").value;
            if (studentId && classId) {
                document.getElementById("deleteEnrolmentForm").submit(); 
            } else {
                document.querySelector(".blurOverlay").style.visibility = "visible";
                document.getElementById("deleteFail").showModal();
            }
        }


        function validateEditForm() {
            let studentId = document.getElementById("studentIdForEdit").value;
            let oldClassYear = document.getElementById("oldClassYear").value;
            let oldClassId = document.getElementById("oldClassId").value;
            if (studentId && oldClassYear && oldClassId) {
                let newClassYear = document.getElementById("newClassYear").value;
                let newClassId = document.getElementById("newClassId").value;
                if (newClassYear && newClassId) {
                    if (oldClassYear === newClassYear && oldClassId === newClassId) {
                        document.querySelector(".blurOverlay").style.visibility = "visible";
                        document.getElementById("repeatEditData").showModal();
                    } else {
                        document.getElementById("editEnrolmentForm").submit();
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
            let studentId = document.getElementById("studentId").value;
            let classId = document.getElementById("classId").value;
            let classYear = document.getElementById("classYear").value;
            if (studentId && classId && classYear) {
                document.getElementById("createEnrolmentForm").submit(); 
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
