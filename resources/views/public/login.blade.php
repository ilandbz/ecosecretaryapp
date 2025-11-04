<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acceder | EcoSecretary</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <style>
    body { background:#f5f7fb }
    .card { border:none; box-shadow:0 6px 20px rgba(0,0,0,.06) }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card p-4">
        <div class="text-center mb-3">
          <i class="fa-solid fa-lock fa-2x text-primary mb-2"></i>
          <h4 class="mb-0">Iniciar sesión</h4>
          <small class="text-muted">Usa tu cuenta del API</small>
        </div>

        <div id="alert" class="alert alert-danger d-none"></div>

        <form id="frmLogin" autocomplete="on">
          <div class="mb-3">
            <label class="form-label">Email o usuario</label>
            <input id="email" type="text" class="form-control" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
              <input id="password" type="password" class="form-control" required>
              <button type="button" class="btn btn-outline-secondary" id="togglePass">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>
          <div class="d-grid">
            <button id="btnLogin" class="btn btn-primary">
              <i class="fa-solid fa-right-to-bracket me-1"></i> Entrar
            </button>
          </div>
        </form>

        <div class="text-center mt-3">
          <a href="/documentos-publicos" class="small">Ver documentos públicos sin iniciar sesión</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const API_BASE = "/api/"; // mismo host
const alertBox = document.getElementById('alert');

document.getElementById('togglePass').addEventListener('click', () => {
  const inp = document.getElementById('password');
  inp.type = (inp.type === 'password') ? 'text' : 'password';
});

document.getElementById('frmLogin').addEventListener('submit', async (e) => {
  e.preventDefault();
  alertBox.classList.add('d-none');
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;

  const btn = document.getElementById('btnLogin');
  btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Ingresando...';

  try {
    const res = await fetch(API_BASE + 'login', {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, username: email, password }) // si tu API usa "email" o "username", lo cubrimos
    });

    if (!res.ok) {
      let msg = 'Credenciales inválidas';
      try { const j = await res.json(); msg = j.message || msg; } catch(_){}
      throw new Error(msg);
    }

    const data = await res.json();
    // soporta "token" o "access_token"
    const token = data.token || data.access_token;
    if (!token) throw new Error('El API no devolvió token');

    // Guarda token + usuario
    localStorage.setItem('api_token', token);
    if (data.user) localStorage.setItem('api_user', JSON.stringify(data.user));

    // Redirige
    location.href = '/documentos-publicos';
  } catch (err) {
    alertBox.textContent = err.message || 'Error inesperado';
    alertBox.classList.remove('d-none');
  } finally {
    btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-right-to-bracket me-1"></i> Entrar';
  }
});
</script>
</body>
</html>
