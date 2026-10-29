<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: ../Login/login.html");
    exit();
}

$nombre_usuario = $_SESSION['usuario_nombre'];
$correo_usuario = $_SESSION['usuario_correo'];

$stock_critico = 5;
$stock_bajo = 15;
$query = "SELECT id_producto, nombre, stock FROM stock WHERE stock <= ? ORDER BY stock ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $stock_bajo);
$stmt->execute();
$resultado = $stmt->get_result();

$alertas = [];
$total_alertas = 0;

while ($producto = $resultado->fetch_assoc()) {
    $alertas[] = $producto;
    $total_alertas++;
}
$stmt->close();
$query_ventas_24h = "SELECT COUNT(*) as total_ventas, COALESCE(SUM(total), 0) as total_monto 
                     FROM ventas 
                     WHERE fecha_venta >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
$result_24h = $conn->query($query_ventas_24h);
$ventas_24h = $result_24h->fetch_assoc();
$total_ventas_24h = $ventas_24h['total_ventas'];
$monto_ventas_24h = $ventas_24h['total_monto'];
$query_ultimas = "SELECT id_venta, total, metodo_pago, fecha_venta 
                  FROM ventas 
                  WHERE fecha_venta >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                  ORDER BY fecha_venta DESC";
$result_ultimas = $conn->query($query_ultimas);
$ultimas_ventas = [];
while ($row = $result_ultimas->fetch_assoc()) {
    $ultimas_ventas[] = $row;
}
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - ADMIN</title>
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
   class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary text-white transition-colors">
    <span class="material-symbols-outlined text-white">dashboard</span>
    <p class="text-white text-sm font-medium">Panel Principal</p>
  </a>

  <a href="stock.php"
   class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
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
<h2 class="text-gray-800 dark:text-white text-lg font-bold">Panel Principal</h2>
</div>
</header>

<div class="p-6 lg:p-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6" style="height: calc(100vh - 140px);">
<div class="flex flex-col gap-2 rounded-xl border border-gray-200 dark:border-white/10 p-6 bg-white dark:bg-[#182335] shadow-sm hover:shadow-md transition-shadow overflow-hidden">
<div class="flex items-center justify-between mb-2 flex-shrink-0">
<p class="text-gray-600 dark:text-white text-base font-medium">Ventas Últimas 24h</p>
<span class="material-symbols-outlined text-secondary text-2xl">schedule</span>
</div>
<p class="text-gray-800 dark:text-white tracking-light text-[32px] font-bold truncate flex-shrink-0">
<?php echo $total_ventas_24h; ?> Venta<?php echo $total_ventas_24h != 1 ? 's' : ''; ?>
</p>

<div class="flex flex-col gap-3 mt-4 overflow-y-auto flex-1" style="max-height: calc(100vh - 320px);">
<?php if (count($ultimas_ventas) > 0): ?>
    <?php foreach ($ultimas_ventas as $venta): ?>
        <?php 
        $tiempo_transcurrido = time() - strtotime($venta['fecha_venta']);
        $horas = floor($tiempo_transcurrido / 3600);
        $minutos = floor(($tiempo_transcurrido % 3600) / 60);
        $tiempo_texto = $horas > 0 ? "Hace {$horas}h" : "Hace {$minutos}min";
        
        $metodo = $venta['metodo_pago'];
        $color_metodo = 'blue';
        if ($metodo == 'Efectivo') $color_metodo = 'green';
        elseif ($metodo == 'Transferencia Bancaria') $color_metodo = 'purple';
        ?>
        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-[#21314a]/50 border border-gray-200 dark:border-white/10 rounded-lg">
            <span class="material-symbols-outlined text-secondary text-xl">receipt</span>
            <div class="flex-1">
                <p class="text-gray-900 dark:text-white text-sm font-semibold">
                    Venta #<?php echo $venta['id_venta']; ?>
                </p>
                <p class="text-gray-600 dark:text-[#8ea7cc] text-xs">
                    <?php echo $tiempo_texto; ?> • 
                    <span class="text-<?php echo $color_metodo; ?>-500"><?php echo $metodo; ?></span>
                </p>
            </div>
            <span class="text-green-500 text-sm font-bold">
                $<?php echo number_format($venta['total'], 2); ?>
            </span>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="flex items-center justify-center gap-3 p-6 bg-gray-50 dark:bg-[#21314a]/30 border border-gray-200 dark:border-white/10 rounded-lg">
        <span class="material-symbols-outlined text-gray-400 text-3xl">shopping_cart_off</span>
        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">
            No hay ventas en las últimas 24 horas
        </p>
    </div>
<?php endif; ?>
</div>
</div>
<div class="flex flex-col gap-2 rounded-xl border border-gray-200 dark:border-white/10 p-6 bg-white dark:bg-[#182335] shadow-sm hover:shadow-md transition-shadow overflow-hidden">
<div class="flex items-center justify-between mb-2 flex-shrink-0">
<p class="text-gray-600 dark:text-white text-base font-medium">Alertas de Stock</p>
<?php if ($total_alertas > 0): ?>
<span class="material-symbols-outlined text-red-500">warning</span>
<?php else: ?>
<span class="material-symbols-outlined text-green-500">check_circle</span>
<?php endif; ?>
</div>
<p class="text-gray-800 dark:text-white tracking-light text-[32px] font-bold truncate flex-shrink-0">
<?php echo $total_alertas; ?> Producto<?php echo $total_alertas != 1 ? 's' : ''; ?>
</p>
<div class="flex flex-col gap-3 overflow-y-auto flex-1 mt-4" style="max-height: calc(100vh - 320px);">
<?php if ($total_alertas > 0): ?>
    <?php foreach ($alertas as $alerta): ?>
        <?php 
        $es_critico = $alerta['stock'] <= $stock_critico;
        $color_class = $es_critico ? 'red' : 'yellow';
        $nivel = $es_critico ? 'CRÍTICO' : 'BAJO';
        ?>
        <div class="flex items-center gap-3 p-3 bg-<?php echo $color_class; ?>-500/10 border border-<?php echo $color_class; ?>-500/20 rounded-lg flex-shrink-0">
            <span class="material-symbols-outlined text-<?php echo $color_class; ?>-500 text-xl">
                <?php echo $es_critico ? 'error' : 'warning'; ?>
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-gray-900 dark:text-white text-sm font-semibold truncate">
                    <?php echo htmlspecialchars($alerta['nombre']); ?>
                </p>
                <p class="text-gray-600 dark:text-[#8ea7cc] text-xs">
                    <?php 
                    if ($alerta['stock'] == 0) {
                        echo 'Stock: <span class="font-bold text-red-600 dark:text-red-400">AGOTADO</span>';
                    } else {
                        echo 'Stock: ' . $alerta['stock'] . ' unidades';
                    }
                    ?>
                </p>
            </div>
            <span class="text-<?php echo $color_class; ?>-500 text-xs font-bold whitespace-nowrap">
                <?php echo $nivel; ?>
            </span>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="flex items-center justify-center gap-3 p-6 bg-green-500/10 border border-green-500/20 rounded-lg">
        <span class="material-symbols-outlined text-green-500 text-3xl">inventory</span>
        <p class="text-gray-900 dark:text-white text-sm font-medium">
            No hay productos con stock bajo
        </p>
    </div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</main>
</div>
</body>
</html>