let chipData = [];

// Fetch data from your database using the API
fetch('api.php?action=read')
  .then(res => res.json())
  .then(data => {
    chipData = data;
    populateBrands(data);
  })
  .catch(err => {
    console.error('Failed to load chip data:', err);
    alert('Error loading chip tuning data. Please try again later.');
  });

    // Simulate API call
    setTimeout(() => {
      chipData = sampleData;
      populateBrands(sampleData);
    }, 500);

    function populateBrands(data) {
      const brands = [...new Set(data.map(item => item.brand))];
      const brandSelect = document.getElementById('brandSelect');
      
      brands.forEach(brand => {
        const option = document.createElement('option');
        option.value = brand;
        option.textContent = brand;
        brandSelect.appendChild(option);
      });

      brandSelect.addEventListener('change', () => {
        const selectedBrand = brandSelect.value;
        const models = [...new Set(data.filter(d => d.brand === selectedBrand).map(d => d.model))];

        const modelSelect = document.getElementById('modelSelect');
        modelSelect.innerHTML = '<option value="">Select Model</option>';
        models.forEach(model => {
          const option = document.createElement('option');
          option.value = model;
          option.textContent = model;
          modelSelect.appendChild(option);
        });
        modelSelect.disabled = false;

        // Reset engine select
        const engineSelect = document.getElementById('engineSelect');
        engineSelect.innerHTML = '<option value="">Select Engine</option>';
        engineSelect.disabled = true;
        
        // Clear results
        document.getElementById('resultsBox').innerHTML = '<div class="no-results">Select your vehicle specifications above to see tuning options</div>';
        document.getElementById('resultsBox').classList.remove('has-data');
      });

      document.getElementById('modelSelect').addEventListener('change', () => {
        const selectedBrand = document.getElementById('brandSelect').value;
        const selectedModel = document.getElementById('modelSelect').value;
        const engines = [...new Set(data.filter(d => d.brand === selectedBrand && d.model === selectedModel).map(d => d.engine))];

        const engineSelect = document.getElementById('engineSelect');
        engineSelect.innerHTML = '<option value="">Select Engine</option>';
        engines.forEach(engine => {
          const option = document.createElement('option');
          option.value = engine;
          option.textContent = engine;
          engineSelect.appendChild(option);
        });
        engineSelect.disabled = false;
        
        // Clear results
        document.getElementById('resultsBox').innerHTML = '<div class="no-results">Select your vehicle specifications above to see tuning options</div>';
        document.getElementById('resultsBox').classList.remove('has-data');
      });
    }

    function searchTuning() {
      const brand = document.getElementById('brandSelect').value;
      const model = document.getElementById('modelSelect').value;
      const engine = document.getElementById('engineSelect').value;

      if (!brand || !model || !engine) {
        alert('Please select all vehicle specifications');
        return;
      }

      // Show loading
      const loadingDiv = document.getElementById('loadingDiv');
      const resultsBox = document.getElementById('resultsBox');
      
      loadingDiv.classList.add('show');
      resultsBox.style.display = 'none';

      setTimeout(() => {
        const result = chipData.find(item =>
          item.brand === brand &&
          item.model === model &&
          item.engine === engine
        );

        loadingDiv.classList.remove('show');
        resultsBox.style.display = 'block';

        if (result) {
          resultsBox.classList.add('has-data');
          resultsBox.innerHTML = `
            <div class="result-item">
              <h3>🔧 Original Specifications</h3>
              <div class="specs">
                <div class="spec-value hp-value">HP: ${result.original_hp}</div>
                <div class="spec-value torque-value">Torque: ${result.original_torque} Nm</div>
              </div>
            </div>
            <div class="result-item">
              <h3>⚡ Stage 1 Tuning</h3>
              <div class="specs">
                <div class="spec-value hp-value">HP: ${result.stage1_hp} (+${result.stage1_hp - result.original_hp})</div>
                <div class="spec-value torque-value">Torque: ${result.stage1_torque} Nm (+${result.stage1_torque - result.original_torque})</div>
              </div>
            </div>
            <div class="result-item">
              <h3>🚀 Stage 2 Tuning</h3>
              <div class="specs">
                <div class="spec-value hp-value">HP: ${result.stage2_hp} (+${result.stage2_hp - result.original_hp})</div>
                <div class="spec-value torque-value">Torque: ${result.stage2_torque} Nm (+${result.stage2_torque - result.original_torque})</div>
              </div>
            </div>
          `;
        } else {
          resultsBox.classList.remove('has-data');
          resultsBox.innerHTML = '<div class="no-results">❌ No tuning data found for this vehicle configuration.</div>';
        }
      }, 1000);
    }