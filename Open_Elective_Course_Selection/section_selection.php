<?php
session_start();
require_once 'db_connect.php';

// Check if student details exist in session
if (!isset($_SESSION['student_details'])) {
    header("Location: index.php");
    exit();
}

$student_details = $_SESSION['student_details'];
$class_id = $student_details['class'];

// Get course details
$course_query = "SELECT * FROM courses WHERE id = $class_id";
$course_result = $conn->query($course_query);
$course = $course_result->fetch_assoc();

// Get section counts
$section_a_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE class_id = $class_id AND section = 'A'")->fetch_assoc()['count'];
$section_b_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE class_id = $class_id AND section = 'B'")->fetch_assoc()['count'];

$max_capacity = 70;
$section_a_available = $max_capacity - $section_a_count;
$section_b_available = $max_capacity - $section_b_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Selection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .section-card {
            transition: transform 0.2s;
        }
        .section-card:hover {
            transform: translateY(-5px);
        }
        .availability {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Course Details</h3>
            </div>
            <div class="card-body">
                <h4><?php echo $course['name']; ?></h4>
                <p class="mb-0"><?php echo $course['description']; ?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card section-card">
                    <div class="card-header">
                        <h4 class="mb-0">Section A</h4>
                    </div>
                    <div class="card-body">
                        <p class="availability">
                            Available Seats: <?php echo $section_a_available; ?>/70
                        </p>
                        <?php if ($section_a_available > 0): ?>
                            <form action="process_section.php" method="POST">
                                <input type="hidden" name="section" value="A">
                                <button type="submit" class="btn btn-primary w-100">Register for Section A</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>Section Full</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card section-card">
                    <div class="card-header">
                        <h4 class="mb-0">Section B</h4>
                    </div>
                    <div class="card-body">
                        <p class="availability">
                            Available Seats: <?php echo $section_b_available; ?>/70
                        </p>
                        <?php if ($section_b_available > 0): ?>
                            <form action="process_section.php" method="POST">
                                <input type="hidden" name="section" value="B">
                                <button type="submit" class="btn btn-primary w-100">Register for Section B</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>Section Full</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 