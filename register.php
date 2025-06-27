<?php
session_start();
include 'includes/config.php';

$registerError = "";
$registerSuccess = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (!$fullName || !$email || !$password || !$confirmPassword) {
        $registerError = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registerError = "Invalid email format.";
    } elseif ($password !== $confirmPassword) {
        $registerError = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $registerError = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $sql = "SELECT * FROM users WHERE LOWER(email) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $registerError = "Email already registered.";
        } else {
            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertSql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("sss", $fullName, $email, $hashedPassword);
            if ($insertStmt->execute()) {
                $registerSuccess = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $registerError = "Registration failed. Please try again.";
            }
            $insertStmt->close();
        }
        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | Payroll System</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

  <header>
    <h1>Employee Registration</h1>
    <?php require('includes/nav.php'); ?>
  </header>

  <main>
    <section>
      <h2>Create your account</h2>

      <?php if (!empty($registerError)): ?>
        <p id="registerMessage" style="color: red;"><?php echo htmlspecialchars($registerError); ?></p>
      <?php elseif (!empty($registerSuccess)): ?>
        <p id="registerMessage" style="color: green;"><?php echo htmlspecialchars($registerSuccess); ?></p>
      <?php else: ?>
        <p id="registerMessage" style="color: red;"></p>
      <?php endif; ?>

      <form id="registerForm" method="POST" action="register.php" onsubmit="return validateRegisterForm()">
        <label for="fullName">Full Name:</label>
        <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter a password" required><br><br>

        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required><br><br>

        <button type="submit">Register</button>
      </form>
    </section>
  </main>

  <?php require('includes/footer.php'); ?>

  <script>
    function validateRegisterForm() {
      const fullName = document.getElementById("fullName").value.trim();
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value.trim();
      const confirmPassword = document.getElementById("confirmPassword").value.trim();
      const message = document.getElementById("registerMessage");

      message.textContent = "";

      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!fullName || !email || !password || !confirmPassword) {
        message.textContent = "Please fill in all fields.";
        return false;
      }

      if (!emailPattern.test(email)) {
        message.textContent = "Invalid email format.";
        return false;
      }

      if (password.length < 6) {
        message.textContent = "Password must be at least 6 characters long.";
        return false;
      }

      if (password !== confirmPassword) {
        message.textContent = "Passwords do not match.";
        return false;
      }

      return true;
    }
  </script>

</body>
</html>
