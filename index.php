<?php
include_once("./assets/php/connection.php");

session_start();

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
    // Redirect to the marketplace or any other page since the user is already logged in
    header("Location: marketplace.php");
    exit();
}

// Function to authenticate the user
function authenticateUser($email, $password)
{
    // Get the database connection
    $conn = conn();

    // Check if the connection was successful
    if (!$conn) {
        // Handle the error or return an error response
        $_SESSION['error'] = "Failed to connect to the database.";
        return false;
    }

    try {
        // Prepare the SQL query
        $query = "SELECT id, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user_data = $result->fetch_assoc();
            $hashedPassword = $user_data['password'];

            // Verify the password
            if (password_verify($password, $hashedPassword)) {
                // Password is correct, login successful
                $_SESSION['user'] = $user_data;
                $_SESSION['success'] = "Login successful!";
                return true;
            }
        }

        // Incorrect email or password
        $_SESSION['error'] = "Invalid email or password.";
        return false;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        return false;
    } finally {
        // Close the database connection
        $conn->close();
    }
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Basic form validation (you can add more validation as needed)
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please enter your email and password.";
    } else {
        // Call the function to authenticate the user
        if (authenticateUser($email, $password)) {
            // Redirect to the marketplace or any other page after successful login
            header("Location: marketplace.php");
            exit();
        }
    }
}
?>

<?php include_once("./components/header.php") ?>

<div class="flex items-start mt-14 justify-center h-full px-6">
    <div class="w-full flex items-center flex-col">
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
        
        <form method="POST" action="/" class="bg-white shadow-bg px-7 py-12 flex flex-col max-w-lg w-full">
            <h1 class="text-2xl text-center font-bold capitalize">login</h1>

            <br />

            <div>
                <label for="email" class="text-gray1 mb-2 text-sm">Email</label>
                <br />
                <input class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="email" name="email"
                    id="email" placeholder="email@email.com">
            </div>
            <br />


            <div>
                <label for="password" class="text-gray1 mb-2 text-sm">Password</label>
                <br />
                <input class="w-full border px-3 py-3 rounded-md placeholder:text-gray5" type="password" name="password"
                    id="password" placeholder="******">
            </div>

            <br />
            <input type="submit" value="Login" class="font-medium bg-iris100 rounded-md text-white py-3 cursor-pointer">

            <br />
            <p class="font-medium text-center">don’t have an account? <a href="/signup.php"
                    class="underline text-blue100">Sign up</a></p>
        </form>
    </div>
</div>
<?php include_once("./components/footer.php") ?>