<?php
// ==================================== 
// database_setup.php - Database Setup Script for XAMPP
// ====================================

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'chiptuning_db';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Database Setup Started...</h2>";

    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database");
    echo "<p>✓ Database '$database' created successfully (or already exists)</p>";

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
        service ENUM('tuning', 'diagnostics', 'repair', 'maintenance') DEFAULT 'tuning',
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT chk_email_format CHECK (email LIKE '%@%.%')
    )";
    $pdo->exec($sql);
    echo "<p>✓ Table 'contact_messages' created successfully</p>";

    // Create chip table
   $sql = "CREATE TABLE IF NOT EXISTS chip (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year INT NOT NULL CHECK (year BETWEEN 1950 AND 2030),
    engine VARCHAR(100) NOT NULL,
    ecu VARCHAR(100) NOT NULL,
    original_hp INT CHECK (original_hp >= 0),
    stage1_hp INT CHECK (stage1_hp >= 0),
    stage2_hp INT CHECK (stage2_hp >= 0),
    original_torque INT CHECK (original_torque >= 0),
    stage1_torque INT CHECK (stage1_torque >= 0),
    stage2_torque INT CHECK (stage2_torque >= 0)
)";

    $pdo->exec($sql);
    echo "<p>✓ Table 'chip' created successfully</p>";

    echo "<h3 style='color: green;'>✓ Database setup completed successfully!</h3>";
    echo "<p><strong>Database Name:</strong> $database</p>";
    echo "<p><strong>Tables Created:</strong></p>";
    echo "<ul>
            <li>users</li>
            <li>contact_messages</li>
            <li>chip</li>
          </ul>";
    echo "<p>You can now check your database in phpMyAdmin at: 
            <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></p>";

} catch(PDOException $e) {
    echo "<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>";
    echo "<ul>
            <li>Check XAMPP is running</li>
            <li>MySQL should be on port 3306</li>
            <li>Verify credentials (username: root, password: empty)</li>
          </ul>";
}

$pdo = null;
?>
