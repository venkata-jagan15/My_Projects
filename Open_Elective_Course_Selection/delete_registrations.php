<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Check if action is set
if (!isset($_POST['action'])) {
    $_SESSION['error'] = "Invalid request";
    header("Location: admin_dashboard.php");
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    switch ($_POST['action']) {
        case 'delete_student':
            if (!isset($_POST['student_id'])) {
                throw new Exception("Student ID not provided");
            }
            
            // Delete from course_registrations first (due to foreign key)
            $stmt = $conn->prepare("DELETE FROM course_registrations WHERE student_id = ?");
            $stmt->bind_param("i", $_POST['student_id']);
            $stmt->execute();
            
            // Then delete from students
            $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
            $stmt->bind_param("i", $_POST['student_id']);
            $stmt->execute();
            
            $_SESSION['success'] = "Student registration deleted successfully";
            break;
            
        case 'delete_section':
            if (!isset($_POST['section'])) {
                throw new Exception("Section not provided");
            }
            
            // Get all student IDs from the section
            $stmt = $conn->prepare("SELECT id FROM students WHERE section = ?");
            $stmt->bind_param("s", $_POST['section']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                // Delete from course_registrations
                $stmt = $conn->prepare("DELETE FROM course_registrations WHERE student_id = ?");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
            }
            
            // Delete all students from the section
            $stmt = $conn->prepare("DELETE FROM students WHERE section = ?");
            $stmt->bind_param("s", $_POST['section']);
            $stmt->execute();
            
            $_SESSION['success'] = "All registrations for Section " . htmlspecialchars($_POST['section']) . " deleted successfully";
            break;
            
        case 'delete_all':
            // Delete all course registrations
            $conn->query("DELETE FROM course_registrations");
            
            // Delete all students
            $conn->query("DELETE FROM students");
            
            $_SESSION['success'] = "All registrations deleted successfully";
            break;
            
        default:
            throw new Exception("Invalid action");
    }
    
    // Commit transaction
    $conn->commit();
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: admin_dashboard.php");
exit();
?> 