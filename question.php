<?php
session_start();
include 'db.php';

if (!isset($_SESSION['player_name'])) {
    header("Location: start_game.php");
    exit();
}

$qn = $_SESSION['question_number'];
$lifeline = $_SESSION['lifeline_used'];

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

$options = [
    "A" => $question['option_a'],
    "B" => $question['option_b'],
    "C" => $question['option_c'],
    "D" => $question['option_d']
];

$reduced_options = $options;
if (isset($_GET['lifeline']) && !$lifeline) {
    $_SESSION['lifeline_used'] = true;
    $correct = $question['correct'];
    $wrong = array_diff(array_keys($options), [$correct]);
    shuffle($wrong);
    $remove = array_slice($wrong, 0, 2);
    foreach ($remove as $key) {
        unset($reduced_options[$key]);
    }
}

$selected_answer = $_SESSION['selected_answer'] ?? null;
$correct_answer = $_SESSION['correct_answer'] ?? null;
$next_action = $_SESSION['next_action'] ?? null;
$show_milestone_prompt = $_SESSION['show_milestone_prompt'] ?? false;

unset($_SESSION['selected_answer'], $_SESSION['correct_answer'], $_SESSION['next_action'], $_SESSION['show_milestone_prompt']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>KBC - Question <?= $qn ?> - ₹<?= number_format($question['prize_money'] ?? 0) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="question-bg">
    <div class="question-box">
        <div class="question-header">Question <?= $qn ?> - ₹<?= number_format($question['prize_money'] ?? 0) ?></div>
        <div><?= $question['question'] ?></div>
    </div>

    <?php if ($show_milestone_prompt && isset($_SESSION['last_milestone_amount']) && $_SESSION['last_milestone_amount'] > 0): ?>
        <div class="milestone-prompt">
            <h3>Congratulations! You've reached a milestone of ₹<?= number_format($_SESSION['last_milestone_amount']) ?>!</h3>
            <p>Aap abhi khelna jari rakhna chahte hain ya jeeta hua paisa leke exit karna chahte hain?</p>
            <form method="POST" action="process_answer.php">
                <button type="submit" name="exit" value="yes">Exit</button>
                <button type="submit" name="exit" value="no">Continue</button>
            </form>
        </div>
    <?php else: ?>
        <form method="POST" action="process_answer.php">
            <div class="option-grid">
                <?php foreach ($reduced_options as $key => $text): 
                    $class = "";
                    if ($selected_answer && $key == $correct_answer) $class = "correct";
                    elseif ($selected_answer && $key == $selected_answer && $selected_answer != $correct_answer) $class = "wrong";
                    elseif ($selected_answer && $key == $selected_answer) $class = "selected";
                ?>
                    <label class="option <?= $class ?>">
                        <input type="radio" name="answer" value="<?= $key ?>" required> 
                        <span class="option-label"><?= $key ?></span>
                        <span class="option-text"><?= $text ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (!$selected_answer): ?>
                <button type="submit">Lock Answer</button>
            <?php endif; ?>
        </form>

        <?php if (!$lifeline): ?>
            <br><a href="question.php?lifeline=1" class ="button-link" >Use 50-50 Lifeline</a>
        <?php else: ?>
            <p><i>50-50 Lifeline Used</i></p>
        <?php endif; ?>
    <?php endif; ?>

   <?php if ($next_action == "next_question" && !$show_milestone_prompt && isset($_SESSION['answer_locked'])): ?>
    <script>
        setTimeout(function(){ window.location.href = "question.php"; }, 100);
    </script>
<?php elseif ($next_action == "game_over"): ?>
    <script>
        setTimeout(function(){ window.location.href = "result.php"; }, 5000);
    </script>
<?php endif; ?>

<?php
unset($_SESSION['selected_answer'], $_SESSION['correct_answer'], $_SESSION['next_action'], $_SESSION['show_milestone_prompt'], $_SESSION['answer_locked']);
?>

<script src="scripts.js"></script>
</body>
</html>