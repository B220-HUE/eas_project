<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eas_db");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Supervisor') {
    echo "<p class='error'>❌ Access denied. Only Supervisors can submit appraisals.</p>";
    exit();
}

$feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'] ?? '';
    $comments = $_POST['comments'] ?? '';
    $ratings = $_POST['ratings'] ?? [];

    if (empty($employee_id) || empty($ratings)) {
        $feedback = "<p class='error'>❌ Please select an employee and rate all KPIs.</p>";
    } else {
        $evaluator_id = $_SESSION['user_id'];
        $appraisal_type = 'Supervisor';

        $stmt = $conn->prepare("INSERT INTO appraisals (employee_id, evaluator_id, appraisal_type, appraisal_date, comments) VALUES (?, ?, ?, CURDATE(), ?)");
        $stmt->bind_param("iiss", $employee_id, $evaluator_id, $appraisal_type, $comments);

        if ($stmt->execute()) {
            $appraisal_id = $stmt->insert_id;
            $stmt->close();

            $score_stmt = $conn->prepare("INSERT INTO scores (appraisal_id, kpi_id, rating) VALUES (?, ?, ?)");
            foreach ($ratings as $kpi_id => $rating) {
                $score_stmt->bind_param("iii", $appraisal_id, $kpi_id, $rating);
                $score_stmt->execute();
            }
            $score_stmt->close();

            $feedback = "<p class='success'>✅ Appraisal submitted successfully!</p>";
        } else {
            $feedback = "<p class='error'>❌ Error submitting appraisal: " . $stmt->error . "</p>";
            $stmt->close();
        }
    }
}

$employees = $conn->query("SELECT e.employee_id, u.name FROM employees e JOIN users u ON e.user_id = u.user_id");
$kpis = $conn->query("SELECT * FROM kpis");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Appraisal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('assets/self_assessment_mobile.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(2px);
            z-index: -1;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-top: 40px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 40px auto;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        select, textarea, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .kpi-block {
            margin-bottom: 15px;
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

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 5px #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        th, td {
            text-align: left;
            padding: 12px;
        }

        th {
            background-color: #f0f8ff;
        }

        #whyWorks {
            max-width: 700px;
            margin: 20px auto;
        }

        button.toggle-btn {
            margin: 30px auto;
            display: block;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button.toggle-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="back-button">
  <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left-circle"></i> Back
  </a>
</div>

<h2>📝 Submit Appraisal</h2>

<?= $feedback ?>

<form method="POST">
    <label>Select Employee:</label>
    <select name="employee_id" required>
        <option value="">-- Choose Employee --</option>
        <?php while ($row = $employees->fetch_assoc()) {
            echo "<option value='{$row['employee_id']}'>{$row['name']}</option>";
        } ?>
    </select>

    <label>Rate KPIs:</label>
    <?php while ($kpi = $kpis->fetch_assoc()) { ?>
        <div class="kpi-block">
            <label><?= $kpi['kpi_name'] ?> (<?= $kpi['description'] ?>):</label>
            <select name="ratings[<?= $kpi['kpi_id'] ?>]" required>
                <option value="">-- Select Rating --</option>
                <option value="1">1 - Poor</option>
                <option value="2">2 - Fair</option>
                <option value="3">3 - Good</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select>
        </div>
    <?php } ?>

    <label>Comments (optional):</label>
    <textarea name="comments" rows="4" placeholder="Add any remarks or observations..."></textarea>

    <input type="submit" value="✅ Submit Appraisal">
</form>

<!-- 🔽 Collapsible Why This Works Section -->
<button onclick="toggleWhyWorks()" class="toggle-btn">ℹ️ Why This Works</button>

<div id="whyWorks" style="display:none;">
  <table>
    <tr>
      <th>Feature</th>
      <th>Benefit</th>
    </tr>
    <tr>
      <td>🎨 Background image</td>
      <td>Adds branding and thematic relevance to the appraisal process</td>
    </tr>
    <tr>
      <td>🧼 Soft overlay</td>
      <td>Improves contrast and ensures form elements remain readable</td>
    </tr>
    <tr>
      <td>🌫️ Blurred backdrop</td>
      <td>Reduces visual noise from detailed image elements</td>
    </tr>
    <tr>
      <td>📋 White form container</td>
      <td>Keeps the form clean, focused, and accessible for all users</td>
    </tr>
  </table>
</div>

<script>
function toggleWhyWorks() {
  const section = document.getElementById('whyWorks');
  section.style.display = section.style.display === 'none' ? 'block' : 'none';
}
</script>

</body>
</html>
