<?php
include 'conn.php'; // Include database connection

// Get the quiz ID from the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['quizId'])) {
    $quiz_id = intval($data['quizId']);


        // Begin transaction to ensure atomic operation
        $conn->begin_transaction();

        // Step 1: Delete from the answer_selection table
        $deleteAnswers = $conn->prepare("DELETE FROM answer_selection WHERE quest_id IN (SELECT quest_id FROM question WHERE quiz_id = ?)");
        $deleteAnswers->bind_param("i", $quiz_id);
        if (!$deleteAnswers->execute()) {
            throw new Exception("Error deleting answer selections: " . $conn->error);
        }

        // Step 2: Delete from the question table
        $deleteQuestions = $conn->prepare("DELETE FROM question WHERE quiz_id = ?");
        $deleteQuestions->bind_param("i", $quiz_id);
        if (!$deleteQuestions->execute()) {
            throw new Exception("Error deleting questions: " . $conn->error);
        }

        // Step 3: Delete from the exercise table
        $deleteExercise = $conn->prepare("DELETE FROM exercise WHERE quiz_id = ?");
        $deleteExercise->bind_param("i", $quiz_id);
        if (!$deleteExercise->execute()) {
            throw new Exception("Error deleting exercises: " . $conn->error);
        }

        // Step 4: Delete from the test table (if applicable)
        $deleteTest = $conn->prepare("DELETE FROM test WHERE quiz_id = ?");
        $deleteTest->bind_param("i", $quiz_id);
        if (!$deleteTest->execute()) {
            throw new Exception("Error deleting tests: " . $conn->error);
        }

        // Step 5: Finally, delete from the quiz table
        $deleteQuiz = $conn->prepare("DELETE FROM quiz WHERE quiz_id = ?");
        $deleteQuiz->bind_param("i", $quiz_id);
        if (!$deleteQuiz->execute()) {
            throw new Exception("Error deleting quiz: " . $conn->error);
        }

        // Commit the transaction if all operations were successful
        $conn->commit();

        echo "All information related to quiz ID $quiz_id has been successfully deleted.";
    
    
        $conn->close();
    }
 else {
    echo "Quiz ID is required.";
}
?>
