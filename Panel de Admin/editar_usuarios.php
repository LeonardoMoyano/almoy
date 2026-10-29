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

if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $correo, $hash, $id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nombre, $correo, $id);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: usuarios.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, nombre, correo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();
$stmt->close();

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Editar Usuario</title>
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
  <a href="stock.php" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
    <span class="material-symbols-outlined text-white">inventory_2</span>
    <p class="text-white text-sm font-medium">Stock</p>
  </a>
  <a href="usuarios.php" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary text-white transition-colors">
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
<a href="usuarios.php" class="text-gray-500 hover:text-gray-700 dark:text-[#8ea7cc] dark:hover:text-white">
<span class="material-symbols-outlined">arrow_back</span>
</a>
<h2 class="text-gray-800 dark:text-white text-lg font-bold">Editar Usuario #<?php echo $id; ?></h2>
</div>
</header>

<div class="flex-1 overflow-y-auto p-6 lg:p-8">
<div class="max-w-2xl mx-auto">
<div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#182335] shadow-sm p-8">
<div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200 dark:border-white/10">
<div class="w-20 h-20 rounded-full bg-primary/20 flex items-center justify-center">
<span class="material-symbols-outlined text-primary text-4xl">person</span>
</div>
<div>
<h3 class="text-xl font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($usuario['nombre']); ?></h3>
<p class="text-gray-500 dark:text-[#8ea7cc]"><?php echo htmlspecialchars($usuario['correo']); ?></p>
</div>
</div>

<form method="POST" class="space-y-6">
<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Nombre Completo</label>
<input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" 
       class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required/>
</div>

<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Correo Electrónico</label>
<input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" 
       class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required/>
</div>

<div>
<label class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Nueva Contraseña (Opcional)</label>
<input type="password" name="password" placeholder="Dejar en blanco para no cambiar" 
       class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#21314a] border border-gray-300 dark:border-white/10 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary"/>
<p class="mt-2 text-xs text-gray-500 dark:text-[#8ea7cc]">
<span class="material-symbols-outlined text-sm align-middle">info</span>
Solo completa este campo si deseas cambiar la contraseña actual
</p>
</div>

<div class="flex gap-4 pt-6">
<button type="submit" class="flex-1 flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold py-3 rounded-lg transition-colors">
<span class="material-symbols-outlined">save</span>
Guardar Cambios
</button>
<a href="usuarios.php" class="px-8 flex items-center justify-center gap-2 bg-gray-200 dark:bg-[#21314a] hover:bg-gray-300 dark:hover:bg-[#2b3e5a] text-gray-800 dark:text-white font-semibold py-3 rounded-lg transition-colors">
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