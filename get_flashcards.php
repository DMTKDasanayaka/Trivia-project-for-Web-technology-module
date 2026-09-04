<?php
// CORS Header to allow fetch
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$user = "root";
$pass = ""; // XAMPP / WAMP Default Password
$db   = "triviastorm_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$sql = "SELECT question, answer FROM flashcards ORDER BY id ASC";
$result = $conn->query($sql);

$flashcards = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $flashcards[] = $row;
    }
}

echo json_encode($flashcards);
$conn->close();
?>