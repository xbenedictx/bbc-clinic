// js/scripts.js
document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const response = await fetch('/api/auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'login', email, password })
  });
  const data = await response.json();
  if (data.success) {
    // Redirect based on role, store token in localStorage
    localStorage.setItem('token', data.token);
    if (data.role === 'admin') window.location = '/admin.html';
    else window.location = '/index.html';
  } else {
    alert(data.message);
  }
});