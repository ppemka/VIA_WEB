<?php
// Simple direct connection for project
try {
    $db = new PDO("mysql:host=localhost;dbname=webdev_project", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("set names utf8");
} catch(PDOException $e) {
    echo "Connection Error: " . $e->getMessage();
    die();
}
?>