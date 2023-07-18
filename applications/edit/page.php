<?php
include_once("../../assets/php/connection.php");

session_start();
// unset($_SESSION['error']);
// unset($_SESSION['success']);

$user = isset($_SESSION["user"]) ? $_SESSION["user"] : null;

// Check if the user is logged in
if (!$user) {
    // Redirect to the login page since the user is not logged in
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
        $query = "SELECT * FROM jobs WHERE id = ?";
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
    header("Location: /marketplace.php");
    exit();
}

// Check if the user is the creator of the job
if ($job['owner_id'] !== $user['id']) {
    $_SESSION['error'] = "You are not authorized to edit this job.";
    header("Location: /applications/page.php?id={$jobId}");
    exit();
}

// Check if the form is submitted for job update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process the job update form submission

    // Sanitize and validate form data (you can add your validation rules here)

    // Retrieve updated job details from the form
    $updatedRole = $_POST['role'];
    $updatedExp = $_POST['exp'];
    $updatedCoverImg = $_POST['cover-img'];
    $updatedDescription = $_POST['description'];

    // Update the job in the database
    $conn = conn();
    if (!$conn) {
        $_SESSION['error'] = "Failed to connect to the database. Please try again later.";
        header("Location: /edit_job.php?id={$jobId}");
        exit();
    }

    try {
        // Prepare the SQL query to update the job
        $query = "UPDATE jobs 
                  SET role = ?, exp_level = ?, cover_url = ?, description = ?
                  WHERE id = ?";
        $stmt = $conn->prepare($query);

        // Bind the parameters to the statement
        $stmt->bind_param("ssssi", $updatedRole, $updatedExp, $updatedCoverImg, $updatedDescription, $jobId);
        $stmt->execute();

        // Close the statement and database connection
        $stmt->close();
        $conn->close();

        $_SESSION['success'] = "Job details updated successfully!";
        header("Location: /applications/page.php?id={$jobId}");
        exit();
    } catch (Exception $e) {
        // Handle the exception if necessary
        $_SESSION['error'] = "An error occurred while processing the request. Please try again later.";
        header("Location: /applications/edit/page.php?id={$jobId}");
        exit();
    }
}

?>

<?php include_once("../../components/header.php") ?>

<main class="pb-14">
    <div class="h-[20vh] md:h-[40vh] max-h-[500px] overflow-hidden mb-10">
        <img src="../../assets/images/img.jpg" alt="img" class="h-full w-full object-cover">
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
        <form method="POST" action="">
            <div class="grid sm:grid-cols-2 gap-6 w-full">
                <div class="w-full">
                    <label for="role" class="text-gray1 mb-2 text-sm font-medium capitalize">role <span class="required">*</span></label>
                    <br />
                    <input required class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="role" name="role"
                        id="role" placeholder="frontend developer" value="<?php echo $job['role']; ?>">
                </div>
                <div class="w-full">
                    <label for="exp" class="text-gray1 mb-2 text-sm font-medium capitalize">years of experience <span class="required">*</span></label>
                    <br />
                    <input required class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="exp" name="exp"
                        id="exp" placeholder="2 years" value="<?php echo $job['exp_level']; ?>">
                </div>
                <div class="w-full sm:col-span-2">
                    <label for="cover-img" class="text-gray1 mb-2 text-sm font-medium capitalize">cover image</label>
                    <br />
                    <input class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="text"
                        name="cover-img" id="cover-img" placeholder="doe" value="<?php echo $job['cover_url']; ?>">
                </div>
                <div class="w-full sm:col-span-2">
                    <label for="description" class="text-gray1 mb-2 text-sm font-medium capitalize">Description <span class="required">*</span></label>
                    <br />
                    <textarea required id="description" name="description"
                        class="w-full border px-3 py-3 rounded-md placeholder:text-gray5"
                        placeholder="enter your job description"><?php echo $job['description']; ?></textarea>
                </div>
            </div>
            <br>
            <button class="bg-blue100 text-white font-medium py-2 px-3 text-sm rounded-md">Save</button>
        </form>
    </div>

</main>
<?php include_once("../../components/footer.php") ?>