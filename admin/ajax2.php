<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
session_start();

// Include the class that handles the actions
include 'admin_class2.php';
$crud = new Action();

// Define valid actions to prevent invalid input
$valid_actions = [
    'reset_password', 'update_password'
];

// Sanitize the action parameter to ensure it's a valid action
$action = isset($_GET['action']) && in_array($_GET['action'], $valid_actions) ? $_GET['action'] : null;

// If the action is invalid, return an error and exit
if ($action === null) {
    echo json_encode(['error' => 'Invalid action']);
    ob_end_flush();
    exit;
}

// Handle each action
switch ($action) {
        case 'reset_password':
            $save = $crud->reset_password();
            echo $save;
            break;
       
        case 'update_password':
            $save = $crud->update_password();
            echo $save;
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
}

// End output buffering
ob_end_flush();
?>