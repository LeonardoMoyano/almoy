<!DOCTYPE html>
<html class="dark" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>ALMOY - Contacto</title>
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
    .form-input {
        background-color: #182335;
        border: 1px solid #2f476a;
        color: #000000ff;
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .form-input:focus {
        outline: none;
        border-color: #0667f9;
        box-shadow: 0 0 0 3px rgba(6, 103, 249, 0.3);
    }
    .form-input::placeholder {
        color: #8ea7cc;
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
            <a class="text-white text-sm font-medium leading-normal hover:text-primary transition-colors" href="catalogo.php">Catálogo</a>
            <a class="text-primary text-sm font-bold leading-normal" href="contacto.php">Contacto</a>
        </div>
        <div class="flex gap-2">
            <button onclick="abrirCarrito()" class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 bg-[#21314a] text-white hover:bg-primary transition-colors gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5">
                <span class="material-symbols-outlined">shopping_cart</span>
            </button>
        </div>
    </div>
</header>

<main class="flex-1 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-2xl">
        <div class="text-center mb-8">
            <h2 class="text-4xl font-black font-display tracking-[-0.033em] text-gray-900 dark:text-white">Contáctanos</h2>
            <p class="text-base font-normal leading-normal text-gray-600 dark:text-[#8ea7cc] mt-2">
                Nos encantaría saber de ti. Por favor completa el formulario a continuación y nos pondremos en contacto contigo lo antes posible.
            </p>
        </div>
        <form class="space-y-6" onsubmit="enviarFormulario(event)">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <label class="flex flex-col">
                    <p class="text-base font-medium leading-normal pb-2 text-gray-800 dark:text-white">Nombre Completo</p>
                    <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg h-14 p-[15px] text-base font-normal leading-normal" placeholder="Ingresa tu nombre completo" required/>
                </label>
                <label class="flex flex-col">
                    <p class="text-base font-medium leading-normal pb-2 text-gray-800 dark:text-white">Correo Electrónico</p>
                    <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg h-14 p-[15px] text-base font-normal leading-normal" placeholder="Ingresa tu correo electrónico" type="email" required/>
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <label class="flex flex-col">
                    <div class="flex items-center pb-2">
                        <p class="text-base font-medium leading-normal text-gray-800 dark:text-white">Teléfono</p>
                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">(Opcional)</span>
                    </div>
                    <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg h-14 p-[15px] text-base font-normal leading-normal" placeholder="Ingresa tu número de teléfono"/>
                </label>
                <label class="flex flex-col">
                    <p class="text-base font-medium leading-normal pb-2 text-gray-800 dark:text-white">Asunto</p>
                    <div class="relative">
                        <select class="form-input appearance-none flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg h-14 p-[15px] text-base font-normal leading-normal" required>
                            <option value="">Selecciona una razón</option>
                            <option value="general">Consulta General</option>
                            <option value="product">Pregunta sobre Producto</option>
                            <option value="order">Problema con el Pedido</option>
                            <option value="other">Otro</option>
                        </select>
                        <span class="material-symbols-outlined absolute top-1/2 right-4 -translate-y-1/2 text-[#8ea7cc] pointer-events-none">
                            unfold_more
                        </span>
                    </div>
                </label>
            </div>
            <div>
                <label class="flex flex-col">
                    <p class="text-base font-medium leading-normal pb-2 text-gray-800 dark:text-white">Mensaje</p>
                    <textarea class="form-input flex w-full min-w-0 flex-1 resize-y overflow-hidden rounded-lg p-[15px] text-base font-normal leading-normal min-h-[120px]" placeholder="Escribe tu mensaje aquí..." required></textarea>
                </label>
            </div>
            <div>
                <button class="w-full bg-primary text-white font-bold py-4 px-4 rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-4 focus:ring-primary/30 transition-all duration-300 ease-in-out flex items-center justify-center gap-2" type="submit">
                    Enviar
                    <span class="material-symbols-outlined">send</span>
                </button>
            </div>
        </form>
        <div class="hidden mt-8 p-4 rounded-lg bg-teal-500/20 text-teal-500 border border-teal-500/30" id="confirmationMessage">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-secondary">check_circle</span>
                <div>
                    <p class="font-bold">¡Mensaje Enviado!</p>
                    <p>Gracias por contactarnos. Te responderemos en 24-48 horas.</p>
                </div>
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
<script>
function enviarFormulario(event) {
    event.preventDefault();
    
    const confirmationMessage = document.getElementById('confirmationMessage');
    confirmationMessage.classList.remove('hidden');
    
    event.target.reset();
    
    setTimeout(() => {
        confirmationMessage.classList.add('hidden');
    }, 5000);
}
</script>
</body>
</html>