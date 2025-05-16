<?php
session_start();
$_SESSION['quizRedirect'] = "no";
// session_destroy();
if (!isset($_SESSION['admin_id'])) {
    session_start();
    header("Location: index.php");
    session_write_close();
    exit();
}
include "conn.php";

//get all classes
$sqlClass = "SELECT * FROM class ORDER BY year DESC, grade ASC, class_name ASC";
$classList = mysqli_query($conn, $sqlClass);

$sqlUser = 
"SELECT 
    user.user_id, 
    username, 
    role, 
    class_name, 
    grade, 
    year, 
    email, 
    dob, 
    photo 
FROM 
    user
LEFT JOIN
    (SELECT 
        enrolment.student_id,  
        enrolment.class_id, 
        student.user_id, 
        class_name, 
        grade, 
        year  
    FROM 
        enrolment
    JOIN 
        student ON enrolment.student_id = student.student_id
    JOIN 
        class ON enrolment.class_id = class.class_id) as studentTable
ON 
    user.user_id = studentTable.user_id
WHERE role = 'student' OR role = 'teacher' 
ORDER BY username ASC;";
$userResult = mysqli_query($conn, $sqlUser);

$usersList = [];
while ($row = mysqli_fetch_array($userResult)) {
    $email = $row['email'];
    $role = $row['role']; 
    $className = $row['class_name'];
    $class = "-";

    if ($className !== null) {
        $year = (int)$row['year'];
        $currentYear = date("Y");
        if ($year == $currentYear) {
            $class = $row['grade']."-".$className."-".$year;
            $usersList[] = [
                'no' => null, 
                'id' => $row['user_id'], 
                'pfp' => $row['photo'],
                'username' => $row['username'],
                'role' => $role, 
                'email' => $row['email'], 
                'class' => $class, 
                'dob' => $row['dob']
            ];
        }
    } else {
        $usersList[] = [
            'no' => null, 
            'id' => $row['user_id'], 
            'pfp' => $row['photo'],
            'username' => $row['username'],
            'role' => $role, 
            'email' => $row['email'], 
            'class' => $class, 
            'dob' => $row['dob']
        ];
    }
}

$jsonUsers = json_encode($usersList);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management and Filter</title>
    <link rel="stylesheet" href="manageUser.css">
    
</head>
<body>
    <div id="headerContainer">
        <?php include "adminHeader.php"?>
    </div>
    <div id="contentContainer"><!-- User Management Section -->
        <div class="user-management">
            <h2>User Management</h2>
            <div class="create-user-btn">
                <button onclick="window.location.href = 'createProfile.php'">Create User</button>
            </div>
            <hr>
            <br>
            <h4>Search User</h4>
            <div class="search-bar">
                <div style="display: flex; justify-content: center; align-items: center; width: 90%">
                    <input type="text" id="searchInput" placeholder="🔍 Search Username or ID">
                </div>
                <div>
                    <button onclick="searchUser()">Search</button>
                    <button onclick="toggleFilter()" class="filter-button">Filter Result</button>
                </div>
            </div>

            <div class="filter-section" id="filterSection" style="display: none;">
                <div class="filter-row">
                    <!-- Filter items remain unchanged -->
                    <div class="filter-item">
                        <label>Role:</label>
                        <input type="hidden" id="role" name="role">
                        <select id="roleOptions">
                            <option value="">-</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                        </select>
                    </div>
                    <div class="filter-item" id="gradeFilterContainer">
                        <label>Grade:</label>
                        <input type="hidden" id="grade" name="grade">
                        <select id="gradeOptions">
                            <option value="">-</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                    <div class="filter-item" id="classFilterContainer">
                        <label>Class</label>
                        <input type="hidden" id="fullClass" name="fullClass">
                        <select id="classOptions">
                            <option value="">-</option>
                        <?php
                        if (mysqli_num_rows($classList) > 0) {
                            while ($row = mysqli_fetch_array($classList)) {
                                // Example: Assuming the class name is stored in a column named 'class_name'
                                echo '<option value="'.htmlspecialchars($row['grade']).'-'.htmlspecialchars($row['class_name']).'-'.htmlspecialchars($row['year']).'">'.htmlspecialchars($row['grade']).'-'.htmlspecialchars($row['class_name']).'-'.htmlspecialchars($row['year']).
                                '</option>';
                            }
                        } else {
                            //if no classes
                            echo '<option value="">No classes available</option>';
                        }
                        ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="month">Month and Year</label>
                        <input type="month" id="month" name="month">
                    </div>
                </div>
                <button class="filter-button" onclick="filterResults()">Search Filtered</button>
            </div>
        </div>

        <!-- Results Table -->
        <div class="results-table-container">
        <table class="results-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th>DOB (Y-M-D)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="resultsTableBody">
                <!-- Results will be populated here -->
            </tbody>
        </table>
    </div>
    <form id="editRequestForm" action="editProfile.php" method="get" onsubmit="preventSubmission(event)">
        <input type="hidden" id="origin" name="origin" value="editRequest">
        <input type="hidden" id="userId" name="userId">
    </form>

<script>
    //event listeners for filtered search
    const roleFilter = document.getElementById('roleOptions');
    const gradeFilter = document.getElementById('gradeOptions');
    const classFilter = document.getElementById('classOptions');

    roleFilter.addEventListener('change', () => {
        document.getElementById('role').value = roleFilter.value;
        if (roleFilter.value == "student") {
            document.getElementById('classFilterContainer').style.display = "block";
            document.getElementById('gradeFilterContainer').style.display = "block";
        } else {
            document.getElementById('classFilterContainer').style.display = "none";
            document.getElementById('gradeFilterContainer').style.display = "none";
        }
    });

    gradeFilter.addEventListener('change', () => {
        document.getElementById('grade').value = gradeFilter.value;
        if (document.getElementById('grade').value != "") {
            classFilter.value = "";
            const event = new Event('change');
            classFilter.dispatchEvent(event);
        };
    });

    classFilter.addEventListener('change', () => {
        document.getElementById('fullClass').value = classFilter.value;
        if (document.getElementById('fullClass').value != "") {
            gradeFilter.value = "";
            const event = new Event('change');
            gradeFilter.dispatchEvent(event);
        };
    });

    
    // Function to toggle filter section visibility
    function toggleFilter() {
        const filterSection = document.getElementById('filterSection');
        filterSection.style.display = filterSection.style.display === 'none' ? 'block' : 'none';
    }

    // Function: Search for users
    function searchUser() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const filteredUsers = users.filter(user =>
            user.username.toLowerCase().includes(searchInput) ||
            user.id.toString().includes(searchInput)
        );
        displayResults(filteredUsers);
    }

    // Function: Edit a user
    function editUser(index) {
        let userId = users[index].id;
        document.getElementById("userId").value = userId;
        document.getElementById("editRequestForm").submit(); 
    }

    function preventSubmission(event) {
        event.preventDefault();
    }

    // Function: Delete a user
    function deleteUser(index) {
        if (confirm('Are you sure you want to delete this user?')) {
            let userId = users[index].id;

            fetch('deleteUser.php', { // URL of your PHP file
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ user_id: userId }) // Send userId to PHP
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("User deleted successfully");
                    users.splice(index, 1);
                    //update list
                    users = users.map((user, i) => ({ ...user, no: i + 1 }));
                    displayResults(users);
                    alert('User deleted successfully!');
                } else {
                    alert("Cannot delete user at this time.");
                }
            })
        }
    }

    //user list from php
    let users = [];
    let num = 1;
    //without time, with attempts count
    //display results with php variables
    let userList = <?php echo $jsonUsers; ?>;

    userList.forEach(user => {
        users.push({
            no: num++, 
            id: user.id,  
            pfp: user.pfp, //profile picture
            username: user.username, 
            role: user.role, 
            email: user.email, 
            class: user.class, 
            dob: user.dob
        });
    });

    // Function: Display results in the table
    function displayResults(filteredUsers) {
        const tableBody = document.getElementById('resultsTableBody');
        tableBody.innerHTML = '';

        filteredUsers.forEach((user, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${user.no}</td>
                <td>${user.id}</td>
                <td>
                    <div style="display: flex; align-items: center;">
                        <div class="profileContainer">
                            <img id="profile" class="profile${user.no}" src="" alt="Profile Picture">
                        </div>
                        <ul class="noBullets">
                            <li>${user.username}</li>
                        </ul>
                    </div>
                </td>
                <td>${user.role}</td>
                <td>${user.email}</td>
                <td>${user.class}</td>
                <td>${user.dob}</td>
                <td>
                    <div class="action-buttons">
                        <button class="edit-btn" onclick="editUser(${index})">✏️</button>
                        <button class="delete-btn" onclick="deleteUser(${index})">🗑️</button>
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
            let profileImage = document.querySelector(".profile"+user.no);
            if (user.pfp == null) {
                profileImage.src = "media/profileDefault.png";
            } else {
                profileImage.src = user.pfp;
            }
            profileImage.alt = "Profile Picture";
        });
    }

    // Initial display of users
    displayResults(users);

    function matchesFilters(user) {
        let selectedRole = document.getElementById('role').value; 
        let selectedGrade = document.getElementById('grade').value;
        let selectedClass = document.getElementById('fullClass').value;
        let selectedMY = document.getElementById("month").value;
        // Check role filter - only apply if selectedRole is not empty
        if (selectedRole && user.role !== selectedRole) {
            return false;
        }

        // Check date filter if selectedMY is not empty
        if (selectedMY !== "") {
            const [selectedYear, selectedMonth] = selectedMY.split('-');
            const [dbYear, dbMonth] = user.dob.split('-');
            if (selectedYear !== dbYear || selectedMonth !== dbMonth) {
                return false;
            }
        }

        // Check grade filter if selectedGrade is not empty
        if (selectedGrade !== "") {
            const dbGrade = user.class[0]; // Assuming class starts with grade
            if (dbGrade !== selectedGrade) {
                return false;
            }
        }

        // Check class filter if selectedClass is not empty
        if (selectedClass !== "" && user.class !== selectedClass) {
            return false;
        }

        return true; // If all conditions are met, return true
    }

    function filterResults() {
        let filterUsers = [];
        let num = 1;
        users.forEach((user, index) => {
            if (matchesFilters(user)) {
                filterUsers.push({
                    no: num++, 
                    id: user.id,  
                    pfp: user.pfp, //profile picture
                    username: user.username, 
                    role: user.role, 
                    email: user.email, 
                    class: user.class, 
                    dob: user.dob
                });
            }
        })
        displayResults(filterUsers);
    }
</script>

</body>
</html>
