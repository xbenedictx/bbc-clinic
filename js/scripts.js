async function register() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const response = await fetch('/api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'register', email, password })
    });
    const data = await response.json();
    if (data.success) {
        alert('Registration successful! Please log in.');
        window.location.href = '/login.html';
    } else {
        alert(data.message);
    }
}

async function login() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const response = await fetch('/api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'login', email, password })
    });
    const data = await response.json();
    if (data.success) {
        localStorage.setItem('token', data.token);
        window.location.href = '/index.html';
    } else {
        alert(data.message);
    }
}