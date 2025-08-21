 <?php
session_start();

if ($_SESSION['role'] !== 'HR') {
    echo "Unauthorized access.";
    exit();
}

echo "<h1>HR Report Overview</h1>";
// You can now query and display appraisals, feedback, etc.
