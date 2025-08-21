<?php
session_start();
include 'db_connect.php';

// ✅ Only employees can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    echo "<p style='color:red;'>Access denied. Employees only.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// ✅ Handle response submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback_id'])) {
    $feedback_id = $_POST['feedback_id'];
    $response_text = $conn->real_escape_string($_POST['response_text']);

    $stmt = $conn->prepare("INSERT INTO feedback_responses (feedback_id, responder_id, response_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $feedback_id, $user_id, $response_text);

    if ($stmt->execute()) {
        echo "<p class='success'>✅ Response submitted successfully!</p>";
    } else {
        echo "<p class='error'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// ✅ Get feedback given to this employee
$result = $conn->query("
    SELECT f.feedback_id, f.feedback_text, f.given_at, u.name AS giver_name
    FROM feedback f
    JOIN users u ON f.given_by = u.user_id
    WHERE f.employee_id = $user_id
    ORDER BY f.given_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Feedback</title>
    <style>
        body.feedback-background {
            background-image: url('assets/feedback_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            padding: 20px;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        body.feedback-background::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(255,255,255,0.6);
            z-index: -1;
        }

        .feedback-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: auto;
        }

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
        textarea {
            width: 100%;
            padding: 8px;
            resize: vertical;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
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
        .search-form {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 3px #ccc;
        }
        .search-form input[type="text"] {
            width: 70%;
            padding: 8px;
            margin-right: 10px;
        }
        .search-form input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .success, .error {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .success { color: green; }
        .error { color: red; }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
    </style>
    <script>
        function confirmResponseSubmit() {
            return confirm("Are you sure you want to submit this response?");
        }

        function confirmDashboardRedirect() {
            return confirm("Are you sure you want to return to the dashboard? Unsaved responses will be lost.");
        }
    </script>
</head>
<body class="feedback-background">

<div class="feedback-container">
    <h2>💬 Feedback You've Received</h2>
    <p>Welcome, <strong><?= htmlspecialchars($name) ?></strong>. Below is feedback given to you by HR or your supervisor. You can respond to each one.</p>

    <!-- 🔍 Search Form -->
    <div class="search-form">
        <form method="GET" action="search_feedback.php">
            <input type="text" name="query" placeholder="Search feedback by keyword or date (YYYY-MM-DD)" required>
            <input type="submit" value="🔍 Search">
        </form>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>From</th>
                    <th>Feedback</th>
                    <th>Date</th>
                    <th>Your Response</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['giver_name']) ?></td>
                    <td><?= htmlspecialchars($row['feedback_text']) ?></td>
                    <td><?= $row['given_at'] ?></td>
                    <td>
                        <form method="POST" action="view_feedback.php" onsubmit="return confirmResponseSubmit();">
                            <input type="hidden" name="feedback_id" value="<?= $row['feedback_id'] ?>">
                            <textarea name="response_text" rows="3" required placeholder="Write your response here..."></textarea><br>
                            <input type="submit" value="✅ Submit">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No feedback received yet.</p>
    <?php endif; ?>

    <a href="dashboard.php" onclick="return confirmDashboardRedirect();">
        <button>🔙 Back to Dashboard</button>
    </a>
</div>

</body>
</html>
