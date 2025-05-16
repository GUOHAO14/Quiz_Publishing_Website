<?php
session_start();
$_SESSION['quizRedirect'] = "yes";
// resubmit.php
include 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the raw POST data (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if ($data === null) {
    die("Invalid JSON input");
}

// Decode the incoming JSON data

// Check if decoding was successful

// Log the decoded data to check if it contains 'quizMetadata'
error_log("Decoded data: " . print_r($data, true));  // Log the decoded data to PHP error log

// Access quizMetadata (ensure it's part of the incoming JSON structure)


// Check if data is submitted via POST
    // Validate required quiz metadata fields

    $quizName = $data['name'];
    $description = $data['description'];
    $grade = $data['grade'];
    $quizType = $data['quizType'];
    $subject = $data['subject'];
    $folder_name = $data['folderName'];
    $creation_date = date("Y-m-d H:i:s"); // Current timestamp
    $status = $data['status'];
    $quiz_id = $data['quiz_id'];

    // Step 1: Remove all existing data related to the quiz_id from exercise, assessment, and test tables
    $deleteExercise = $conn->prepare("DELETE FROM exercise WHERE quiz_id = ?");
    $deleteExercise->bind_param("i", $quiz_id);
    if (!$deleteExercise->execute()) {
        error_log("Error deleting exercises: " . $conn->error);  // Log error if execution fails
        echo json_encode(['error' => 'Failed to delete exercises']);
        exit;
    }

    // Step 2: Delete from the assessment table based on quiz_id
    $deleteAssessment = $conn->prepare("DELETE FROM assessment WHERE quiz_id = ?");
    $deleteAssessment->bind_param("i", $quiz_id);
    if (!$deleteAssessment->execute()) {
        error_log("Error deleting assessments: " . $conn->error);  // Log error if execution fails
        echo json_encode(['error' => 'Failed to delete assessments']);
        exit;
    }

    // Step 3: Delete from the test table based on quiz_id
    $deleteTest = $conn->prepare("DELETE FROM test WHERE quiz_id = ?");
    $deleteTest->bind_param("i", $quiz_id);
    if (!$deleteTest->execute()) {
        error_log("Error deleting tests: " . $conn->error);  // Log error if execution fails
        echo json_encode(['error' => 'Failed to delete tests']);
        exit;
    }

    // Step 4: Delete from the question table
    $deleteAnswers = $conn->prepare("DELETE FROM answer_selection WHERE quest_id IN (SELECT quest_id FROM question WHERE quiz_id = ?)");
    $deleteAnswers->bind_param("i", $quiz_id);
    if (!$deleteAnswers->execute()) {
        error_log("Error deleting answer selections: " . $conn->error);  // Log error if execution fails
        echo json_encode(['error' => 'Failed to delete answer selections']);
        exit;
    }

    $deleteQuestions = $conn->prepare("DELETE FROM question WHERE quiz_id = ?");
    $deleteQuestions->bind_param("i", $quiz_id);
    if (!$deleteQuestions->execute()) {
        error_log("Error deleting questions: " . $conn->error);  // Log error if execution fails
        echo json_encode(['error' => 'Failed to delete questions']);
        exit;
    }

    // Step 5: Update quiz metadata in the database
    $stmt = $conn->prepare("UPDATE quiz SET quiz_name = ?, quiz_type = ?, creation_date = ?, description = ?, status = ? WHERE quiz_id = ?");
    $creation_date = date("Y-m-d H:i:s");  // Get current timestamp
    $stmt->bind_param("sssssi", $quizName, $quizType, $creation_date, $description, $status, $quiz_id);
    if (!$stmt->execute()) {
        error_log("Error updating quiz metadata: " . $conn->error);  // Log error if execution fails
        echo json_encode(['error' => 'Failed to update quiz metadata']);
        exit;
    }

    if ($quizType == "Exercise") {
        $query = "SELECT MAX(exe_id) AS max_id FROM exercise";
        $result = $conn->query($query);
        $newExeId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

        $insert_exercise = $conn->prepare("INSERT INTO exercise (exe_id, quiz_id, folder_id) VALUES (?, ?, ?)");
        $insert_exercise->bind_param("iii", $newExeId, $quiz_id, $folder_name);
        if (!$insert_exercise->execute()) {
            throw new Exception("Error inserting exercise data: " . $conn->error);
        }
    } elseif ($quizType == "Assessment") {
        $query = "SELECT MAX(assess_id) AS max_id FROM assessment";
        $result = $conn->query($query);
        $newAssessId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

        $grade = $data['grade'] ?? ''; // Default to empty string if not provided
        $insert_assessment = $conn->prepare("INSERT INTO assessment (assess_id, grade, year, subject_id, quiz_id) VALUES (?, ?, ?, ?, ?)");
        $insert_assessment->bind_param("iiisi", $newAssessId, $grade, date("Y"), $subject, $quiz_id);
        if (!$insert_assessment->execute()) {
            throw new Exception("Error inserting assessment data: " . $conn->error);
        }
    } elseif ($quizType == "Test") {
        $query = "SELECT MAX(test_id) AS max_id FROM test";
        $result = $conn->query($query);
        $newTestId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

        $course_id = $data['course']; // Assuming course_id is sent in the request
        $insert_test = $conn->prepare("INSERT INTO test (test_id, course_id, quiz_id) VALUES (?, ?, ?)");
        $insert_test->bind_param("iii", $newTestId, $course_id, $quiz_id);
        if (!$insert_test->execute()) {
            throw new Exception("Error inserting test data: " . $conn->error);
        }
    }



    // Step 6: Insert new data based on the quiz type
    // Loop through the questions to insert them
    if (isset($data['question']) && is_array($data['question'])) {
        foreach ($data['question'] as $question) {
            $question_text = $question['questionName'] ?? '';
            $question_type = $question['questionType'] ?? '';

            $query = "SELECT MAX(quest_id) AS max_id FROM question";
            $result = $conn->query($query);
            $newQuestId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

            $insert_question = $conn->prepare("INSERT INTO question (quest_id, text, quiz_id, question_type) VALUES (?, ?, ?, ?)");
            $insert_question->bind_param("isis", $newQuestId, $question_text, $quiz_id, $question_type);
            if (!$insert_question->execute()) {
                error_log("Error inserting question: " . $conn->error);  // Log error if execution fails
            }

            // Insert options for MCQ or Checkboxes
            if (in_array($question_type, ['MCQ', 'Checkboxes']) && isset($question['option']) && is_array($question['option'])) {
                foreach ($question['option'] as $option) {
                    if (!empty($option['name'])) {
                        $option_text = $option['name'];
                        $accuracy = isset($option['accuracy']) ? (int)$option['accuracy'] : 0;

                        $query = "SELECT MAX(select_id) AS max_id FROM answer_selection";
                        $result = $conn->query($query);
                        $newSelectId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

                        $insert_option = $conn->prepare("INSERT INTO answer_selection (select_id, text, accuracy, quest_id) VALUES (?, ?, ?, ?)");
                        $insert_option->bind_param("isii", $newSelectId, $option_text, $accuracy, $newQuestId);
                        if (!$insert_option->execute()) {
                            error_log("Error inserting option: " . $conn->error);  // Log error if execution fails
                        }
                    }
                }
            }

            // Handle short-answer question
            if ($question_type === "shortans" && isset($question['answer'])) {
                $answer_text = $question['answer'];

                $query = "SELECT MAX(select_id) AS max_id FROM answer_selection";
                $result = $conn->query($query);
                $newSelectId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

                $insert_short_answer = $conn->prepare("INSERT INTO answer_selection (select_id, text, accuracy, quest_id) VALUES (?, ?, ?, ?)");
                $accuracy = 1;  // Accuracy is always 1 for short-answer questions
                $insert_short_answer->bind_param("isii", $newSelectId, $answer_text, $accuracy, $newQuestId);
                if (!$insert_short_answer->execute()) {
                    error_log("Error inserting short-answer into answer_selection: " . $conn->error);  // Log error if execution fails
                }
            }
        }
    }

    // If everything worked, return success message
    echo json_encode(['success' => true]);
?>
