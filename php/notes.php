<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "diginotes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT dn_noteId AS id, dn_noteTitle AS title, dn_noteContent AS content FROM dn_notes";
        $result = $conn->query($sql);
        $notes = [];
        while($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
        echo json_encode($notes);
        break;
    
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'];
        $content = $data['content'];
        $sql = "INSERT INTO dn_notes (dn_noteTitle, dn_noteContent) VALUES ('$title', '$content')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["id" => $conn->insert_id, "title" => $title, "content" => $content]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $title = $data['title'];
        $content = $data['content'];
        $sql = "UPDATE dn_notes SET dn_noteTitle='$title', dn_noteContent='$content' WHERE dn_noteId=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["id" => $id, "title" => $title, "content" => $content]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        if ($id == 'all') {
            $sql = "DELETE FROM dn_notes";
        } else {
            $sql = "DELETE FROM dn_notes WHERE dn_noteId=$id";
        }
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["id" => $id]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;
}

$conn->close();
