<?php 
session_start();
require('../includes/config.php');
require('../includes/empnav.php');

if (!isset($_SESSION['employee_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$employee_id = $_SESSION['employee_id'];
$message = '';

// Fetch user_id corresponding to employee_id (assuming `id` is PK in employees table)
$user_id = null;
$stmtUser = $conn->prepare("SELECT user_id FROM employees WHERE id = ?");
if (!$stmtUser) {
    die("Prepare failed: " . $conn->error);
}
$stmtUser->bind_param("i", $employee_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
if ($rowUser = $resultUser->fetch_assoc()) {
    $user_id = $rowUser['user_id'];
}
$stmtUser->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_type = $_POST['leaveType'] ?? '';
    $from_date = $_POST['fromDate'] ?? '';
    $to_date = $_POST['toDate'] ?? '';
    $reason = trim($_POST['reason'] ?? '');

    if ($leave_type && $from_date && $to_date && $reason) {
        if ($to_date >= $from_date) {
            if ($user_id === null) {
                $message = "User ID not found, cannot submit leave application.";
            } else {
                $stmt = $conn->prepare("INSERT INTO leaves (employee_id, leave_type, from_date, to_date, reason) VALUES (?, ?, ?, ?, ?)");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                if (!$stmt->bind_param("issss", $user_id, $leave_type, $from_date, $to_date, $reason)) {
                    die("Bind failed: " . $stmt->error);
                }
                if ($stmt->execute()) {
                    $message = "Leave application submitted successfully!";
                } else {
                    $message = "Error submitting application: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $message = "To date cannot be before From date.";
        }
    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Apply Leave | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>

<header>
  <h1>Apply for Leave</h1>
</header>

<main>
  <section>
    <h2>Leave Application Form</h2>
    <form id="leaveForm" method="POST" action="">
      <label for="leaveType">Leave Type:</label>
      <select id="leaveType" name="leaveType" required>
        <option value="">Select Type</option>
        <option value="Casual">Casual</option>
        <option value="Sick">Sick</option>
        <option value="Paid">Paid</option>
      </select><br><br>

      <label for="fromDate">From:</label>
      <input type="date" id="fromDate" name="fromDate" required /><br><br>

      <label for="toDate">To:</label>
      <input type="date" id="toDate" name="toDate" required /><br><br>

      <label for="reason">Reason:</label><br>
      <textarea id="reason" name="reason" rows="4" cols="50" required></textarea><br><br>

      <button type="submit">Submit Application</button>
    </form>

    <?php if (!empty($message)): ?>
      <p style="color: green; font-weight: bold;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
  </section>
</main>

<?php require('../includes/footer.php'); ?>

<script>
document.getElementById("leaveForm").addEventListener("submit", function(event) {
  const leaveType = document.getElementById("leaveType").value;
  const fromDate = document.getElementById("fromDate").value;
  const toDate = document.getElementById("toDate").value;
  const reason = document.getElementById("reason").value;

  if (!leaveType || !fromDate || !toDate || !reason) {
    alert("Please fill in all fields.");
    event.preventDefault();
    return;
  }

  if (new Date(toDate) < new Date(fromDate)) {
    alert("To date cannot be before From date.");
    event.preventDefault();
    return;
  }
});
</script>

</body>
</html>
