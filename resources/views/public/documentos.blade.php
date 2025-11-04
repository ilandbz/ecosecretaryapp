<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Gestor de Documentos</title>

  <!-- Bootstrap & FA -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

  <style>
    body { background:#f5f7fb }
    .header-logo { display:flex; flex-direction:column; align-items:center; justify-content:center; margin:24px 0 12px }
    .header-logo img { width:90px; height:90px; object-fit:contain; margin-bottom:8px }
    .header-logo h1 { font-size:1.8rem; font-weight:700; color:#0d6efd; display:flex; align-items:center; gap:10px }
    .table thead th { text-align:center }
    .table td { vertical-align:middle }
    .cursor-pointer { cursor:pointer }
  </style>
</head>

<body>

  <!-- Navbar con login/logout -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <i class="fa-solid fa-folder-tree"></i> Gestor de Documentos
      </a>
      <div class="ms-auto d-flex align-items-center gap-2">
        <span id="userName" class="text-white-50 small"></span>
        <button id="btnLogin" class="btn btn-outline-light btn-sm">
          <i class="fa-solid fa-right-to-bracket"></i>
          <span class="ms-1">Iniciar sesión</span>
        </button>
        <button id="btnLogout" class="btn btn-outline-light btn-sm d-none">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          <span class="ms-1">Cerrar sesión</span>
        </button>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <!-- Encabezado -->
    <div class="header-logo text-center">
      <img src="/logo.png" alt="Logo gestor">
      <p class="text-muted mb-0">Consulta y descarga documentos públicos</p>
    </div>

    <!-- Login inline (modal simple) -->
    <div id="loginCard" class="card p-3 mb-3 d-none" style="max-width:520px; margin:0 auto">
      <h5 class="mb-2"><i class="fa-solid fa-lock me-1 text-primary"></i> Iniciar sesión</h5>
      <div id="loginAlert" class="alert alert-danger d-none"></div>
      <div class="mb-2">
        <label class="form-label">Email o usuario</label>
        <input id="loginEmail" type="text" class="form-control" autocomplete="username">
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <div class="input-group">
          <input id="loginPass" type="password" class="form-control" autocomplete="current-password">
          <button class="btn btn-outline-secondary" id="togglePass" type="button"><i class="fa-regular fa-eye"></i></button>
        </div>
      </div>
      <div class="d-flex justify-content-end gap-2">
        <button id="btnCancelLogin" class="btn btn-outline-secondary">Cancelar</button>
        <button id="btnDoLogin" class="btn btn-primary">
          <i class="fa-solid fa-right-to-bracket me-1"></i> Entrar
        </button>
      </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive shadow-sm rounded bg-white">
      <table class="table table-bordered align-middle mb-0">
        <thead class="table-dark">
          <tr>
            <th>Título</th>
            <th>Tipo</th>
            <th>Área</th>
            <th>Autor</th>
            <th>Fecha</th>
            <th>Archivos</th>
          </tr>
        </thead>
        <tbody id="tbodyDocs">
          <tr><td colspan="6" class="text-center text-muted py-4">Cargando documentos...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <script>
  // ===================== CONFIG =====================
  const API_BASE = "/api/"; // mismo dominio

  // ===================== SESSION HELPERS =====================
  const getToken = () => localStorage.getItem('api_token');
  const setToken = (t) => localStorage.setItem('api_token', t);
  const clearToken = () => localStorage.removeItem('api_token');
  const setUser  = (u) => localStorage.setItem('api_user', JSON.stringify(u||{}));
  const getUser  = () => { try { return JSON.parse(localStorage.getItem('api_user')||'{}'); } catch{ return {}; } };

  function headers(auth=false) {
    const h = { "Accept":"application/json" };
    if (auth) {
      const t = getToken();
      if (t) h["Authorization"] = "Bearer " + t;
    }
    return h;
  }

  // ===================== UI ELEMENTS =====================
  const userNameEl = document.getElementById('userName');
  const btnLogin   = document.getElementById('btnLogin');
  const btnLogout  = document.getElementById('btnLogout');

  const loginCard  = document.getElementById('loginCard');
  const loginEmail = document.getElementById('loginEmail');
  const loginPass  = document.getElementById('loginPass');
  const loginAlert = document.getElementById('loginAlert');
  const btnDoLogin = document.getElementById('btnDoLogin');
  const btnCancelLogin = document.getElementById('btnCancelLogin');
  const togglePass = document.getElementById('togglePass');

  // ===================== LOGIN FLOW =====================
  function showLogin(show=true){
    loginAlert.classList.add('d-none');
    if (show) {
      loginCard.classList.remove('d-none');
      loginEmail.focus();
    } else {
      loginCard.classList.add('d-none');
    }
  }

  async function refreshUserUI(){
    const token = getToken();
    if (!token) {
      userNameEl.textContent = '';
      btnLogin.classList.remove('d-none');
      btnLogout.classList.add('d-none');
      return;
    }
    try {
      const r = await fetch(API_BASE + 'me', { headers: headers(true) });
      if (!r.ok) throw 0;
      const payload = await r.json();
      const user = payload.user || payload; // soporta {user:{}} o plano
      setUser(user);
      userNameEl.textContent = user?.name ? `Conectado: ${user.name}` : 'Conectado';
      btnLogin.classList.add('d-none');
      btnLogout.classList.remove('d-none');
    } catch {
      // token inválido
      clearToken(); setUser(null);
      userNameEl.textContent = '';
      btnLogin.classList.remove('d-none');
      btnLogout.classList.add('d-none');
    }
  }

  btnLogin.addEventListener('click', ()=> showLogin(true));
  btnCancelLogin.addEventListener('click', ()=> showLogin(false));
  togglePass.addEventListener('click', ()=>{
    loginPass.type = (loginPass.type === 'password') ? 'text' : 'password';
  });

  btnDoLogin.addEventListener('click', async ()=>{
    loginAlert.classList.add('d-none');
    const email = loginEmail.value.trim();
    const password = loginPass.value;
    if (!email || !password) {
      loginAlert.textContent = 'Completa tus credenciales';
      loginAlert.classList.remove('d-none');
      return;
    }
    btnDoLogin.disabled = true;
    btnDoLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Ingresando...';
    try {
      const res = await fetch(API_BASE + 'login', {
        method: 'POST',
        headers: { 'Accept':'application/json', 'Content-Type':'application/json' },
        // Cubrimos APIs que aceptan "email" o "username"
        body: JSON.stringify({ name: email, password })
      });
      if (!res.ok) {
        let msg = 'Credenciales inválidas';
        try { const j = await res.json(); msg = j.message || msg; } catch{}
        throw new Error(msg);
      }
      const data = await res.json();
      const token = data.token || data.access_token; // soporta ambas
      if (!token) throw new Error('El API no devolvió token');
      setToken(token);
      setUser(data.user || null);
      showLogin(false);
      await refreshUserUI();
    } catch (e) {
      loginAlert.textContent = e.message || 'Error inesperado';
      loginAlert.classList.remove('d-none');
    } finally {
      btnDoLogin.disabled = false;
      btnDoLogin.innerHTML = '<i class="fa-solid fa-right-to-bracket me-1"></i> Entrar';
    }
  });

  btnLogout.addEventListener('click', async ()=>{
    try {
      await fetch(API_BASE + 'logout', { method:'POST', headers: headers(true) });
    } catch {}
    clearToken(); setUser(null);
    await refreshUserUI();
  });

  // ===================== DOCS LIST =====================
  const tbody = document.getElementById("tbodyDocs");

  function resolveArchivoUrl(arch) {
    // Si ruta_archivo ya es URL absoluta (guardaste con Storage::url), úsala
    const p = arch?.ruta_archivo || '';
    if (!p) return null;

    if (/^https?:\/\//i.test(p)) return p;             // http(s)://...
    if (p.startsWith('/storage/')) return p;           // ya viene con /storage/...
    if (p.startsWith('storage/'))  return '/' + p;     // storage/...
    if (p.startsWith('documentos/')) return '/storage/' + p; // disco public sin prefijo

    // Como último recurso, intenta endpoint privado si lo tuvieras:
    // return `/api/public/archivos/${arch.id}`;
    return '/' + p.replace(/^\/+/, ''); // fallback relativo
  }

  function fmt(s){ return (s ?? '').toString().trim() || '-'; }

  async function loadDocs(){
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">`+
      `<span class="spinner-border spinner-border-sm me-2"></span>Cargando documentos...</td></tr>`;

    try {
      const r = await fetch(API_BASE + 'public/documentos', { headers: headers(false) });
      if (!r.ok) throw new Error('No se pudo cargar documentos');
      const data = await r.json();

      if (!Array.isArray(data) || !data.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No hay documentos disponibles</td></tr>`;
        return;
      }

      tbody.innerHTML = '';
      for (const doc of data) {
        const archivos = Array.isArray(doc.archivos) ? doc.archivos : [];
        const archivosBtns = archivos.length
          ? archivos.map(a=>{
              const href = resolveArchivoUrl(a);
              const label = a.nro ? `Archivo ${a.nro}` : 'Archivo';
              return href
                ? `<a href="${href}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary me-1">
                     <i class="fa-solid fa-paperclip me-1"></i>${label}
                   </a>`
                : `<span class="badge text-bg-secondary">Sin URL</span>`;
            }).join('')
          : "<span class='text-muted'>—</span>";

        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${fmt(doc.titulo)}</td>
          <td>${fmt(doc?.tipo_documento?.nombre)}</td>
          <td>${fmt(doc?.area?.nombre)}</td>
          <td>${fmt(doc?.user?.name)}</td>
          <td>${fmt(doc?.fecha_documento)}</td>
          <td>${archivosBtns}</td>
        `;
        tbody.appendChild(row);
      }
    } catch (err) {
      console.error(err);
      tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center py-4">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>${err.message || 'Error cargando documentos'}
      </td></tr>`;
    }
  }

  // ===================== INIT =====================
  (async function init(){
    await refreshUserUI();    // muestra estado si hay token
    await loadDocs();         // carga documentos públicos siempre
  })();
  </script>
</body>
</html>
