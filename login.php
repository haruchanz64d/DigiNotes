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

// Login process
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Verify username and password
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
            // Password is correct, retrieve user ID
            $user_id = $user['dn_uid'];

            // Store user ID in session
            $_SESSION['user_id'] = $user_id;

            // Return user ID to the client
            echo json_encode(['user_id' => $user_id]);
            header('Location: main.php');
            exit;
        } else {
            // Password is incorrect
            echo json_encode(['error' => 'Invalid username or password']);
            exit;
        }
    } else {
        // User does not exist
        echo json_encode(['error' => 'Invalid username or password']);
        exit;
    }
}

// Register process
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Extract registration data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username is already taken
    $sql = "SELECT * FROM dn_users WHERE dn_username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username is already taken
        echo json_encode(['error' => 'Username is already taken']);
        header('Location: index.html');
        exit;
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO dn_users (dn_username, dn_password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            // Registration successful
            echo json_encode(['message' => 'Registration successful']);
            header('Location: index.html');
            exit;
        } else {
            // Registration failed
            echo json_encode(['error' => 'Registration failed']);
            header('Location: index.html');
            exit;
        }
    }
}
?>