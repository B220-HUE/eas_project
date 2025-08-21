<?php
session_start();
include 'db_connect.php';

// ✅ Only Admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<p style='color:red;'>Access denied. Admins only.</p>";
    exit();
}

// ✅ Handle updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        $value = $conn->real_escape_string($value);
        $stmt = $conn->prepare("UPDATE settings SET setting_value=? WHERE setting_key=?");
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
        $stmt->close();
    }
    echo "<p style='color:green;'>✅ Settings updated successfully!</p>";
}

// ✅ Fetch all settings
$settings = $conn->query("SELECT * FROM settings ORDER BY setting_key ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>System Settings</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        form {
            background-color: #fff; padding: 20px;
            border-radius: 5px; box-shadow: 0 0 5px #ccc;
            max-width: 600px; margin: auto;
        }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], input[type="number"] {
            width: 100%; padding: 8px; margin-top: 5px;
        }
        input[type="submit"] {
            margin-top: 20px; padding: 10px 20px;
            background-color: #007BFF; color: white;
            border: none; border-radius: 5px; cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<h2>⚙️ System Settings</h2>
<form method="POST" action="system_settings.php">
    <?php while ($row = $settings->fetch_assoc()): ?>
        <label for="<?= $row['setting_key'] ?>">
            <?= ucwords(str_replace('_', ' ', $row['setting_key'])) ?>:
        </label>
        <input type="text" name="<?= $row['setting_key'] ?>" value="<?= htmlspecialchars($row['setting_value']) ?>">
    <?php endwhile; ?>

    <input type="submit" value="✅ Save Settings">
</form>

<a href="dashboard.php"><button style="margin-top:20px;">🔙 Back to Dashboard</button></a>
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
