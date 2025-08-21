 <?php
session_start();
include 'db_connect.php';

$id = $_GET['id'];
$conn->query("DELETE FROM feedback WHERE feedback_id = $id");
header("Location: view_feedback_given.php");
exit();
