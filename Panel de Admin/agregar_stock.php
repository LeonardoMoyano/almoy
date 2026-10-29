<?php
session_start();
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: ../Login/login.html");
    exit();
}

$nombre_usuario = $_SESSION['usuario_nombre'];
$correo_usuario = $_SESSION['usuario_correo'];

$conn = new mysqli("localhost", "root", "", "almoy_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$query_categorias = "SELECT * FROM categorias ORDER BY nombre ASC";
$categorias = $conn->query($query_categorias);

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $id_categoria = $_POST['id_categoria'];
    $imagen_url = $_POST['imagen_url'];
    $stock = $_POST['stock'];
    $estado = $_POST['estado'];
    
    $sql = "INSERT INTO stock (nombre, descripcion, precio, id_categoria, imagen_url, stock, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdisis", $nombre, $descripcion, $precio, $id_categoria, $imagen_url, $stock, $estado);
    
    if ($stmt->execute()) {
        $mensaje = "Producto agregado exitosamente";
        $tipo_mensaje = "success";
        header("refresh:2;url=stock.php");
    } else {
        $mensaje = "Error al agregar el producto: " . $conn->error;
        $tipo_mensaje = "error";
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Agregar Producto</title>
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
                },
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
<body class="bg-background-light dark:bg-background-dark font-display">
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
<h2 class="text-gray-800 dark:text-white text-lg font-bold">Agregar Nuevo Producto</h2>
<a href="stock.php" class="text-gray-600 dark:text-gray-400 hover:text-primary transition-colors">
<span class="material-symbols-outlined text-3xl">close</span>
</a>
</header>

<div class="flex-1 overflow-y-auto p-8">
<?php if ($mensaje): ?>
<div class="mb-6 p-4 rounded-lg <?php echo $tipo_mensaje === 'success' ? 'bg-green-500/20 border border-green-500 text-green-500' : 'bg-red-500/20 border border-red-500 text-red-500'; ?>">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined"><?php echo $tipo_mensaje === 'success' ? 'check_circle' : 'error'; ?></span>
        <p class="font-medium"><?php echo $mensaje; ?></p>
    </div>
</div>
<?php endif; ?>

<div class="max-w-4xl mx-auto bg-white dark:bg-[#182335] rounded-xl shadow-lg p-8">
<form method="POST" class="space-y-6">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Nombre del Producto *</label>
        <input type="text" name="nombre" required class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Precio ($) *</label>
        <input type="number" step="0.01" name="precio" required class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Descripción *</label>
    <textarea name="descripcion" rows="4" required class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Categoría *</label>
        <select name="id_categoria" required class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Seleccionar categoría</option>
            <?php while ($categoria = $categorias->fetch_assoc()): ?>
                <option value="<?php echo $categoria['id_categoria']; ?>">
                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Stock Inicial *</label>
        <input type="number" name="stock" min="0" required class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Estado *</label>
        <select name="estado" required class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="descontinuado">Descontinuado</option>
        </select>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">URL de la Imagen *</label>
    <input type="url" name="imagen_url" required placeholder="https://ejemplo.com/imagen.jpg" class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
</div>

<div class="flex gap-4 pt-4">
    <button type="submit" class="flex-1 bg-primary hover:bg-primary/90 text-white font-bold py-3 rounded-lg transition-colors flex items-center justify-center gap-2">
        <span class="material-symbols-outlined">add_circle</span>
        Agregar Producto
    </button>
    <a href="stock.php" class="px-8 bg-gray-200 dark:bg-[#21314a] hover:bg-gray-300 dark:hover:bg-[#2b3e5a] text-gray-800 dark:text-white font-bold py-3 rounded-lg transition-colors flex items-center justify-center">
        Cancelar
    </a>
</div>
</form>
</div>
</div>
</main>
</div>
</body>
</html>
<?php $conn->close(); ?>
