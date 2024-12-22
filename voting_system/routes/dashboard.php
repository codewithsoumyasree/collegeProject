<?php
session_start();
if (!isset($_SESSION['userdata'])) {
    header("location: ../");
}

$userdata = $_SESSION['userdata'];

// Check if the user is approved (status = 1)
if ($userdata['status'] == 0) {
    $_SESSION['error'] = 'Your account is not approved by the admin yet.';
    header("Location: ../api/login.php"); // Redirect back to login page if not approved
    exit();
}
 
// Check Voting Status
include("../api/connect.php");  
$query = "SELECT start_date, end_date FROM voting_settings WHERE id = 1"; 
$result = mysqli_query($connect, $query);
$row = mysqli_fetch_assoc($result);

$start_date = $row['start_date'];
$end_date = $row['end_date'];
$current_time = date('Y-m-d H:i:s');  

// Determine the voting status
if ($current_time < $start_date) {
    $voting_status = "Voting has not started yet.";
    $votebtn_disabled = 'disabled'; // Disable button if voting hasn't started
} elseif ($current_time > $end_date) {
    $voting_status = "Voting has ended.";
    $votebtn_disabled = 'disabled'; // Disable button if voting has ended
} else {
    $voting_status = "Voting is currently active.";
    $votebtn_disabled = ''; // Enable button if voting is active
}

// Check if the user has already voted
if ($_SESSION['userdata']['voted'] == 0) {
    $status = '<b style="color:red;"> NOT VOTED</b>';
    // If user hasn't voted, the button should be enabled
    $votebtn_disabled = $votebtn_disabled ? 'disabled' : '';  // Keep button disabled based on voting status
} else {
    $status = '<b style="color:green;"> VOTED</b>';
    // If user has voted, disable the button
    $votebtn_disabled = 'disabled';
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Custom Styles */
        body {
            font-family: 'Arial', sans-serif;
        }

        #headerSection {
            background-color: #343a40;
            color: #fff;
            padding: 15px;
            text-align: center;
        }

        #headerSection h1 {
            font-size: 32px;
            margin: 0;
        }

        #backbtn, #logoutbtn {
            background-color: #28a745;
            border: none;
            padding: 10px 20px;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        #backbtn {
            margin-right: 10px;
        }

        #profileSection, #groupSection {
            padding: 20px;
            margin-top: 20px;
        }

        .card {
            margin-bottom: 20px;
        }

        #profileImage {
             width: 150px;
             height: 150px;
             border-radius: 50%;
             object-fit: cover; /* Ensures the image covers the entire circular area */
             object-position: center; 
        }
        #votebtn:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../assets/logo.png" alt="Voting System Logo" style="width: 100px;">
        </a>
        <div class="d-flex">
        <a href="./logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
   <center> <h1 style="padding-top:1rem;">Welcome, <?php echo $_SESSION['userdata']['name']; ?></h1></center>
    <!-- Profile Section -->
    <div id="profileSection" class="card">
        <div class="card-header">
            <h4>Profile details</h4>
            <p>Voting Status: <?php echo $voting_status; ?></p>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="../uploads/<?php echo $_SESSION['userdata']['photo']; ?>" alt="Profile Photo" id="profileImage">
                </div>
                <div class="col-md-9">
                    <p>Name: <?php echo $_SESSION['userdata']['name']; ?></p>
                    <p>Role: <?php echo $_SESSION['userdata']['role'] == 1 ? 'Voter' : 'Group'; ?></p>
                    <p>Mobile: <?php echo $_SESSION['userdata']['mobile']; ?></p>
                    <p>Address: <?php echo $_SESSION['userdata']['address']; ?></p>
                    <p>Status: <?php echo $status; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Section -->
    <center> <h1 style="padding-top:1rem;">Available Groups: </h1></center>
    <div id="Group">
    <?php
    if (isset($_SESSION['groupsdata']) && !empty($_SESSION['groupsdata'])) {
        foreach ($_SESSION['groupsdata'] as $group) {
    ?>
    <div style="display: flex; align-items: center; gap: 20px; padding: 10px;">
        <img 
            style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; object-position: center;" 
            src="../uploads/<?php echo htmlspecialchars($group['photo']); ?>" 
            alt="Group Image">

        <div style="flex-grow: 1;">
            <p style="margin: 0; padding: 5px 0;"><b>Group Name: </b><?php echo htmlspecialchars($group['name']); ?></p>
            <p style="margin: 0; padding: 5px 0;"><b>Votes: </b><?php echo htmlspecialchars($group['votes']); ?></p>
        </div>

        <!-- Voting form with button disabled if the user has voted -->
        <form action="../api/vote.php" method="POST" style="display: flex; align-items: center;">
            <input type="hidden" name="gvotes" value="<?php echo htmlspecialchars($group['votes']); ?>">
            <input type="hidden" name="gid" value="<?php echo htmlspecialchars($group['id']); ?>">
            <input type="submit" name="votebtn" value="Vote" id="votebtn" style="padding: 5px 10px; margin-left: 10px;" <?php echo $votebtn_disabled; ?>>
        </form>
    </div>
    <hr>
    <?php
        }
    }
    ?>
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
            <div class="col-md-4 mb-4" style="padding-left: 100px;">
                <h5 class="text-uppercase font-weight-bold">Contact Us</h5>
                <p><strong>Email:</strong> info@yourdomain.com</p>
                <p><strong>Phone:</strong> +1 234 567 890</p>
                <p><strong>Fax:</strong> +1 234 567 891</p>
            </div>

            <!-- Social Media Icons Section -->
            <div class="col-md-4 mb-4" style="padding-left: 200px;">
                <h5 class="text-uppercase font-weight-bold">Follow Us</h5>
                <div class="social-icons">
                    <div class="icons">
                        <img src="../assets/icons8-instagram-logo-30.png" class="insta">
                        <img src="../assets/icons8-twitterx-30.png" class="twitter">
                        <img src="../assets/icons8-facebook-30.png" class="facebook">
                        <img src="../assets/icons8-youtube-logo-30.png" class="youtube">
                        <img src="../assets/icons8-linkedin-logo-30.png" class="linkedin">
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
