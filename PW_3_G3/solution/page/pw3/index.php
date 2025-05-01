<?php
session_start();

// Check if the user is logged in
$welcomeMessage = '';
if (isset($_SESSION['username'])) {
    $username = htmlspecialchars($_SESSION['username']);
    
    // Check for last visit cookie
    if (isset($_COOKIE['last_visit'])) {
        $lastVisitTimestamp = (int)$_COOKIE['last_visit'];
        $lastVisit = date("F j, Y, g:i a", $lastVisitTimestamp);
        $welcomeMessage = "Welcome back, $username! Last visit: $lastVisit";
    } else {
        $welcomeMessage = "Welcome, $username!";
    }

    // Update the cookie with current time
    setcookie('last_visit', time(), time() + (86400 * 30)); // this stores an int
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h2>Welcome to WebDev Project</h2>
                </div>
                <div class="card-body text-center">
                    <?php if ($welcomeMessage): ?>
                        <div class="alert alert-success">
                            <?php echo $welcomeMessage; ?>
                        </div>
                        <a href="dashboard.php" class="btn btn-primary m-2">Go to Dashboard</a>
                    <?php else: ?>
                        <p class="lead">Please <a href="login.php">log in</a> or <a href="register.php">register</a> to get started.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
