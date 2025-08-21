 <?php
session_start();
include 'db_connect.php';

// ✅ Only Supervisors can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supervisor') {
    echo "<p style='color:red;'>Access denied. Supervisors only.</p>";
    exit();
}

$name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];

// ✅ Get feedback given by this supervisor
$result = $conn->query("
    SELECT f.feedback_id, f.feedback_text, f.given_at, u.name AS employee_name
    FROM feedback f
    JOIN users u ON f.employee_id = u.user_id
    WHERE f.given_by = $user_id
    ORDER BY f.given_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feedback Responses</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 5px #ccc;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        th {
            background-color: #007BFF;
            color: white;
            text-align: left;
        }
        em { color: #666; }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<h2>🔍 Feedback Responses</h2>
<p>Welcome, <strong><?= htmlspecialchars($name) ?></strong>. Below are employee responses to feedback you've given.</p>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Feedback</th>
                <th>Given On</th>
                <th>Response</th>
                <th>Responded On</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
            // ✅ Get response for this feedback
            $feedback_id = $row['feedback_id'];
            $response_result = $conn->query("
                SELECT response_text, responded_at
                FROM feedback_responses
                WHERE feedback_id = $feedback_id
                LIMIT 1
            ");
            $response = $response_result->fetch_assoc();
            ?>
            <tr>
                <td><?= htmlspecialchars($row['employee_name']) ?></td>
                <td><?= htmlspecialchars($row['feedback_text']) ?></td>
                <td><?= $row['given_at'] ?></td>
                <td>
                    <?php if ($response): ?>
                        <?= htmlspecialchars($response['response_text']) ?>
                    <?php else: ?>
                        <em>No response yet.</em>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $response ? $response['responded_at'] : '—' ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No feedback records found.</p>
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
