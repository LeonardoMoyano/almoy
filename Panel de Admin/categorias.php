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

$sql = "SELECT c.*, COUNT(s.id_producto) as total_productos 
        FROM categorias c 
        LEFT JOIN stock s ON c.id_categoria = s.id_categoria 
        GROUP BY c.id_categoria 
        ORDER BY c.nombre ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Gestión de Categorías</title>
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
<h2 class="text-gray-800 dark:text-white text-lg font-bold">Gestión de Categorías</h2>
</div>
</header>

<div class="flex-1 overflow-y-auto p-6 lg:p-8">
<div class="flex justify-between items-center mb-6">
<h3 class="text-gray-800 dark:text-white text-xl font-bold">Categorías de Productos</h3>
<button onclick="abrirModalAgregar()" class="flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors">
<span class="material-symbols-outlined">add_circle</span>
Nueva Categoría
</button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#182335] shadow-sm hover:shadow-md transition-shadow p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-primary/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-2xl">category</span>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($row['nombre']); ?></h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">ID: <?php echo $row['id_categoria']; ?></p>
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-gray-600 dark:text-[#8ea7cc] mb-4 line-clamp-2 min-h-[40px]">
                <?php echo htmlspecialchars($row['descripcion'] ?? 'Sin descripción'); ?>
            </p>
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-white/10">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-sm">inventory_2</span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white"><?php echo $row['total_productos']; ?> productos</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="abrirModalEditar(<?php echo $row['id_categoria']; ?>, '<?php echo htmlspecialchars($row['nombre'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['descripcion'] ?? '', ENT_QUOTES); ?>')" class="text-primary hover:text-primary/80 p-1" title="Editar">
                        <span class="material-symbols-outlined text-xl">edit</span>
                    </button>
                    <button onclick="confirmarEliminar(<?php echo $row['id_categoria']; ?>, '<?php echo htmlspecialchars($row['nombre'], ENT_QUOTES); ?>', <?php echo $row['total_productos']; ?>)" class="text-red-500 hover:text-red-400 p-1" title="Eliminar">
                        <span class="material-symbols-outlined text-xl">delete</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="col-span-full text-center py-12">
        <span class="material-symbols-outlined text-gray-400 text-6xl mb-4 block">category</span>
        <p class="text-gray-500 dark:text-[#8ea7cc] text-lg">No hay categorías registradas</p>
    </div>
<?php endif; ?>
</div>
</div>
</main>
</div>

<div id="modalAgregar" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-[#182335] rounded-xl shadow-2xl p-8 w-[90%] max-w-md border border-gray-200 dark:border-white/10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-gray-800 dark:text-white text-2xl font-bold">Nueva Categoría</h2>
            <button onclick="cerrarModalAgregar()" class="text-gray-500 hover:text-gray-700 dark:text-[#8ea7cc] dark:hover:text-white">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>
        <form action="agregar_categoria.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Nombre de la Categoría *</label>
                <input type="text" name="nombre" required class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Ej: Electrónica">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Descripción (Opcional)</label>
                <textarea name="descripcion" rows="3" class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Describe esta categoría..."></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-primary hover:bg-primary/90 text-white font-semibold py-3 rounded-lg transition-colors">
                    Crear Categoría
                </button>
                <button type="button" onclick="cerrarModalAgregar()" class="px-6 bg-gray-200 dark:bg-[#21314a] hover:bg-gray-300 dark:hover:bg-[#2b3e5a] text-gray-800 dark:text-white font-semibold py-3 rounded-lg transition-colors">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditar" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-[#182335] rounded-xl shadow-2xl p-8 w-[90%] max-w-md border border-gray-200 dark:border-white/10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-gray-800 dark:text-white text-2xl font-bold">Editar Categoría</h2>
            <button onclick="cerrarModalEditar()" class="text-gray-500 hover:text-gray-700 dark:text-[#8ea7cc] dark:hover:text-white">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>
        <form action="editar_categoria.php" method="POST" class="space-y-4">
            <input type="hidden" name="id_categoria" id="edit_id_categoria">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Nombre de la Categoría *</label>
                <input type="text" name="nombre" id="edit_nombre" required class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Descripción (Opcional)</label>
                <textarea name="descripcion" id="edit_descripcion" rows="3" class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-primary hover:bg-primary/90 text-white font-semibold py-3 rounded-lg transition-colors">
                    Guardar Cambios
                </button>
                <button type="button" onclick="cerrarModalEditar()" class="px-6 bg-gray-200 dark:bg-[#21314a] hover:bg-gray-300 dark:hover:bg-[#2b3e5a] text-gray-800 dark:text-white font-semibold py-3 rounded-lg transition-colors">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalAgregar() {
    document.getElementById('modalAgregar').classList.remove('hidden');
}

function cerrarModalAgregar() {
    document.getElementById('modalAgregar').classList.add('hidden');
}

function abrirModalEditar(id, nombre, descripcion) {
    document.getElementById('edit_id_categoria').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_descripcion').value = descripcion;
    document.getElementById('modalEditar').classList.remove('hidden');
}

function cerrarModalEditar() {
    document.getElementById('modalEditar').classList.add('hidden');
}

function confirmarEliminar(id, nombre, totalProductos) {
    let mensaje = `¿Estás seguro de eliminar la categoría "${nombre}"?`;
    
    if (totalProductos > 0) {
        mensaje += `\n\n⚠️ ADVERTENCIA: Esta categoría tiene ${totalProductos} producto(s) asociado(s).\nSi la eliminas, estos productos quedarán sin categoría.`;
    }
    
    if (confirm(mensaje)) {
        window.location.href = `eliminar_categoria.php?id=${id}`;
    }
}

document.getElementById('modalAgregar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalAgregar();
});

document.getElementById('modalEditar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalEditar();
});
</script>
</body>
</html>
<?php $conn->close(); ?>