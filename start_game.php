<?php
session_start();
function generatePlayerID() {
    return "PLR" . rand(1000, 9999);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $_SESSION['player_name'] = ucfirst($name);
        $_SESSION['player_id'] = generatePlayerID();
        $_SESSION['balance'] = 0;
        $_SESSION['question_number'] = 1;
        $_SESSION['lifeline_used'] = false;
        header("Location: question.php");
        exit();
    } else {
        $error = "Please enter your name.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Start Game - KBC</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="game-bg">
    <div class="start-game">
         <h2> Start New KBC Game</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
         <form method="POST">
                <label>Your Name:</label>
                <input type="text" name="name" required>
                <br><br>
                <button type="submit">Start Game</button>
         </form>
            <br><a href="index.php" class ="button-link">⬅️ Back to Menu</a>
   </div>
</body>
</html>