 <?php
session_start();
include 'db_connect.php';

// ✅ Heuristic 3: Role check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo "<p class='error'>❌ Access denied.</p>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['goal_id'])) {
    $goal_id = $_POST['goal_id'];

    // ✅ Optional: Fetch goal text before deletion for logging
    $fetch_stmt = $conn->prepare("SELECT goal_text FROM departmental_goals WHERE goal_id = ?");
    $fetch_stmt->bind_param("i", $goal_id);
    $fetch_stmt->execute();
    $fetch_stmt->bind_result($goal_text);
    $fetch_stmt->fetch();
    $fetch_stmt->close();

    // ✅ Delete the goal
    $stmt = $conn->prepare("DELETE FROM departmental_goals WHERE goal_id = ?");
    $stmt->bind_param("i", $goal_id);

    if ($stmt->execute()) {
        $stmt->close();

        // ✅ Audit log entry
        $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action_type, description) VALUES (?, 'Delete Goal', ?)");
        $desc = "Deleted goal ID $goal_id: " . $goal_text;
        $log_stmt->bind_param("is", $_SESSION['user_id'], $desc);
        $log_stmt->execute();
        $log_stmt->close();

        header("Location: view_department_goals.php");
        exit();
    } else {
        echo "<p class='error'>❌ Error deleting goal: " . $stmt->error . "</p>";
        $stmt->close();
    }
}
?>
