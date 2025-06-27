<?php
session_start();
require('../includes/config.php');
require('../includes/empnav.php');

if (!isset($_SESSION['employee_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$employee_id = $_SESSION['employee_id'];

// Fetch employee details
$sql = "SELECT * FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<header>
  <h1>My Profile</h1>
</header>

<main>
  <section>
    <h2>Personal Information</h2>
    <table border="1" cellpadding="8" cellspacing="0">
      <tr><th>Employee ID</th><td><?= htmlspecialchars($employee['employee_code']) ?></td></tr>
      <tr><th>Name</th><td><?= htmlspecialchars($employee['name']) ?></td></tr>
      <tr><th>Email</th><td><?= htmlspecialchars($employee['email']) ?></td></tr>
      <tr><th>Phone</th><td><?= htmlspecialchars($employee['phone']) ?></td></tr>
      <tr><th>Department</th><td><?= htmlspecialchars($employee['department']) ?></td></tr>
      <tr><th>Designation</th><td><?= htmlspecialchars($employee['designation']) ?></td></tr>
      <tr><th>Date of Joining</th><td><?= htmlspecialchars($employee['date_of_joining']) ?></td></tr>
      <tr><th>Address</th><td><?= htmlspecialchars($employee['address']) ?></td></tr>
    </table>
  </section>
</main>

<?php require('../includes/footer.php'); ?>
</body>
</html>
