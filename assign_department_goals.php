<?php
session_start();
include 'db_connect.php';

// ✅ Only HR can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo "<p class='error'>❌ Access denied. HR only.</p>";
    exit();
}

$message = ""; // ✅ Initialize message variable

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dept_id = $_POST['dept_id'] ?? '';
    $cycle_id = $_POST['cycle_id'] ?? '';
    $goal_text = trim($_POST['goal_text'] ?? '');
    $assigned_by = $_SESSION['user_id'];

    // ✅ Validate required fields
    if (empty($dept_id) || empty($cycle_id) || empty($goal_text)) {
        $message = "<p class='error'>❌ Please fill all required fields.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO departmental_goals (dept_id, cycle_id, goal_text, assigned_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $dept_id, $cycle_id, $goal_text, $assigned_by);

        if ($stmt->execute()) {
            $message = "<p class='success'>✅ Departmental goal assigned successfully!</p>";
        } else {
            $message = "<p class='error'>❌ Error assigning goal: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Departmental Goals</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        form {
            background-color: #fff; padding: 20px;
            border-radius: 5px; box-shadow: 0 0 5px #ccc;
            max-width: 600px; margin: auto;
        }
        label { font-weight: bold; display: block; margin-top: 10px; }
        select, textarea {
            width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px;
        }
        input[type="submit"] {
            background-color: #28a745; color: white; border: none; border-radius: 5px;
            padding: 10px 20px; cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #218838; }
        .success {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<h2>🏢 Assign Departmental Goal</h2>

<?= $message ?> <!-- ✅ Display success or error message -->

<form method="POST" action="assign_department_goals.php">
    <label for="dept_id">Select Department:</label>
    <select name="dept_id" required>
        <option value="">-- Choose Department --</option>
        <?php
        $departments = $conn->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name ASC");
        while ($row = $departments->fetch_assoc()) {
            echo "<option value='{$row['dept_id']}'>{$row['dept_name']}</option>";
        }
        ?>
    </select>

    <label for="cycle_id">Select Appraisal Cycle:</label>
    <select name="cycle_id" required>
        <option value="">-- Choose Cycle --</option>
        <?php
        $cycles = $conn->query("SELECT cycle_id, cycle_name FROM cycle ORDER BY start_date DESC");
        while ($row = $cycles->fetch_assoc()) {
            echo "<option value='{$row['cycle_id']}'>{$row['cycle_name']}</option>";
        }
        ?>
    </select>

    <label for="goal_text">Write Goal:</label>
    <textarea name="goal_text" rows="4" required placeholder="e.g. Improve department efficiency by 15%..."></textarea>

    <input type="submit" value="✅ Assign Goal">
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
