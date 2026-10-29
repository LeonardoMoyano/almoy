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

$sql = "SELECT id, nombre, correo, password, fecha_registro FROM usuarios ORDER BY id ASC";
$result = $conn->query($sql);
$total_usuarios = $result->num_rows;
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Usuarios</title>
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
   class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
    <span class="material-symbols-outlined text-white">inventory_2</span>
    <p class="text-white text-sm font-medium">Stock</p>
  </a>

  <a href="usuarios.php"
   class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary text-white transition-colors">
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
<h2 class="text-gray-800 dark:text-white text-lg font-bold">Gestión de Usuarios</h2>
</div>
</header>

<div class="flex-1 overflow-y-auto p-6 lg:p-8">
<div class="grid grid-cols-1 gap-6 mb-8">
<div class="flex min-w-72 flex-1 flex-col gap-2 rounded-xl border border-gray-200 dark:border-white/10 p-6 bg-white dark:bg-[#182335] shadow-sm hover:shadow-md transition-shadow max-w-md">
<div class="flex items-center justify-between">
<p class="text-gray-600 dark:text-white text-base font-medium">Total Usuarios</p>
<span class="material-symbols-outlined text-primary text-3xl">group</span>
</div>
<p class="text-gray-800 dark:text-white tracking-light text-[32px] font-bold truncate"><?php echo $total_usuarios; ?></p>
<p class="text-gray-500 dark:text-[#8ea7cc] text-base font-normal">Registrados en el sistema</p>
</div>
</div>

<div class="flex justify-between items-center mb-6">
<h3 class="text-gray-800 dark:text-white text-xl font-bold">Lista de Usuarios</h3>
</div>

<div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#182335] shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full">
<thead class="bg-gray-50 dark:bg-[#21314a]">
<tr class="border-b border-gray-200 dark:border-white/10">
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">ID</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Usuario</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Correo</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Contraseña</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Fecha Registro</th>
<th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-white uppercase tracking-wider">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-200 dark:divide-white/10">
<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr class="hover:bg-gray-50 dark:hover:bg-[#21314a]/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white font-semibold"><?php echo $row['id']; ?></td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">person</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($row['nombre']); ?></span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($row['correo']); ?></td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-[#8ea7cc] font-mono max-w-[200px] truncate" title="<?php echo $row['password']; ?>">
                <?php echo strlen($row['password']) > 30 ? substr($row['password'], 0, 30) . '...' : $row['password']; ?>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white"><?php echo date('d/m/Y', strtotime($row['fecha_registro'])); ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <a href="editar_usuarios.php?id=<?php echo $row['id']; ?>" class="text-primary hover:text-primary/80 mr-3" title="Editar">
                    <span class="material-symbols-outlined text-xl">edit</span>
                </a>
                <a href="borrar_usuarios.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-400" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')" title="Eliminar">
                    <span class="material-symbols-outlined text-xl">delete</span>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="6" class="text-center py-8 text-gray-400">
            <span class="material-symbols-outlined text-5xl mb-2 block">group</span>
            No hay usuarios registrados
        </td>
    </tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</main>
</div>
</body>
</html>
<?php $conn->close(); ?>