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


// Step 2: Include database connection file
include 'conn.php';

// Step 2: Query to retrieve class names from the class table
$query = "SELECT grade_level FROM grade";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Create an array to store class names
    $gradeOptions = [];
    
    // Fetch class names
    while ($row = $result->fetch_assoc()) {
        $gradeOptions[] = $row['grade_level'];
    }
} else {
    $gradeOptions = [];
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="create.css">
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <title>Create Quiz</title>
    <style>
        input[type=text]::placeholder {
            text-align: left;
        }

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
        <h1 id="summaryHeading">Create Quiz</h1>
    </div>
    <form id="quizForm" onsubmit="submitQuiz(event)">
        <table>
            <tr>
                <td>
                    <div class="firstline">
                        <input type="text" id="qname" name="qname" placeholder="Quiz Name" class="name" autocomplete="off" required><br>
                        <input type="text" id="description" class="description" placeholder="Description" autocomplete="off" required>
                    </div>

                    <div class="container">

                    <div class="form-group">
                            <label for="type">Quiz Type:</label>
                            <select id="type" name="type" required>
                                <option value="" disabled selected hidden>Select Quiz Type</option>
                                <option value="Exercise">Exercise</option>
                                <option value="Test">Test</option>
                                <option value="Assessment">Assessment</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="grade">Grade:</label>
                            <select id="grade" name="grade" required>
                            <option value="" disabled selected hidden>Select Grade</option>
                                <?php
                                // Include the PHP code here to generate options
                                include 'get_grade.php'; // Assuming the PHP code to fetch classes is in this file
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="course">Course:</label>
                            <select id="course" name="course" required>
                            <option value="" disabled selected hidden>Select Course</option>
                                <?php
                                // Include the PHP code here to generate options
                                include 'get_courses.php'; // Assuming the PHP code to fetch classes is in this file
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject:</label>
                            <select id="subject" name="subject" required>
                            <option value="" disabled selected hidden>Select Subject</option>
                            <?php
                                // Include the PHP code here to generate options
                                include 'get_subject.php'; // Assuming the PHP code to fetch classes is in this file
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="folder">Folder:</label>
                            <select id="folder" name="folder" required>
                            <option value="" disabled selected hidden>Select Folder</option>
                            <?php
                                // Include the PHP code here to generate options
                                include 'get_folders.php'; // Assuming the PHP code to fetch classes is in this file
                                ?>
                            </select>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div id="questionContainer"></div>

        <table class="Qbox">
            <tr>
                <th width="100%">
                    <button class="interactive-add-btn" id="addButton" onclick="addQuestionBox()">Add</button>
                    <div class="content-container" id="contentContainer"></div>
                    </button>
                </th>
    </tr>
                <tr>
                <th>
                    <div style="background-color: white;
    padding-top: 6px;
    border-radius: 5px; text-align: center; width: 50px; margin: auto; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <button type="submit" id="submitButton">
                        <img src="media/save.png" alt="Submit" class="submit">Save
                    </button>
                    </div>
                <input style="display: none;" type="image" src="media/save.png" title="Save" alt="Save" class="sidebutton" id="saveQuizButton" onclick="submitQuiz(event)">
                </th>
            </tr>
        </table>
        <br>
    </form>

    

    <script src="create.js"></script>
</body>
</html>
