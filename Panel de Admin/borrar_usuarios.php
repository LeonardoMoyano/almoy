<?php
session_start();
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: ../Login/login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "almoy_db");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: usuarios.php");
exit();
?>