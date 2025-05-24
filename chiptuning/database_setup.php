<?php
// ==================================== 
// database_setup.php - Database Setup Script for XAMPP
// Run this file in your browser: http://localhost/database_setup.php
// ====================================

// Database configuration
$host = 'localhost';
$username = 'root';  // Default XAMPP MySQL username
$password = '';      // Default XAMPP MySQL password (empty)
$database = 'chiptuning_db';

try {
    // Create connection without selecting a database first
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Setup Started...</h2>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    $pdo->exec($sql);
    echo "<p>✓ Database '$database' created successfully (or already exists)</p>";
    
    // Select the database
    $pdo->exec("USE $database");
    echo "<p>✓ Selected database '$database'</p>";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        phone VARCHAR(20),
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p>✓ Table 'users' created successfully</p>";
    
    // Create contact_messages table
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        vehicle VARCHAR(255),
        service VARCHAR(100),
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p>✓ Table 'contact_messages' created successfully</p>";
    
    echo "<h3 style='color: green;'>✓ Database setup completed successfully!</h3>";
    echo "<p><strong>Database Name:</strong> $database</p>";
    echo "<p><strong>Tables Created:</strong></p>";
    echo "<ul>";
    echo "<li>users (id, name, email, phone, password, created_at)</li>";
    echo "<li>contact_messages (id, name, email, phone, vehicle, service, message, created_at)</li>";
    echo "</ul>";
    echo "<p>You can now check your database in phpMyAdmin at: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></p>";
    
} catch(PDOException $e) {
    echo "<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>";
    echo "<p><strong>Common solutions:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure XAMPP is running (Apache and MySQL services)</li>";
    echo "<li>Check if MySQL is running on port 3306</li>";
    echo "<li>Verify the database credentials (username: root, password: empty by default)</li>";
    echo "</ul>";
}

// Close connection
$pdo = null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h2, h3 {
            color: #333;
        }
        p {
            margin: 10px 0;
        }
        ul {
            margin: 10px 0 10px 20px;
        }
        a {
            color: #007cba;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- PHP output will appear here -->
</body>
</html>