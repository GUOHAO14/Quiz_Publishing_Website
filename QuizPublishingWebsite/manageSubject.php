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
        $subjectName = $_POST["subjectName"];
        
        //select from database to make sure no record repetition
        $sqlRepeatSubject = "SELECT subject_name FROM subject;";
        $output = mysqli_query($conn, $sqlRepeatSubject);
        
        while ($row = mysqli_fetch_array($output)) {
            $repeatSubjectName = $row['subject_name'];

            if ($repeatSubjectName === $subjectName) {
                $repeatSign = 1;
                break;
            }
        }
        
        if ($repeatSign !== 1) {
            //no data repetition
            echo "<script>console.log('Inserting new subject into database \'morningkdb\' table \'subject\'...');</script>";
            $sqlCreate = "INSERT INTO subject (subject_name) VALUES ('$subjectName');";
            mysqli_query($conn, $sqlCreate);

            if (mysqli_affected_rows($conn) === 0) {
                echo "<script>console.log('Failed to insert new subject into database \'morningkdb\' table \'subject\'!');</script>";
                echo "<script>alert('Unable to insert data.');</script>";
            } else {
                echo "<script>console.log('Successfully inserted new subject into database \'morningkdb\' table \'subject\'!');</script>";
                $createStatus = 1;
            }
        } else {
            //data repetition
            echo "<script>console.log('Subject entered already exist in database \'morningkdb\' table \'subject\'.');</script>";
        }
    }
    
    else if ($_POST['form'] === "edit") {
        //post values to edit selected subject
        $newSubjectName = $_POST["editedSubjectName"];
        $oldSubjectName = $_POST["oldSubjectName"];

        //check if any changes are actually made
        if ($newSubjectName === $oldSubjectName) {
            echo "<script>console.log('Edited subject name is not saved because no changes are added.');</script>";
            $repeatSign = 1;
        } 
        else {
            //select from database to make sure no record repetition
            $sqlRepeatSubject = "SELECT subject_name FROM subject;";
            $output = mysqli_query($conn, $sqlRepeatSubject);
            
            while ($row = mysqli_fetch_array($output)) {
                $repeatSubjectName = $row['subject_name'];

                if ($repeatSubjectName === $newSubjectName) {
                    $repeatSign = 1;
                    break;
                }
            }
        }

        if ($repeatSign !== 1) {
            $sqlUpdate = "UPDATE subject SET subject_name = '$newSubjectName' WHERE subject_name = '$oldSubjectName';";

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
        $deleteSubjectName = $_POST["deleteSubject"];

        $sqlDelete = "DELETE FROM subject WHERE subject_name = '$deleteSubjectName';";
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

$sqlEditSubject = "SELECT subject_name FROM subject ORDER BY subject_name ASC;";
$allSubjectsForEdit = mysqli_query($conn, $sqlEditSubject);

$sqlDeleteSubject = "SELECT subject_name FROM subject ORDER BY subject_name ASC;";
$allSubjectsForDelete = mysqli_query($conn, $sqlDeleteSubject);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subject</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="manageSubject.css">
    <link rel="stylesheet" href="adminMngPages.css">
    <style>
        .editSubjectOptions div, .deleteSubjectOptions div{
            display: block;
        }
    </style>
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
                                <h1 style="text-align: center;">Create New Subject</h1>
                            </div>
                            <br><br>
                            <em>All characters in the entered subject name will be automatically converted into uppercase.</em>
                            <form id="createSubjectForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="create">
                                <table>
                                    <tr>
                                        <th><label for="subjectName">Subject Name:</label></th>
                                        <td><input class="subjectInput" type="text" name="subjectName" id="subjectName" onfocus="inpDesign(id)" oninput="inpDesign(id)" onfocusout="inpRevert(id)"></td>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForCreate" value="Save Subject" onclick="validateCreateForm()">
                            </form>      
                        </div>
                        <div class="contentBody" id="edit">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Edit Subject</h1>
                            </div>
                            <form id="editSubjectForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="edit">
                                <table>
                                    <tr>
                                        <th>Subject Name:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="editSubjectDropdown" onclick="controlDropdown('editSubjectOptions')">
                                                    <div id="editSubjectdpText">Select</div>
                                                    <span class="dpImg editImg">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="editSubjectContainer">
                                                    <input type="text" name="" id="editSearch" onkeyup="filterSearch(id, 'editSubjectOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editSubjectOptions')">
                                                    </div>
                                                </div>
                                                <div class="editSubjectOptions">
                                                    <div></div>
                                                    <?php while ($rows = mysqli_fetch_array($allSubjectsForEdit)) { ?>
                                                        <div class="option" data-name="<?php echo $rows['subject_name'];?>" onclick="editSubject(this)">
                                                            <?php echo $rows['subject_name']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <input type="hidden" name="oldSubjectName" id="oldSubjectName" value="">
                                    <tr>
                                        <th><label for="editSubjectName">Updated Subject Name:</label></th>
                                        <td><input class="subjectInput" type="text" name="editedSubjectName" id="editSubjectName" onfocus="inpDesign(id)" oninput="inpDesign(id)" onfocusout="inpRevert(id)"></td>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForEdit" value="Save Changes" onclick="validateEditForm()">
                            </form>  
                        </div>
                        <div class="contentBody" id="delete">
                            <div id="mngHeading">
                                <h1 style="text-align: center;">Delete Subject</h1>
                            </div>
                            <br><br>
                            <em>You cannot delete a class that is already assigned to students. If you want to change student's class, go to edit profile section.</em>
                            <form id="deleteSubjectForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="delete">    
                                <table>
                                    <tr>
                                        <th>Subject Name:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="deleteSubjectDropdown" onclick="controlDropdown('deleteSubjectOptions')">
                                                    <div id="deleteSubjectdpText">Select</div>
                                                    <span class="dpImg editImg">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="deleteSubjectContainer">
                                                    <input type="text" name="" id="deleteSubjectSearch" onkeyup="filterSearch(id, 'deleteSubjectOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('deleteSubjectOptions')">
                                                    </div>
                                                </div>
                                                <div class="deleteSubjectOptions">
                                                    <?php while ($rows = mysqli_fetch_array($allSubjectsForDelete)) { ?>
                                                        <div class="option" data-name="<?php echo $rows['subject_name']?>" onclick="deleteSubject(this)">
                                                            <?php echo $rows['subject_name']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="deleteSubject" id="deleteSubject" value="">
                                        </td>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForDelete" value="Delete Class" onclick="validateDeleteForm()">
                            </form>
                                
                        </div> 
                    </div>
                </div>
            </div>
        </div>    
    </div>    

    <dialog id="repeatData">Data discarded because it already exist in database.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="createFail">Subject name must be 3 or more characters.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="editFail">Choose a subject to edit.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="deleteFail">Choose a subject to delete.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="editMsg">Subject name changes are saved.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="createMsg">Entered subject is created and saved successfully.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>

    <dialog id="deleteMsg">Subject is permanently deleted.<br><button id="msgExit" onclick="okayExit()">OKAY</button></dialog>
    
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
            if (optionList === "editSubjectOptions") {
                text = document.getElementById("editSubjectDropdown");
                input = document.getElementsByClassName("editSubjectContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editSearch").value = "";
            }

            if (optionList === "deleteSubjectOptions") {
                text = document.getElementById("deleteSubjectDropdown");
                input = document.getElementsByClassName("deleteSubjectContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("deleteSubjectSearch").value = "";
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

        
        function editSubject(element) {
            let newText = element.innerText;
            document.getElementById("editSubjectdpText").textContent = newText;
            controlDropdown("editSubjectOptions");

            let subjectName = element.dataset.name;

            let newSubjectName = document.getElementById("editSubjectName");
            let oldSubjectName = document.getElementById("oldSubjectName");

            console.log(subjectName);

            newSubjectName.value = subjectName;
            oldSubjectName.value = subjectName;
        }


        function deleteSubject(element) {
            let newText = element.innerText;
            document.getElementById("deleteSubjectdpText").textContent = newText;
            controlDropdown("deleteSubjectOptions");

            let subjectName = element.dataset.name;

            let deleteSubject = document.getElementById("deleteSubject");

            deleteSubject.value = subjectName;
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
            let deleteSubject = document.getElementById("deleteSubject").value;
            if (deleteSubject) {
                document.getElementById("deleteSubjectForm").submit(); 
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("deleteFail").showModal();
            }
        }


        function validateEditForm() {
            let oldSubjectName = document.getElementById("oldSubjectName").value;
            if (oldSubjectName) {
                let newSubjectName = document.getElementById("editSubjectName").value;
                console.log(newSubjectName);
                if (newSubjectName.length > 2 && newSubjectName.length < 30) {
                    document.getElementById("editSubjectName").value = newSubjectName.toUpperCase();
                    document.getElementById("editSubjectForm").submit(); 
                } else {
                    document.querySelector('.blurOverlay').style.visibility = 'visible';
                    document.getElementById("createFail").showModal();
                }
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("editFail").showModal();
            }
            
        }
            

        function validateCreateForm() {
            let subjectName = document.getElementById("subjectName").value;
            if (subjectName.length > 2) {
                document.getElementById("subjectName").value = subjectName.toUpperCase();
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
            const editSubjectDp = document.getElementById('editSubjectDropdown');
            const editSubjectDp1 = document.getElementsByClassName('editSubjectContainer')[0];
            const editSubjectOpt = document.getElementsByClassName('editSubjectOptions')[0];
            const deleteSubjectDp = document.getElementById('deleteSubjectDropdown');
            const deleteSubjectDp1 = document.getElementsByClassName('deleteSubjectContainer')[0];
            const deleteSubjectOpt = document.getElementsByClassName('deleteSubjectOptions')[0];

            // Click outside closes dropdown
            document.addEventListener('click', function(event) {
                if (editSubjectOpt.style.display === "block" && !editSubjectDp.contains(event.target) && !editSubjectDp1.contains(event.target) && !editSubjectOpt.contains(event.target)) {
                    controlDropdown('editSubjectOptions');
                }

                if (deleteSubjectOpt.style.display === "block" && !deleteSubjectDp.contains(event.target) && !deleteSubjectDp1.contains(event.target) && !deleteSubjectOpt.contains(event.target)) {
                    controlDropdown('deleteSubjectOptions');
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
