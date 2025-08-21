<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password - Maseno Mission Hospital | EAS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ✅ Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #eef2f7;
      font-family: "Segoe UI", sans-serif;
    }
    .forgot-box {
      max-width: 420px;
      margin: 70px auto;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
    }
    .header-banner {
      text-align: center;
      margin-bottom: 20px;
    }
    .header-banner img {
      height: 60px;
      border-radius: 6px;
      margin-bottom: 10px;
    }
    .site-name {
      font-size: 1.2rem;
      font-weight: 600;
      color: #0d6efd;
    }
  </style>
</head>
<body>

<div class="forgot-box">
  <div class="header-banner">
    <img src="assets/logo.png" alt="Maseno Mission Hospital Logo">
    <div class="site-name">Maseno Mission Hospital</div>
    <small class="text-muted">Employee Appraisal System</small>
  </div>

  <h5 class="text-center mb-4"><i class="bi bi-key-fill"></i> Forgot Password</h5>

  <?php if (isset($_SESSION['reset_feedback'])): ?>
    <div class="alert alert-info">
      <?= htmlspecialchars($_SESSION['reset_feedback']) ?>
    </div>
    <?php unset($_SESSION['reset_feedback']); ?>
  <?php endif; ?>

  <form method="POST" action="process_reset_request.php">
    <div class="mb-3">
      <label for="email" class="form-label">📧 Enter your registered email</label>
      <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary w-100">
      <i class="bi bi-send"></i> Request Reset Link
    </button>
  </form>

  <div class="text-center mt-3">
    <a href="login.php" class="text-decoration-none"><i class="bi bi-box-arrow-left"></i> Back to Login</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
