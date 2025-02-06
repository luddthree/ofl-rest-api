<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer (Change the path if necessary)
require 'vendor/autoload.php'; // If installed via Composer
// require 'path/to/src/PHPMailer.php'; // If installed manually

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io'; // Change to your SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'fa0f2c40f7fd3f'; // Your Mailtrap/Gmail SMTP username
    $mail->Password   = '65af621d168667'; // Your Mailtrap/Gmail SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
    $mail->Port       = 587; // SMTP port

    // Sender & Recipient
    $mail->setFrom('noreply@example.com', 'Your App');
    $mail->addAddress('luddetv@gmail.com', 'Ludvik'); // Change to the recipient

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Task Assigned';
    $mail->Body    = '<h1>New Task Assigned</h1><p>Finish project report by 2025-02-10</p>';

    // Send Email
    if ($mail->send()) {
        echo "Email Sent Successfully!";
    } else {
        echo "Failed to Send Email!";
    }
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
