<?php
// Step 2: Include database connection file
include 'conn.php';

// Step 2: Query to retrieve grade details (grade_id, grade, grade_name) from the grade table
$query = "SELECT id, grade_level FROM grade";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Create an array to store grade options
    $gradeOptions = [];
    
    // Fetch grade details
    while ($row = $result->fetch_assoc()) {
        // Concatenate grade and grade name (e.g., "1A")
        $gradeDisplay = $row['grade_level'] ; 
        // Store the grade id as the value and grade display name as the text
        $gradeOptions[] = [
            'grade_level' => $row['grade_level'],
            'grade_display' => $gradeDisplay
        ];
    }
} else {
    $gradeOptions = [];
}

if (count($gradeOptions) > 0) {
    foreach ($gradeOptions as $grade) {
        // Use grade_id as the value and display grade + grade_name
        echo "<option value=\"" . $grade['grade_level'] . "\">" . $grade['grade_display'] . "</option>";
    }
} else {
    echo "<option value=\"\">No grade available</option>";
}


?>