<?php
session_start();
include 'db_connect.php';

// ✅ Only Supervisors can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supervisor') {
    echo "<div class='alert error'>❌ Access denied. Supervisors only.</div>";
    exit();
}

$name = $_SESSION['name'];

// ✅ Get goals assigned to employees
$result = $conn->query("
    SELECT g.goal_text, u.name AS employee_name, c.cycle_name, c.start_date, c.end_date
    FROM goals g
    JOIN users u ON g.employee_id = u.user_id
    JOIN cycle c ON g.cycle_id = c.cycle_id
    ORDER BY c.start_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assigned Goals</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #E6F0FA;
            margin: 0;
            padding: 40px 20px;
        }

        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            font-size: 16px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-top: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            animation: fadeIn 0.8s ease-in-out;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #F9FBFD;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .alert {
            max-width: 600px;
            margin: 20px auto;
            padding: 15px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            transition: background-color 0.3s ease;
        }

        .back-button a:hover {
            background-color: #0056b3;
        }

        button {
            margin: 30px auto;
            display: block;
            padding: 12px 24px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            table, th, td {
                font-size: 14px;
            }

            .back-button a {
                font-size: 12px;
                padding: 8px 12px;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="back-button">
    <a href="dashboard.php" onclick="return confirmBack()">🔙 Back</a>
</div>

<h2>🎯 Assigned Goals</h2>
<p>Welcome, <strong><?= htmlspecialchars($name) ?></strong>. Below are goals assigned to employees across appraisal cycles.</p>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Goal</th>
                <th>Cycle</th>
                <th>Start</th>
                <th>End</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['employee_name']) ?></td>
                <td><?= htmlspecialchars($row['goal_text']) ?></td>
                <td><?= htmlspecialchars($row['cycle_name']) ?></td>
                <td><?= htmlspecialchars($row['start_date']) ?></td>
                <td><?= htmlspecialchars($row['end_date']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert error">⚠️ No goals assigned yet.</div>
<?php endif; ?>

<a href="dashboard.php"><button onclick="return confirmBack()">🔙 Back to Dashboard</button></a>

<script>
    function confirmBack() {
        return confirm("Are you sure you want to return to the dashboard?");
    }
</script>

</body>
</html>
