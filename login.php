<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Maseno Mission Hospital | EAS Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Favicon & Preload -->
  <link rel="icon" href="assets/hospital_logo.png" type="image/png">
  <link rel="preload" as="image" href="assets/bg_login.png">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style id="theme-style">
    body {
      background: url('assets/bg_login.png') no-repeat center center fixed;
      background-size: cover;
      font-family: "Segoe UI", sans-serif;
      color: #343a40;
      transition: background-color 0.3s ease;
    }
    .background-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.45);
      z-index: 0;
    }
    .login-box {
      max-width: 420px;
      margin: 70px auto;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 14px rgba(0, 0, 0, 0.12);
      position: relative;
      z-index: 1;
    }
    .form-label {
      font-weight: 500;
    }
    .form-control:focus {
      box-shadow: 0 0 6px rgba(13, 110, 253, 0.4);
      border-color: #0d6efd;
      outline: none;
    }
    .toggle-password {
      cursor: pointer;
    }
    .header-banner {
      text-align: center;
      margin-bottom: 20px;
    }
    .header-banner img {
      height: 80px;
      border-radius: 6px;
      display: block;
      margin: 0 auto 10px;
      opacity: 0;
      animation: fadeLogo 0.6s ease-in forwards;
    }
    @keyframes fadeLogo {
      to { opacity: 1; }
    }
    .alert-danger {
      animation: shake 0.4s ease-in-out;
    }
    @keyframes shake {
      0% { transform: translateX(0); }
      25% { transform: translateX(-4px); }
      50% { transform: translateX(4px); }
      75% { transform: translateX(-2px); }
      100% { transform: translateX(0); }
    }
    .site-name {
      font-size: 1.2rem;
      font-weight: 600;
      color: #0d6efd;
    }
    .theme-toggle {
      position: absolute;
      top: 16px;
      right: 16px;
      z-index: 2;
    }
    .btn-primary {
      transition: all 0.25s ease-in-out;
      border-radius: 6px;
      font-weight: 500;
      font-size: 1rem;
    }
    .btn-primary:active {
      transform: scale(0.98);
    }
    a:hover {
      text-decoration: underline;
    }
    @media (max-width: 480px) {
      .login-box {
        margin: 30px auto;
        padding: 20px;
      }
      .header-banner img {
        height: 60px;
      }
    }
  </style>
</head>
<body>

<div class="background-overlay"></div>

<!-- Theme Toggle -->
<div class="theme-toggle">
  <button id="toggleTheme" class="btn btn-sm btn-outline-light" aria-label="Toggle theme"><i class="bi bi-moon"></i></button>
</div>

<div class="login-box" role="form" aria-labelledby="loginTitle">
  <div class="header-banner">
    <img src="assets/hospital_logo.png" alt="Maseno Mission Hospital Logo">
    <div class="site-name">Maseno Mission Hospital</div>
    <small class="text-muted">Employee Appraisal System</small>
  </div>

  <div class="text-center mb-1">
    <em class="text-secondary">Good day! Please log in to continue 🔐</em>
  </div>

  <h5 id="loginTitle" class="text-center mb-3"><i class="bi bi-person-circle"></i> Staff Login</h5>

  <!-- 🔴 Display login error message -->
  <?php if (isset($_SESSION['login_error'])): ?>
    <div class="alert alert-danger d-flex align-items-center" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <div><?= htmlspecialchars($_SESSION['login_error']) ?></div>
    </div>
    <?php unset($_SESSION['login_error']); ?>
  <?php endif; ?>

  <form method="POST" action="process_login.php">
    <div class="mb-3">
      <label for="email" class="form-label" aria-label="Enter your staff email">📧 Email</label>
      <input type="email" name="email" id="email" class="form-control" placeholder="nurse.joy@maseno.or.ke" required autofocus>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label" aria-label="Enter your password">🔒 Password</label>
      <div class="input-group">
        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
        <span class="input-group-text toggle-password"><i class="bi bi-eye-slash"></i></span>
      </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">
      <i class="bi bi-box-arrow-in-right"></i> Login
    </button>
  </form>

  <div class="text-center mt-3">
    <a href="forgot_password.php" class="text-decoration-none"><i class="bi bi-question-circle"></i> Forgot Password?</a>
  </div>

  <div class="mt-3">
    <p class="text-muted small">
      <strong>Access Roles:</strong> <em>Admin</em>, <em>HR</em>, <em>Supervisor</em>, <em>Employee</em>.
    </p>
  </div>

  <div class="text-center mt-2">
    <small class="text-muted">Keyboard-accessible and screen-reader friendly.</small>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Password Toggle -->
<script>
  document.querySelector('.toggle-password').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    const icon = this.querySelector('i');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
  });
</script>

<!-- Dark Mode Toggle -->
<script>
  const themeButton = document.getElementById('toggleTheme');
  const themeStyle = document.getElementById('theme-style');

  themeButton.addEventListener('click', () => {
    if (themeStyle.innerHTML.includes("background: url('assets/bg_login.png')")) {
      themeStyle.innerHTML = `
        body { background-color: #212529; font-family: "Segoe UI", sans-serif; color: #f8f9fa; padding: 20px; }
        .login-box { background-color: #343a40; color: #fff; box-shadow: 0 0 12px rgba(255,255,255,0.08); position: relative; z-index: 1; }
        .header-banner .site-name { color: #f8f9fa; }
      `;
      themeButton.innerHTML = '<i class="bi bi-sun"></i>';
    } else {
      themeStyle.innerHTML = `
        body {
          background: url('assets/bg_login.png') no-repeat center center fixed;
          background-size: cover;
          font-family: "Segoe UI", sans-serif;
          color: #343a40;
          transition: background-color 0.3s ease;
        }
        .login-box {
          max-width: 420px;
          margin: 70px auto;
          background-color: #ffffff;
          padding