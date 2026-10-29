<?php
session_start();
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: ../Login/login.html");
    exit();
}

$nombre_usuario = $_SESSION['usuario_nombre'];
$correo_usuario = $_SESSION['usuario_correo'];

$conn = new mysqli("localhost", "root", "", "almoy_db");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

$id = intval($_GET['id']);

$query_categorias = "SELECT * FROM categorias ORDER BY nombre ASC";
$categorias = $conn->query($query_categorias);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $id_categoria = $_POST['id_categoria'];
    $imagen_url = $_POST['imagen_url'];
    $stock = $_POST['stock'];
    $estado = $_POST['estado'];

    $stmt = $conn->prepare("UPDATE stock SET nombre=?, descripcion=?, precio=?, id_categoria=?, imagen_url=?, stock=?, estado=?, fecha_actualizacion=NOW() WHERE id_producto=?");
    $stmt->bind_param("ssdisssi", $nombre, $descripcion, $precio, $id_categoria, $imagen_url, $stock, $estado, $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: stock.php");
        exit();
    } else {
        echo "Error al actualizar: " . $stmt->error;
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT s.*, c.nombre as nombre_categoria FROM stock s LEFT JOIN categorias c ON s.id_categoria = c.id_categoria WHERE s.id_producto=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$producto = $res->fetch_assoc();
$stmt->close();

if (!$producto) {
    die("Producto no encontrado");
}
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Editar Producto</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#0667f9",
                    "secondary": "#22d3ee",
                    "background-light": "#f5f7f8",
                    "background-dark": "#0f1723",
                },
                fontFamily: {
                    "display": ["Space Grotesk", "sans-serif"]
                }
            },
        },
    }
</script>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24
    }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-white">
<div class="flex h-screen">

<aside class="w-64 bg-[#101823] flex flex-col p-4 shrink-0">
<div class="flex items-center gap-3 mb-8 px-2">
<span class="material-symbols-outlined text-secondary" style="font-size: 32px;">widgets</span>
<h1 class="text-white text-xl font-bold">ALMOY</h1>
</div>
<nav class="flex flex-col gap-2">
  <a href="admin.php" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
    <span class="material-symbols-outlined text-white">dashboard</span>
    <p class="text-white text-sm font-medium">Panel Principal</p>
  </a>
  <a href="stock.php" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary text-white transition-colors">
    <span class="material-symbols-outlined text-white">inventory_2</span>
    <p class="text-white text-sm font-medium">Stock</p>
  </a>
  <a href="usuarios.php" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
    <span class="material-symbols-outlined text-white">group</span>
    <p class="text-white text-sm font-medium">Usuarios</p>
  </a>
  <a href="ingresos.php" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
    <span class="material-symbols-outlined text-white">receipt_long</span>
    <p class="text-white text-sm font-medium">Ingresos</p>
  </a>
</nav>
<div class="mt-auto">
<div class="flex items-center gap-3 border-t border-white/10 pt-4">
<div class="bg-primary/80 flex items-center justify-center rounded-full size-10">
<span class="material-symbols-outlined text-white text-2xl">person</span>
</div>
<div class="flex flex-col">
<h1 class="text-white text-base font-medium"><?php echo htmlspecialchars($nombre_usuario); ?></h1>
<p class="text-[#8ea7cc] text-sm font-normal"><?php echo htmlspecialchars($correo_usuario); ?></p>
</div>
</div>
</div>
</aside>

<main class="flex-1 flex flex-col overflow-hidden">
<header class="flex items-center justify-between border-b border-gray-200 dark:border-white/10 px-10 py-3 bg-white dark:bg-[#182335]">
<div class="flex items-center gap-4">
<a href="stock.php" class="text-gray-500 hover:text-gray-700 dark:text-[#8ea7cc] dark:hover:text-white">
<span class="material-symbols-outlined">arrow_back</span>
</a>
<h2 class="text-gray-800 dark:text-white text-lg font-bold">Editar Producto: <?php echo htmlspecialchars($producto['nombre']); ?></h2>
</div>
</header>

<div class="flex-1 overflow-y-auto p-6 lg:p-8">
<div class="max-w-4xl mx-auto">
<div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#182335] shadow-sm p-8">
<form method="POST" class="space-y-6">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Nombre del Producto *</label>
<input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" 
       class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required/>
</div>

<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Precio ($) *</label>
<input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" 
       class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required/>
</div>
</div>

<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Descripción *</label>
<textarea name="descripcion" rows="4" 
          class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Categoría *</label>
<select name="id_categoria" required class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
    <option value="">Seleccionar categoría</option>
    <?php while ($categoria = $categorias->fetch_assoc()): ?>
        <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo ($producto['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($categoria['nombre']); ?>
        </option>
    <?php endwhile; ?>
</select>
<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Actual: <span class="font-semibold text-primary"><?php echo htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría'); ?></span></p>
</div>

<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Stock *</label>
<input type="number" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" min="0"
       class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required/>
<?php 
$stock_actual = $producto['stock'];
if ($stock_actual == 0) {
    $mensaje_stock = '<p class="text-xs text-red-500 mt-1 font-semibold">⚠️ AGOTADO</p>';
} elseif ($stock_actual <= 5) {
    $mensaje_stock = '<p class="text-xs text-red-500 mt-1 font-semibold">⚠️ Stock crítico</p>';
} elseif ($stock_actual <= 15) {
    $mensaje_stock = '<p class="text-xs text-yellow-500 mt-1 font-semibold">⚠️ Stock bajo</p>';
} else {
    $mensaje_stock = '<p class="text-xs text-green-500 mt-1 font-semibold">✓ Stock OK</p>';
}
echo $mensaje_stock;
?>
</div>

<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Estado *</label>
<select name="estado" class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
    <option value="activo" <?php if($producto['estado']=='activo') echo 'selected'; ?>>Activo</option>
    <option value="inactivo" <?php if($producto['estado']=='inactivo') echo 'selected'; ?>>Inactivo</option>
    <option value="descontinuado" <?php if($producto['estado']=='descontinuado') echo 'selected'; ?>>Descontinuado</option>
</select>
</div>
</div>

<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">URL de la Imagen *</label>
<input type="url" name="imagen_url" value="<?php echo htmlspecialchars($producto['imagen_url']); ?>" 
       class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required/>
</div>

<div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-[#21314a] rounded-lg border border-gray-200 dark:border-white/10">
<span class="text-sm font-semibold text-gray-700 dark:text-white">Vista previa:</span>
<img src="<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="Preview" class="w-24 h-24 rounded-lg object-cover border-2 border-primary/30 shadow-md">
<div class="flex-1">
<p class="text-xs text-gray-500 dark:text-gray-400">ID: #<?php echo $producto['id_producto']; ?></p>
<p class="text-xs text-gray-500 dark:text-gray-400">Creado: <?php echo date('d/m/Y', strtotime($producto['fecha_creacion'])); ?></p>
<p class="text-xs text-gray-500 dark:text-gray-400">Última actualización: <?php echo date('d/m/Y H:i', strtotime($producto['fecha_actualizacion'])); ?></p>
</div>
</div>

<div class="flex gap-4 pt-6 border-t border-gray-200 dark:border-white/10">
<button type="submit" class="flex-1 flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold py-3 rounded-lg transition-colors shadow-md">
<span class="material-symbols-outlined">save</span>
Guardar Cambios
</button>
<a href="stock.php" class="px-8 flex items-center justify-center gap-2 bg-gray-200 dark:bg-[#21314a] hover:bg-gray-300 dark:hover:bg-[#2b3e5a] text-gray-800 dark:text-white font-semibold py-3 rounded-lg transition-colors">
<span class="material-symbols-outlined">close</span>
Cancelar
</a>
</div>
</form>
</div>
</div>
</div>
</main>
</div>
</body>
</html>
<?php $conn->close(); ?>
