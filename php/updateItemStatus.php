<?php
// Set error reporting level to 1 (errors)
error_reporting(1);

// Include necessary files
include('../essential/backbone.php');

// Retrieve username and session ID from cookies
$username = $_COOKIE['user_name'];
$sessionID = $_COOKIE['sessionId'];

// Check if the user is logged in
$loggedIn = confirmSessionKey($username, $sessionID);
$isAdmin = checkIsUserAdmin($username, $sessionID);

// If not logged in, exit script
if (!$isAdmin) {
    die("Error: User not admin");
	exit();
}


// Get the user ID
$idx = getUserID();

// Check if the user is an admin
$isAdmin = checkIsUserAdmin($username, $sessionID);

// Check if required POST parameters are set
if (isset($_POST['itemId']) && isset($_POST['avaliable'])) {
    // Create AES256 object for encryption/decryption
    $aes = new AES256();
	
	// Retrieve POST data
	$itemId = $_POST['itemId'];
	$avaliable = $_POST['avaliable'];
	
	// Sanitize comments to strip HTML tags
	strip_tags($comments);
	
    // Check if required fields are not empty
    if (!empty($itemId) && !empty($avaliable)) {
		// Update bike status based on the provided status
		if($avaliable == "no")
		{
			updateItemStatusAvaliable($itemId);
		    echo 'responseCode=1';
		    // Redirect with success message
		    header("Location: ../edit-item/?itemId=" . $itemId . "&?err=" . urlencode($aes->encrypt("Status updated.", "secretkey")));
		}
		if($avaliable == "yes")
		{
			updateItemStatusUnavaliable($itemId);
		    echo 'responseCode=1';
		    // Redirect with success message
		    header("Location: ../edit-item/?itemId=" . $itemId . "&?err=" . urlencode($aes->encrypt("Status updated.", "secretkey")));
		}
    }else {
        // Send error response if required fields are empty
        echo 'responseCode=991&status=' . $avaliable;
        // Redirect with error message
        header("Location: ../edit-item/?itemId=" . $itemId . "&?err=" . urlencode($aes->encrypt("An error occurred.", "secretkey")));
    }
} else {
    // Send response code if POST parameters are not set
    echo 'responseCode=990';
}
?>
