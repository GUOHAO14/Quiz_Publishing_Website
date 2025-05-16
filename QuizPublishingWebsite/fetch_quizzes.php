<?php
include 'conn.php'; // Include database connection

// Fetch filter parameters from the GET request
$grade = isset($_GET['grade']) ? intval($_GET['grade']) : null;
$subject = isset($_GET['subject']) ? intval($_GET['subject']) : null;
$course = isset($_GET['course']) ? intval($_GET['course']) : null;
$folder = isset($_GET['folder']) ? intval($_GET['folder']) : null;
$status = isset($_GET['status']) ? intval($_GET['status']) : null;
$year = isset($_GET['year']) ? intval($_GET['year']) : null;
$type = isset($_GET['type']) ? $_GET['type'] : null; // Keep this as string, not integer

// Build the base query
$sql = "
    SELECT DISTINCT quiz.quiz_id, quiz.quiz_name, quiz.quiz_type, quiz.creation_date, quiz.status 
    FROM quiz
    LEFT JOIN assessment ON quiz.quiz_id = assessment.quiz_id
    LEFT JOIN test ON quiz.quiz_id = test.quiz_id
    LEFT JOIN exercise ON quiz.quiz_id = exercise.quiz_id
    LEFT JOIN folder ON exercise.folder_id = folder.folder_id
    WHERE 1=1
";

// Add conditions based on filters
if (!is_null($grade)) {
    $sql .= " AND assessment.grade = $grade";
}
if (!is_null($subject)) {
    $sql .= " AND assessment.subject_id = $subject";
}
if (!is_null($folder)) {
    $sql .= " AND exercise.folder_id = $folder";
}
if (!is_null($course)) {
    $sql .= " AND (test.course_id = $course OR folder.course_id = $course)";
}
if (!is_null($status)) {
    $sql .= " AND quiz.status = $status";
}
if (!is_null($year)) {
    $sql .= " AND YEAR(quiz.creation_date) = $year";
}
if (!is_null($type) && $type !== '') {  // Ensure the quiz type filter is applied only when it is not empty
    $sql .= " AND quiz.quiz_type = '" . $conn->real_escape_string($type) . "'"; // Use real_escape_string for security
}

// Execute the query
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    echo '<div class="quiz-container">'; // Parent container for flexbox

    // Loop through each quiz and display it in a div
    while ($row = $result->fetch_assoc()) {
        $statusText = ($row['status'] == 0) ? 'Saved' : 'Submitted';

        echo '
        <div class="a" data-id="' . htmlspecialchars($row['quiz_id']) . '">
            <div class="a1">
                <div class="a1-1">
                    <p class="tag-a1">' . htmlspecialchars($row['quiz_type']) . '</p>
                    <div class="dropdown">
                        <button class="dropdown-toggle" onclick="toggleDropdown(this)">
                            <img src="media/more.png" alt="More" width="20px" height="20px">
                        </button>
                        <div class="dropdown-menu" style="display: none;">
                            <button class="dropdown-item" onclick="editQuiz(\'' . htmlspecialchars($row['quiz_id']) . '\')" ' . ($row['status'] == 1 ? 'disabled' : '') . '>Edit</button>
                            <button class="dropdown-item" onclick="removeQuiz(\'' . htmlspecialchars($row['quiz_id']) . '\')">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="a2">
                <br>
                Quiz Name: ' . htmlspecialchars($row['quiz_name']) . '
                <br>
                Status: ' . htmlspecialchars($statusText) . '
                <br>
                Year: ' . date('Y', strtotime($row['creation_date'])) . '
                <br><br>
                <a href="resultAnalysis.php?id=' . htmlspecialchars($row['quiz_id']) . '" class="view" title="View Details">VIEW</a>          
            </div>
        </div>';
    }

    echo '</div>'; // Close the parent container
} else {
    echo "No quizzes found.";
}
$conn->close(); // Close the database connection
?>
