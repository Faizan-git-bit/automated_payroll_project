<?php
session_start();
require('../includes/config.php');

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $empID = trim($_POST['empID']);
  $basic = floatval($_POST['basicSalary']);
  $bonus = floatval($_POST['bonus'] ?? 0);
  $deduction = floatval($_POST['deduction'] ?? 0);
  
  // Calculate HRA as 20% of basic
  $hra = $basic * 0.20;
  $netPay = $basic + $hra + $bonus - $deduction;
  $netPay = max(0, round($netPay, 2)); // Avoid negative values

  $month = date('m');
  $year = date('Y');
  $generated_on = date('Y-m-d H:i:s');

  // Check if employee exists
  $check = $conn->prepare("SELECT id FROM employees WHERE id = ?");
  $check->bind_param("s", $empID);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    // Insert into updated salaries table
    $stmt = $conn->prepare("INSERT INTO salaries (employee_id, salary_month, salary_year, basic, hra, bonus, deductions, net_pay, generated_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiddddds", $empID, $month, $year, $basic, $hra, $bonus, $deduction, $netPay, $generated_on);

    if ($stmt->execute()) {
      $message = "✅ Salary generated successfully for Employee ID: $empID";
    } else {
      $message = "❌ Failed to insert salary record.";
    }

    $stmt->close();
  } else {
    $message = "❌ Employee ID not found.";
  }

  $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Salary Management | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<header>
  <h1>Salary Management</h1>
  <?php require('../includes/admnav.php'); ?>
</header>

<main>
  <section id="salary-section">
    <h2>Process Employee Salaries</h2>
    
    <?php if ($message): ?>
      <p style="color: <?= strpos($message, '✅') !== false ? 'green' : 'red' ?>; font-weight: bold;"><?= $message ?></p>
    <?php endif; ?>
    
    <form id="salaryForm" method="POST">
      <label for="empID">Employee ID:</label>
      <input type="text" id="empID" name="empID" required>

      <label for="basicSalary">Basic Salary:</label>
      <input type="number" id="basicSalary" name="basicSalary" required>

      <label for="bonus">Bonus:</label>
      <input type="number" id="bonus" name="bonus">

      <label for="deduction">Deductions:</label>
      <input type="number" id="deduction" name="deduction">

      <label for="netSalary">Net Pay:</label>
      <input type="number" id="netSalary" name="netSalary" readonly>

      <button type="submit" class="btn">Generate Salary</button>
    </form>
  </section>
</main>

<?php require('../includes/footer.php'); ?>

<script>
  const basic = document.getElementById('basicSalary');
  const bonus = document.getElementById('bonus');
  const deduction = document.getElementById('deduction');
  const net = document.getElementById('netSalary');

  function calculateNetSalary() {
    const basicVal = parseFloat(basic.value) || 0;
    const bonusVal = parseFloat(bonus.value) || 0;
    const deductionVal = parseFloat(deduction.value) || 0;
    const hra = basicVal * 0.20;
    const netPay = basicVal + hra + bonusVal - deductionVal;
    net.value = netPay >= 0 ? netPay.toFixed(2) : 0;
  }

  basic.addEventListener('input', calculateNetSalary);
  bonus.addEventListener('input', calculateNetSalary);
  deduction.addEventListener('input', calculateNetSalary);
</script>

</body>
</html>
