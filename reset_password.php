<?php
session_start();
include 'db_connect.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // 1. Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Invalid or expired token.";
        exit;
    }

    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            echo "Passwords do not match.";
            exit;
        }

        // 2. Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // 3. Update user's password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            // 4. Delete the token (optional but good practice)
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();

            echo "Password updated successfully.";
        } else {
            echo "Error updating password: " . $stmt->error;
        }
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reset Password</title>
        </head>
        <body>
            <h1>Reset Password</h1>
            <form method="post">
                New Password: <input type="password" name="new_password" required><br>
                Confirm Password: <input type="password" name="confirm_password" required><br>
                <button type="submit">Update Password</button>
            </form>
        </body>
        </html>
        <?php
    }
} else {
    echo "No token provided.";
}

$conn->close();
?>