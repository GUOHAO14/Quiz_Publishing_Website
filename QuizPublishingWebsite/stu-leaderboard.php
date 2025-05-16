<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="stu-leaderboard.css">

</head>
<body>


    <div class="leaderboard-container">
        <!-- Back Button -->
        <div class="back-button">🔙</div>

        <!-- Header -->
        <div class="header">Leaderboard</div>

        <!-- Podium -->
        <div class="podium">
            <div class="podium-step second">
                <img src="media/profile.png" alt="User Icon" class="icon">
                <div class="stats">Username 2</div>
                <div class="stats"><strong>100%</strong></div>
                <div class="stats">3m 45s</div>
                <div class="medal second">2</div>
            </div>
            <div class="podium-step first">
                <img src="media/profile.png" alt="User Icon" class="icon">
                <div class="stats">Username 1</div>
                <div class="stats"><strong>100%</strong></div>
                <div class="stats">3m 22s</div>
                <div class="medal">1</div>
            </div>
            <div class="podium-step third">
                <img src="media/profile.png" alt="User Icon" class="icon">
                <div class="stats">Username 3</div>
                <div class="stats"><strong>100%</strong></div>
                <div class="stats">3m 58s</div>
                <div class="medal third">3</div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table class="table">
                <tr>
                    <th>Rank</th>
                    <th>Username</th>
                    <th>Accuracy</th>
                    <th>Time</th>
                </tr>
                <tr class="table-row">
                    <td class="rank">4 <img src="media/profile.png" alt="User Icon" class="icon" style="width: 50px;"></td>
                    <td>Username 4</td>
                    <td>100%</td>
                    <td>6m 32s</td>
                </tr>
                <tr class="table-row">
                    <td class="rank">5 <img src="profile.png" alt="User Icon" class="icon" style="width: 50px;"></td>
                    <td>Username 5</td>
                    <td>99%</td>
                    <td>7m 59s</td>
                </tr>
                <tr class="table-row">
                    <td class="rank">6 <img src="profile.png" alt="User Icon" class="icon" style="width: 50px;"></td>
                    <td>Username 6</td>
                    <td>98%</td>
                    <td>9m 02s</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
