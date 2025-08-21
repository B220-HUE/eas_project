<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eas_db");

// ✅ Only allow Employees
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Employee') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit();
}

// ✅ Get employee_id securely
$user_id = $_SESSION['user_id'];
$emp_stmt = $conn->prepare("SELECT employee_id FROM employees WHERE user_id = ?");
$emp_stmt->bind_param("i", $user_id);
$emp_stmt->execute();
$emp_result = $emp_stmt->get_result();

if ($emp_result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Employee record not found.</div>";
    exit();
}
$employee_id = $emp_result->fetch_assoc()['employee_id'];

// ✅ Get appraisals securely
$app_stmt = $conn->prepare("
    SELECT a.appraisal_id, a.appraisal_date, a.comments,
           u.name AS evaluator_name
    FROM appraisals a
    JOIN users u ON a.evaluator_id = u.user_id
    WHERE a.employee_id = ?
    ORDER BY a.appraisal_date DESC
");
$app_stmt->bind_param("i", $employee_id);
$app_stmt->execute();
$appraisals = $app_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appraisal History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 40px 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 30px;
        }

        table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        td, th {
            padding: 12px;
            vertical-align: top;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
        }

        .back-button a {
            background-color: #007BFF;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .back-button a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="back-button">
    <a href="dashboard.php" onclick="return confirm('Return to dashboard?')">🔙 Back</a>
</div>

<h2>📋 My Appraisal History</h2>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Date</th>
                <th>Evaluator</th>
                <th>Comments</th>
                <th>Scores</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $appraisals->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['appraisal_date']) ?></td>
                    <td><?= htmlspecialchars($row['evaluator_name']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['comments'])) ?></td>
                    <td>
                        <?php
                        $score_stmt = $conn->prepare("
                            SELECT s.rating, k.kpi_name
                            FROM scores s
                            JOIN kpis k ON s.kpi_id = k.kpi_id
                            WHERE s.appraisal_id = ?
                        ");
                        $score_stmt->bind_param("i", $row['appraisal_id']);
                        $score_stmt->execute();
                        $scores = $score_stmt->get_result();

                        if ($scores->num_rows > 0) {
                            while ($score = $scores->fetch_assoc()) {
                                echo "<strong>" . htmlspecialchars($score['kpi_name']) . ":</strong> " . htmlspecialchars($score['rating']) . "<br>";
                            }
                        } else {
                            echo "<em>No scores recorded</em>";
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
