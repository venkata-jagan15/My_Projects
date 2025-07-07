<?php
session_start();
require_once 'db_connect.php';

// Debug session start
error_log("Admin Login - Session started. ID: " . session_id());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    error_log("Admin Login Attempt - Username: " . $username);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please enter both username and password";
        header("Location: index.php");
        exit();
    }

    // Get admin details
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        error_log("Admin found in database. Verifying password...");
        if (password_verify($password, $admin['password'])) {
            // Login successful
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            
            error_log("Password verified. Setting session variables:");
            error_log("admin_id: " . $admin['id']);
            error_log("admin_name: " . $admin['name']);
            
            // Ensure no output before header
            if (headers_sent()) {
                error_log("Headers already sent, cannot redirect!");
                die("Redirect failed. Please try again.");
            }
            
            header("Location: admin_dashboard.php");
            exit();
        } else {
            error_log("Invalid password for admin: " . $username);
            $_SESSION['error'] = "Invalid password";
            header("Location: index.php");
            exit();
        }
    } else {
        error_log("Admin not found: " . $username);
        $_SESSION['error'] = "Admin account not found";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
} 