<?php
session_start();
require('../includes/config.php');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_code = $_POST['empCode'] ?? '';
    $full_name = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $department = $_POST['department'] ?? '';
    $designation = $_POST['designation'] ?? '';
    $joining_date = $_POST['joiningDate'] ?? '';
    $address = $_POST['address'] ?? '';

    // For demo: default password and role
    $defaultPassword = password_hash("password123", PASSWORD_DEFAULT); // Or let admin set password
    $role = 'employee';

    // Validation
    if ($emp_code && $full_name && $email && $phone) {

        // Step 1: Insert into users table
        $userSql = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
        $userStmt = $conn->prepare($userSql);
        $userStmt->bind_param("sss", $email, $defaultPassword, $role);

        if ($userStmt->execute()) {
            $user_id = $userStmt->insert_id;

            // Step 2: Insert into employees table
            $sql = "INSERT INTO employees (user_id, emp_code, full_name, email, phone, department, designation, joining_date, address) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssssss", $user_id, $emp_code, $full_name, $email, $phone, $department, $designation, $joining_date, $address);

            if ($stmt->execute()) {
                $message = "✅ Employee added successfully!";
            } else {
                $message = "❌ Error inserting employee: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "❌ Error creating user: " . $userStmt->error;
        }
        $userStmt->close();
    } else {
        $message = "⚠️ Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Employee | Payroll System</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<header>
  <h1>Add New Employee</h1>
  <?php require('../includes/admnav.php'); ?>
</header>

<main>
  <section id="add-employee-form">
    <h2>Enter Employee Details</h2>

    <?php if ($message): ?>
      <p style="color: green; font-weight: bold;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" id="employeeForm">
      <label for="empCode">Employee Code:</label>
      <input type="text" id="empCode" name="empCode" required>

      <label for="fullName">Full Name:</label>
      <input type="text" id="fullName" name="fullName" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="phone">Phone:</label>
      <input type="tel" id="phone" name="phone" required>

      <label for="department">Department:</label>
      <input type="text" id="department" name="department">

      <label for="designation">Designation:</label>
      <input type="text" id="designation" name="designation">

      <label for="joiningDate">Joining Date:</label>
      <input type="date" id="joiningDate" name="joiningDate">

      <label for="address">Address:</label>
      <textarea id="address" name="address" rows="3"></textarea>

      <button type="submit" class="btn">Add Employee</button>
    </form>
  </section>
</main>

<?php require('../includes/footer.php'); ?>

<script>
document.getElementById("employeeForm").addEventListener("submit", function(e) {
  const requiredFields = ['empCode', 'fullName', 'email', 'phone'];
  for(let field of requiredFields) {
    if(!document.getElementById(field).value.trim()) {
      e.preventDefault();
      alert("Please fill in all required fields.");
      return;
    }
  }
});
</script>

</body>
</html>
