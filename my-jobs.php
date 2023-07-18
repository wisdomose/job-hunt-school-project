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

// Function to fetch all jobs created by the currently logged-in user
function getUserJobs($userId)
{
    $conn = conn();
    if (!$conn) {
        return array(); // Return an empty array if database connection fails
    }

    $jobs = array();
    try {
        // Prepare the SQL query to fetch jobs created by the user with the specified user ID
        $query = "SELECT j.*, IFNULL(app_count.application_count, 0) AS application_count
        FROM jobs j
        LEFT JOIN (
            SELECT job_id, COUNT(*) AS application_count
            FROM applications
            GROUP BY job_id
        ) AS app_count
        ON j.id = app_count.job_id
        WHERE j.owner_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch each job and store in the $jobs array
        while ($row = $result->fetch_assoc()) {
            $row['created_at'] = date("d/m/Y", strtotime($row['created_at'])); // Format the date
            $jobs[] = $row;
        }

        // Close the statement and database connection
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        // Handle the exception if necessary
    }

    return $jobs;
}

// Get all jobs created by the currently logged-in user
$userJobs = getUserJobs($user['id']);

?>
<?php include_once("./components/header.php") ?>

<main class="max-w-3xl px-6 mx-auto">

    <h1 class="text-2xl font-bold py-10">Jobs posted by you</h1>

    <?php if (isset($_SESSION['error'])) { ?>
        <p class="text-red-500">
            <?php echo $_SESSION['error']; ?>
        </p>
    <?php } ?>
    <?php if (isset($_SESSION['success'])) { ?>
        <p class="text-red-500">
            <?php echo $_SESSION['success']; ?>
        </p>
    <?php } ?>

    <?php
    if (empty($userJobs)) { ?>
        <p class="text-center text-gray-500/50">You haven't created a job</p>
    <?php } else { ?>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-10 md:gap-6 md:mt-10">
            <?php
            foreach ($userJobs as $job) { ?>
                <div>
                    <a href="/applications/page.php?id=<?php echo $job['id']; ?>" class="group">
                        <div class="overflow-hidden">
                            <img src="<?php echo $job['cover_url']; ?>" alt="img"
                                class="group-hover:scale-125 duration-150 ease-in-out">
                        </div>
                        <div class="px-3 mt-3">
                            <p><Strong>
                                    <?php echo $job['role']; ?>
                                </Strong></p>
                            <p><strong>
                                    <?php echo $job['application_count']; ?>
                                </strong> applicants</p>
                            <p><strong>Posted on: </strong>
                                <?php echo $job['created_at']; ?>
                            </p>
                        </div>
                    </a>
                    <form method="get" action="./assets/php/delete-job.php" class="px-3 mt-3">
                        <input type="hidden" class="hidden" name="id" value="<?php echo $job['id']; ?>">
                        <input type="submit" value="Delete"
                            class="bg-red text-white font-medium py-2 px-3 text-sm rounded-md cursor-pointer" />
                    </form>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</main>
<?php include_once("./components/footer.php") ?>