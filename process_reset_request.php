<?php
session_start();
include 'db_connect.php'; // Make sure this connects properly

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // ✅ Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['reset_feedback'] = "Invalid email format. Please try again.";
        header("Location: forgot_password.php");
        exit();
    }

    // ✅ Prepare statement for correct table: users
    $stmt = $conn->prepare("SELECT user_id, name, role FROM users WHERE email = ?");
    if (!$stmt) {
        $_SESSION['reset_feedback'] = "Server error while verifying your account.";
        header("Location: forgot_password.php");
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // ✅ Check existence
    if ($stmt->num_rows === 0) {
        $_SESSION['reset_feedback'] = "We couldn’t find that email in our system.";
        header("Location: forgot_password.php");
        exit();
    }

    // ✅ Get user details
    $stmt->bind_result($user_id, $name, $role);
    $stmt->fetch();

    // ✅ Log request
    $logStmt = $conn->prepare("INSERT INTO password_resets (email) VALUES (?)");
    if ($logStmt) {
        $logStmt->bind_param("s", $email);
        $logStmt->execute();
    }

    // ✅ Notify HR
    $to = "hr@masenohospital.or.ke"; // Change to real HR inbox
    $subject = "Password Reset Request - EAS";
    $message = "Password reset requested:\n"
             . "Name: $name\nRole: $role\nEmail: $email\n"
             . "Time: " . date('Y-m-d H:i:s') . "\n\nPlease verify and assist.";
    $headers = "From: eas_system@maseno.or.ke";

    @mail($to, $subject, $message, $headers); // Suppress warnings if mail fails

    // ✅ Notify user
    $_SESSION['reset_feedback'] = "Your request has been submitted. HR will follow up shortly.";
    header("Location: forgot_password.php");
    exit();
}
?>
