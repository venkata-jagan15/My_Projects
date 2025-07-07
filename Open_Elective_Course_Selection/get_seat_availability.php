<?php
require_once 'db_connect.php';

// Get section counts
$section_a_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE section = 'A'")->fetch_assoc()['count'];
$section_b_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE section = 'B'")->fetch_assoc()['count'];

$max_capacity = 70;
$section_a_available = $max_capacity - $section_a_count;
$section_b_available = $max_capacity - $section_b_count;

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'sectionA' => $section_a_available,
    'sectionB' => $section_b_available
]);
?> 