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
            padding: 120px 0px 130px 0px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

                /* Card header color */
    .card-header {
        background-color:rgb(250, 250, 240); /* Dark gray */
        color: white;
        padding: 10px 20px; /* Added padding for better appearance */
    }

    .card.border-daek {
        width: 100%; /* Example: Set to 100% of the container width */
        max-width: 500px; /* Limit the max width */
        background-color:rgb(250, 250, 255); /* Dark gray */
    }
    

    .login-label {
        display: flex;
        justify-content: space-between; /* Space between Login and Sign Up */
        align-items: center; /* Center vertically */
        width: 100%; /* Full width */
        background-color:rgb(250, 250, 240); /* Dark gray */
        color: #FF5733; /* White text */
        padding: 10px 20px; /* Spacing inside the label */
        border-radius: 5px; /* Rounded corners */
        font-size: 14px; /* Font size */
    }

    .login-label h3 {
        margin: 0; /* Remove default margin */
        font-size: 16px; /* Adjust font size */
        color: #FF5733; /* White text for the Sign Up link */
    }

    .login-label a {
        color: #FF5733; /* White text for the Sign Up link */
        text-decoration: underline; /* Optional: Underline the link */
    }

        .button-container {
      display: flex;
      align-items: center; /* Center buttons vertically */
      justify-content: center; /* Center buttons horizontally */
      gap: 10px; /* Space between buttons */
    }
</style>

<body>
    <div class="login-container">
        <div class="card border-dark">
            <div class="card-header">
                <div class="login-label">
                    <a href="javascript:void(0)" id="signin">Log in?</a>
                </div>
            </div>
            <div class="card-body">
                <h5>Password Reset Confirmation</h5>
                <p>Kindly follow the link sent to your email to input new password.</p>
                <p>If you don't see the email, please check your spam folder.</p>
                <p><a href="index.php?page=home">Return to Home</a></p>
            </div>
        </div>
    </div>

   
</body>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('signin').addEventListener('click', function() {
        event.preventDefault(); // Prevent default button behavior
        window.location.href = 'index.php?page=signin';
    });
</script>
</body>
</html>