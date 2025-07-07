<?php
session_start();
require_once 'db_connect.php';

// Check if student has already registered
$registration_check = "SELECT cr.*, c.name as course_name 
                      FROM course_registrations cr 
                      JOIN students s ON cr.student_id = s.id 
                      JOIN courses c ON cr.course_id = c.id 
                      WHERE s.jntuno = ? 
                      LIMIT 1";

// Get the JNTU number from session if it exists
$jntuno = isset($_SESSION['student_details']['jntuno']) ? $_SESSION['student_details']['jntuno'] : '';

$stmt = $conn->prepare($registration_check);
$stmt->bind_param("s", $jntuno);
$stmt->execute();
$registration_result = $stmt->get_result();
$existing_registration = $registration_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Allocation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .section-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .section-card:hover {
            transform: translateY(-5px);
        }
        .availability {
            font-size: 0.9em;
            color: #6c757d;
        }
        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
        .registration-info {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .container {
                margin-top: 20px;
                padding: 0 15px;
            }
            .card-header h3 {
                font-size: 1.5rem;
            }
            .card-header h4 {
                font-size: 1.2rem;
            }
            .section-card {
                margin-bottom: 15px;
            }
            .col-md-6 {
                padding: 0 10px;
            }
            .registration-info {
                padding: 15px;
            }
            .admin-link {
                bottom: 10px;
                right: 10px;
            }
            .btn {
                padding: 0.5rem;
                font-size: 0.9rem;
            }
            .modal-dialog {
                margin: 0.5rem;
            }
        }
        @media (max-width: 576px) {
            .container {
                margin-top: 10px;
            }
            .card-body {
                padding: 1rem;
            }
            .registration-info {
                padding: 10px;
            }
            .admin-link .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if ($existing_registration): ?>
            <div class="card">
                <div class="card-header text-center">
                    <h3>Registration Status</h3>
                </div>
                <div class="card-body">
                    <div class="registration-info">
                        <h4>You have already registered for:</h4>
                        <p><strong>Course:</strong> <?php echo htmlspecialchars($existing_registration['course_name']); ?></p>
                        <p><strong>Section:</strong> <?php echo htmlspecialchars($existing_registration['section']); ?></p>
                        <p><strong>Registration Time:</strong> <?php echo htmlspecialchars($existing_registration['registration_time']); ?></p>
                    </div>
                    <div class="text-center">
                        <a href="index.php" class="btn btn-primary">Return to Home</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header text-center">
                    <h3>Select Your Section</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card section-card">
                                <div class="card-header">
                                    <h4 class="mb-0">Section A</h4>
                                </div>
                                <div class="card-body">
                                    <p class="availability">
                                        Available Seats: <span id="sectionA_seats">70</span>/70
                                    </p>
                                    <form action="student_details.php" method="POST">
                                        <input type="hidden" name="section" value="A">
                                        <button type="submit" class="btn btn-primary w-100">Select Section A</button>
                                    </form>
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
                                        Available Seats: <span id="sectionB_seats">70</span>/70
                                    </p>
                                    <form action="student_details.php" method="POST">
                                        <input type="hidden" name="section" value="B">
                                        <button type="submit" class="btn btn-primary w-100">Select Section B</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="admin-link">
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#adminLoginModal">
            Admin Login
        </button>
    </div>

    <!-- Admin Login Modal -->
    <div class="modal fade" id="adminLoginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Admin Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <form action="admin_login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update seat availability
        function updateSeatAvailability() {
            fetch('get_seat_availability.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('sectionA_seats').textContent = data.sectionA;
                    document.getElementById('sectionB_seats').textContent = data.sectionB;
                });
        }

        // Update seats every 30 seconds
        updateSeatAvailability();
        setInterval(updateSeatAvailability, 30000);
    </script>
</body>
</html> 