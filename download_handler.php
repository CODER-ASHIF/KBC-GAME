<?php
session_start();
require('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_id']) && isset($_POST['download_type'])) {
    $player_id = $_POST['player_id'];
    $download_type = $_POST['download_type'];

    $stmt = $conn->prepare("SELECT * FROM players WHERE player_id = ?");
    $stmt->bind_param("s", $player_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $player = $result->fetch_assoc();

        // Cheque download
        if ($download_type === 'cheque') {
            if (!empty($player['cheque_file']) && file_exists($player['cheque_file'])) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($player['cheque_file']) . '"');
                readfile($player['cheque_file']);
                exit();
            } else {
                $_SESSION['error'] = "Cheque file not found for Player ID: $player_id. Please generate it first!";
            }
        }

        // Bank transfer download
        elseif ($download_type === 'bank_transfer') {
            if (!empty($player['bank_file']) && file_exists($player['bank_file'])) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($player['bank_file']) . '"');
                readfile($player['bank_file']);
                exit();
            } else {
                $_SESSION['error'] = "Bank transfer file not found for Player ID: $player_id. Please generate it first!";
            }
        }
    } else {
        $_SESSION['error'] = "Invalid Player ID: $player_id!";
    }
} else {
    $_SESSION['error'] = "Invalid request! Please use the form to download.";
}

header("Location: index.php");
exit();
?>