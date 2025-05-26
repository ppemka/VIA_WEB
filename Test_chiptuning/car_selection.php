<?php
require_once 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Selection - ProTune</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f4f4;
        }
        .container {
            max-width: 800px;
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select, input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            background: #ff6b35;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: #e55a2b;
        }
        .message {
            margin-top: 20px;
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
        <h1>Car Tuning Selection</h1>
        <div id="message" class="message" style="display: none;"></div>
        <form id="carSelectionForm">
            <div class="form-group">
                <label for="make">Car Make</label>
                <select id="make" name="make" required>
                    <option value="">Select Make</option>
                    <option value="BMW">BMW</option>
                    <option value="Audi">Audi</option>
                    <option value="Volkswagen">Volkswagen</option>
                    <option value="Mercedes">Mercedes</option>
                </select>
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <select id="model" name="model" required>
                    <option value="">Select Model</option>
                </select>
            </div>
            <div class="form-group">
                <label for="engine">Engine</label>
                <select id="engine" name="engine" required>
                    <option value="">Select Engine</option>
                </select>
            </div>
            <div class="form-group">
                <label for="year">Year</label>
                <input type="number" id="year" name="year" min="1980" max="2025" required>
            </div>
            <div class="form-group">
                <label for="stage">Tuning Stage</label>
                <select id="stage" name="stage" required>
                    <option value="">Select Stage</option>
                    <option value="Stage 1">Stage 1</option>
                    <option value="Stage 2">Stage 2</option>
                </select>
            </div>
            <button type="submit" class="btn">Submit Selection</button>
        </form>
        <a href="index.html" class="btn" style="margin-top: 20px;">← Back to Home</a>
    </div>

    <script>
        const carData = {
            BMW: {
                '3 Series': ['2.0L Turbo', '3.0L Turbo'],
                '5 Series': ['2.0L Diesel', '4.4L V8']
            },
            Audi: {
                'A3': ['1.8L TFSI', '2.0L TDI'],
                'A4': ['2.0L TFSI', '3.0L V6']
            },
            Volkswagen: {
                'Golf': ['1.4L TSI', '2.0L GTI'],
                'Passat': ['1.8L TSI', '2.0L TDI']
            },
            Mercedes: {
                'C-Class': ['2.0L Turbo', '3.0L V6'],
                'E-Class': ['2.0L Diesel', '4.0L V8']
            }
        };

        const makeSelect = document.getElementById('make');
        const modelSelect = document.getElementById('model');
        const engineSelect = document.getElementById('engine');
        const form = document.getElementById('carSelectionForm');
        const messageDiv = document.getElementById('message');

        makeSelect.addEventListener('change', () => {
            modelSelect.innerHTML = '<option value="">Select Model</option>';
            engineSelect.innerHTML = '<option value="">Select Engine</option>';
            const make = makeSelect.value;
            if (make && carData[make]) {
                for (const model in carData[make]) {
                    const option = document.createElement('option');
                    option.value = model;
                    option.textContent = model;
                    modelSelect.appendChild(option);
                }
            }
        });

        modelSelect.addEventListener('change', () => {
            engineSelect.innerHTML = '<option value="">Select Engine</option>';
            const make = makeSelect.value;
            const model = modelSelect.value;
            if (make && model && carData[make][model]) {
                carData[make][model].forEach(engine => {
                    const option = document.createElement('option');
                    option.value = engine;
                    option.textContent = engine;
                    engineSelect.appendChild(option);
                });
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                make: makeSelect.value,
                model: modelSelect.value,
                engine: engineSelect.value,
                year: document.getElementById('year').value,
                stage: document.getElementById('stage').value
            };

            try {
                const response = await fetch('save_car.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                messageDiv.style.display = 'block';
                messageDiv.className = `message ${result.success ? 'success' : 'error'}`;
                messageDiv.textContent = result.success || result.error;
            } catch (error) {
                messageDiv.style.display = 'block';
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Error submitting selection';
            }
        });
    </script>
</body>
</html>