<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendRegistrationEmail($student_email, $student_name, $jntuno, $course_name, $section) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';  // Replace with your email
        $mail->Password = 'your-app-password';  // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your-email@gmail.com', 'Course Registration System');  // Replace with your email
        $mail->addAddress($student_email, $student_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Course Registration Confirmation';
        
        $email_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .details { margin: 20px 0; }
                .footer { text-align: center; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Course Registration Confirmation</h2>
                </div>
                <div class='content'>
                    <p>Dear $student_name,</p>
                    <p>Your course registration has been successfully processed.</p>
                    <div class='details'>
                        <p><strong>JNTU Number:</strong> $jntuno</p>
                        <p><strong>Course:</strong> $course_name</p>
                        <p><strong>Section:</strong> $section</p>
                    </div>
                    <p>Please keep this email for your records.</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->Body = $email_body;
        $mail->AltBody = "Dear $student_name,\n\nYour course registration has been successfully processed.\n\nJNTU Number: $jntuno\nCourse: $course_name\nSection: $section\n\nPlease keep this email for your records.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?> 