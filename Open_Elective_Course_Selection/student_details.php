<?php
session_start();
require_once 'db_connect.php';

// Check if section is selected
if (!isset($_POST['section'])) {
    header("Location: index.php");
    exit();
}

$section = $_POST['section'];

// Check if section is available
$section_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE section = '$section'")->fetch_assoc()['count'];
if ($section_count >= 70) {
    $_SESSION['error'] = "Selected section is full. Please choose another section.";
    header("Location: index.php");
    exit();
}

// Store section in session
$_SESSION['selected_section'] = $section;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details - Section Allocation System</title>
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
        @media (max-width: 768px) {
            .container {
                margin-top: 20px;
                padding: 0 15px;
                width: 100%;
            }
            .card-header h3 {
                font-size: 1.5rem;
            }
            .form-label {
                font-size: 0.9rem;
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
            .form-control {
                font-size: 0.9rem;
                padding: 0.4rem 0.8rem;
            }
            .btn {
                padding: 0.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3>Student Details - Section <?php echo $section; ?></h3>
            </div>
            <div class="card-body">
                <form action="process_student_details.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="jntuno" class="form-label">JNTU Number</label>
                        <input type="text" class="form-control" id="jntuno" name="jntuno" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Proceed to Course Selection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 