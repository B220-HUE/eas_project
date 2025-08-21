<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eas_db");

// ✅ Allow HR and Employees only
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['HR', 'Employee'])) {
    echo "Access denied.";
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// ✅ Build filter conditions
$where = "WHERE 1=1";

if ($role === 'Employee') {
    $where .= " AND e.user_id = $user_id";
} else {
    if (!empty($_GET['dept_id'])) {
        $dept_id = $_GET['dept_id'];
        $where .= " AND e.dept_id = $dept_id";
    }

    if (!empty($_GET['from_date'])) {
        $from = $_GET['from_date'];
        $where .= " AND a.appraisal_date >= '$from'";
    }

    if (!empty($_GET['to_date'])) {
        $to = $_GET['to_date'];
        $where .= " AND a.appraisal_date <= '$to'";
    }
}

$appraisals = $conn->query("
    SELECT a.appraisal_id, a.appraisal_date, a.comments,
           e.employee_id, eu.name AS employee_name,
           u.name AS evaluator_name
    FROM appraisals a
    JOIN employees e ON a.employee_id = e.employee_id
    JOIN users eu ON e.user_id = eu.user_id
    JOIN users u ON a.evaluator_id = u.user_id
    $where
    ORDER BY a.appraisal_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Appraisals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #e3f2fd; /* Soft Blue */
            font-family: Arial, sans-serif;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 1200px;
            margin: auto;
        }

        h2 { color: #333; }
        form { margin-bottom: 20px; }
        label { margin-right: 10px; }
        select, input[type="date"] {
            margin-right: 20px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            padding: 6px 12px;
            border-radius: 5px;
            border: none;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #fff;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
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

<div class="container">
    <h2>📋 Submitted Appraisals</h2>
    <p>Logged in as: <strong><?= htmlspecialchars($role) ?></strong></p>

    <?php if ($role === 'HR'): ?>
    <form method="GET">
        <label>Department:</label>
        <select name="dept_id">
            <option value="">-- All Departments --</option>
            <?php
            $depts = $conn->query("SELECT * FROM departments");
            while ($d = $depts->fetch_assoc()) {
                $selected = (isset($_GET['dept_id']) && $_GET['dept_id'] == $d['dept_id']) ? 'selected' : '';
                echo "<option value='{$d['dept_id']}' $selected>{$d['dept_name']}</option>";
            }
            ?>
        </select>

        <label>From:</label>
        <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>">

        <label>To:</label>
        <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>">

        <input type="submit" value="Filter">
    </form>
    <?php endif; ?>

    <table>
        <tr>
            <th>Employee</th>
            <th>Evaluator</th>
            <th>Date</th>
            <th>Comments</th>
            <th>Scores</th>
        </tr>

        <?php while ($row = $appraisals->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['employee_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['evaluator_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['appraisal_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['comments']) . "</td>";

            $scores = $conn->query("
                SELECT s.rating, k.kpi_name
                FROM scores s
                JOIN kpis k ON s.kpi_id = k.kpi_id
                WHERE s.appraisal_id = {$row['appraisal_id']}
            ");

            echo "<td>";
            while ($score = $scores->fetch_assoc()) {
                echo htmlspecialchars($score['kpi_name']) . ": " . htmlspecialchars($score['rating']) . "<br>";
            }
            echo "</td>";

            echo "</tr>";
        } ?>
    </table>
</div>

<div class="back-button">
    <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left-circle"></i> Back
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
