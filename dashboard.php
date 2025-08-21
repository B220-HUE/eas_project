<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 🎯 Set background class based on role
$role = $_SESSION['role'] ?? '';
$bodyClass = match($role) {
  'HR'         => 'hr-background',
  'Employee'   => 'employee-background',
  'Supervisor' => 'supervisor-background',
  'Admin'      => 'admin-background',
  default      => ''
};

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
      body {
        background-color: #f8f9fa;
        padding: 20px;
        min-height: 100vh;
        position: relative;
        z-index: 1;
      }

      .dashboard-section {
        margin-bottom: 40px;
      }

      .btn {
        margin: 5px 5px 10px 0;
      }

      /* 🎯 HR role: custom background image */
      .hr-background {
        background-image: url('assets/hr_bg.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
      }

      .hr-background::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255,255,255,0.6);
        z-index: -1;
      }

      /* 👤 Employee role: custom background image */
      .employee-background {
        background-image: url('assets/employee_bg.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
      }

      .employee-background::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255,255,255,0.4);
        z-index: -1;
      }

      /* 🧑‍💼 Supervisor role: custom background image */
      .supervisor-background {
        background-image: url('assets/supervisor_bg.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
      }

      .supervisor-background::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255,255,255,0.5);
        z-index: -1;
      }
      /* 🛡️ Admin role: custom background image */
.admin-background {
  background-image: url('assets/admin_bg.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-color: #f0f0f0; /* fallback color */
}

.admin-background::before {
  content: "";
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(255,255,255,0.5); /* soft overlay */
  z-index: -1;
}

    </style>
</head>

<!-- 🔗 Apply background class based on role -->
<body class="<?= $bodyClass ?>">

<div class="container">




  <!-- 🎉 Login Success Message -->
<?php if (isset($_SESSION['login_success'])): ?>
  <div class="alert alert-success text-center mt-3 mx-auto" style="max-width: 600px;">
    <i class="bi bi-check-circle-fill me-2"></i>
    <?= htmlspecialchars($_SESSION['login_success']) ?>
  </div>
  <?php unset($_SESSION['login_success']); ?>
<?php endif; ?>

<!-- 🔓 Logout Button with Confirmation -->
<div class="text-end mb-3">
  <a href="logout.php" onclick="return confirm('Are you sure you want to log out?')" class="btn btn-outline-danger">
    <i class="bi bi-box-arrow-left"></i> Logout
  </a>
</div>

<!-- 👋 Welcome Header -->
<h2 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
<p>Your role is: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>

<div class="dashboard-section">
<?php

    switch ($_SESSION['role']) {
        case 'Admin':
  echo "<h3>🛠️ Admin Dashboard</h3>";
  echo "<p>Oversee system operations, manage users, and monitor performance across departments.</p>";

  echo "<h4>📋 Core Management Tools</h4>";
  echo '<a href="manage_cycles.php" class="btn btn-outline-primary mb-2"><i class="bi bi-calendar3"></i> Manage Appraisal Cycles</a> ';
  echo '<a href="manage_goals.php" class="btn btn-outline-success mb-2"><i class="bi bi-bullseye"></i> Manage Goals</a> ';
  echo '<a href="admin_appraisals.php" class="btn btn-outline-info mb-2"><i class="bi bi-bar-chart-line"></i> View All Appraisals</a> ';
  echo '<a href="admin_feedback_log.php" class="btn btn-outline-secondary mb-2"><i class="bi bi-chat-left-text"></i> View Feedback Logs</a> ';
  echo '<a href="export_reports.php" class="btn btn-outline-dark mb-2"><i class="bi bi-file-earmark-arrow-down"></i> Export Appraisal Summaries</a> ';
  echo '<a href="audit_log.php" class="btn btn-outline-warning mb-2"><i class="bi bi-shield-lock"></i> Track Admin Actions</a>';

  echo "<h4 class='mt-4'>⚙️ System Configuration</h4>";
  echo '<a href="manage_users.php" class="btn btn-outline-primary mb-2"><i class="bi bi-people"></i> Manage Users</a> ';
  echo '<a href="system_settings.php" class="btn btn-outline-secondary mb-2"><i class="bi bi-gear"></i> System Settings</a>';

  break;


   case 'HR':
    echo '<div class="dashboard-section">';
    echo '<h3>📋 HR Dashboard</h3>';
    echo '<p class="text-muted">You can manage appraisals, goals, feedback, and reports.</p>';
    echo '<div class="row mb-4">';

    // Appraisals
    echo '
    <div class="col-md-4">
        <div class="card bg-success text-white mb-3 shadow-sm">
            <div class="card-header"><i class="bi bi-journal-text"></i> View Appraisals</div>
            <div class="card-body">
                <h5 class="card-title">Appraisal History</h5>
                <p class="card-text">Review performance records and insights.</p>
                <a href="view_appraisals.php" class="btn btn-light btn-sm">View</a>
            </div>
        </div>
    </div>';

    // Add Employee
    echo '
    <div class="col-md-4">
        <div class="card bg-primary text-white mb-3 shadow-sm">
            <div class="card-header"><i class="bi bi-person-plus"></i> Add Employee</div>
            <div class="card-body">
                <h5 class="card-title">Register Staff</h5>
                <p class="card-text">Create accounts for new hires or transfers.</p>
                <a href="add_employee.php" class="btn btn-light btn-sm">Add</a>
            </div>
        </div>
    </div>';

    // Feedback
    echo '
    <div class="col-md-4">
        <div class="card bg-info text-white mb-3 shadow-sm">
            <div class="card-header"><i class="bi bi-chat-dots"></i> Feedback</div>
            <div class="card-body">
                <h5 class="card-title">Give Feedback</h5>
                <p class="card-text">Share HR notes or observations.</p>
                <a href="feedback.php" class="btn btn-light btn-sm">Write</a>
            </div>
        </div>
    </div>';

    // View Submitted Feedback
    echo '
    <div class="col-md-4">
        <div class="card bg-secondary text-white mb-3 shadow-sm">
            <div class="card-header"><i class="bi bi-search"></i> Submitted Feedback</div>
            <div class="card-body">
                <h5 class="card-title">Archived Notes</h5>
                <p class="card-text">Search or analyze previously given input.</p>
                <a href="view_feedback_given.php" class="btn btn-light btn-sm">Browse</a>
            </div>
        </div>
    </div>';

    // Appraisal Cycles
    echo '
    <div class="col-md-4">
        <div class="card bg-warning text-dark mb-3 shadow-sm">
            <div class="card-header"><i class="bi bi-calendar-event"></i> Appraisal Cycles</div>
            <div class="card-body">
                <h5 class="card-title">Manage Periods</h5>
                <p class="card-text">Create and track appraisal timelines.</p>
                <a href="appraisal_cycles.php" class="btn btn-dark btn-sm">Configure</a>
            </div>
        </div>
    </div>';

    // Individual Goals
    echo '
    <div class="col-md-4">
        <div class="card border-success mb-3 shadow-sm">
            <div class="card-header text-success"><i class="bi bi-bullseye"></i> Individual Goals</div>
            <div class="card-body">
                <h5 class="card-title">Assign Goals</h5>
                <p class="card-text">Set personal targets for staff.</p>
                <a href="assign_goals.php" class="btn btn-success btn-sm">Assign</a>
            </div>
        </div>
    </div>';

    // Departmental Goals
    echo '
    <div class="col-md-4">
        <div class="card border-primary mb-3 shadow-sm">
            <div class="card-header text-primary"><i class="bi bi-building"></i> Departmental Goals</div>
            <div class="card-body">
                <h5 class="card-title">Set Team Objectives</h5>
                <p class="card-text">Define goals by unit or department.</p>
                <a href="assign_department_goals.php" class="btn btn-primary btn-sm">Assign</a>
            </div>
        </div>
    </div>';

    // View Goal Progress
    echo '
    <div class="col-md-4">
        <div class="card border-info mb-3 shadow-sm">
            <div class="card-header text-info"><i class="bi bi-graph-up"></i> Goal Progress</div>
            <div class="card-body">
                <h5 class="card-title">Monitor Status</h5>
                <p class="card-text">Track how goals are progressing.</p>
                <a href="view_goals_progress.php" class="btn btn-info btn-sm">Track</a>
            </div>
        </div>
    </div>';

    // Generate Reports
    echo '
    <div class="col-md-4">
        <div class="card border-dark mb-3 shadow-sm">
            <div class="card-header text-dark"><i class="bi bi-file-earmark-text"></i> Generate Reports</div>
            <div class="card-body">
                <h5 class="card-title">Create Performance Sheets</h5>
                <p class="card-text">Build summaries for internal reviews.</p>
                <a href="generate_report.php" class="btn btn-dark btn-sm">Generate</a>
            </div>
        </div>
    </div>';

    // Export Appraisals
    echo '
    <div class="col-md-4">
        <div class="card border-success mb-3 shadow-sm">
            <div class="card-header text-success"><i class="bi bi-file-earmark-excel"></i> Export Appraisals</div>
            <div class="card-body">
                <h5 class="card-title">Download Data</h5>
                <p class="card-text">Export appraisals to Excel format.</p>
                <a href="export_excel.php" class="btn btn-success btn-sm">Download</a>
            </div>
        </div>
    </div>';

    // Register New User
    echo '
    <div class="col-md-4">
        <div class="card border-warning mb-3 shadow-sm">
            <div class="card-header text-warning"><i class="bi bi-person-plus-fill"></i> Register New User</div>
            <div class="card-body">
                <h5 class="card-title">Onboard Staff</h5>
                <p class="card-text">Add users with role and permissions.</p>
                <a href="register.php" class="btn btn-warning btn-sm">Register</a>
            </div>
        </div>
    </div>';

    echo '</div>'; // end .row
    echo '</div>'; // end .dashboard-section
    break;



        case 'Supervisor':
            echo "<h3>📈 Supervisor Dashboard</h3>";
            echo "<p>You can submit evaluations, give feedback, and monitor your team's progress.</p>";
            echo '<a href="submit_appraisal.php" class="btn btn-primary"><i class="bi bi-pencil-square"></i> Submit Appraisal</a>';
            echo '<a href="feedback.php" class="btn btn-info"><i class="bi bi-chat-dots"></i> Give Feedback</a>';
            echo '<a href="view_goals.php" class="btn btn-success"><i class="bi bi-bullseye"></i> View Assigned Goals</a>';
            echo '<a href="view_goals_progress.php" class="btn btn-warning"><i class="bi bi-graph-up"></i> Track Goal Progress</a>';
            echo '<a href="view_responses.php" class="btn btn-secondary"><i class="bi bi-search"></i> View Feedback Responses</a>';
            break;

        case 'Employee':
    echo '<div class="container mt-4">';

    echo "<h3>👤 Employee Dashboard</h3>";
    echo "<p>You can view your appraisal history and feedback.</p>";

    echo '<div class="mb-3">';
    echo '<a href="view_history.php" class="btn btn-primary me-2"><i class="bi bi-journal-text"></i> View Appraisal History</a>';
    echo '<a href="view_feedback.php" class="btn btn-info me-2"><i class="bi bi-chat-dots"></i> View Feedback</a>';
    echo '<a href="download_summary.php" class="btn btn-success me-2"><i class="bi bi-download"></i> Download Summary</a>';
    echo '<a href="respond_feedback.php" class="btn btn-warning me-2"><i class="bi bi-check-circle"></i> Respond to Feedback</a>';
    echo '<a href="view_cycles.php" class="btn btn-outline-primary me-2"><i class="bi bi-calendar"></i> View Appraisal Cycles</a>';
    echo '<a href="logout.php" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to log out?\')"><i class="bi bi-box-arrow-right"></i> Logout</a>';
    echo '</div>';

    echo '<form method="GET" action="search_feedback.php" class="mt-4">';
    echo '<div class="input-group">';
    echo '<input type="text" name="query" class="form-control" placeholder="Search feedback by keyword or date (YYYY-MM-DD)" required>';
    echo '<button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i> Search</button>';
    echo '</div>';
    echo '</form>';

    echo '</div>'; // close container
    break;


        default:
            echo "<p class='text-danger'>Unknown role. Please contact the administrator.</p>";
    }
    ?>
    </div>

    
</div>

<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
