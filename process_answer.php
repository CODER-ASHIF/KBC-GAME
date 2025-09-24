<?php
session_start();
include 'db.php';

if (!isset($_SESSION['player_name'])) {
    header("Location: start_game.php");
    exit();
}

$qn = $_SESSION['question_number'];
$answer = $_POST['answer'] ?? null;

if (isset($_POST['exit']) && $_POST['exit'] == 'yes') {
    $_SESSION['final_amount'] = $_SESSION['last_milestone_amount'] ?? 0;
    $_SESSION['balance'] = $_SESSION['final_amount'];
    header("Location: result.php");
    exit();
} elseif (isset($_POST['exit']) && $_POST['exit'] == 'no') {
    unset($_SESSION['next_action']); // Unset next_action to prevent auto-redirect
    header("Location: question.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->bind_param("i", $qn);
$stmt->execute();
$result = $stmt->get_result();
$question = $result->fetch_assoc();

if (!$question) {
    $_SESSION['final_amount'] = $_SESSION['last_milestone_amount'] ?? 0;
    $_SESSION['balance'] = $_SESSION['final_amount'];
    header("Location: result.php");
    exit();
}

$correct = $question['correct'];

$prize_money = [
    1 => 1000, 2 => 2000, 3 => 3000, 4 => 5000,
    5 => 10000, 6 => 20000, 7 => 40000, 8 => 80000,
    9 => 160000, 10 => 320000, 11 => 640000, 12 => 1250000,
    13 => 2500000, 14 => 5000000, 15 => 10000000, 16 => 70000000
];

$milestones = [
    5 => 10000, 10 => 320000, 15 => 10000000
];

$_SESSION['selected_answer'] = $answer;
$_SESSION['correct_answer'] = $correct;

if ($answer == $correct) {
    $_SESSION['balance'] = $prize_money[$qn] ?? 0;
    $_SESSION['answer_locked'] = true; // Add this
    
    if (isset($milestones[$qn])) {
        $_SESSION['last_milestone_amount'] = $milestones[$qn];
        $_SESSION['show_milestone_prompt'] = true;
    } else {
        $_SESSION['show_milestone_prompt'] = false;
    }
    $_SESSION['question_number']++;
    $_SESSION['next_action'] = "next_question";
} else {
    $_SESSION['final_amount'] = $_SESSION['last_milestone_amount'] ?? 0;
    $_SESSION['balance'] = $_SESSION['final_amount'];
    $_SESSION['next_action'] = "game_over";
    $_SESSION['show_milestone_prompt'] = false;
}

header("Location: question.php");
exit();
?>