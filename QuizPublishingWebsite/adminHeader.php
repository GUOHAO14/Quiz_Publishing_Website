<?php
$profilePic = $_SESSION['photo'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morning Quiznos</title>
    <link rel="icon" type="image/x-icon" href="sun.png">
    <link rel="stylesheet" href="adminHeader.css">
</head>

<body>
    <header>
        <!-- Burger menu button -->
        <button class="burger-menu" onclick="toggleSidebar()">☰</button>
        <a href="stu-home.html" class="logo" title="Morning Quiznos">
            <img src="media/sun.png" alt="Morning" width="40px" height="40px">
        </a>
        <a href="stu-home.html" class="morning">
            <h1 class="header">MORNING QUIZNOS<span class="admin-text">-- Administration</span></h1>
        </a>
        <a href="viewAdminProfile.php" class="profile" title="Profile">
            <div class="profileHeaderContainer">
                <img id="headerProfile" src="<?php echo $profilePic?>" alt="Profile Picture">
            </div>
        </a>
        <a href="logout.php" id="logout"><img id="logoutImg" src="media/logout.png" alt="Logout"></a>
    </header>

    <!-- Sidebar dropdown menu -->
    <div class="sidebar" id="sidebar">
        <div class="linkContainer">
            <a href="adminMngData.php" class="dashboard"><img class="sideBarImg" src="media/Home.png" alt="">Dashboard</a>
            <a href="manageUser.php" class="user"><img class="sideBarImg" src="media/Group 56.png" alt="">Users</a>
            <a href="adminMngQuiz.php" class="activities"><img class="sideBarImg" src="media/Book open.png" alt="">Activities</a>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>