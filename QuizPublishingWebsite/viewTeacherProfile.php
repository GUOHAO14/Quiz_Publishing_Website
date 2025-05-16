<?php
session_start();
$_SESSION['quizRedirect'] = "no";
// session_destroy();

if (!isset($_SESSION['teacher_id'])) {
    session_start();
    header("Location: index.php");
    session_write_close();
    exit();
}
include "conn.php";

$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE user_id = '$userId';";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$email = $row['email'];
$profilePic = $row['photo'];
if ($profilePic == null) {
    $profilePic = "media/profileDefault.png";
}
$fileSizeError = 0;
$fileUploadError = 0;
$fileTypeError = 0;
$fileUploadSuccess = 0;
$fileRemoveSuccess = 0;
$fileRemoveError = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "conn.php";

    $userId = $_SESSION['user_id'];
    $file = $_FILES['profilePic'];

    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_NO_FILE) {
        $sql = "UPDATE user SET photo = null WHERE user_id = '$userId';";
        $result = mysqli_query($conn, $sql);
        if (mysqli_affected_rows($conn) <= 0) {
            echo "<script>console.log('Unable to insert profile photo.');</script>";
            $fileRemoveError = 1;
        } 
        else {
            $fileRemoveSuccess = 1;
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
                    
                    if (move_uploaded_file($fileTmpName, $fileLocation)) {
                        $sql = "UPDATE user SET photo = '$fileLocation' WHERE user_id = '$userId';";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_affected_rows($conn) <= 0) {
                            echo "<script>console.log('Unable to insert profile photo.');</script>";
                            $fileUploadError = 1;
                        } 
                        else {
                            $fileUploadSuccess = 1;
                        }
                    } else {
                        echo "<script>console.log('Failed to move uploaded file.');</script>";
                        $fileUploadError = 1;
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Information</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="viewAdminProfile.css">
    <style>
        body {
            background-color: rgba(184, 215, 247, 0.67);
        }
        * {
            box-sizing: border-box;
        }

        #headerContainer {
            position: absolute;
            width: 100%;
            height: 100vh;
            left: 0;  /* Positioning the left compartment on the left side */
            top: 0;
            bottom: 0;
        }

        #bodyContainer {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: calc(11vh + 3rem) auto 3rem auto;
            width: 100%;
            padding-left: 0;
        }
        
        #header h1 {
            left: 28%;
        }

        #logoutImg, #back {
            box-sizing: border-box;
            background-color: white;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            border: 3px solid black;
            box-shadow: -3px -3px 10px 4px rgba(0, 0, 0, 0.07), 3px 3px 10px 4px rgba(0, 0, 0, 0.07);
        }

        #logoutImg:hover, #back:hover {
            background-color: rgb(199, 199, 199);
            cursor: pointer;
        }

        #logoutImg:active, #back:active {
            width: 44px;
            height: 44px;
            margin: 3px;
        }
        
        #email {
            display: flex;
            align-items: center;
            flex-direction: column;
        }

        #emailContent {
            margin-top: 23.5px;
        }

        @media screen and (max-width: 1010px) {
            #logoutImg, #back {
                width: 46px;
                height: 46px;
            }

            #logoutImg:active, #back:active {
                width: 40px;
                height: 40px;
                margin: 3px;
            }
        }
    </style>
</head>
<body>
    <div class="blurOverlay"></div>
    <div id="headerContainer">
        <?php include "header.php";?>
    </div>
    <div id="bodyContainer">
        <div class="body">
            <div id="header">
                <img id="back" onclick="window.history.back();" src="media/back.png" alt="Back">
                <h1>Profile</h1>
                <a href="logout.php" id="logout"><img id="logoutImg" src="media/logout.png" alt="Logout"></a>
            </div>
            <div class="row1">
                <div id="user">
                    <div class="profileContainer">
                        <img id="profile" src=<?php echo $profilePic?> alt="Profile Picture">
                        <img id="editPen" src="media/edit_pen.png" alt="Edit Pen" onclick="document.querySelector('.blurOverlay').style.visibility = 'visible';
                    document.getElementById('uploadImgPanel').showModal();">
                    </div>
                    <div class="identity">
                        <p id="username"><?php echo $_SESSION['username'];?></p>
                        <p id="userId"><?php echo "#ST".$_SESSION['user_id'];?></p>
                    </div>
                </div>
                <div id="email">
                    <div>Email</div>
                    <div id="emailContent">
                        <?php echo $email?>
                    </div>
                </div>
                    
            </div>
        </div>
    </div>
    
    <dialog id="uploadImgPanel">
        <form id="profilePicForm" action="" method="POST" enctype="multipart/form-data" onsubmit="preventSubmission(event)">
            <h3>Edit Profile Picture</h3>
            <div class="profileContainer dialogProfileContainer">
                <img id="editingProfile" src=<?php echo $profilePic?> alt="Profile Picture">
            </div>
            <input type="file" name="profilePic" id="profilePic" accept="image/*" style="display: none;" onchange="previewImage()">
            <button onclick="document.getElementById('profilePic').click();">Browse Files</button>
            <button onclick="unsetPhoto();">Unset Photo</button>
            <br>
            <div id="dialogAction">
                <button type="submit" id="upload" onclick="document.getElementById('profilePicForm').submit();">Upload</button><button id="msgExit" onclick="okayExit(1)">Cancel</button>
            </div>
        </form>
        
    </dialog>

    <dialog id="fileSizeErrorMsg">The file chosen cannot be uploaded because it is too large in size.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="fileUploadErrorMsg">Your file cannot be uploaded at this time. Try using another file or try again later.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="fileTypeErrorMsg">File uploaded must be an image (.jpg, .jpeg, .png).<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <dialog id="fileUploadSuccessMsg">Your image is successfully uploaded as your profile photo.<br><button id="msgExit" onclick="okayExit(2)">OKAY</button></dialog>

    <dialog id="fileRemoveSuccessMsg">Your profile photo is unset.<br><button id="msgExit" onclick="okayExit(2)">OKAY</button></dialog>

    <dialog id="fileRemoveErrorMsg">Your profile photo cannot be removed for now.<br><button id="msgExit" onclick="okayExit(0)">OKAY</button></dialog>

    <script>
        //signals for opening dialog
        let fileSizeError = <?php echo $fileSizeError?>;
        let fileUploadError = <?php echo $fileUploadError?>;
        let fileTypeError = <?php echo $fileTypeError?>;
        let fileUploadSuccess = <?php echo $fileUploadSuccess?>;
        let fileRemoveSuccess = <?php echo $fileRemoveSuccess?>;
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

        if (fileUploadSuccess === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById('fileUploadSuccessMsg').showModal();
        }

        if (fileRemoveSuccess === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById('fileRemoveSuccessMsg').showModal();
        }

        if (fileRemoveError === 1) {
            document.querySelector('.blurOverlay').style.visibility = 'visible';
            document.getElementById('fileRemoveErrorMsg').showModal();
        }


        function okayExit(exitSign) {
            //exit dialog
            //helps decide next location after dialog exit
            if (exitSign === 1) {
                document.querySelectorAll('dialog').forEach(dialog => dialog.close());
                document.querySelector(".blurOverlay").style.visibility = "hidden";
            } else if (exitSign === 2) {
                document.querySelectorAll('dialog').forEach(dialog => dialog.close());
                document.querySelector(".blurOverlay").style.visibility = "hidden";
                window.location.href = "teachhome.php"; //exit
            } else {
                document.querySelector('.blurOverlay').style.visibility = 'visible';
                document.getElementById('uploadImgPanel').showModal();
            }
        }


        function previewImage() {
            //preview image chosen in dialog
            let file = document.getElementById('profilePic').files[0];
            let reader = new FileReader();

            reader.onload = function(e) {
                //update the image src to the selected file
                document.getElementById('editingProfile').src = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file); //convert the image file to a data URL
            }
        }


        function unsetPhoto() {
            document.getElementById('profilePic').value = "";
            //default image if users unset
            document.getElementById('editingProfile').src = "media/profileDefault.png";
        }


        function preventSubmission(event) {
            event.preventDefault();
        }
    </script>
</body>
</html>