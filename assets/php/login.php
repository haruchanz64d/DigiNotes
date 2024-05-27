<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "diginotes";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Login process
        $username = $_POST['username'];
        $password = $_POST['password'];

        // SQL query to check if the provided username exists
        $sql = "SELECT * FROM dn_users WHERE dn_username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If the user exists, verify the password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['dn_password'])) {
                // Password is correct, set session variables and redirect to main page
                $_SESSION['user_id'] = $user['dn_userId']; // Store user ID in session
                session_create_id(); // Regenerate session ID to prevent session fixation attacks
                $_SESSION['username'] = $username;
                header('Location: .../main.html');
                exit;
            } else {
                // Password is incorrect, display an error message
                $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid username or password. Please try again.'];
            }
        } else {
            // User does not exist, display an error message
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid username or password. Please try again.'];
        }
    } elseif (isset($_POST['register'])) {
        // Registration process
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check if the username already exists
        $sql_check_username = "SELECT * FROM dn_users WHERE dn_username=?";
        $stmt_check = $conn->prepare($sql_check_username);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result_check_username = $stmt_check->get_result();

        if ($result_check_username->num_rows > 0) {
            // Username already exists, display an error message
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Username already exists. Please choose a different username.'];
        } else {
            // Insert the new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_insert_user = "INSERT INTO dn_users (dn_username, dn_password) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert_user);
            $stmt_insert->bind_param("ss", $username, $hashed_password);
        
            if ($stmt_insert->execute()) {
                // Registration successful, display a success message
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Registration successful. You can now login with your credentials.'];
            } else {
                // Error occurred during registration, display an error message
                $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error: ' . $conn->error];
            }
        }
    }

    // Redirect back to the login page after processing the form submission
    header('Location: .../index.html');
    exit;
}
?>
