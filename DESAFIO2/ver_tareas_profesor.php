<?php
include "conexion.php";
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 1) { header("Location: index.php"); exit(); }

$id  = $_SESSION['id'];
$res = $conexion->query("SELECT * FROM tareas WHERE profesor_id=$id ORDER BY fecha_limite ASC");
$tareas = [];
while ($t = $res->fetch_assoc()) $tareas[] = $t;

if (empty($tareas)) {
    echo "<div class='estado-vacio'><p>Aún no has publicado ninguna tarea.</p></div>";
    exit();
}

foreach ($tareas as $t):
    $sub = $conexion->query("
        SELECT te.id, te.estado_id, te.entrega, te.archivo, te.fecha_entrega_real, u.nombre
        FROM tareas_estudiantes te
        JOIN usuarios u ON u.id = te.estudiante_id
        WHERE te.tarea_id = {$t['id']}
        ORDER BY u.nombre ASC
    ");
    $estudiantes = [];
    while ($e = $sub->fetch_assoc()) $estudiantes[] = $e;

    $total       = count($estudiantes);
    $completadas = count(array_filter($estudiantes, fn($e) => $e['estado_id'] == 2));
    $todas       = $total > 0 && $completadas == $total;
?>
<div class="item-tarea <?= $todas ? 'completada' : 'pendiente' ?>" style="flex-direction:column;gap:10px">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;width:100%">
    <div>
      <div class="tarea-titulo"><?= htmlspecialchars($t['titulo']) ?></div>
      <div class="tarea-meta">
        Límite: <?= date('d/m/Y H:i', strtotime($t['fecha_limite'])) ?>
        &nbsp;·&nbsp; <?= $completadas ?>/<?= $total ?> completadas
      </div>
    </div>
    <span class="badge-estado <?= $todas ? 'completada' : 'pendiente' ?>">
      <?= $todas ? 'Todas completadas' : 'En curso' ?>
    </span>
  </div>

  <?php if (!empty($estudiantes)): ?>
  <div style="width:100%;border-top:1px solid var(--gris-borde);padding-top:10px;display:flex;flex-direction:column;gap:8px">
    <?php foreach ($estudiantes as $e): ?>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:8px 10px;background:var(--blanco);border-radius:var(--radio);border:0.5px solid var(--gris-borde)">
      <div style="flex:1;min-width:0">
        <div style="font-size:13px;font-weight:500;color:#1A1A1A;margin-bottom:4px">
          <?= htmlspecialchars($e['nombre']) ?>
        </div>
        <?php if ($e['entrega'] || $e['archivo']): ?>
          <?php if ($e['fecha_entrega_real']): ?>
            <div style="font-size:12px;color:var(--gris-texto);margin-bottom:4px">
              🕐 <?= date('d/m/Y H:i', strtotime($e['fecha_entrega_real'])) ?>
            </div>
          <?php endif; ?>
          <?php if ($e['entrega']): ?>
            <div style="font-size:13px;color:#1A1A1A;background:var(--gris-fondo);padding:6px 10px;border-radius:6px;border-left:3px solid var(--azul-medio);margin-bottom:4px">
              <?= htmlspecialchars($e['entrega']) ?>
            </div>
          <?php endif; ?>
          <?php if ($e['archivo']): ?>
            <div style="font-size:12px;margin-top:2px">
              📎 <a href="uploads/<?= htmlspecialchars($e['archivo']) ?>" target="_blank" style="color:var(--azul-medio)">
                Ver archivo adjunto
              </a>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div style="font-size:12px;color:var(--gris-texto)">Sin entrega aún</div>
        <?php endif; ?>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
        <?php if ($e['estado_id'] == 2): ?>
          <span class="badge-estado completada">Completada</span>
        <?php elseif ($e['entrega'] || $e['archivo']): ?>
          <span class="badge-estado entregada">Entregada</span>
          <button class="btn-accion btn-completar" onclick="completar(<?= $e['id'] ?>)">Completar</button>
        <?php else: ?>
          <span class="badge-estado pendiente">Pendiente</span>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php endforeach; ?>