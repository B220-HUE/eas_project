 <?php
session_start();
include 'db_connect.php';

$message = ""; // ✅ Initialize message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $dept_id = $_POST['dept_id'];

    // ✅ Basic validation
    if (empty($name) || empty($email) || empty($dept_id)) {
        $message = "<p class='error'>❌ Please fill all required fields.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO employees (name, email, dept_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $email, $dept_id);

        if ($stmt->execute()) {
            $message = "<p class='success'>✅ Employee registered successfully!</p>";
        } else {
            $message = "<p class='error'>❌ Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Employee</title>
    <style>
        body { font-family: Arial; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
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
            background-color: #fff; padding: 20px;
            border-radius: 5px; box-shadow: 0 0 5px #ccc;
            max-width: 400px;
        }
        label { display: block; margin-top: 10px; }
        input, select {
            width: 100%; padding: 8px; margin-top: 5px;
        }
        input[type="submit"] {
            margin-top: 15px; background-color: #007BFF;
            color: white; border: none; border-radius: 5px;
            padding: 10px 20px; cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<h2>🧑‍💼 Register New Employee</h2>

<?= $message ?> <!-- ✅ Display message -->

<form method="POST" action="add_employee.php">
    <label>Full Name:</label>
    <input type="text" name="name" required>

    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Select Department:</label>
    <select name="dept_id" required>
        <option value="">-- Choose Department --</option>
        <?php
        $result = $conn->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name ASC");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['dept_id']}'>{$row['dept_name']}</option>";
        }
        ?>
    </select>

    <input type="submit" value="✅ Register Employee">
</form>
<style>
  .back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 1000;
  }
</style>

<div class="back-button">
  <a href="dashboard.php" onclick="return confirm('Return to dashboard?')" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left-circle"></i> Back
  </a>
</div>

</body>
</html>
