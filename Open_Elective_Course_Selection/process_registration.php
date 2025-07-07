<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $class = $_POST['class'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $jntuno = $_POST['jntuno'];

    // Store student details in session
    $_SESSION['student_details'] = [
        'class' => $class,
        'name' => $name,
        'email' => $email,
        'jntuno' => $jntuno
    ];

    // Redirect to section selection page
    header("Location: section_selection.php");
    exit();
} else {
    // If someone tries to access this page directly
    header("Location: index.php");
    exit();
}
?> 