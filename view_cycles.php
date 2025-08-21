<?php
session_start();
include 'db_connect.php';

// ✅ Allow only employees
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    echo "<p style='color:red;'>Access denied.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch all appraisal cycles
$result = $conn->query("
    SELECT cycle_id, cycle_name, start_date, end_date, status
    FROM cycle
    ORDER BY start_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appraisal Cycles</title>
    <style>
        body.employee-appraisal-background {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-image: url('assets/appraisal_cycle_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        body.employee-appraisal-background::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(255,255,255,0.4); /* soft overlay */
            z-index: -1;
        }

        h2 { color: #333; }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 5px #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #eaeaea;
        }

        a.button {
            background-color: #007BFF;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        a.button:hover {
            background-color: #0056b3;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
    </style>
</head>

<body class="employee-appraisal-background">

<div class="back-button">
  <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left-circle"></i> Back
  </a>
</div>

<h2>📅 Appraisal Cycles</h2>

<table>
    <tr>
        <th>Cycle Name</th>
        <th>Deadline</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= htmlspecialchars($row['cycle_name']) ?></td>
        <td><?= date("F j, Y", strtotime($row['start_date'])) ?> → <?= date("F j, Y", strtotime($row['end_date'])) ?></td>
        <td><?= $row['status'] ?></td>
        <td>
            <?php if ($row['status'] === 'Open'): ?>
                <a class="button" href="submit_self_appraisal.php?cycle_id=<?= $row['cycle_id'] ?>">✍️ Fill Appraisal</a>
            <?php else: ?>
                <span style="color:gray;">Closed</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php } ?>
</table>

<a href="dashboard.php"><button style="margin-top:20px;">🔙 Back to Dashboard</button></a>

</body>
</html>
