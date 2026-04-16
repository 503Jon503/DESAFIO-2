<?php
include "conexion.php";
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 1) { header("Location: index.php"); exit(); }

$id = intval($_POST['id'] ?? 0);
if (!$id) exit("ID inválido");

$stmt = $conexion->prepare("UPDATE tareas_estudiantes SET estado_id=2 WHERE id=?");
$stmt->bind_param("i", $id);

echo $stmt->execute() ? "ok" : "Error";
?>