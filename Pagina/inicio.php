<?php
$conn = new mysqli("localhost", "root", "", "almoy_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$query_novedades = "SELECT * FROM stock WHERE stock > 0 AND estado = 'activo' ORDER BY fecha_creacion DESC LIMIT 4";
$novedades = $conn->query($query_novedades);
$query_ofertas = "SELECT * FROM stock WHERE stock > 0 AND estado = 'activo' ORDER BY RAND() LIMIT 4";
$ofertas = $conn->query($query_ofertas);
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Inicio</title>
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
<body class="bg-background-light dark:bg-background-dark font-display">
<div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
<div class="layout-container flex h-full grow flex-col">
<div class="px-4 md:px-10 lg:px-20 xl:px-40 flex flex-1 justify-center py-5">
<div class="layout-content-container flex flex-col max-w-[1200px] flex-1">
<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-white/10 px-4 sm:px-6 lg:px-10 py-3 bg-[#101823] rounded-xl">
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 text-white">
            <span class="material-symbols-outlined text-[#22d3ee]" style="font-size: 32px;">widgets</span>
            <h2 class="text-white text-xl font-bold leading-tight tracking-[-0.015em]">ALMOY</h2>
        </div>
    </div>
    <div class="flex flex-1 justify-end gap-8">
        <div class="hidden lg:flex items-center gap-9">
            <a class="text-white text-sm font-medium leading-normal hover:text-[#0667f9] transition-colors" href="inicio.php">Inicio</a>
            <a class="text-white text-sm font-medium leading-normal hover:text-[#0667f9] transition-colors" href="catalogo.php">Catálogo</a>
            <a class="text-white text-sm font-medium leading-normal hover:text-[#0667f9] transition-colors" href="contacto.php">Contacto</a>
        </div>
        <div class="flex gap-2">
            <button onclick="abrirCarrito()" class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 bg-[#21314a] text-white hover:bg-[#0667f9] transition-colors gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5">
                <span class="material-symbols-outlined">shopping_cart</span>
            </button>
        </div>
    </div>
</header>
<main class="flex flex-col gap-10 mt-8">
<div class="@container">
<div class="@[480px]:p-0">
<div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 rounded-xl items-center justify-center p-4" style='background-image: linear-gradient(rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%), url("https://as2.ae/wp-content/uploads/2024/10/Gadgets-Examples.webp");'>
<div class="flex flex-col gap-2 text-center">
<h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">
    ¡Gran oferta en relojes inteligentes! ¡Hasta 50% de descuento!
</h1>
<h2 class="text-white/80 text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal">
    Explora nuestra colección de relojes inteligentes y encuentra el perfecto para ti.
</h2>
</div>
<button onclick="window.location.href='catalogo.php'" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
<span class="truncate">Ver Más</span>
</button>
</div>
</div>
</div>
<div>
<h2 class="text-slate-900 dark:text-white text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Novedades</h2>
<div class="flex overflow-y-auto [-ms-scrollbar-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
<div class="flex items-stretch p-4 gap-4">
<?php if ($novedades && $novedades->num_rows > 0): ?>
    <?php while ($producto = $novedades->fetch_assoc()): ?>
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-white dark:bg-[#182335] shadow-sm min-w-60 hover:shadow-lg transition-shadow">
        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-t-xl flex flex-col" style='background-image: url("<?php echo htmlspecialchars($producto['imagen_url']); ?>");'></div>
        <div class="flex flex-col flex-1 justify-between p-4 pt-0 gap-4">
            <div>
                <p class="text-slate-900 dark:text-white text-base font-medium leading-normal"><?php echo htmlspecialchars($producto['nombre']); ?></p>
                <p class="text-slate-500 dark:text-[#8ea7cc] text-sm font-normal leading-normal">$<?php echo number_format($producto['precio'], 2); ?></p>
            </div>
            <button onclick="window.location.href='detalles.php?id=<?php echo $producto['id_producto']; ?>'" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-[#21314a] text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary transition-colors">
                <span class="truncate">Ver Detalles</span>
            </button>
        </div>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="w-full text-center py-8">
        <p class="text-slate-500 dark:text-[#8ea7cc]">No hay productos disponibles en este momento.</p>
    </div>
<?php endif; ?>
</div>
</div>
</div>
<div>
<h2 class="text-slate-900 dark:text-white text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">No Te Pierdas Estas Ofertas</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4">
<?php if ($ofertas && $ofertas->num_rows > 0): ?>
    <?php while ($producto = $ofertas->fetch_assoc()): ?>
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-white dark:bg-[#182335] shadow-sm hover:shadow-lg transition-shadow">
        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-t-xl flex flex-col relative" style='background-image: url("<?php echo htmlspecialchars($producto['imagen_url']); ?>");'>
            <?php 
            $descuento = rand(15, 50);
            $precio_original = $producto['precio'] / (1 - $descuento/100);
            ?>
            <div class="absolute top-2 right-2 bg-secondary text-black text-xs font-bold px-2 py-1 rounded"><?php echo $descuento; ?>% OFF</div>
        </div>
        <div class="flex flex-col flex-1 justify-between p-4 pt-0 gap-4">
            <div>
                <p class="text-slate-900 dark:text-white text-base font-medium leading-normal"><?php echo htmlspecialchars($producto['nombre']); ?></p>
                <div class="flex items-center gap-2">
                    <p class="text-slate-500 dark:text-[#8ea7cc] text-sm font-normal leading-normal line-through">$<?php echo number_format($precio_original, 2); ?></p>
                    <p class="text-secondary text-lg font-bold leading-normal">$<?php echo number_format($producto['precio'], 2); ?></p>
                </div>
            </div>
            <button onclick="window.location.href='detalles.php?id=<?php echo $producto['id_producto']; ?>'" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
                <span class="truncate">Ver Detalles</span>
            </button>
        </div>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="col-span-full text-center py-8">
        <p class="text-slate-500 dark:text-[#8ea7cc]">No hay ofertas disponibles en este momento.</p>
    </div>
<?php endif; ?>
</div>
</div>
<div>
<h2 class="text-slate-900 dark:text-white text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Explora Nuestras Categorías</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
<div onclick="window.location.href='catalogo.php'" class="flex flex-col items-center justify-center gap-4 p-8 rounded-xl bg-white dark:bg-[#182335] shadow-sm hover:shadow-lg transition-shadow cursor-pointer">
<span class="material-symbols-outlined text-primary" style="font-size: 48px;">watch</span>
<h3 class="text-slate-900 dark:text-white text-lg font-bold">Relojes</h3>
</div>
<div onclick="window.location.href='catalogo.php'" class="flex flex-col items-center justify-center gap-4 p-8 rounded-xl bg-white dark:bg-[#182335] shadow-sm hover:shadow-lg transition-shadow cursor-pointer">
<span class="material-symbols-outlined text-primary" style="font-size: 48px;">headphones</span>
<h3 class="text-slate-900 dark:text-white text-lg font-bold">Audio</h3>
</div>
<div onclick="window.location.href='catalogo.php'" class="flex flex-col items-center justify-center gap-4 p-8 rounded-xl bg-white dark:bg-[#182335] shadow-sm hover:shadow-lg transition-shadow cursor-pointer">
<span class="material-symbols-outlined text-primary" style="font-size: 48px;">phone_android</span>
<h3 class="text-slate-900 dark:text-white text-lg font-bold">Accesorios Móviles</h3>
</div>
</div>
</div>
</main>
<footer class="mt-20 border-t border-solid border-white/10 bg-[#101823] rounded-xl p-10">
<div class="grid grid-cols-1 md:grid-cols-4 gap-8">
<div class="col-span-1 md:col-span-1">
<div class="flex items-center gap-2 text-white">
<span class="material-symbols-outlined text-secondary" style="font-size: 32px;">widgets</span>
<h2 class="text-white text-xl font-bold leading-tight tracking-[-0.015em]">ALMOY</h2>
</div>
<p class="text-white/60 mt-4 text-sm">Tu tienda ideal para los mejores accesorios y gadgets tecnológicos.</p>
</div>
<div>
<h3 class="text-white font-bold mb-4">Atención al Cliente</h3>
<ul class="space-y-2 text-white/60">
<li><a class="hover:text-primary transition-colors" href="#">Preguntas Frecuentes</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Envíos y Devoluciones</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Términos de Servicio</a></li>
</ul>
</div>
<div>
<h3 class="text-white font-bold mb-4">Sobre Nosotros</h3>
<ul class="space-y-2 text-white/60">
<li><a class="hover:text-primary transition-colors" href="#">Nuestra Historia</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Carreras</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Prensa</a></li>
</ul>
</div>
<div>
<h3 class="text-white font-bold mb-4">Síguenos</h3>
<div class="flex space-x-4">
<a class="text-white/60 hover:text-primary transition-colors" href="#">
<svg class="feather feather-facebook" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
</a>
<a class="text-white/60 hover:text-primary transition-colors" href="#">
<svg class="feather feather-twitter" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
</a>
<a class="text-white/60 hover:text-primary transition-colors" href="#">
<svg class="feather feather-instagram" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><rect height="20" rx="5" ry="5" width="20" x="2" y="2"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
</a>
</div>
</div>
</div>
<div class="text-center text-white/40 text-sm mt-10 pt-8 border-t border-white/10">
© 2023 ALMOY. All Rights Reserved.
</div>
</footer>
</div>
</div>
</div>
</div>

<script src="carrito-manager.js"></script>
</body>
</html>
<?php $conn->close(); ?>