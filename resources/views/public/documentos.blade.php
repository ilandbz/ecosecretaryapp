<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Documentos Públicos</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <h2 class="mb-4 text-center"><i class="fa-solid fa-folder-open text-primary"></i> Documentos Públicos</h2>

    <div class="table-responsive shadow-sm">
      <table class="table table-bordered align-middle">
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
          <tr><td colspan="6" class="text-center text-muted">Cargando documentos...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const tbody = document.getElementById("tbodyDocs");

    fetch("/api/public/documentos")
      .then(r => r.json())
      .then(data => {
        tbody.innerHTML = "";

        if (!data.length) {
          tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No hay documentos disponibles</td></tr>`;
          return;
        }

        data.forEach(doc => {
          const archivosHTML = (doc.archivos || []).map(a => `
            <a href="/storage/${a.ruta_archivo}" target="_blank" class="btn btn-sm btn-outline-secondary">
              <i class="fa-solid fa-paperclip"></i> Archivo ${a.nro}
            </a>
          `).join(" ") || "<span class='text-muted'>—</span>";

          const fila = `
            <tr>
              <td>${doc.titulo}</td>
              <td>${doc.tipo_documento?.nombre || '-'}</td>
              <td>${doc.area?.nombre || '-'}</td>
              <td>${doc.user?.name || '-'}</td>
              <td>${doc.fecha_documento || '-'}</td>
              <td>${archivosHTML}</td>
            </tr>
          `;
          tbody.insertAdjacentHTML("beforeend", fila);
        });
      })
      .catch(err => {
        console.error(err);
        tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Error cargando documentos</td></tr>`;
      });
  });
  </script>
</body>
</html>
