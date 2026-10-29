<?php
$conn = new mysqli("localhost", "root", "", "almoy_db");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM stock WHERE id_producto = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: stock.php");
exit();
?>
