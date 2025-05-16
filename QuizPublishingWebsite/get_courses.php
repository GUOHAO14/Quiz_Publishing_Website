<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file
include 'conn.php';

// Query to retrieve course details from the course table
$query = "SELECT course_id FROM course";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Create an array to store course options
    $courseOptions = [];

    // Fetch course details
    while ($row = $result->fetch_assoc()) {
        // Store course_id as both the value and display text
        $courseOptions[] = [
            'course_id' => $row['course_id'],
            'display' => $row['course_id']
        ];
    }
} else {
    $courseOptions = [];
}

// Generate <option> elements based on the course options
if (count($courseOptions) > 0) {
    foreach ($courseOptions as $course) {
        // Use course_id as the value and display course_id as the text
        echo "<option value=\"" . $course['course_id'] . "\">" . $course['display'] . "</option>";
    }
} else {
    echo "<option value=\"\">No courses available</option>";
}

// Close the database connection
?>
