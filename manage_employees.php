<?php
session_start();
require('../includes/config.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Handle delete employee request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    // You may want to add further validation or prevent deleting yourself, etc.
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    $msg = "Employee deleted successfully.";
}

// Fetch employees list
$sql = "SELECT id, full_name, email, designation, department, joining_date FROM employees ORDER BY joining_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Employees | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>

  <header>
    <h1>Manage Employees</h1>
    <?php require('../includes/admnav.php'); ?>
  </header>

  <main>
    <section id="employee-table-section">
      <h2>Employee List</h2>

      <?php if (!empty($msg)): ?>
        <p style="color: green; font-weight: bold;"><?= htmlspecialchars($msg) ?></p>
      <?php endif; ?>

      <?php if ($result && $result->num_rows > 0): ?>
        <table id="employeeTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Department</th>
              <th>Position</th>
              <th>Date Joined</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td><?= htmlspecialchars($row['designation']) ?></td>
                <td><?= htmlspecialchars($row['joining_date']) ?></td>
                <td>
                  <a href="edit_employee.php?id=<?= $row['id'] ?>">Edit</a>
                  |
                  <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>" />
                    <button type="submit" style="background:none; border:none; color:red; cursor:pointer;">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No employees found.</p>
      <?php endif; ?>
    </section>
  </main>

  <?php require('../includes/footer.php'); ?>

</body>
</html>
