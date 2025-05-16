<?php
// Step 2: Include database connection file
include 'conn.php';

$subjectQuery = "SELECT subject_id, subject_name FROM subject";
$subjectResult = $conn->query($subjectQuery);

$subjectOptions = [];
if ($subjectResult->num_rows > 0) {
    // Fetch subject details
    while ($row = $subjectResult->fetch_assoc()) {
        // Store subject_id and subject_name in the options array
        $subjectOptions[] = [
            'subject_id' => $row['subject_id'],
            'subject_name' => $row['subject_name']
        ];
    }
} else {
    $subjectOptions = [];
}

if (count($subjectOptions) > 0) {
    foreach ($subjectOptions as $subject) {
        // Display subject options as subject_name and use subject_id as the value
        echo "<option value=\"" . $subject['subject_id'] . "\">" . $subject['subject_name'] . "</option>";
    }
} else {
    echo "<option value=\"\">No subjects available</option>";
}

?>