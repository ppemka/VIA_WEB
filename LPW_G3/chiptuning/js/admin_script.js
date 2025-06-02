const modal = document.getElementById('chipModal');
        const closeBtn = document.querySelector('.close');
        
        // Load data on page load
        document.addEventListener('DOMContentLoaded', loadData);
        
        // Event listeners
        document.getElementById('addBtn').addEventListener('click', () => openModal());
        closeBtn.addEventListener('click', closeModal);
        document.getElementById('chipForm').addEventListener('submit', saveEntry);
        
        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        
        // Load data from API
        async function loadData() {
            try {
                const response = await fetch('api.php?action=read');
                const data = await response.json();
                displayData(data);
            } catch (error) {
                showAlert('Error loading data: ' + error.message, 'error');
            }
        }
        
        // Display data in table
        function displayData(data) {
            const tbody = document.getElementById('chipData');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="13">No entries found</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.map(row => `
                <tr>
                    <td>${row.id}</td>
                    <td>${row.brand}</td>
                    <td>${row.model}</td>
                    <td>${row.year}</td>
                    <td>${row.engine}</td>
                    <td>${row.ecu}</td>
                    <td>${row.original_hp || ''}</td>
                    <td>${row.stage1_hp || ''}</td>
                    <td>${row.stage2_hp || ''}</td>
                    <td>${row.original_torque || ''}</td>
                    <td>${row.stage1_torque || ''}</td>
                    <td>${row.stage2_torque || ''}</td>
                    <td>
                        <button class="btn" onclick="editEntry(${row.id})">Edit</button>
                        <button class="btn btn-danger" onclick="deleteEntry(${row.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
        }
        
        // Open modal
        function openModal(data = null) {
            if (data) {
                document.getElementById('modalTitle').textContent = 'Edit Entry';
                document.getElementById('chipId').value = data.id;
                document.getElementById('brand').value = data.brand;
                document.getElementById('model').value = data.model;
                document.getElementById('year').value = data.year;
                document.getElementById('engine').value = data.engine;
                document.getElementById('ecu').value = data.ecu;
                document.getElementById('originalHp').value = data.original_hp || '';
                document.getElementById('stage1Hp').value = data.stage1_hp || '';
                document.getElementById('stage2Hp').value = data.stage2_hp || '';
                document.getElementById('originalTorque').value = data.original_torque || '';
                document.getElementById('stage1Torque').value = data.stage1_torque || '';
                document.getElementById('stage2Torque').value = data.stage2_torque || '';
            } else {
                document.getElementById('modalTitle').textContent = 'Add Entry';
                document.getElementById('chipForm').reset();
                document.getElementById('chipId').value = '';
            }
            
            modal.style.display = 'block';
            document.getElementById('modalAlert').innerHTML = '';
        }
        
        // Close modal
        function closeModal() {
            modal.style.display = 'none';
        }
        
        // Save entry
        async function saveEntry(e) {
            e.preventDefault();
            
            const chipId = document.getElementById('chipId').value;
            const isEdit = chipId !== '';
            
            const data = {
                brand: document.getElementById('brand').value,
                model: document.getElementById('model').value,
                year: parseInt(document.getElementById('year').value),
                engine: document.getElementById('engine').value,
                ecu: document.getElementById('ecu').value,
                original_hp: parseInt(document.getElementById('originalHp').value) || null,
                stage1_hp: parseInt(document.getElementById('stage1Hp').value) || null,
                stage2_hp: parseInt(document.getElementById('stage2Hp').value) || null,
                original_torque: parseInt(document.getElementById('originalTorque').value) || null,
                stage1_torque: parseInt(document.getElementById('stage1Torque').value) || null,
                stage2_torque: parseInt(document.getElementById('stage2Torque').value) || null
            };
            
            try {
                const url = isEdit ? `api.php?action=update&id=${chipId}` : 'api.php?action=create';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeModal();
                    loadData();
                    showAlert(isEdit ? 'Entry updated successfully' : 'Entry created successfully', 'success');
                } else {
                    showAlert('modalAlert', result.error, 'error');
                }
            } catch (error) {
                showAlert('modalAlert', 'Error saving entry: ' + error.message, 'error');
            }
        }
        
        // Edit entry
        async function editEntry(id) {
            try {
                const response = await fetch('api.php?action=read');
                const data = await response.json();
                const entry = data.find(item => item.id == id);
                
                if (entry) {
                    openModal(entry);
                }
            } catch (error) {
                showAlert('Error loading entry: ' + error.message, 'error');
            }
        }
        
        // Delete entry
        async function deleteEntry(id) {
            if (!confirm('Are you sure you want to delete this entry?')) {
                return;
            }
            
            try {
                const response = await fetch(`api.php?action=delete&id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    loadData();
                    showAlert('Entry deleted successfully', 'success');
                } else {
                    showAlert(result.error, 'error');
                }
            } catch (error) {
                showAlert('Error deleting entry: ' + error.message, 'error');
            }
        }
        
        // Show alert
        function showAlert(message, type, containerId = 'alertContainer') {
            const container = document.getElementById(containerId);
            const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
            
            container.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
            
            setTimeout(() => {
                container.innerHTML = '';
            }, 3000);
        }