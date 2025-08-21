 <?php
session_start();
$conn = new mysqli("localhost", "root", "", "eas_db");

// Only allow Admin or HR
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'HR'])) {
    echo "Access denied.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<p>User registered successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register User</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 400px; margin: auto; }
        input, select { width: 100%; padding: 8px; margin: 10px 0; }
        input[type="submit"] { background-color: #007BFF; color: white; border: none; cursor: pointer; }
        input[type="submit"]:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<h2>Register New User</h2>

<form method="POST">
    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Role:</label>
    <select name="role" required>
        <option value="">-- Select Role --</option>
        <option value="Admin">Admin</option>
        <option value="HR">HR</option>
        <option value="Supervisor">Supervisor</option>
        <option value="Employee">Employee</option>
    </select>

    <input type="submit" value="Register">
</form>
<style>
  .back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 1000;
  }
</style>

<div class="back-button">
  <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left-circle"></i> Back
  </a>
</div>

</body>
</html>
