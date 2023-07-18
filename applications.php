<?php
include_once("./assets/php/connection.php");

session_start();

$user = isset($_SESSION["user"]) ? $_SESSION["user"] : null;

// Check if the user is logged in
if (!$user) {
    // Redirect to the login page since the user is not logged in
    header("Location: /marketplace.php");
    exit();
}
// Function to fetch all the applications for a specific user with job details
function getUserApplications($userId)
{
    $conn = conn();
    if (!$conn) {
        return array(); // Return an empty array if database connection fails
    }

    $applications = array();
    try {
        // Prepare the SQL query to fetch applications for the specific user and join with the jobs table
        $query = "SELECT a.*, j.*, 
        (SELECT COUNT(*) FROM applications app WHERE app.job_id = a.job_id) AS applicants_count
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE a.applicant_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch each application with job details and store in the $applications array
        while ($row = $result->fetch_assoc()) {
            $row['created_at'] = date("d/m/Y", strtotime($row['created_at'])); // Format the date
            $applications[] = $row;
        }

        // Close the statement and database connection
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        // Handle the exception if necessary
    }

    return $applications;
}

// Get the user's applications with job details
$userApplications = getUserApplications($user['id']);
?>

<?php include_once("./components/header.php") ?>

<main class="max-w-3xl mx-auto px-6">

    <h1 class="text-2xl font-bold py-10">Jobs you applied to</h1>

    <?php
    if (empty($userApplications)) { ?>
        <p class="text-center text-gray-500/50">You haven't applied to a job</p>
    <?php } else { ?>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-10 md:gap-6 md:mt-10">
            <?php
            foreach ($userApplications as $job) { ?>
                <div class="group">
                    <a href="/applications/page.php?id=<?php echo $job['id']; ?>">
                        <div class="overflow-hidden">
                            <img src="<?php echo $job['cover_url']; ?>" alt="img"
                                class="group-hover:scale-125 duration-150 ease-in-out">
                        </div>
                        <div class="px-3 mt-3">
                            <p><Strong>
                                    <?php echo $job['role']; ?>
                                </Strong></p>
                            <p><strong>
                                    <?php echo $job['applicants_count']; ?>
                                </strong> applicants</p>
                            <p><strong>applied on: </strong>
                                <?php echo $job['created_at']; ?>
                            </p>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</main>
<?php include_once("./components/footer.php") ?>