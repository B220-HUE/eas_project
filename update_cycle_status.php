 <?php
include 'db_connect.php';

$cycle_id = $_POST['cycle_id'];
$status = $_POST['status'];

$conn->query("UPDATE cycle SET status = '$status' WHERE cycle_id = $cycle_id");

header("Location: appraisal_cycles.php");
exit();
