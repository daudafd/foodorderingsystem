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
	    $this->db->close();
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
		$stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();
	
		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();
			// Verify the provided password
			if (password_verify($password, $user['password'])) {
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
		} else {
			return json_encode(['success' => false, 'error' => 'User does not exist.']);
		}
	}
	
	
function login2() {
    // Extract POST data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        return json_encode(['success' => false, 'error' => 'Email or Password is empty.']);
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $this->db->prepare("SELECT * FROM user_info WHERE email = ?");
    if (!$stmt) {
        return json_encode(['success' => false, 'error' => 'Database prepare error.']);
    }

    $stmt->bind_param('s', $email);
    if (!$stmt->execute()) {
        return json_encode(['success' => false, 'error' => 'Database execute error.']);
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the provided password against the stored hash
        if (password_verify($password, $user['password'])) {
            // Set session variables for the user (excluding password)
            foreach ($user as $key => $value) {
                if ($key != 'password' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                }
            }

            // Set additional fields if available
            $_SESSION['address'] = $user['address'] ?? '';
            $_SESSION['mobile'] = $user['mobile'] ?? '';

            // Transfer guest cart to user cart if necessary
           // Check if there are items in the session's cart and move them to the user's cart
                if (isset($_SESSION['cart_items']) && !empty($_SESSION['cart_items'])) {
                    foreach ($_SESSION['cart_items'] as $item) {
                        // Insert into the cart table
                        $cart_stmt = $this->db->prepare("INSERT INTO cart (product_id, qty, user_id, price, size, soup) VALUES (?, ?, ?, ?, ?, ?)");
                        if (!$cart_stmt) {
                            return json_encode(['success' => false, 'error' => 'Database cart insert prepare error.']);
                        }
            
                        // Ensure size is handled correctly
                        $size = isset($item['size']) && !empty($item['size']) ? $item['size'] : null;
            
                        // Corrected bind_param: 'iidsss'
                        $cart_stmt->bind_param('iidsss', $item['product_id'], $item['qty'], $_SESSION['login_user_id'], $item['price'], $size, $item['soup']);
            
                        if (!$cart_stmt->execute()) {
                            return json_encode(['success' => false, 'error' => 'Database cart insert execute error.']);
                        }
                    }
                    // Clear the session's cart after transferring the items to the database
                    unset($_SESSION['cart_items']);
            
                    //delete guest cart items from database.
                    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
                    if($ip){
                        $this->db->query("DELETE FROM cart WHERE client_ip = '$ip'");
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
    } else {
        return json_encode(['success' => false, 'error' => 'User does not exist.']);
    }
}
			
	// Helper function to get the client's IP address
	private function getClientIP() {
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}
    
	function save_user() {
		// Check for required fields in POST request
		if (!isset($_POST['name'], $_POST['username'], $_POST['password'], $_POST['type'])) {
			die(json_encode(['status' => 'error', 'message' => 'Missing required fields.']));
		}
	
		// Sanitize input data
		$name = $this->db->real_escape_string($_POST['name']);
		$username = $this->db->real_escape_string($_POST['username']);
		$password = $_POST['password'] ?? '';
		$type = "2";

		// Prepare data string
		$data = "name = '$name', username = '$username', type = '$type'";
		if (!empty($password)) {
			$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
			$data .= ", password = '$hashedPassword'";
		}
	
		// Insert or update logic
		if (empty($_POST['id'])) {
			$query = "INSERT INTO users SET $data";
		} else {
			$id = $this->db->real_escape_string($_POST['id']);
			$query = "UPDATE users SET $data WHERE id = '$id'";
		}
	
		// Execute query and handle errors
		$result = $this->db->query($query);
		if (!$result) {
			die(json_encode(['status' => 'error', 'message' => 'Database Error: ' . $this->db->error]));
		}
	
		// Success response
		return json_encode(['status' => 'success', 'message' => 'User saved successfully.']);
	}

	function get_user($id) {

        $query = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();
        $query->close();

        return $user;
    }

	function delete_user($id) {
		// Sanitize the user ID
		$id = $this->db->real_escape_string($id);
	
		// Query to delete the user
		$query = "DELETE FROM users WHERE id = '$id'";
		$result = $this->db->query($query);
	
		// Debugging: Check if query executed successfully
		if ($result) {
			return json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
		} else {
			// Log the SQL error
			error_log("Delete User Error: " . $this->db->error);
			return json_encode(['status' => 'error', 'message' => 'Failed to delete user.']);
		}
	}
			
	function signup() {
		// Check if any field is empty
		$fields = ['first_name', 'last_name', 'mobile', 'address', 'email', 'password'];
		foreach ($fields as $field) {
			if (empty($_POST[$field])) {
				error_log("Field '$field' is empty.");
			}
		}
	
		// Validation checks
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
	
		// Extract and sanitize POST values
		extract($_POST);
		$email = $this->db->real_escape_string(strtolower($email));
		$password_hashed = password_hash($password, PASSWORD_DEFAULT);
	
		$data = "
			first_name = '" . $this->db->real_escape_string($first_name) . "',
			last_name = '" . $this->db->real_escape_string($last_name) . "',
			mobile = '" . $this->db->real_escape_string($mobile) . "',
			address = '" . $this->db->real_escape_string($address) . "',
			email = '$email',
			password = '$password_hashed'
		";
	
		// Check if email already exists
		$email = strtolower(trim($_POST['email'])); // Ensure consistency

        $chk_query = "SELECT COUNT(*) as count FROM user_info WHERE email = '$email'";
        $chk_result = $this->db->query($chk_query);
        
        if (!$chk_result) {
            error_log("Email check query failed: " . $this->db->error);
            exit(json_encode(['error' => 'Database error while checking email existence.']));
        }
        
        $chk_row = $chk_result->fetch_assoc();
        $chk = $chk_row['count'] ?? 0;
        
        error_log("Email Check Result: " . json_encode($chk_row)); // Debug log
        
        if ($chk > 0) {
            exit(json_encode(['error' => 'Email already exists.']));
        }
	
		// Insert user into the database
		$save = $this->db->query("INSERT INTO user_info SET " . $data);
		if ($save) {
			$login = $this->login2(); // Log the user in after successful signup
			return json_encode(['success' => 'Signup successful']);
		} else {
			// Log the database error in case of failure
			error_log("Signup Error: " . $this->db->error);
			return json_encode(['error' => 'Signup failed. Database error: ' . $this->db->error]);
		}
	}

	function save_settings() {
		$system_settings_id = null;
	
		$chk = $this->db->query("SELECT id FROM system_settings LIMIT 1");
		if ($chk && $chk->num_rows > 0) {
			$row = $chk->fetch_assoc();
			$system_settings_id = $row['id'];
		}
	
		// Update System Settings (Conditionally)
		$update_fields = [];
		$bind_params = "";
		$bind_values = [];
	
		if (isset($_POST['name'])) {
			$update_fields[] = "name = ?";
			$bind_params .= "s";
			$bind_values[] = $_POST['name'];
		}
		if (isset($_POST['email'])) {
			$update_fields[] = "email = ?";
			$bind_params .= "s";
			$bind_values[] = $_POST['email'];
		}
		if (isset($_POST['contact'])) {
			$update_fields[] = "contact = ?";
			$bind_params .= "s";
			$bind_values[] = $_POST['contact'];
		}
		if (isset($_POST['about'])) {
			$update_fields[] = "about_content = ?";
			$bind_params .= "s";
			$bind_values[] = $_POST['about'];
		}
	
		if (!empty($update_fields) && $system_settings_id) { // Only update if there are fields to update AND the system settings ID exists
			$update_query = "UPDATE system_settings SET " . implode(", ", $update_fields) . " WHERE id = ?";
			$bind_params .= "i";
			$bind_values[] = $system_settings_id;
	
			$stmt = $this->db->prepare($update_query);
			if (!$stmt) {
				error_log("Prepare failed: " . $this->db->error);
				return json_encode(['error' => "Database error."]);
			}
	
			$stmt->bind_param($bind_params, ...$bind_values);
	
			if (!$stmt->execute()) {
				error_log("Execute failed: " . $stmt->error);
				return json_encode(['error' => "Failed to update settings."]);
			}
			$stmt->close();
		} else if (empty($system_settings_id) && (isset($_POST['name']) || isset($_POST['email']) || isset($_POST['contact']) || isset($_POST['about']))){
			$stmt = $this->db->prepare("INSERT INTO system_settings (name, email, contact, about_content) VALUES (?, ?, ?, ?)"); // Removed cover_img from here
			if (!$stmt) {
				error_log("Prepare failed: " . $this->db->error);
				return json_encode(['error' => "Database error."]);
			}
			$stmt->bind_param("ssss", $name, $email, $contact, $about);
	
			if (!$stmt->execute()) {
				error_log("Execute failed: " . $stmt->error);
				return json_encode(['error' => "Failed to insert settings."]);
			}
			$stmt->close();
			$system_settings_id = $this->db->insert_id;
		}
	
		// Handle Multiple Image Uploads (Conditionally)
		if (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name']) && $system_settings_id) {
			//Delete Existing Banner Images
			$del_banners = $this->db->query("DELETE FROM banner_images where system_settings_id = ".$system_settings_id);
			$files = $_FILES['images'];
	
			for ($i = 0; $i < count($files['tmp_name']); $i++) {
				if (!empty($files['tmp_name'][$i])) {
					// ... (Image upload and database insert logic as before)
					$fname = strtotime(date('y-m-d H:i')) . '_' . $files['name'][$i];
					$target_dir = '../assets/img/';
					$target_file = $target_dir . $fname;
					$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
	
					$check = getimagesize($files["tmp_name"][$i]);
					if ($check === false) {
						return json_encode(['error' => "File is not an image."]);
					}
	
					if ($files["size"][$i] > 5000000) { // Increased to 5MB
						return json_encode(['error' => "Sorry, one of your files is too large. Max 5MB allowed."]);
					}
	
					if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
						return json_encode(['error' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."]);
					}
	
					if (move_uploaded_file($files['tmp_name'][$i], $target_file)) {
						// Insert image path into banner_images table
						$stmt = $this->db->prepare("INSERT INTO banner_images (system_settings_id, image_path) VALUES (?, ?)");
						$stmt->bind_param("is", $system_settings_id, $fname);
						if (!$stmt->execute()) {
							error_log("Failed to insert banner image: " . $stmt->error);
							return json_encode(['error' => "Failed to save banner images."]);
						}
						$stmt->close();
					} else {
						return json_encode(['error' => "Failed to upload one or more images."]);
					}
				}
			}
		}
	
			// Update session variables
			$query = $this->db->query("SELECT * FROM system_settings LIMIT 1");
			if ($query && $query->num_rows>0) {
			  $query = $query->fetch_array();
			  foreach ($query as $key => $value) {
				  if (!is_numeric($key)) {
					  $_SESSION['setting_'.$key] = $value;
				  }
			  }
			}
	
		return json_encode(['success' => 'Settings saved successfully']);
	}

	function save_category(){
		extract($_POST);
		$data = " name = '$name' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO category_list SET ".$data);
			if ($save) {
				return json_encode(['success' => 'Category added successfully']);
			} else {
				return json_encode(['error' => 'Failed to add category']);
			}
		} else {
			$save = $this->db->query("UPDATE category_list SET ".$data." WHERE id=".$id);
			if ($save) {
				return json_encode(['success' => 'Category updated successfully']);
			} else {
				return json_encode(['error' => 'Failed to update category']);
			}
		}
	}
	
	function delete_category(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM category_list WHERE id = ".$id);
		if ($delete) {
			return json_encode(['success' => 'Category deleted successfully']);
		} else {
			return json_encode(['error' => 'Failed to delete category']);
		}
	}
	
    function save_menu(){
        extract($_POST);
        $data = " name = '$name' ";
        $data .= ", price = '$price' ";
        $data .= ", category_id = '$category_id' ";
        $data .= ", description = '$description' ";
        if(isset($status) && $status == 'on')
            $data .= ", status = 1 ";
        else
            $data .= ", status = 0 ";

        // Check if the item has size options (for meat items)
        if(isset($has_size_options) && $has_size_options == 'on'){
            $data .= ", price_small = '$price_small' ";
            $data .= ", price_medium = '$price_medium' ";
            $data .= ", price_large = '$price_large' ";
        } else {
            // Clear the extra pricing if not applicable
            $data .= ", price_small = NULL, price_medium = NULL, price_large = NULL ";
        }
        
        // Handle image upload if provided
        if($_FILES['img']['tmp_name'] != ''){
            $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
            $move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
            $data .= ", img_path = '$fname' ";
        }
        
        if(empty($id)){
            $save = $this->db->query("INSERT INTO product_list SET ".$data);
        } else {
            $save = $this->db->query("UPDATE product_list SET ".$data." WHERE id=".$id);
        }
        if($save)
            return 1;
        else
            return 0;
    }
    
    // Delete a menu item
    function delete_menu(){
        extract($_POST);
        $delete = $this->db->query("DELETE FROM product_list WHERE id = ".$id);
        if($delete)
            return 1;
        else
            return 0;
    }
	
	function get_menu_data($id) {
        $qry = $this->db->query("SELECT * FROM product_list WHERE id = $id");
        return $qry->num_rows > 0 ? $qry->fetch_assoc() : false;
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

    function delete_meat_option() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM meat_options WHERE id = " . $id);
        if ($delete) {
            return 1;
        } else {
            return 0;
        }
    }
    
        function save_soup_option() {
        extract($_POST);
        $data = " soup_type = '$soup_type' ";

        if (empty($id)) {
            $save = $this->db->query("INSERT INTO soup_options set " . $data);
        } else {
            $save = $this->db->query("UPDATE soup_options set " . $data . " WHERE id = " . $id);
        }
        if ($save) {
            return 1; // Return 1 for success
        } else {
            return 0; // Return 0 for failure
        }
    }

    function delete_soup_option() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM soup_options WHERE id = " . $id);
        if ($delete) {
            return 1;
        } else {
            return 0;
        }
    }

function add_to_cart() {
    extract($_POST);
    $data = " product_id = $pid ";
    $qty = isset($qty) ? $qty : 1;
    $data .= ", qty = $qty ";

    // Add size to the data
    if (isset($size) && !empty($size)) {
        $data .= ", size = '$size' ";
    }

    // Add soup choice to the data
    if (isset($soup_choice) && !empty($soup_choice)) {
        $data .= ", soup = '$soup_choice' "; // Using soup instead of soup_choice
    }

    // Fetch product details
    $product = $this->db->query("SELECT * FROM product_list WHERE id = $pid")->fetch_array();

    // Determine price based on product and size
    if ($product && isset($size)) {
        $meat_option = $this->db->query("SELECT price FROM meat_options WHERE meat_type = '" . $product['name'] . "' AND size = '$size'")->fetch_array();
        if ($meat_option) {
            $price = $meat_option['price'];
        } else {
            $price = $product['price'];
        }
    } else {
        $price = $product['price'];
    }
    $data .= ", price = '$price'";

    if (isset($_SESSION['login_user_id'])) {
        $data .= ", user_id = '" . $_SESSION['login_user_id'] . "' ";
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $data .= ", client_ip = '$ip' ";

        if (!isset($_SESSION['cart_items'])) {
            $_SESSION['cart_items'] = [];
        }
        $_SESSION['cart_items'][] = [
            'product_id' => $pid,
            'qty' => $qty,
            'size' => $size ?? null,
            'soup' => $soup_choice ?? null, // Using soup instead of soup_choice
            'price' => $price
        ];
    }

    $save = $this->db->query("INSERT INTO cart set " . $data);
    if ($save) {
        $_SESSION['cart_count'] = $this->get_cart_count();
        return true;
    }
    return false;
}

	function get_cart_count() {
        $user_id = $_SESSION['login_user_id'] ?? null;
		$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
        // $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        // Construct query based on user or guest
        if ($user_id) {
            $query = "SELECT SUM(qty) AS total_items FROM cart WHERE user_id = $user_id";
        } else {
            $query = "SELECT SUM(qty) AS total_items FROM cart WHERE client_ip = '$ip'";
        }

        $result = $this->db->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total_items'] ?: 0; // Return the count or 0 by default
        }
        return false;
    }

	function update_cart_qty(){
		extract($_POST);
		$data = " qty = $qty ";
		$save = $this->db->query("UPDATE cart SET ".$data." WHERE id = ".$id);
		
		if ($save) {
			// update cart_session
			$_SESSION['cart_count'] = $this->get_cart_count();
			
			// Fetch the updated item total (unit price * qty)
			$result = $this->db->query("SELECT p.price, c.qty FROM cart c INNER JOIN product_list p ON p.id = c.product_id WHERE c.id = ".$id);
			$row = $result->fetch_assoc();
			$item_total = $row['price'] * $row['qty'];
			
			// Calculate the new total cart amount
			$cart_result = $this->db->query("SELECT SUM(c.qty * p.price) AS total_amount FROM cart c INNER JOIN product_list p ON p.id = c.product_id");
			$cart_row = $cart_result->fetch_assoc();
			$total_amount = $cart_row['total_amount'];
			
			// Return the updated item total and cart total
			echo json_encode([
				'success' => true,
				'item_total' => number_format($item_total, 2),
				'total_amount' => number_format($total_amount, 2)
			]);
		} else {
			echo json_encode(['error' => 'Failed to update cart quantity']);
		}
	}
	
	function remove_from_cart($id) {
        $id = $this->db->real_escape_string($id);
        $delete = $this->db->query("DELETE FROM cart WHERE id = $id");
        if ($delete) {
			// Update session cart count and total amount after deletion
			$_SESSION['cart_count'] = $this->get_cart_count(); 
			$_SESSION['total_amount'] = $this->get_cart_total(); 
            return true;
        } else {
            return $this->db->error; // Return the error message
        }
    }
	
    function get_cart_total() {
            $total = 0;
            $sql = "SELECT *, c.id AS cid FROM cart c INNER JOIN product_list p ON p.id = c.product_id";
            if (isset($_SESSION['login_user_id'])) {
                $sql .= " WHERE c.user_id = '" . $_SESSION['login_user_id'] . "'";
            } else {
                $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
                $sql .= " WHERE c.client_ip = '" . $ip . "'";
            }
            $result = $this->db->query($sql);
            while ($row = $result->fetch_assoc()) {
                $total += ($row['qty'] * $row['price']);
            }
            return $total;
    }

		function get_cart_items() {
			$cart_items = array();
			$sql = "SELECT *, c.id AS cid, c.qty as cart_qty FROM cart c INNER JOIN product_list p ON p.id = c.product_id";
			if (isset($_SESSION['login_user_id'])) {
				$sql .= " WHERE c.user_id = '" . $conn->real_escape_string($_SESSION['login_user_id']) . "'";
			} else {
				$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
				$sql .= " WHERE c.client_ip = '" . $conn->real_escape_string($ip) . "'";
			}
			$qry = $conn->query($sql);
			if ($qry) {
				while ($row = $qry->fetch_assoc()) {
					$cart_items[] = $row;
				}
			} else {
				error_log("Error getting cart items: " . $conn->error);
			}
			return $cart_items;
		}
		
		// Function to generate a unique reference ID
        function generate_unique_reference_id() {
            $date_part = date('Ymd'); // Get the date in YYYYMMDD format
            $random_part = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT); // Generate a 6-digit number
        
            $reference_id = $date_part . $random_part; // Combine date and random number
        
            // Optional: Check if the ID already exists and regenerate if needed
            while ($this->db->query("SELECT reference_id FROM orders WHERE reference_id = '" . $this->db->real_escape_string($reference_id) . "'")->num_rows > 0) {
                $random_part = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $reference_id = $date_part . $random_part;
            }
        
            return $reference_id;
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

    if (empty($first_name) || empty($last_name) || empty($address) || empty($mobile) || empty($email)) {
        echo json_encode(["error" => "Incomplete order details"]);
        exit;
    }

    if (!isset($_SESSION['total_amount'])) {
        echo json_encode(["error" => "Total amount is missing."]);
        exit;
    }
    
    // Get the current date and time
    $created_at = date('Y-m-d H:i:s'); // Format: YYYY-MM-DD HH:MM:SS

    $this->db->begin_transaction();
    try {
        // Generate a unique reference ID
        $reference_id = $this->generate_unique_reference_id();
        
        $data = " name = '" . $first_name . " " . $last_name . "' ";
        $data .= ", address = '" . $this->db->real_escape_string($address) . "' ";
        $data .= ", mobile = '" . $this->db->real_escape_string($mobile) . "' ";
        $data .= ", email = '" . $this->db->real_escape_string($email) . "' ";
        $data .= ", delivery_charge = '$delivery_charge' ";
        $data .= ", total_amount = '$total_amount' ";
        $data .= ", created_at = '$created_at' ";
        $data .= ", item_total = '$item_total' ";
        $data .= ", user_id = '" . $_SESSION['login_user_id'] . "' ";
        $data .= ", payment_status = 0";
        $data .= ", reference_id = '" . $this->db->real_escape_string($reference_id) . "' "; // Add the reference ID

        if ($transaction_reference !== null) {
            $data .= ", transaction_reference = '" . $this->db->real_escape_string($transaction_reference) . "' ";
        } else {
            $data .= ", transaction_reference = NULL ";
        }

        $save = $this->db->query("INSERT INTO orders SET " . $data);
        if (!$save) {
            throw new Exception("Failed to insert order: " . $this->db->error);
        }

        $order_id = $this->db->insert_id;

        if (empty($email)) {
            throw new Exception("Email is not set or invalid.");
        }

        $update_user_info = "UPDATE user_info SET address = '" . $this->db->real_escape_string($address) . "', mobile = '" . $this->db->real_escape_string($mobile) . "' WHERE email = '" . $this->db->real_escape_string($email) . "'";

        error_log("Update Query: " . $update_user_info);

        if (!$this->db->query($update_user_info)) {
            throw new Exception("Failed to update user info: " . $this->db->error);
        }

        $qry = $this->db->query("SELECT * FROM cart WHERE user_id = " . $_SESSION['login_user_id']);
        if (!$qry) {
            throw new Exception("Failed to fetch cart: " . $this->db->error);
        }

        while ($row = $qry->fetch_assoc()) {
            $item_data = " order_id = '$order_id' ";
            $item_data .= ", product_id = '" . $row['product_id'] . "' ";
            $item_data .= ", qty = '" . $row['qty'] . "' ";
            $item_data .= ", price = '" . $row['price'] . "' ";
            $item_data .= ", size = '" . $row['size'] . "' ";
            $item_data .= ", soup = '" . $row['soup'] . "' ";

            if (!$this->db->query("INSERT INTO order_list SET " . $item_data)) {
                throw new Exception("Failed to insert order item: " . $this->db->error);
            }

            $client_ip = $_SERVER['REMOTE_ADDR'];
            if (!$this->db->query("DELETE FROM cart WHERE user_id = " . $_SESSION['login_user_id'] . " OR client_ip = '" . $this->db->real_escape_string($client_ip) . "'")) {
                throw new Exception("Failed to delete cart items: " . $this->db->error);
            }
        }

        $this->db->commit();
        unset($_SESSION['total_amount']);

        $_SESSION['cart_count'] = $this->get_cart_count();
        $_SESSION['total_amount'] = $this->get_cart_total();

        echo json_encode(["success" => "Order saved successfully", "redirect_url" => "/order"]);
    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Order processing failed: " . $e->getMessage());
        echo json_encode(["error" => "Order processing failed. Please try again."]);
    }
    exit;
}
		

	function confirm_order() {
    extract($_POST);

    // Fetch the delivery charge from the database based on order ID
    $order = $this->db->query("SELECT delivery_charge FROM orders WHERE id = $id")->fetch_assoc();
    $delivery_charge = $order['delivery_charge'];

    // Set payment status based on the delivery charge
    $payment_status = ($delivery_charge > 0) ? 2 : 1;

    // Get the current date and time
    $confirmed_at = date('Y-m-d H:i:s'); // Format: YYYY-MM-DD HH:MM:SS

    // Update the order with payment status and confirmed_at
    $save = $this->db->query("UPDATE orders SET payment_status = $payment_status, confirmed_at = '$confirmed_at' WHERE id = $id");

    if ($save) {
        return 1;
    } else {
        return 0;
    }
     exit;
}

    function cancel_order() {
    extract($_POST);

    // Get the current date and time
    $canceled_at = date('Y-m-d H:i:s');

    // Update the order with canceled status and canceled_at
    $save = $this->db->query("UPDATE orders SET payment_status = 3, confirmed_at = '$canceled_at' WHERE id = $id");

    if ($save) {
        return json_encode(['success' => true, 'message' => 'Order canceled successfully.']);
    } else {
        return json_encode(['success' => false, 'message' => 'Failed to cancel order.']);
    }
    exit;
}

function count_today_orders() {
    date_default_timezone_set('Africa/Lagos');
    $today_start = date('Y-m-d 00:00:00');
    $today_end = date('Y-m-d 23:59:59');

    if (!$this->db) {
        error_log("Database connection failed in count_today_orders.");
        return ['error' => 'Database connection not available'];
    } else {
        error_log("Database connection successful in count_today_orders.");
    }

    // Pending Orders (payment_status = 0)
    $pending_query = "SELECT COUNT(*) as pending_count FROM orders WHERE created_at >= '$today_start' AND created_at <= '$today_end' AND payment_status = 0";
    $pending_result = $this->db->query($pending_query);

    if (!$pending_result) {
        error_log("Pending query failed: " . $this->db->error);
        return ['error' => 'Pending query failed: ' . $this->db->error];
    }

    $pending_count = $pending_result->fetch_assoc()['pending_count'] ?? 0;

    // Confirmed Orders (payment_status = 1 or 2)
    $confirmed_query = "SELECT COUNT(*) as confirmed_count FROM orders WHERE created_at >= '$today_start' AND created_at <= '$today_end' AND (payment_status = 1 OR payment_status = 2)";
    $confirmed_result = $this->db->query($confirmed_query);

    if (!$confirmed_result) {
        error_log("Confirmed query failed: " . $this->db->error);
        return ['error' => 'Confirmed query failed: ' . $this->db->error];
    }

    $confirmed_count = $confirmed_result->fetch_assoc()['confirmed_count'] ?? 0;

    // Rejected Orders (payment_status = 3, adjust if needed)
    $rejected_query = "SELECT COUNT(*) as rejected_count FROM orders WHERE created_at >= '$today_start' AND created_at <= '$today_end' AND payment_status = 3";
    $rejected_result = $this->db->query($rejected_query);

    if (!$rejected_result) {
        error_log("Rejected query failed: " . $this->db->error);
        return ['error' => 'Rejected query failed: ' . $this->db->error];
    }

    $rejected_count = $rejected_result->fetch_assoc()['rejected_count'] ?? 0;

    // Total Orders (pending, confirmed, rejected)
    $total_orders = $pending_count + $confirmed_count + $rejected_count;

    error_log("Today Start: " . $today_start . ", Today End: " . $today_end . ", Pending: " . $pending_count . ", Confirmed: " . $confirmed_count . ", Rejected: " . $rejected_count . ", Total: " . $total_orders);

    return [
        'pending' => $pending_count,
        'confirmed' => $confirmed_count,
        'rejected' => $rejected_count,
        'total' => $total_orders
    ];
}

}