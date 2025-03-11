<?php
session_start();
require_once '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$googleClientId = $_ENV['GOOGLE_CLIENT_ID'];
$googleClientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
$googleRedirectUri = $_ENV['GOOGLE_REDIRECT_URI'];
$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];
$dbName = $_ENV['DB_NAME'];

$client = new Google_Client();
$client->setClientId($googleClientId);
$client->setClientSecret($googleClientSecret);
$client->setRedirectUri($googleRedirectUri);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
$client->setApplicationName("Food Ordering App");
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($token['error'])) {
            throw new Exception('Error fetching access token: ' . $token['error_description']);
        }

        if (empty($token) || !is_array($token)) {
            throw new Exception('Invalid token response');
        }

        $client->setAccessToken($token);
        $service = new Google_Service_Oauth2($client);
        $user = $service->userinfo->get();

        if (!$user) {
            throw new Exception('Failed to retrieve user information.');
        }

        $nameParts = explode(' ', $user->name, 2);
        $lastName = $nameParts[0];
        $firstName = isset($nameParts[1]) ? $nameParts[1] : '';

        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }

        $google_id = $user->id;
        $email = $user->email;

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, login_type FROM user_info WHERE email = ?");
        if ($stmt === false) {
            throw new Exception('Error preparing statement: ' . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new Exception('Error getting result: ' . $stmt->error);
        }

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            $_SESSION['login_user_id'] = $user_data['user_id'];
            $_SESSION['login_email'] = $email;
            $_SESSION['login_type'] = 'google';
            $_SESSION['login_first_name'] = $firstName;
            $_SESSION['login_last_name'] = $lastName;
            $_SESSION['login_type'] = $user_data['login_type']; // Store user type in session

            // Check if user_type is already set for Google login
            if ($user_data['login_type'] !== 'google') {
                $user_type = 3; // Default user type for new Google users
                $stmt2 = $conn->prepare("UPDATE user_info SET login_type = ? WHERE user_id = ?");
                if ($stmt2 === false) {
                    throw new Exception('Error preparing update statement: ' . $conn->error);
                }
                $stmt2->bind_param("ii", $user_type, $_SESSION['login_user_id']);
                $stmt2->execute();
                $stmt2->close();
            }

        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO user_info (google_id, email, first_name, last_name) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception('Error preparing insert statement: ' . $conn->error);
            }
            $stmt->bind_param("ssss", $google_id, $email, $firstName, $lastName);

            if (!$stmt->execute()) {
                throw new Exception('Error inserting user: ' . $stmt->error);
            }

            $_SESSION['login_user_id'] = $conn->insert_id;
            $_SESSION['login_email'] = $email;
            $_SESSION['login_type'] = 'google';
            $_SESSION['login_first_name'] = $firstName;
            $_SESSION['login_last_name'] = $lastName;
            $user_type = 3; // Default user type for new Google users
            $stmt2 = $conn->prepare("UPDATE user_info SET login_type = ? WHERE user_id = ?");
            if ($stmt2 === false) {
                throw new Exception('Error preparing update statement: ' . $conn->error);
            }
            $stmt2->bind_param("ii", $user_type, $_SESSION['login_user_id']);
            $stmt2->execute();
            $stmt2->close();
        }

        $stmt->close();
        $conn->close();

        echo "<script>window.location.href = 'index.php?page=home';</script>";
        exit();

    } catch (Google_Service_Exception $e) {
        echo '<p style="color: red;">Google API error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    } catch (Exception $e) {
        echo '<p style="color: red;">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}
?>