<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="teacher.css">
</head>
<body>
    <div>
        <h1 style="margin-top: 20px; text-align: center;">Quiz Dashboard</h1>
    </div>
<br><br>
    <table>
        <tr>
            <td colspan="4">
  <form id="filter-form" method="GET" action="">
  <div class="filters">
  <select id="type" name="type" >
        <option value="" disabled selected hidden>Select Quiz Type</option>
        <option value="Exercise">Exercise</option>
        <option value="Test">Test</option>
        <option value="Assessment">Assessment</option>
        <option value="">All</option>
    </select>
    
    <select id="grade" name="grade">
    <option value="" disabled selected hidden>Select Grade</option>
    <?php
    // Include the PHP code here to generate options
    include 'get_grade.php'; // Assuming the PHP code to fetch classes is in this file
     ?>
    </select>

    <select id="subject" name="subject">
    <option value="" disabled selected hidden>Select Subject</option>
    <?php
    // Include the PHP code here to generate options
    include 'get_subject.php'; // Assuming the PHP code to fetch classes is in this file
    ?>
    </select>

    <select id="course" name="course">
    <option value="" disabled selected hidden>Select Course</option>
    <?php
    // Include the PHP code here to generate options
    include 'get_courses.php'; // Assuming the PHP code to fetch classes is in this file
    ?>
    </select>

    <select id="folder" name="folder">
    <option value="" disabled selected hidden>Select Folder</option>
    <?php
    $sql = "SELECT folder_id, folder_name FROM folder";
    $result = $conn->query($sql);

    // Check if there are folders in the database
    if ($result->num_rows > 0) {
        // Loop through each folder and generate an option
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['folder_id']) . '">' . htmlspecialchars($row['folder_name']) . '</option>';
        }
    } else {
        // If no folders found, display a default option
        echo '<option value="" disabled>No folders available</option>';
    }
    ?>
    </select>

    <select name="status" id="status">
      <option value="" disabled selected hidden>Select Status</option>
      <option value="0">Saved</option>
      <option value="1">Submmited</option>
      <!-- Options populated dynamically -->
    </select>

    <select name="year" id="year">
      <option value="" disabled selected hidden>Select Year</option>
      <option value="2023">2023</option>
      <option value="2024">2024</option>
      <!-- Options populated dynamically -->
    </select>

    <button type="submit">Filter</button>
  </div>
</form>
            </td>
        </tr>
        <tr>
            <td colspan="4" id="DisplayArea">
            <body onload="DisplayQuiz()">
            </td>
        </tr>
    </table>

    <script>
        function DisplayQuiz() {
            // Fetch the quiz data from the database using AJAX
            fetch('fetch_quizzes.php')
                .then(response => response.text())
                .then(data => {
                    // Inject the fetched data into the quiz display area
                    document.getElementById('DisplayArea').innerHTML = data;
                })
                .catch(error => console.error('Error fetching quiz data:', error));
        }

  function toggleDropdown(button) {
  const dropdownMenu = button.nextElementSibling;

  // Close all other open dropdowns
  document.querySelectorAll('.dropdown-menu').forEach(menu => {
    if (menu !== dropdownMenu) menu.style.display = 'none';
  });

  // Toggle current dropdown
  dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
  const isClickInside = event.target.closest('.dropdown');
  if (!isClickInside) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
      menu.style.display = 'none';
    });
  }
});


function removeQuiz(quizId) {
    if (confirm("Are you sure you want to remove this quiz? This action cannot be undone.")) {
        // Find the specific quiz element to be removed
        const quizElement = document.querySelector(`.a[data-id="${quizId}"]`);
        
        // Add the fade-out animation class to the quiz element
        quizElement.classList.add('fade-out');

        // Wait for the animation to complete before removing the element from the DOM
        setTimeout(() => {
            // Make the fetch request to delete the quiz from the database
            fetch('delete_quiz.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ quizId: quizId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Quiz deleted successfully!");
                }
            })
            .finally(() => {
                // Remove the quiz element from the DOM after the animation
                quizElement.remove();
            });
        }, 500); // Match the duration of the animation (in milliseconds)
    }
}

document.getElementById('filter-form').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Get form data
    const formData = new FormData(this);
    const queryString = new URLSearchParams(formData).toString();

    // Fetch filtered quizzes
    fetch('fetch_quizzes.php?' + queryString)
        .then(response => response.text())
        .then(data => {
            // Display filtered quizzes
            document.getElementById('DisplayArea').innerHTML = data;

            // Reset all dropdowns to their default values
            const dropdowns = this.querySelectorAll('select');
            dropdowns.forEach(dropdown => dropdown.selectedIndex = 0); // Reset each dropdown to the first option
        })
        .catch(error => console.error('Error fetching filtered quizzes:', error));
});



function editQuiz(quizId) {
    window.location.href = `edit_quiz.php?quiz_id=${quizId}`;
}

document.getElementById('filter-form').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Get form data
        const formData = new FormData(this);
        const queryString = new URLSearchParams(formData).toString();

        // Fetch filtered quizzes
        fetch('fetch_quizzes.php?' + queryString)
            .then(response => response.text())
            .then(data => {
                document.getElementById('DisplayArea').innerHTML = data;
            })
            .catch(error => console.error('Error fetching filtered quizzes:', error));
    });
    document.addEventListener('DOMContentLoaded', function () {
    const quizTypeSelect = document.getElementById('type');
    const gradeSelect = document.getElementById('grade');
    const courseSelect = document.getElementById('course');
    const subjectSelect = document.getElementById('subject');
    const folderSelect = document.getElementById('folder');

    // Store the initial state of the select elements
    const initialState = {
        grade: gradeSelect.innerHTML,
        course: courseSelect.innerHTML,
        subject: subjectSelect.innerHTML,
        folder: folderSelect.innerHTML,
    };

    // Disable all dependent select elements initially
    const disableAll = () => {
        gradeSelect.disabled = true;
        courseSelect.disabled = true;
        subjectSelect.disabled = true;
        folderSelect.disabled = true;
    };

    // Reset all select elements to their original state
    const resetSelects = () => {
        gradeSelect.innerHTML = initialState.grade;
        courseSelect.innerHTML = initialState.course;
        subjectSelect.innerHTML = initialState.subject;
        folderSelect.innerHTML = initialState.folder;
        disableAll();
    };

    // Disable all select elements at the start
    disableAll();

    // Add event listener to the quiz type dropdown
    quizTypeSelect.addEventListener('change', function () {
        resetSelects(); // Reset the dropdowns to their initial state

        switch (quizTypeSelect.value) {
            case 'Exercise':
                folderSelect.disabled = false; // Enable only the folder dropdown
                break;

            case 'Test':
                courseSelect.disabled = false; // Enable only the course dropdown
                break;

            case 'Assessment':
                gradeSelect.disabled = false; // Enable grade and subject dropdowns
                subjectSelect.disabled = false;
                break;

            default:
                disableAll(); // Keep everything disabled for invalid or default options
                break;
        }
    });
});
    </script>

<?php
include 'conn.php'; // Database connection

// Fetch unique filter values
function getDistinctValues($conn, $column) {
    $query = "SELECT DISTINCT $column FROM quiz";
    $result = $conn->query($query);
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $values[] = $row[$column];
    }
    return $values;
}




// Fetch filtered quizzes


$conn->close();
?>
</body>
</html>