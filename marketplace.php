<?php
include_once("./assets/php/connection.php");

session_start();

$user = isset($_SESSION["user"]) ? $_SESSION["user"] : null;


// Function to fetch all jobs from the database
function getAllJobs()
{
    $conn = conn();
    if (!$conn) {
        return array(); // Return an empty array if database connection fails
    }

    $jobs = array();
    try {
        // Prepare the SQL query to fetch all jobs
        $query = "SELECT j.*, u.username, COUNT(a.job_id) AS application_count 
        FROM jobs j 
        INNER JOIN users u ON j.owner_id = u.id 
        LEFT JOIN applications a ON j.id = a.job_id 
        GROUP BY j.id";
        $result = $conn->query($query);

        // Fetch each job and store in the $jobs array
        while ($row = $result->fetch_assoc()) {
            $row['created_at'] = date("d/m/Y", strtotime($row['created_at'])); // Format the date
            $jobs[] = $row;
        }

        // Close the database connection
        $conn->close();
    } catch (Exception $e) {
        // Handle the exception if necessary
    }

    return $jobs;
}

// Get all jobs from the database
$allJobs = getAllJobs();

?>
<?php include_once("./components/header.php") ?>

<main class="max-w-3xl px-6 mx-auto">

    <div class="flex justify-between sm:items-center gap-6 py-10 flex-col sm:flex-row">
        <h1 class="text-2xl font-bold">Available jobs</h1>

        <?php if ($user) { ?>
            <div>
                <a href="./applications/create.php"
                    class="bg-blue100 text-white font-medium py-2 px-3 text-sm rounded-md">Create new Job</a>
            </div>
        <?php } ?>
    </div>


    <?php
    if (empty($allJobs)) { ?>
        <p class="text-center text-gray-500/50">No jobs available</p>
    <?php } else { ?>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-10 md:gap-6 md:mt-10">
            <?php
            foreach ($allJobs as $job) { ?>
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
                        <p><strong>Posted by: </strong><span class="capitalize">
                                <?php echo $job['username']; ?>
                            </span></p>
                    </div>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
</main>
<?php include_once("./components/footer.php") ?>