<?php
session_start();

$name = $_SESSION['player_name'] ?? 'Unknown';
$player_id = $_SESSION['player_id'] ?? 'N/A';
$balance = $_SESSION['balance'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>KBC Game Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="game-bg">
    <div class="result">

        <h1>🎉 Game Over 🎉</h1>
        <h2>Congrats, <?= $name ?>!</h2>
        <p>Your Player ID: <b><?= $player_id ?></b></p>
        <p>You Won: <b>₹<?= number_format($balance) ?></b></p>

        <?php if ($balance > 0): ?>
            <h3>Choose Your Prize Option:</h3>
            <ul>
                <li><a href="cheque.php"  class="button-link"> 📄 Download Cheque PDF</a></li>
                <li><button id="bankTransferBtn"> 🏦 Download Bank Transfer PDF</button></li>
            </ul>

            <div class="bank-form" id="bankForm">
                <h4>Enter Bank Details for Transfer:</h4>
                <form method="POST" action="bank_transfer.php">
                    <label>Account Holder:</label>
                    <input type="text" name="acc_holder" value="<?= $name ?>" required><br>
                    <label>Bank Name:</label>
                    <input type="text" name="bank_name" required><br>
                    <label>IFSC Code:</label>
                    <input type="text" name="ifsc" required><br>
                    <label>Account Number:</label>
                    <input type="text" name="acc_number" required><br>
                    <button type="submit">🏦 Submit and Download</button>
                </form>
                <button id="cancelBtn">Cancel</button>
            </div>
        <?php endif; ?>

        <br><a href="start_game.php" class="button-link">🔁 Play Again</a>
        </div>

    <script src="scripts.js"></script>
</body>
</html>