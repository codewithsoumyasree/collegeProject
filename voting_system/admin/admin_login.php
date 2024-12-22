<?php
include("../api/connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if the admin exists in the database
    $query = "SELECT * FROM admins WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($connect, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Admin exists, start session and redirect to dashboard
        session_start();
        $_SESSION['admin'] = $username;
        echo '<script> window.location= "./admin_dashboard.php";</script>';
    } else {
        echo '<script>alert("Invalid username or password!"); window.location= "./admin_login.html";</script>';
    }
}
?>
