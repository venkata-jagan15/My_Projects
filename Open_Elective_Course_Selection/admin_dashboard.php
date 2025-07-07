<?php
session_start();
require_once 'db_connect.php';

// Debug session information
error_log("Admin Dashboard - Session ID: " . session_id());
error_log("Admin Dashboard - Session Data: " . print_r($_SESSION, true));

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    error_log("Admin Dashboard - No admin_id in session, redirecting to index.php");
    header("Location: index.php");
    exit();
}

// Get filter parameters
$section = isset($_GET['section']) ? $_GET['section'] : '';
$course = isset($_GET['course']) ? $_GET['course'] : '';

// Base query
$query = "SELECT s.name, s.email, s.jntuno, s.section, c.name as course_name, cr.registration_time 
          FROM students s 
          JOIN course_registrations cr ON s.id = cr.student_id 
          JOIN courses c ON cr.course_id = c.id 
          WHERE 1=1";

// Add filters
if ($section) {
    $query .= " AND s.section = '$section'";
}
if ($course) {
    $query .= " AND c.id = $course";
}

$query .= " ORDER BY cr.registration_time DESC";

// Get courses for filter
$courses_query = "SELECT * FROM courses";
$courses_result = $conn->query($courses_query);

// Get registrations
$registrations = $conn->query($query);

// Get section counts
$section_a_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE section = 'A'")->fetch_assoc()['count'];
$section_b_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE section = 'B'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Section Allocation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .stats-card {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
        }
        .export-btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Section Allocation System - Admin</a>
            <div class="d-flex">
                <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <a href="admin_logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">Section A</h5>
                        <p class="card-text">Total Students: <?php echo $section_a_count; ?></p>
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo ($section_a_count/35)*100; ?>%" 
                                 aria-valuenow="<?php echo $section_a_count; ?>" aria-valuemin="0" aria-valuemax="35"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="?section=A" class="btn btn-light btn-sm">View Section A</a>
                                <a href="export_to_excel.php?section=A" class="btn btn-light btn-sm export-btn">
                                    <i class="bi bi-file-excel"></i> Export
                                </a>
                            </div>
                            <form action="delete_registrations.php" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Are you sure you want to delete all registrations in Section A?');">
                                <input type="hidden" name="action" value="delete_section">
                                <input type="hidden" name="section" value="A">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Reset Section
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">Section B</h5>
                        <p class="card-text">Total Students: <?php echo $section_b_count; ?></p>
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo ($section_b_count/35)*100; ?>%" 
                                 aria-valuenow="<?php echo $section_b_count; ?>" aria-valuemin="0" aria-valuemax="35"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="?section=B" class="btn btn-light btn-sm">View Section B</a>
                                <a href="export_to_excel.php?section=B" class="btn btn-light btn-sm export-btn">
                                    <i class="bi bi-file-excel"></i> Export
                                </a>
                            </div>
                            <form action="delete_registrations.php" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Are you sure you want to delete all registrations in Section B?');">
                                <input type="hidden" name="action" value="delete_section">
                                <input type="hidden" name="section" value="B">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Reset Section
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">
                        <?php if ($section): ?>
                            Section <?php echo htmlspecialchars($section); ?> Students
                        <?php else: ?>
                            All Registered Students
                        <?php endif; ?>
                    </h5>
                    <div>
                        <?php if ($section): ?>
                            <a href="admin_dashboard.php" class="btn btn-secondary btn-sm">View All Sections</a>
                        <?php endif; ?>
                        <a href="export_to_excel.php<?php echo $section ? '?section='.urlencode($section) : ''; ?>" 
                           class="btn btn-success btn-sm">
                            <i class="bi bi-file-excel"></i> Export Current View
                        </a>
                        <form action="delete_registrations.php" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete ALL registrations? This cannot be undone!');">
                            <input type="hidden" name="action" value="delete_all">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Reset All Data
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>JNTU Number</th>
                                <th>Email</th>
                                <th>Section</th>
                                <th>Course</th>
                                <th>Registration Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($registrations->num_rows > 0): ?>
                                <?php while ($row = $registrations->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['jntuno']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['section']); ?></td>
                                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['registration_time']); ?></td>
                                        <td>
                                            <form action="delete_registrations.php" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this registration?');">
                                                <input type="hidden" name="action" value="delete_student">
                                                <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No registrations found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 