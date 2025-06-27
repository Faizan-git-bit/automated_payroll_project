<?php
session_start();
include 'includes/config.php';

$forgotMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim(strtolower($_POST['email'] ?? ''));

    if (!$email) {
        $forgotMessage = "Please enter your email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $forgotMessage = "Invalid email format.";
    } else {
        // Check if the email exists
        $sql = "SELECT * FROM users WHERE LOWER(email) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Simulate sending reset link
            $forgotMessage = "Password reset link sent to your email!";
            // You can implement actual mailing functionality using PHPMailer or mail()
        } else {
            $forgotMessage = "No user found with this email.";
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
  <title>Forgot Password | Payroll System</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

  <header>
    <h1>Forgot Password</h1>
    <?php require('includes/nav.php'); ?>
  </header>

  <main>
    <section>
      <h2>Reset your password</h2>

      <?php if (!empty($forgotMessage)): ?>
        <p id="forgotPasswordMessage" style="color: <?= strpos($forgotMessage, 'sent') !== false ? 'green' : 'red'; ?>">
          <?= htmlspecialchars($forgotMessage) ?>
        </p>
        <?php if (strpos($forgotMessage, 'sent') !== false): ?>
          <script>
            setTimeout(() => window.location.href = "login.php", 2000);
          </script>
        <?php endif; ?>
      <?php else: ?>
        <p id="forgotPasswordMessage" style="color: red;"></p>
      <?php endif; ?>

      <form id="forgotPasswordForm" method="POST" action="forgot_password.php" onsubmit="return validateForgotForm()">
        <label for="email">Enter your email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required><br><br>

        <button type="submit">Submit</button>
      </form>
    </section>
  </main>

  <?php require('includes/footer.php'); ?>

  <script>
    function validateForgotForm() {
      const email = document.getElementById("email").value.trim();
      const message = document.getElementById("forgotPasswordMessage");
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!email) {
        message.textContent = "Please enter your email.";
        return false;
      }

      if (!emailPattern.test(email)) {
        message.textContent = "Please enter a valid email address.";
        return false;
      }

      return true;
    }
  </script>

</body>
</html>
