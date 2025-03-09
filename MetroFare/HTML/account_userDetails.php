<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../HTML/user_login.php");
    exit();
}
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "transit_db";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$sql = "SELECT first_name, last_name, date_of_birth, email_mobile FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    $full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
    $dob = new DateTime($user_data['date_of_birth']);
    $formatted_dob = $dob->format('F j, Y');
    $contact_info = $user_data['email_mobile'];
} else {

    session_destroy();
    header("Location: ../HTML/user_login.php");
    exit();
}

$conn->close();
?>