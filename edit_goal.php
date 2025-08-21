<?php
session_start();
include 'db_connect.php';

// ✅ Heuristic 3: Role check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo "<p class='error'>❌ Access denied. HR only.</p>";
    exit();
}

$message = "";
$goal_id = $_GET['goal_id'] ?? null;

// ✅ Heuristic 5: Validate goal_id
if (!$goal_id || !is_numeric($goal_id)) {
    echo "<p class='error'>❌ Invalid goal ID.</p>";
    exit();
}

// ✅ Fetch existing goal
$stmt = $conn->prepare("SELECT goal_text FROM departmental_goals WHERE goal_id = ?");
$stmt->bind_param("i", $goal_id);
$stmt->execute();
$stmt->bind_result($goal_text);
$stmt->fetch();
$stmt->close();

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updated_text = trim($_POST['goal_text'] ?? '');

    if (empty($updated_text)) {
        $message = "<p class='error'>❌ Goal text cannot be empty.</p>";
    } else {
        $update_stmt = $conn->prepare("UPDATE departmental_goals SET goal_text = ? WHERE goal_id = ?");
        $update_stmt->bind_param("si", $updated_text, $goal_id);

        if ($update_stmt->execute()) {
            // ✅ Audit log entry
            $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action_type, description) VALUES (?, 'Edit Goal', ?)");
            $desc = "Updated goal ID $goal_id to: " . $updated_text;
            $log_stmt->bind_param("is", $_SESSION['user_id'], $desc);
            $log_stmt->execute();
            $log_stmt->close();

            header("Location: view_department_goals.php");
            exit();
        } else {
            $message = "<p class='error'>❌ Error updating goal: " . $update_stmt->error . "</p>";
        }

        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Departmental Goal</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        form {
            background-color: #fff; padding: 20px;
            border-radius: 5px; box-shadow: 0 0 5px #ccc;
            max-width: 600px; margin: auto;
        }
        label { font-weight: bold; display: block; margin-top: 10px; }
        textarea {
            width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px;
        }
        input[type="submit"] {
            background-color: #ffc107; color: #333; border: none; border-radius: 5px;
            padding: 10px 20px; cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #e0a800; }
        .success, .error {
            font-weight: bold; margin-bottom: 15px;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<h2>📝 Edit Departmental Goal</h2>

<?= $message ?>

<form method="POST" action="edit_goal.php?goal_id=<?= $goal_id ?>">
    <label for="goal_text">Update Goal Text:</label>
    <textarea name="goal_text" rows="4" required><?= htmlspecialchars($goal_text) ?></textarea>

    <input type="submit" value="✅ Update Goal">
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
