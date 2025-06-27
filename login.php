<?php
session_start();
include 'includes/config.php';

$loginError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM users WHERE LOWER(email) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set session data
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    $_SESSION['admin_id'] = $user['id'];
                    header("Location: admin/dashboard.php"); // ✅ fixed
                } else {
                    $_SESSION['employee_id'] = $user['id'];
                    header("Location: employee/dashboard.php"); // ✅ fixed
                }
                exit();
            } else {
                $loginError = "Incorrect password. Please try again.";
            }
        } else {
            $loginError = "No user found with that email.";
        }

        $stmt->close();
    } else {
        $loginError = "Please enter both email and password.";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Payroll System</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

  <header>
    <h1>Login</h1>
    <?php require('includes/nav.php'); ?>
  </header>

  <main>
    <section>
      <h2>Please login to your account</h2>

      <?php if (!empty($loginError)): ?>
        <p id="loginMessage" class="error"><?php echo htmlspecialchars($loginError); ?></p>
      <?php endif; ?>

      <form id="loginForm" method="POST" action="login.php" onsubmit="return validateLoginForm()">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>

        <button type="submit">Login</button>
      </form>
    </section>
  </main>

  <?php require('includes/footer.php'); ?>

  <script>
    function validateLoginForm() {
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value.trim();
      const message = document.getElementById("loginMessage");
      if (message) message.textContent = "";

      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(email)) {
        if (message) message.textContent = "Please enter a valid email address.";
        return false;
      }

      if (password.length < 6) {
        if (message) message.textContent = "Password must be at least 6 characters long.";
        return false;
      }

      return true;
    }
  </script>

</body>
</html>
