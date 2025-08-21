 <?php
session_start();
include 'db_connect.php';

// ✅ Heuristic 3 & 4: Role check with clear error message
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo "<p class='error'>❌ Access denied. HR only.</p>";
    exit();
}

$message = "";
$filter_dept = $_POST['dept_id'] ?? '';
$filter_cycle = $_POST['cycle_id'] ?? '';

// ✅ Fetch departments and cycles for dropdowns
$departments = $conn->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name ASC");
$cycles = $conn->query("SELECT cycle_id, cycle_name FROM cycle ORDER BY start_date DESC");

// ✅ Build query to fetch goals
$sql = "
    SELECT dg.goal_id, dg.goal_text, dg.date_assigned, d.dept_name, c.cycle_name, u.name AS assigned_by
    FROM departmental_goals dg
    JOIN departments d ON dg.dept_id = d.dept_id
    JOIN cycle c ON dg.cycle_id = c.cycle_id
    JOIN users u ON dg.assigned_by = u.user_id
    WHERE 1=1
";

if (!empty($filter_dept)) {
    $sql .= " AND dg.dept_id = " . intval($filter_dept);
}
if (!empty($filter_cycle)) {
    $sql .= " AND dg.cycle_id = " . intval($filter_cycle);
}

$sql .= " ORDER BY dg.date_assigned DESC";

$goals = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Departmental Goals</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        form {
            background-color: #fff; padding: 20px;
            border-radius: 5px; box-shadow: 0 0 5px #ccc;
            margin-bottom: 20px; max-width: 600px;
        }
        label { font-weight: bold; display: block; margin-top: 10px; }
        select {
            width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px;
        }
        input[type="submit"] {
            background-color: #007BFF; color: white; border: none; border-radius: 5px;
            padding: 10px 20px; cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #0056b3; }
        .goal-block {
            background-color: #fff; padding: 15px;
            border-radius: 5px; box-shadow: 0 0 3px #ccc;
            margin-bottom: 15px;
        }
        .goal-block h4 { margin: 0 0 5px; }
        .goal-block p { margin: 5px 0; }
        .btn-group {
            margin-top: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 8px 12px; border: none; border-radius: 5px;
            cursor: pointer; margin-right: 10px;
        }
        .edit-btn {
            background-color: #ffc107; color: #333;
        }
        .edit-btn:hover { background-color: #e0a800; }
        .delete-btn {
            background-color: #dc3545; color: white;
        }
        .delete-btn:hover { background-color: #c82333; }
        .success, .error {
            font-weight: bold; margin-bottom: 15px;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<h2>📋 View Departmental Goals</h2>

<?= $message ?>

<form method="POST" action="view_department_goals.php">
    <label for="dept_id">Filter by Department:</label>
    <select name="dept_id">
        <option value="">-- All Departments --</option>
        <?php while ($row = $departments->fetch_assoc()) {
            $selected = ($row['dept_id'] == $filter_dept) ? 'selected' : '';
            echo "<option value='{$row['dept_id']}' $selected>{$row['dept_name']}</option>";
        } ?>
    </select>

    <label for="cycle_id">Filter by Appraisal Cycle:</label>
    <select name="cycle_id">
        <option value="">-- All Cycles --</option>
        <?php while ($row = $cycles->fetch_assoc()) {
            $selected = ($row['cycle_id'] == $filter_cycle) ? 'selected' : '';
            echo "<option value='{$row['cycle_id']}' $selected>{$row['cycle_name']}</option>";
        } ?>
    </select>

    <input type="submit" value="🔍 View Goals">
</form>

<?php
if ($goals->num_rows > 0) {
    while ($goal = $goals->fetch_assoc()) {
        echo "<div class='goal-block'>";
        echo "<h4>📌 {$goal['goal_text']}</h4>";
        echo "<p><strong>Department:</strong> {$goal['dept_name']}</p>";
        echo "<p><strong>Cycle:</strong> {$goal['cycle_name']}</p>";
        echo "<p><strong>Assigned by:</strong> {$goal['assigned_by']}</p>";
        echo "<p><strong>Date Assigned:</strong> {$goal['date_assigned']}</p>";

        // ✅ Edit and Delete buttons
        echo "<div class='btn-group'>";
        echo "<a href='edit_goal.php?goal_id={$goal['goal_id']}'><button class='edit-btn'>📝 Edit</button></a>";
        echo "<form method='POST' action='delete_goal.php' style='display:inline;' onsubmit='return confirmDelete();'>";
        echo "<input type='hidden' name='goal_id' value='{$goal['goal_id']}'>";
        echo "<button type='submit' class='delete-btn'>🗑️ Delete</button>";
        echo "</form>";
        echo "</div>";

        echo "</div>";
    }
} else {
    echo "<p>No goals found for the selected filters.</p>";
}
?>

<script>
function confirmDelete() {
    return confirm("⚠️ Are you sure you want to delete this goal? This action cannot be undone.");
}
</script>
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
