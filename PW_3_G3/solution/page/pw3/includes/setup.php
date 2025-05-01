<?php
// Connect to MySQL without selecting a database
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS webdev_project";
    $pdo->exec($sql);
    echo "Database 'webdev_project' created or already exists.<br>";
    
    // Select the database
    $pdo->exec("USE webdev_project");
    
    // Create services table
    $sql = "CREATE TABLE IF NOT EXISTS services (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        image VARCHAR(255)
    )";
    $pdo->exec($sql);
    echo "Table 'services' created or already exists.<br>";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )";
    $pdo->exec($sql);
    echo "Table 'users' created or already exists.<br>";
    
    // Create messages table for the contact form
    $sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'messages' created or already exists.<br>";
    
    echo "<p>Setup completed successfully!</p>";
    
} catch(PDOException $e) {
    die("ERROR: " . $e->getMessage());
}
?>