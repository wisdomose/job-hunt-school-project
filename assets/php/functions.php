<?php
// Function to check if email or username already exists in the database
function checkIfEmailOrUsernameExists($email, $username)
{
    $conn = conn();

    // Prepare the SQL query
    $query = "SELECT COUNT(*) as count FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Return true if the email or username already exists, false otherwise
    return ($row['count'] > 0);
}

// Function to delete a job by its ID along with associated applications
function deleteJob($jobId)
{
    $conn = conn();
    if (!$conn) {
        return false; // Return false if database connection fails
    }

    try {
        // Start a transaction to ensure both deletions are executed together
        $conn->begin_transaction();

        // Prepare the SQL query to delete the applications associated with the job
        $queryDeleteApplications = "DELETE FROM applications WHERE job_id = ?";
        $stmtDeleteApplications = $conn->prepare($queryDeleteApplications);
        $stmtDeleteApplications->bind_param("i", $jobId);

        // Execute the query to delete the applications
        $resultDeleteApplications = $stmtDeleteApplications->execute();

        // Prepare the SQL query to delete the job with the specified ID
        $queryDeleteJob = "DELETE FROM jobs WHERE id = ?";
        $stmtDeleteJob = $conn->prepare($queryDeleteJob);
        $stmtDeleteJob->bind_param("i", $jobId);

        // Execute the query to delete the job
        $resultDeleteJob = $stmtDeleteJob->execute();

        // Commit the transaction if both queries executed successfully
        if ($resultDeleteApplications && $resultDeleteJob) {
            $conn->commit();
        } else {
            // Rollback the transaction if any of the queries failed
            $conn->rollback();
        }

        // Close the statements and database connection
        $stmtDeleteApplications->close();
        $stmtDeleteJob->close();
        $conn->close();

        return $resultDeleteJob; // Return true on successful deletion, false otherwise
    } catch (Exception $e) {
        // Handle the exception if necessary
        $conn->rollback(); // Rollback the transaction on exception
    }

    return false;
}
?>