<?php
session_start();
require('../includes/config.php');

if (!isset($_SESSION['employee_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$employee_id = $_SESSION['employee_id'];

// Get the latest salary record
$stmt = $conn->prepare("SELECT salary_month, salary_year, basic, hra, bonus, deductions, net_pay 
                        FROM salaries 
                        WHERE employee_id = ? 
                        ORDER BY salary_year DESC, salary_month DESC 
                        LIMIT 1");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$salary_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Salary Slip | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<header>
  <h1>My Salary Slip</h1>
  <?php require('../includes/empnav.php'); ?>
</header>

<main>
  <section>
    <?php if ($salary_data): ?>
      <h2>Salary Details for <?php echo date("F", mktime(0, 0, 0, $salary_data['salary_month'], 10)); ?> <?php echo $salary_data['salary_year']; ?></h2>
      <table border="1" cellpadding="8" cellspacing="0">
        <tr><th>Component</th><th>Amount (₹)</th></tr>
        <tr><td>Basic Salary</td><td><?php echo number_format($salary_data['basic'], 2); ?></td></tr>
        <tr><td>HRA</td><td><?php echo number_format($salary_data['hra'], 2); ?></td></tr>
        <tr><td>Bonus</td><td><?php echo number_format($salary_data['bonus'], 2); ?></td></tr>
        <tr><td>Tax Deductions</td><td>-<?php echo number_format($salary_data['deductions'], 2); ?></td></tr>
        <tr><th>Net Pay</th><th>₹<?php echo number_format($salary_data['net_pay'], 2); ?></th></tr>
      </table>
    <?php else: ?>
      <p>No salary record found.</p>
    <?php endif; ?>
  </section>
</main>

<?php require('../includes/footer.php'); ?>

</body>
</html>
