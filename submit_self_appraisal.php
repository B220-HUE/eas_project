<?php
session_start();
include 'db_connect.php';

$role = $_SESSION['role'] ?? 'Not set';
echo "<p style='color:gray;'>Role: $role</p>";

if (!isset($_SESSION['user_id']) || $role !== 'Employee') {
    echo "<p class='error'>❌ Access denied. Employees only.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];
$cycle_id = $_GET['cycle_id'] ?? null;
$message = "";

if (!$cycle_id || !is_numeric($cycle_id)) {
    $message = "<p class='error'>❌ Invalid cycle selected.</p>";
} else {
    $emp_stmt = $conn->prepare("SELECT employee_id FROM employees WHERE user_id = ?");
    $emp_stmt->bind_param("i", $user_id);
    $emp_stmt->execute();
    $emp_stmt->bind_result($employee_id);
    $emp_stmt->fetch();
    $emp_stmt->close();

    if (empty($employee_id)) {
        echo "<p class='error'>❌ No employee record found for this user.</p>";
        exit();
    }

    $cycle = $conn->query("SELECT cycle_name, end_date FROM cycle WHERE cycle_id = $cycle_id")->fetch_assoc();
    $current_date = date("Y-m-d");

    if ($current_date > $cycle['end_date']) {
        echo "<p class='error'>❌ This appraisal cycle has closed. Deadline was " . date("F j, Y", strtotime($cycle['end_date'])) . ".</p>";
        exit();
    }

    $check = $conn->prepare("SELECT appraisal_id FROM appraisals WHERE employee_id = ? AND cycle_id = ?");
    $check->bind_param("ii", $employee_id, $cycle_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "<p class='error'>❌ You already submitted this appraisal.</p>";
        $check->close();
    } else {
        $check->close();
        $kpis = $conn->query("SELECT * FROM kpis");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $comments = $_POST['comments'] ?? '';
            $ratings = $_POST['ratings'] ?? [];

            if (empty($ratings)) {
                $message = "<p class='error'>❌ Please rate all KPIs before submitting.</p>";
            } else {
                $appraisal_type = 'Self';

                $stmt = $conn->prepare("INSERT INTO appraisals (employee_id, cycle_id, appraisal_type, appraisal_date, comments) VALUES (?, ?, ?, CURDATE(), ?)");
                $stmt->bind_param("iiss", $employee_id, $cycle_id, $appraisal_type, $comments);

                if ($stmt->execute()) {
                    $appraisal_id = $stmt->insert_id;
                    $stmt->close();

                    $score_stmt = $conn->prepare("INSERT INTO scores (appraisal_id, kpi_id, rating) VALUES (?, ?, ?)");
                    foreach ($ratings as $kpi_id => $rating) {
                        $score_stmt->bind_param("iii", $appraisal_id, $kpi_id, $rating);
                        $score_stmt->execute();
                    }
                    $score_stmt->close();

                    $message = "<p class='success'>✅ Appraisal submitted successfully!</p>";
                } else {
                    $message = "<p class='error'>❌ Error submitting appraisal: " . $stmt->error . "</p>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Self-Appraisal</title>
    <style>
        body.appraisal-form-background {
            font-family: Arial, sans-serif;
            padding: 20px;
            min-height: 100vh;
            background-image: url('assets/appraisal_form_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            z-index: 1;
        }

        body.appraisal-form-background::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(255,255,255,0.5);
            z-index: -1;
        }

        h2 { color: #333; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        select, textarea, input[type="submit"] {
            width: 100%; padding: 10px; margin-top: 5px;
        }

        input[type="submit"] {
            background-color: #007BFF; color: white; border: none; cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .success {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px #ccc;
            max-width: 700px;
            margin: auto;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body class="appraisal-form-background">

<?php if (!empty($message)): ?>
    <div class="<?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
        <?= $message ?>
    </div>
<?php endif; ?>

<?php if (isset($cycle)): ?>
<h2>✍️ Submit Appraisal for <?= htmlspecialchars($cycle['cycle_name']) ?></h2>
<p><strong>Deadline:</strong> <?= date("F j, Y", strtotime($cycle['end_date'])) ?></p>

<form method="POST">
    <label>Rate KPIs:</label>
    <?php while ($kpi = $kpis->fetch_assoc()) { ?>
        <label><?= $kpi['kpi_name'] ?> (<?= $kpi['description'] ?>):</label>
        <select name="ratings[<?= $kpi['kpi_id'] ?>]" required>
            <option value="">-- Select Rating --</option>
            <option value="1">1 - Poor</option>
            <option value="2">2 - Fair</option>
            <option value="3">3 - Good</option>
            <option value="4">4 - Very Good</option>
            <option value="5">5 - Excellent</option>
        </select><br><br>
    <?php } ?>

    <label>Comments (optional):</label>
    <textarea name="comments" rows="4" placeholder="Describe your achievements and challenges..."></textarea><br><br>

    <input type="submit" value="✅ Submit Appraisal">
</form>
<?php endif; ?>

<a href="view_cycles.php"><button style="margin-top:20px;">🔙 Back to Cycles</button></a>

<div class="back-button">
  <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left-circle"></i> Back
  </a>
</div>

</body>
</html>
