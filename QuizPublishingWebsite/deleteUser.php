<?php
header('Content-Type: application/json'); // Return JSON response

// Connect to database
include "conn.php";

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get user ID from the POST request
$data = json_decode(file_get_contents("php://input"), true);
$userId = (int)$data['user_id'];

if ($userId) {
    // Prepare and execute the DELETE query
    $sqlDelete = "DELETE FROM user WHERE user_id = $userId";
    mysqli_query($conn, $sqlDelete);

    if (mysqli_affected_rows($conn) <= 0) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    } else {
        echo json_encode(['success' => true]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'User ID not provided']);
}

exit();
?>