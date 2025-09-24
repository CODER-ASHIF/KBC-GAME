<?php
include 'db.php';

$conn->query("TRUNCATE TABLE questions");
$conn->query("ALTER TABLE questions AUTO_INCREMENT = 1");

$questions = [
    ["What is the full form of DBMS?", "Data Base Management System", "Digital Base Management System", "Data Backup Management System", "Distributed Base Management System", "A", 1000],
    ["Who is considered the Father of Python?", "Guido van Rossum", "James Gosling", "Dennis Ritchie", "Bjarne Stroustrup", "A", 2000],
    ["What is the full form of HTML?", "Hyper Text Markup Language", "High Text Management Language", "Hyperlink and Text Markup Language", "Home Tool Markup Language", "A", 3000],
    ["Who is considered the Father of Java?", "Guido van Rossum", "James Gosling", "Dennis Ritchie", "Bjarne Stroustrup", "B", 5000],
    ["Which language is primarily used for Android app development?", "Python", "Java", "C++", "HTML", "B", 10000],
    ["Which Python keyword is used to define a function?", "func", "define", "def", "lambda", "C", 20000],
    ["What is the file extension for Python source files?", ".py", ".java", ".cpp", ".html", "A", 40000],
    ["Which Java keyword is used to create a subclass?", "implements", "extends", "inherits", "super", "B", 80000],
    ["What does SQL stand for in the context of DBMS?", "Structured Query Language", "Simple Query Language", "Sequential Query Language", "Standard Query Language", "A", 160000],
    ["Which Python module is used for working with regular expressions?", "regex", "re", "regexp", "pattern", "B", 320000],
    ["In Java, which keyword is used to prevent method overriding?", "static", "final", "abstract", "private", "B", 640000],
    ["Which data structure is used in Python for key-value pairs?", "List", "Tuple", "Dictionary", "Set", "C", 1250000],
    ["What is the main purpose of CSS in web development?", "Structure", "Styling", "Scripting", "Database Management", "B", 2500000],
    ["Which command is used to run a Python script from the command line?", "run python", "python script.py", "execute script.py", "py run script", "B", 5000000],
    ["In Java, which package contains the String class?", "java.util", "java.lang", "java.io", "java.net", "B", 10000000],
    ["Which database model uses tables to represent data?", "Hierarchical", "Network", "Relational", "Object-Oriented", "C", 70000000]
];

$stmt = $conn->prepare("INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct, prize_money) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($questions as $q) {
    $stmt->bind_param("ssssssi", $q[0], $q[1], $q[2], $q[3], $q[4], $q[5], $q[6]);
    $stmt->execute();
}

echo "Table cleared and 16 questions inserted successfully!";
?>