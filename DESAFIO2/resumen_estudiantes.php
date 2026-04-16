<?php
include "conexion.php";
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 1) { header("Location: index.php"); exit(); }

$res = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol_id=2 ORDER BY nombre ASC");
$estudiantes = [];
while ($u = $res->fetch_assoc()) $estudiantes[] = $u;

if (empty($estudiantes)) {
    echo "<div class='estado-vacio'><p>No hay estudiantes registrados.</p></div>";
    exit();
}
?>
<table class="tabla-resumen">
  <thead>
    <tr>
      <th>Estudiante</th>
      <th>Carnet</th>
      <th>Completadas</th>
      <th>Entregadas</th>
      <th>Pendientes</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($estudiantes as $u):
    $uid = $u['id'];

    $comp = $conexion->query("SELECT COUNT(*) c FROM tareas_estudiantes WHERE estudiante_id=$uid AND estado_id=2")->fetch_assoc()['c'];

    $entr = $conexion->query("SELECT COUNT(*) c FROM tareas_estudiantes WHERE estudiante_id=$uid AND estado_id=1 AND (entrega IS NOT NULL OR archivo IS NOT NULL)")->fetch_assoc()['c'];

    $pend = $conexion->query("SELECT COUNT(*) c FROM tareas_estudiantes WHERE estudiante_id=$uid AND estado_id=1 AND entrega IS NULL AND archivo IS NULL")->fetch_assoc()['c'];

    $carnet_row = $conexion->query("SELECT carnet FROM usuarios WHERE id=$uid")->fetch_assoc();
  ?>
  <tr>
    <td style="font-weight:500"><?= htmlspecialchars($u['nombre']) ?></td>
    <td style="color:var(--gris-texto)"><?= htmlspecialchars($carnet_row['carnet']) ?></td>
    <td><span class="badge-estado completada"><?= $comp ?></span></td>
    <td><span class="badge-estado entregada"><?= $entr ?></span></td>
    <td><span class="badge-estado pendiente"><?= $pend ?></span></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>