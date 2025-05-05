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

// Check if service ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: services.php");
    exit;
}

// Get service ID
$service->id = $_GET['id'];

// Read service details
if (!$service->readOne()) {
    $_SESSION['error_message'] = "Service not found.";
    header("location: services.php");
    exit;
}

// Store image name for deletion
$image_to_delete = $service->image;

// Delete service
if ($service->delete()) {
    // Delete image file if exists
    if (!empty($image_to_delete) && file_exists("uploads/" . $image_to_delete)) {
        unlink("uploads/" . $image_to_delete);
    }
    
    $_SESSION['success_message'] = "Service deleted successfully.";
} else {
    $_SESSION['error_message'] = "Unable to delete service.";
}

// Redirect to services page
header("location: services.php");
exit;
?>