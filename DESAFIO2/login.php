<?php
include "conexion.php";

$carnet   = trim($_POST['carnet']   ?? '');
$password = trim($_POST['password'] ?? '');

if (!$carnet || !$password) exit("Datos incompletos");

$stmt = $conexion->prepare("SELECT id, nombre, rol_id FROM usuarios WHERE carnet=? AND password=?");
$stmt->bind_param("ss", $carnet, $password);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) exit("error");

$u = $res->fetch_assoc();

$_SESSION['id']     = $u['id'];
$_SESSION['nombre'] = $u['nombre'];
$_SESSION['rol']    = $u['rol_id'];

echo "ok";
?>