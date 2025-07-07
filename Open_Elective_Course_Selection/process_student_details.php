<?php
session_start();
require_once 'db_connect.php';

// Check if section is selected and form is submitted
if (!isset($_SESSION['selected_section']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

// Validate and sanitize input
$name = $_POST['name'];
$email = $_POST['email'];
$jntuno = $_POST['jntuno'];
$section = $_SESSION['selected_section'];

// Store student details in session
$_SESSION['student_details'] = [
    'name' => $name,
    'email' => $email,
    'jntuno' => $jntuno,
    'section' => $section
];

// Redirect to course selection page
header("Location: course_selection.php");
exit();
?> 