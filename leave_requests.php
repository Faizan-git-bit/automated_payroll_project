<?php
session_start();
require('../includes/config.php');

// Admin session check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Handle approve/reject POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_id'], $_POST['action'])) {
    $leave_id = intval($_POST['leave_id']);
    $action = ($_POST['action'] === 'approve') ? 'Approved' : (($_POST['action'] === 'reject') ? 'Rejected' : '');

    if ($action) {
        $stmt = $conn->prepare("UPDATE leaves SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $leave_id);
        $stmt->execute();
        $stmt->close();
        $msg = "Leave request has been $action.";
    }
}

// Fetch pending leaves with employee names
$sql = "SELECT l.id, l.employee_id, e.full_name, l.leave_type, l.from_date, l.to_date, l.reason, l.status, l.applied_on
        FROM leaves l
        JOIN employees e ON l.employee_id = e.id
        WHERE l.status = 'Pending'
        ORDER BY l.applied_on DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Leave Requests | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>
  <header>
    <h1>Employee Leave Requests</h1>
    <?php require('../includes/admnav.php'); ?>
  </header>

  <main>
    <section id="leave-requests-section">
      <h2>Pending Leave Requests</h2>

      <?php if (!empty($msg)): ?>
        <p style="color: green; font-weight: bold;"><?= htmlspecialchars($msg) ?></p>
      <?php endif; ?>

      <?php if ($result && $result->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Employee ID</th>
              <th>Name</th>
              <th>Leave Type</th>
              <th>From</th>
              <th>To</th>
              <th>Reason</th>
              <th>Status</th>
              <th>Applied On</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['employee_id']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['leave_type']) ?></td>
                <td><?= htmlspecialchars($row['from_date']) ?></td>
                <td><?= htmlspecialchars($row['to_date']) ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['applied_on']) ?></td>
                <td>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="leave_id" value="<?= $row['id'] ?>" />
                    <button type="submit" name="action" value="approve" onclick="return confirm('Approve this leave request?');">Approve</button>
                  </form>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="leave_id" value="<?= $row['id'] ?>" />
                    <button type="submit" name="action" value="reject" onclick="return confirm('Reject this leave request?');">Reject</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No pending leave requests.</p>
      <?php endif; ?>
    </section>
  </main>

  <?php require('../includes/footer.php'); ?>
</body>
</html>
