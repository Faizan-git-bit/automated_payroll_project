<!-- index.html -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Automated Payroll Management System</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

  <header>
    <h1>Welcome to the Automated Payroll Management System</h1>
   <?php  require('includes/nav.php');?>
  </header>

  <main>
    <section>
      <h2>About Our System</h2>
      <p>
        Our Automated Payroll Management System is designed to streamline the payroll process,
        improve accuracy, and provide transparency for both employees and management.
        Whether you're an employee managing your attendance and salary or an admin handling the entire payroll system, our platform makes it easy.
      </p>
    </section>

    <section>
      <h2>Key Features</h2>
      <ul>
        <li><strong>Employee Management:</strong> Easily manage employee details and records.</li>
        <li><strong>Attendance Tracking:</strong> Accurately track and manage employee attendance.</li>
        <li><strong>Leave Management:</strong> Apply and approve leave requests seamlessly.</li>
        <li><strong>Payroll Generation:</strong> Automatically calculate and manage payroll for employees.</li>
      </ul>
    </section>

    <section>
      <h2>Getting Started</h2>
      <p>To get started, simply <a href="login.php">Login</a> or <a href="register.php">Register</a> to access the system.</p>
    </section>
  </main>
<?php  require('includes/footer.php');?>
</body>
</html>
