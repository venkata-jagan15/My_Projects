<?php
session_start();
require_once 'db_connect.php';

// Check if student details exist in session
if (!isset($_SESSION['student_details']) || !isset($_POST['course_id'])) {
    header("Location: index.php");
    exit();
}

$student_details = $_SESSION['student_details'];
$course_id = $_POST['course_id'];
$section = $student_details['section'];

// Check if student has already registered for a course
$check_registrations = "SELECT COUNT(*) as count FROM course_registrations cr 
                       JOIN students s ON cr.student_id = s.id 
                       WHERE s.jntuno = ?";
$stmt = $conn->prepare($check_registrations);
$stmt->bind_param("s", $student_details['jntuno']);
$stmt->execute();
$result = $stmt->get_result();
$registration_count = $result->fetch_assoc()['count'];

if ($registration_count >= 1) {
    $_SESSION['error'] = "You have already registered for a course. You cannot register for more courses.";
    header("Location: index.php");
    exit();
}

// Check if course is already full for this section
$check_course_limit = "SELECT COUNT(*) as count FROM course_registrations 
                      WHERE course_id = ? AND section = ?";
$stmt = $conn->prepare($check_course_limit);
$stmt->bind_param("is", $course_id, $section);
$stmt->execute();
$result = $stmt->get_result();
$course_count = $result->fetch_assoc()['count'];

if ($course_count >= 35) {
    $_SESSION['error'] = "This course is already full for your section.";
    header("Location: course_selection.php");
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert student record if not exists
    $check_student = "SELECT id FROM students WHERE jntuno = ?";
    $stmt = $conn->prepare($check_student);
    $stmt->bind_param("s", $student_details['jntuno']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $insert_student = "INSERT INTO students (name, email, jntuno, section, registration_time) 
                          VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insert_student);
        $stmt->bind_param("ssss", 
            $student_details['name'],
            $student_details['email'],
            $student_details['jntuno'],
            $section
        );
        $stmt->execute();
        $student_id = $conn->insert_id;
    } else {
        $student_id = $result->fetch_assoc()['id'];
    }

    // Insert course registration
    $insert_registration = "INSERT INTO course_registrations 
                          (student_id, course_id, section, registration_time) 
                          VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_registration);
    $stmt->bind_param("iis", $student_id, $course_id, $section);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    $_SESSION['success'] = "Course registration successful!";
    header("Location: confirmation.php");
    exit();
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error'] = "Registration failed. Please try again.";
    header("Location: course_selection.php");
    exit();
}
?> 