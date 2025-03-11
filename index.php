<?php
session_start()
?>
<!DOCTYPE html>
<html lang="en">
<?php
  include('header.php');

// Get banner images
$banner_qry = $conn->query("SELECT image_path FROM banner_images");
$banner_images = [];
while ($banner_row = $banner_qry->fetch_assoc()) {
    $banner_images[] = $banner_row['image_path'];
}

// Handle the case where there are no banner images
if (empty($banner_images)) {
    $banner_images = ['default-banner.jpg']; // Use a default image
}

// Option 1: Display a single random image (PHP)
$random_banner = $banner_images[array_rand($banner_images)];

// Option 2: Prepare image paths for JavaScript slideshow (PHP)
$banner_images_json = json_encode($banner_images);

  ?>

<style>
header.masthead {
    background: linear-gradient(to bottom, rgb(0 0 0 / 40%) 0%, rgb(245 242 240 / 45%) 100%), url(assets/img/<?php echo $random_banner; ?>);
    background-repeat: no-repeat;
    background-size: cover;
    transition: background-image 1s ease-in-out;
}

#mainNav .navbar-brand,
#mainNav .nav-link {
    color: #ffffff !important;
    /* Ensure white text */
}

#mainNav .navbar-brand:hover,
#mainNav .nav-link:hover {
    color: #000 !important;
    /* Hover color for navbar brand and nav links */
}

.navbar-toggler-icon {
    filter: invert(1);
    /* Make the toggle icon white */
}

/* Customize success toasts */
.toast-success {
  background-color:rgb(0, 128, 32) !important; /* Example: Blue */
  color :rgb(255, 255, 255) !important; /* Example: Blue */
}

/* Customize error toasts */
.toast-error {
  background-color: #dc3545 !important; /* Example: Red */
  color: #fff !important; /* Example: White text */
}

/* Customize info toasts */
.toast-info {
  background-color:rgb(48, 11, 214) !important; /* Example: Cyan */
  color: #fff !important; /* Example: White text */
}

/* Customize warning toasts */
.toast-warning {
  background-color: #ffc107 !important; /* Example: Yellow */
  color: #000 !important; /* Example: Black text */
}

/* General toast customizations (applies to all types) */
.toast {
  /* Add any general styles here */
  font-weight: bold !important; /* Example: Bold text */
}

/* Example: A custom toast class */
.my-custom-toast {
  background-color: #8e44ad !important; /* Example: Purple */
  color: #fff !important;
}
</style>

<body id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" style="background-color: #ea3b16;" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="./"><?php echo $setting_name; ?></a>
            <!-- Navbar container -->
            <div class="d-flex align-items-center">
                <!-- Cart Icon with Count -->
                <a class="nav-link js-scroll-trigger d-flex align-items-center" href="index.php?page=cart_list">
                <i class="fa fa-shopping-cart"></i>
                    <span class="badge badge-danger item_count mr-1">
                        <?php 
                            if (isset($_SESSION['cart_count'])) {
                                echo $_SESSION['cart_count']; 
                            } else {
                                $_SESSION['cart_count'] = 0; 
                                echo 0; 
                            }
                        ?>
                    </span>
                </a>
            <!-- Navbar Toggle Button -->
            <button class="navbar-toggler navbar-toggler-right ml-2" type="button" data-toggle="collapse"
                data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto my-2 my-lg-0">
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=home">Home</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=about">About</a>
                    </li>
                    <?php if(isset($_SESSION['login_user_id'])): ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=order">Orders</a>
                    </li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger"
                            href="admin/ajax.php?action=logout2"><?php echo "Welcome ". $_SESSION['login_first_name'] ?>
                            <i class="fa fa-power-off"></i></a></li>
                    <?php else: ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="javascript:void(0)"
                            id="login_now">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php
     include('auth_check.php');
     ?>

    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                </div>
                <div class="modal-body">
                    <div id="delete_content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="uni_modal" role='dialog'>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id='submit'
                        onclick="$('#uni_modal form').submit()">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="uni_modal_right" role='dialog'>
        <div class="modal-dialog modal-full-height  modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="fa fa-arrow-righ t"></span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-light py-5">
        <div class="container">
            <div class="small text-center text-muted">Copyright © <?= date('Y'); ?> - Designed by <a
                    href="https://www.kosibound.com.ng" target="_blank">Kosibound</a></div>
    </footer>

    <?php include('footer.php') ?>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

    <script>
    window.addEventListener('message', function(event) {
        if (event.data.type === 'toast') {
            if (event.data.status === 'success') {
                toastr.success(event.data.message);
            } else {
                toastr.error(event.data.message);
            }
        }
    });
    
    $('#login_now').click(function () {
    // // Check if the current page is a protected page
    // var currentPage = window.location.href;
    // var protectedPages = ['order', 'checkout']; // Add other protected pages

    // var isProtected = false;
    // for (var i = 0; i < protectedPages.length; i++) {
    //     if (currentPage.indexOf('page=' + protectedPages[i]) !== -1) {
    //         isProtected = true;
    //         break;
    //     }
    // }

    // Store intended URL if on a protected page
    // if (isProtected) {
        $.ajax({
            url: 'admin/ajax.php?action=set_intended_url', // Create a new PHP action
            method: 'POST',
            data: { url: currentPage },
            success: function (response) {
                uni_modal("Login", 'login.php'); // Open the modal after setting the intended URL
            }
        });
    // } else {
    //     uni_modal("Login", 'login.php'); // Open the modal directly if on an unprotected page
    // }
});
    </script>
</body>

<?php $conn->close() ?>

</html>