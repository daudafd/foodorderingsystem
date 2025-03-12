<?php
// The following will affect login but needed for password reset email
header('Content-Type: application/json'); // Force JSON response

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

require __DIR__ . '/../vendor/autoload.php'; // Adjust path if needed

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Class Action {
    private $db;

    public function __construct() {
        ob_start();
        include 'db_connect.php'; // This should now provide a PDO connection ($conn)
        $this->db = $conn;
    }

    function __destruct() {
        // No need to close PDO explicitly, it will close automatically
        ob_end_flush();
    }

    function reset_password() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method.');
            }

            if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address.');
            }

            $email = $_POST['email']; // No need to escape with PDO

            // Check if user exists
            $stmt = $this->db->prepare("SELECT user_id, email, first_name FROM user_info WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Database error preparing statement: " . $this->db->errorInfo()[2]);
            }
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('No account found with that email.');
            }

            $user_id = $user['user_id'];
            $user_name = $user['first_name'];
            $user_email = $user['email'];
            $token = bin2hex(random_bytes(32));
            $expire_time = time() + 86400;

            // Store the reset token
            $stmt = $this->db->prepare("
                INSERT INTO password_resets (user_id, user_email, token, created_at, expires_at)
                VALUES (?, ?, ?, NOW(), FROM_UNIXTIME(?))
                ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)
            ");

            if (!$stmt) {
                throw new Exception("Database error preparing statement: " . $this->db->errorInfo()[2]);
            }

            $stmt->execute([$user_id, $user_email, $token, $expire_time]);

            $reset_link = "https://www.fifi.kosibound.com.ng/index.php?page=new_password&token=" . $token;

            // Send email using PHPMailer
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'fifi.kosibound.com.ng';
            $mail->SMTPAuth = true;
            $mail->Username = 'reset@fifi.kosibound.com.ng';
            $mail->Password = '@Amafemolar.1'; // **Important:** Do not hardcode passwords!
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('reset@fifi.kosibound.com.ng', 'Fifi Cuisine');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset Request';
            // $mail->Body = "Click the link below to reset your password:\n\n$reset_link\n\nThis link expires in 1 hour.";
            $mail->isHTML(true);

            $mail->Body = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
              <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <meta name="x-apple-disable-message-reformatting" />
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <meta name="color-scheme" content="light dark" />
                <meta name="supported-color-schemes" content="light dark" />
                <title></title>
                <style type="text/css" rel="stylesheet" media="all">
                /* Base ------------------------------ */
                
                @import url("https://fonts.googleapis.com/css?family=Nunito+Sans:400,700&display=swap");
                body {
                  width: 100% !important;
                  height: 100%;
                  margin: 0;
                  -webkit-text-size-adjust: none;
                }
                
                a {
                  color: #3869D4;
                }
                
                a img {
                  border: none;
                }
                
                td {
                  word-break: break-word;
                }
                
                .preheader {
                  display: none !important;
                  visibility: hidden;
                  mso-hide: all;
                  font-size: 1px;
                  line-height: 1px;
                  max-height: 0;
                  max-width: 0;
                  opacity: 0;
                  overflow: hidden;
                }
                /* Type ------------------------------ */
                
                body,
                td,
                th {
                  font-family: "Nunito Sans", Helvetica, Arial, sans-serif;
                }
                
                h1 {
                  margin-top: 0;
                  color: #333333;
                  font-size: 22px;
                  font-weight: bold;
                  text-align: left;
                }
                
                h2 {
                  margin-top: 0;
                  color: #333333;
                  font-size: 16px;
                  font-weight: bold;
                  text-align: left;
                }
                
                h3 {
                  margin-top: 0;
                  color: #333333;
                  font-size: 14px;
                  font-weight: bold;
                  text-align: left;
                }
                
                td,
                th {
                  font-size: 16px;
                }
                
                p,
                ul,
                ol,
                blockquote {
                  margin: .4em 0 1.1875em;
                  font-size: 16px;
                  line-height: 1.625;
                }
                
                p.sub {
                  font-size: 13px;
                }
                /* Utilities ------------------------------ */
                
                .align-right {
                  text-align: right;
                }
                
                .align-left {
                  text-align: left;
                }
                
                .align-center {
                  text-align: center;
                }
                
                .u-margin-bottom-none {
                  margin-bottom: 0;
                }
                /* Buttons ------------------------------ */
                
                .button {
                  background-color: #3869D4;
                  border-top: 10px solid #3869D4;
                  border-right: 18px solid #3869D4;
                  border-bottom: 10px solid #3869D4;
                  border-left: 18px solid #3869D4;
                  display: inline-block;
                  color: #FFF;
                  text-decoration: none;
                  border-radius: 3px;
                  box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
                  -webkit-text-size-adjust: none;
                  box-sizing: border-box;
                }
                
                .button--green {
                  background-color: #22BC66;
                  border-top: 10px solid #22BC66;
                  border-right: 18px solid #22BC66;
                  border-bottom: 10px solid #22BC66;
                  border-left: 18px solid #22BC66;
                }
                
                .button--red {
                  background-color: #FF6136;
                  border-top: 10px solid #FF6136;
                  border-right: 18px solid #FF6136;
                  border-bottom: 10px solid #FF6136;
                  border-left: 18px solid #FF6136;
                }
                
                @media only screen and (max-width: 500px) {
                  .button {
                    width: 100% !important;
                    text-align: center !important;
                  }
                }
                /* Attribute list ------------------------------ */
                
                .attributes {
                  margin: 0 0 21px;
                }
                
                .attributes_content {
                  background-color: #F4F4F7;
                  padding: 16px;
                }
                
                .attributes_item {
                  padding: 0;
                }
                /* Related Items ------------------------------ */
                
                .related {
                  width: 100%;
                  margin: 0;
                  padding: 25px 0 0 0;
                  -premailer-width: 100%;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                }
                
                .related_item {
                  padding: 10px 0;
                  color: #CBCCCF;
                  font-size: 15px;
                  line-height: 18px;
                }
                
                .related_item-title {
                  display: block;
                  margin: .5em 0 0;
                }
                
                .related_item-thumb {
                  display: block;
                  padding-bottom: 10px;
                }
                
                .related_heading {
                  border-top: 1px solid #CBCCCF;
                  text-align: center;
                  padding: 25px 0 10px;
                }
                /* Discount Code ------------------------------ */
                
                .discount {
                  width: 100%;
                  margin: 0;
                  padding: 24px;
                  -premailer-width: 100%;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                  background-color: #F4F4F7;
                  border: 2px dashed #CBCCCF;
                }
                
                .discount_heading {
                  text-align: center;
                }
                
                .discount_body {
                  text-align: center;
                  font-size: 15px;
                }
                /* Social Icons ------------------------------ */
                
                .social {
                  width: auto;
                }
                
                .social td {
                  padding: 0;
                  width: auto;
                }
                
                .social_icon {
                  height: 20px;
                  margin: 0 8px 10px 8px;
                  padding: 0;
                }
                /* Data table ------------------------------ */
                
                .purchase {
                  width: 100%;
                  margin: 0;
                  padding: 35px 0;
                  -premailer-width: 100%;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                }
                
                .purchase_content {
                  width: 100%;
                  margin: 0;
                  padding: 25px 0 0 0;
                  -premailer-width: 100%;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                }
                
                .purchase_item {
                  padding: 10px 0;
                  color: #51545E;
                  font-size: 15px;
                  line-height: 18px;
                }
                
                .purchase_heading {
                  padding-bottom: 8px;
                  border-bottom: 1px solid #EAEAEC;
                }
                
                .purchase_heading p {
                  margin: 0;
                  color: #85878E;
                  font-size: 12px;
                }
                
                .purchase_footer {
                  padding-top: 15px;
                  border-top: 1px solid #EAEAEC;
                }
                
                .purchase_total {
                  margin: 0;
                  text-align: right;
                  font-weight: bold;
                  color: #333333;
                }
                
                .purchase_total--label {
                  padding: 0 15px 0 0;
                }
                
                body {
                  background-color: #F2F4F6;
                  color: #51545E;
                }
                
                p {
                  color: #51545E;
                }
                
                .email-wrapper {
                  width: 100%;
                  margin: 0;
                  padding: 0;
                  -premailer-width: 100%;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                  background-color: #F2F4F6;
                }
                
                .email-content {
                  width: 100%;
                  margin: 0;
                  padding: 0;
                  -premailer-width: 100%;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                }
                /* Masthead ----------------------- */
                
                .email-masthead {
                  padding: 25px 0;
                  text-align: center;
                }
                
                .email-masthead_logo {
                  width: 94px;
                }
                
                .email-masthead_name {
                  font-size: 16px;
                  font-weight: bold;
                  color: #FF6136;
                  text-decoration: none;
                  text-shadow: 0 1px 0 white;
                }
                /* Body ------------------------------ */
                
                .email-body {
                  width: 100%;
                  margin: 0;
                  padding: 0;
                  -premailer-width: 100%;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                }
                
                .email-body_inner {
                  width: 570px;
                  margin: 0 auto;
                  padding: 0;
                  -premailer-width: 570px;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                  background-color: #FFFFFF;
                }
                
                .email-footer {
                  width: 570px;
                  margin: 0 auto;
                  padding: 0;
                  -premailer-width: 570px;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                  text-align: center;
                }
                
                .email-footer p {
                  color: #A8AAAF;
                }
                
                .body-action {
                  width: 100%;
                  margin: 30px auto;
                  padding: 0;
                  -premailer-width: 100%;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                  text-align: center;
                }
                
                .body-sub {
                  margin-top: 25px;
                  padding-top: 25px;
                  border-top: 1px solid #EAEAEC;
                }
                
                .content-cell {
                  padding: 45px;
                }
                /*Media Queries ------------------------------ */
                
                @media only screen and (max-width: 600px) {
                  .email-body_inner,
                  .email-footer {
                    width: 100% !important;
                  }
                }
                
                @media (prefers-color-scheme: dark) {
                  body,
                  .email-body,
                  .email-body_inner,
                  .email-content,
                  .email-wrapper,
                  .email-masthead,
                  .email-footer {
                    background-color: #333333 !important;
                    color: #FFF !important;
                  }
                  p,
                  ul,
                  ol,
                  blockquote,
                  h1,
                  h2,
                  h3,
                  span,
                  .purchase_item {
                    color: #FFF !important;
                  }
                  .attributes_content,
                  .discount {
                    background-color: #222 !important;
                  }
                  .email-masthead_name {
                    text-shadow: none !important;
                  }
                }
                
                :root {
                  color-scheme: light dark;
                  supported-color-schemes: light dark;
                }
                </style>
                <!--[if mso]>
                <style type="text/css">
                  .f-fallback  {
                    font-family: Arial, sans-serif;
                  }
                </style>
              <![endif]-->
              </head>
              <body>
                <span class="preheader">Use this link to reset your password. The link is only valid for 24 hours.</span>
                <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td align="center">
                      <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                          <td class="email-masthead">
                            <a href="https://fifi.kosibound.com.ng" class="f-fallback email-masthead_name">
                            FIFI CUISINE
                          </a>
                          </td>
                        </tr>
                        <!-- Email Body -->
                        <tr>
                          <td class="email-body" width="570" cellpadding="0" cellspacing="0">
                            <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                              <!-- Body content -->
                              <tr>
                                <td class="content-cell">
                                  <div class="f-fallback">
                                    <h1>Hi '. $user_name. ',</h1>
                                    <p>You recently requested to reset your password for your account. Use the button below to reset it. <strong>This password reset is only valid for the next 24 hours.</strong></p>
                                    <!-- Action -->
                                    <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                      <tr>
                                        <td align="center">
                                          <!-- Border based button
                                            https://litmus.com/blog/a-guide-to-bulletproof-buttons-in-email-design -->
                                          <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                            <tr>
                                             <td align="center">
                                                <a href="'. $reset_link .'" class="f-fallback button button--red" target="_blank" style="color: #ffffff;">Reset your password</a>
                                            </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                    <p>For security, if you did not request a password reset, please ignore this email or <a href="https://fifi.kosibound.com.ng">contact support</a> if you have questions.</p>
                                    <p>Thanks,
                                    <!-- Sub copy -->
                                    <table class="body-sub" role="presentation">
                                      <tr>
                                        <td>
                                          <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
                                          <p class="f-fallback sub">'. $reset_link. '</p>
                                        </td>
                                      </tr>
                                    </table>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                              <tr>
                                <td class="content-cell" align="center">
                                  <p class="f-fallback sub align-center">
                                    FIFI CUISINE
                                  </p>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </body>
            </html>
            ';
            
            $mail->AltBody = "Click the link below to reset your password:\n\n" . $reset_link . "\n\nThis link expires in 1 hour.";

            if ($mail->send()) {
                echo json_encode(['status' => 'success', 'message' => 'A reset link has been sent to your email.']);
            } else {
                throw new Exception('Email sending failed: ' . $mail->ErrorInfo);
            }
        } 
        catch (Exception $e) {
            throw new Exception('Email sending failed: ' . $e->getMessage());
        }


        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

        exit;
    }

    function update_password() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            exit;
        }
      
        try {
                    // Validate form data
                    $token = $_POST['token'] ?? null;
                    $new_password = $_POST['new_password'] ?? null;
                    $confirm_password = $_POST['confirm_password'] ?? null;
            
                    if (!$token || !$new_password || !$confirm_password) {
                        throw new Exception('All fields are required.');
                    }
            
                    // Check if passwords match
                    if ($new_password !== $confirm_password) {
                        throw new Exception('Passwords do not match.');
                    }
            
                    // Validate password complexity
                    if (strlen($new_password) < 6) {
                        throw new Exception('Password must be at least 6 characters long.');
                    }
      
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
      
            // Find user by token
            $stmt = $this->db->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
            $stmt->execute([$token]);
            $reset_record = $stmt->fetch(PDO::FETCH_ASSOC);
      
            if (!$reset_record) {
                throw new Exception('Invalid or expired reset token.');
            }
      
            $user_id = $reset_record['user_id'];
      
            // Update user's password
            $stmt = $this->db->prepare("UPDATE user_info SET password = ? WHERE user_id = ?");
            $stmt->execute([$hashed_password, $user_id]);
      
            // Delete used token
            $stmt = $this->db->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);
      
            echo json_encode(["status" => "success", "message" => "Your password has been successfully updated."]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
      }
         
    }