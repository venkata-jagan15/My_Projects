<?php
session_start();
require_once 'db_connect.php';

// Check if student details exist in session
if (!isset($_SESSION['student_details'])) {
    header("Location: index.php");
    exit();
}

$student_details = $_SESSION['student_details'];
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

// Get available courses with their current registration counts
$courses_query = "SELECT c.*, 
                 (SELECT COUNT(*) FROM course_registrations cr2 
                  WHERE cr2.course_id = c.id AND cr2.section = ?) as current_count
                 FROM courses c";
$stmt = $conn->prepare($courses_query);
$stmt->bind_param("s", $section);
$stmt->execute();
$courses_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Selection - Section Allocation System</title>
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
        .course-card {
            transition: transform 0.2s;
        }
        .course-card:hover {
            transform: translateY(-5px);
        }
        .availability {
            font-size: 0.9em;
            color: #6c757d;
        }
        .full-course {
            opacity: 0.7;
            pointer-events: none;
            background-color: #f8f9fa;
        }
        .full-course .card-body {
            background-color: #f8f9fa;
        }
        .full-course .btn {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        @media (max-width: 768px) {
            .container {
                margin-top: 20px;
                padding: 0 15px;
                width: 100%;
            }
            .card-header h3 {
                font-size: 1.5rem;
            }
            .card-header p {
                font-size: 1rem;
            }
            .course-card {
                margin-bottom: 15px;
            }
            .card-title {
                font-size: 1.2rem;
            }
            .card-text {
                font-size: 0.9rem;
            }
            .availability {
                font-size: 0.8em;
            }
            .btn {
                padding: 0.5rem;
                font-size: 0.9rem;
            }
        }
        @media (max-width: 576px) {
            .container {
                margin-top: 10px;
            }
            .card-body {
                padding: 1rem;
            }
            .col-md-6 {
                padding: 0 10px;
            }
            .course-card {
                margin-bottom: 10px;
            }
            .btn {
                padding: 0.4rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3>Course Selection</h3>
                <p class="mb-0">Section <?php echo htmlspecialchars($section); ?></p>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php while ($course = $courses_result->fetch_assoc()): ?>
                        <?php 
                        $is_full = $course['current_count'] >= 35;
                        $remaining_seats = 35 - $course['current_count'];
                        ?>
                        <div class="col-md-6 mb-4">
                            <div class="card course-card <?php echo $is_full ? 'full-course' : ''; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($course['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
                                    <p class="availability">
                                        Available Seats: <?php echo $remaining_seats; ?>/35
                                    </p>
                                    <?php if ($is_full): ?>
                                        <button class="btn btn-secondary w-100" disabled>Course Full</button>
                                    <?php else: ?>
                                        <form action="process_course_selection.php" method="POST">
                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                            <button type="submit" class="btn btn-primary w-100">Select Course</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 