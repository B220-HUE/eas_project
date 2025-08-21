<?php
session_start();
include 'db_connect.php';

// 🔐 Access control: Only HR and Supervisor can use this page
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['HR', 'Supervisor'])) {
    echo "<p style='color:red;'>Access denied. You do not have permission to give feedback.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'];

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_id = $_POST['recipient_id'];
    $feedback_text = $conn->real_escape_string($_POST['feedback']);

    $stmt = $conn->prepare("INSERT INTO feedback (employee_id, feedback_text, given_by, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $recipient_id, $feedback_text, $user_id, $role);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Feedback successfully submitted to employee.</p>";
    } else {
        echo "<p style='color:red;'>❌ Error submitting feedback: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// ✅ Get list of employees
$employees = $conn->query("SELECT user_id, name FROM users WHERE role = 'Employee'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Give Feedback</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        label { font-weight: bold; }
        textarea, select, input[type="submit"] {
            width: 100%; padding: 10px; margin-top: 10px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<h2>💬 Give Feedback</h2>
<p>Welcome, <strong><?= htmlspecialchars($name) ?></strong>. Use the form below to send feedback to an employee.</p>

<form method="POST" action="feedback.php">
    <label>Select Employee:</label>
    <select name="recipient_id" required>
        <option value="">-- Choose Employee --</option>
        <?php while ($row = $employees->fetch_assoc()): ?>
            <option value="<?= $row['user_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Feedback:</label>
    <textarea name="feedback" rows="5" placeholder="Write your feedback here..." required></textarea>

    <input type="submit" value="✅ Submit Feedback">
</form>

<a href="dashboard.php"><button>🔙 Back to Dashboard</button></a>
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
