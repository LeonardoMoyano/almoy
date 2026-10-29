<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "almoy_db");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit();
}

$id_venta = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM ventas WHERE id_venta = ?");
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$result = $stmt->get_result();
$venta = $result->fetch_assoc();
$stmt->close();

if (!$venta) {
    echo json_encode(['success' => false, 'message' => 'Venta no encontrada']);
    exit();
}
$stmt = $conn->prepare("SELECT * FROM detalle_ventas WHERE id_venta = ?");
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}
$stmt->close();

echo json_encode([
    'success' => true,
    'venta' => $venta,
    'productos' => $productos
]);

$conn->close();
?>