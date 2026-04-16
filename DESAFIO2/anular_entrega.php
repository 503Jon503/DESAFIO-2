<?php
include "conexion.php";
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) { header("Location: index.php"); exit(); }

$id = intval($_POST['id'] ?? 0);
if (!$id) exit("ID inválido");

// Verificar que pertenece al estudiante y no está completada por el profesor
$check = $conexion->prepare("SELECT archivo, estado_id FROM tareas_estudiantes WHERE id=? AND estudiante_id=?");
$check->bind_param("ii", $id, $_SESSION['id']);
$check->execute();
$row = $check->get_result()->fetch_assoc();

if (!$row)                  exit("No autorizado");
if ($row['estado_id'] == 2) exit("No se puede anular, ya fue completada por el profesor");

// Borrar archivo físico si existe
if ($row['archivo'] && file_exists("uploads/" . $row['archivo'])) {
    unlink("uploads/" . $row['archivo']);
}

$stmt = $conexion->prepare("UPDATE tareas_estudiantes SET entrega=NULL, archivo=NULL, fecha_entrega_real=NULL, estado_id=1 WHERE id=? AND estudiante_id=?");
$stmt->bind_param("ii", $id, $_SESSION['id']);

echo $stmt->execute() ? "ok" : "Error";
?>