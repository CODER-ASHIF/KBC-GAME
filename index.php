<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>KBC Game Menu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="game-bg">

    <h1> Welcome to KBC Game </h1>
    <ul>
        <li><a href="start_game.php" class="button-link">▶️ Start New Game</a></li>
    </ul>

    <div class="download-form">
        <h3>Re-Download Your Documents</h3>
        <form method="POST" action="download_handler.php">
            <label>Enter Your Player ID:</label>
            <input type="text" name="player_id" required><br>
            <button type="submit" name="download_type" value="cheque">📄 Download Cheque</button>
            <button type="submit" name="download_type" value="bank_transfer">🏦 Download Bank Transfer</button>
        </form>
    </div>
</body>
</html>