<?php
// Start the session
session_start();

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect the user to the login page or any other desired page after logout
header("Location: marketplace.php");
exit();
?>
