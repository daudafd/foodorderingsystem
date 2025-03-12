<?php
ob_start();
session_start();

// Include the class that handles the actions
include 'admin_class.php';
$crud = new Action();

// Define valid actions to prevent invalid input
$valid_actions = [
    'login', 'login2', 'signin', 'logout', 'logout2', 'save_user', 'delete_user', 'signup', 
    'save_settings', 'save_category', 'delete_category', 'save_menu', 
    'delete_menu', 'save_meat_option', 'get_meat_price', 'delete_meat_option', 'save_soup_option', 
    'delete_soup_option', 'add_to_cart', 'get_cart_count', 'update_cart_qty', 
    'save_order', 'confirm_order', 'cancel_order', 'count_today_orders', 'remove_from_cart'
];

// Sanitize the action parameter to ensure it's a valid action
$action = isset($_GET['action']) && in_array($_GET['action'], $valid_actions) ? $_GET['action'] : null;

// Handle count_today_orders correctly
if ($action === 'count_today_orders') {
    header('Content-Type: application/json');
    echo $crud->count_today_orders(); // Use the correct object name
    ob_end_flush();
    exit();
}

// If the action is invalid, return an error and exit
if ($action === null) {
    echo json_encode(['error' => 'Invalid action']);
    ob_end_flush();
    exit;
}

// Handle each action
switch ($action) {
    case 'login':
        $response = $crud->login();
        echo $response; // Return JSON response directly
        break;
    
     case 'login2':
        $response = $crud->login2();
        echo $response; // Return JSON response directly
        break;
    
        case 'set_intended_url':
        $_SESSION['intended_url'] = $_POST['url'];
        break;

    case 'logout':
        $logout = $crud->logout();
        if ($logout) {
            echo json_encode(['success' => 'Logged out successfully']);
        } else {
            echo json_encode(['error' => 'Logout failed']);
        }
        break;

    case 'logout2':
        $logout = $crud->logout2();
        if ($logout) {
            echo json_encode(['success' => 'Logged out successfully']);
        } else {
            echo json_encode(['error' => 'Logout2 failed']);
        }
        break;

    case 'save_user':
        $save = $crud->save_user();
        if ($save) {
            echo json_encode(['success' => 'User saved successfully']);
        } else {
            echo json_encode(['error' => 'Failed to save user']);
        }
        break;

        case 'get_user':
            if (isset($_GET['id'])) {
                $user = $action->get_user($_GET['id']);
                echo json_encode($user);
            }
            break;

            case 'delete_user':
                if (isset($_POST['id'])) {
                    $response = $crud->delete_user($_POST['id']); // Correct function call
                    echo $response; // Should already be JSON-encoded in the function
                } else {
                    echo json_encode(['error' => 'User ID not provided']);
                }
                break;

    case 'signup':
        $save = $crud->signup();
        if ($save) {
			// $_SESSION['login_id'] = $user_id; // Replace with the actual user ID
            echo json_encode(['success' => 'Signup successful']);
        } else {
            echo json_encode(['error' => 'Signup failed']);
        }
        break;

        case 'save_settings':
            $save = $crud->save_settings();
            echo $save; // Directly echo the JSON response from save_settings()
            break;
        

        case 'save_category':
            echo $crud->save_category(); // This will echo the JSON response from the save_category function
            break;
        
        case 'delete_category':
            echo $crud->delete_category(); // This will echo the JSON response from the delete_category function
            break;

        case 'save_menu':
            $save = $crud->save_menu();
            if ($save !== false) {
                echo json_encode(['success' => true, 'message' => 'Menu saved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save menu']);
            }
            break;

        case 'delete_menu':
            $save = $crud->delete_menu();
            if ($save) {
                echo json_encode(['success' => true, 'message' => 'Menu deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete menu']);
            }
            break;

        case 'add_to_cart':
            $save = $crud->add_to_cart();
            if ($save) {
                echo json_encode(['status' => 'success', 'message' => 'Item added to cart successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add item to cart']);
            }
            break;
    
        case 'get_cart_count':
            $cart_count = $crud->get_cart_count();
            if ($cart_count !== false) {
                echo json_encode(['success' => true, 'cart_count' => $cart_count]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to get cart count']);
            }
            break;
            
       case 'save_meat_option':
            echo $crud->save_meat_option(); // Echo the result (1 or 0)
        break;
    
        case 'get_meat_price':
            $meat_type = $_POST['meat_type'];
            $size = $_POST['size'];
            $result = $crud->get_meat_price($meat_type, $size);
            echo json_encode($result);
            break;

        case 'delete_meat_option':
            $delete = $crud->delete_meat_option();
            if ($delete) {
                echo 1;
            } else {
                echo 0;
            }
            break;
            
        case 'save_soup_option':
            echo $crud->save_soup_option(); // Echo the result (1 or 0)
        break;

        case 'delete_soup_option':
            $delete = $crud->delete_soup_option();
            if ($delete) {
                echo 1;
            } else {
                echo 0;
            }
            break;
    
        case 'update_cart_qty':
            $crud->update_cart_qty(); // This function directly echoes its result
            break;
        

    case 'save_order':
        $save = $crud->save_order();
        if ($save) {
            echo json_encode(['success' => 'Order saved successfully']);
        } else {
            echo json_encode(['error' => 'Failed to save order']);
        }
        break;

    case 'confirm_order':
        $save = $crud->confirm_order();
        if ($save) {
            echo json_encode(['success' => 'Order confirmed']);
        } else {
            echo json_encode(['error' => 'Failed to confirm order']);
        }
        break;
        
    case 'cancel_order':
        $save = $crud->cancel_order();
        if ($save) {
            echo json_encode(['success' => 'Order cancelled']);
        } else {
            echo json_encode(['error' => 'Failed to cancelled order']);
        }
        break;
        
        // case 'cancel_order':
        //     echo json_encode($crud->cancel_order()); // Assuming $crud is your class instance
        // break;

        case 'count_today_orders':
            $counts = $crud->count_today_orders();
            echo json_encode($counts); // Return counts as JSON
            break;

            case 'remove_from_cart':
                if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                    $id = $_POST['id'];
                    $response = $crud->remove_from_cart($id); // Get the JSON response
                    $decodedResponse = json_decode($response, true); // Decode JSON
            
                    if ($decodedResponse && isset($decodedResponse['success']) && $decodedResponse['success'] === true) {
                        $total = $crud->get_cart_total();
                        echo json_encode(['success' => true, 'total_amount' => number_format($total, 2)]);
                    } else {
                        echo json_encode(['success' => false, 'error' => $decodedResponse['error'] ?? 'Unknown error.']); // Send error
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'Invalid cart item ID.']);
                }
                break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// End output buffering
ob_end_flush();
?>