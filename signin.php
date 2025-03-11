<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$error_message = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']); // Clear the error message after displaying
?>
<style>
        /* Set fixed size for cards */
        .card.menu-item {
            height: 350px; /* Fixed height */
            width: 100%; /* Adjust to column width */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Ensure the image fits */
        .card.menu-item .card-img-top {
            height: 150px; /* Fixed height for images */
            object-fit: cover; /* Crop or scale the image to fit */
            border-radius: 0; /* Optional: Remove any rounded corners */
        }

        /* Truncate text */
        .card.menu-item .truncate {
            white-space: nowrap; /* Prevent wrapping */
            overflow: hidden; /* Hide overflow */
            text-overflow: ellipsis; /* Add "..." for overflowing text */
        }

        /* Ensure buttons align */
        .card.menu-item .text-center {
            margin-top: auto; /* Push buttons to the bottom */
        }
        
      .rolling {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    body {
        height: 100vh;
        justify-content: center;
        align-items: center;
        background: #fff;
        margin: 0;
    }

    .login-container {
        margin: 0 auto;
        max-width: 800px;
        width: 100%;
        padding: 120px 0px 70px 0px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .card-header {
        background-color: rgb(245, 245, 245);
        color: white;
        padding: 10px 20px;
    }

    .card.border-daek {
        width: 100%;
        max-width: 500px;
        background-color: rgb(250, 250, 255);
    }

    .login-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        background-color: rgb(245, 245, 245);
        color: #FF5733;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 14px;
    }

    .login-label h3 {
        margin: 0;
        font-size: 16px;
        color: #FF5733;
    }

    .login-label a {
        color: #FF5733;
        text-decoration: underline;
    }

    .button-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    #googleSignInButton {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        background-color: rgb(255, 255, 255);
        color: #357ae8;
        font-size: 12px;
        font-weight: bold;
        border: 2px solid #357ae8;
        border-radius: 5px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
    }

    #googleSignInButton svg {
        margin-right: 10px;
    }

    #googleSignInButton:hover {
        background-color: #357ae8;
        color: white;
        border-color: #357ae8;
    }

    #googleSignInButton:active {
        transform: scale(0.95);
    }

    #googleSignInButton:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.5);
    }

    .button {
        padding: 10px 15px;
    }
    </style>
</head>
<body>
<div class="login-container">
    <div class="card border-daek">
        <div class="card-header">
            <div class="login-label">
                <h3>Sign In</h3>
                <a href="javascript:void(0)" id="new_account">Sign Up?</a>
            </div>
        </div>
        <div class="card-body">
            <form id="login-form" method="POST" action="ajax.php?action=login2">
                <?php if (!empty($error_message)): ?>
                    <div id="login-error" class="text-danger mt-2">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>
                <div class="form-group mt-3">
                    <label for="" class="control-label">Email</label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="form-group mt-3">
                    <label for="" class="control-label">Password</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                <div class="button-container mt-4">
                    <button type="submit" class="btn btn-primary">Sign In</button>
                    <button type="button" id="googleSignInButton" data-client-id="<?php echo htmlspecialchars($_ENV['GOOGLE_CLIENT_ID']); ?>" data-redirect-uri="<?php echo htmlspecialchars($_ENV['REDIRECT_URI']); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20px" height="14px">
                            <path fill="#EA4335" d="M24 9.5c3.87 0 7.27 1.44 9.97 3.78l7.4-7.4C37.25 2.37 31.06 0 24 0 14.37 0 6.26 5.42 2.68 13.35l8.95 6.93C13.02 13.17 17.26 9.5 24 9.5z"/>
                            <path fill="#34A853" d="M46.64 20.2h-22.64v8.54h13.04c-1.16 3.06-3.37 5.54-6.3 7.22l9.44 7.31c5.5-5.06 8.66-12.54 8.66-21.07 0-1.43-.15-2.82-.42-4.2z"/>
                            <path fill="#FBBC05" d="M10.61 20.49c-.4 1.15-.61 2.38-.61 3.65 0 1.27.22 2.51.61 3.65L1.74 34.72c-1.64-3.31-2.55-6.98-2.55-10.83s.91-7.52 2.55-10.83z"/>
                            <path fill="#4285F4" d="M24 48c6.48 0 11.92-2.15 15.89-5.84l-9.44-7.31c-1.79 1.19-4.11 1.9-6.45 1.9-5.48 0-10.12-3.57-11.8-8.37l-8.95 6.93C6.26 42.58 14.37 48 24 48z"/>
                            <path fill="none" d="M0 0h48v48h-48z"/>
                        </svg>
                        Sign in with Google
                    </button>
                </div>
                <p class="card-text mt-3 text-center"><a href="javascript:void(0)" id="forgot_password">Forgot Password?</a></p>
            </form>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $('#new_account').click(function () {
        uni_modal("Create an Account", 'signup.php?redirect=index.php?page=checkout')
    });

    $('#login-form').submit(function (e) {
        e.preventDefault();

        var $submitBtn = $('#login-form button[type="submit"]');
        $submitBtn.attr('disabled', true).html('Signing in...');
        $submitBtn.append('<div class="rolling"></div>');
        $('#login-error').hide();

        $.ajax({
            url: 'admin/ajax.php?action=login2',
            method: 'POST',
            data: $('#login-form').serialize(),
            success: function (response) {
                var data = JSON.parse(response);

                if (data.success) {
                    let redirectUrl = data.redirect.replace(/\\/g, "");

                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Welcome back!',
                            showConfirmButton: false,
                            timer: 1000
                        }).then(function() {
                            window.location.href = 'index.php?page=home';
                        });
                    }
                } else {
                    $('#login-error').text('Login failed: ' + data.error).show();
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: data.error || 'An unknown error occurred.'
                    }).then(function() {
                        $submitBtn.attr('disabled', false).html('Sign In');
                        $('.rolling').remove();
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
                console.log("Server response:", xhr.responseText);
                $('#login-error').text('An error occurred, please try again.').show();
                Swal.fire({
                    icon: 'error',
                    title: 'AJAX Error',
                    text: 'An error occurred, please try again.'
                }).then(function() {
                    $submitBtn.attr('disabled', false).html('Sign In');
                    $('.rolling').remove();
                });
            }
        });
    });

    document.getElementById('googleSignInButton').addEventListener('click', function(event) {
        event.preventDefault();
        const clientId = this.getAttribute('data-client-id');
        const redirectUri = this.getAttribute('data-redirect-uri');
        const scope = 'openid email profile';
        const accessType = 'offline';
        const prompt = 'consent';

        const authUrl = new URL('https://accounts.google.com/o/oauth2/v2/auth');
        authUrl.searchParams.append('client_id', clientId);
        authUrl.searchParams.append('redirect_uri', redirectUri);
        authUrl.searchParams.append('response_type', 'code');
        authUrl.searchParams.append('scope', scope);
        authUrl.searchParams.append('access_type', accessType);
        authUrl.searchParams.append('prompt', prompt);

        window.location.href = authUrl.toString();
    });

    document.getElementById('forgot_password').addEventListener('click', function() {
        window.location.href = 'index.php?page=password';
    });

    document.getElementById('new_account').addEventListener('click', function() {
        window.location.href = 'index.php?page=register';
    });
</script>
</body>
</html>
