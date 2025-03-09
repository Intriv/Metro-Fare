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

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $username = $_GET['username'];
    $password = $_GET['password'];

    $_SESSION['username'] = $username;
    
    if (empty($username) || empty($password)) {
        echo "Username and password are required";
        exit();
    }

    $query = "SELECT id, username, password FROM users WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $stored_password = $user['password'];

        if ($password === $stored_password) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['id'] = $user['id'];
            echo "<p>Login successful! Redirecting...</p>";
            echo "<script>setTimeout(function(){ window.location = '../HTML/home_loggedIn.html'; }, 2000);</script>";
        } else {
            if (function_exists('password_verify') && password_verify($password, $stored_password)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['id'] = $user['id'];
                echo "<p>Login successful! Redirecting...</p>";
                echo "<script>setTimeout(function(){ window.location = '../HTML/home_loggedIn.html'; }, 2000);</script>";
            } else {
                echo "<p>Login failed. Please check your username and password.</p>";
            }
        }
    } else {
        echo "User not found";
    }
}

$conn->close();
?>

<p><a href="index.html">Return to login page</a></p>