<?php
// 1. Initialize the session
session_start();
 
// 2. Unset all session variables
$_SESSION = array();
 
// 3. Destroy the session
session_destroy();
 
// 4. Redirect to home page with a special action
header("location: index.php?action=login");
exit;
?>