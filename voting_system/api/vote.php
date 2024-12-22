<?php

session_start();
include('connect.php');

// Get POST data
$votes = $_POST['gvotes'];
$total_votes = $votes + 1;
$gid = $_POST['gid'];
$uid = $_SESSION['userdata']['id'];

// Update votes for the group
$update_votes = mysqli_query($connect, "UPDATE user SET votes='$total_votes' WHERE id='$gid'");

// Update the status of the current user
$update_user_status = mysqli_query($connect, "UPDATE user SET status=1 WHERE id='$uid'");

// Mark the current user as having voted
$update_user_voted = mysqli_query($connect, "UPDATE user SET voted=1 WHERE id='$uid'");

// Check if all queries succeeded
if ($update_votes && $update_user_status && $update_user_voted) {
    // Update session to reflect the vote status
    $_SESSION['userdata']['voted'] = 1;  // Set session voted to 1, meaning user has voted

    // Fetch updated group data
    $groups = mysqli_query($connect, "SELECT * FROM user WHERE role=2");
    $groupsdata = mysqli_fetch_all($groups, MYSQLI_ASSOC);

    // Save the updated groups data in session
    $_SESSION['groupsdata'] = $groupsdata;

    // Update session status
    $_SESSION['userdata']['status'] = 1;

    // Show success message and redirect
    echo '<script>
        alert("Voting Successful!");
        window.location="../routes/dashboard.php";
    </script>';
} else {
    // If there was any error, show the error message
    echo '<script>
        alert("Some error occurred!");
        window.location="../routes/dashboard.php";
    </script>';
}

?>
