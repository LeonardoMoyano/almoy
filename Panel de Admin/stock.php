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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['accion_categoria'])) {
        $accion = $_POST['accion_categoria'];
        
        if ($accion == 'agregar') {
            $nombre = $_POST['nombre_categoria'];
            $descripcion = $_POST['descripcion_categoria'];
            
            $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
            $stmt->bind_param("ss", $nombre, $descripcion);
            $stmt->execute();
            $stmt->close();
            
            header("Location: stock.php");
            exit();
        } 
        elseif ($accion == 'editar') {
            $id_categoria = $_POST['id_categoria'];
            $nombre = $_POST['nombre_categoria'];
            $descripcion = $_POST['descripcion_categoria'];
            
            $stmt = $conn->prepare("UPDATE categorias SET nombre=?, descripcion=? WHERE id_categoria=?");
            $stmt->bind_param("ssi", $nombre, $descripcion, $id_categoria);
            $stmt->execute();
            $stmt->close();
            
            header("Location: stock.php");
            exit();
        }
        elseif ($accion == 'eliminar') {
            $id_categoria = $_POST['id_categoria'];
            
            $check = $conn->prepare("SELECT COUNT(*) as total FROM stock WHERE id_categoria = ?");
            $check->bind_param("i", $id_categoria);
            $check->execute();
            $result_check = $check->get_result();
            $row_check = $result_check->fetch_assoc();
            
            if ($row_check['total'] == 0) {
                $stmt = $conn->prepare("DELETE FROM categorias WHERE id_categoria = ?");
                $stmt->bind_param("i", $id_categoria);
                $stmt->execute();
                $stmt->close();
            }
            $check->close();
            
            header("Location: stock.php");
            exit();
        }
    }
}

$sql_cat = "SELECT c.*, COUNT(s.id_producto) as total_productos 
            FROM categorias c 
            LEFT JOIN stock s ON c.id_categoria = s.id_categoria 
            GROUP BY c.id_categoria 
            ORDER BY c.id_categoria ASC";
$result_cat = $conn->query($sql_cat);

$sql = "SELECT * FROM stock ORDER BY id_producto ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Stock</title>
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
                borderRadius: {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
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
  <a href="admin.php"
   class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
    <span class="material-symbols-outlined text-white">dashboard</span>
    <p class="text-white text-sm font-medium">Panel Principal</p>
  </a>

  <a href="stock.php"
   class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary text-white transition-colors">
    <span class="material-symbols-outlined text-white">inventory_2</span>
    <p class="text-white text-sm font-medium">Stock</p>
  </a>

  <a href="usuarios.php"
   class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
    <span class="material-symbols-outlined text-white">group</span>
    <p class="text-white text-sm font-medium">Usuarios</p>
  </a>

  <a href="ingresos.php"
   class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
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
<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-gray-200 dark:border-white/10 px-10 py-3 bg-white dark:bg-[#182335]">
<div class="flex items-center gap-4 text-white">
<h2 class="text-gray-800 dark:text-white text-lg font-bold">Gestión de Stock</h2>
</div>
</header>

<div class="flex-1 overflow-y-auto p-6 lg:p-8">

<div class="flex justify-between items-center mb-6">
<h3 class="text-gray-800 dark:text-white text-2xl font-bold">Productos en Stock</h3>
<a href="agregar_stock.php" class="flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors">
<span class="material-symbols-outlined">add_circle</span>
Agregar Producto
</a>
</div>

<div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#182335] shadow-sm overflow-hidden mb-12">
<div class="overflow-x-auto max-h-[600px] overflow-y-auto">
<table class="w-full">
<thead class="bg-gray-50 dark:bg-[#21314a] sticky top-0 z-10">
<tr class="border-b border-gray-200 dark:border-white/10">
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">ID</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Producto</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Descripción</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Precio</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Categoría</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Imagen</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Stock</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Estado</th>
<th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-200 dark:divide-white/10">
<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php
            if ($row['stock'] == 0) {
                $stock_color = 'text-red-600 dark:text-red-500 font-bold';
                $stock_text = 'AGOTADO';
            } elseif ($row['stock'] <= 5) {
                $stock_color = 'text-red-500 dark:text-red-400 font-bold';
                $stock_text = $row['stock'] . ' unidades';
            } elseif ($row['stock'] <= 15) {
                $stock_color = 'text-yellow-500 dark:text-yellow-400 font-semibold';
                $stock_text = $row['stock'] . ' unidades';
            } else {
                $stock_color = 'text-green-500 dark:text-green-400 font-semibold';
                $stock_text = $row['stock'] . ' unidades';
            }
            
            $estado_badge = ($row['estado'] == 'activo') 
                ? 'px-2 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-500'
                : 'px-2 py-1 text-xs font-medium rounded-full bg-gray-500/20 text-gray-500';
        ?>
        <tr class="hover:bg-gray-50 dark:hover:bg-[#21314a]/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white"><?php echo $row['id_producto']; ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($row['nombre']); ?></td>
            <td class="px-6 py-4 text-sm text-gray-800 dark:text-white max-w-[200px] truncate" title="<?php echo htmlspecialchars($row['descripcion']); ?>">
                <?php echo htmlspecialchars($row['descripcion']); ?>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white">$<?php echo number_format($row['precio'], 2, ',', '.'); ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white"><?php echo $row['id_categoria']; ?></td>
            <td class="px-6 py-4 whitespace-nowrap">
                <img src="<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="Producto" class="w-12 h-12 rounded-lg object-cover border border-white/10">
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $stock_color; ?>"><?php echo $stock_text; ?></td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="<?php echo $estado_badge; ?>"><?php echo ucfirst($row['estado']); ?></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <a href="editar_stock.php?id=<?php echo $row['id_producto']; ?>" class="text-primary hover:text-primary/80 mr-3" title="Editar">
                    <span class="material-symbols-outlined text-xl">edit</span>
                </a>
                <a href="borrar_stock.php?id=<?php echo $row['id_producto']; ?>" class="text-red-500 hover:text-red-400" onclick="return confirm('¿Seguro que deseas eliminar este producto?')" title="Eliminar">
                    <span class="material-symbols-outlined text-xl">delete</span>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="9" class="text-center py-8 text-gray-400">
            <span class="material-symbols-outlined text-5xl mb-2 block">inventory_2</span>
            No hay productos en stock
        </td>
    </tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<div class="mb-8">
<div class="flex justify-between items-center mb-6">
<h3 class="text-gray-800 dark:text-white text-2xl font-bold">Gestión de Categorías</h3>
<button onclick="document.getElementById('modalAgregarCategoria').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-secondary hover:bg-secondary/90 text-gray-900 rounded-lg font-medium transition-colors">
<span class="material-symbols-outlined">add_circle</span>
Nueva Categoría
</button>
</div>

<div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#182335] shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full">
<thead class="bg-gray-50 dark:bg-[#21314a]">
<tr class="border-b border-gray-200 dark:border-white/10">
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">ID</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Nombre</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Descripción</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Productos</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Fecha Creación</th>
<th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-200 dark:divide-white/10">
<?php if ($result_cat && $result_cat->num_rows > 0): ?>
    <?php while ($cat = $result_cat->fetch_assoc()): ?>
        <tr class="hover:bg-gray-50 dark:hover:bg-[#21314a]/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white"><?php echo $cat['id_categoria']; ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($cat['nombre']); ?></td>
            <td class="px-6 py-4 text-sm text-gray-800 dark:text-white max-w-[300px] truncate"><?php echo htmlspecialchars($cat['descripcion']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-primary/20 text-primary">
                    <?php echo $cat['total_productos']; ?> productos
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white"><?php echo date('d/m/Y', strtotime($cat['fecha_creacion'])); ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <button onclick="abrirModalEditar(<?php echo $cat['id_categoria']; ?>, '<?php echo addslashes($cat['nombre']); ?>', '<?php echo addslashes($cat['descripcion']); ?>')" class="text-primary hover:text-primary/80 mr-3" title="Editar">
                    <span class="material-symbols-outlined text-xl">edit</span>
                </button>
                <button onclick="confirmarEliminar(<?php echo $cat['id_categoria']; ?>, '<?php echo addslashes($cat['nombre']); ?>', <?php echo $cat['total_productos']; ?>)" class="text-red-500 hover:text-red-400" title="Eliminar">
                    <span class="material-symbols-outlined text-xl">delete</span>
                </button>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="6" class="text-center py-8 text-gray-400">
            <span class="material-symbols-outlined text-5xl mb-2 block">category</span>
            No hay categorías registradas
        </td>
    </tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</main>
</div>

<div id="modalAgregarCategoria" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
<div class="bg-white dark:bg-[#182335] rounded-xl shadow-2xl max-w-md w-full p-6">
<div class="flex justify-between items-center mb-6">
<h3 class="text-xl font-bold text-gray-800 dark:text-white">Nueva Categoría</h3>
<button onclick="document.getElementById('modalAgregarCategoria').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<form method="POST" class="space-y-4">
<input type="hidden" name="accion_categoria" value="agregar">
<div>
<label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Nombre *</label>
<input type="text" name="nombre_categoria" required class="w-full px-4 py-2 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Descripción</label>
<textarea name="descripcion_categoria" rows="3" class="w-full px-4 py-2 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
</div>
<div class="flex gap-3 pt-4">
<button type="submit" class="flex-1 bg-secondary hover:bg-secondary/90 text-gray-900 font-semibold py-2 rounded-lg transition-colors">
Agregar
</button>
<button type="button" onclick="document.getElementById('modalAgregarCategoria').classList.add('hidden')" class="px-6 bg-gray-200 dark:bg-[#21314a] hover:bg-gray-300 dark:hover:bg-[#2b3e5a] text-gray-800 dark:text-white font-semibold py-2 rounded-lg transition-colors">
Cancelar
</button>
</div>
</form>
</div>
</div>

<div id="modalEditarCategoria" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
<div class="bg-white dark:bg-[#182335] rounded-xl shadow-2xl max-w-md w-full p-6">
<div class="flex justify-between items-center mb-6">
<h3 class="text-xl font-bold text-gray-800 dark:text-white">Editar Categoría</h3>
<button onclick="document.getElementById('modalEditarCategoria').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<form method="POST" class="space-y-4">
<input type="hidden" name="accion_categoria" value="editar">
<input type="hidden" name="id_categoria" id="edit_id_categoria">
<div>
<label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Nombre *</label>
<input type="text" name="nombre_categoria" id="edit_nombre_categoria" required class="w-full px-4 py-2 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Descripción</label>
<textarea name="descripcion_categoria" id="edit_descripcion_categoria" rows="3" class="w-full px-4 py-2 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
</div>
<div class="flex gap-3 pt-4">
<button type="submit" class="flex-1 bg-primary hover:bg-primary/90 text-white font-semibold py-2 rounded-lg transition-colors">
Guardar Cambios
</button>
<button type="button" onclick="document.getElementById('modalEditarCategoria').classList.add('hidden')" class="px-6 bg-gray-200 dark:bg-[#21314a] hover:bg-gray-300 dark:hover:bg-[#2b3e5a] text-gray-800 dark:text-white font-semibold py-2 rounded-lg transition-colors">
Cancelar
</button>
</div>
</form>
</div>
</div>

<form id="formEliminarCategoria" method="POST" class="hidden">
<input type="hidden" name="accion_categoria" value="eliminar">
<input type="hidden" name="id_categoria" id="delete_id_categoria">
</form>

<script>
function abrirModalEditar(id, nombre, descripcion) {
    document.getElementById('edit_id_categoria').value = id;
    document.getElementById('edit_nombre_categoria').value = nombre;
    document.getElementById('edit_descripcion_categoria').value = descripcion;
    document.getElementById('modalEditarCategoria').classList.remove('hidden');
}

function confirmarEliminar(id, nombre, totalProductos) {
    if (totalProductos > 0) {
        alert('No se puede eliminar la categoría "' + nombre + '" porque tiene ' + totalProductos + ' producto(s) asociado(s).');
        return;
    }
    
    if (confirm('¿Estás seguro de eliminar la categoría "' + nombre + '"?')) {
        document.getElementById('delete_id_categoria').value = id;
        document.getElementById('formEliminarCategoria').submit();
    }
}
</script>
</body>
</html>
<?php $conn->close(); ?>