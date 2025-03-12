<?php
// The following will affect login but needed for password reset email
// header('Content-Type: application/json'); // Force JSON response

require __DIR__ . '/../vendor/autoload.php'; // Adjust path if needed

// session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
Class Action {
    private $db;

    public function __construct() {
        ob_start();
        include 'db_connect.php';
        $this->db = $conn;
    }
    function __destruct() {
        // $this->db = null; // Close PDO connection
        ob_end_flush();
    }

    function login() {
        // Extract POST data
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Check if username and password are provided
        if (empty($username) || empty($password)) {
            return json_encode(['success' => false, 'error' => 'Username or Password is required.']);
        }

        // Prepare SQL statement to prevent SQL injection
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        if (!$stmt) {
            return json_encode(['success' => false, 'error' => 'Database prepare error.']);
        }

        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) { //Use password_verify
            // Store user session
            foreach ($user as $key => $value) {
                if ($key != 'password' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                }
            }
            return json_encode(['success' => true]);
        } else {
            return json_encode(['success' => false, 'error' => 'Incorrect password.']);
        }
    }

    function login2() {
        // CSRF Token Verification
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            // return json_encode(['success' => false, 'error' => 'CSRF token mismatch.']);
            echo json_encode(['success' => false, 'error' => 'Error, try again later']);
        }
    
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
    
        if (empty($email) || empty($password)) {
            return json_encode(['success' => false, 'error' => 'Email or Password is empty.']);
        }
    
        $stmt = $this->db->prepare("SELECT * FROM user_info WHERE email = :email");
        if (!$stmt) {
            return json_encode(['success' => false, 'error' => 'Database prepare error.']);
        }
    
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID
            session_regenerate_id(true);
    
            foreach ($user as $key => $value) {
                if ($key != 'password' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                }
            }
    
            $_SESSION['address'] = $user['address'] ?? '';
            $_SESSION['mobile'] = $user['mobile'] ?? '';
    
            if (isset($_SESSION['cart_items']) && !empty($_SESSION['cart_items'])) {
                foreach ($_SESSION['cart_items'] as $item) {
                    $cart_stmt = $this->db->prepare("INSERT INTO cart (product_id, qty, user_id, price, size, soup) VALUES (:product_id, :qty, :user_id, :price, :size, :soup)");
                    if (!$cart_stmt) {
                        return json_encode(['success' => false, 'error' => 'Database cart insert prepare error.']);
                    }
    
                    $size = isset($item['size']) && !empty($item['size']) ? $item['size'] : null;
    
                    $cart_stmt->execute([':product_id' => $item['product_id'], ':qty' => $item['qty'], ':user_id' => $_SESSION['login_user_id'], ':price' => $item['price'], ':size' => $size, ':soup' => $item['soup']]);
    
                    if (!$cart_stmt->rowCount() > 0) {
                        return json_encode(['success' => false, 'error' => 'Database cart insert execute error.']);
                    }
                }
                // Clear the session's cart after transferring the items to the database
                unset($_SESSION['cart_items']);
    
                //delete guest cart items from database.
                $ip = $this->getClientIP();
                if ($ip) {
                    $stmt_delete = $this->db->prepare("DELETE FROM cart WHERE client_ip = :ip");
                    if ($stmt_delete) {
                        $stmt_delete->execute([':ip' => $ip]);
                    }
                }
            }
    
            // Redirect handling
            if (isset($_SESSION['intended_url'])) {
                $redirect_url = $_SESSION['intended_url'];
                unset($_SESSION['intended_url']); // Clear intended URL
                return json_encode(['success' => true, 'redirect' => $redirect_url]);
            } else {
                return json_encode(['success' => true, 'redirect' => 'index.php?page=home']);
            }
        } else {
            return json_encode(['success' => false, 'error' => 'Incorrect password.']);
        }
    }

    // Helper function to get the client's IP address
    function getClientIP() {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    function logout() {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:login.php");
    }

    function logout2() {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:../index.php");
    }
    
    function save_user() {
        if (!isset($_POST['name'], $_POST['username'], $_POST['password'], $_POST['type'])) {
            die(json_encode(['status' => 'error', 'message' => 'Missing required fields.']));
        }
    
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'] ?? '';
        $type = $_POST['type']; // Get type from POST
    
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        } else {
            $hashedPassword = null; // Or handle as needed (e.g., if password update is optional)
        }
    
        if (empty($_POST['id'])) {
            try {
                $stmt = $this->db->prepare("INSERT INTO users (name, username, type, password) VALUES (:name, :username, :type, :password)");
                $stmt->execute([':name' => $name, ':username' => $username, ':type' => $type, ':password' => $hashedPassword]);
                return json_encode(['status' => 'success', 'message' => 'User saved successfully.']);
            } catch (PDOException $e) {
                error_log("Save User Error (Insert): " . $e->getMessage());
                return json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
            }
        } else {
            $id = $_POST['id'];
            try {
                if ($hashedPassword !== null) {
                    $stmt = $this->db->prepare("UPDATE users SET name = :name, username = :username, type = :type, password = :password WHERE id = :id");
                    $stmt->execute([':name' => $name, ':username' => $username, ':type' => $type, ':password' => $hashedPassword, ':id' => $id]);
                } else {
                    $stmt = $this->db->prepare("UPDATE users SET name = :name, username = :username, type = :type WHERE id = :id");
                    $stmt->execute([':name' => $name, ':username' => $username, ':type' => $type, ':id' => $id]);
                }
                return json_encode(['status' => 'success', 'message' => 'User updated successfully.']);
            } catch (PDOException $e) {
                error_log("Save User Error (Update): " . $e->getMessage());
                return json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
            }
        }
    }
    
    function get_user($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return null; // Or throw an exception, or return an error JSON
        }
    }
    
    function delete_user($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
        } catch (PDOException $e) {
            error_log("Delete User Error: " . $e->getMessage());
            return json_encode(['status' => 'error', 'message' => 'Failed to delete user.']);
        }
    }
    
    function signup() {
        $fields = ['first_name', 'last_name', 'mobile', 'address', 'email', 'password'];
        foreach ($fields as $field) {
            if (empty($_POST[$field])) {
                error_log("Field '$field' is empty.");
            }
        }
    
        if (
            empty($_POST['email']) ||
            empty($_POST['password']) ||
            empty($_POST['first_name']) ||
            empty($_POST['last_name']) ||
            empty($_POST['mobile']) ||
            empty($_POST['address'])
        ) {
            return json_encode(["error" => "All fields are required."]);
        }
    
        extract($_POST);
        $email = strtolower(trim($email)); // Ensure consistency
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    
        try {
            $chk_query = $this->db->prepare("SELECT COUNT(*) as count FROM user_info WHERE email = :email");
            $chk_query->execute([':email' => $email]);
            $chk_row = $chk_query->fetch(PDO::FETCH_ASSOC);
            $chk = $chk_row['count'] ?? 0;
    
            if ($chk > 0) {
                return json_encode(['error' => 'Email already exists.']);
            }
    
            $stmt = $this->db->prepare("INSERT INTO user_info (first_name, last_name, mobile, address, email, password) VALUES (:first_name, :last_name, :mobile, :address, :email, :password)");
            $stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':mobile' => $mobile,
                ':address' => $address,
                ':email' => $email,
                ':password' => $password_hashed,
            ]);
    
            $login = $this->login2(); // Log the user in after successful signup
            return json_encode(['success' => 'Signup successful']);
        } catch (PDOException $e) {
            error_log("Signup Error: " . $e->getMessage());
            return json_encode(['error' => 'Signup failed. Database error: ' . $e->getMessage()]);
        }
    }

    function save_settings() {
        $system_settings_id = null;
    
        $chk = $this->db->query("SELECT id FROM system_settings LIMIT 1");
        if ($chk && $chk->rowCount() > 0) { // Corrected to rowCount()
            $row = $chk->fetch(PDO::FETCH_ASSOC); // Corrected fetch
            $system_settings_id = $row['id'];
        }
    
        // Update System Settings (Conditionally)
        $update_fields = [];
        $bind_values = [];
    
        if (isset($_POST['name'])) {
            $update_fields[] = "name = ?";
            $bind_values[] = $_POST['name'];
        }
        if (isset($_POST['email'])) {
            $update_fields[] = "email = ?";
            $bind_values[] = $_POST['email'];
        }
        if (isset($_POST['contact'])) {
            $update_fields[] = "contact = ?";
            $bind_values[] = $_POST['contact'];
        }
        if (isset($_POST['about'])) {
            $update_fields[] = "about_content = ?";
            $bind_values[] = $_POST['about'];
        }
    
        if (!empty($update_fields) && $system_settings_id) {
            $update_query = "UPDATE system_settings SET " . implode(", ", $update_fields) . " WHERE id = ?";
            $bind_values[] = $system_settings_id;
    
            $stmt = $this->db->prepare($update_query);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->errorInfo()[2]);
                return json_encode(['error' => "Database error."]);
            }
    
            if (!$stmt->execute($bind_values)) { // Execute with parameter array
                error_log("Execute failed: " . implode(", ", $stmt->errorInfo()));
                return json_encode(['error' => "Failed to update settings."]);
            }
            $stmt->closeCursor();
        } else if (empty($system_settings_id) && (isset($_POST['name']) || isset($_POST['email']) || isset($_POST['contact']) || isset($_POST['about']))) {
            $stmt = $this->db->prepare("INSERT INTO system_settings (name, email, contact, about_content) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->errorInfo()[2]);
                return json_encode(['error' => "Database error."]);
            }
    
            if (!$stmt->execute([$_POST['name'], $_POST['email'], $_POST['contact'], $_POST['about']])) { // Execute with parameter array
                error_log("Execute failed: " . implode(", ", $stmt->errorInfo()));
                return json_encode(['error' => "Failed to insert settings."]);
            }
            $system_settings_id = $this->db->lastInsertId();
        }
    
        // Handle Multiple Image Uploads (Conditionally)
        if (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name']) && $system_settings_id) {
            //Delete Existing Banner Images
            $del_banners = $this->db->query("DELETE FROM banner_images where system_settings_id = " . $system_settings_id);
            $files = $_FILES['images'];
    
            for ($i = 0; $i < count($files['tmp_name']); $i++) {
                if (!empty($files['tmp_name'][$i])) {
                    $fname = strtotime(date('y-m-d H:i')) . '_' . $files['name'][$i];
                    $target_dir = '../assets/img/';
                    $target_file = $target_dir . $fname;
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
                    $check = getimagesize($files["tmp_name"][$i]);
                    if ($check === false) {
                        return json_encode(['error' => "File is not an image."]);
                    }
    
                    if ($files["size"][$i] > 5000000) {
                        return json_encode(['error' => "Sorry, one of your files is too large. Max 5MB allowed."]);
                    }
    
                    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                        return json_encode(['error' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."]);
                    }
    
                    if (move_uploaded_file($files['tmp_name'][$i], $target_file)) {
                        $stmt = $this->db->prepare("INSERT INTO banner_images (system_settings_id, image_path) VALUES (?, ?)");
                        if (!$stmt->execute([$system_settings_id, $fname])) {
                            error_log("Failed to insert banner image: " . implode(", ", $stmt->errorInfo()));
                            return json_encode(['error' => "Failed to save banner images."]);
                        }
                        $stmt->closeCursor();
                    } else {
                        return json_encode(['error' => "Failed to upload one or more images."]);
                    }
                }
            }
        }
    
        // Update session variables
        $query = $this->db->query("SELECT * FROM system_settings LIMIT 1");
        if ($query && $query->rowCount() > 0) {
            $query = $query->fetch(PDO::FETCH_ASSOC);
            foreach ($query as $key => $value) {
                if (!is_numeric($key)) {
                    $_SESSION['setting_' . $key] = $value;
                }
            }
        }
    
        return json_encode(['success' => 'Settings saved successfully']);
        exit;
    }

    function save_category() {
        extract($_POST);
        $name = $_POST['name']; // Extract name separately
    
        if (empty($id)) {
            try {
                $stmt = $this->db->prepare("INSERT INTO category_list (name) VALUES (?)");
                $stmt->execute([$name]);
                return json_encode(['success' => 'Category added successfully']);
            } catch (PDOException $e) {
                return json_encode(['error' => 'Failed to add category: ' . $e->getMessage()]);
            }
        } else {
            try {
                $stmt = $this->db->prepare("UPDATE category_list SET name = ? WHERE id = ?");
                $stmt->execute([$name, $id]);
                return json_encode(['success' => 'Category updated successfully']);
            } catch (PDOException $e) {
                return json_encode(['error' => 'Failed to update category: ' . $e->getMessage()]);
            }
        }
    }
    
    function delete_category() {
        extract($_POST);
    
        try {
            $stmt = $this->db->prepare("DELETE FROM category_list WHERE id = ?");
            $stmt->execute([$id]);
            return json_encode(['success' => 'Category deleted successfully']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to delete category: ' . $e->getMessage()]);
        }
    }
    
    function save_menu() {
        extract($_POST);
    
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];
        $description = $_POST['description'];
        $status = isset($_POST['status']) && $_POST['status'] == 'on' ? 1 : 0;
        $price_small = isset($_POST['has_size_options']) && $_POST['has_size_options'] == 'on' ? $_POST['price_small'] : null;
        $price_medium = isset($_POST['has_size_options']) && $_POST['has_size_options'] == 'on' ? $_POST['price_medium'] : null;
        $price_large = isset($_POST['has_size_options']) && $_POST['has_size_options'] == 'on' ? $_POST['price_large'] : null;
        $img_path = '';
    
        if ($_FILES['img']['tmp_name'] != '') {
            $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
            $move = move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/' . $fname);
            $img_path = $fname;
        }
    
        try {
            if (empty($id)) {
                $stmt = $this->db->prepare("INSERT INTO product_list (name, price, category_id, description, status, price_small, price_medium, price_large, img_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $price, $category_id, $description, $status, $price_small, $price_medium, $price_large, $img_path]);
                return json_encode(['success' => 'Menu item added successfully']);
            } else {
                $sql = "UPDATE product_list SET name = ?, price = ?, category_id = ?, description = ?, status = ?, price_small = ?, price_medium = ?, price_large = ?";
                $params = [$name, $price, $category_id, $description, $status, $price_small, $price_medium, $price_large];
                if($img_path != ''){
                    $sql .= ", img_path = ?";
                    $params[] = $img_path;
                }
                $sql .= " WHERE id = ?";
                $params[] = $id;
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
    
                return json_encode(['success' => 'Menu item updated successfully']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to save menu item: ' . $e->getMessage()]);
        }
    }
    
    function delete_menu() {
        extract($_POST);
    
        try {
            $stmt = $this->db->prepare("DELETE FROM product_list WHERE id = ?");
            $stmt->execute([$id]);
            return json_encode(['success' => 'Menu item deleted successfully']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to delete menu item: ' . $e->getMessage()]);
        }
    }
    
    function get_menu_data($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_list WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result : false;
        } catch (PDOException $e) {
            // Log the error or handle it as needed
            return false;
        }
    }
	
    function save_meat_option() {
        extract($_POST);
        $data = " meat_type = '$meat_type' ";
        $data .= ", size = '$size' ";
        $data .= ", price = '$price' ";
        if (empty($id)) {
            $save = $this->db->query("INSERT INTO meat_options set " . $data);
        } else {
            $save = $this->db->query("UPDATE meat_options set " . $data . " WHERE id = " . $id);
        }
        if ($save) {
            return 1; // Return 1 for success
        } else {
            return 0; // Return 0 for failure
        }
    }

    function get_meat_price($meat_type, $size) {
        try {
            $stmt = $this->db->prepare("SELECT price FROM meat_options WHERE meat_type = ? AND size = ?");
            $stmt->execute([$meat_type, $size]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return ['status' => 'success', 'price' => number_format($row['price'], 2)];
            } else {
                return ['status' => 'error', 'message' => 'Price not found.'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    function delete_meat_option() {
        extract($_POST);
    
        try {
            $stmt = $this->db->prepare("DELETE FROM meat_options WHERE id = ?");
            $stmt->execute([$id]);
            return json_encode(['success' => 'Meat option deleted successfully']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to delete meat option: ' . $e->getMessage()]);
        }
    }
    
    function save_soup_option() {
        extract($_POST);
        $soup_type = $_POST['soup_type']; // Extract safely
    
        if (empty($id)) {
            try {
                $stmt = $this->db->prepare("INSERT INTO soup_options (soup_type) VALUES (?)");
                $stmt->execute([$soup_type]);
                return json_encode(['success' => 'Soup option added successfully']);
            } catch (PDOException $e) {
                return json_encode(['error' => 'Failed to add soup option: ' . $e->getMessage()]);
            }
        } else {
            try {
                $stmt = $this->db->prepare("UPDATE soup_options SET soup_type = ? WHERE id = ?");
                $stmt->execute([$soup_type, $id]);
                return json_encode(['success' => 'Soup option updated successfully']);
            } catch (PDOException $e) {
                return json_encode(['error' => 'Failed to update soup option: ' . $e->getMessage()]);
            }
        }
    }
    
    function delete_soup_option() {
        extract($_POST);
    
        try {
            $stmt = $this->db->prepare("DELETE FROM soup_options WHERE id = ?");
            $stmt->execute([$id]);
            return json_encode(['success' => 'Soup option deleted successfully']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to delete soup option: ' . $e->getMessage()]);
        }
    }
    
    function add_to_cart() {
        extract($_POST);
        $pid = $_POST['pid'];
        $qty = isset($_POST['qty']) ? $_POST['qty'] : 1;
        $size = isset($_POST['size']) ? $_POST['size'] : null;
        $soup_choice = isset($_POST['soup_choice']) ? $_POST['soup_choice'] : null;
    
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_list WHERE id = ?");
            $stmt->execute([$pid]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $price = $product['price']; // Default price
    
            if ($product && $size) {
                $stmt = $this->db->prepare("SELECT price FROM meat_options WHERE meat_type = ? AND size = ?");
                $stmt->execute([$product['name'], $size]);
                $meat_option = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($meat_option) {
                    $price = $meat_option['price'];
                }
            }
    
            if (isset($_SESSION['login_user_id'])) {
                $user_id = $_SESSION['login_user_id'];
                $stmt = $this->db->prepare("INSERT INTO cart (product_id, qty, size, soup, price, user_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$pid, $qty, $size, $soup_choice, $price, $user_id]);
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? null;
                $stmt = $this->db->prepare("INSERT INTO cart (product_id, qty, size, soup, price, client_ip) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$pid, $qty, $size, $soup_choice, $price, $ip]);
    
                if (!isset($_SESSION['cart_items'])) {
                    $_SESSION['cart_items'] = [];
                }
                $_SESSION['cart_items'][] = [
                    'product_id' => $pid,
                    'qty' => $qty,
                    'size' => $size,
                    'soup' => $soup_choice,
                    'price' => $price
                ];
            }
    
            $_SESSION['cart_count'] = $this->get_cart_count();
            return json_encode(['success' => 'Item added to cart']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to add item to cart: ' . $e->getMessage()]);
        }
    }
    
    function get_cart_count() {
        $user_id = $_SESSION['login_user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    
        try {
            if ($user_id) {
                $stmt = $this->db->prepare("SELECT SUM(qty) AS total_items FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
            } else {
                $stmt = $this->db->prepare("SELECT SUM(qty) AS total_items FROM cart WHERE client_ip = ?");
                $stmt->execute([$ip]);
            }
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['total_items'] ?: 0); // Return the integer directly
        } catch (PDOException $e) {
            error_log('Failed to get cart count: ' . $e->getMessage());
            return 0; // Return 0 on error
        }
    }

    function update_cart_qty() {
        extract($_POST);
        $qty = $_POST['qty']; // Extract safely
    
        try {
            $stmt = $this->db->prepare("UPDATE cart SET qty = ? WHERE id = ?");
            $stmt->execute([$qty, $id]);
    
            $_SESSION['cart_count'] = $this->get_cart_count();
    
            $stmt = $this->db->prepare("SELECT p.price, c.qty FROM cart c INNER JOIN product_list p ON p.id = c.product_id WHERE c.id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $item_total = $row['price'] * $row['qty'];
    
            $stmt = $this->db->prepare("SELECT SUM(c.qty * p.price) AS total_amount FROM cart c INNER JOIN product_list p ON p.id = c.product_id");
            $stmt->execute();
            $cart_row = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_amount = $cart_row['total_amount'];
    
            echo json_encode([
                'success' => true,
                'item_total' => number_format($item_total, 2),
                'total_amount' => number_format($total_amount, 2)
            ]);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Failed to update cart quantity: ' . $e->getMessage()]);
        }
    }
    
    function remove_from_cart($id) {
        error_log("Attempting to remove item with ID: " . $id);
        try {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE id = ?");
            $result = $stmt->execute([$id]);
            error_log("Database delete result: " . ($result ? "success" : "failure"));
            $_SESSION['cart_count'] = $this->get_cart_count();
            $_SESSION['total_amount'] = $this->get_cart_total();
            return json_encode(['success' => true]);
        } catch (PDOException $e) {
            error_log("Error removing item: " . $e->getMessage());
            return json_encode(['error' => 'Failed to remove item from cart: ' . $e->getMessage()]);
        }
    }
    
    function get_cart_total() {
        $user_id = $_SESSION['login_user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    
        try {
            if ($user_id) {
                $stmt = $this->db->prepare("SELECT SUM(c.qty * p.price) AS total FROM cart c INNER JOIN product_list p ON p.id = c.product_id WHERE c.user_id = ?");
                $stmt->execute([$user_id]);
            } else {
                $stmt = $this->db->prepare("SELECT SUM(c.qty * p.price) AS total FROM cart c INNER JOIN product_list p ON p.id = c.product_id WHERE c.client_ip = ?");
                $stmt->execute([$ip]);
            }
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)($row['total'] ?: 0); // Return the total as a float
        } catch (PDOException $e) {
            error_log("Failed to get cart total: " . $e->getMessage());
            return 0.0; // Return 0.0 on error
        }
    }
    
    function get_cart_items() {
        $cart_items = [];
        try {
            $sql = "SELECT *, c.id AS cid, c.qty as cart_qty FROM cart c INNER JOIN product_list p ON p.id = c.product_id";
            if (isset($_SESSION['login_user_id'])) {
                $sql .= " WHERE c.user_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$_SESSION['login_user_id']]);
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? null;
                $sql .= " WHERE c.client_ip = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$ip]);
            }
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cart_items[] = $row;
            }
            return json_encode(['items' => $cart_items]);
        } catch (PDOException $e) {
            error_log("Error getting cart items: " . $e->getMessage());
            return json_encode(['error' => 'Failed to get cart items: ' . $e->getMessage()]);
        }
    }		
	// Function to generate a unique reference ID
    function generate_unique_reference_id() {
        $date_part = date('Ymd');
        $random_part = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $reference_id = $date_part . $random_part;
    
        try {
            $stmt = $this->db->prepare("SELECT reference_id FROM orders WHERE reference_id = ?");
            while (true) {
                $stmt->execute([$reference_id]);
                if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
                    break;
                }
                $random_part = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $reference_id = $date_part . $random_part;
            }
            return $reference_id;
        } catch (PDOException $e) {
            error_log("Error generating reference ID: " . $e->getMessage());
            return null; // Or handle the error as needed
        }
    }
    
    function save_order($transaction_reference = null) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        $first_name = trim($_SESSION['login_first_name'] ?? '');
        $last_name = trim($_SESSION['login_last_name'] ?? '');
        $email = trim($_SESSION['login_email'] ?? '');
        $address = trim($_POST['address'] ?? $_SESSION['login_address'] ?? '');
        $mobile = trim($_POST['mobile'] ?? $_SESSION['login_mobile'] ?? '');
        $_SESSION['login_mobile'] = $mobile;
        $_SESSION['login_address'] = $address;
        $transaction_reference = $_POST['payment_reference'] ?? null;
        $delivery_charge = $_POST['delivery_charge'] ?? 0;
        $item_total = $_SESSION['total_amount'];
        $total_amount = $item_total + $delivery_charge;
        $created_at = date('Y-m-d H:i:s');
    
        if (empty($first_name) || empty($last_name) || empty($address) || empty($mobile) || empty($email) || !isset($_SESSION['total_amount'])) {
            echo json_encode(["error" => "Incomplete order details"]);
            exit;
        }
    
        $this->db->beginTransaction();
        try {
            $reference_id = $this->generate_unique_reference_id();
    
            $stmt = $this->db->prepare("INSERT INTO orders (name, address, mobile, email, delivery_charge, total_amount, created_at, item_total, user_id, payment_status, reference_id, transaction_reference) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)");
            $stmt->execute([$first_name . " " . $last_name, $address, $mobile, $email, $delivery_charge, $total_amount, $created_at, $item_total, $_SESSION['login_user_id'], $reference_id, $transaction_reference]);
            $order_id = $this->db->lastInsertId();
    
            $stmt = $this->db->prepare("UPDATE user_info SET address = ?, mobile = ? WHERE email = ?");
            $stmt->execute([$address, $mobile, $email]);
    
            $stmt = $this->db->prepare("SELECT * FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['login_user_id']]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stmt_item = $this->db->prepare("INSERT INTO order_list (order_id, product_id, qty, price, size, soup) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_item->execute([$order_id, $row['product_id'], $row['qty'], $row['price'], $row['size'], $row['soup']]);
            }
    
            $client_ip = $_SERVER['REMOTE_ADDR'];
            $stmt_delete = $this->db->prepare("DELETE FROM cart WHERE user_id = ? OR client_ip = ?");
            $stmt_delete->execute([$_SESSION['login_user_id'], $client_ip]);
    
            $this->db->commit();
            unset($_SESSION['total_amount']);
            $_SESSION['cart_count'] = $this->get_cart_count();
            $_SESSION['total_amount'] = $this->get_cart_total();
            echo json_encode(["success" => "Order saved successfully", "redirect_url" => "/order"]);
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Order processing failed: " . $e->getMessage());
            echo json_encode(["error" => "Order processing failed. Please try again."]);
        }
        exit;
    }
    
    function confirm_order() {
        extract($_POST);
        try {
            $stmt = $this->db->prepare("SELECT delivery_charge FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            $delivery_charge = $order['delivery_charge'];
            $payment_status = ($delivery_charge > 0) ? 2 : 1;
            $confirmed_at = date('Y-m-d H:i:s');
            $stmt_update = $this->db->prepare("UPDATE orders SET payment_status = ?, confirmed_at = ? WHERE id = ?");
            $stmt_update->execute([$payment_status, $confirmed_at, $id]);
            return json_encode(['success' => true]);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to confirm order: ' . $e->getMessage()]);
        }
    }
    
    function cancel_order() {
        extract($_POST);
        try {
            $canceled_at = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare("UPDATE orders SET payment_status = 3, confirmed_at = ? WHERE id = ?");
            $stmt->execute([$canceled_at, $id]);
            return json_encode(['success' => true, 'message' => 'Order canceled successfully.']);
        } catch (PDOException $e) {
            return json_encode(['success' => false, 'message' => 'Failed to cancel order: ' . $e->getMessage()]);
        }
    }

    function count_today_orders() {
        date_default_timezone_set('Africa/Lagos');
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');
    
        try {
            // Pending Orders (payment_status = 0)
            $pending_stmt = $this->db->prepare("SELECT COUNT(*) as pending_count FROM orders WHERE created_at >= :start AND created_at <= :end AND payment_status = 0");
            $pending_stmt->execute([':start' => $today_start, ':end' => $today_end]);
            $pending_count = $pending_stmt->fetch(PDO::FETCH_ASSOC)['pending_count'] ?? 0;
    
            // Confirmed Orders (payment_status = 1 or 2)
            $confirmed_stmt = $this->db->prepare("SELECT COUNT(*) as confirmed_count FROM orders WHERE created_at >= :start AND created_at <= :end AND (payment_status = 1 OR payment_status = 2)");
            $confirmed_stmt->execute([':start' => $today_start, ':end' => $today_end]);
            $confirmed_count = $confirmed_stmt->fetch(PDO::FETCH_ASSOC)['confirmed_count'] ?? 0;
    
            // Rejected Orders (payment_status = 3, adjust if needed)
            $rejected_stmt = $this->db->prepare("SELECT COUNT(*) as rejected_count FROM orders WHERE created_at >= :start AND created_at <= :end AND payment_status = 3");
            $rejected_stmt->execute([':start' => $today_start, ':end' => $today_end]);
            $rejected_count = $rejected_stmt->fetch(PDO::FETCH_ASSOC)['rejected_count'] ?? 0;
    
            // Total Orders (pending, confirmed, rejected)
            $total_orders = $pending_count + $confirmed_count + $rejected_count;
    
            error_log("Today Start: " . $today_start . ", Today End: " . $today_end . ", Pending: " . $pending_count . ", Confirmed: " . $confirmed_count . ", Rejected: " . $rejected_count . ", Total: " . $total_orders);
    
            header('Content-Type: application/json');
            return json_encode([
                'pending' => $pending_count,
                'confirmed' => $confirmed_count,
                'rejected' => $rejected_count,
                'total' => $total_orders
            ]);
        } catch (PDOException $e) {
            error_log("Error counting orders: " . $e->getMessage());
            header('Content-Type: application/json');
            return json_encode(['error' => 'Error counting orders: ' . $e->getMessage()]);
        }
    }

}