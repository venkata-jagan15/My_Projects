<?php
require_once 'db_connect.php';

// Admin credentials
$username = 'it_gmrit';
$password = 'it@cr1';
$name = 'IT Administrator';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin exists
$check_query = "SELECT id FROM admin WHERE username = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing admin
    $update_query = "UPDATE admin SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ss", $hashed_password, $username);
    if ($stmt->execute()) {
        echo "Admin password updated successfully!<br>";
        echo "Username: " . htmlspecialchars($username) . "<br>";
        echo "Password: " . htmlspecialchars($password);
    } else {
        echo "Error updating admin password: " . $conn->error;
    }
} else {
    // Insert new admin
    $insert_query = "INSERT INTO admin (username, password, name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sss", $username, $hashed_password, $name);
    if ($stmt->execute()) {
        echo "Admin account created successfully!<br>";
        echo "Username: " . htmlspecialchars($username) . "<br>";
        echo "Password: " . htmlspecialchars($password);
    } else {
        echo "Error creating admin account: " . $conn->error;
    }
}
?> 