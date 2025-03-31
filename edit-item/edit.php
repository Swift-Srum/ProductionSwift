<?php
// Setting error reporting to display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Including necessary files
include('../essential/backbone.php');

// Debugging: Check if file inclusion is successful
if (!file_exists('../essential/backbone.php')) {
    die("Error: backbone.php not found");
}

// Retrieving username and session ID from cookies
$username = $_COOKIE['user_name'] ?? '';
$sessionID = $_COOKIE['sessionId'] ?? '';

// Checking if user is logged in
$loggedIn = confirmSessionKey($username, $sessionID);
$isAdmin = checkIsUserAdmin($username, $sessionID);

// If not logged in, exit script
if (!$isAdmin) {
    die("Error: User not admin");
	exit();
}

// Retrieving user ID
$idx = getUserID();

// Debugging: Check if getUserID function works correctly
if ($idx === false) {
    die("Error: Unable to retrieve user ID");
}

// Function to submit item details to database
function submitItem($id, $name, $price, $details, $buyLink) {
    $aes = new AES256();

    // Establishing database connection
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check for database connection error
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Preparing SQL statement for insertion
    $q = $db->prepare("UPDATE `products` SET `name` = ?, `price` = ?, `details` = ?, `buyLink` = ? WHERE id = ?");
    
    // Check for SQL preparation error
    if (!$q) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    // Binding parameters to the prepared statement
    $q->bind_param('ssssi', $name, $price, $details, $buyLink, $id);
    
    // Executing the prepared statement
    $q->execute();

    // Checking if insertion was successful
    if ($q->affected_rows == 1) {
        // Redirecting with success message
        header("Location: ../edit-item/?itemId=$id&err=" . urlencode($aes->encrypt("Item edited successfully.", "secretkey")));
        exit(); // Exit script after redirection
    }

    // Redirecting with error message if insertion failed
    header("Location: ../edit-item/?itemId=$id&err=" . urlencode($aes->encrypt("An unknown error occurred.", "secretkey")));
    exit(); // Exit script after redirection
}

// Processing POST data if present
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aes = new AES256();

    // Stripping HTML tags from POST data to prevent cross-site scripting
    $id = strip_tags($_POST['id'] ?? '');
    $name = strip_tags($_POST['name'] ?? '');
    $price = strip_tags($_POST['price'] ?? '');
    $details = strip_tags($_POST['details'] ?? '');
    $buyLink = strip_tags($_POST['buyLink'] ?? '');

    // Checking if all required fields are not empty
    if (!empty($id) && !empty($name) && !empty($price) && !empty($details) && !empty($buyLink)) {
        // Calling function to submit item details and exiting script after redirection
        submitItem($id, $name, $price, $details, $buyLink);
    } else {
        // If any required field is empty, return error response and redirect with error message
        header("Location: ../edit-item/?itemId=$id&err=" . urlencode($aes->encrypt("Please fill out all fields", "secretkey")));
        exit(); // Exit script after redirection
    }
} else {
    // If POST data is not present, return response code 990
    echo 'responseCode=990';
}
?>
