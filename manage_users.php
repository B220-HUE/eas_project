 <?php
session_start();
include 'db_connect.php';

// ✅ Only Admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<p style='color:red;'>Access denied. Admins only.</p>";
    exit();
}

// ✅ Handle new user creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_user'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, role, status, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $role, $status, $password);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ User created successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// ✅ Handle user update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, status=? WHERE user_id=?");
    $stmt->bind_param("ssssi", $name, $email, $role, $status, $user_id);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ User updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// ✅ Fetch all users
$users = $conn->query("SELECT * FROM users ORDER BY role ASC, name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        form, table {
            background-color: #fff; padding: 15px;
            border-radius: 5px; box-shadow: 0 0 5px #ccc;
            margin-bottom: 30px;
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
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; vertical-align: top; }
        th { background-color: #007BFF; color: white; }
    </style>
</head>
<body>

<h2>👥 Add New User</h2>
<form method="POST" action="manage_users.php">
    <label for="name">Name:</label>
    <input type="text" name="name" required>

    <label for="email">Email:</label>
    <input type="email" name="email" required>

    <label for="role">Role:</label>
    <select name="role" required>
        <option value="Employee">Employee</option>
        <option value="Supervisor">Supervisor</option>
        <option value="Admin">Admin</option>
    </select>

    <label for="status">Status:</label>
    <select name="status" required>
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
    </select>

    <label for="password">Password:</label>
    <input type="text" name="password" required>

    <input type="submit" name="create_user" value="✅ Create User">
</form>

<h2>📋 Existing Users</h2>
<?php if ($users->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $users->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="manage_users.php">
                    <td><input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"></td>
                    <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>"></td>
                    <td>
                        <select name="role">
                            <option value="Employee" <?= $row['role'] === 'Employee' ? 'selected' : '' ?>>Employee</option>
                            <option value="Supervisor" <?= $row['role'] === 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
                            <option value="Admin" <?= $row['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </td>
                    <td>
                        <select name="status">
                            <option value="Active" <?= $row['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= $row['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                        <input type="submit" name="update_user" value="✏️ Update">
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No users found.</p>
<?php endif; ?>

<a href="dashboard.php"><button style="margin-top:20px;">🔙 Back to Dashboard</button></a>
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
