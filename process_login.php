<?php
session_start();
include 'db_connect.php';

// 📩 Get submitted credentials
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// 🛡️ Basic sanitization check
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "Please enter both email and password.";
    header("Location: login.php");
    exit();
}

// 🧠 Prepare query to check user by email
$stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// 🔍 Validate credentials
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // 🛠️ Replace with password hashing in production!
    if ($password === $user['password']) {
        // ✅ Store session data
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // 🟢 Add login success message
        $_SESSION['login_success'] = "Welcome, " . htmlspecialchars($user['name']) . "! You have successfully logged in.";

        // 👉 Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // 🔴 Wrong password
        $_SESSION['login_error'] = "Incorrect password. Please try again.";
        header("Location: login.php");
        exit();
    }
} else {
    // 🛑 No matching email
    $_SESSION['login_error'] = "Email not found. Please check your credentials.";
    header("Location: login.php");
    exit();
}
?>
