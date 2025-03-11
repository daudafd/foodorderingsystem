<?php session_start(); ?>
<div class="container-fluid">
<form id="signup-form" method="POST" action="admin/ajax.php?action=signup">
		<div class="form-group">
			<label for="" class="control-label">Firstname</label>
			<input type="text" name="first_name" placeholder="Input your First name" required="" class="form-control">
		</div>
		<div class="form-group">
			<label for="" class="control-label">Lastname</label>
			<input type="text" name="last_name" placeholder="Input your Last name" required="" class="form-control">
		</div>
		<div class="form-group">
			<label for="" class="control-label">Phone</label>
			<input type="text" name="mobile" placeholder="Input your Phone Number" required="" class="form-control">
		</div>
		<div class="form-group">
			<label for="" class="control-label">Email</label>
			<input type="email" name="email" placeholder="Input your email" required="" class="form-control">
		</div>
		<div class="form-group">
			<label for="" class="control-label">Address</label>
			<textarea cols="30" rows="3" name="address" placeholder="Input your address here" required="" class="form-control"></textarea>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Password</label>
			<input type="password" name="password" placeholder="input your password" required="" class="form-control">
			<a href="javascript:void(0)" id="signin">Signin</a>
        </div>
		<div class="button-container">
			<button type="submit" class="btn btn-primary">Register</button>
			<button id="googleSignInButton">
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
	</form>
</div>

<style>
  #uni_modal .modal-footer {
    display: none;
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
      margin: 0;
      /* display: flex; */
      justify-content: center;
      align-items: center;
      height: 100vh; /* Full screen height */
      background-color: #f4f4f4; /* Light background for contrast */
    }

    .button-container {
      display: flex;
      align-items: center; /* Center buttons vertically */
      justify-content: center; /* Center buttons horizontally */
      gap: 10px; /* Space between buttons */
    }

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
                body {
            height: 100vh;
            justify-content: center;
            align-items: center;
            background: #fff; /* Light gray background for contrast */
            margin: 0;
        }

        .login-container {
            margin: 0 auto; /* Center align horizontally */
            max-width: 800px;
            width: 100%;
            padding: 80px 0px 40px 0px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            }

                /* Card header color */
    .card-header {
        background-color:rgb(119, 119, 119); /* Dark gray */
        color: white;
    }

    .card.border-daek {
    width: 100%; /* Example: Set to 100% of the container width */
    max-width: 500px; /* Limit the max width */
    background-color:rgb(250, 250, 250); /* Dark gray */
}
    

            .login-label {
        display: flex;
        justify-content: space-between; /* Space between Login and Sign Up */
        align-items: center; /* Center vertically */
        width: 100%; /* Full width */
        background-color:rgb(119, 119, 119); /* Dark gray */
        color: #fff; /* White text */
        padding: 10px 20px; /* Spacing inside the label */
        border-radius: 5px; /* Rounded corners */
        font-size: 14px; /* Font size */
    }

    .login-label h3 {
        margin: 0; /* Remove default margin */
        font-size: 16px; /* Adjust font size */
    }

    .login-label a {
        color: #fff; /* White text for the Sign Up link */
        text-decoration: underline; /* Optional: Underline the link */
    }

        .button-container {
      display: flex;
      align-items: center; /* Center buttons vertically */
      justify-content: center; /* Center buttons horizontally */
      gap: 10px; /* Space between buttons */
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

<script>
    $('#new_account').click(function () {
        uni_modal("Create an Account", 'signup.php?redirect=index.php?page=checkout')
    });

    $('#login-form').submit(function (e) {
        e.preventDefault();

        var $submitBtn = $('#login-form button[type="submit"]');
        $submitBtn.attr('disabled', true).html('signing in...');
        $submitBtn.append('<div class="rolling"></div>');
        $('#login-error').hide();

        $.ajax({
            url: 'admin/ajax.php?action=login2',
            method: 'POST',
            data: $('#login-form').serialize(),
            success: function (response) {
                var data = JSON.parse(response);
                setTimeout(function () {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.success) {
                        window.location.href = 'index.php?page=home';
                    } else {
                        $('#login-error').text('Login failed: ' + data.error).show();
                        $submitBtn.attr('disabled', false).html('Login');
                        alert(data.error || 'An unknown error occurred.');
                    }
                }, 1000);
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
                console.log("Server response:", xhr.responseText);
                $('#login-error').text('An error occurred, please try again.').show();
                $submitBtn.attr('disabled', false).html('Login');
            }
        });
    });

    function googleSignIn() {
        const clientId = '697818075082-8b41bldeadn75qrlpe7jeruodpccsj12.apps.googleusercontent.com'; // Replace with your Client ID
            const redirectUri = 'https://fifi.kosibound.com.ng/callback.php'; // EXACT match!
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
        }

    document.getElementById('googleSignInButton').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default button behavior
        googleSignIn();
    });

   document.getElementById('signin').addEventListener('click', function() {
        window.location.href = 'index.php?page=login';
    });


    $('#loginModal').on('hidden.bs.modal', function () {
        // This will keep the modal open in case of error and prevent automatic closure
        // You can also trigger the error message again if needed
    });

    function showError(message) {
        $('#login-error').text(message).show();
        $('#loginModal').modal('show');
    }

    $('#closeModal').click(function() {
        $('#login-error').text('').hide();
        $('#login-form')[0].reset();
        $('#loginModal').modal('hide');
    });
</script>