<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    echo "<div class='alert alert-danger'>❌ Access denied.</div>";
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $feedback_id = $_POST['feedback_id'];
    $response_text = trim($_POST['response_text']);
    $file_path = null;

    // ✅ Handle file upload securely
    if (!empty($_FILES['response_file']['name'])) {
        $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        $file_ext = strtolower(pathinfo($_FILES["response_file"]["name"], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            $target_dir = "uploads/";
            $safe_name = uniqid() . "_" . basename($_FILES["response_file"]["name"]);
            $file_path = $target_dir . $safe_name;

            if (!move_uploaded_file($_FILES["response_file"]["tmp_name"], $file_path)) {
                $message = "<div class='alert alert-danger'>❌ File upload failed.</div>";
            }
        } else {
            $message = "<div class='alert alert-warning'>⚠️ Invalid file type.</div>";
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO feedback_responses (feedback_id, responder_id, response_text, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $feedback_id, $user_id, $response_text, $file_path);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Response submitted successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
        }

        $stmt->close();
    }
}

// ✅ Fetch feedback options
$feedbacks = $conn->prepare("SELECT feedback_id, feedback_text FROM feedback WHERE employee_id = ?");
$feedbacks->bind_param("i", $user_id);
$feedbacks->execute();
$result = $feedbacks->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Respond to Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>📝 Respond to Feedback</h2>
    <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary">
      🔙 Back to Dashboard
    </a>
  </div>

  <?= $message ?>

  <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm bg-white">
    <div class="mb-3">
      <label class="form-label">Select Feedback:</label>
      <select name="feedback_id" class="form-select" required>
        <option value="">-- Choose Feedback --</option>
        <?php while ($row = $result->fetch_assoc()) {
          echo "<option value='{$row['feedback_id']}'>" . htmlspecialchars($row['feedback_text']) . "</option>";
        } ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Your Response:</label>
      <textarea name="response_text" class="form-control" rows="4" required placeholder="Write your reply..."></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Upload File (optional):</label>
      <input type="file" name="response_file" class="form-control">
      <small class="text-muted">Allowed: PDF, DOC, DOCX, JPG, PNG</small>
    </div>

    <button type="submit" class="btn btn-primary">📤 Submit Response</button>
  </form>
</div>
</body>
</html>
