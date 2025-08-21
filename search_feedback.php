 <?php
session_start();
include 'db_connect.php';

// ✅ Role check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    echo "<p style='color:red;'>❌ Access denied. Employees only.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];
$query = $_GET['query'] ?? '';

echo "<h2>🔍 Search Results for: " . htmlspecialchars($query) . "</h2>";

if (!empty($query)) {
    $like = "%$query%";

    $stmt = $conn->prepare("
        SELECT f.feedback_id, f.comments, f.feedback_date, u.name AS sender
        FROM feedback f
        JOIN users u ON f.sender_id = u.user_id
        WHERE f.employee_id = ? AND (f.comments LIKE ? OR f.feedback_date = ?)
        ORDER BY f.feedback_date DESC
    ");
    $stmt->bind_param("iss", $user_id, $like, $query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div style='background:#fff; padding:10px; margin-bottom:10px; border-radius:5px; box-shadow:0 0 3px #ccc;'>";
            echo "<p><strong>From:</strong> " . htmlspecialchars($row['sender']) . "</p>";
            echo "<p><strong>Date:</strong> " . $row['feedback_date'] . "</p>";
            echo "<p><strong>Comments:</strong> " . htmlspecialchars($row['comments']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No feedback found matching your search.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Please enter a search term.</p>";
}
?>
