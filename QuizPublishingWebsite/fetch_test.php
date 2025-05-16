<?php
include 'conn.php'; // Include database connection

// Fetch quizzes from the database
$sql = "SELECT * FROM test";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    echo '<div class="quiz-container">'; // Parent container for flexbox
    // Loop through each quiz and display it in a div
    while ($row = $result->fetch_assoc()) {
        echo '
        <div class="a">
            <div class="a1">
                <div class="a1-1">
                    <p class="tag-a1">' . htmlspecialchars($row['title']) . '</p>
                    <a href="javascript:void(0);" class="more-a" title="More" onclick="toggleButtons(this)">
                        <img src="media/more.png" alt="More" width="20px" height="20px">
                    </a>
                </div>
                <div class="a1-1">
                    <p class="tag-a2">' . $row['question_count'] . ' Questions</p>
                    <p class="tag-a2" title="Mathematics"> </p>
                </div>
            </div>
            <div class="a2">
                <h3 class="name-a">' . htmlspecialchars($row['title']) . '</h3>
                <p class="accuracy" style="background: linear-gradient(to right, #5ced73 ' . $row['accuracy'] . '%, red 0);">' . $row['accuracy'] . '% Accuracy</p>
                <button class="start" title="Start Quiz">START</button>
            </div>
            <div class="more-options" style="display: none;"> <!-- Hidden by default -->
                <button class="edit">Edit</button>
                <button class="remove">Remove</button>
            </div>
        </div>';
    }
    echo '</div>'; // Close the parent container
} else {
    echo "No quizzes found.";
}

$conn->close(); // Close the database connection
?>
