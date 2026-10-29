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
$sql_total = "SELECT COALESCE(SUM(total), 0) as total_ingresos, COUNT(*) as total_ventas FROM ventas";
$result_total = $conn->query($sql_total);
$stats = $result_total->fetch_assoc();
$total_ingresos = $stats['total_ingresos'] ?? 0;
$total_ventas = $stats['total_ventas'] ?? 0;
$ganancias_semanales = [
    'Semana 1' => 0,
    'Semana 2' => 0,
    'Semana 3' => 0,
    'Semana 4' => 0
];

$ventas_semanales = [
    'Semana 1' => 0,
    'Semana 2' => 0,
    'Semana 3' => 0,
    'Semana 4' => 0
];
$sql_semanas = "
SELECT 
    FLOOR(DATEDIFF(NOW(), fecha_venta) / 7) as semanas_atras,
    SUM(total) as total_semana,
    COUNT(*) as ventas_semana
FROM ventas 
WHERE fecha_venta >= DATE_SUB(NOW(), INTERVAL 28 DAY)
GROUP BY FLOOR(DATEDIFF(NOW(), fecha_venta) / 7)
ORDER BY semanas_atras ASC
";

$result_semanas = $conn->query($sql_semanas);

if ($result_semanas) {
    while ($row = $result_semanas->fetch_assoc()) {
        $semanas_atras = intval($row['semanas_atras']);
        if ($semanas_atras == 0) {
            $ganancias_semanales['Semana 4'] = floatval($row['total_semana']);
            $ventas_semanales['Semana 4'] = intval($row['ventas_semana']);
        } elseif ($semanas_atras == 1) {
            $ganancias_semanales['Semana 3'] = floatval($row['total_semana']);
            $ventas_semanales['Semana 3'] = intval($row['ventas_semana']);
        } elseif ($semanas_atras == 2) {
            $ganancias_semanales['Semana 2'] = floatval($row['total_semana']);
            $ventas_semanales['Semana 2'] = intval($row['ventas_semana']);
        } elseif ($semanas_atras == 3) {
            $ganancias_semanales['Semana 1'] = floatval($row['total_semana']);
            $ventas_semanales['Semana 1'] = intval($row['ventas_semana']);
        }
    }
}
$max_ganancia = max($ganancias_semanales);
$max_ventas = max($ventas_semanales);
if ($max_ganancia == 0) $max_ganancia = 1;
if ($max_ventas == 0) $max_ventas = 1;
$sql_ventas = "SELECT * FROM ventas ORDER BY fecha_venta DESC LIMIT 20";
$result_ventas = $conn->query($sql_ventas);
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Ventas e Ingresos</title>
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
  <a href="stock.php" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
    <span class="material-symbols-outlined text-white">inventory_2</span>
    <p class="text-white text-sm font-medium">Stock</p>
  </a>
  <a href="usuarios.php" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-[#21314a] hover:bg-[#2b3e5a] transition-colors">
    <span class="material-symbols-outlined text-white">group</span>
    <p class="text-white text-sm font-medium">Usuarios</p>
  </a>
  <a href="ingresos.php" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary text-white transition-colors">
    <span class="material-symbols-outlined text-white">receipt_long</span>
    <p class="text-white text-sm font-medium">Ventas e Ingresos</p>
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
<h2 class="text-gray-800 dark:text-white text-lg font-bold">Gestión de Ventas e Ingresos</h2>
</header>

<div class="flex-1 overflow-y-auto p-6 lg:p-8">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
<div class="flex flex-col gap-2 rounded-xl border border-gray-200 dark:border-white/10 p-6 bg-white dark:bg-[#182335] shadow-sm hover:shadow-md transition-shadow">
<p class="text-gray-600 dark:text-white text-base font-medium">Ganancias Semanales</p>
<p class="text-gray-800 dark:text-white tracking-light text-[32px] font-bold truncate">
$<?php echo number_format($ganancias_semanales['Semana 4'], 2); ?>
</p>
<div class="flex gap-1 items-center">
<p class="text-gray-500 dark:text-[#8ea7cc] text-base font-normal">Últimos 28 Días</p>
<?php 
$semana_actual = $ganancias_semanales['Semana 4'];
$semana_anterior = $ganancias_semanales['Semana 3'];
$cambio = 0;
if ($semana_anterior > 0) {
    $cambio = (($semana_actual - $semana_anterior) / $semana_anterior) * 100;
}
$es_positivo = $cambio >= 0;
?>
<?php if ($semana_anterior > 0): ?>
<p class="text-<?php echo $es_positivo ? 'secondary' : 'red-500'; ?> text-base font-medium flex items-center">
<span class="material-symbols-outlined text-sm"><?php echo $es_positivo ? 'arrow_upward' : 'arrow_downward'; ?></span>
<?php echo ($es_positivo ? '+' : '') . number_format($cambio, 1); ?>%
</p>
<?php endif; ?>
</div>
<div class="grid min-h-[180px] grid-flow-col gap-6 grid-rows-[1fr_auto] items-end justify-items-center px-3 mt-4">
<?php foreach ($ganancias_semanales as $semana => $ganancia): ?>
    <?php 
    $altura = ($ganancia / $max_ganancia) * 100;
    if ($altura > 0 && $altura < 5) $altura = 5;
    $es_actual = $semana == 'Semana 4';
    ?>
    <div class="bg-<?php echo $es_actual ? 'primary' : 'primary/20'; ?> w-full rounded-t-md" style="height: <?php echo $altura; ?>%;"></div>
    <p class="text-gray-500 dark:text-[#8ea7cc] text-[13px] font-bold tracking-[0.015em]"><?php echo $semana; ?></p>
<?php endforeach; ?>
</div>
</div>
<div class="flex flex-col gap-2 rounded-xl border border-gray-200 dark:border-white/10 p-6 bg-white dark:bg-[#182335] shadow-sm hover:shadow-md transition-shadow">
<div class="flex items-center justify-between">
<p class="text-gray-600 dark:text-white text-base font-medium">Ventas por Semana</p>
<span class="material-symbols-outlined text-primary text-3xl">receipt</span>
</div>
<p class="text-gray-800 dark:text-white text-[32px] font-bold"><?php echo $ventas_semanales['Semana 4']; ?> Ventas</p>
<p class="text-gray-500 dark:text-[#8ea7cc] text-base">Última semana</p>
<div class="grid min-h-[180px] grid-flow-col gap-6 grid-rows-[1fr_auto] items-end justify-items-center px-3 mt-4">
<?php foreach ($ventas_semanales as $semana => $ventas): ?>
    <?php 
    $altura = ($ventas / $max_ventas) * 100;
    if ($altura > 0 && $altura < 5) $altura = 5;
    $es_actual = $semana == 'Semana 4';
    ?>
    <div class="bg-<?php echo $es_actual ? 'secondary' : 'secondary/20'; ?> w-full rounded-t-md" style="height: <?php echo $altura; ?>%;"></div>
    <p class="text-gray-500 dark:text-[#8ea7cc] text-[13px] font-bold tracking-[0.015em]"><?php echo $semana; ?></p>
<?php endforeach; ?>
</div>
</div>
</div>

<div class="flex justify-between items-center mb-6">
<h3 class="text-gray-800 dark:text-white text-xl font-bold">Historial de Ventas (Últimas 20)</h3>
<div class="text-gray-500 dark:text-[#8ea7cc] text-sm">
Total de todas las ventas: <span class="font-bold text-gray-800 dark:text-white"><?php echo $total_ventas; ?></span> • 
Ingresos: <span class="font-bold text-green-500">$<?php echo number_format($total_ingresos, 2); ?></span>
</div>
</div>

<div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#182335] shadow-sm overflow-hidden">
<div class="overflow-x-auto max-h-[500px] overflow-y-auto">
<table class="w-full">
<thead class="bg-gray-50 dark:bg-[#21314a] sticky top-0 z-10">
<tr class="border-b border-gray-200 dark:border-white/10">
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">ID</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">Fecha</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">Subtotal</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">Intereses</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">Total</th>
<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">Método</th>
<th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-white uppercase">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-200 dark:divide-white/10">
<?php if ($result_ventas && $result_ventas->num_rows > 0): ?>
    <?php while ($venta = $result_ventas->fetch_assoc()): ?>
        <tr class="hover:bg-gray-50 dark:hover:bg-[#21314a]/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-white">#<?php echo $venta['id_venta']; ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white"><?php echo date('d/m/Y H:i', strtotime($venta['fecha_venta'])); ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white">$<?php echo number_format($venta['subtotal'], 2); ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-white">$<?php echo number_format($venta['interes'], 2); ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-500">$<?php echo number_format($venta['total'], 2); ?></td>
            <td class="px-6 py-4 whitespace-nowrap">
                <?php
                $metodo = $venta['metodo_pago'];
                $badge_class = 'px-3 py-1 text-xs font-medium rounded-full ';
                if ($metodo == 'Efectivo' || $metodo == 'Transferencia Bancaria') {
                    $badge_class .= 'bg-green-500/20 text-green-500';
                } elseif ($metodo == 'Tarjeta de Crédito') {
                    $badge_class .= 'bg-blue-500/20 text-blue-500';
                } else {
                    $badge_class .= 'bg-purple-500/20 text-purple-500';
                }
                ?>
                <span class="<?php echo $badge_class; ?>"><?php echo $metodo; ?></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <button onclick="verDetalle(<?php echo $venta['id_venta']; ?>)" class="text-primary hover:text-primary/80" title="Ver detalle">
                    <span class="material-symbols-outlined text-xl">visibility</span>
                </button>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="7" class="text-center py-12 text-gray-400">
            <span class="material-symbols-outlined text-6xl mb-3 block">receipt_long</span>
            <p class="text-lg">No hay ventas registradas</p>
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
<div id="modalDetalle" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
<div class="bg-white dark:bg-[#182335] rounded-xl shadow-2xl max-w-3xl w-full p-8 max-h-[90vh] overflow-y-auto">
<div class="flex justify-between items-center mb-6 border-b border-gray-200 dark:border-white/10 pb-4">
<h3 class="text-2xl font-bold text-gray-800 dark:text-white">Detalle de Venta #<span id="detalle-id"></span></h3>
<button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
<span class="material-symbols-outlined text-3xl">close</span>
</button>
</div>

<div class="mb-6 grid grid-cols-2 gap-4">
<div class="bg-gray-50 dark:bg-[#21314a] p-4 rounded-lg">
<p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Fecha</p>
<p class="text-gray-900 dark:text-white font-semibold" id="detalle-fecha"></p>
</div>
<div class="bg-gray-50 dark:bg-[#21314a] p-4 rounded-lg">
<p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Método de Pago</p>
<p class="text-gray-900 dark:text-white font-semibold" id="detalle-metodo"></p>
</div>
</div>

<div class="mb-6">
<h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Productos</h4>
<div id="detalle-productos" class="space-y-3">
</div>
</div>

<div class="border-t border-gray-200 dark:border-white/10 pt-4">
<div class="space-y-2">
<div class="flex justify-between text-gray-800 dark:text-gray-300">
<span>Subtotal:</span>
<span id="detalle-subtotal">$0.00</span>
</div>
<div class="flex justify-between text-gray-800 dark:text-gray-300">
<span>Intereses:</span>
<span id="detalle-intereses">$0.00</span>
</div>
<div class="flex justify-between text-gray-900 dark:text-white text-xl font-bold border-t border-gray-200 dark:border-white/10 pt-2">
<span>Total:</span>
<span id="detalle-total">$0.00</span>
</div>
</div>
</div>

<div class="flex gap-3 mt-6">
<button onclick="cerrarModal()" class="flex-1 bg-gray-200 dark:bg-[#21314a] hover:bg-gray-300 dark:hover:bg-[#2b3e5a] text-gray-800 dark:text-white font-semibold py-3 rounded-lg transition-colors">
Cerrar
</button>
</div>
</div>
</div>

<script>
function verDetalle(idVenta) {
    fetch('obtener_detalle_venta.php?id=' + idVenta)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const venta = data.venta;
            const productos = data.productos;
            
            document.getElementById('detalle-id').textContent = venta.id_venta;
            document.getElementById('detalle-fecha').textContent = new Date(venta.fecha_venta).toLocaleString('es-AR');
            document.getElementById('detalle-metodo').textContent = venta.metodo_pago;
            document.getElementById('detalle-subtotal').textContent = '$' + parseFloat(venta.subtotal).toFixed(2);
            document.getElementById('detalle-intereses').textContent = '$' + parseFloat(venta.interes).toFixed(2);
            document.getElementById('detalle-total').textContent = '$' + parseFloat(venta.total).toFixed(2);
            
            let productosHtml = '';
            productos.forEach(prod => {
                productosHtml += `
                <div class="flex items-center justify-between bg-gray-50 dark:bg-[#21314a] p-3 rounded-lg">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-semibold">${prod.nombre_producto}</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Cantidad: ${prod.cantidad} × $${parseFloat(prod.precio_unitario).toFixed(2)}</p>
                    </div>
                    <p class="text-gray-900 dark:text-white font-bold">$${parseFloat(prod.subtotal_producto).toFixed(2)}</p>
                </div>
                `;
            });
            
            document.getElementById('detalle-productos').innerHTML = productosHtml;
            document.getElementById('modalDetalle').classList.remove('hidden');
        } else {
            alert('Error al cargar el detalle');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar el detalle');
    });
}

function cerrarModal() {
    document.getElementById('modalDetalle').classList.add('hidden');
}

document.getElementById('modalDetalle').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>
</body>
</html>
<?php $conn->close(); ?>