<?php
include_once("../assets/php/connection.php");

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

    $conn = conn();
    if (!$conn) {
        $_SESSION['error'] = "Failed to connect to the database. Please try again later.";
        header("Location: /marketplace.php");
        exit();
    }

    try {
        // Prepare the SQL query to fetch the job by its ID
        // $query = "SELECT j.*, u.username AS posted_by
        // FROM jobs j 
        // JOIN users u ON j.owner_id = u.id 
        // WHERE j.id = ?";
        $query = "SELECT j.*, u.username AS posted_by, 
        (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) AS application_count
        FROM jobs j
        JOIN users u ON j.owner_id = u.id
        WHERE j.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $jobId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the job details
        $job = $result->fetch_assoc();

        // Close the statement and database connection
        $stmt->close();
        $conn->close();

        // Check if the job exists
        if (!$job) {
            $_SESSION['error'] = "Job not found.";
            header("Location: /marketplace.php");
            exit();
        }
    } catch (Exception $e) {
        // Handle the exception if necessary
        $_SESSION['error'] = "An error occurred while processing the request. Please try again later.";
        header("Location: /marketplace.php");
        exit();
    }
} else {
    // Redirect back to the page listing all jobs if job ID is not provided
    header("Location: /dashboard.php");
    exit();
}

// Check if the user is trying to apply for the job
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the application form submission
    // Add code to upload and store the user's resume (if needed)

    unset($_SESSION['error']);
    unset($_SESSION['success']);
    $conn = conn();
    if (!$conn) {
        $_SESSION['error'] = "Failed to connect to the database. Please try again later.";
        header("Location: /applications/page.php?id={$jobId}");
        exit();
    }

    try {
        // Check if the user has already applied for the job
        $query1 = "SELECT * FROM applications WHERE job_id = ? AND applicant_id = ?";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("ii", $jobId, $user['id']);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        if ($result1->num_rows > 0) {
            // User has already applied for the job
            $_SESSION['error'] = "You have already applied for this job.";
            header("Location: /applications/page.php?id={$jobId}");
            exit();
        }

        // Close the statement and free the result set
        $stmt1->close();
        $result1->free();

        // Prepare the SQL query to create a new application
        $query = "INSERT INTO applications (job_id, applicant_id, owner_id, created_at)
                 VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $jobId, $user['id'], $job["owner_id"]);
        $stmt->execute();

        // Close the statement and database connection
        $stmt->close();
        $conn->close();

        $_SESSION['success'] = "Your application has been submitted successfully!";
        header("Location: /applications/page.php?id={$jobId}");
        exit();
    } catch (Exception $e) {
        // Handle the exception if necessary
        $_SESSION['error'] = "An error occurred while processing the request. Please try again later.";
        header("Location: /applications/page.php?id={$jobId}");
        exit();
    }
}

?>
<?php include_once("../components/header.php") ?>
<main class="pb-14">
    <div class="h-[20vh] md:h-[40vh] max-h-[500px] overflow-hidden mb-10">
        <img src=<?php echo $job['cover_url']; ?> alt="img" class="h-full w-full object-cover object-top">
    </div>


    <div class="max-w-3xl px-6 mx-auto">
        <?php if (isset($_SESSION['error'])) { ?>
            <div class="notification__wrapper">
                <div class="notification error">
                    <p>
                        <?php echo $_SESSION['error'] ?>
                    </p>
                </div>
            </div>
        <?php }
        unset($_SESSION['error']); ?>
        <?php if (isset($_SESSION['success'])) { ?>
            <div class="notification__wrapper">
                <div class="notification success">
                    <p>
                        <?php echo $_SESSION['success'] ?>
                    </p>
                </div>
            </div>
        <?php }
        unset($_SESSION['success']); ?>
        
        <div class="flex justify-between items-center mb-4">
            <p class="text-3xl capitalize"><strong>
                    <?php echo $job['role']; ?>
                </strong></p>
            <p><strong>
                    <?php echo $job["application_count"] ?>
                </strong> applicants</p>
        </div>
        <div class="flex justify-between items-start mb-10">
            <p><span class="font-bold text-sm">Created at:</span>
                <?php echo date("d/m/Y", strtotime($job['created_at'])); ?>
            </p>
            <?php if ($job["owner_id"] == $user["id"]) { ?>
                <a href="/applications/edit/page.php?id=<?php echo $job["id"] ?>"
                    class="inline-block bg-blue100 text-white font-medium py-2 px-3 text-sm rounded-md">Edit</a>
            <?php } else { ?>
                <p><span class="font-bold text-sm">Posted by:</span>
                    <?php echo $job['posted_by']; ?>
                </p>
            <?php } ?>
        </div>

        <p class="font-bold text-sm">Experience level</p>
        <p class="mb-2 text-sm md:text-base">
            <?php echo $job['exp_level']; ?>
        </p>

        <p class="font-bold text-sm">Description</p>
        <p class="mb-10 text-sm md:text-base">
            <?php echo $job['description']; ?>
        </p>

        <?php if ($job["owner_id"] != $user["id"]) { ?>
            <form method="post" action="">
                <input type="hidden" name="job_id" class="hidden" value="<?php echo $job['id']; ?>">
                <input type="submit" value="Apply now"
                    class="bg-blue100 text-white font-medium py-2 px-3 text-sm rounded-md cursor-pointer" />
            </form>
        <?php } ?>
    </div>

</main>
<?php include_once("../components/footer.php") ?>