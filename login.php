<?php
header('Content-Type: application/json');

$host = 'localhost';
$db_user = 'root';
$db_pass = ''; 
$db_name = 'triviastorm_db';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed!']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all fields!']);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, fullname, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Password එක පරීක්ෂා කිරීම
        if (password_verify($password, $user['password'])) {
            unset($user['password']); // Safety එකට Password එක response එකෙන් ඉවත් කිරීම
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful!',
                'user' => $user
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found!']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method!']);
}

$conn->close();
?>