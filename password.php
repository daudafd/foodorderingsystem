<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a secure token
}
?>
<style>
    .card.menu-item {
        height: 350px;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card.menu-item .card-img-top {
        height: 150px;
        object-fit: cover;
        border-radius: 0;
    }

    .card.menu-item .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card.menu-item .text-center {
        margin-top: auto;
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
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
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
        padding: 120px 0px 200px 0px;
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

<div class="login-container">
    <div class="card border-dark">
        <div class="card-header">
            <div class="login-label">
                <h3>Reset Password</h3>
                <a href="javascript:void(0)" id="signin">Log in?</a>
            </div>
        </div>
        <div class="card-body">
            <form id="reset-password-form" method="POST" action="admin/ajax2.php?action=reset_password">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label for="email" class="control-label">Email</label>
                    <input type="email" id="email" name="email" placeholder="Input your email" required class="form-control">
                </div>
                <div class="button-container">
                    <button id="reset-btn" class="btn btn-primary">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // View Product Modal
    $('.view_prod').click(function() {
        uni_modal_right('Product', 'view_prod.php?id=' + $(this).attr('data-id'));
    });

    $(document).ready(function() {
        // Reset Password Form Submission
        $('#reset-password-form').submit(function(e) {
            e.preventDefault();

            var $submitBtn = $('#reset-btn');
            $submitBtn.attr('disabled', true).html('Sending...');

            $.ajax({
                url: 'admin/ajax2.php?action=reset_password',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.status === 'success') {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = 'index.php?page=reset_message';
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message || "An error occurred.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                    $submitBtn.attr('disabled', false).html('Reset Password');
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error("AJAX Error:", xhr.status, textStatus, errorThrown);
                    let errorMessage = "An error occurred while processing your request.";
                    if (xhr.status === 404) {
                        errorMessage = "Resource not found.";
                    } else if (xhr.status === 500) {
                        errorMessage = "Internal server error.";
                    }
                    Swal.fire({
                        title: "Error!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                    $submitBtn.attr('disabled', false).html('Reset Password');
                }
            });
        });
    });

    document.getElementById('signin').addEventListener('click', function () {
        window.location.href = 'index.php?page=signin';
    });
</script>
</body>
</html>