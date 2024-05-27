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

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT dn_noteId AS id, dn_noteTitle AS title, dn_noteContent AS content FROM dn_notes WHERE dn_userId=?";
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
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'];
        $content = $data['content'];

        // Perform data validation

        $sql = "INSERT INTO dn_notes (dn_noteTitle, dn_noteContent, dn_userId) VALUES (?, ?, ?)";
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
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $title = $data['title'];
        $content = $data['content'];

        // Perform data validation

        $sql = "UPDATE dn_notes SET dn_noteTitle=?, dn_noteContent=? WHERE dn_noteId=? AND dn_userId=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $title, $content, $id, $userId);

        if ($stmt->execute()) {
            echo json_encode(["id" => $id, "title" => $title, "content" => $content, "user_id" => $userId]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["error" => "Failed to update note"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];

        if ($id == 'all') {
            $sql = "DELETE FROM dn_notes WHERE dn_userId=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
        } else {
            $sql = "DELETE FROM dn_notes WHERE dn_noteId=? AND dn_userId=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id, $userId);
        }

        if ($stmt->execute()) {
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
