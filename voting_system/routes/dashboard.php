<?php
session_start();
if(!isset($_SESSION['userdata'])){
    header("location: ../");
}
$userdata= $_SESSION['userdata'];
if($_SESSION['userdata']['status']==0){
    $status='<b style="color:red;"> NOT VOTED</b>';
}
else{
    $status='<b style="color:green;"> VOTED</b>';
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
    </style>
</head>
<body>

 <!-- Navbar -->
 <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../assets/logo.png" alt="logo" style="width: 100px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <form class="d-flex ms-auto" role="search">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" href="../index.html">Home</a>
                        </li>
                        </li>
                    </ul>
                </div>
            </form>
        </div>
    </nav>

<div class="container">
   <center> <h1 style="padding-top:1rem;">Welcome, <?php echo $_SESSION['userdata']['name']; ?></h1></center>
    <!-- Profile Section -->
    <div id="profileSection" class="card">
        <div class="card-header">
            <h4>Profile details</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="../uploads/<?php echo $_SESSION['userdata']['photo']; ?>" alt="Profile Photo" id="profileImage">
                </div>
                <div class="col-md-9">
                    <p>Name:<?php echo $_SESSION['userdata']['name']; ?></p>
                    <p>Role: <?php echo $_SESSION['userdata']['role'] == 1 ? 'Voter' : 'Group'; ?></p>
                    <p>Mobile: <?php echo $_SESSION['userdata']['mobile']; ?></p>
                    <p>Address: <?php echo $_SESSION['userdata']['address']; ?></p>
                    <p>Status:<?php echo $status; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Section -->
    <center> <h1 style="padding-top:1rem;"> Avaliable Groups: </h1></center>
<div id="Group">
<?php
// Ensure that groupsdata exists in the session
if (isset($_SESSION['groupsdata']) && !empty($_SESSION['groupsdata'])) {

    // Loop through the group data stored in the session
    for ($i = 0; $i < count($_SESSION['groupsdata']); $i++) {
        $group = $_SESSION['groupsdata'][$i];
?>

<div style="display: flex; align-items: center; gap: 20px; padding: 10px;">
    <!-- Display the group image -->
    <img 
        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; object-position: center;" 
        src="../uploads/<?php echo htmlspecialchars($group['photo']); ?>" 
        alt="Group Image">

    <div style="flex-grow: 1;">
        <!-- Display the group name and vote count with proper spacing -->
        <p style="margin: 0; padding: 5px 0;"><b>Group Name: </b><?php echo htmlspecialchars($group['name']); ?></p>
        <p style="margin: 0; padding: 5px 0;"><b>Votes: </b><?php echo htmlspecialchars($group['votes']); ?></p>
    </div>

    <!-- Voting form with proper alignment -->
    <form action="../api/vote.php" method="POST" style="display: flex; align-items: center;">
        <input type="hidden" name="gvotes" value="<?php echo htmlspecialchars($group['votes']); ?>">
        <input type="hidden" name="gid" value="<?php echo htmlspecialchars($group['id']); ?>">
        <input type="submit" name="votebtn" value="Vote" id="votebtn" style="padding: 5px 10px; margin-left: 10px;">
    </form>
</div>


<hr>

<?php

}

}

else{

}

?>

</div>

</div>

        <div id="headerSection">
           <a href="../"> <button id="backbtn">Back</button></a>
            <a href="logout.php"><button id="logoutbtn">Logout</button></a>
          
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



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>