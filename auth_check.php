    <?php
        // session_start(); // Ensure session_start() is at the very top
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $allowed_pages = ['home', 'about', 'cart_list', 'checkout', 'order', 'signin', 'register', 'password', 'reset_password', 'reset_message', 'new_password'];
        $page = isset($_GET['page']) && in_array($_GET['page'], $allowed_pages) ? basename($_GET['page']) : 'home';
        
        // Authorization check
        $protected_pages = ['order', 'checkout']; // Pages requiring login
        if (in_array($page, $protected_pages) && !isset($_SESSION['login_user_id'])) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI']; // Store intended URL
            $page = 'signin'; // Redirect to signin if unauthorized
        }
        
        echo ""; // Debugging line
        include $page . '.php';
    ?>