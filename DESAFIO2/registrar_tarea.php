<?php
include "conexion.php";
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 1) { header("Location: index.php"); exit(); }

$titulo      = trim($_POST['titulo']      ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$fecha       = trim($_POST['fecha']       ?? '');

if (!$titulo || !$fecha) exit("Datos incompletos");

$fecha = str_replace('T', ' ', $fecha);
if (strlen($fecha) == 16) $fecha .= ':00';

if ($fecha <= date("Y-m-d H:i:s")) exit("Fecha inválida");

// Procesar imagen adjunta del profesor (opcional)
$imagen_nombre = null;
if (!empty($_FILES['imagen']['name'])) {
    $ext_permitidas = ['jpg', 'jpeg', 'png', 'pdf'];
    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $ext_permitidas)) exit("Tipo de archivo no permitido. Solo JPG, PNG o PDF.");
    if ($_FILES['imagen']['size'] > 8 * 1024 * 1024) exit("El archivo supera los 8MB.");

    $carpeta = "uploads/tareas/";
    if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

    $imagen_nombre = uniqid('tarea_', true) . '.' . $ext;
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $imagen_nombre)) {
        exit("Error al guardar el archivo.");
    }
}

$stmt = $conexion->prepare("INSERT INTO tareas(titulo, descripcion, fecha_limite, profesor_id, imagen) VALUES (?,?,?,?,?)");
$stmt->bind_param("sssii", $titulo, $descripcion, $fecha, $_SESSION['id'], $imagen_nombre);

// imagen es varchar, corregir bind
$stmt = $conexion->prepare("INSERT INTO tareas(titulo, descripcion, fecha_limite, profesor_id, imagen) VALUES (?,?,?,?,?)");
$stmt->bind_param("sssis", $titulo, $descripcion, $fecha, $_SESSION['id'], $imagen_nombre);

if (!$stmt->execute()) exit("Error al guardar");

$tarea_id = $stmt->insert_id;

$est = $conexion->query("SELECT id FROM usuarios WHERE rol_id=2");
while ($e = $est->fetch_assoc()) {
    $conexion->query("INSERT INTO tareas_estudiantes(tarea_id, estudiante_id, estado_id) VALUES ($tarea_id, {$e['id']}, 1)");
}

echo "ok";
?>