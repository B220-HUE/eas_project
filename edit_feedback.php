 <?php
session_start();
include 'db_connect.php';

$id = $_GET['id'];
$result = $conn->query("SELECT feedback_text FROM feedback WHERE feedback_id = $id");
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_text = $conn->real_escape_string($_POST['feedback']);
    $conn->query("UPDATE feedback SET feedback_text = '$new_text' WHERE feedback_id = $id");
    header("Location: view_feedback_given.php");
    exit();
}
?>

<form method="POST">
    <textarea name="feedback"><?= htmlspecialchars($row['feedback_text']) ?></textarea>
    <input type="submit" value="✅ Update">
</form>
