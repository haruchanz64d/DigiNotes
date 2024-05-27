<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "diginotes";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User not authenticated"]);
    exit();
}

// Get the user ID from the session
$userId = $_SESSION['user_id'];

switch ($method) {
    case 'GET':
        // Fetch all notes belonging to the authenticated user
        $sql = "SELECT dn_noteId AS id, dn_noteTitle AS title, dn_noteContent AS content FROM dn_notes WHERE dn_uid=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notes = [];
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
        echo json_encode($notes);
        break;

    case 'POST':
        // Add a new note
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'];
        $content = $data['content'];

        // Perform data validation

        $sql = "INSERT INTO dn_notes (dn_noteTitle, dn_noteContent, dn_uid) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $title, $content, $userId);

        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            $response = ["id" => $newId, "title" => $title, "content" => $content, "user_id" => $userId];
            echo json_encode($response);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["error" => "Failed to save note"]);
        }
        break;

    case 'PUT':
        // Update an existing note
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $title = $data['title'];
        $content = $data['content'];

        // Check if the note belongs to the current user
        $check_sql = "SELECT dn_noteId FROM dn_notes WHERE dn_noteId=? AND dn_uid=?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $id, $userId);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        if ($check_result->num_rows === 0) {
            http_response_code(403); // Forbidden
            echo json_encode(["error" => "You don't have permission to update this note"]);
            exit;
        }

        // Perform data validation

        $update_sql = "UPDATE dn_notes SET dn_noteTitle=?, dn_noteContent=? WHERE dn_noteId=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $title, $content, $id);

        if ($update_stmt->execute()) {
            echo json_encode(["id" => $id, "title" => $title, "content" => $content]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["error" => "Failed to update note"]);
        }
        break;

    case 'DELETE':
        // Delete a note
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];

        // Check if the note belongs to the current user
        $check_sql = "SELECT dn_noteId FROM dn_notes WHERE dn_noteId=? AND dn_uid=?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $id, $userId);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        if ($check_result->num_rows === 0) {
            http_response_code(403); // Forbidden
            echo json_encode(["error" => "You don't have permission to delete this note"]);
            exit;
        }

        if ($id == 'all') {
            // Delete all notes
            $delete_sql = "DELETE FROM dn_notes WHERE dn_uid=?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $userId);
        } else {
            // Delete a specific note
            $delete_sql = "DELETE FROM dn_notes WHERE dn_noteId=?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $id);
        }

        if ($delete_stmt->execute()) {
            echo json_encode(["id" => $id]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["error" => "Failed to delete note"]);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();
?>
