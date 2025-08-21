 <?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo "<p style='color:red;'>❌ Access denied.</p>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cycle_id'])) {
    $cycle_id = $_POST['cycle_id'];

    $stmt = $conn->prepare("DELETE FROM cycle WHERE cycle_id = ?");
    $stmt->bind_param("i", $cycle_id);

    if ($stmt->execute()) {
        header("Location: appraisal_cycles.php");
        exit();
    } else {
        echo "<p style='color:red;'>❌ Error deleting cycle: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>
