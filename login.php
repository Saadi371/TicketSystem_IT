<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "ng!nx@1234";
    $dbname = "ticket_system";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    if ($stmt->execute() === false) {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind the result variables
        $stmt->bind_result($id, $dbUsername, $dbPassword);
        $stmt->fetch();

        // Verify the plaintext password
        if ($password === $dbPassword) {
            // Password is correct
            $_SESSION['user_id'] = $id; // Store the user ID in session
            $_SESSION['username'] = $username; // Optionally store the username
            echo 'success';
        } else {
            // Invalid password
            echo 'Invalid username or password.';
        }
    } else {
        // Invalid username
        echo 'Invalid username or password.';
    }

    $stmt->close();
    $conn->close();
}
?>
