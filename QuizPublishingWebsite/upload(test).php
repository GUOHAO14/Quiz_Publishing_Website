<?php
    $targetDir = "uploads/";
    $fileName = basename($_FILES["profilePicture"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
    // Check if image file is a valid image
    $check = getimagesize($_FILES["profilePicture"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
    
    // Allow only certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    
    // Save file if it's valid
    if ($uploadOk && move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetFilePath)) {
        // Save file path to SQL
        $sql = "UPDATE users SET profile_picture = '$targetFilePath' WHERE user_id = :user_id";
        // Run this SQL query in your SQL database
        echo "The file ". htmlspecialchars(basename($_FILES["profilePicture"]["name"])). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
?>