<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morning Quiznos</title>
    <link rel="icon" type="image/x-icon" href="media/sun.png">
    <link rel="stylesheet" href="header.css">
</head>

<body>
    <header>
        <a href="teachhome.php" class="logo" title="Morning Quiznos"><img src="media/sun.png" alt="Morning" width="40px" height="40px"></a>
        <a href="teachhome.php" class="morning"><h1 class="header">MORNING QUIZNOS</h1></a>
        <nav class="header">
            <a href="create.php" class="header">Create Quiz</a>
        </nav>
        <a href="viewTeacherProfile.php" class="profile" title="Profile">
            <div class="profileHeaderContainer">
                <img id="headerProfile" src="<?php echo $_SESSION['photo']?>" alt="Profile Picture">
            </div>
        </a>
    </header>
</body>
</html>