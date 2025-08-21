<?php
session_start();
include 'db_connect.php';

// ✅ Only HR can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo "<p class='error'>❌ Access denied. HR only.</p>";
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_cycle'])) {
    $cycle_name = !empty($_POST['new_cycle_name']) ? $_POST['new_cycle_name'] : $_POST['cycle_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (!empty($cycle_name)) {
        $stmt = $conn->prepare("INSERT INTO cycle (cycle_name, start_date, end_date) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $cycle_name, $start_date, $end_date);

        if ($stmt->execute()) {
            $message = "<p class='success'>✅ Cycle created successfully!</p>";
        } else {
            $message = "<p class='error'>❌ Error creating cycle: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        $message = "<p class='error'>❌ Please select or enter a cycle name.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appraisal Cycles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-image: url('assets/cycles_background.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(233, 239, 245, 0.85); /* Soft overlay */
            z-index: -1;
        }

        h2, h3 {
            color: #333;
        }

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

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px #ccc;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input[type="text"], input[type="date"], select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            margin-top: 10px;
        }

        .delete-btn:hover {
            background-color: darkred;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #fff;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 3px #ccc;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        .btn-outline-secondary {
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            border: 1px solid #6c757d;
            color: #6c757d;
            background-color: transparent;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>

<h2>📅 Create Appraisal Cycle</h2>

<?= $message ?>

<form method="POST" action="appraisal_cycles.php">
    <input type="hidden" name="create_cycle" value="1">

    <label for="cycle_name">Select Existing Cycle Name:</label>
    <select name="cycle_name">
        <option value="">-- Choose Cycle --</option>
        <?php
        $existing_cycles = $conn->query("SELECT DISTINCT cycle_name FROM cycle ORDER BY cycle_name ASC");
        while ($row = $existing_cycles->fetch_assoc()) {
            echo "<option value='{$row['cycle_name']}'>{$row['cycle_name']}</option>";
        }
        ?>
    </select>

    <label for="new_cycle_name">Or Add New Cycle Name:</label>
    <input type="text" name="new_cycle_name" placeholder="e.g. Mid-Year Review 2025">

    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" required>

    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" required>

    <input type="submit" value="✅ Create Cycle">
</form>

<h3>📋 Existing Appraisal Cycles</h3>
<?php
$result = $conn->query("SELECT * FROM cycle ORDER BY start_date DESC");
if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>";
        echo "<strong>{$row['cycle_name']}</strong> ({$row['start_date']} to {$row['end_date']})<br>";
        echo "Status: <strong>{$row['status']}</strong>";

        echo '<form method="POST" action="update_cycle_status.php">';
        echo '<input type="hidden" name="cycle_id" value="' . $row['cycle_id'] . '">';
        echo '<select name="status">';
        echo '<option value="Open"' . ($row['status'] == 'Open' ? ' selected' : '') . '>Open</option>';
        echo '<option value="Closed"' . ($row['status'] == 'Closed' ? ' selected' : '') . '>Closed</option>';
        echo '</select>';
        echo '<input type="submit" value="Update">';
        echo '</form>';

        echo '<form method="POST" action="delete_cycle.php" onsubmit="return confirmDelete();">';
        echo '<input type="hidden" name="cycle_id" value="' . $row['cycle_id'] . '">';
        echo '<input type="submit" value="🗑️ Delete Cycle" class="delete-btn">';
        echo '</form>';

        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No appraisal cycles found.</p>";
}
?>

<script>
function confirmDelete() {
    return confirm("⚠️ Are you sure you want to delete this cycle? This action cannot be undone.");
}
</script>

<div class="back-button">
  <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left-circle"></i> Back
  </a>
</div>

</body>
</html>
