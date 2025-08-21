 <?php
session_start();
include 'db_connect.php';

// ✅ Only Admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<p style='color:red;'>Access denied. Admins only.</p>";
    exit();
}

// ✅ Handle new cycle creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_cycle'])) {
    $name = $conn->real_escape_string($_POST['cycle_name']);
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO cycle (cycle_name, start_date, end_date, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $start, $end, $status);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Cycle created successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// ✅ Handle cycle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cycle'])) {
    $id = $_POST['cycle_id'];
    $name = $conn->real_escape_string($_POST['cycle_name']);
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE cycle SET cycle_name=?, start_date=?, end_date=?, status=? WHERE cycle_id=?");
    $stmt->bind_param("ssssi", $name, $start, $end, $status, $id);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Cycle updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// ✅ Fetch all cycles
$cycles = $conn->query("SELECT * FROM cycle ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Appraisal Cycles</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        form, table {
            background-color: #fff; padding: 15px;
            border-radius: 5px; box-shadow: 0 0 5px #ccc;
            margin-bottom: 30px;
        }
        label { display: block; margin-top: 10px; }
        input, select {
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
    </style>
</head>
<body>

<h2>🗓️ Create New Appraisal Cycle</h2>
<form method="POST" action="manage_cycles.php">
    <label for="cycle_name">Cycle Name:</label>
    <input type="text" name="cycle_name" required>

    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" required>

    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" required>

    <label for="status">Status:</label>
    <select name="status" required>
        <option value="Open">Open</option>
        <option value="Closed">Closed</option>
    </select>

    <input type="submit" name="create_cycle" value="✅ Create Cycle">
</form>

<h2>📋 Existing Appraisal Cycles</h2>
<?php if ($cycles->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Cycle Name</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $cycles->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="manage_cycles.php">
                    <td><input type="text" name="cycle_name" value="<?= htmlspecialchars($row['cycle_name']) ?>"></td>
                    <td><input type="date" name="start_date" value="<?= $row['start_date'] ?>"></td>
                    <td><input type="date" name="end_date" value="<?= $row['end_date'] ?>"></td>
                    <td>
                        <select name="status">
                            <option value="Open" <?= $row['status'] === 'Open' ? 'selected' : '' ?>>Open</option>
                            <option value="Closed" <?= $row['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="cycle_id" value="<?= $row['cycle_id'] ?>">
                        <input type="submit" name="update_cycle" value="✏️ Update">
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No cycles found.</p>
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
