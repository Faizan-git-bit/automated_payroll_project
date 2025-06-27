<?php
session_start();
require('../includes/config.php');

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$reportHtml = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['reportType'] ?? '';
    $from = $_POST['fromDate'] ?? '';
    $to = $_POST['toDate'] ?? '';

    if ($type && $from && $to) {
        // Sanitize dates
        $fromDate = date('Y-m-d', strtotime($from));
        $toDate = date('Y-m-d', strtotime($to));

        if ($type === 'salary') {
            // Example: salary paid between from and to
            $sql = "SELECT e.full_name, s.salary_month, s.amount 
                    FROM salaries s 
                    JOIN employees e ON s.employee_id = e.id 
                    WHERE s.salary_month BETWEEN ? AND ? 
                    ORDER BY s.salary_month DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $fromDate, $toDate);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $reportHtml .= "<h3>Salary Report</h3>";
                $reportHtml .= "<table border='1' cellpadding='8'><thead><tr><th>Employee</th><th>Salary Month</th><th>Amount</th></tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    $reportHtml .= "<tr>
                        <td>" . htmlspecialchars($row['full_name']) . "</td>
                        <td>" . htmlspecialchars($row['salary_month']) . "</td>
                        <td>$" . number_format($row['amount'], 2) . "</td>
                    </tr>";
                }
                $reportHtml .= "</tbody></table>";
            } else {
                $reportHtml = "<p>No salary records found for this period.</p>";
            }

            $stmt->close();

        } elseif ($type === 'attendance') {
            // Attendance records between dates
            $sql = "SELECT e.full_name, a.date, a.status, a.remarks
                    FROM attendance a 
                    JOIN employees e ON a.employee_id = e.id
                    WHERE a.date BETWEEN ? AND ?
                    ORDER BY a.date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $fromDate, $toDate);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $reportHtml .= "<h3>Attendance Report</h3>";
                $reportHtml .= "<table border='1' cellpadding='8'><thead><tr><th>Employee</th><th>Date</th><th>Status</th><th>Remarks</th></tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    $reportHtml .= "<tr>
                        <td>" . htmlspecialchars($row['full_name']) . "</td>
                        <td>" . htmlspecialchars($row['date']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>" . htmlspecialchars($row['remarks']) . "</td>
                    </tr>";
                }
                $reportHtml .= "</tbody></table>";
            } else {
                $reportHtml = "<p>No attendance records found for this period.</p>";
            }

            $stmt->close();

        } elseif ($type === 'leave') {
            // Leave requests between dates
            $sql = "SELECT e.full_name, l.leave_type, l.from_date, l.to_date, l.reason, l.status, l.applied_on
                    FROM leaves l
                    JOIN employees e ON l.employee_id = e.id
                    WHERE l.applied_on BETWEEN ? AND ?
                    ORDER BY l.applied_on DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $fromDate, $toDate);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $reportHtml .= "<h3>Leave Report</h3>";
                $reportHtml .= "<table border='1' cellpadding='8'><thead><tr>
                    <th>Employee</th><th>Leave Type</th><th>From</th><th>To</th><th>Reason</th><th>Status</th><th>Applied On</th>
                    </tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    $reportHtml .= "<tr>
                        <td>" . htmlspecialchars($row['full_name']) . "</td>
                        <td>" . htmlspecialchars($row['leave_type']) . "</td>
                        <td>" . htmlspecialchars($row['from_date']) . "</td>
                        <td>" . htmlspecialchars($row['to_date']) . "</td>
                        <td>" . htmlspecialchars($row['reason']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>" . htmlspecialchars($row['applied_on']) . "</td>
                    </tr>";
                }
                $reportHtml .= "</tbody></table>";
            } else {
                $reportHtml = "<p>No leave records found for this period.</p>";
            }

            $stmt->close();

        } else {
            $reportHtml = "<p>Invalid report type selected.</p>";
        }
    } else {
        $reportHtml = "<p>Please fill all fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reports | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>

<header>
  <h1>Reports</h1>
  <?php require('../includes/admnav.php'); ?>
</header>

<main>
  <section id="reports-section">
    <h2>Generate Reports</h2>
    <form method="POST" id="reportForm">
      <label for="reportType">Select Report Type:</label>
      <select id="reportType" name="reportType" required>
        <option value="">Choose</option>
        <option value="salary" <?= (isset($type) && $type === 'salary') ? 'selected' : '' ?>>Salary Report</option>
        <option value="attendance" <?= (isset($type) && $type === 'attendance') ? 'selected' : '' ?>>Attendance Report</option>
        <option value="leave" <?= (isset($type) && $type === 'leave') ? 'selected' : '' ?>>Leave Report</option>
      </select>

      <label for="fromDate">From:</label>
      <input type="date" id="fromDate" name="fromDate" required value="<?= htmlspecialchars($from ?? '') ?>">

      <label for="toDate">To:</label>
      <input type="date" id="toDate" name="toDate" required value="<?= htmlspecialchars($to ?? '') ?>">

      <button type="submit" class="btn">Generate Report</button>
    </form>

    <div id="reportResult" style="margin-top: 20px;">
      <?= $reportHtml ?>
    </div>
  </section>
</main>

<?php require('../includes/footer.php'); ?>

</body>
</html>
