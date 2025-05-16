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
$repeatSign = 0;
$createStatus = 0;
$editStatus = 0;
$deleteStatus = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "conn.php";
    $repeatSign = 0;

    if ($_POST['form'] === "create") {
        //change data type
        $grade = (int) $_POST["gradeCl"];
        $className = $_POST["className"];
        $year = (int) $_POST["year"];
        
        $sqlClass = "SELECT class_name, grade, year FROM class;";
        $clOutput = mysqli_query($conn, $sqlClass);

        while ($classRow = mysqli_fetch_array($clOutput)) {
            $grVerify = $classRow['grade'];
            $clNmVerify = $classRow['class_name'];
            $yrVerify = $classRow['year'];

            if ($grade == $grVerify && $className == $clNmVerify && $year == $yrVerify) {
                $repeatSign = 1;
                break;
            }
        }
        
        if ($repeatSign !== 1) {
            echo "<script>console.log('Inserting class data into database \'morningkdb\' table \'class\'...');</script>";
            $sqlSave = "INSERT INTO class (grade, class_name, year) VALUES ('$grade', '$className', '$year');";
            mysqli_query($conn, $sqlSave);

            if (mysqli_affected_rows($conn) <= 0) {
                echo "<script>console.log('Failed to insert new class data into database \'morningkdb\' table \'class\'!');</script>";
                echo "<script>alert('Unable to insert data.');</script>";
            } else {
                echo "<script>console.log('Successfully inserted new class data into database \'morningkdb\' table \'class\'!');</script>";
                $createStatus = 1;
            }
        } else {
            echo "<script>console.log('Class data already exist in database \'morningkdb\' table \'class\'.');</script>";
        }
    }
    
    else if ($_POST['form'] === "edit") {
        $editGrade = (int) $_POST["editedGrade"];
        $editClName = $_POST["editedClName"];
        $editYear = (int) $_POST["editedYear"];

        $oldGrade = (int) $_POST["oldGrade"];
        $oldClName = $_POST["oldClName"];
        $oldYear = (int) $_POST["oldYear"];

        //check if any changes are actually made
        if ($editGrade === $oldGrade && $editClName === $oldClName && $editYear === $oldYear) {
            echo "<script>console.log('Class data is not edited because no changes are added.');</script>";
            $repeatSign = 1;
        } 
        else {
            //select from database to make sure no record repetition
            $sqlClass = "SELECT class_name, grade, year FROM class;";
            $clOutput = mysqli_query($conn, $sqlClass);
            while ($classRow = mysqli_fetch_array($clOutput)) {
                $grVerify = $classRow['grade'];
                $clNmVerify = $classRow['class_name'];
                $yrVerify = $classRow['year'];
    
                if ($editGrade == $grVerify && $editClName == $clNmVerify && $editYear == $yrVerify) {
                    $repeatSign = 1;
                    echo "<script>console.log('Class data is not edited because it clashes with existing data.');</script>";
                    break;
                }
            }
        }

        if ($repeatSign !== 1) {
            $sqlUpdate = "UPDATE class
                SET grade = '$editGrade', class_name = '$editClName', year = '$editYear'
                WHERE grade = '$oldGrade' AND class_name = '$oldClName' AND year = '$oldYear';
            ";

            mysqli_query($conn, $sqlUpdate);
            if (mysqli_affected_rows($conn) <= 0) {
                echo "<script>console.log('Failed to edit class data in database \'morningkdb\' table \'class\'!');</script>";
                echo "<script>alert('Unable to edit data.');</script>";
            } else {
                echo "<script>console.log('Successfully edited class data in database \'morningkdb\' table \'class\'!');</script>";
                $editStatus = 1;
            }
        }
    }

    else if ($_POST['form'] === "delete") {
        $deleteGrade = (int) $_POST["deleteGrade"];
        $deleteClName = $_POST["deleteClName"];
        $deleteYear = (int) $_POST["deleteYear"];

        $sqlDelete = "DELETE FROM class WHERE grade = '$deleteGrade' AND class_name = '$deleteClName' AND year = '$deleteYear';";
        mysqli_query($conn, $sqlDelete);

        if (mysqli_affected_rows($conn) <= 0) {
            echo "<script>console.log('Failed to delete class data from database \'morningkdb\' table \'class\'!');</script>";
            echo "<script>alert('Unable to delete data.');</script>";
        } else {
            echo "<script>console.log('Successfully deleted class data from database \'morningkdb\' table \'class\'!');</script>";
            $deleteStatus = 1;
        }
        
    }
}

include "conn.php";

$sqlGrade = "SELECT grade_level FROM grade;";
$gradeOutput = mysqli_query($conn, $sqlGrade);

$sqlGrade2 = "SELECT grade_level FROM grade;";
$gradeOutput2 = mysqli_query($conn, $sqlGrade2);

$sqlClass = "SELECT class_name FROM classname;";
$classOutput = mysqli_query($conn, $sqlClass);

$sqlClass2 = "SELECT class_name FROM classname;";
$classOutput2 = mysqli_query($conn, $sqlClass2);

$sqlEditClass = "SELECT * FROM class ORDER BY year DESC, grade ASC, class_name ASC";
$allClassesForEdit = mysqli_query($conn, $sqlEditClass);

$sqlDeleteClass = "SELECT * FROM class ORDER BY year DESC, grade ASC, class_name ASC";
$allClassesForDelete = mysqli_query($conn, $sqlDeleteClass);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="manageClass.css">
    <link rel="stylesheet" href="adminMngPages.css">
    <style>
        .editClassOptions div, .deleteClassOptions div{
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
                                <h1 style="text-align: center;" id="ceTitle">Create New Class</h1>
                            </div>
                            <form id="createClassForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="create">
                                <input type="hidden" name="verifyInput" id="verifyInput" value="">
                                <table>
                                    <tr>
                                        <th>Grade:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="gradeDropdown" onclick="toggleDropdown('gradeOptions')">
                                                    <div id="gdpText">Select</div>
                                                    <span class="dpImg">
                                                        <img id="grUp" src="media/up.png" alt="Up Arrow">
                                                        <img id="grDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="gradeOptions">
                                                    <?php while ($rows = mysqli_fetch_array($gradeOutput)) { ?>
                                                        <div class="option" dataValue="<?php echo trim($rows['grade_level']); ?>" onclick="selectOption(this, 'gdpText', 'grCl', 'gradeOptions')">
                                                            <?php echo trim($rows['grade_level']); ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <input type="hidden" name="gradeCl" id="grCl">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Class Name:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="classDropdown" onclick="toggleDropdown('classOptions')">
                                                    <span id="cdpText">Select</span>
                                                    <span class="dpImg">
                                                        <img id="clUp" src="media/up.png" alt="Up Arrow">
                                                        <img id="clDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="classOptions">
                                                    <?php while ($rows = mysqli_fetch_array($classOutput)) { ?>
                                                        <div class="option" dataValue="<?php echo trim($rows['class_name']); ?>" onclick="selectOption(this, 'cdpText', 'clCl', 'classOptions')">
                                                            <?php echo trim($rows['class_name']); ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <input type="hidden" name="className" id="clCl">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Year:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="yearDropdown" onclick="toggleDropdown('yearOptions')">
                                                    <span id="yrText">Select</span>
                                                    <span class="dpImg">
                                                        <img id="yrUp" src="media/up.png" alt="Up Arrow">
                                                        <img id="yrDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="yearOptions" id="yearOptions">
                                                </div>
                                                <input type="hidden" name="year" id="yrCl">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForCreate" value="Save Class" onclick="validateCreateForm()">
                            </form>      
                        </div>
                        <div class="contentBody" id="edit">
                            <div id="mngHeading">
                                <h1 style="text-align: center;" id="ceTitle">Edit Class</h1>
                            </div>
                            <form id="editClassForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="edit">
                                <table>
                                    <tr>
                                        <th>Class:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="editClassDropdown" onclick="controlDropdown('editClassOptions')">
                                                    <div id="editCdpText">Select</div>
                                                    <span class="dpImg editImg">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="editClContainer">
                                                    <input type="text" name="" id="editSearch" onkeyup="filterSearch(id, 'editClassOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('editClassOptions')">
                                                    </div>
                                                </div>
                                                <div class="editClassOptions">
                                                    <?php while ($rows = mysqli_fetch_array($allClassesForEdit)) { ?>
                                                        <div class="option" data-grade="<?php echo $rows['grade']?>" data-name="<?php echo $rows['class_name']?>" data-year="<?php echo $rows['year'];?>" onclick="editClass(this)">
                                                            <?php echo $rows['grade']."-".$rows['class_name']."-".$rows['year']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <input type="hidden" name="oldGrade" id="oldGrade" value="">
                                    <input type="hidden" name="oldClName" id="oldClName" value="">
                                    <input type="hidden" name="oldYear" id="oldYear" value="">
                                    <tr>
                                        <th>Updated Grade:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="gradeDropdown" onclick="toggleDropdown('editGradeOptions')">
                                                    <div id="editGdpText">Select</div>
                                                    <span class="dpImg">
                                                        <img id="editGrUp" src="media/up.png" alt="Up Arrow">
                                                        <img id="editGrDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="editGradeOptions">
                                                    <?php while ($rows = mysqli_fetch_array($gradeOutput2)) { ?>
                                                        <div class="option" dataValue="<?php echo trim($rows['grade_level']); ?>" onclick="selectOption(this, 'editGdpText', 'editGrade', 'editGradeOptions')">
                                                            <?php echo trim($rows['grade_level']); ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <input type="hidden" name="editedGrade" id="editGrade">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Updated Class Name:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="classDropdown" onclick="toggleDropdown('editClNameOptions')">
                                                    <span id="editCNdpText">Select</span>
                                                    <span class="dpImg">
                                                        <img id="editClUp" src="media/up.png" alt="Up Arrow">
                                                        <img id="editClDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="editClNameOptions">
                                                    <?php while ($rows = mysqli_fetch_array($classOutput2)) { ?>
                                                        <div class="option" dataValue="<?php echo trim($rows['class_name']); ?>" onclick="selectOption(this, 'editCNdpText', 'editClName', 'editClNameOptions')">
                                                            <?php echo trim($rows['class_name']); ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <input type="hidden" name="editedClName" id="editClName">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Updated Year:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="yearDropdown" onclick="toggleDropdown('editYearOptions')">
                                                    <span id="editYrText">Select</span>
                                                    <span class="dpImg">
                                                        <img id="editYrUp" src="media/up.png" alt="Up Arrow">
                                                        <img id="editYrDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="editYearOptions" id="editYearOptions">
                                                </div>
                                                <input type="hidden" name="editedYear" id="editYear">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForEdit" value="Save Changes" onclick="validateEditForm()">
                            </form>  
                        </div>
                        <div class="contentBody" id="delete">
                            <div id="mngHeading">
                                <h1 style="text-align: center;" id="ceTitle">Delete Class</h1>
                            </div>
                            <br><br>
                            <em>You cannot delete a class that is already assigned to students. If you want to change student's class, go to edit profile section.</em>
                            <form id="deleteClassForm" style="margin-top: 10px;" action="" method="post" onsubmit="preventSubmission(event)">
                                <input type="hidden" name="form" value="delete">    
                                <table>
                                    <tr>
                                        <th>Class:</th>
                                        <td>
                                            <div class="customDropdown">
                                                <button id="deleteClassDropdown" onclick="controlDropdown('deleteClassOptions')">
                                                    <div id="deleteCdpText">Select</div>
                                                    <span class="dpImg editImg">
                                                        <img id="edSrchDown" src="media/down.png" alt="Down Arrow">
                                                    </span>
                                                </button>
                                                <div class="deleteClContainer">
                                                    <input type="text" name="" id="deleteClassSearch" onkeyup="filterSearch(id, 'deleteClassOptions')" placeholder="Search">
                                                    <div class="inpDpImg">
                                                        <img id="edSrchUp" src="media/up.png" alt="Up Arrow" onclick="controlDropdown('deleteClassOptions')">
                                                    </div>
                                                </div>
                                                <div class="deleteClassOptions">
                                                    <?php while ($rows = mysqli_fetch_array($allClassesForDelete)) { ?>
                                                        <div class="option" data-grade="<?php echo $rows['grade']?>" data-name="<?php echo $rows['class_name']?>" data-year="<?php echo $rows['year'];?>" onclick="deleteClass(this)">
                                                            <?php echo $rows['grade']."-".$rows['class_name']."-".$rows['year']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                
                                <input type="hidden" name="deleteGrade" id="deleteGrade" value="">
                                <input type="hidden" name="deleteClName" id="deleteClName" value="">
                                <input type="hidden" name="deleteYear" id="deleteYear" value="">
                                <input style="margin-top: 30px; padding: 10px 15px" type="submit" class="mockSubmit" id="submitForDelete" value="Delete Class" onclick="validateDeleteForm()">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <dialog id="saveEditDataFail">Grade must be an integer and not exceed 30. Try again.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

    <dialog id="saveEditDataFail2">Year must be an integer and is the current year or at most 2 years from the current year. Try again.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

    <dialog id="saveEditDataFail3">Class name must not exceed 20 characters. Try again.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>
    
    <dialog id="repeatData">Data discarded because it already exist.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

    <dialog id="editFail">Fill up all fields before submit.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

    <dialog id="editFail2">Choose a class to edit.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

    <dialog id="deleteFail">Choose a class to delete.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

    <dialog id="editMsg">Class detail(s) is edited and saved.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

    <dialog id="createMsg">Class is created and saved.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

    <dialog id="deleteMsg">Class is permanently deleted.<br><button id="msgExit" onclick="okayExit()">Okay</button></dialog>

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

        let selectCreateYear = document.getElementById("yearOptions");
        let selectEditYear = document.getElementById("editYearOptions");

        year1 = new Date().getFullYear();
        year2 = year1 + 1;
        year3 = year1 + 2;

        selectCreateYear.innerHTML = 
            `<div class="option" dataValue="${year1}" onclick="selectOption(this, 'yrText', 'yrCl', 'yearOptions')">${year1}</div>
            <div class="option" dataValue="${year2}" onclick="selectOption(this, 'yrText', 'yrCl', 'yearOptions')">${year2}</div>
            <div class="option" dataValue="${year3}" onclick="selectOption(this, 'yrText', 'yrCl', 'yearOptions')">${year3}</div>`;

        selectEditYear.innerHTML = 
            `<div class="option" dataValue="${year1}" onclick="selectOption(this, 'editYrText', 'editYear', 'editYearOptions')">${year1}</div>
            <div class="option" dataValue="${year2}" onclick="selectOption(this, 'editYrText', 'editYear', 'editYearOptions')">${year2}</div>
            <div class="option" dataValue="${year3}" onclick="selectOption(this, 'editYrText', 'editYear', 'editYearOptions')">${year3}</div>`;


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
            if (optionList === "editClassOptions") {
                text = document.getElementById("editClassDropdown");
                input = document.getElementsByClassName("editClContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editSearch").value = "";
            }

            if (optionList === "deleteClassOptions") {
                text = document.getElementById("deleteClassDropdown");
                input = document.getElementsByClassName("deleteClContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("deleteClassSearch").value = "";
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

        
        function editClass(element) {
            let newText = element.innerText;
            document.getElementById("editCdpText").textContent = newText;
            controlDropdown("editClassOptions");

            let grade = element.dataset.grade;
            let name = element.dataset.name;
            let year = element.dataset.year;

            let oldGrade = document.getElementById("oldGrade");
            let oldClName = document.getElementById("oldClName");
            let oldYear = document.getElementById("oldYear");

            oldGrade.value = grade;
            oldClName.value = name;
            oldYear.value = year;
        }


        function deleteClass(element) {
            let newText = element.innerText;
            document.getElementById("deleteCdpText").textContent = newText;
            controlDropdown("deleteClassOptions");

            let grade = element.dataset.grade;
            let name = element.dataset.name;
            let year = element.dataset.year;

            let deleteGrade = document.getElementById("deleteGrade");
            let deleteClName = document.getElementById("deleteClName");
            let deleteYear = document.getElementById("deleteYear");

            deleteGrade.value = grade;
            deleteClName.value = name;
            deleteYear.value = year;
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
            let grade = document.getElementById("deleteGrade").value;
            let className = document.getElementById("deleteClName").value;
            let year = document.getElementById("deleteYear").value;
            if (grade && className && year) {
                document.getElementById("deleteClassForm").submit(); 
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("deleteFail").showModal();
            }
        }


        function validateEditForm() {
            let oldGrade = document.getElementById("oldGrade").value;
            let oldClName = document.getElementById("oldClName").value;
            let oldYear = document.getElementById("oldYear").value;
            if (oldGrade && oldClName && oldYear) {
                let newGrade = document.getElementById("editGrade").value;
                let newClName = document.getElementById("editClName").value;
                let newYear = document.getElementById("editYear").value;
                if (newGrade && newClName && newYear) {
                    document.getElementById("editClassForm").submit(); 
                } else {
                    document.querySelector('.blurOverlay').style.visibility = 'visible';
                    document.getElementById("editFail").showModal();
                }
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("editFail2").showModal();
            }
        }
            

        function validateCreateForm() {
            let gr = document.getElementById("grCl").value;
            let cl = document.getElementById("clCl").value;
            let yr = document.getElementById("yrCl").value;
            if (gr && cl && yr) {
                document.getElementById("verifyInput").value = "null";
                document.getElementById("createClassForm").submit(); 
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("editFail").showModal();
            }
        }


        function preventSubmission(event) {
            event.preventDefault();
        }


        document.addEventListener('DOMContentLoaded', function() {
            const editClDp = document.getElementById('editClassDropdown');
            const editClDp1 = document.getElementsByClassName('editClContainer')[0];
            const editClOpt = document.getElementsByClassName('editClassOptions')[0];
            const deleteClDp = document.getElementById('deleteClassDropdown');
            const deleteClDp1 = document.getElementsByClassName('deleteClContainer')[0];
            const deleteClOpt = document.getElementsByClassName('deleteClassOptions')[0];

            // Click outside closes dropdown
            document.addEventListener('click', function(event) {
                if (editClOpt.style.display === "block" && !editClDp.contains(event.target) && !editClDp1.contains(event.target) && !editClOpt.contains(event.target)) {
                    controlDropdown('editClassOptions');
                }

                if (deleteClOpt.style.display === "block" && !deleteClDp.contains(event.target) && !deleteClDp1.contains(event.target) && !deleteClOpt.contains(event.target)) {
                    controlDropdown('deleteClassOptions');
                }
            });
        });

        //styling section
        function inpDesign(id) {
            let inputID = document.getElementById(id);
            inputID.style.border = "2px solid rgb(52, 216, 57)";
            inputID.style.boxShadow = "none";
            inputID.style.outline = "none";
            inputID.style.backgroundColor = "rgb(224, 247, 250)";
        }

        function inpRevert(id) {
            let inputID = document.getElementById(id);
            inputID.style.border = "2px solid grey";
            inputID.style.backgroundColor = "white";  
        }

        function toggleDropdown(optionList) {
            let options, up, down;
            options = document.querySelector("."+optionList);

            if (optionList === "gradeOptions") {
                up = document.getElementById("grUp");
                down = document.getElementById("grDown");
            } 
            else if (optionList === "classOptions") {
                up = document.getElementById("clUp");
                down = document.getElementById("clDown");
            }
            else if (optionList === "yearOptions") {
                up = document.getElementById("yrUp");
                down = document.getElementById("yrDown");
            }
            else if (optionList === "editGradeOptions") {
                up = document.getElementById("editGrUp");
                down = document.getElementById("editGrDown");
            }
            else if (optionList === "editClNameOptions") {
                up = document.getElementById("editClUp");
                down = document.getElementById("editClDown");
            }
            else if (optionList === "editYearOptions") {
                up = document.getElementById("editYrUp");
                down = document.getElementById("editYrDown");
            }
            
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

            toggleDropdown(optionList); // close dropdown after selection
        }
    </script>
    </body  >
</html>
