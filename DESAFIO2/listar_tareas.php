<?php
include "conexion.php";
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) { header("Location: index.php"); exit(); }

$id  = $_SESSION['id'];
$now = time();

$sql = "SELECT te.id, t.titulo, t.descripcion, t.fecha_limite, t.imagen,
               te.entrega, te.archivo, te.estado_id
        FROM tareas_estudiantes te
        JOIN tareas t ON t.id = te.tarea_id
        WHERE te.estudiante_id = $id
        ORDER BY t.fecha_limite ASC";

$res    = $conexion->query($sql);
$tareas = [];
while ($r = $res->fetch_assoc()) $tareas[] = $r;

if (empty($tareas)) {
    echo "<div class='estado-vacio'><p>No tienes tareas asignadas aún.</p></div>";
    exit();
}

// Clasificar tareas
$pendientes  = [];
$entregadas  = [];
$expiradas   = [];

foreach ($tareas as $t) {
    $tiene_entrega = $t['entrega'] || $t['archivo'];
    $vencida       = strtotime($t['fecha_limite']) < $now;

    if ($tiene_entrega) {
        $entregadas[] = $t;
    } elseif ($vencida) {
        $expiradas[] = $t;
    } else {
        $pendientes[] = $t;
    }
}

$total_pendientes  = count($pendientes) + count($expiradas);
$total_entregadas  = count($entregadas);

// Emitir contadores para JS
echo "<div data-met-pendientes='{$total_pendientes}' data-met-entregadas='{$total_entregadas}' style='display:none'></div>";

// ---- SECCIÓN: PENDIENTES ----
if (!empty($pendientes) || !empty($expiradas)): ?>
<div class="seccion-tareas-header">
  <span class="seccion-label naranja">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    Pendientes (<?= count($pendientes) + count($expiradas) ?>)
  </span>
</div>

<?php foreach ($pendientes as $r):
    $restante = strtotime($r['fecha_limite']) - $now;
    $horas    = max(0, floor($restante / 3600));
?>
<div class="item-tarea pendiente">
  <div style="flex:1;min-width:0">
    <div class="tarea-titulo"><?= htmlspecialchars($r['titulo']) ?></div>
    <?php if ($r['descripcion']): ?>
      <div class="tarea-meta" style="margin-bottom:4px"><?= htmlspecialchars($r['descripcion']) ?></div>
    <?php endif; ?>
    <?php if (!empty($r['imagen'])): ?>
      <?php $ext_img = strtolower(pathinfo($r['imagen'], PATHINFO_EXTENSION)); ?>
      <?php if ($ext_img === 'pdf'): ?>
        <div class="tarea-meta" style="margin-top:4px">
          📄 <a href="uploads/tareas/<?= htmlspecialchars($r['imagen']) ?>" target="_blank" style="color:var(--azul-medio)">Ver PDF adjunto</a>
        </div>
      <?php else: ?>
        <div style="margin-top:8px">
          <img src="uploads/tareas/<?= htmlspecialchars($r['imagen']) ?>" alt="Imagen de tarea"
               style="max-width:100%;max-height:180px;border-radius:8px;border:1px solid var(--gris-borde);object-fit:cover">
        </div>
      <?php endif; ?>
    <?php endif; ?>
    <div class="tarea-meta" style="margin-top:6px">
      ⏱ <?= $horas ?> hora<?= $horas != 1 ? 's' : '' ?> restante<?= $horas != 1 ? 's' : '' ?>
      &nbsp;·&nbsp; Límite: <?= date('d/m/Y H:i', strtotime($r['fecha_limite'])) ?>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
    <span class="badge-estado pendiente">Pendiente</span>
    <button class="btn-accion btn-entregar" onclick="entregar(<?= $r['id'] ?>)">Entregar</button>
  </div>
</div>
<?php endforeach; ?>

<?php foreach ($expiradas as $r): ?>
<div class="item-tarea" style="border-left-color:var(--rojo-danger)">
  <div style="flex:1;min-width:0">
    <div class="tarea-titulo"><?= htmlspecialchars($r['titulo']) ?></div>
    <?php if ($r['descripcion']): ?>
      <div class="tarea-meta" style="margin-bottom:4px"><?= htmlspecialchars($r['descripcion']) ?></div>
    <?php endif; ?>
    <?php if (!empty($r['imagen'])): ?>
      <?php $ext_img = strtolower(pathinfo($r['imagen'], PATHINFO_EXTENSION)); ?>
      <?php if ($ext_img === 'pdf'): ?>
        <div class="tarea-meta" style="margin-top:4px">
          📄 <a href="uploads/tareas/<?= htmlspecialchars($r['imagen']) ?>" target="_blank" style="color:var(--azul-medio)">Ver PDF adjunto</a>
        </div>
      <?php else: ?>
        <div style="margin-top:8px">
          <img src="uploads/tareas/<?= htmlspecialchars($r['imagen']) ?>" alt="Imagen de tarea"
               style="max-width:100%;max-height:180px;border-radius:8px;border:1px solid var(--gris-borde);object-fit:cover;filter:grayscale(30%)">
        </div>
      <?php endif; ?>
    <?php endif; ?>
    <div class="tarea-meta" style="margin-top:6px;color:var(--rojo-danger)">
      ⚠ Fecha vencida — <?= date('d/m/Y H:i', strtotime($r['fecha_limite'])) ?>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
    <span class="badge-estado" style="background:var(--rojo-claro);color:var(--rojo-danger);border:1px solid rgba(163,45,45,0.2)">Expirada</span>
  </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php if (!empty($entregadas)): ?>
<div class="seccion-tareas-header" style="margin-top:<?= (!empty($pendientes) || !empty($expiradas)) ? '18px' : '0' ?>">
  <span class="seccion-label verde">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    Entregadas (<?= count($entregadas) ?>)
  </span>
</div>

<?php foreach ($entregadas as $r):
  $completada = $r['estado_id'] == 2;
?>
<div class="item-tarea <?= $completada ? 'completada' : 'pendiente' ?>">
  <div style="flex:1;min-width:0">
    <div class="tarea-titulo"><?= htmlspecialchars($r['titulo']) ?></div>
    <?php if ($r['entrega']): ?>
      <div class="tarea-meta" style="margin-top:4px">📝 <?= htmlspecialchars($r['entrega']) ?></div>
    <?php endif; ?>
    <?php if ($r['archivo']): ?>
      <div class="tarea-meta" style="margin-top:4px">
        📎 <a href="uploads/<?= htmlspecialchars($r['archivo']) ?>" target="_blank" style="color:var(--azul-medio)">Ver archivo adjunto</a>
      </div>
    <?php endif; ?>
    <div class="tarea-meta" style="margin-top:4px">
      <?= $completada ? '✅ Revisada por el profesor' : '✉ Enviada — esperando revisión' ?>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;flex-wrap:wrap;justify-content:flex-end">
    <span class="badge-estado <?= $completada ? 'completada' : 'entregada' ?>">
      <?= $completada ? 'Completada' : 'Entregada' ?>
    </span>
    <?php if (!$completada): ?>
      <button class="btn-accion btn-editar"  onclick="editarEntrega(<?= $r['id'] ?>, '<?= addslashes(htmlspecialchars($r['entrega'])) ?>')">Editar</button>
      <button class="btn-accion btn-anular"  onclick="anularEntrega(<?= $r['id'] ?>)">Anular</button>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php if (empty($pendientes) && empty($expiradas) && empty($entregadas)): ?>
<div class="estado-vacio"><p>No tienes tareas asignadas aún.</p></div>
<?php endif; ?>