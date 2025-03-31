<?php
// Suppress error reporting
error_reporting(0);

// Include necessary files
include('../essential/backbone.php');
$origin = $_SERVER['HTTP_REFERER'];

// Create AES256 object for encryption/decryption
$aes = new AES256();

// Retrieve bike ID, username, and session ID from POST and cookies respectively
$itemId = $_POST['id'];
$username = $_COOKIE['user_name'];
$sessionID = $_COOKIE['sessionId'];

$isAdmin = checkIsUserAdmin($username, $sessionID);

// If not logged in, exit script
if (!$isAdmin) {
    die("Error: User not admin");
	exit();
}


// Check if the user is logged in
$loggedIn = confirmSessionKey($username, $sessionID);

// If the user is not logged in, send a response code and exit
if ($loggedIn != true)
{
    echo 'responseCode=999';
    exit();
}

// Get the user ID
$idx = getUserID();

// Get the owner of the bike
$itemOwner = getItemOwner($itemId);

// If the user is not the owner of the bike and is not an admin, send a response code and exit
if ($idx != $itemId && $isAdmin != true)
{
    echo 'responseCode=999';
    exit();
}
else
{
    // Delete the bike
    deleteItem($itemId);
    
    // Send a success response code and redirect with a success message
    echo 'responseCode=1';
	header("Location: ".$origin);
}
?>
