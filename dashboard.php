<?php
session_start();
require('../includes/config.php');
require('../includes/empnav.php');
if (!isset($_SESSION['employee_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php");
    exit;
}
$employee_id = $_SESSION['employee_id'];

// Fetch name
$sql = "SELECT full_name FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();

// Get last month's salary
$salary = 0;
$sql = "SELECT net_pay FROM salaries WHERE employee_id = ? ORDER BY generated_on DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($salary);
$stmt->fetch();
$stmt->close();

// Get leaves remaining (example: out of 20 annual leaves)
$total_allowed = 20;
$sql = "SELECT COUNT(*) FROM leaves WHERE employee_id = ? AND status = 'Approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($approved_leaves);
$stmt->fetch();
$stmt->close();
$leaves = $total_allowed - $approved_leaves;

// Get attendance percentage
$sql = "SELECT 
    (SELECT COUNT(*) FROM attendance WHERE employee_id = ?) AS total_days,
    (SELECT COUNT(*) FROM attendance WHERE employee_id = ? AND status = 'Present') AS present_days";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $employee_id, $employee_id);
$stmt->execute();
$stmt->bind_result($total_days, $present_days);
$stmt->fetch();
$stmt->close();

$attendance = ($total_days > 0) ? round(($present_days / $total_days) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Dashboard | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<header>
  <h1>Employee Dashboard</h1>
</header>

<main>
  <section>
    <h2>Welcome, <?= htmlspecialchars($name) ?></h2>
    <div class="dashboard-cards">
      <div class="card">
        <h3>Last Month Salary</h3>
        <p>₹<?= number_format($salary, 2) ?></p>
      </div>
      <div class="card">
        <h3>Leaves Remaining</h3>
        <p><?= $leaves ?></p>
      </div>
      <div class="card">
        <h3>Attendance %</h3>
        <p><?= $attendance ?>%</p>
      </div>
    </div>

    <div class="quick-links">
      <h3>Quick Access</h3>
      <ul>
        <li><a href="view_attendance.php">My Attendance</a></li>
        <li><a href="view_salary.php">My Salary Slip</a></li>
        <li><a href="leave_application.php">Apply for Leave</a></li>
      </ul>
    </div>
  </section>
</main>

<?php require('../includes/footer.php'); ?>
</body>
</html>
