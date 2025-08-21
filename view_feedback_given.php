<?php
session_start();
include 'db_connect.php';

// ✅ Only HR can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo "<div class='alert alert-danger'>❌ Access denied. HR only.</div>";
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Secure query using prepared statement
$stmt = $conn->prepare("
    SELECT f.feedback_id, u.name AS employee_name, f.feedback_text, f.given_at
    FROM feedback f
    JOIN users u ON f.employee_id = u.user_id
    WHERE f.given_by = ?
    ORDER BY f.given_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Given</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .feedback-card {
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .feedback-actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>💬 Feedback You’ve Given</h2>
        <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="feedback-card">
                <h5 class="mb-1"><?= htmlspecialchars($row['employee_name']) ?></h5>
                <p class="mb-2"><?= nl2br(htmlspecialchars($row['feedback_text'])) ?></p>
                <small class="text-muted">🕒 <?= date("M d, Y H:i", strtotime($row['given_at'])) ?></small>
                <div class="feedback-actions mt-2">
                    <a href="edit_feedback.php?id=<?= $row['feedback_id'] ?>" class="btn btn-sm btn-outline-primary">
                        ✏️ Edit
                    </a>
                    <a href="delete_feedback.php?id=<?= $row['feedback_id'] ?>" onclick="return confirm('Are you sure you want to delete this feedback?')" class="btn btn-sm btn-outline-danger">
                        🗑️ Delete
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No feedback records found.
        </div>
    <?php endif; ?>
</div>
</body>
</html>
