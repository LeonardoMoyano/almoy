<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "almoy_db");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit();
}

$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (!$datos) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

$subtotal = $datos['subtotal'];
$interes = $datos['interes'];
$total = $datos['total'];
$metodo_pago = $datos['metodo_pago'];
$productos = $datos['productos'];
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO ventas (subtotal, interes, total, metodo_pago) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ddds", $subtotal, $interes, $total, $metodo_pago);
    $stmt->execute();
    $id_venta = $conn->insert_id;
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, nombre_producto, cantidad, precio_unitario, subtotal_producto) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($productos as $producto) {
        $id_producto = intval($producto['id']);
        $nombre = $producto['nombre'];
        $cantidad = intval($producto['cantidad']);
        $precio = floatval($producto['precio']);
        $subtotal_prod = $precio * $cantidad;
        
        $stmt->bind_param("iisidi", $id_venta, $id_producto, $nombre, $cantidad, $precio, $subtotal_prod);
        $stmt->execute();
        
        $update_stock = $conn->prepare("UPDATE stock SET stock = stock - ? WHERE id_producto = ?");
        $update_stock->bind_param("ii", $cantidad, $id_producto);
        $update_stock->execute();
        $update_stock->close();
    }
    
    $stmt->close();

    $conn->commit();
    
    echo json_encode(['success' => true, 'id_venta' => $id_venta]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>