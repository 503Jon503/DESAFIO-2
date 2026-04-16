<?php
include "conexion.php";
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) { header("Location: index.php"); exit(); }

$id    = intval($_POST['id']      ?? 0);
$texto = trim($_POST['entrega']   ?? '');

if (!$id) exit("ID inválido");

// Verificar que la tarea pertenece a este estudiante y obtener fecha límite
$check = $conexion->prepare("
    SELECT te.id, te.estado_id, t.fecha_limite
    FROM tareas_estudiantes te
    JOIN tareas t ON t.id = te.tarea_id
    WHERE te.id=? AND te.estudiante_id=?
");
$check->bind_param("ii", $id, $_SESSION['id']);
$check->execute();
$row = $check->get_result()->fetch_assoc();

if (!$row)                      exit("No autorizado");
if ($row['estado_id'] == 2)     exit("Ya fue completada por el profesor");

// Validar fecha límite
if (strtotime($row['fecha_limite']) < time()) {
    exit("La fecha límite ha vencido. No puedes entregar esta tarea.");
}

$archivo_nombre = null;

// Procesar archivo si viene
if (!empty($_FILES['archivo']['name'])) {
    $ext_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $ext_permitidas)) exit("Tipo de archivo no permitido. Solo PDF, JPG o PNG.");
    if ($_FILES['archivo']['size'] > 5 * 1024 * 1024) exit("El archivo supera los 5MB.");

    $carpeta = "uploads/";
    if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

    $archivo_nombre = uniqid('entrega_', true) . '.' . $ext;
    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $carpeta . $archivo_nombre)) {
        exit("Error al guardar el archivo.");
    }
}

// Debe haber texto o archivo
if (!$texto && !$archivo_nombre) exit("Debes escribir algo o adjuntar un archivo.");

$stmt = $conexion->prepare("UPDATE tareas_estudiantes SET entrega=?, archivo=?, fecha_entrega_real=NOW(), estado_id=1 WHERE id=? AND estudiante_id=?");
$stmt->bind_param("ssii", $texto, $archivo_nombre, $id, $_SESSION['id']);

echo $stmt->execute() ? "ok" : "Error al guardar";
?>