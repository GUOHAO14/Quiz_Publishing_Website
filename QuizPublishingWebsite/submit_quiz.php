<?php
session_start();
$_SESSION['quizRedirect'] = "yes";
include 'conn.php';

// Read JSON input from fetch request
$data = json_decode(file_get_contents('php://input'), true);

if ($data === null) {
    die("Invalid JSON input");
}

// Debug output to confirm data is received
var_dump($data); // This should print the entire `$data` array, including `status`



// Get the last quiz_id from the quiz table
$query = "SELECT MAX(quiz_id) AS max_id FROM quiz";
$result = $conn->query($query);
$newQuizId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

echo "New Quiz ID: " . $newQuizId;

// Prepare data for quiz insertion
$quiz_name = $data['name'];
$description = $data['description'];
$grade = $data['grade'];
$quiz_type = strtolower($data['quizType']);
$subject = $data['subject'];
$folder_name = $data['folderName'];
$creation_date = date("Y-m-d H:i:s"); // Current timestamp
$status = $data['status']; // Get the status value

// Insert quiz into quiz table
$insert_quiz = $conn->prepare("INSERT INTO quiz (quiz_id, quiz_name, quiz_type, creation_date, description, status) VALUES (?, ?, ?, ?, ?, ?)");
$insert_quiz->bind_param("issssi", $newQuizId, $quiz_name, $quiz_type, $creation_date, $description, $status);

if (!$insert_quiz->execute()) {
    throw new Exception("Error inserting quiz into quiz table: " . $conn->error);
}

// Success response (optional)
echo "Quiz inserted successfully.";


    // Logic for Exercise, Assessment, and Test IDs
    if ($quiz_type == "Exercise") {
        $query = "SELECT MAX(exe_id) AS max_id FROM exercise";
        $result = $conn->query($query);
        $newExeId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

        $insert_exercise = $conn->prepare("INSERT INTO exercise (exe_id, quiz_id, folder_id) VALUES (?, ?, ?)");
        $insert_exercise->bind_param("iii", $newExeId, $newQuizId, $folder_name);
        if (!$insert_exercise->execute()) {
            throw new Exception("Error inserting exercise data: " . $conn->error);
        }
    } elseif ($quiz_type == "Assessment") {
        $query = "SELECT MAX(assess_id) AS max_id FROM assessment";
        $result = $conn->query($query);
        $newAssessId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

        $grade = $data['grade'] ?? ''; // Default to empty string if not provided
        $insert_assessment = $conn->prepare("INSERT INTO assessment (assess_id, grade, year, subject_id, quiz_id) VALUES (?, ?, ?, ?, ?)");
        $insert_assessment->bind_param("iiisi", $newAssessId, $grade, date("Y"), $subject, $newQuizId);
        if (!$insert_assessment->execute()) {
            throw new Exception("Error inserting assessment data: " . $conn->error);
        }
    } elseif ($quiz_type == "Test") {
        $query = "SELECT MAX(test_id) AS max_id FROM test";
        $result = $conn->query($query);
        $newTestId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

        $course_id = $data['course']; // Assuming course_id is sent in the request
        $insert_test = $conn->prepare("INSERT INTO test (test_id, course_id, quiz_id) VALUES (?, ?, ?)");
        $insert_test->bind_param("iii", $newTestId, $course_id, $newQuizId);
        if (!$insert_test->execute()) {
            throw new Exception("Error inserting test data: " . $conn->error);
        }
    }

    // Insert questions
    foreach ($data['question'] as $question) {
        $question_text = $question['questionName'];
        $question_type = $question['questionType'];

        $query = "SELECT MAX(quest_id) AS max_id FROM question";
        $result = $conn->query($query);
        $newQuestId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

        $insert_question = $conn->prepare("INSERT INTO question (quest_id, text, quiz_id, question_type) VALUES (?, ?, ?, ?)");
        $insert_question->bind_param("isis", $newQuestId, $question_text, $newQuizId, $question_type);
        if (!$insert_question->execute()) {
            throw new Exception("Error inserting question: " . $conn->error);
        } else {
            echo "Inserted Question ID: " . $newQuestId . " with Text: " . $question_text . "<br>";
        }

        // Insert options for MCQ or Checkboxes
        if (in_array($question_type, ['MCQ', 'Checkboxes']) && isset($question['option']) && is_array($question['option'])) {
            foreach ($question['option'] as $option) {
                if (!empty($option['name'])) { // Ensure option name is not empty
                    $option_text = $option['name'];
                    $accuracy = isset($option['accuracy']) ? (int)$option['accuracy'] : 0;
        
                    // Retrieve the next available select_id for the answer_selection table
                    $query = "SELECT MAX(select_id) AS max_id FROM answer_selection";
                    $result = $conn->query($query);
                    $newSelectId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;
        
                    // Insert the option into answer_selection
                    $insert_option = $conn->prepare("INSERT INTO answer_selection (select_id, text, accuracy, quest_id) VALUES (?, ?, ?, ?)");
                    $insert_option->bind_param("isii", $newSelectId, $option_text, $accuracy, $newQuestId);
        
                    if (!$insert_option->execute()) {
                        error_log("Error inserting option: " . $insert_option->error);
                    } else {
                        echo "Inserted Option ID: $newSelectId with Text: $option_text and Accuracy: $accuracy<br>";
                    }
                }
            }
        }
        

        // Handle short-answer question
        if ($question_type === "shortans" && isset($question['answer'])) {
            $answer_text = $question['answer'];

            // Retrieve the next available select_id for the answer_selection table
            $query = "SELECT MAX(select_id) AS max_id FROM answer_selection";
            $result = $conn->query($query);
            $newSelectId = ($result->num_rows > 0 && ($row = $result->fetch_assoc()) && $row['max_id']) ? $row['max_id'] + 1 : 1;

            // Insert the short-answer into answer_selection with accuracy = 1
            $insert_short_answer = $conn->prepare("INSERT INTO answer_selection (select_id, text, accuracy, quest_id) VALUES (?, ?, ?, ?)");
            $accuracy = 1; // Accuracy is always 1 for short-answer questions
            $insert_short_answer->bind_param("isii", $newSelectId, $answer_text, $accuracy, $newQuestId);
            if (!$insert_short_answer->execute()) {
                throw new Exception("Error inserting short-answer into answer_selection: " . $conn->error);
            } else {
                echo "Inserted Short Answer into answer_selection for Question ID: " . $newQuestId . " with Text: " . $answer_text . " and Accuracy: " . $accuracy . "<br>";
            }
        }
    }


    $conn->close();
    
?>
