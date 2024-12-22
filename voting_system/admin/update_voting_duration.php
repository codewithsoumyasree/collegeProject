<?php
session_start();

// Ensure the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

include("../api/connect.php"); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to prevent SQL injection
    $start_date = mysqli_real_escape_string($connect, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($connect, $_POST['end_date']);
    
    // Update the voting start and end dates in the database
    $query = "UPDATE voting_settings SET start_date = '$start_date', end_date = '$end_date' WHERE id = 1"; // Assuming only one record exists
    if (mysqli_query($connect, $query)) {
        $_SESSION['message'] = 'Voting dates updated successfully.';
    } else {
        $_SESSION['error'] = 'Failed to update voting dates.';
    }
    header("Location: admin_dashboard.php");
    exit();
}
