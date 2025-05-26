// Navigation: Switch visible section
function showSection(sectionName) {
    document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
    const target = document.getElementById(sectionName);
    if (target) target.classList.add('active');
}

// User session management
let currentUser = null;

function updateAuthUI() {
    const authButtons = document.getElementById('authButtons');
    const userButtons = document.getElementById('userButtons');
    const welcomeUser = document.getElementById('welcomeUser');
    
    // Create or select the admin button
    let adminButton = document.getElementById('adminPanelBtn');
    if (!adminButton) {
        adminButton = document.createElement('a');
        adminButton.id = 'adminPanelBtn';
        adminButton.href = 'admin.html';
        adminButton.className = 'btn btn-primary';
        adminButton.textContent = 'Admin Panel';
        userButtons.appendChild(adminButton);
    }

    if (currentUser) {
        authButtons.style.display = 'none';
        userButtons.style.display = 'flex';
        welcomeUser.textContent = `Welcome back, ${currentUser.name}`;
        adminButton.style.display = 'inline-block';
    } else {
        authButtons.style.display = 'flex';
        userButtons.style.display = 'none';
        welcomeUser.textContent = '';
        adminButton.style.display = 'none';
    }
}


async function logout() {
    try {
        const response = await fetch('logout.php', { method: 'POST' });
        const result = await response.json();

        if (result.success) {
            currentUser = null;
            updateAuthUI();
            showSection('home');
        }
    } catch (error) {
        console.error('Logout failed:', error);
    }
}

async function checkSession() {
    try {
        const response = await fetch('check_session.php');
        const result = await response.json();

        if (result.logged_in && result.user) {
            currentUser = result.user;
        }
        updateAuthUI();
    } catch (error) {
        console.error('Session check failed:', error);
    }
}

// Helper: API call wrapper
async function apiCall(endpoint, data) {
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return await response.json();
    } catch (error) {
        console.error('API call error:', error);
        return { error: 'Network error. Please try again.' };
    }
}

// Form: Contact
document.getElementById('contactForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = Object.fromEntries(new FormData(this));

    const errorBox = document.getElementById('contactError');
    const successBox = document.getElementById('contactSuccess');

    if (!formData.name || !formData.email || !formData.message) {
        errorBox.textContent = 'Please fill in all required fields.';
        errorBox.style.display = 'block';
        successBox.style.display = 'none';
        return;
    }

    const result = await apiCall('contact.php', formData);
    if (result.success) {
        successBox.style.display = 'block';
        errorBox.style.display = 'none';
        this.reset();
    } else {
        errorBox.textContent = result.error || 'An error occurred.';
        errorBox.style.display = 'block';
        successBox.style.display = 'none';
    }
});

// Form: Login
document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = Object.fromEntries(new FormData(this));

    const result = await apiCall('login.php', formData);
    const errorBox = document.getElementById('loginError');
    const successBox = document.getElementById('loginSuccess');

    if (result.success) {
        currentUser = result.user;
        successBox.style.display = 'block';
        errorBox.style.display = 'none';
        updateAuthUI();
        this.reset();
        setTimeout(() => showSection('home'), 1500);
    } else {
        errorBox.textContent = result.error || 'Login failed.';
        errorBox.style.display = 'block';
        successBox.style.display = 'none';
    }
});

// Form: Register
document.getElementById('registerForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = Object.fromEntries(new FormData(this));

    const errorBox = document.getElementById('registerError');
    const successBox = document.getElementById('registerSuccess');

    if (!formData.name || !formData.email || !formData.password || formData.password !== formData.confirmPassword) {
        errorBox.textContent = 'Please fill in all fields correctly and ensure passwords match.';
        errorBox.style.display = 'block';
        successBox.style.display = 'none';
        return;
    }

    const result = await apiCall('register.php', formData);
    if (result.success) {
        successBox.style.display = 'block';
        errorBox.style.display = 'none';
        this.reset();
        setTimeout(() => showSection('login'), 2000);
    } else {
        errorBox.textContent = result.error || 'Registration failed.';
        errorBox.style.display = 'block';
        successBox.style.display = 'none';
    }
});

// On page load
document.addEventListener('DOMContentLoaded', () => {
    updateAuthUI();
    checkSession();
});

  fetch('check_session.php')
    .then(response => response.json())
    .then(data => {
      if (data.loggedin) {
        document.getElementById('adminBtn').style.display = 'inline-block';
      }
    });


  