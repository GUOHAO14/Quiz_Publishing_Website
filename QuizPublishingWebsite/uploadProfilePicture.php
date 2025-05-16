<?php
if (isset($_POST['submit'])) {
    $file = $_FILES['profilePic'];

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

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 1000000) {
                $fileNameNew = uniqid('', true).".".$fileActualExt;
                $fileLocation = "uploads/".$fileNameNew;  
                move_uploaded_file($fileTmpName, $fileLocation);
                header("Location: ");   
            } else {
                //file too big
            }
        } else {
            //uploading error
        }
    } else {
        //invalid file type
    }
}
?>