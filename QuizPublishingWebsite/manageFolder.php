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
$createStatus = 0;
$editStatus = 0;
$deleteStatus = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "conn.php";
    $repeatSign = 0; //usage -> value = 1 means stop insert or update process, then show dialog box
    $duplicateClassYearSign = 0; //for verification

    if ($_POST['form'] === "create") {
        //post values to create folder
        $folderName = $_POST["folderName"];
        $courseId = (int) $_POST["courseId"];

        $sql = "INSERT INTO folder (folder_name, course_id) VALUES ('$folderName', '$courseId');";
        mysqli_query($conn, $sql);

        if (mysqli_affected_rows($conn) === 0) {
            echo "<script>console.log('Failed to insert new folder into database \'morningkdb\' table \'folder\'!');</script>";
            echo "<script>alert('Unable to insert data.');</script>";
        } else {
            echo "<script>console.log('Successfully inserted new folder into database \'morningkdb\' table \'folder\'!');</script>";
            $createStatus = 1;
        }
    }
    
    else if ($_POST['form'] === "edit") {
        //post values to edit selected folder
        $folderId = (int) $_POST["folderIdForEdit"];
        $folderName = $_POST["editFolderName"];

        $sqlUpdate = "UPDATE folder SET folder_name = '$folderName' WHERE folder_id = '$folderId';";

        mysqli_query($conn, $sqlUpdate);
        if (mysqli_affected_rows($conn) === 0) {
            echo "<script>console.log('Failed to edit folder in database \'morningkdb\' table \'folder\'!');</script>";
            echo "<script>alert('Unable to edit data.');</script>";
        } else {
            echo "<script>console.log('Successfully edited folder in database \'morningkdb\' table \'folder\'!');</script>";
            $editStatus = 1;
        }
    }

    else if ($_POST['form'] === "delete") {
        $folderId = (int) $_POST["folderIdForDelete"];
        
        $sqlQuiz = "SELECT quiz_id FROM exercise WHERE folder_id = '$folderId';";
        $result = mysqli_query($conn, $sqlQuiz);
        while ($row = mysqli_fetch_array($result)) {
            $quizId = (int) $row['quiz_id'];
            $sql = "DELETE FROM quiz WHERE quiz_id = '$quizId'";
            mysqli_query($conn, $sql);
        }
        $sqlDelete = "DELETE FROM folder WHERE folder_id = '$folderId';";
        
        mysqli_query($conn, $sqlDelete);

        if (mysqli_affected_rows($conn) === 0) {
            echo "<script>console.log('Failed to delete folder from database \'morningkdb\' table \'folder\'!');</script>";
            echo "<script>alert('Unable to delete data.');</script>";
        } else {
            echo "<script>console.log('Successfully deleted folder from database \'morningkdb\' table \'folder\'!');</script>";
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

//get folders with their associating course information
$sqlCourseFolderForEdit = 
"SELECT 
    *
FROM 
    folder 
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
ON folder.course_id = fullCourseTable.courseid;";
$allCourseFoldersForEdit = mysqli_query($conn, $sqlCourseFolderForEdit);

$sqlCourseFolderForDelete = 
"SELECT 
    *
FROM 
    folder 
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
ON folder.course_id = fullCourseTable.courseid;";
$allCourseFoldersForDelete = mysqli_query($conn, $sqlCourseFolderForDelete);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Folders</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="manageFolder.css">
    <link rel="stylesheet" href="adminMngPages.css">
</head>
<body>
    <div class="blurOverlay"></div>
    <div id="parentBody">
        <div id="bodyContainer">
            <div id="contentHeaderContainer">
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
                                <h1 style="text-align: center;">Create New Folder</h1>
                            </div>
                            <form id="createCourseFolderForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="create">
                                <table>
                                    <tr>
                                        <th colspan="2"><br>Choose Course to Create Folder For:</th>
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
                                    <tr>
                                        <th><label for="folderName">Folder Name:</label></th>
                                        <td><input class="folderInput" type="text" name="folderName" id="folderName" onfocus="inpDesign(id)" oninput="inpDesign(id)" onfocusout="inpRevert(id)"></td>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForCreate" value="Assign" onclick="validateCreateForm()">
                            </form>      
                        </div>
                        <div class="contentBody" id="edit">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Update Course Folder</h1>
                            </div>
                            <br><br>
                            <em>Search folder by its name, or with its associating course, class or subject.</em>
                            <form id="editCourseFolderForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="edit">
                                <table>
                                    <tr>
                                        <th colspan="2">Course and Folder:</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="editFolderDropdownContainer">
                                                <button id="editCourseFolderDropdown" onclick="controlDropdown('editCourseFolderOptions')">
                                                    <div id="editCourseFolderDpText">Select</div>
                                                    <div class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="editCourseFolderContainer">
                                                    <input type="text" name="" id="editCourseFolderSearch" onkeyup="filterSearch(id, 'editCourseFolderOptions')" placeholder="Search Course Folder &quot;Course > Folder&quot;">
                                                    <div class="fullTableDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editCourseFolderOptions')">
                                                    </div>
                                                </div>
                                                <div class="editCourseFolderOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allCourseFoldersForEdit)) { ?>
                                                        <div class="option" data-editcourseid="<?php echo ($rows['courseid']." (".$rows['subjectname'].", ".$rows['classgrade']."-".$rows['classname']."-".$rows['classyear'].")");?>" data-editfolderid="<?php echo $rows['folder_id'];?>" data-editfoldername="<?php echo $rows['folder_name'];?>" onclick="editCourseFolder(this)">
                                                            <?php echo $rows['courseid']." (".$rows['subjectname'].", ".$rows['classgrade']."-".$rows['classname']."-".$rows['classyear'].") > ".$rows['folder_name']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="folderIdForEdit" id="folderIdForEdit" value="">
                                            <input type="hidden" name="oldFolderName" id="oldFolderName" value="">
                                        </th>
                                    </tr>
                                    <tbody id="editCourseFolderToggle">
                                        <tr>
                                            <th colspan="2">Course:</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2"><p style="font-weight: 500;" id="selectedCourseForEdit"></p></th>
                                        </tr>
                                        <tr>
                                            <th><label for="editFolderName">Updated Folder Name:</label></th>
                                            <td><input class="folderInput" type="text" name="editFolderName" id="editFolderName" onfocus="inpDesign(id)" oninput="inpDesign(id)" onfocusout="inpRevert(id)"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForEdit" value="Save Changes" onclick="validateEditForm()">
                            </form>  
                        </div>
                        <div class="contentBody" id="delete">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Delete Folder</h1>
                            </div>
                            <br><br>
                            <em>For the folder that is chosen to be deleted, quizzes contained in it will also be permanently deleted.</em>
                            <form id="deleteCourseFolderForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="delete">    
                                <table>
                                    <tr>
                                        <th colspan="2">Course Folder:</th>
                                    </tr>
                                    <tr>
                                        <th style="font-weight: normal;" colspan="2">
                                            <div class="customDropdown" id="deleteCourseFolderDropdownContainer">
                                                <button id="deleteCourseFolderDropdown" onclick="controlDropdown('deleteCourseFolderOptions')">
                                                    <div id="deleteCourseFolderDpText">Select</div>
                                                    <div class="fullTableDpImg" style="margin-right: calc(0.61rem - 1px);">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </div>
                                                </button>
                                                <div class="deleteCourseFolderContainer">
                                                    <input type="text" name="" id="deleteCourseFolderSearch" onkeyup="filterSearch(id, 'deleteCourseFolderOptions')" placeholder="Search Course Folder &quot;Course > Folder&quot;">
                                                    <div class="fullTableDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('deleteCourseFolderOptions')">
                                                    </div>
                                                </div>
                                                <div class="deleteCourseFolderOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allCourseFoldersForDelete)) { ?>
                                                        <div class="option" data-delfolderid="<?php echo $rows['folder_id'];?>" data-delfoldername="<?php echo $rows['folder_name'];?>" onclick="deleteCourseFolder(this)">
                                                            <?php echo $rows['courseid']." (".$rows['subjectname'].", ".$rows['classgrade']."-".$rows['classname']."-".$rows['classyear'].") > ".$rows['folder_name']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="folderIdForDelete" id="folderIdForDelete" value="">
                                        </th>
                                    </tr>
                                    <tbody id="deleteCourseFolderToggle">
                                    <tr>
                                            <th colspan="2">Folder to be deleted:</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2"><p style="font-weight: 500;" id="selectedCourseForDelete"></p></th>
                                        </tr>
                                    </tbody>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForDelete" value="Delete Course" onclick="validateDeleteForm()">
                            </form>
                                
                        </div>
                            
                    </div>
                </div>
            </div>
        </div>
    </div>

    <dialog id="repeatEditData">Data is not submitted because no changes are made.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="createFail">Choose a folder before submitting.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="longFolderName">Folder name is too long.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="editFail">Choose a folder to edit.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="editFail2">Choose a new course to be assigned to the course teacher.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="deleteFail">Choose a folder to delete.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="editMsg">Folder name is changed successfully.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="createMsg">Folder is created and saved successfully.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="deleteMsg">Folder is permanently deleted.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>
    

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

        let createStatus = <?php echo $createStatus?>;
        let editStatus = <?php echo $editStatus?>;
        let deleteStatus = <?php echo $deleteStatus?>;

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


        function controlDropdown(optionList) {
            let text, input, options;

            if (optionList === "courseOptions") {
                text = document.getElementById("courseDropdown");
                input = document.getElementsByClassName("courseContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("courseSearch").value = "";
            }

            if (optionList === "editCourseFolderOptions") {
                text = document.getElementById("editCourseFolderDropdown");
                input = document.getElementsByClassName("editCourseFolderContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editCourseFolderSearch").value = "";
            }

            if (optionList === "deleteCourseFolderOptions") {
                text = document.getElementById("deleteCourseFolderDropdown");
                input = document.getElementsByClassName("deleteCourseFolderContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("deleteCourseFolderSearch").value = "";
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


        function editCourseFolder(element) {
            let newText = element.innerText;
            document.getElementById("editCourseFolderDpText").textContent = newText;
            controlDropdown("editCourseFolderOptions");

            let courseId = element.dataset.editcourseid;
            let folderId = element.dataset.editfolderid; 
            let folderName = element.dataset.editfoldername;

            document.getElementById("selectedCourseForEdit").textContent = courseId;
            document.getElementById("folderIdForEdit").value = folderId;
            document.getElementById("editFolderName").value = folderName;
            document.getElementById("oldFolderName").value = folderName;
            document.getElementById("editCourseFolderToggle").style.display = "block";
        }


        function deleteCourseFolder(element) {
            let newText = element.innerText;
            document.getElementById("deleteCourseFolderDpText").textContent = newText;
            controlDropdown("deleteCourseFolderOptions");

            let folderId = element.dataset.delfolderid;
            let folderName= element.dataset.delfoldername;

            document.getElementById("selectedCourseForDelete").textContent = "\""+folderName+"\"";
            document.getElementById("folderIdForDelete").value = folderId;
            document.getElementById("deleteCourseFolderToggle").style.display = "block";    
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
            let folderId = document.getElementById("folderIdForDelete").value;
            if (folderId) {
                document.getElementById("deleteCourseFolderForm").submit(); 
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("deleteFail").showModal();
            }
        }


        function validateEditForm() {
            let folderId = document.getElementById("folderIdForEdit").value;
            if (folderId) {
                let newFolderName = document.getElementById("editFolderName").value;
                let oldFolderName = document.getElementById("oldFolderName").value;
                if (oldFolderName !== newFolderName) {
                    if (folderName.length < 30) {
                        document.querySelector('.blurOverlay').style.visibility = 'visible';
                        document.getElementById("longFolderName").showModal();
                    } else {
                        document.getElementById("editCourseFolderForm").submit();
                    } 
                } else {
                    document.querySelector('.blurOverlay').style.visibility = 'visible';
                    document.getElementById("repeatEditData").showModal();
                }
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("editFail").showModal();
            }
            
        }
            

        function validateCreateForm() {
            let courseId = document.getElementById("courseId").value;
            let folderName = document.getElementById("folderName").value;
            if (folderName && courseId) {
                if (folderName.length < 30) {
                    document.getElementById("createCourseFolderForm").submit(); 
                } else {
                    document.querySelector('.blurOverlay').style.visibility = 'visible';
                    document.getElementById("longFolderName").showModal();
                }
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("createFail").showModal();
            }
        }


        function preventSubmission(event) {
            event.preventDefault();
        }

        
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
