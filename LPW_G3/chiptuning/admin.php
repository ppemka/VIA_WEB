<?php
require_once 'config.php';
session_start();

// Simple admin check (you can enhance this)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - ProTune</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #ff6b35;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #ff6b35;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            background: #ff6b35;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .btn:hover {
            background: #e55a2b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel - Contact Messages</h1>
        <a href="index.html" class="btn">← Back to Website</a>
        <a href="logout.php" class="btn">Logout</a>
        
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($messages) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Vehicle</th><th>Service</th><th>Message</th><th>Date</th></tr>";
                
                foreach ($messages as $msg) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($msg['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($msg['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($msg['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($msg['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($msg['vehicle']) . "</td>";
                    echo "<td>" . htmlspecialchars($msg['service']) . "</td>";
                    echo "<td>" . htmlspecialchars(substr($msg['message'], 0, 100)) . "...</td>";
                    echo "<td>" . $msg['created_at'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No messages found.</p>";
            }
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>
</body>
</html>
