<?php
session_start();
require 'db_connect.php';

// ✅ Restrict access to HR only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: access_denied.php");
    exit();
}

// ✅ Handle form submission
$success = $error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employee_id = intval($_POST['employee_id']);
    $cycle_id = intval($_POST['cycle_id']);
    $goal_text = trim($_POST['goal_text']);
    $assigned_by = $_SESSION['user_id'];

    if ($employee_id && $cycle_id && $goal_text !== '') {
        $stmt = $conn->prepare("INSERT INTO goals (employee_id, cycle_id, goal_text, assigned_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $employee_id, $cycle_id, $goal_text, $assigned_by);

        if ($stmt->execute()) {
            header("Location: assign_goals.php?success=1");
            exit();
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "❌ All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assign Goals</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">

  <!-- 🔙 Back Button -->
  <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary mb-4">
    <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
  </a>

  <h2 class="mb-4">🎯 Assign Goal to Employee</h2>

  <!-- ✅ Success or ❌ Error Message -->
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">✅ Goal assigned successfully!</div>
  <?php elseif (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- 📝 Goal Assignment Form -->
  <form method="POST" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3">
      <label for="employee_id" class="form-label">Select Employee:</label>
      <select name="employee_id" class="form-select" required>
        <option value="">-- Choose Employee --</option>
        <?php
        $employees = $conn->query("SELECT employee_id, name FROM employees ORDER BY name ASC");
        while ($row = $employees->fetch_assoc()) {
            echo "<option value='{$row['employee_id']}'>{$row['name']}</option>";
        }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="cycle_id" class="form-label">Select Appraisal Cycle:</label>
      <select name="cycle_id" class="form-select" required>
        <option value="">-- Choose Cycle --</option>
        <?php
        $cycles = $conn->query("SELECT cycle_id, cycle_name FROM cycle ORDER BY start_date DESC");
        while ($row = $cycles->fetch_assoc()) {
            echo "<option value='{$row['cycle_id']}'>{$row['cycle_name']}</option>";
        }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="goal_text" class="form-label">Write Goal:</label>
      <textarea name="goal_text" class="form-control" rows="4" required placeholder="e.g. Improve customer satisfaction by 20%..."></textarea>
    </div>

    <button type="submit" class="btn btn-success">✅ Assign Goal</button>
  </form>

  <!-- 🧠 Optional: Goal History Preview (future enhancement)
  <div class="mt-5">
    <h5>📋 Previously Assigned Goals</h5>
    <ul class="list-group">
      <?php
      // $history = $conn->query("SELECT g.goal_text, u.name, c.cycle_name FROM goals g
      //   JOIN employees u ON g.employee_id = u.employee_id
      //   JOIN cycle c ON g.cycle_id = c.cycle_id
      //   WHERE g.assigned_by = {$_SESSION['user_id']} ORDER BY g.assigned_at DESC LIMIT 5");
      // while ($row = $history->fetch_assoc()) {
      //   echo "<li class='list-group-item'><strong>{$row['name']}</strong> ({$row['cycle_name']}): {$row['goal_text']}</li>";
      // }
      ?>
    </ul>
  </div>
  -->

</div>
</body>
</html>
<?php
session_start();
require 'db_connect.php';

// ✅ Restrict access to HR only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: access_denied.php");
    exit();
}

// ✅ Handle form submission
$success = $error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employee_id = intval($_POST['employee_id']);
    $cycle_id = intval($_POST['cycle_id']);
    $goal_text = trim($_POST['goal_text']);
    $assigned_by = $_SESSION['user_id'];

    if ($employee_id && $cycle_id && $goal_text !== '') {
        $stmt = $conn->prepare("INSERT INTO goals (employee_id, cycle_id, goal_text, assigned_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $employee_id, $cycle_id, $goal_text, $assigned_by);

        if ($stmt->execute()) {
            header("Location: assign_goals.php?success=1");
            exit();
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "❌ All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assign Goals</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">

  <!-- 🔙 Back Button -->
  <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary mb-4">
    <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
  </a>

  <h2 class="mb-4">🎯 Assign Goal to Employee</h2>

  <!-- ✅ Success or ❌ Error Message -->
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">✅ Goal assigned successfully!</div>
  <?php elseif (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- 📝 Goal Assignment Form -->
  <form method="POST" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3">
      <label for="employee_id" class="form-label">Select Employee:</label>
      <select name="employee_id" class="form-select" required>
        <option value="">-- Choose Employee --</option>
        <?php
        $employees = $conn->query("SELECT employee_id, name FROM employees ORDER BY name ASC");
        while ($row = $employees->fetch_assoc()) {
            echo "<option value='{$row['employee_id']}'>{$row['name']}</option>";
        }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="cycle_id" class="form-label">Select Appraisal Cycle:</label>
      <select name="cycle_id" class="form-select" required>
        <option value="">-- Choose Cycle --</option>
        <?php
        $cycles = $conn->query("SELECT cycle_id, cycle_name FROM cycle ORDER BY start_date DESC");
        while ($row = $cycles->fetch_assoc()) {
            echo "<option value='{$row['cycle_id']}'>{$row['cycle_name']}</option>";
        }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="goal_text" class="form-label">Write Goal:</label>
      <textarea name="goal_text" class="form-control" rows="4" required placeholder="e.g. Improve customer satisfaction by 20%..."></textarea>
    </div>

    <button type="submit" class="btn btn-success">✅ Assign Goal</button>
  </form>

  <!-- 🧠 Optional: Goal History Preview (future enhancement)
  <div class="mt-5">
    <h5>📋 Previously Assigned Goals</h5>
    <ul class="list-group">
      <?php
      // $history = $conn->query("SELECT g.goal_text, u.name, c.cycle_name FROM goals g
      //   JOIN employees u ON g.employee_id = u.employee_id
      //   JOIN cycle c ON g.cycle_id = c.cycle_id
      //   WHERE g.assigned_by = {$_SESSION['user_id']} ORDER BY g.assigned_at DESC LIMIT 5");
      // while ($row = $history->fetch_assoc()) {
      //   echo "<li class='list-group-item'><strong>{$row['name']}</strong> ({$row['cycle_name']}): {$row['goal_text']}</li>";
      // }
      ?>
    </ul>
  </div>
  -->

</div>
</body>
</html>
