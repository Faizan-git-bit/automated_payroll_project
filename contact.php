<?php
include 'includes/config.php';

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $message = trim($_POST["message"]);

  if (!empty($name) && !empty($email) && !empty($message)) {
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    if ($stmt->execute()) {
      $success = "Your message has been sent successfully!";
    } else {
      $error = "Something went wrong. Please try again.";
    }
    $stmt->close();
  } else {
    $error = "All fields are required.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - Automated Payroll Management System</title>
  <link rel="stylesheet" href="css/styles.css"/>
  <script>
    function validateForm() {
      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const message = document.getElementById("message").value.trim();
      const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

      if (name === "" || email === "" || message === "") {
        alert("All fields are required.");
        return false;
      }

      if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return false;
      }

      if (message.length < 10) {
        alert("Message should be at least 10 characters long.");
        return false;
      }

      return true;
    }
  </script>
</head>
<body>

<header>
  <h1>Contact Us</h1>
  <?php require('includes/nav.php'); ?>
</header>

<main>
  <section>
    <h2>Get in Touch</h2>
    <p>If you have any questions, feel free to reach out to us via the contact form below or via email.</p>

    <?php if ($success): ?>
      <p class="success"><?php echo $success; ?></p>
    <?php elseif ($error): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="contact.php" method="post" onsubmit="return validateForm();">
      <label for="name">Your Name</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Your Email</label>
      <input type="email" id="email" name="email" required>

      <label for="message">Your Message</label>
      <textarea id="message" name="message" rows="4" required></textarea>

      <button type="submit">Send Message</button>
    </form>
  </section>
</main>

<?php require('includes/footer.php'); ?>
</body>
</html>
