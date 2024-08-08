<?php
// update_status.php

session_start(); // Start the session to access session variables

if (isset($_POST['ticketId'], $_POST['status'])) {
    // Database credentials
    $servername = "localhost";
    $username = "root";
    $password = "ng!nx@1234";
    $dbname = "ticket_system";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize inputs
    $ticketId = $conn->real_escape_string($_POST['ticketId']);
    $status = $conn->real_escape_string($_POST['status']);

    // Update query
    $sql = "UPDATE ticket_quries SET Status='$status'";

    // Check if status is 'Completed' and update attended_by with logged-in admin's name
    if ($status == 'Completed' OR $status =='Processing' && isset($_SESSION['username'])) {
        $username = $conn->real_escape_string($_SESSION['username']);

        // Query to get the admin's name
        $adminQuery = "SELECT name FROM admins WHERE username='$username'";
        $adminResult = $conn->query($adminQuery);

        if ($adminResult->num_rows > 0) {
            $adminRow = $adminResult->fetch_assoc();
            $adminName = $conn->real_escape_string($adminRow['name']);
            $sql .= ", attended_by='$adminName'";
        } else {
            echo "Error: Admin not found";
            $conn->close();
            exit();
        }
    }

    // Check if status is 'unattended' and reset attended_by to default value
    // if ($status == 'unattended') {
    //     $sql .= ", attended_by=''"; // Or use NULL if your database allows it
    // }

    $sql .= " WHERE id='$ticketId'";

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid input";
}
?>
