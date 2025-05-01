<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db.php';

// Include Service class
require_once 'classes/Service.php';

// Create service object
$service = new Service($db);

// If search is submitted
$search_keyword = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_keyword = htmlspecialchars(trim($_GET['search']));
    $stmt = $service->search($search_keyword);
} else {
    // Get all services
    $stmt = $service->readAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Our Services</h1>
    
    <!-- Search Bar -->
    <div class="row mb-4 justify-content-center">
        <div class="col-md-6">
            <form action="services.php" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control" placeholder="Search services..." value="<?php echo $search_keyword; ?>">
                <button type="submit" class="btn btn-primary ms-2">Search</button>
                <?php if (!empty($search_keyword)): ?>
                    <a href="services.php" class="btn btn-secondary ms-2">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <!-- Services Cards -->
    <div class="row">
        <?php
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
        ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($image) && file_exists("uploads/" . $image)): ?>
                            <img src="uploads/<?php echo $image; ?>" class="card-img-top" alt="<?php echo $title; ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light text-center py-5">
                                <span class="text-muted">No image available</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $title; ?></h5>
                            <p class="card-text"><?php echo $description; ?></p>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
        ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <?php 
                    if (!empty($search_keyword)) {
                        echo "No services found matching '" . $search_keyword . "'.";
                    } else {
                        echo "No services available at the moment.";
                    }
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>