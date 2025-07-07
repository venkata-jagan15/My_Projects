<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Get the section parameter
$section = isset($_GET['section']) ? $_GET['section'] : '';

// Prepare the query based on section
$query = "SELECT s.name, s.jntuno, s.email, s.section, c.name as course_name, cr.registration_time 
          FROM students s 
          JOIN course_registrations cr ON s.id = cr.student_id 
          JOIN courses c ON cr.course_id = c.id";

if ($section !== '') {
    $query .= " WHERE s.section = ?";
}
$query .= " ORDER BY cr.registration_time DESC";

$stmt = $conn->prepare($query);
if ($section !== '') {
    $stmt->bind_param("s", $section);
}
$stmt->execute();
$result = $stmt->get_result();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="student_registrations' . ($section ? '_section_'.$section : '') . '_' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Create Excel content
echo "Name\tJNTU Number\tEmail\tSection\tCourse\tRegistration Time\n";

while ($row = $result->fetch_assoc()) {
    echo implode("\t", [
        $row['name'],
        $row['jntuno'],
        $row['email'],
        $row['section'],
        $row['course_name'],
        $row['registration_time']
    ]) . "\n";
}
exit;
?> 