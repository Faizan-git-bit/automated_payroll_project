<?php
session_start();
require('../includes/config.php');

if (!isset($_SESSION['employee_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$employee_id = $_SESSION['employee_id'];

// Fetch attendance records
$stmt = $conn->prepare("SELECT attendance_date, status, remarks FROM attendance WHERE employee_id = ? ORDER BY attendance_date DESC");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Attendance | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<header>
  <h1>My Attendance</h1>
  <?php require('../includes/empnav.php'); ?>
</header>

<main>
  <section>
    <h2>Attendance Record</h2>
    <table border="1" cellpadding="8" cellspacing="0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Status</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['attendance_date']); ?></td>
              <td><?php echo htmlspecialchars($row['status']); ?></td>
              <td><?php echo htmlspecialchars($row['remarks'] ?? '-'); ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="3">No attendance records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</main>

<?php require('../includes/footer.php'); ?>

</body>
</html>
