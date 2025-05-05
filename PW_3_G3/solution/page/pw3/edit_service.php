<?php
// Start session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include database connection
require_once 'includes/db.php';

// Include Service class
require_once 'classes/Service.php';

// Create service object
$service = new Service($db);

// Initialize variables
$title = $description = $image = "";
$title_err = $description_err = $image_err = "";
$success_message = $error_message = "";
$current_image = "";

// Check if service ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: services.php");
    exit;
}

// Get service ID
$service->id = $_GET['id'];

// Read service details
if (!$service->readOne()) {
    header("location: services.php");
    exit;
}

// Set current values
$title = $service->title;
$description = $service->description;
$current_image = $service->image;

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }
    
    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }
    
    // Handle image upload
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $image_err = "File is not an image.";
        }
        
        // Check file size (max 5MB)
        else if ($_FILES["image"]["size"] > 5000000) {
            $image_err = "File is too large. Max 5MB.";
        }
        
        // Allow only certain file formats
        else if (!in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
            $image_err = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
        
        // If no errors, try to upload the file
        else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $new_filename;
                
                // Delete old image if exists
                if (!empty($current_image) && file_exists("uploads/" . $current_image)) {
                    unlink("uploads/" . $current_image);
                }
            } else {
                $image_err = "Sorry, there was an error uploading your file.";
            }
        }
    }
    
    // Check input errors before updating in database
    if (empty($title_err) && empty($description_err) && empty($image_err)) {
        
        // Set service properties
        $service->title = $title;
        $service->description = $description;
        if (!empty($image)) {
            $service->image = $image;
        }
        
        // Update service
        if ($service->update()) {
            $success_message = "Service updated successfully.";
            
            // Update current_image for display
            if (!empty($image)) {
                $current_image = $image;
            }
        } else {
            $error_message = "Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Edit Service</h2>
                </div>
                <div class="card-body">
                    <!-- Success/Error messages -->
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Edit Form -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $service->id; ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo $title; ?>">
                            <div class="invalid-feedback"><?php echo $title_err; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="5"><?php echo $description; ?></textarea>
                            <div class="invalid-feedback"><?php echo $description_err; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <?php if (!empty($current_image) && file_exists("uploads/" . $current_image)): ?>
                                <div class="mb-2">
                                    <img src="uploads/<?php echo $current_image; ?>" alt="Current Image" class="img-thumbnail" style="max-height: 200px;">
                                    <p class="text-muted">Current image</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>" id="image" name="image">
                            <div class="invalid-feedback"><?php echo $image_err; ?></div>
                            <div class="form-text">Leave empty to keep current image. Only JPG, JPEG, PNG & GIF files (max 5MB).</div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="services.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>