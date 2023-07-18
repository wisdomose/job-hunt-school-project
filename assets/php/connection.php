<?php

// Function to establish a database connection
function conn()
{
    $hostname = 'localhost';
    $dbname = 'job';
    $username_db = 'root';
    $password_db = 'root';

    // Create a mysqli connection
    $conn = new mysqli($hostname, $username_db, $password_db, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>