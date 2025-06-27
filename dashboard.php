<?php
session_start();
require('../includes/config.php');


// Check admin login session (adjust if you use a different session variable)
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Total employees count
$sql = "SELECT COUNT(*) FROM employees";
$result = $conn->query($sql);
$totalEmployees = $result->fetch_row()[0];

// Present today count (assuming 'attendance' table has 'date' and 'status')
$today = date('Y-m-d');
$sql = "SELECT COUNT(*) FROM attendance WHERE date = ? AND status = 'Present'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$stmt->bind_result($presentToday);
$stmt->fetch();
$stmt->close();

// Pending leaves count (using 'leaves' table)
$sql = "SELECT COUNT(*) FROM leaves WHERE status = 'Pending'";
$result = $conn->query($sql);
$pendingLeaves = $result->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>
<?php 
require('../includes/admnav.php');
?>
<header>
  <h1>Admin Dashboard</h1>
</header>

<main>
  <section id="dashboard-section">
    <h2>Welcome, Admin!</h2>
    <div class="dashboard-cards">
      <div class="card">
        <h3>Total Employees</h3>
        <p id="totalEmployees"><?= $totalEmployees ?></p>
      </div>
      <div class="card">
        <h3>Present Today</h3>
        <p id="presentToday"><?= $presentToday ?></p>
      </div>
      <div class="card">
        <h3>Pending Leaves</h3>
        <p id="pendingLeaves"><?= $pendingLeaves ?></p>
      </div>
    </div>

    <div class="quick-links">
      <h3>Quick Actions</h3>
      <ul>
        <li><a href="add_employee.php">Add New Employee</a></li>
        <li><a href="attendance.php">Mark Attendance</a></li>
        <li><a href="leave_requests.php">Review Leave Requests</a></li>
        <li><a href="reports.php">Generate Reports</a></li>
      </ul>
    </div>
  </section>
</main>

<?php require('../includes/footer.php'); ?>

</body>
</html>
