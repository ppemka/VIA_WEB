<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
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

// Define variables and initialize with empty values
$title = $description = $image = "";
$title_err = $description_err = $image_err = "";
$success_message = $error_message = "";
$current_image = "";

// Processing data when ID is passed
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Get service ID
    $id = trim($_GET["id"]);
    
    // Get service details
    if ($service->readOne($id)) {
        // Retrieve service data
        $title = $service->title;
        $description = $service->description;
        $current_image = $service->image;
    } else {
        // Service not found
        header("location: dashboard.php");
        exit();
    }
} else {
    // No ID provided
    header("location: dashboard.php");
    exit();
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get ID from form
    $id = $_POST["id"];
    
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
    
    // Handle image upload (if a new image is uploaded)
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $image_err = "Please select a valid file format (JPG, JPEG, PNG, GIF).";
        }
        
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $image_err = "File size is larger than the allowed limit (5MB).";
        }
        
        // Verify MIME type of the file
        if (in_array($filetype, $allowed)) {
            // Check if uploads directory exists, create if not
            if (!file_exists("uploads")) {
                mkdir("uploads", 0777, true);
            }
            
            // Create a unique filename to prevent overwriting
            $new_filename = uniqid() . "." . $ext;
            
            // Move the uploaded file to the uploads directory
            if (empty($image_err) && move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $new_filename)) {
                // If there was an existing image, delete it
                if (!empty($current_image) && file_exists("uploads/" . $current_image)) {
                    unlink("uploads/" . $current_image);
                }
                $image = $new_filename;
            } else {
                $image_err = "Error uploading your file.";
            }
        } else {
            $image_err = "There was a problem with your upload.";
        }
    } else {
        // No new image uploaded, keep the current one
        $image = $current_image;
    }
    
    // Check input errors before updating in database
    if (empty($title_err) && empty($description_err) && empty($image_err)) {
        // Update the service
        if ($service->update($id, $title, $description, $image)) {
            $success_message = "Service updated successfully.";
            $current_image = $image; // Update the current image for display
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
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="form-group mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
                            <span class="invalid-feedback"><?php echo $title_err; ?></span>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>" rows="5"><?php echo $description; ?></textarea>
                            <span class="invalid-feedback"><?php echo $description_err; ?></span>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Current Image</label>
                            <?php if (!empty($current_image) && file_exists("uploads/" . $current_image)): ?>
                                <div class="mb-2">
                                    <img src="uploads/<?php echo $current_image; ?>" alt="Current Image" class="img-thumbnail" width="200">
                                </div>
                            <?php else: ?>
                                <p>No image available</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>New Image (leave empty to keep current image)</label>
                            <input type="file" name="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                            <span class="invalid-feedback"><?php echo $image_err; ?></span>
                        </div>
                        
                        <div class="form-group d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                            <input type="submit" class="btn btn-primary" value="Update">
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