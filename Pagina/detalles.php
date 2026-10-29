<?php

$conn = new mysqli("localhost", "root", "", "almoy_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM stock WHERE id_producto = ? AND estado = 'activo'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: catalogo.php");
    exit();
}

$producto = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ALMOY - <?php echo htmlspecialchars($producto['nombre']); ?></title>
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
          fontFamily: { display: ["Space Grotesk", "sans-serif"] },
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

<header class="flex items-center justify-between border-b border-white/10 px-6 lg:px-16 py-4 bg-[#101823]">
  <div class="flex items-center gap-2 text-white">
    <span class="material-symbols-outlined text-secondary" style="font-size: 32px;">widgets</span>
    <h2 class="text-white text-xl font-bold">ALMOY</h2>
  </div>
  <div class="flex items-center gap-4">
    <div class="hidden lg:flex items-center gap-9">
      <a class="text-white text-sm font-medium leading-normal hover:text-primary transition-colors" href="inicio.php">Inicio</a>
      <a class="text-white text-sm font-medium leading-normal hover:text-primary transition-colors" href="catalogo.php">Catálogo</a>
      <a class="text-white text-sm font-medium leading-normal hover:text-primary transition-colors" href="contacto.php">Contacto</a>
    </div>
    <div class="flex gap-2">
      <button onclick="abrirCarrito()" class="flex items-center justify-center rounded-lg h-10 bg-[#21314a] text-white hover:text-primary transition-colors px-3 relative">
        <span class="material-symbols-outlined">shopping_cart</span>
      </button>
    </div>
  </div>
</header>

<main class="max-w-6xl mx-auto py-10 px-6 lg:px-20">
  <button onclick="history.back()" class="flex items-center gap-2 text-white hover:text-primary transition-colors mb-6">
    <span class="material-symbols-outlined">arrow_back</span>
    <span>Volver</span>
  </button>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

    <div class="flex justify-center">
      <div class="w-full aspect-square rounded-xl bg-center bg-no-repeat bg-cover shadow-lg" 
           style="background-image: url('<?php echo htmlspecialchars($producto['imagen_url']); ?>');"></div>
    </div>


    <div class="flex flex-col justify-center">
      <h1 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
      <p class="text-3xl text-primary font-bold mb-6">$<?php echo number_format($producto['precio'], 2); ?></p>

      <div class="text-gray-400 leading-relaxed mb-6">
        <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
      </div>


      <div class="mb-6">
        <div class="flex items-center gap-2 text-green-500">
          <span class="material-symbols-outlined">check_circle</span>
          <span class="font-semibold">Stock disponible</span>
        </div>
      </div>

      <button 
        onclick="agregarAlCarrito()" 
        id="btn-agregar-carrito"
        class="flex items-center justify-center gap-2 w-full h-14 bg-primary text-white rounded-lg text-lg font-bold hover:bg-primary/90 transition-colors shadow-lg">
        <span class="material-symbols-outlined">add_shopping_cart</span>
        <span>Agregar al Carrito</span>
      </button>
    </div>
  </div>
</main>

<footer class="border-t border-white/10 bg-[#101823] p-10 mt-20">
  <div class="text-center text-white/60 text-sm">
    © 2025 ALMOY. Todos los derechos reservados.
  </div>
</footer>

<script src="carrito-manager.js"></script>
<script>

const productoData = {
  id: '<?php echo $producto['id_producto']; ?>',
  nombre: '<?php echo addslashes($producto['nombre']); ?>',
  precio: <?php echo $producto['precio']; ?>,
  imagen: '<?php echo addslashes($producto['imagen_url']); ?>'
};

function agregarAlCarrito() {
  window.carrito.agregarProducto(productoData);
}
</script>

</body>
</html>