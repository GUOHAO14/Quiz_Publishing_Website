<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}

if (!isset($_SESSION['teacher_id']) && !isset($_SESSION['admin_id']) ) {
    session_start();
    header("Location: index.php");
    session_write_close();
    exit();
}

if (isset($_SESSION['quizRedirect']) && $_SESSION['quizRedirect'] == "yes") {
    if (isset($_SESSION['admin_id'])) {
        header("Location: adminMngQuiz.php"); // Redirect to admin's path
    } else if (isset($_SESSION['teacher_id'])) {
        header("Location: teachhome.php"); // Redirect to teacher's path
    } else {
        header("Location: index.php"); // Redirect to default path if neither is set
    }
}

include 'conn.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['quizEditId'])) {
    die("Quiz ID is required.");
}

$quiz_id = $_SESSION['quizEditId']; // Sanitize quiz_id

// Fetch quiz details
$quizQuery = $conn->prepare("SELECT * FROM quiz WHERE quiz_id = ?");
$quizQuery->bind_param("i", $quiz_id);
$quizQuery->execute();
$quiz = $quizQuery->get_result()->fetch_assoc();

$AssessmentQuery = $conn->prepare("SELECT * FROM assessment WHERE quiz_id = ?");
$AssessmentQuery->bind_param("i", $quiz_id);
$AssessmentQuery->execute();
$Assessment = $AssessmentQuery->get_result()->fetch_assoc();

$grade_value = $Assessment['grade'] ?? '';
$grade_text = $Assessment['grade'] ?? 'Select Grade';

$exerciseQuery = $conn->prepare("SELECT * FROM exercise WHERE quiz_id = ?");
$exerciseQuery->bind_param("i", $quiz_id);
$exerciseQuery->execute();
$exercise = $exerciseQuery->get_result()->fetch_assoc();

$AssessmentQuery = $conn->prepare("SELECT * FROM assessment WHERE quiz_id = ?");
$AssessmentQuery->bind_param("i", $quiz_id);
$AssessmentQuery->execute();
$Assessment = $AssessmentQuery->get_result()->fetch_assoc();

// Query to fetch the folder name based on quiz_id
$Fnamequery = "SELECT f.folder_name, f.folder_id
               FROM exercise e
               JOIN folder f ON e.folder_id = f.folder_id
               WHERE e.quiz_id = ?";

// Prepare the query
$stmt = $conn->prepare($Fnamequery);
if (!$stmt) {
    throw new Exception("Failed to prepare the statement: " . $conn->error);
}

// Bind the quiz_id parameter
$stmt->bind_param('i', $quiz_id);

// Execute the query
$stmt->execute();

// Fetch the result
$result = $stmt->get_result();
$folder = $result->fetch_assoc();

// Check if folder was found
if ($folder) {
    $folder_name = $folder['folder_name']; // Folder name
    $folder_id = $folder['folder_id']; // Folder ID
} else {
    // Default values if no folder found
    $folder_name = 'Select Folder';
    $folder_id = '';
}

    $query = "SELECT s.subject_name, s.subject_id
    FROM assessment a
    JOIN subject s ON a.subject_id = s.subject_id
    WHERE a.quiz_id = ?";
    
$stmt = $conn->prepare($query);

if (!$stmt) {
throw new Exception("Failed to prepare the statement: " . $conn->error);
}

// Bind the parameter
$stmt->bind_param('i', $quiz_id);

// Execute the query
$stmt->execute();

// Fetch the result
$result = $stmt->get_result();
$subject = $result->fetch_assoc();

// Set default subject name and ID
$subject_name = $subject['subject_name'] ?? 'Select Subject';
$subject_id = $subject['subject_id'] ?? '';


$course_id = '';
$course_text = 'Select Course';

    if ($quiz['quiz_type'] === 'Test') {
        // Fetch course_id from the quiz table (or related test logic)
        $TestQuery = $conn->prepare("SELECT course_id FROM test WHERE quiz_id = ?");
        $TestQuery->bind_param("i", $quiz_id);
        $TestQuery->execute();
        $test = $TestQuery->get_result()->fetch_assoc();

        if ($test) {
            $course_id = $test['course_id'];
            $course_text = $course_id; // Assuming you want to display the ID; modify as needed
        }
    } elseif ($quiz['quiz_type'] === 'Exercise') {
        // Fetch course_id via exercise and folder tables
        $ExerciseQuery = $conn->prepare("
            SELECT f.course_id 
            FROM exercise e 
            JOIN folder f ON e.folder_id = f.folder_id
            WHERE e.quiz_id = ?
        ");
        $ExerciseQuery->bind_param("i", $quiz_id);
        $ExerciseQuery->execute();
        $exercise = $ExerciseQuery->get_result()->fetch_assoc();

        if ($exercise) {
            $course_id = $exercise['course_id'];
            $course_text = $course_id; // Modify if you want the course name
        }
    }

if (!$quiz) {
    die("Quiz not found.");
}

// Fetch questions for the quiz
$questionQuery = $conn->prepare("SELECT * FROM question WHERE quiz_id = ?");
$questionQuery->bind_param("i", $quiz_id);
$questionQuery->execute();
$questions = $questionQuery->get_result();

foreach ($_POST as $key => $value) {
    // Detect question and option based on key name structure
    if (preg_match('/^option(\d+)_([0-9]+)$/', $key, $matches)) {
        $questionId = intval($matches[1]);
        $optionIndex = intval($matches[2]);

        // Update the answer options based on user input
        $updateOptionQuery = $conn->prepare("UPDATE answer_selection SET text = ?, accuracy = ? WHERE quest_id = ? AND option_index = ?");
        $updateOptionQuery->bind_param("siii", $value, $accuracy, $questionId, $optionIndex);

        // Check if the option is correct
        $accuracy = isset($_POST["correctOption_{$questionId}_{$optionIndex}"]) ? 1 : 0;
        $updateOptionQuery->execute();
    }
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz</title>
    <script src="edit_quiz.js"></script> <!-- Include the JS for handling question boxes -->
    <link rel="stylesheet" href="create.css">
    <style>
        #contentHeader {
            margin-top: 0.8rem;
            margin-left: 14%;
            width: 50%;
            display: flex;
            height: 60px;
            align-items: center;
        }
        
        #back {
            flex: 0;
            width:40px;
            height: 40px;
            background-color: white;
            padding: 2px;
            border-radius: 12px;
            border: 3px solid black;
            box-shadow: -3px -3px 10px 4px rgba(0,0,0,0.07), 3px 3px 10px 4px rgba(0,0,0,0.07);
        }

        #back:hover {
            cursor: pointer;
            background-color: rgb(232, 232, 232);
        }

        #back:active {
            margin-top: 2px;
            margin-left: 2px;
            width: 36px;
            height: 36px;
            background-color: rgb(199, 199, 199);
            box-shadow: none;
        }

        #summaryHeading {
            margin-left: 2rem;
        }

        @media screen and (max-width: 492px) {
            #contentHeader {
                margin-top: 2rem;
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <div id="contentHeader">
        <img id="back" onclick="window.history.back()" src="media/back.png" alt="Back">
        <h1 id="summaryHeading">Edit Quiz</h1>
    </div>
    
    <form id="quizForm" onsubmit="resubmitQuiz(event, <?php echo $quiz_id; ?>)">
        <table>
            <tr>
                <td>
                    <div class="firstline">
                        <input type="text" id="quiz_name" name="quiz_name" value="<?= htmlspecialchars($quiz['quiz_name']) ?>" placeholder="Quiz Name" class="name" autocomplete="off" required>
                        <br>
                        <input type="text" id="description" class="description" value="<?= htmlspecialchars($quiz['description']) ?>" placeholder="Description" autocomplete="off" required>
                    </div>

                    <div class="container">
                        <div class="form-group">
                            <label for="type">Quiz Type:</label>
                            <select id="type" name="type">
                                <option value="" disabled hidden>Select Quiz Type</option>
                                <option value="Exercise" <?= $quiz['quiz_type'] == 'Exercise' ? 'selected' : '' ?>>Exercise</option>
                                <option value="Test" <?= $quiz['quiz_type'] == 'Test' ? 'selected' : '' ?>>Test</option>
                                <option value="Assessment" <?= $quiz['quiz_type'] == 'Assessment' ? 'selected' : '' ?>>Assessment</option>
                            </select>
                        </div>

                        <div class="form-group">
                        <label for="grade">Grade:</label>
                        <select id="grade" name="grade" required>
                            <option value="<?= htmlspecialchars($grade_value) ?>" disabled selected hidden>
                                <?= htmlspecialchars($grade_text) ?>
                            </option>
                            <?php include 'get_grade.php'; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="course">Course:</label>
                        <select id="course" name="course" required>
                            <option value="<?= htmlspecialchars($course_id) ?>" disabled selected hidden>
                                <?= htmlspecialchars($course_text) ?>
                            </option>
                            <?php include 'get_courses.php'; ?>
                        </select>
                    </div>


                        <div class="form-group">
                        <label for="subject">Subject:</label>
                        <select id="subject" name="subject" required>
                            <option value="<?= htmlspecialchars($subject_id) ?>" disabled selected hidden>
                                <?= htmlspecialchars($subject_name) ?>
                            </option>
                            <?php include 'get_subject.php'; ?>
                        </select>
                    </div>

                    <div class="form-group">
    <label for="folder">Folder:</label>
    <select id="folder" name="folder" required>
        <?php if (!empty($folder_id) && !empty($folder_name)) : ?>
            <option value="<?= htmlspecialchars($folder_id) ?>" selected>
                <?= htmlspecialchars($folder_name) ?>
            </option>
        <?php else : ?>
            <option value="" disabled selected hidden>Select Folder</option>
        <?php endif; ?>

        <!-- Fetch and display folders directly based on course_id -->
        <?php
        include 'conn.php';

        if (!empty($course_id)) {
            $query = $conn->prepare("SELECT folder_id, folder_name FROM folder WHERE course_id = ?");
            $query->bind_param("i", $course_id);
            $query->execute();
            $result = $query->get_result();

            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['folder_id']) . '">' . htmlspecialchars($row['folder_name']) . '</option>';
            }
        }
        ?>
    </select>
</div>




                    </div>
                </td>
            </tr>
        </table>

        <div id="questionContainer">
        <div id="questionContainer">
        <?php
$questionCount = 0; // Initialize question count
while ($question = $questions->fetch_assoc()): 
    $questionCount++; // Increment question count
?>
    <div class="questionBox" id="questionBox<?= $questionCount ?>" data-question-count="<?= $questionCount ?>">
        <table>
            <tr>
                <td>
                    <input type="text" id="qname<?= $questionCount ?>" name="qname<?= $questionCount ?>"
                           value="<?= htmlspecialchars($question['text']) ?>" required>
                    <select id="qtype<?= $questionCount ?>" name="qtype<?= $questionCount ?>"
                            onchange="selectqtype(<?= $questionCount ?>)">
                        <option value="MCQ" <?= $question['question_type'] === 'MCQ' ? 'selected' : '' ?>>Multiple Choice</option>
                        <option value="Checkboxes" <?= $question['question_type'] === 'Checkboxes' ? 'selected' : '' ?>>Checkboxes</option>
                        <option value="shortans" <?= $question['question_type'] === 'shortans' ? 'selected' : '' ?>>Short Answer</option>
                    </select>
                </td>
                <td width="5%">
                    <button class="remove" onclick="removeQuestionBox(<?= $questionCount ?>)">
                        <span>&#10005;</span>
                    </button>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div id="dynamicContent<?= $questionCount ?>">
                        <?php if ($question['question_type'] === 'MCQ' || $question['question_type'] === 'Checkboxes'): ?>
                            <?php
                            $optionsQuery = $conn->prepare("SELECT * FROM answer_selection WHERE quest_id = ?");
                            $optionsQuery->bind_param("i", $question['quest_id']);
                            $optionsQuery->execute();
                            $options = $optionsQuery->get_result();
                            ?>
                            <?php while ($option = $options->fetch_assoc()): ?>
                                <div class="option-item">
                                    <input type="<?= $question['question_type'] === 'MCQ' ? 'radio' : 'checkbox' ?>"
                                           name="<?= $question['question_type'] ?><?= $questionCount ?>"
                                           value="<?= htmlspecialchars($option['text']) ?>"
                                           <?= $option['accuracy'] == 1 ? 'checked' : '' ?>>
                                    <textarea class="option-label" placeholder="Enter option text" rows="1"
                                              oninput="autoExpand(this)"><?= htmlspecialchars($option['text']) ?></textarea>
                                    <button type="button" class="remove-option-btn"
                                            onclick="removeSpecificOption(this, <?= $questionCount ?>)">Remove</button>
                                </div>
                            <?php endwhile; ?>
                            <button type="button" class="add-option-btn"
                                    onclick="addOption(<?= $questionCount ?>, '<?= $question['question_type'] ?>')">
                                Add Option
                            </button>
                        <?php elseif ($question['question_type'] === 'shortans'): ?>
                            <input type="text" name="shortAnswer<?= $questionCount ?>"
                                   value="<?= htmlspecialchars($conn->query("SELECT text FROM answer_selection WHERE quest_id = {$question['quest_id']}")->fetch_assoc()['text'] ?? '') ?>"
                                   placeholder="Enter short answer">
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <?php endwhile; ?>
</div>

</div>


        <table class="Qbox">
            <tr>
                <th width="100%">
                <button class="interactive-add-btn" id="addButton">Add</button>
                    <div class="content-container" id="contentContainer"></div>
                </th>
            </tr>
            <tr>
                <th>
                <button style="display: none;" type="submit" class="submit" id="submitButton">
                    <img class="submit" src="media/submit.png" alt="submit"></img>
                </button>
                    <input type="image" src="media/save.png" title="Save" alt="Save" class="sidebutton" id="saveQuizButton" onclick="resubmitQuiz(event, <?php echo $quiz_id; ?>)">

                </th>
            </tr>
        </table>
    </form>

    <!-- JavaScript for enabling/disabling form elements -->
    <script type="text/javascript">
    // Get the question count passed from PHP
    let questionCount = <?php echo $questionCount; ?>; // Ensure the value is echoed correctly as a number
    
    // Log the initial question count
    console.log("Initial question count: " + questionCount);
    
    // Add the event listener to the button
    document.getElementById("addButton").addEventListener("click", function() {
        addQuestionBox();
    });

    function addQuestionBox() {
        questionCount++;  // Increment the question count for each new question box
        
        // Create a new question box with unique id
        const questionContainer = document.getElementById("questionContainer");
        const questionBox = document.createElement("div");
        questionBox.classList.add("questionBox");
        questionBox.id = `questionBox${questionCount}`;
        questionBox.setAttribute('data-question-count', questionCount);

        questionBox.innerHTML = `
            <table class="questionbox">
                <tr>
                    <td>
                        <input type="text" id="qname${questionCount}" name="qname${questionCount}" placeholder="Question Name" class="qname" autocomplete="off" required><br><br>
                        <select id="qtype${questionCount}" name="qtype${questionCount}" class="dropdown" required onchange="selectqtype(${questionCount})">
                            <option value="" disabled selected hidden>Select Question Type</option>
                            <option value="MCQ">Multiple Choice Question</option>
                            <option value="shortans">Short Answer</option>
                            <option value="Checkboxes">Checkboxes</option>
                        </select>
                    </td>
                    <td width="5%">
                        <button class="remove" onclick="removeQuestionBox(${questionCount})">
                            <span>&#10005;</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" id="dynamicContent${questionCount}">
                        <p>Select a question type to see specific content here.</p>
                    </td>
                </tr>
            </table>
        `;
        
        questionContainer.appendChild(questionBox);

        // Update the total question count display
        document.getElementById("totalQuestionCount").textContent = questionCount;
        
        // Optional: Trigger animation for the new question box
        setTimeout(() => {
            questionBox.classList.add("animate-in");
        }, 10); // Slight delay for animation
    }

    const preSelectedFolderId = <?= json_encode($selectedFolderId ?? '') ?>; // Pass the folder ID or empty string
</script>

</body>
</html>