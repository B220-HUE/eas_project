 <?php
session_start();
include 'db_connect.php';

// ✅ Only Admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<p style='color:red;'>Access denied. Admins only.</p>";
    exit();
}

// ✅ Handle new goal submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_goal'])) {
    $employee_id = $_POST['employee_id'];
    $cycle_id = $_POST['cycle_id'];
    $goal_text = $conn->real_escape_string($_POST['goal_text']);

    $stmt = $conn->prepare("INSERT INTO goals (employee_id, cycle_id, goal_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $employee_id, $cycle_id, $goal_text);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Goal assigned successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// ✅ Handle goal update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_goal'])) {
    $goal_id = $_POST['goal_id'];
    $goal_text = $conn->real_escape_string($_POST['goal_text']);

    $stmt = $conn->prepare("UPDATE goals SET goal_text=? WHERE goal_id=?");
    $stmt->bind_param("si", $goal_text, $goal_id);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Goal updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// ✅ Fetch all goals
$goals = $conn->query("
    SELECT g.goal_id, g.goal_text, u.name AS employee_name, c.cycle_name
    FROM goals g
    JOIN users u ON g.employee_id = u.user_id
    JOIN cycle c ON g.cycle_id = c.cycle_id
    ORDER BY c.start_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Goals</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        form, table {
            background-color: #fff; padding: 15px;
            border-radius: 5px; box-shadow: 0 0 5px #ccc;
            margin-bottom: 30px;
        }
        label { display: block; margin-top: 10px; }
        input, select, textarea {
            width: 100%; padding: 8px; margin-top: 5px;
        }
        input[type="submit"] {
            margin-top: 15px; background-color: #007BFF;
            color: white; border: none; border-radius: 5px;
            padding: 10px 20px; cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; vertical-align: top; }
        th { background-color: #007BFF; color: white; }
        em { color: #666; }
    </style>
</head>
<body>

<h2>🎯 Assign New Goal</h2>
<form method="POST" action="manage_goals.php">
    <label for="employee_id">👤 Employee:</label>
    <select name="employee_id" required>
        <option value="">-- Select Employee --</option>
        <?php
        $emp_result = $conn->query("SELECT user_id, name FROM users WHERE role = 'Employee'");
        while ($emp = $emp_result->fetch_assoc()) {
            echo "<option value='{$emp['user_id']}'>{$emp['name']}</option>";
        }
        ?>
    </select>

    <label for="cycle_id">🗓️ Appraisal Cycle:</label>
    <select name="cycle_id" required>
        <option value="">-- Select Cycle --</option>
        <?php
        $cycle_result = $conn->query("SELECT cycle_id, cycle_name FROM cycle ORDER BY start_date DESC");
        while ($cycle = $cycle_result->fetch_assoc()) {
            echo "<option value='{$cycle['cycle_id']}'>{$cycle['cycle_name']}</option>";
        }
        ?>
    </select>

    <label for="goal_text">📌 Goal Description:</label>
    <textarea name="goal_text" rows="3" required placeholder="Describe the goal..."></textarea>

    <input type="submit" name="create_goal" value="✅ Assign Goal">
</form>

<h2>📋 Existing Goals</h2>
<?php if ($goals->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Cycle</th>
                <th>Goal</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $goals->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="manage_goals.php">
                    <td><?= htmlspecialchars($row['employee_name']) ?></td>
                    <td><?= htmlspecialchars($row['cycle_name']) ?></td>
                    <td>
                        <textarea name="goal_text" rows="2"><?= htmlspecialchars($row['goal_text']) ?></textarea>
                    </td>
                    <td>
                        <input type="hidden" name="goal_id" value="<?= $row['goal_id'] ?>">
                        <input type="submit" name="update_goal" value="✏️ Update">
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No goals assigned yet.</p>
<?php endif; ?>

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
