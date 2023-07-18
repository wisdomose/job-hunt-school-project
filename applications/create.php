<?php
include_once("../assets/php/connection.php");

session_start();

$user = isset($_SESSION["user"]) ? $_SESSION["user"] : null;

// Check if the user is logged in
if (!$user) {
    // Redirect to the login page since the user is not logged in
    header("Location: /marketplace.php");
    exit();
}

// Function to create a new job
function createNewJob($owner_id, $role, $exp, $description, $coverImg = null, )
{
    // Get the database connection
    $conn = conn();

    // Check if the connection was successful
    if (!$conn) {
        // Handle the error or return an error response
        $_SESSION['error'] = "Failed to connect to the database.";
        return;
    }

    try {
        // Set the default cover image if $coverImg is null
        if (!$coverImg) {
            error_log("coverImg");
            $coverImg = "https://source.unsplash.com/random/800x600";
            error_log($coverImg);
        }
        // Prepare the SQL query
        $query = "INSERT INTO jobs (owner_id, role, exp_level, description, cover_url, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())";


        // Prepare the statement
        $stmt = $conn->prepare($query);

        // Bind parameters to the statement
        $stmt->bind_param("issss", $owner_id, $role, $exp, $description, $coverImg);

        // Execute the query to create a new job
        if ($stmt->execute()) {
            $_SESSION['success'] = "New job created successfully!";
            // Redirect to the job listing page or any other page after job creation
            header("Location: /marketplace.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    } finally {
        // Close the database connection
        $conn->close();
    }
}

// Handle the form submission to create a new job
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the form data
    $role = $_POST["role"];
    $exp = $_POST["exp"];
    $coverImg = $_POST["cover-img"];
    $description = $_POST["description"];

    // Basic form validation (you can add more validation as needed)
    if (empty($role) || empty($exp) || empty($description)) {
        $_SESSION['error'] = "Please fill in all the required fields.";
    } else {
        // Create a new job in the database
        createNewJob($user['id'], $role, $exp, $description, $coverImg);
    }
}
?>

<?php include_once("../components/header.php") ?>


<main class="pb-14">
    <div class="h-[20vh] md:h-[40vh] max-h-[500px] overflow-hidden mb-10">
        <img src="../assets/images/create.png" alt="img" class="h-full w-full object-cover">
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
                        id="role" placeholder="frontend developer">
                </div>
                <div class="w-full">
                    <label for="exp" class="text-gray1 mb-2 text-sm font-medium capitalize">years of experience <span class="required">*</span></label>
                    <br />
                    <input required class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="exp" name="exp"
                        id="exp" placeholder="2 years">
                </div>
                <div class="w-full sm:col-span-2">
                    <label for="cover-img" class="text-gray1 mb-2 text-sm font-medium capitalize">cover image</label>
                    <br />
                    <input class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="text"
                        name="cover-img" id="cover-img" placeholder="doe">
                </div>
                <div class="w-full sm:col-span-2">
                    <label for="description" class="text-gray1 mb-2 text-sm font-medium capitalize">Description <span class="required">*</span></label>
                    <br />
                    <textarea required id="description" name="description"
                        class="w-full border px-3 py-3 rounded-md placeholder:text-gray5"
                        placeholder="enter your job description"></textarea>
                </div>
            </div>
            <br>
            <button class="bg-blue100 text-white font-medium py-2 px-3 text-sm rounded-md">Save</button>
        </form>

    </div>

</main>
<?php include_once("../components/footer.php") ?>