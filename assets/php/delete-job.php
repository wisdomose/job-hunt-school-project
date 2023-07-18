<?php
include_once("./connection.php");
include_once("./functions.php");

// Check if the user is logged in
session_start();
$user = isset($_SESSION["user"]) ? $_SESSION["user"] : null;

if (!$user) {
    // Redirect to the login page if the user is not logged in
    header("Location: /marketplace.php");
    exit();
}

// Check if the job ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $jobId = intval($_GET['id']);

    // Fetch the job from the database to verify if the current user is the creator
    $conn = conn();
    if (!$conn) {
        $_SESSION['error'] = "Failed to connect to the database. Please try again later.";
        header("Location: /my-jobs.php");
        exit();
    }

    try {
        // Prepare the SQL query to fetch the job by its ID
        $query = "SELECT owner_id FROM jobs WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $jobId);
        $stmt->execute();
        $result = $stmt->get_result();
        $jobData = $result->fetch_assoc();

        // Close the statement and database connection
        $stmt->close();
        $conn->close();

        // Check if the current user is the job creator
        if (!$jobData || $jobData['owner_id'] !== $user['id']) {
            $_SESSION['error'] = "You are not authorized to delete this job.";
            header("Location: /my-jobs.php");
            exit();
        }

        // Call the deleteJob function to delete the job by its ID
        if (deleteJob($jobId)) {
            // Successful deletion
            $_SESSION['success'] = "Job deleted successfully!";
            // Redirect back to the page listing all jobs (e.g., marketplace.php)

        } else {
            // Failed to delete
            $_SESSION['error'] = "Failed to delete the job. Please try again later.";
        }
    } catch (Exception $e) {
        // Handle the exception if necessary
        $_SESSION['error'] = "An error occurred while processing the request. Please try again later.";

    }
}

// Redirect back to the page listing all jobs (e.g., marketplace.php)
header("Location: /my-jobs.php");
exit();
?>