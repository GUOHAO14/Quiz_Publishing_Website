<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conn.php';

// Check for quiz_id to populate dropdown


// Handle request for JSON response based on course_id
if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);

    // Query to fetch folders based on course_id
    $query = $conn->prepare("SELECT folder_id, folder_name FROM folder WHERE course_id = ?");
    $query->bind_param("i", $course_id);
    $query->execute();
    $result = $query->get_result();

    $folders = [];
    while ($row = $result->fetch_assoc()) {
        $folders[] = $row;
    }

    header('Content-Type: application/json');
    if (empty($folders)) {
        echo json_encode(["message" => "No folders found for the given course_id"]);
    } else {
        echo json_encode($folders);
    }
    exit;
}

?>
