<?php

session_start();

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "transit_db";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $first_name = isset($_GET['first_name']) ? $_GET['first_name'] : '';
    $last_name = isset($_GET['last_name']) ? $_GET['last_name'] : '';
    $day = isset($_GET['day']) ? $_GET['day'] : '';
    $month = isset($_GET['month']) ? $_GET['month'] : '';
    $year = isset($_GET['year']) ? $_GET['year'] : '';
    $emailNumber_info = isset($_GET['emailNumber_info']) ? $_GET['emailNumber_info'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : '';

    if (
        empty($first_name) || empty($last_name) || empty($day) || empty($month) ||
        empty($year) || empty($emailNumber_info) || empty($password)
    ) {
        echo "All fields are required!";
        exit();
    }

    $date_of_birth = "$year-$month-$day";   

    $check_query = "SELECT * FROM users WHERE email_mobile = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $emailNumber_info);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email or mobile number already registered!";
        $check_stmt->close();
        exit();
    }
    $check_stmt->close();

    $username = $emailNumber_info;

    $username = preg_replace("/[^a-zA-Z0-9]/", "", $username);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $insert_query = "INSERT INTO users (username, password, first_name, last_name, date_of_birth, email_mobile, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssss", $username, $hashed_password, $first_name, $last_name, $date_of_birth, $emailNumber_info);

    if ($stmt->execute()) {

        $_SESSION['registration_success'] = true;
        $_SESSION['new_username'] = $username;

        header("Location: ../index.html");
        exit();
    } else {

        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        .error-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .error-message {
            color: #d9534f;
            font-weight: bold;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            background: #5cb85c;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <h2>Registration Error</h2>
        <p class="error-message">There was a problem with your registration.</p>
        <p>Please try again, making sure all required fields are filled correctly.</p>
        <a href="../HTML/signUp.html" class="button">Back to Registration</a>
    </div>
</body>

</html>