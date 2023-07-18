<?php
include_once("./assets/php/connection.php");
include_once("./assets/php/functions.php");

session_start();

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
    // Redirect to the marketplace or any other page since the user is already logged in
    header("Location: marketplace.php");
    exit();
}


// Function to create a new user account in the database
function createNewUser($fname, $name, $email, $password, $username)
{
    // Check if the email or username already exists
    if (checkIfEmailOrUsernameExists($email, $username)) {
        $_SESSION['error'] = "Email or username already exists.";
        return;
    }

    // Get the database connection
    $conn = conn();

    // Check if the connection was successful
    if (!$conn) {
        // Handle the error or return an error response
        $_SESSION['error'] = "Failed to connect to the database.";
        return;
    }

    try {
        // Create a hashed version of the password (for security)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL query
        $query = "INSERT INTO users (fname, name, email, password, username, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";

        // Prepare the statement
        $stmt = $conn->prepare($query);

        // Bind parameters to the statement
        $stmt->bind_param("sssss", $fname, $name, $email, $hashedPassword, $username);

        // Execute the query to create a new user
        if ($stmt->execute()) {
            $_SESSION['success'] = "New user account created successfully!";
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


// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $fname = $_POST["fname"];
    $name = $_POST["lname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $username = $_POST["username"];

    // Basic form validation (you can add more validation as needed)
    if (empty($fname) || empty($name) || empty($email) || empty($password) || empty($username)) {
        $_SESSION['error'] = "Please fill in all the required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
    } else {
        // Call the function to create the new user account
        createNewUser($fname, $name, $email, $password, $username);
    }
}
?>

<?php include_once("./components/header.php"); ?>

<div class="flex items-start mt-14 justify-center h-full px-6">

    <div>
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

        <form method="POST" action="" class="bg-white shadow-bg px-7 py-12 flex flex-col max-w-lg w-full">
            <h1 class="text-2xl text-center font-bold capitalize">sign up</h1>

            <br />

            <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <label for="fname" class="text-gray1 mb-2 text-sm">firstname</label>
                    <br />
                    <input class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="fname" name="fname"
                        id="fname" placeholder="john">
                </div>
                <div class="w-full">
                    <label for="lname" class="text-gray1 mb-2 text-sm">lastname</label>
                    <br />
                    <input class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="lname" name="lname"
                        id="lname" placeholder="doe">
                </div>
            </div>

            <br />
            <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <label for="username" class="text-gray1 mb-2 text-sm">username</label>
                    <br />
                    <input class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="username"
                        name="username" id="username" placeholder="username">
                </div>
                <div class="w-full">
                    <label for="email" class="text-gray1 mb-2 text-sm">Email</label>
                    <br />
                    <input class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="email" name="email"
                        id="email" placeholder="email@email.com">
                </div>
            </div>
            <br />


            <div>
                <label for="password" class="text-gray1 mb-2 text-sm">Password</label>
                <br />
                <input class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="password" name="password"
                    id="password" placeholder="******">
            </div>

            <br />
            <input type="submit" value="Sign up"
                class="font-medium bg-iris100 rounded-md text-white py-3 cursor-pointer">

            <br />
            <p class="font-medium text-center">already have an account? <a href="/index.php"
                    class="underline text-blue100">Login</a></p>
        </form>
    </div>
</div>

<?php include_once("./components/footer.php") ?>