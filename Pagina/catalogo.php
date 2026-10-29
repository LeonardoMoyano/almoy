<?php
$conn = new mysqli("localhost", "root", "", "almoy_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$query_categorias = "SELECT * FROM categorias ORDER BY nombre ASC";
$categorias = $conn->query($query_categorias);

$query = "SELECT s.*, c.nombre as nombre_categoria 
          FROM stock s 
          LEFT JOIN categorias c ON s.id_categoria = c.id_categoria 
          WHERE s.estado = 'activo' 
          ORDER BY s.id_producto ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Catálogo</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet"/>
<script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#0667f9",
                    "secondary": "#22d3ee",
                    "background-light": "#F0F0F0",
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
    
    #sidebarCategorias {
        transition: transform 0.3s ease-in-out;
    }
    
    #overlayCategoria {
        transition: opacity 0.3s ease-in-out;
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
            <span class="material-symbols-outlined text-secondary" style="font-size: 32px;">widgets</span>
            <h2 class="text-white text-xl font-bold leading-tight tracking-[-0.015em]">ALMOY</h2>
        </div>
    </div>
    <div class="flex flex-1 justify-end gap-8">
        <div class="hidden lg:flex items-center gap-9">
            <a class="text-white text-sm font-medium leading-normal hover:text-primary transition-colors" href="inicio.php">Inicio</a>
            <a class="text-primary text-sm font-bold leading-normal" href="catalogo.php">Catálogo</a>
            <a class="text-white text-sm font-medium leading-normal hover:text-primary transition-colors" href="contacto.php">Contacto</a>
        </div>
        <div class="flex gap-2">
            <button onclick="abrirCarrito()" class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 bg-[#21314a] text-white hover:bg-primary transition-colors gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5">
                <span class="material-symbols-outlined">shopping_cart</span>
            </button>
        </div>
    </div>
</header>

<main class="flex flex-col gap-6 p-4 md:p-6 lg:p-8">
    <div class="bg-gray-100 dark:bg-gray-800/20 p-4 rounded-lg flex flex-col md:flex-row gap-4 items-center">
        <div class="flex-grow w-full">
            <label class="flex flex-col min-w-40 h-12 w-full">
                <div class="flex w-full flex-1 items-stretch rounded-lg h-full">
                    <div class="text-gray-500 dark:text-[#8ea7cc] flex border-none bg-white dark:bg-[#21314a] items-center justify-center pl-4 rounded-l-lg border-r-0">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input id="searchInput" onkeypress="buscarEnter(event)" class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-r-lg text-gray-800 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary border-none bg-white dark:bg-[#21314a] focus:border-none h-full placeholder:text-gray-500 dark:placeholder:text-[#8ea7cc] px-4 pl-2 text-base font-normal leading-normal" placeholder="buscar productos y presiona Enter" value=""/>
                </div>
            </label>
        </div>
        <div class="flex gap-3 flex-wrap justify-center">
            <button onclick="toggleSidebarCategorias()" class="flex h-12 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-white dark:bg-[#21314a] px-4 hover:bg-gray-50 dark:hover:bg-[#2b3e5a] transition-colors">
                <p class="text-gray-800 dark:text-white text-sm font-medium leading-normal">Categoría</p>
                <span class="material-symbols-outlined text-secondary dark:text-secondary">arrow_drop_down</span>
            </button>
            <button onclick="toggleOrdenPrecio()" id="btnPrecio" class="flex h-12 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-white dark:bg-[#21314a] px-4 hover:bg-gray-50 dark:hover:bg-[#2b3e5a] transition-colors">
                <p class="text-gray-800 dark:text-white text-sm font-medium leading-normal">Precio</p>
                <span class="material-symbols-outlined text-secondary dark:text-secondary" id="iconoPrecio">arrow_drop_down</span>
            </button>
        </div>
    </div>
    
    <div id="filtrosActivos" class="flex flex-wrap gap-2 hidden">
        <span id="filtroCategoria" class="hidden bg-primary/20 text-primary px-3 py-1 rounded-full text-sm font-medium flex items-center gap-2">
            <span id="nombreCategoriaActiva"></span>
            <button onclick="quitarFiltroCategoria()" class="hover:bg-primary/30 rounded-full">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </span>
        <span id="filtroPrecio" class="hidden bg-secondary/20 text-secondary px-3 py-1 rounded-full text-sm font-medium flex items-center gap-2">
            <span id="ordenPrecioActivo"></span>
            <button onclick="quitarFiltroPrecio()" class="hover:bg-secondary/30 rounded-full">
                <span class="material-symbols-oriented text-sm">close</span>
            </button>
        </span>
    </div>
    
    <div id="productosGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($producto = $result->fetch_assoc()): ?>
                <div class="producto-item flex flex-col gap-3 pb-3 bg-white dark:bg-[#1a2638] rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden" 
                     data-nombre="<?php echo strtolower($producto['nombre']); ?>" 
                     data-descripcion="<?php echo strtolower($producto['descripcion']); ?>"
                     data-categoria="<?php echo $producto['id_categoria']; ?>"
                     data-precio="<?php echo $producto['precio']; ?>">
                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover" style='background-image: url("<?php echo htmlspecialchars($producto['imagen_url']); ?>");'></div>
                    <div class="p-4 flex flex-col flex-grow">
                        <p class="text-gray-900 dark:text-white text-lg font-bold leading-normal"><?php echo htmlspecialchars($producto['nombre']); ?></p>
                        <p class="text-gray-500 dark:text-[#8ea7cc] text-xs mt-1"><?php echo htmlspecialchars($producto['nombre_categoria']); ?></p>
                        
                        <?php if ($producto['stock'] > 0): ?>
                            <p class="text-gray-900 dark:text-white text-base font-medium mt-2">$<?php echo number_format($producto['precio'], 2); ?></p>
                            <button onclick="window.location.href='detalles.php?id=<?php echo $producto['id_producto']; ?>'" class="mt-4 w-full flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90">
                                <span class="truncate">Ver Detalles</span>
                            </button>
                        <?php else: ?>
                            <div class="mt-3 bg-red-500/20 border border-red-500 rounded-lg py-2 px-3 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-red-500 text-xl">error</span>
                                <p class="text-red-500 text-sm font-bold uppercase">AGOTADO</p>
                            </div>
                            <button disabled class="mt-4 w-full flex min-w-[84px] max-w-[480px] cursor-not-allowed items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-gray-500 text-white text-sm font-bold leading-normal tracking-[0.015em] opacity-50">
                                <span class="truncate">No Disponible</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <span class="material-symbols-outlined text-gray-400 text-6xl mb-4 block">inventory_2</span>
                <p class="text-gray-500 dark:text-[#8ea7cc] text-lg">No hay productos disponibles en este momento.</p>
            </div>
        <?php endif; ?>
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

<div id="overlayCategoria" class="hidden fixed inset-0 bg-black/60 z-40" onclick="toggleSidebarCategorias()"></div>

<div id="sidebarCategorias" class="fixed top-0 right-0 h-full w-80 bg-white dark:bg-[#182335] shadow-2xl z-50 transform translate-x-full">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-white/10">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Categorías</h3>
            <button onclick="toggleSidebarCategorias()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <div class="space-y-2">
                <button onclick="filtrarPorCategoria(null, 'Todas')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-[#21314a] transition-colors text-gray-800 dark:text-white font-medium categoria-btn">
                    Todas las categorías
                </button>
                <?php 
                $categorias->data_seek(0);
                while ($categoria = $categorias->fetch_assoc()): 
                ?>
                    <button onclick="filtrarPorCategoria(<?php echo $categoria['id_categoria']; ?>, '<?php echo htmlspecialchars($categoria['nombre']); ?>')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-[#21314a] transition-colors text-gray-800 dark:text-white categoria-btn">
                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                    </button>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script src="carrito-manager.js"></script>
<script>
let ordenPrecio = 'ninguno';
let categoriaActiva = null;

function toggleSidebarCategorias() {
    const sidebar = document.getElementById('sidebarCategorias');
    const overlay = document.getElementById('overlayCategoria');
    
    if (sidebar.classList.contains('translate-x-full')) {
        sidebar.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('translate-x-full');
        overlay.classList.add('hidden');
    }
}

function filtrarPorCategoria(idCategoria, nombreCategoria) {
    categoriaActiva = idCategoria;
    
    toggleSidebarCategorias();
    
    const filtroCategoria = document.getElementById('filtroCategoria');
    const nombreCategoriaActiva = document.getElementById('nombreCategoriaActiva');
    const filtrosActivos = document.getElementById('filtrosActivos');
    
    if (idCategoria === null) {
        filtroCategoria.classList.add('hidden');
    } else {
        nombreCategoriaActiva.textContent = nombreCategoria;
        filtroCategoria.classList.remove('hidden');
        filtrosActivos.classList.remove('hidden');
    }
    
    aplicarFiltros();
}

function quitarFiltroCategoria() {
    categoriaActiva = null;
    document.getElementById('filtroCategoria').classList.add('hidden');
    verificarFiltrosActivos();
    aplicarFiltros();
}

function toggleOrdenPrecio() {
    const iconoPrecio = document.getElementById('iconoPrecio');
    const filtroPrecio = document.getElementById('filtroPrecio');
    const ordenPrecioActivo = document.getElementById('ordenPrecioActivo');
    const filtrosActivos = document.getElementById('filtrosActivos');
    
    if (ordenPrecio === 'ninguno') {
        ordenPrecio = 'asc';
        iconoPrecio.textContent = 'arrow_upward';
        ordenPrecioActivo.textContent = 'Menor a Mayor';
        filtroPrecio.classList.remove('hidden');
        filtrosActivos.classList.remove('hidden');
    } else if (ordenPrecio === 'asc') {
        ordenPrecio = 'desc';
        iconoPrecio.textContent = 'arrow_downward';
        ordenPrecioActivo.textContent = 'Mayor a Menor';
    } else {
        ordenPrecio = 'ninguno';
        iconoPrecio.textContent = 'arrow_drop_down';
        filtroPrecio.classList.add('hidden');
        verificarFiltrosActivos();
    }
    
    aplicarFiltros();
}

function quitarFiltroPrecio() {
    ordenPrecio = 'ninguno';
    document.getElementById('iconoPrecio').textContent = 'arrow_drop_down';
    document.getElementById('filtroPrecio').classList.add('hidden');
    verificarFiltrosActivos();
    aplicarFiltros();
}

function verificarFiltrosActivos() {
    const filtrosActivos = document.getElementById('filtrosActivos');
    const filtroCategoria = document.getElementById('filtroCategoria');
    const filtroPrecio = document.getElementById('filtroPrecio');
    
    if (filtroCategoria.classList.contains('hidden') && filtroPrecio.classList.contains('hidden')) {
        filtrosActivos.classList.add('hidden');
    }
}

function aplicarFiltros() {
    const productos = Array.from(document.getElementsByClassName('producto-item'));
    const inputBusqueda = document.getElementById('searchInput').value.toLowerCase();
    
    let productosVisibles = productos.filter(producto => {
        if (inputBusqueda !== '') {
            const nombre = producto.getAttribute('data-nombre');
            const descripcion = producto.getAttribute('data-descripcion') || '';
            if (nombre.indexOf(inputBusqueda) === -1 && descripcion.indexOf(inputBusqueda) === -1) {
                return false;
            }
        }
        
        if (categoriaActiva !== null) {
            const categoria = producto.getAttribute('data-categoria');
            if (parseInt(categoria) !== categoriaActiva) {
                return false;
            }
        }
        
        return true;
    });
    
    if (ordenPrecio !== 'ninguno') {
        productosVisibles.sort((a, b) => {
            const precioA = parseFloat(a.getAttribute('data-precio'));
            const precioB = parseFloat(b.getAttribute('data-precio'));
            return ordenPrecio === 'asc' ? precioA - precioB : precioB - precioA;
        });
    }
    
    productos.forEach(p => p.style.display = 'none');
    
    const grid = document.getElementById('productosGrid');
    productosVisibles.forEach(producto => {
        producto.style.display = '';
        grid.appendChild(producto);
    });
    
    mostrarMensajeNoEncontrado(productosVisibles.length === 0, inputBusqueda);
}

function buscarEnter(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        aplicarFiltros();
    }
}

function mostrarMensajeNoEncontrado(mostrar, termino) {
    let mensaje = document.getElementById('mensajeNoEncontrado');
    
    if (mostrar) {
        if (!mensaje) {
            mensaje = document.createElement('div');
            mensaje.id = 'mensajeNoEncontrado';
            mensaje.className = 'col-span-full text-center py-12 bg-gray-100 dark:bg-gray-800/20 rounded-lg';
            mensaje.innerHTML = `
                <span class="material-symbols-outlined text-gray-400 text-6xl mb-4 block">search_off</span>
                <p class="text-gray-500 dark:text-[#8ea7cc] text-lg font-semibold mb-2">No se encontraron productos</p>
                <p class="text-gray-400 dark:text-[#8ea7cc] text-sm">No hay productos que coincidan con tu búsqueda</p>
                <button onclick="limpiarTodo()" class="mt-4 px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    Limpiar filtros
                </button>
            `;
            document.getElementById('productosGrid').appendChild(mensaje);
        }
    } else {
        if (mensaje) {
            mensaje.remove();
        }
    }
}

function limpiarTodo() {
    document.getElementById('searchInput').value = '';
    categoriaActiva = null;
    ordenPrecio = 'ninguno';
    document.getElementById('iconoPrecio').textContent = 'arrow_drop_down';
    document.getElementById('filtroCategoria').classList.add('hidden');
    document.getElementById('filtroPrecio').classList.add('hidden');
    document.getElementById('filtrosActivos').classList.add('hidden');
    aplicarFiltros();
}
</script>
</body>
</html>
<?php $conn->close(); ?>