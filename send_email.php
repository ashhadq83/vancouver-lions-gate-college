<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        echo "All fields are required!";
        exit;
    }

    // Sanitize email and other inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address!";
        exit;
    }

    // Optional: Sanitize other fields, like phone and name if necessary
    // Note: For example purposes, we're just using basic sanitization
    $name = htmlspecialchars($name);
    $message = htmlspecialchars($message);
    $phone = htmlspecialchars($phone);
    $subject = htmlspecialchars($subject);

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.titan.email'; // Use correct SMTP host for Titan
        $mail->SMTPAuth = true;
        
        // Use environment variables for security
        $mail->Username = getenv('SMTP_USERNAME'); // E.g., 'vancouverlionsgatecollege@yahoo.com'
        $mail->Password = getenv('SMTP_PASSWORD'); // Get from environment variable
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Email content
        $mail->setFrom('vancouverlionsgatecollege@yahoo.com', 'Vancouver Lions Gate College');
        $mail->addAddress('vancouverlionsgatecollege@yahoo.com');  // Your email address

        // Optional: Add the sender's email as a reply-to
        $mail->addReplyTo($email, $name);

        $mail->Subject = "New Message from $name - $subject";
        $mail->Body = "
            Name: $name\n
            Email: $email\n
            Phone: $phone\n
            Subject: $subject\n
            Message:\n$message
        ";

        // Send email
        $mail->send();
        
        // Redirect to thank you page
        header("Location: thank_you.html");
        exit;

    } catch (Exception $e) {
        // Log error to a file or database (instead of displaying it)
        error_log("Mailer Error: {$mail->ErrorInfo}");
        
        // Display user-friendly error message
        echo "There was an error sending your message. Please try again later.";
    }
} else {
    echo "Invalid request!";
}
?>
