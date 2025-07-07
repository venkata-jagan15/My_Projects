<?php
session_start();
require_once 'db_connect.php';

// Check if student details exist in session
if (!isset($_SESSION['student_details']) || !isset($_SESSION['success'])) {
    header("Location: index.php");
    exit();
}

$student_details = $_SESSION['student_details'];
$section = $student_details['section'];

// Get student's registration details including course information
$registration_query = "SELECT cr.*, c.name as course_name, c.description as course_description 
                      FROM course_registrations cr 
                      JOIN courses c ON cr.course_id = c.id 
                      JOIN students s ON cr.student_id = s.id 
                      WHERE s.jntuno = ? 
                      ORDER BY cr.registration_time DESC LIMIT 1";

$stmt = $conn->prepare($registration_query);
$stmt->bind_param("s", $student_details['jntuno']);
$stmt->execute();
$registration_result = $stmt->get_result();
$registration = $registration_result->fetch_assoc();

if (!$registration) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #28a745;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 15px 15px 0 0 !important;
        }
        .card-header h3 {
            margin: 0;
            font-size: 24px;
        }
        .card-body {
            padding: 30px;
        }
        .details-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .details-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .details-list li:last-child {
            border-bottom: none;
        }
        .alert-success {
            margin-top: 20px;
            text-align: center;
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Registration Successful!</h3>
            </div>
            <div class="card-body">
                <ul class="details-list">
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($student_details['name']); ?></li>
                    <li><strong>JNTU Number:</strong> <?php echo htmlspecialchars($student_details['jntuno']); ?></li>
                    <li><strong>Email:</strong> <?php echo htmlspecialchars($student_details['email']); ?></li>
                    <li><strong>Section:</strong> <?php echo htmlspecialchars($section); ?></li>
                    <li><strong>Course:</strong> <?php echo htmlspecialchars($registration['course_name']); ?></li>
                </ul>
                <div class="alert alert-success mt-3">
                    <?php echo $_SESSION['success']; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Clear session data
unset($_SESSION['student_details']);
unset($_SESSION['success']);
?> 