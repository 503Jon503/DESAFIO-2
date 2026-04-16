<?php
include "conexion.php";
if (!isset($_SESSION['id'])) { header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Académico</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/app.js"></script>
<style>
  :root {
    --azul-institucional: #0B3D6B;
    --azul-medio: #185FA5;
    --azul-claro: #E6F1FB;
    --dorado: #C9A84C;
    --verde-exito: #0F6E56;
    --verde-claro: #E1F5EE;
    --naranja-pendiente: #BA7517;
    --naranja-claro: #FDF3E3;
    --rojo-danger: #A32D2D;
    --rojo-claro: #FCEBEB;
    --gris-borde: #D8D6CE;
    --gris-fondo: #F5F4F0;
    --gris-texto: #6B6A65;
    --blanco: #FFFFFF;
    --sombra-card: 0 2px 12px rgba(11,61,107,0.08);
    --radio: 10px;
    --radio-lg: 14px;
  }

  * { box-sizing: border-box; }

  body {
    font-family: 'DM Sans', sans-serif;
    background-color: var(--gris-fondo);
    color: #1A1A1A;
    min-height: 100vh;
  }

  .navbar-institucional {
    background: var(--azul-institucional);
    padding: 0;
    border-bottom: 3px solid var(--dorado);
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 16px rgba(11,61,107,0.18);
  }

  .navbar-inner {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
  }

  .navbar-brand-bloque {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 0;
  }

  .navbar-escudo {
    width: 42px; height: 42px;
    background: rgba(255,255,255,0.12);
    border: 1.5px solid rgba(201,168,76,0.5);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }

  .navbar-titulo .institucion {
    font-family: 'Playfair Display', serif;
    font-size: 13px;
    color: var(--dorado);
    letter-spacing: 0.8px;
    text-transform: uppercase;
  }

  .navbar-titulo .usuario {
    font-size: 15px;
    font-weight: 500;
    color: #FFFFFF;
  }

  .navbar-acciones {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .badge-rol {
    font-size: 11px;
    font-weight: 500;
    padding: 4px 12px;
    border-radius: 20px;
    letter-spacing: 0.4px;
    text-transform: uppercase;
  }

  .badge-rol.profesor {
    background: rgba(201,168,76,0.2);
    color: var(--dorado);
    border: 1px solid rgba(201,168,76,0.4);
  }

  .badge-rol.estudiante {
    background: rgba(230,241,251,0.15);
    color: #B5D4F4;
    border: 1px solid rgba(181,212,244,0.3);
  }

  .btn-salir {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.25);
    color: rgba(255,255,255,0.85);
    padding: 7px 16px;
    border-radius: var(--radio);
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .btn-salir:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.5);
    color: white;
  }

  .contenido-principal {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
  }

  .grid-metricas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
    margin-bottom: 1.5rem;
  }

  .card-metrica {
    background: var(--blanco);
    border-radius: var(--radio);
    padding: 1rem 1.25rem;
    border: 0.5px solid var(--gris-borde);
    box-shadow: var(--sombra-card);
    border-left: 4px solid transparent;
    transition: transform 0.15s;
  }

  .card-metrica:hover { transform: translateY(-2px); }
  .card-metrica.azul    { border-left-color: var(--azul-medio); }
  .card-metrica.verde   { border-left-color: var(--verde-exito); }
  .card-metrica.naranja { border-left-color: var(--naranja-pendiente); }

  .card-metrica .etiqueta {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gris-texto);
    margin-bottom: 6px;
    font-weight: 500;
  }

  .card-metrica .valor {
    font-size: 28px;
    font-weight: 300;
    line-height: 1;
  }

  .card-metrica.azul .valor    { color: var(--azul-medio); }
  .card-metrica.verde .valor   { color: var(--verde-exito); }
  .card-metrica.naranja .valor { color: var(--naranja-pendiente); }

  .card-panel {
    background: var(--blanco);
    border-radius: var(--radio-lg);
    border: 0.5px solid var(--gris-borde);
    box-shadow: var(--sombra-card);
    margin-bottom: 1.25rem;
    overflow: hidden;
  }

  .card-panel-header {
    padding: 14px 20px;
    border-bottom: 1px solid var(--gris-borde);
    background: #FAFAF8;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .card-panel-header .titulo-panel {
    font-family: 'Playfair Display', serif;
    font-size: 15px;
    color: var(--azul-institucional);
  }

  .card-panel-body { padding: 20px; }

  .form-label-academico {
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gris-texto);
    margin-bottom: 5px;
    display: block;
  }

  .form-control-academico {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    border: 1px solid var(--gris-borde);
    border-radius: var(--radio);
    background: var(--gris-fondo);
    color: #1A1A1A;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    margin-bottom: 14px;
  }

  .form-control-academico:focus {
    border-color: var(--azul-medio);
    box-shadow: 0 0 0 3px rgba(24,95,165,0.1);
    background: var(--blanco);
  }

  textarea.form-control-academico { resize: vertical; min-height: 80px; }

  .btn-crear {
    background: var(--azul-institucional);
    color: white;
    border: none;
    padding: 10px 22px;
    border-radius: var(--radio);
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    display: inline-flex;
    align-items: center;
    gap: 7px;
  }

  .btn-crear:hover  { background: var(--azul-medio); }
  .btn-crear:active { transform: scale(0.98); }

  /* Upload zone para imagen del profesor */
  .upload-zona {
    border: 2px dashed var(--gris-borde);
    border-radius: var(--radio);
    padding: 16px;
    background: var(--gris-fondo);
    cursor: pointer;
    text-align: center;
    transition: border-color 0.2s, background 0.2s;
    margin-bottom: 14px;
    position: relative;
  }
  .upload-zona:hover {
    border-color: var(--azul-medio);
    background: var(--azul-claro);
  }
  .upload-zona input[type=file] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }
  .upload-zona .upload-texto {
    font-size: 13px;
    color: var(--gris-texto);
    pointer-events: none;
  }
  .upload-zona .upload-texto span {
    color: var(--azul-medio);
    font-weight: 500;
  }
  #previewImagen {
    display: none;
    margin-top: 10px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--gris-borde);
    position: relative;
  }
  #previewImagen img {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    display: block;
  }
  #previewImagen .preview-label {
    font-size: 12px;
    color: var(--gris-texto);
    padding: 6px 10px;
    background: var(--gris-fondo);
    display: flex;
    align-items: center;
    gap: 6px;
  }
  #previewImagen .btn-quitar {
    position: absolute;
    top: 6px; right: 6px;
    background: rgba(0,0,0,0.55);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px; height: 24px;
    font-size: 14px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    line-height: 1;
  }

  .item-tarea {
    border-left: 4px solid var(--gris-borde);
    background: var(--gris-fondo);
    border-radius: 0 var(--radio) var(--radio) 0;
    padding: 12px 16px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    transition: border-color 0.2s;
  }

  .item-tarea.pendiente  { border-left-color: var(--naranja-pendiente); }
  .item-tarea.completada { border-left-color: var(--verde-exito); }

  .item-tarea .tarea-titulo {
    font-size: 14px;
    font-weight: 500;
    color: #1A1A1A;
    margin-bottom: 3px;
  }

  .item-tarea .tarea-meta {
    font-size: 12px;
    color: var(--gris-texto);
  }

  .badge-estado {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 500;
    white-space: nowrap;
  }

  .badge-estado.pendiente {
    background: var(--naranja-claro);
    color: var(--naranja-pendiente);
    border: 1px solid rgba(186,117,23,0.2);
  }

  .badge-estado.completada {
    background: var(--verde-claro);
    color: var(--verde-exito);
    border: 1px solid rgba(15,110,86,0.2);
  }

  .badge-estado.entregada {
    background: var(--azul-claro);
    color: var(--azul-medio);
    border: 1px solid rgba(24,95,165,0.2);
  }

  .btn-accion {
    font-size: 12px;
    padding: 5px 13px;
    border-radius: var(--radio);
    border: 1px solid;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    font-weight: 500;
    transition: all 0.15s;
    margin-left: 6px;
  }

  .btn-completar {
    background: var(--verde-claro);
    color: var(--verde-exito);
    border-color: rgba(15,110,86,0.25);
  }

  .btn-completar:hover { background: var(--verde-exito); color: white; }

  .btn-entregar {
    background: var(--azul-claro);
    color: var(--azul-medio);
    border-color: rgba(24,95,165,0.25);
  }

  .btn-entregar:hover { background: var(--azul-medio); color: white; }

  .btn-editar {
    background: #FDF6E3;
    color: #BA7517;
    border-color: rgba(186,117,23,0.25);
  }

  .btn-editar:hover { background: #BA7517; color: white; }

  .btn-anular {
    background: var(--rojo-claro);
    color: var(--rojo-danger);
    border-color: rgba(163,45,45,0.25);
  }

  .btn-anular:hover { background: var(--rojo-danger); color: white; }

  /* Separadores de sección en lista de tareas */
  .seccion-tareas-header {
    margin-bottom: 8px;
  }
  .seccion-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 4px 10px;
    border-radius: 20px;
  }
  .seccion-label.naranja {
    background: var(--naranja-claro);
    color: var(--naranja-pendiente);
    border: 1px solid rgba(186,117,23,0.2);
  }
  .seccion-label.verde {
    background: var(--verde-claro);
    color: var(--verde-exito);
    border: 1px solid rgba(15,110,86,0.2);
  }

  .tabla-resumen {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }

  .tabla-resumen th {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gris-texto);
    font-weight: 500;
    padding: 8px 12px;
    border-bottom: 1.5px solid var(--gris-borde);
    text-align: left;
  }

  .tabla-resumen td {
    padding: 10px 12px;
    border-bottom: 0.5px solid var(--gris-borde);
    color: #1A1A1A;
  }

  .tabla-resumen tr:last-child td { border-bottom: none; }
  .tabla-resumen tr:hover td { background: var(--gris-fondo); }

  .estado-vacio {
    text-align: center;
    padding: 2.5rem 1rem;
    color: var(--gris-texto);
  }

  .estado-vacio p { font-size: 14px; margin: 0; }

  .footer-institucional {
    text-align: center;
    padding: 1.5rem;
    font-size: 12px;
    color: var(--gris-texto);
    border-top: 1px solid var(--gris-borde);
    margin-top: 2rem;
  }

  .footer-institucional span { color: var(--dorado); }

  @media (max-width: 576px) {
    .navbar-titulo .institucion { display: none; }
    .badge-rol { display: none; }
    .contenido-principal { padding: 1rem; }
  }
</style>
</head>
<body>

<nav class="navbar-institucional">
  <div class="navbar-inner">
    <div class="navbar-brand-bloque">
      <div class="navbar-escudo">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C9A84C" stroke-width="1.8">
          <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
          <path d="M6 12v5c3 3 9 3 12 0v-5"/>
        </svg>
      </div>
      <div class="navbar-titulo">
        <div class="institucion">Sistema Académico</div>
        <div class="usuario"><?= htmlspecialchars($_SESSION['nombre']) ?></div>
      </div>
    </div>
    <div class="navbar-acciones">
      <?php if ($_SESSION['rol'] == 1): ?>
        <span class="badge-rol profesor">Profesor</span>
      <?php else: ?>
        <span class="badge-rol estudiante">Estudiante</span>
      <?php endif; ?>
      <a href="logout.php" class="btn-salir">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Salir
      </a>
    </div>
  </div>
</nav>

<div class="contenido-principal">

<?php if ($_SESSION['rol'] == 1): ?>
  <!-- ========== VISTA PROFESOR ========== -->

  <div class="card-panel">
    <div class="card-panel-header">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#185FA5" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="16"/>
        <line x1="8" y1="12" x2="16" y2="12"/>
      </svg>
      <span class="titulo-panel">Crear nueva tarea</span>
    </div>
    <div class="card-panel-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label-academico">Título de la tarea</label>
          <input id="titulo" class="form-control-academico" placeholder="Ej. Evaluación parcial 1…">
        </div>
        <div class="col-md-6">
          <label class="form-label-academico">Fecha límite de entrega</label>
          <input type="datetime-local" id="fecha" class="form-control-academico">
        </div>
        <div class="col-12">
          <label class="form-label-academico">Descripción / instrucciones</label>
          <textarea id="descripcion" class="form-control-academico" placeholder="Describa los objetivos y criterios de evaluación…"></textarea>
        </div>
        <div class="col-12">
          <label class="form-label-academico">Imagen o PDF de apoyo (opcional — máx. 8MB)</label>
          <div class="upload-zona" id="uploadZona">
            <input type="file" id="imagenTarea" accept=".jpg,.jpeg,.png,.pdf">
            <div class="upload-texto">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:6px;color:var(--azul-medio)">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
              </svg>
              <br>
              <span>Haz clic o arrastra</span> un archivo JPG, PNG o PDF
            </div>
          </div>
          <div id="previewImagen">
            <button class="btn-quitar" onclick="quitarImagen()" title="Quitar archivo">✕</button>
            <img id="previewImg" src="" alt="Vista previa">
            <div class="preview-label" id="previewLabel">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <span id="previewNombre"></span>
            </div>
          </div>
        </div>
        <div class="col-12">
          <button onclick="crearTarea()" class="btn-crear">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Publicar tarea
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="card-panel">
    <div class="card-panel-header">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#185FA5" stroke-width="2">
        <path d="M9 11l3 3L22 4"/>
        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
      </svg>
      <span class="titulo-panel">Tareas publicadas</span>
    </div>
    <div class="card-panel-body" id="panelProfesor">
      <div class="estado-vacio"><p>Cargando tareas…</p></div>
    </div>
  </div>

  <div class="card-panel">
    <div class="card-panel-header">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#185FA5" stroke-width="2">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      <span class="titulo-panel">Resumen de estudiantes</span>
    </div>
    <div class="card-panel-body" id="resumen">
      <div class="estado-vacio"><p>Cargando resumen…</p></div>
    </div>
  </div>

<?php else: ?>
  <!-- ========== VISTA ESTUDIANTE ========== -->

  <div class="grid-metricas">
    <div class="card-metrica naranja">
      <div class="etiqueta">Pendientes</div>
      <div class="valor" id="met-pendientes-est">—</div>
    </div>
    <div class="card-metrica verde">
      <div class="etiqueta">Entregadas</div>
      <div class="valor" id="met-completadas-est">—</div>
    </div>
  </div>

  <div class="card-panel">
    <div class="card-panel-header">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#185FA5" stroke-width="2">
        <line x1="8" y1="6" x2="21" y2="6"/>
        <line x1="8" y1="12" x2="21" y2="12"/>
        <line x1="8" y1="18" x2="21" y2="18"/>
        <line x1="3" y1="6" x2="3.01" y2="6"/>
        <line x1="3" y1="12" x2="3.01" y2="12"/>
        <line x1="3" y1="18" x2="3.01" y2="18"/>
      </svg>
      <span class="titulo-panel">Mis tareas</span>
    </div>
    <div class="card-panel-body" id="tareas">
      <div class="estado-vacio"><p>Cargando tus tareas…</p></div>
    </div>
  </div>

<?php endif; ?>

</div>

<footer class="footer-institucional">
  Sistema Académico &mdash; <span>DSS</span> &copy; <?= date('Y') ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Preview de imagen al seleccionar archivo
$('#imagenTarea').on('change', function () {
  var file = this.files[0];
  if (!file) return;

  var ext = file.name.split('.').pop().toLowerCase();
  $('#previewNombre').text(file.name);

  if (ext === 'pdf') {
    $('#previewImg').hide();
    $('#previewLabel').css('padding', '12px 10px');
  } else {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#previewImg').attr('src', e.target.result).show();
    };
    reader.readAsDataURL(file);
    $('#previewLabel').css('padding', '6px 10px');
  }
  $('#previewImagen').show();
});

function quitarImagen() {
  $('#imagenTarea').val('');
  $('#previewImagen').hide();
  $('#previewImg').attr('src', '').hide();
}
</script>
</body>
</html>