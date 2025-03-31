<?php
// Set error reporting level to 1 (errors)
error_reporting(1);

// Include necessary files
include('../essential/backbone.php');

// Check if user_name and sessionId cookies are set
if(isset($_COOKIE['user_name']) && isset($_COOKIE['sessionId'])) {
    // Retrieve username and session key from cookies
    $username = $_COOKIE['user_name'];
    $key = $_COOKIE['sessionId'];

    // Confirm session key validity
    if(confirmSessionKey($username, $key)) {
        // Destroy the session
        session_destroy();
        
        // Establish database connection
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Prepare and execute SQL query to select active status of user
        $q = $db->prepare("SELECT active FROM users WHERE username = ? AND sessionKey = ? LIMIT 1;");
        $q->bind_param('ss', $username, $key);
        $q->execute();
        
        // Get query result
        $res = $q->get_result();

        // If result fetched successfully
        if($res = $res->fetch_array()) {
            // Update sessionKey field to empty for the user
            $db->query("UPDATE users SET sessionKey = '' WHERE id = " . $res['id'] . ";");
			
			// Redirect to homepage after session key removal
            header("Location: /");
            // Echo success message (this may not be effective as the header redirects)
            echo 'success';
        }
        else {
            // Redirect to homepage if unable to fetch result
            header("Location: /");
            exit; // Exit script after redirection
        }
    }
    else {
        // Redirect to homepage if session key is not valid
        header("Location: /");
        exit; // Exit script after redirection
    }
}
else {
    // Redirect to homepage if user_name or sessionId cookies are not set
    header("Location: /");
    exit; // Exit script after redirection
}
?>
