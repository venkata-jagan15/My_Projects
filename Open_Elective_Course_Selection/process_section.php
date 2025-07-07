<?php
session_start();
require_once 'db_connect.php';

// Check if student details exist in session
if (!isset($_SESSION['student_details'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_details = $_SESSION['student_details'];
    $section = $_POST['section'];
    $class_id = $student_details['class'];

    // Check if section is available
    $section_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE class_id = $class_id AND section = '$section'")->fetch_assoc()['count'];
    
    if ($section_count >= 70) {
        // Section is full
        $_SESSION['error'] = "Selected section is full. Please choose another section.";
        header("Location: section_selection.php");
        exit();
    }

    // Check if student is already registered
    $existing_registration = $conn->query("SELECT * FROM students WHERE jntuno = '{$student_details['jntuno']}' AND class_id = $class_id");
    
    if ($existing_registration->num_rows > 0) {
        $_SESSION['error'] = "You are already registered for this course.";
        header("Location: section_selection.php");
        exit();
    }

    // Insert student registration
    $stmt = $conn->prepare("INSERT INTO students (class_id, name, email, jntuno, section, registration_time) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issss", $class_id, $student_details['name'], $student_details['email'], $student_details['jntuno'], $section);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Successfully registered for Section $section!";
        header("Location: confirmation.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: section_selection.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?> 