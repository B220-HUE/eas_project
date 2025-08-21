<?php
session_start();
include 'db_connect.php';

// Fetch filter options
$employees = $conn->query("SELECT user_id, name FROM users");
$cycles = $conn->query("SELECT cycle_id, cycle_name FROM cycle");

// Fetch goals
$result = $conn->query("
    SELECT g.goal_id, u.user_id, u.name, c.cycle_id, c.cycle_name, g.goal_text, g.progress
    FROM goals g
    JOIN users u ON g.employee_id = u.user_id
    JOIN cycle c ON g.cycle_id = c.cycle_id
");
$goals = [];
while ($row = $result->fetch_assoc()) {
    $goals[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Goal Progress</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #E6F0FA;
            min-height: 100vh;
            overflow-y: auto;
        }

        header {
            padding: 20px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        .filters {
            display: flex;
            gap: 20px;
            padding: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        select {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: white;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .container {
            display: flex;
            flex-direction: row;
            gap: 20px;
            padding: 20px;
            flex-wrap: wrap;
        }

        .table-wrapper {
            flex: 2;
            max-height: 70vh;
            overflow-y: auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            animation: slideUp 0.8s ease;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #F9FBFD;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .progress-bar {
            background-color: #e0e0e0;
            border-radius: 20px;
            height: 20px;
            width: 100%;
            overflow: hidden;
            margin-top: 5px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 20px;
            animation: fillProgress 1s ease forwards;
        }

        .progress-fill.green { background-color: #4CAF50; }
        .progress-fill.yellow { background-color: #FFC107; }
        .progress-fill.red { background-color: #F44336; }

        .sidebar {
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            animation: fadeIn 1s ease-in-out;
        }

        .toggle-sidebar {
            display: none;
        }

        .back-button {
            margin: 20px;
            text-align: center;
        }

        .back-button button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .back-button button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.5s ease;
            }

            .sidebar.expanded {
                max-height: 300px;
            }

            .toggle-sidebar {
                display: block;
                text-align: center;
                margin: 10px;
                cursor: pointer;
                font-weight: bold;
                color: #007BFF;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fillProgress {
            from { width: 0%; }
            to { width: var(--target); }
        }
    </style>
</head>
<body>

<header>
    <h2>📊 Goal Progress</h2>
</header>

<div class="filters">
    <select id="employeeFilter">
        <option value="">👤 All Employees</option>
        <?php while ($emp = $employees->fetch_assoc()): ?>
            <option value="<?= $emp['user_id'] ?>"><?= $emp['name'] ?></option>
        <?php endwhile; ?>
    </select>

    <select id="cycleFilter">
        <option value="">📅 All Cycles</option>
        <?php while ($cyc = $cycles->fetch_assoc()): ?>
            <option value="<?= $cyc['cycle_id'] ?>"><?= $cyc['cycle_name'] ?></option>
        <?php endwhile; ?>
    </select>
</div>

<div class="toggle-sidebar" onclick="toggleSidebar()">📂 Show Summary</div>

<div class="container">
    <div class="table-wrapper">
        <table id="goalTable">
            <thead>
                <tr><th>Employee</th><th>Cycle</th><th>Goal</th><th>Progress</th></tr>
            </thead>
            <tbody>
                <?php foreach ($goals as $row): 
                    $progress = $row['progress'];
                    $colorClass = ($progress >= 80) ? 'green' : (($progress >= 50) ? 'yellow' : 'red');
                ?>
                <tr data-employee="<?= $row['user_id'] ?>" data-cycle="<?= $row['cycle_id'] ?>">
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['cycle_name'] ?></td>
                    <td><?= $row['goal_text'] ?></td>
                    <td>
                        <?= $progress ?>%
                        <div class="progress-bar">
                            <div class="progress-fill <?= $colorClass ?>" style="--target: <?= $progress ?>%;"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="sidebar" id="sidebar">
        <h3>🧭 Summary</h3>
        <p>Use filters to view goals by employee or cycle. Progress bars show completion percentage.</p>
        <p>Track alignment with objectives and identify areas needing support.</p>
    </div>
</div>

<div class="back-button">
    <button onclick="confirmBack()">🔙 Back to Dashboard</button>
</div>

<script>
    const employeeFilter = document.getElementById('employeeFilter');
    const cycleFilter = document.getElementById('cycleFilter');
    const rows = document.querySelectorAll('#goalTable tbody tr');

    function filterGoals() {
        const emp = employeeFilter.value;
        const cyc = cycleFilter.value;

        rows.forEach(row => {
            const matchEmp = !emp || row.dataset.employee === emp;
            const matchCyc = !cyc || row.dataset.cycle === cyc;
            row.style.display = (matchEmp && matchCyc) ? '' : 'none';
        });
    }

    employeeFilter.addEventListener('change', filterGoals);
    cycleFilter.addEventListener('change', filterGoals);

    function confirmBack() {
        if (confirm("Are you sure you want to return to the dashboard?")) {
            window.location.href = "dashboard.php";
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('expanded');
    }
</script>

</body>
</html>
