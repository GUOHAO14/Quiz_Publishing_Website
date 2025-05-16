<?php
session_start();
// session_destroy();
if (!isset($_SESSION['admin_id'])) {
    session_start();
    header("Location: index.php");
    session_write_close();
    exit();
}
include "conn.php";

$createStatus = 0;
$repeatSign = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "conn.php";
    $username = $_POST["user"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $dob = $_POST["dob"];

    $sqlUserId = "SELECT user_id FROM user WHERE username = '$username' OR email = '$email';";
    $userIdOutput = mysqli_query($conn, $sqlUserId);
    // $row = mysqli_fetch_array($userIdOutput);
    // $userId = $row['user_id'];

    if (mysqli_affected_rows($conn) <= 0) {
        //hashed password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        //insert hashed password
        $sql = "INSERT INTO user (username, password, email, role, dob) VALUES ('$username', '$hashedPassword', '$email', '$role', '$dob');";

        mysqli_query($conn, $sql);  //execute query

        if (mysqli_affected_rows($conn) <= 0) {
            echo "<script>alert('Unable to insert data.');</script>";
        } else {

            $sqlUserId = "SELECT user_id FROM user WHERE username = '$username';";
            $userIdOutput = mysqli_query($conn, $sqlUserId);
            $row = mysqli_fetch_array($userIdOutput);
            $userId = $row['user_id'];

            if ($role === "student") {
                $sql = "INSERT INTO student (user_id) VALUES ('$userId');";
                mysqli_query($conn, $sql);

                if (mysqli_affected_rows($conn) <= 0)
                    echo "<script>console.log('Unable to create student.');</script>";
                else {
                    echo "<script>console.log('Student created successfully.');</script>";
                    $sqlStudentId = "SELECT student_id FROM student WHERE user_id = '$userId';";
                    $studentIdOutput = mysqli_query($conn, $sqlStudentId);
                    $row = mysqli_fetch_array($studentIdOutput);
                    $studentId = $row['student_id'];
                    $class = $_POST["studentClass"];
                    
                    if ($class !== "") {
                        $sqlStudent = "INSERT INTO enrolment (student_id, class_id) VALUES ('$studentId', '$class');";
                        mysqli_query($conn, $sqlStudent);

                        if (mysqli_affected_rows($conn) <= 0)
                            echo "<script>console.log('Unable to create student.');</script>";
                        else {
                            $createStatus = 1;
                        }
                    } else {
                        $createStatus = 1;
                    }
                }
            }

            else if ($role === "teacher") {
                $sql = "INSERT INTO teacher (user_id) VALUES ('$userId');";
                mysqli_query($conn, $sql);

                if (mysqli_affected_rows($conn) <= 0) {
                    echo "<script>alert('Unable to create admin.');</script>";
                } else {
                    $createStatus = 1;
                }
            }

            else if ($role === "admin") {
                $sql = "INSERT INTO admin (user_id) VALUES ('$userId');";
                mysqli_query($conn, $sql);

                if (mysqli_affected_rows($conn) <= 0) {
                    echo "<script>alert('Unable to create admin.');</script>";
                } else {
                    $createStatus = 1;
                }
            }
        }
    } else {
        $repeatSign = 1;
    }
    
}

$sqlClass = "SELECT * FROM class ORDER BY year DESC, grade ASC, class_name ASC";
$allClasses = mysqli_query($conn, $sqlClass);


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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User Profile</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <script src="index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/validator@13.6.0/validator.min.js"></script>
    <link rel="stylesheet" href="createProfile.css">
</head>
<body>
    <div id="headerContainer">
        <?php include "adminHeader.php"?>
    </div>
    <div class="body" id="bodyContainer">
        <form action="" method="post" onsubmit="preventDefaultSubmit(event)">
            <table id="createProfile">
                <tr>
                    <td colspan="3">
                        <div id="cpHeading">
                            <button id="back" onclick="window.history.back()">back</button>
                            <h1 style="text-align: center;" id="ceTitle">Create New Profile</h1>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="vertical-align: middle;">
                        <div class="picContainer" style="width: 80px; height: 80px; border: 2px solid black">
                            <img id="profilePic" src="media/profileDefault.png" alt="Profile">
                        </div>
                    </td>
                    <td>
                        <div class="picInfoContainer">
                            <img style="cursor: pointer;" id="infoPic" src="media/info.png" alt="Profile" onclick="toggleInfoBox()">
                            <div id="picInfoBox">
                                Profile picture can be uploaded by users. This is the default if users do not upload anything.
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>    
                    <th colspan="3"><label for="us">Username</label></th>
                </tr>
                <tr>
                    <td colspan="3" id="username"><input type="text" name="user" id="us" onfocus="inpDesign(id)" oninput="inpDesign(id); usVad()" onfocusout="inpRevert(id)" placeholder="e.g.: Sulaiman M."></td>
                </tr>
                <tr>
                    <td colspan="3"><p id="msgUS"></p></td>
                </tr>
                <tr>
                    <th colspan="3"><label for="pw">Password</label></th>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="pwContainer">
                            <input type="password" name="password" id="pw" onfocus="inpDesign(id)" oninput="inpDesign(id); pwVad()" onfocusout="inpRevert(id)">
                            <img id="pwVisible" src="media/visibilityOff.png" alt="VisibilityOff" onclick="hidePW()"><img id="pwNotVisible" src="media/visibilityOn.png" alt="VisibilityOn" onclick="hidePW()">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="reqPW" colspan="3">
                        <p id="msgPW">Password requirement:<br></p>
                        <ul id="ulPW" style="padding-left: 0.9rem;">
                            <li id="pw1">Combination of English alphabets, numbers, and symbols</li>
                            <li id="pw2">Use both uppercase and lowercase alphabets</li>
                            <li id="pw3">8 or more characters</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th colspan="3"><label for="em">Email</label></th>
                </tr>
                <tr>
                    <td colspan="3"><input type="text" name="email" id="em" onfocus="inpDesign(id)" oninput="inpDesign(id); emVad()" onfocusout="inpRevert(id)" placeholder="e.g.: sulaiman@gmail.com"></td>
                </tr>
                <tr>
                    <td colspan="3"><p id="msgEM"></p></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <th style="width: 10px"></th>
                    <th><div><label for="dob">Date of Birth</label></div></th>
                </tr>
                <tr>
                    <td>
                        <div class="customDropdown">
                            <button class="test14" id="roleDropdown" onclick="toggleDropdown('roleOptions');">
                                <div id="rdpText">Select</div>
                                <span class="dpImg">
                                    <img id="rlUp" src="media/up.png" alt="Up Arrow">
                                    <img id="rlDown" src="media/down.png" alt="Down Arrow">
                                </span>
                            </button>
                            <div class="roleOptions">
                                <div class="option" dataValue="student" onclick="selectOption(this, 'rdpText', 'rl', 'roleOptions')">Student</div>
                                <div class="option" dataValue="teacher" onclick="selectOption(this, 'rdpText', 'rl', 'roleOptions')">Teacher</div>
                                <div class="option" dataValue="admin" onclick="selectOption(this, 'rdpText', 'rl', 'roleOptions')">Admin</div>
                            </div>
                            <input type="hidden" name="role" id="rl">
                        </div>
                    </td>
                    <td style="width: 10px"></td>
                    <td>
                        <div style="width: 100%;">
                            <input type="date" name="dob" id="dob">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="height: 30px;"></td>
                </tr>
                <tbody class="roleSelections" id="studentSelections">
                    <tr>
                        <th colspan="2" style="padding-bottom: 1.5rem;"><h2>Student Details</h2></th>
                    </tr>
                    <tr>
                        <th>Class:</th>
                        <td>
                            <div style="width: 220px" class="customDropdown" id="chooseClassDropdown">
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
                                    <div class="option" onclick="chooseClass(this)">Clear selection</div>
                                    <?php while ($rows = mysqli_fetch_array($allClasses)) { ?>
                                        <div class="option" data-classid="<?php echo $rows['class_id']?>" onclick="chooseClass(this)">
                                            <?php echo $rows['grade']."-".$rows['class_name']."-".$rows['year']; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <input type="hidden" name="studentClass" id="studentClass" value="">
                        </td>
                    </tr>
                </tbody>
                <tr>
                    <td><p></p></td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 40px; display: flex; justify-content: center; align-items: center;"><input type="submit" id="submit" value="Create Profile" onclick="vadCPForm()"></td>
                </tr>
            </table> 
        </form>
    </div>
    
    <dialog id="repeatData">Profile cannot be saved because it already exist.<br><button id="msgExit" onclick="okayExit(1)">OKAY</button></dialog>

    <dialog id="createFail">Fill up all fields and in correct formats before submitting profile.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="createMsg">Profile created and saved successfully.<br><button id="msgExit" onclick="okayExit(1)">OKAY</button></dialog>

    <script>
        let createStatus = <?php echo $createStatus?>;
        let repeatSign = <?php echo $repeatSign?>;

        if (createStatus === 1) {
            document.getElementById("bodyContainer").style.filter = "blur(5px)";
            document.getElementById("createMsg").showModal();
        }

        if (repeatSign === 1) {
            document.getElementById("bodyContainer").style.filter = "blur(5px)";
            document.getElementById("repeatData").showModal();
        }

        function okayExit(sign) {
            document.getElementById("repeatData").close(); 
            document.getElementById("createFail").close(); 
            document.getElementById("createMsg").close();
            document.getElementById("bodyContainer").style.filter = "blur(0px)";
            if (sign == 1) {
                window.location.href = "manageUser.php";
            }
        }


        function pwVad() {
            let pw = document.getElementById("pw").value, pwCon1 = pwCheckCharAndCase(pw), pwCon2 = pwCheckLen(pw);
            if (pwCon1 && pwCon2) {
                return true;
            } else {
                return false;
            }
        }
            

        function toggleInfoBox() {
            let infoBox = document.getElementById("picInfoBox");
            if (infoBox.style.visibility == "hidden") {
                infoBox.style.visibility = "visible";
            } else {
                infoBox.style.visibility = "hidden";
            }
        }


        function turnRed(id) {
            document.getElementById(id).style.color = "red";
        }


        function turnGreen(id) {
            document.getElementById(id).style.color = "green";
        }


        //ensure password has uppercase and lowercase letters, numbers and symbols
        function pwCheckCharAndCase(pw) {
            //check ascii 33 - 126 and check alphabet case
            //charSign used to indicate that all three types of characters are used
            //caseSign used to indicate that uppercase and lowercase alphabets are used
            //alp = alphabets, sym = symbol, num = number, up = uppercase alp, low = lowercase alp
            let charSign = 0, caseSign = 0, sym = 0, alp = 0, num = 0, up = 0, low = 0;
            for (let char of pw) {
                let code = char.charCodeAt(0);  //using ascii code to check
                if (!(code >= 33 && code <= 126)) {
                    let sym = 0, alp = 0, num = 0;
                    break;
                } 
                else {

                    if ((code >= 33 && code <= 47) || (code >= 58 && code <= 64) || (code >= 91 && code <= 96) || (code >= 123 && code <= 126))
                    {
                        sym++;
                    }
                    else if ((code >= 48 && code <= 57)) {
                        num++;
                    }
                    else if ((code >= 65 && code <= 90) || (code >= 97 && code <= 122)) {
                        alp++;
                        if (code >= 65 && code <= 90) {
                            up++;
                        } else {
                            low++;
                        }
                    } 
                }
            } 

            if (sym > 0 && alp > 0 && num > 0) {
                charSign++;
                turnGreen("pw1");
            } else {
                turnRed("pw1");
            }

            if (up > 0 && low > 0) {
                caseSign++;
                turnGreen("pw2");
            } else {
                turnRed("pw2");
            }

            if (charSign == 1 && caseSign == 1) {
                return true;
            } else {
                return false;
            }
        }
            

        //validate password length
        function pwCheckLen(pw) {
            //check length
            let len = pw.length;
            if (len < 8) {
                turnRed("pw3");
                return false;
            } else {
                turnGreen("pw3");
                return true;
            }
        }


        //username validation
        function usVad() {
            let us = document.getElementById("us").value;
            let len = us.length;
            let id = document.getElementById("msgUS");
            if (len < 4) {
                id.innerHTML = "Username must be 4 characters or longer.";
                return false;
            } else {
                id.innerHTML = "";
                return true;
            }
        }


        //email validation
        function emVad() {
            let email = document.getElementById("em").value;
            let id = document.getElementById("msgEM");
            let vad = 0;
            if (validator.isEmail(email)) {
                id.innerHTML = "";
                return true;
            } else {
                id.innerHTML = "Invalid email.";
                return false;
            }
        }


        function toggleDropdown(optionList) {
            let options, up, down;
            options = document.querySelector("."+optionList);

            if (optionList === "roleOptions") {
                up = document.getElementById("rlUp");
                down = document.getElementById("rlDown");
            }
            else if (optionList === "gradeOptions") {
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
            else if (optionList === "deleteTypeOptions") {
                up = document.getElementById("deleteUp");
                down = document.getElementById("deleteTypeDown");
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


        function controlDropdown(optionList) {
            let text, input, options;
            if (optionList === "editClassOptions") {
                text = document.getElementById("editClassDropdown");
                input = document.getElementsByClassName("editClContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("editSearch").value = "";
            }

            if (optionList === "courseOptions") {
                text = document.getElementById("courseDropdown");
                input = document.getElementsByClassName("courseContainer")[0];
                options = document.querySelector("."+optionList);
                document.getElementById("courseSearch").value = "";
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


        function chooseClass(element) {
            let newText = element.innerText;
            let studentClass = document.getElementById("studentClass");

            if (newText == "Clear selection") {
                document.getElementById("editCdpText").textContent = "";
                studentClass.value = "";
            } else {
                document.getElementById("editCdpText").textContent = newText;
                let classId = element.dataset.classid;
                studentClass.value = classId;
            }
            controlDropdown("editClassOptions");

            
        }


        function chooseCourse(element) {
            let newText = element.innerText;
            let course = document.getElementById("teacherCourse");
            if (newText == "Clear selection") {
                document.getElementById("courseDpText").textContent = "";
                course.value = "";
            } else {
                document.getElementById("courseDpText").textContent = newText;
                let courseId = element.dataset.courseid;
                course.value = courseId;
            }
            controlDropdown("courseOptions");

            
        }

        function selectOption(element, dpText, dataID, optionList) {
            let text = element.innerText;
            let dataValue = element.getAttribute("dataValue");

            document.getElementById(dpText).textContent = text; // Update button text
            document.getElementById(dataID).value = dataValue; // Set the hidden input's value

            toggleDropdown(optionList); // close the dropdown after selection

            if (dataID === "rl") {
                roleSpecificMenu(dataValue);
            }
        }


        function roleSpecificMenu(role) {
            let studentMenu = document.getElementById("studentSelections");
            if (role === "student") {
                studentMenu.style.display = "block";
            }
            else {
                studentMenu.style.display = "none";
            }
        }

        let submitSignal;

        function vadCPForm() {
            let role = document.getElementById("rl").value;
            let dob = document.getElementById("dob").value;
            if (usVad() & pwVad() & emVad() && role && dob) {
                if (role === "student") {
                    let studentClass = document.getElementById("studentClass").value;
                    submitSignal = true;
                }
                else {
                    submitSignal = true;
                }
            } else {
                submitSignal = false;
                document.getElementById("bodyContainer").style.filter = "blur(5px)";
                document.getElementById("createFail").showModal();
            }
        }


        function preventDefaultSubmit(event) {
            if (submitSignal == true)
                event.target.submit();
            else 
                event.preventDefault();
        }
            

    </script>
</body>
</html>