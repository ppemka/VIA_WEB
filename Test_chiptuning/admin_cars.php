<?php
require_once 'config.php';
session_start();

// Admin check
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
    <title>Admin Panel - Cars - ProTune</title>
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
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
        }
        .btn:hover {
            background: #e55a2b;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input, select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel - Manage Cars</h1>
        <a href="index.html" class="btn">← Back to Website</a>
        <a href="admin.php" class="btn">View Contact Messages</a>
        <a href="logout.php" class="btn">Logout</a>

        <h2>Add/Edit Car</h2>
        <div id="message" class="message" style="display: none;"></div>
        <form id="carForm">
            <input type="hidden" id="carId" name="id">
            <div class="form-group">
                <label for="make">Make</label>
                <input type="text" id="make" name="make" required>
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" required>
            </div>
            <div class="form-group">
                <label for="engine">Engine</label>
                <input type="text" id="engine" name="engine" required>
            </div>
            <div class="form-group">
                <label for="year">Year</label>
                <input type="number" id="year" name="year" min="1980" max="2025" required>
            </div>
            <div class="form-group">
                <label for="stage">Stage</label>
                <select id="stage" name="stage" required>
                    <option value="Stage 1">Stage 1</option>
                    <option value="Stage 2">Stage 2</option>
                </select>
            </div>
            <div class="form-group">
                <label for="horsepower">Horsepower (Optional)</label>
                <input type="number" id="horsepower" name="horsepower">
            </div>
            <div class="form-group">
                <label for="torque">Torque (Optional)</label>
                <input type="number" id="torque" name="torque">
            </div>
            <button type="submit" class="btn">Save Car</button>
        </form>

        <h2>Car List</h2>
        <?php
        try {
            $stmt = $conn->query("SELECT * FROM cars ORDER BY created_at DESC");
            $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($cars) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Make</th><th>Model</th><th>Engine</th><th>Year</th><th>Stage</th><th>Horsepower</th><th>Torque</th><th>Actions</th></tr>";
                foreach ($cars as $car) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($car['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($car['make']) . "</td>";
                    echo "<td>" . htmlspecialchars($car['model']) . "</td>";
                    echo "<td>" . htmlspecialchars($car['engine']) . "</td>";
                    echo "<td>" . htmlspecialchars($car['year']) . "</td>";
                    echo "<td>" . htmlspecialchars($car['stage']) . "</td>";
                    echo "<td>" . htmlspecialchars($car['horsepower'] ?: 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($car['torque'] ?: 'N/A') . "</td>";
                    echo "<td>";
                    echo "<a href='#' class='btn edit-btn' data-id='{$car['id']}'>Edit</a>";
                    echo "<a href='#' class='btn delete-btn' data-id='{$car['id']}'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No cars found.</p>";
            }
        } catch(PDOException $e) {
            echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <script>
        const form = document.getElementById('carForm');
        const messageDiv = document.getElementById('message');

        // Handle form submission (Add/Edit)
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                id: document.getElementById('carId').value,
                make: document.getElementById('make').value,
                model: document.getElementById('model').value,
                engine: document.getElementById('engine').value,
                year: document.getElementById('year').value,
                stage: document.getElementById('stage').value,
                horsepower: document.getElementById('horsepower').value || null,
                torque: document.getElementById('torque').value || null
            };

            try {
                const response = await fetch('manage_car.php', {
                    method: 'POST',
                    headers: { 'Content-Type: 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                messageDiv.style.display = 'block';
                messageDiv.className = `message ${result.success ? 'success' : 'error'}`;
                messageDiv.textContent = result.success || result.error;
                if (result.success) {
                    setTimeout(() => location.reload(), 1000);
                }
            } catch (error) {
                messageDiv.style.display = 'block';
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Error saving car';
            }
        });

        // Handle Edit
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = e.target.dataset.id;
                try {
                    const response = await fetch(`manage_car.php?id=${id}`);
                    const car = await response.json();
                    if (car.success) {
                        document.getElementById('carId').value = car.data.id;
                        document.getElementById('make').value = car.data.make;
                        document.getElementById('model').value = car.data.model;
                        document.getElementById('engine').value = car.data.engine;
                        document.getElementById('year').value = car.data.year;
                        document.getElementById('stage').value = car.data.stage;
                        document.getElementById('horsepower').value = car.data.horsepower || '';
                        document.getElementById('torque').value = car.data.torque || '';
                    }
                } catch (error) {
                    messageDiv.style.display = 'block';
                    messageDiv.className = 'message error';
                    messageDiv.textContent = 'Error loading car data';
                }
            });
        });

        // Handle Delete
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this car?')) {
                    const id = e.target.dataset.id;
                    try {
                        const response = await fetch('manage_car.php', {
                            method: 'DELETE',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id })
                        });
                        const result = await response.json();
                        messageDiv.style.display = 'block';
                        messageDiv.className = `message ${result.success ? 'success' : 'error'}`;
                        messageDiv.textContent = result.success || result.error;
                        if (result.success) {
                            setTimeout(() => location.reload(), 1000);
                        }
                    } catch (error) {
                        messageDiv.style.display = 'block';
                        messageDiv.className = 'message error';
                        messageDiv.textContent = 'Error deleting car';
                    }
                }
            });
        });
    </script>
</body>
</html>