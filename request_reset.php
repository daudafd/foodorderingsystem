<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // 1. Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users_info WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Email not found.";
        header("Location: request_reset.php");
        exit;
    }

    $user = $result->fetch_assoc();
    $user_id = 27;
    $stmt->close();

    // 2. Generate and store token
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now

    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $token, $expires_at);

    if (!$stmt->execute()) {
        $_SESSION['error'] = "Error storing reset token: " . $stmt->error;
        header("Location: request_reset.php");
        exit;
    }
    $stmt->close();

    // 3. Success message (No email sending)
    $_SESSION['success'] = "Password reset initiated.  You can now reset your password using the link below (for testing purposes).";  // Informative message
    $reset_link = "https://kosibound.com.nh/reset_password.php?token=" . $token; // Display link for testing
    $_SESSION['reset_link'] = $reset_link; // Store it in the session to show it on the page
    header("Location: request_reset.php"); // Redirect back to the form
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Request</title>
</head>
<body>
    <h1>Password Reset</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
        <p>Reset Link: <a href="<?php echo $_SESSION['reset_link']; ?>"><?php echo $_SESSION['reset_link']; ?></a></p>
        <?php 
        unset($_SESSION['success']); 
        unset($_SESSION['reset_link']);
        ?>
    <?php endif; ?>

    <form method="post">
        Email: <input type="email" name="email" required><br>
        <button type="submit">Request Reset</button>
    </form>
</body>
</html>