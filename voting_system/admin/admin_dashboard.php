<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ./admin_login.html");
    exit();
}

include("../api/connect.php");

// Function to prevent SQL Injection
function sanitize($input) {
    global $connect;
    return mysqli_real_escape_string($connect, $input);
}

// Handle approve or remove actions for candidates and groups
if (isset($_GET['approve_candidate']) || isset($_GET['approve_group'])) {
    // Determine whether the action is for a candidate or group
    $id = sanitize($_GET['approve_candidate'] ?? $_GET['approve_group']);
    $role = isset($_GET['approve_candidate']) ? 1 : 2;  // 1 = Candidate, 2 = Group

    // Query to update the status of the user (approve the candidate or group)
    $query = "UPDATE user SET status = 1 WHERE id = '$id' AND role = '$role'";

    if (mysqli_query($connect, $query)) {
        $_SESSION['message'] = ($role == 1) ? 'Candidate approved successfully.' : 'Group approved successfully.';
    } else {
        $_SESSION['error'] = ($role == 1) ? 'Failed to approve candidate.' : 'Failed to approve group.';
    }
    header("Location: ./admin_dashboard.php");
    exit();
}


if (isset($_GET['remove_candidate'])) {
    $id = sanitize($_GET['remove_candidate']);
    $query = "DELETE FROM user WHERE id = '$id' AND role = 1";
    if (mysqli_query($connect, $query)) {
        $_SESSION['message'] = 'Candidate removed successfully.';
    } else {
        $_SESSION['error'] = 'Failed to remove candidate.';
    }
    header("Location: ./admin_dashboard.php");
    exit();
}

if (isset($_GET['remove_group'])) {
    $id = sanitize($_GET['remove_group']);
    $query = "DELETE FROM user WHERE id = '$id' AND role = 2";
    if (mysqli_query($connect, $query)) {
        $_SESSION['message'] = 'Group removed successfully.';
    } else {
        $_SESSION['error'] = 'Failed to remove group.';
    }
    header("Location: ./admin_dashboard.php");
    exit();
}

// Fetch all registered candidates and groups (re-run the queries)
$candidates_query = "SELECT * FROM user WHERE role = 1 AND status = 0";
$candidates_result = mysqli_query($connect, $candidates_query);

$groups_query = "SELECT * FROM user WHERE role = 2 AND status = 0";
$groups_result = mysqli_query($connect, $groups_query);

// Fetch voting results for candidates
$candidates_votes_query = "SELECT * FROM user WHERE role = 1";
$candidates_votes_result = mysqli_query($connect, $candidates_votes_query);

// Fetch voting results for groups
$groups_votes_query = "SELECT * FROM user WHERE role = 2";
$groups_votes_result = mysqli_query($connect, $groups_votes_query);
?>


<?php
// Query to count candidates who have voted and those who have not
$voted_count_query = "SELECT COUNT(*) AS voted_count FROM user WHERE role = 1 AND voted = 1";
$not_voted_count_query = "SELECT COUNT(*) AS not_voted_count FROM user WHERE role = 1 AND voted = 0";

// Execute queries
$voted_result = mysqli_query($connect, $voted_count_query);
$not_voted_result = mysqli_query($connect, $not_voted_count_query);

// Check if the queries executed successfully
if ($voted_result && $not_voted_result) {
    // Fetch the results of the queries
    $voted_count = mysqli_fetch_assoc($voted_result)['voted_count'];
    $not_voted_count = mysqli_fetch_assoc($not_voted_result)['not_voted_count'];
} else {
    // If queries fail, set default values for counts
    $voted_count = 0;
    $not_voted_count = 0;
}
?>





<?php
// Fetch existing voting start and end dates
$query = "SELECT start_date, end_date FROM voting_settings WHERE id = 1"; // Assuming only one entry
$result = mysqli_query($connect, $query);
$row = mysqli_fetch_assoc($result);

$start_date = $row['start_date'] ?? '';
$end_date = $row['end_date'] ?? '';
?>

<?php
// Fetch the current voting start and end dates from the database
$query = "SELECT start_date, end_date FROM voting_settings WHERE id = 1";
$result = mysqli_query($connect, $query);
$row = mysqli_fetch_assoc($result);

$start_date = $row['start_date'];
$end_date = $row['end_date'];

$current_time = date('Y-m-d H:i:s');  // Get the current time


// Determine the voting status
if ($current_time < $start_date) {
    $voting_status = "Voting has not started yet.";
    $status_color = "red";
} elseif ($current_time > $end_date) {
    $voting_status = "Voting has ended.";
    $status_color = "gray";
} else {
    $voting_status = "Voting is currently active.";
    $status_color = "green";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard | Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../assets/logo.png" alt="Voting System Logo" style="width: 100px;">
        </a>
        <div class="d-flex">
        <a href="../routes/logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>

<!-- Admin Dashboard -->
<div class="container py-5">
    <h2 class="text-center">Admin Dashboard</h2>
    <!-- Display Success/Error Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Admin Panel: Set Voting Time/Duration -->
<div class="card my-4">
    <div class="card-header">
        <h5>Set Voting Time/Duration</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="update_voting_duration.php">
            <div class="mb-3">
                <label for="start_date" class="form-label">Voting Start Date</label>
                <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">Voting End Date</label>
                <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<!-- Display Voting Status -->
<div class="alert alert-<?php echo $status_color; ?>">
    <strong>Voting Status:</strong> <?php echo $voting_status; ?>
</div>



    <!-- Manage Candidates -->
    <div class="card my-4">
        <div class="card-header">
            <h5>Manage Candidates</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($candidate = mysqli_fetch_assoc($candidates_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                            <td><?php echo htmlspecialchars($candidate['mobile']); ?></td>
                            <td>
                                <a href="?approve_candidate=<?php echo $candidate['id']; ?>" class="btn btn-success">Approve</a>
                                <a href="?remove_candidate=<?php echo $candidate['id']; ?>" class="btn btn-danger">Remove</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Manage Groups -->
    <div class="card my-4">
        <div class="card-header">
            <h5>Manage Groups</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($group = mysqli_fetch_assoc($groups_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($group['name']); ?></td>
                            <td><?php echo htmlspecialchars($group['mobile']); ?></td>
                            <td>
                                <a href="?approve_group=<?php echo $group['id']; ?>" class="btn btn-success">Approve</a>
                                <a href="?remove_group=<?php echo $group['id']; ?>" class="btn btn-danger">Remove</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<!-- View Results Section -->
<div class="container py-5">
    <h2 class="text-center">Voting Results</h2>
<!-- Results for Candidates -->
<div class="card my-4">
    <div class="card-header">
        <h5>Candidate Voting Results</h5>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b style="color:green;">Voted</b></td>
                    <td><?php echo $voted_count; ?></td>
                </tr>
                <tr>
                    <td><b style="color:red;">Not Voted</b></td>
                    <td><?php echo $not_voted_count; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


    <!-- Results for Groups -->
    <div class="card my-4">
        <div class="card-header">
            <h5>Group Voting Results</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Total Votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($group = mysqli_fetch_assoc($groups_votes_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($group['name']); ?></td>
                            <td><?php echo $group['votes']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Footer Section -->
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <!-- Address Section -->
            <div class="col-md-4 mb-4">
                <h5 class="text-uppercase font-weight-bold">Our Address</h5>
                <p>
                    123 Main Street, Suite 456<br>
                    City, State, ZIP Code<br>
                    Country
                </p>
            </div>

            <!-- Contact Us Section -->
            <div class="col-md-4 mb-4" style="padding-left: 100px ;">
                <h5 class="text-uppercase font-weight-bold">Contact Us</h5>
                <p><strong>Email:</strong> info@yourdomain.com</p>
                <p><strong>Phone:</strong> +1 234 567 890</p>
                <p><strong>Fax:</strong> +1 234 567 891</p>
            </div>

            <!-- Social Media Icons Section -->
            <div class="col-md-4 mb-4" style="padding-left: 200px ;">
                <h5 class="text-uppercase font-weight-bold">Follow Us</h5>
                <div class="social-icons">
                    <div class="icons">
                        <img src="../assets/icons8-instagram-logo-30.png" class="insta"></img>
                        <img src="../assets/icons8-twitterx-30.png" class="twitter"></img>
                        <img src="../assets/icons8-facebook-30.png" class="facebook"></img>
                        <img src="../assets/icons8-youtube-logo-30.png" class="youtube"></img>
                        <img src="../assets/icons8-linkedin-logo-30.png" class="linkedin"></img>
                    </div>
                </div>
            </div>
        </div>
        <!-- All Rights Reserved -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <p>&copy; 2024 Your Company. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</footer>



<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
