<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
        padding: 120px 0px 150px 0px;
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
</style>
<?php
require 'admin/db_connect.php'; // Ensure you have a database connection

$token = isset($_GET['token']) ? $_GET['token'] : null;

if (!$token) {
    die("Invalid password reset link.");
}
?>
<body>
    <div class="login-container">
        <div class="card border-dark">
            <div class="card-header">
                <div class="login-label">
                    <a href="javascript:void(0)" id="signin">Log in?</a>
                </div>
            </div>
                <div class="card-body">
                    <?php
                    $token = isset($_GET['token']) ? $_GET['token'] : null;
                
                    if ($token) {
                        // Process the token and display the password reset form
                        ?>
                        <form id="update-password-form" method="POST" action="admin/ajax2.php?action=update_password">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            <div class="form-group">
                                <label for="new_password" class="control-label">New Password:</label>
                                <input type="password" name="new_password" id="new_password" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password" class="control-label">Confirm Password:</label>
                                <input type="password" name="confirm_password" id="confirm_password" required class="form-control">
                            </div>
                            <div class="button-container">
                                <button id="update-btn" class="btn btn-primary">Submit New Password</button>
                            </div>
                        </form>
                        <?php
                    } else {
                        // Handle the case where the token is missing
                        echo "Invalid password reset link.";
                    }
                    ?>
                </div>
            </div>
        </div>
</body>
<!-- Include SweetAlert2 for notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // View Product Modal
    $('.view_prod').click(function() {
        uni_modal_right('Product', 'view_prod.php?id=' + $(this).attr('data-id'));
    });

    $(document).ready(function() {
    $('#update-password-form').submit(function(e) {
        e.preventDefault();
        console.log("Submitting Form...");

        var $submitBtn = $('#update-btn');
        $submitBtn.attr('disabled', true).html('Updating...');

        $.ajax({
            url: 'admin/ajax2.php?action=update_password',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log("AJAX Success:", response);
                if (response.status === 'success') {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = 'index.php?page=home';
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message || "An error occurred.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
                $submitBtn.attr('disabled', false).html('Submit New Password');
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error("AJAX Error:", xhr.status, textStatus, errorThrown);
                Swal.fire({
                    title: "Error!",
                    text: "An error occurred while processing your request.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                $submitBtn.attr('disabled', false).html('Submit New Password');
            }
        });
    });
});

        // Redirect to Sign In Page on button click
        document.getElementById('signin').addEventListener('click', function() {
            window.location.href = 'index.php?page=signin';
        });
</script>
</body>
</html>