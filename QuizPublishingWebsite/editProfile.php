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

$updateStatus = 0;
$repeatStatus = 0;
$fileSizeError = 0;
$fileUploadError = 0;
$fileTypeError = 0;
$fileRemoveError = 0;
$userId = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "conn.php";
    $origin = $_POST["origin"];

    if ($origin == "mainEdit") {
        $userId = $_POST["userId"];
        $username = $_POST["user"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $dob = $_POST["dob"];
        $file = $_FILES['profilePic'];  
        
        $sqlUsers = "SELECT * FROM user;";
        $usersOutput = mysqli_query($conn, $sqlUsers);
        while ($row = mysqli_fetch_array($usersOutput)) {
            if ($row['user_id'] != $userId) {
                $existingUsername = $row['username'];
                $existingEmail = $row['email'];
                //ensure edited username and email does not conflict with current ones in database (must be unique)
                if ($existingUsername === $username || $existingEmail === $email) {
                    $repeatStatus = 1;  //indicate repetition
                }
            }
        }

        if ($repeatStatus !== 1) {
            //hashed password for security
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $fileLocation = "0";
            if ($_FILES['profilePic']['name'] == "") {
                //update with hashed password
                $sql = "UPDATE user SET username = '$username', password = '$hashedPassword', email = '$email', dob = '$dob', photo = null WHERE user_id = '$userId';";
                mysqli_query($conn, $sql);  //execute update query

                if (mysqli_affected_rows($conn) <= 0) {
                    $updateStatus = -1;
                } 
                else {
                    $updateStatus = 1;
                }
            } else  {
                $fileName = $_FILES['profilePic']['name'];
                $fileTmpName = $_FILES['profilePic']['tmp_name'];
                $fileSize = $_FILES['profilePic']['size'];
                $fileError = $_FILES['profilePic']['error'];
                $fileType = $_FILES['profilePic']['type'];
        
                //identify file extension/type
                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));
        
                //image file types
                $allowedFileType = array('jpg', 'jpeg', 'png');
        
                if (in_array($fileActualExt, $allowedFileType)) {
                    if ($fileError === 0) {
                        if ($fileSize < 1000000) {
                            $fileBaseName = "profile".$userId;
        
                            //delete all image files with the same base name
                            foreach ($allowedFileType as $ext) {
                                $existingFile = "uploads/".$fileBaseName.".".$ext;
                                if (file_exists($existingFile)) {
                                    if (!unlink($existingFile)) {
                                        echo "<script>console.log('Failed to delete $existingFile.');</script>";
                                    } else {
                                        echo "<script>console.log('$existingFile deleted successfully.');</script>";
                                    }
                                }
                            }
        
                            $fileNameNew = $fileBaseName.".".$fileActualExt;
                            $fileLocation = "uploads/".$fileNameNew;
                            //save file in the location set
                            //later saves the path in the database
                            move_uploaded_file($fileTmpName, $fileLocation);
                            //update with hashed password
                            $sql = "UPDATE user SET username = '$username', password = '$hashedPassword', email = '$email', dob = '$dob', photo = '$fileLocation' WHERE user_id = '$userId';";
                            mysqli_query($conn, $sql);  //execute update query

                            if (mysqli_affected_rows($conn) <= 0) {
                                $updateStatus = -1;
                            } 
                            else {
                                $updateStatus = 1;
                            }
                            
                        } else {
                            $fileSizeError = 1;
                        }
                    } else {
                        $fileUploadError = 1;
                    }
                } else {
                    $fileTypeError = 1;
                }
            }
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $userId = $_GET["userId"];
}

if ($userId == "") {
    die("No user is chosen to be edited!");
} else {
    $sqlTest = "SELECT * FROM user where user_id = $userId;";
    $testOutput = mysqli_query($conn, $sqlTest);
    $testRow = mysqli_fetch_array($testOutput);
    $oldUsername = $testRow['username'];
    $oldPassword = $testRow['password'];
    $oldEmail = $testRow['email'];
    $oldDob = $testRow['dob'];
    $profile = $testRow['photo'];
    if ($profile == null) {
        $profile = "media/profileDefault.png";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <script src="https://cdn.jsdelivr.net/npm/validator@13.6.0/validator.min.js"></script>
    <script src="index.js"></script>
    <link rel="stylesheet" href="createProfile.css">
    <style>

    </style>
</head>
<body>
    <div class="blurOverlay"></div>
    <div id="headerContainer">
        <?php include "adminHeader.php"?>
    </div>
    <div class="body" id="bodyContainer">
        <form action="" method="post" enctype="multipart/form-data" onsubmit="preventDefaultSubmit(event)">
            <table id="createProfile">
                <input type="hidden" name="origin" value="mainEdit">
                <tr>
                    <td colspan="3">
                        <div id="cpHeading">
                            <button id="back" onclick="window.history.back();">back</button>
                            <h1 style="text-align: center;" id="ceTitle">Edit Profile</h1>
                            <input type="hidden" name="userId" id="userId" value="<?php echo $testRow['user_id'];?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="display: flex; justify-content: center; align-items: center; margin: 20px 0px;">
                        <div class="profileContainer">
                            <img id="profile" src=<?php echo $profile?> alt="Profile Picture">
                            <img id="editPen" src="media/edit_pen.png" alt="Edit Pen" onclick="document.getElementById('profilePic').click();">
                        </div>
                            <button id="unsetButton" onclick="unsetPhoto();">Unset Photo</button>
                    </td>
                    <input type="file" name="profilePic" id="profilePic" accept="image/*" style="display: none;" onchange="previewImage()">
                </tr>
                <tr>    
                    <th colspan="3"><label for="us">Username</label></th>
                </tr>
                <tr>
                    <td colspan="3" id="username"><input type="text" name="user" id="us" onfocus="inpDesign(id); usVad()" oninput="inpDesign(id); usVad()" onfocusout="inpRevert(id)" value="<?php echo $oldUsername;?>"></td>
                </tr>
                <tr>
                    <td colspan="3"><p id="msgUS"></p></td>
                </tr>
                <tr>
                    <th colspan="3"><label for="pw">New Password</label></th>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="pwContainer">
                            <input type="password" name="password" id="pw" onfocus="inpDesign(id); pwVad();" oninput="inpDesign(id); pwVad();" onfocusout="inpRevert(id)">
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
                            <li id="pw3">More than 8 characters</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th colspan="3"><label for="em">Email</label></th>
                </tr>
                <tr>
                    <td colspan="3"><input type="text" name="email" id="em" onfocus="inpDesign(id); emVad()" oninput="inpDesign(id); emVad()" onfocusout="inpRevert(id)" value="<?php echo $oldEmail;?>"></td>
                </tr>
                <tr>
                    <td colspan="3"><p id="msgEM"></p></td>
                </tr>
                <tr>
                    <th>Role:&emsp;<role style="font-weight: normal;"><?php echo ucfirst($testRow['role']);?></role></th>
                    <th></th>
                    <th><div><label for="dob">Date of Birth</label></div></th>
                </tr>
                <tr>
                    <th></th>
                    <td></td>
                    <td>
                        <div style="text-align: end;">
                            <input type="date" name="dob" id="dob" value="<?php echo $oldDob;?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="height: 30px;"></td>
                </tr>
                <tr>
                    <td><p></p></td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 40px; display: flex; justify-content: center; align-items: center;"><input type="submit" id="submit" value="Update" onclick="vadCPForm()"></td>
                </tr>
            </table> 
        </form>
    </div>


    <dialog id="fileSizeErrorMsg">The profile chosen cannot be uploaded because it is too large in size.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="fileUploadErrorMsg">Your profile chosen cannot be uploaded at this time. Try using another file or try again later.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="fileTypeErrorMsg">Profile chosen must be an image (.jpg, .jpeg, .png).<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="fileRemoveErrorMsg">Your profile photo cannot be removed for now.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>
    
    <dialog id="repeatData">Changes cannot be saved because entered data conflicts with existing data.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="unchangedData">Profile is not updated because no changes are made.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="updateFail">Fill up all fields and in correct formats before submitting profile.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="updateFail2">Changes failed to be saved.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="updateMsg">Profile updated and saved successfully.<br><button id="msgExit" onclick="okayExit(1)">OKAY</button></dialog>
    
    <script>
        let updateStatus = <?php echo $updateStatus?>;
        let repeatStatus = <?php echo $repeatStatus?>;
        let fileSizeError = <?php echo $fileSizeError?>;
        let fileUploadError = <?php echo $fileUploadError?>;
        let fileTypeError = <?php echo $fileTypeError?>;
        let fileRemoveError = <?php echo $fileRemoveError?>;

        if (fileSizeError === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById('fileSizeErrorMsg').showModal();
        }

        if (fileUploadError === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById('fileUploadErrorMsg').showModal();
        }

        if (fileTypeError === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById('fileTypeErrorMsg').showModal();
        }

        if (fileRemoveError === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById('fileRemoveErrorMsg').showModal();
        }

        if (updateStatus === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById("updateMsg").showModal();
        }

        if (updateStatus === -1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById("updateFail2").showModal();
        }

        if (repeatStatus === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById("repeatData").showModal();
        }


        function okayExit(exit) {
            if (exit === 0) {
                document.querySelectorAll('dialog').forEach(dialog => dialog.close());
                document.querySelector(".blurOverlay").style.visibility = "hidden";
            } else {
                document.querySelectorAll('dialog').forEach(dialog => dialog.close());
                document.querySelector(".blurOverlay").style.visibility = "hidden";
                window.location.href = "manageUser.php"; // Small delay to ensure dialogs close first+
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


        function previewImage() {
            //preview image chosen in dialog
            let file = document.getElementById('profilePic').files[0];
            let reader = new FileReader();

            reader.onload = function(e) {
                //update the image src to the selected file
                document.getElementById('profile').src = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file); //convert the image file to a data URL
            }
        }

        function unsetPhoto() {
            document.getElementById('profilePic').value = "";
            //default image if users unset
            document.getElementById('profile').src = "media/profileDefault.png";
        }

        let submitSignal;
        
        let oldUsername = document.getElementById("us").value;
        let oldPassword = document.getElementById("pw").value;
        let oldEmail = document.getElementById("em").value;
        let oldDob = document.getElementById("dob").value;
        let oldPfp = document.getElementById("profile").src;

        function vadCPForm() {
            let username = document.getElementById("us").value;
            let password = document.getElementById("pw").value;
            let email = document.getElementById("em").value;
            let dob = document.getElementById("dob").value;
            let pfp = document.getElementById("profile").value;
            if (usVad() & pwVad() & emVad() && dob) {
                if (username === oldUsername && password === oldPassword && email === oldEmail && dob === oldDob && pfp === oldPfp) {
                    submitSignal = false;
                    document.querySelector('.blurOverlay').style.visibility = 'visible';
                    document.getElementById("unchangedData").showModal();
                }
                else {
                    submitSignal = true;
                }
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById("updateFail").showModal();
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