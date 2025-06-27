<?php
session_start();
require('../includes/config.php');


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['empID'] ?? '';
    $attendance_date = $_POST['attendanceDate'] ?? '';
    $status = $_POST['status'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');

    if ($employee_id && $attendance_date && $status) {
        $checkEmp = $conn->prepare("SELECT id FROM employees WHERE id = ?");
        $checkEmp->bind_param("i", $employee_id);
        $checkEmp->execute();
        $checkEmp->store_result();

        if ($checkEmp->num_rows === 0) {
            $message = "Employee ID not found.";
        } else {
            $checkEmp->close();

            $stmt = $conn->prepare("INSERT INTO attendance (employee_id, date, status, remarks) VALUES (?, ?, ?, ?)
                                    ON DUPLICATE KEY UPDATE status = VALUES(status), remarks = VALUES(remarks)");
            $stmt->bind_param("isss", $employee_id, $attendance_date, $status, $remarks);

            if ($stmt->execute()) {
                $message = "Attendance marked successfully.";
            } else {
                $message = "Error marking attendance: " . $stmt->error;
            }
            $stmt->close();
        }
        $checkEmp->close();
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<header>
  <h1>Employee Attendance</h1>
  <?php require('../includes/admnav.php'); ?>
</header>

<main>
  <section id="attendance-section">
    <h2>Mark Attendance</h2>

    <?php if ($message): ?>
      <p style="color: <?= strpos($message, 'Error') !== false ? 'red' : 'green' ?>; font-weight: bold;">
        <?= htmlspecialchars($message) ?>
      </p>
    <?php endif; ?>

    <form method="POST" id="attendanceForm">
      <label for="empID">Employee ID:</label>
      <input type="number" id="empID" name="empID" required>

      <label for="attendanceDate">Date:</label>
      <input type="date" id="attendanceDate" name="attendanceDate" required>

      <label for="status">Status:</label>
      <select id="status" name="status" required>
        <option value="">Select Status</option>
        <option value="Present">Present</option>
        <option value="Absent">Absent</option>
        <option value="Half-Day">Half-Day</option>
        <option value="Remote">Remote</option>
      </select>

      <label for="remarks">Remarks (Optional):</label>
      <input type="text" id="remarks" name="remarks" maxlength="255">

      <button type="submit" class="btn">Mark Attendance</button>
    </form>
  </section>
</main>

<?php require('../includes/footer.php'); ?>

<script>
document.getElementById("attendanceForm").addEventListener("submit", function(e) {
  const empId = document.getElementById("empID").value.trim();
  const date = document.getElementById("attendanceDate").value;
  const status = document.getElementById("status").value;

  if (!empId || !date || !status) {
    e.preventDefault();
    alert("Please fill in all fields.");
  }
});
</script>

</body>
</html>
