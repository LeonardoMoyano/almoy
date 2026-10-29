class CarritoManager {
    constructor() {
        this.items = this.cargarCarrito();
        this.actualizarContadorCarrito();
    }

    cargarCarrito() {
        const carritoGuardado = localStorage.getItem('almoy_carrito');
        return carritoGuardado ? JSON.parse(carritoGuardado) : [];
    }

    guardarCarrito() {
        localStorage.setItem('almoy_carrito', JSON.stringify(this.items));
        this.actualizarContadorCarrito();
    }

    agregarProducto(producto) {
        const itemExistente = this.items.find(item => item.id === producto.id);
        
        if (itemExistente) {
            itemExistente.cantidad++;
        } else {
            this.items.push({
                id: producto.id,
                nombre: producto.nombre,
                precio: producto.precio,
                imagen: producto.imagen,
                cantidad: 1
            });
        }
        
        this.guardarCarrito();
        this.mostrarNotificacion(`${producto.nombre} agregado al carrito`);
    }

    actualizarCantidad(id, nuevaCantidad) {
        const item = this.items.find(i => i.id === id);
        if (item) {
            item.cantidad = nuevaCantidad;
            this.guardarCarrito();
        }
    }

    eliminarProducto(id) {
        this.items = this.items.filter(item => item.id !== id);
        this.guardarCarrito();
    }

    obtenerTotal() {
        return this.items.reduce((total, item) => total + (item.precio * item.cantidad), 0);
    }

    obtenerCantidadTotal() {
        return this.items.reduce((total, item) => total + item.cantidad, 0);
    }

    vaciarCarrito() {
        this.items = [];
        this.guardarCarrito();
    }

    actualizarContadorCarrito() {
        const contador = this.obtenerCantidadTotal();
        const btnCarrito = document.querySelectorAll('button[onclick*="abrirCarrito"]');
        
        btnCarrito.forEach(btn => {
            let badge = btn.querySelector('.carrito-badge');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'carrito-badge';
                badge.style.cssText = `
                    position: absolute;
                    top: -5px;
                    right: -5px;
                    background-color: #22d3ee;
                    color: #000;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 11px;
                    font-weight: bold;
                `;
                btn.style.position = 'relative';
                btn.appendChild(badge);
            }
            
            if (contador > 0) {
                badge.textContent = contador;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    mostrarNotificacion(mensaje) {
        const notificacion = document.createElement('div');
        notificacion.className = 'carrito-notificacion';
        notificacion.textContent = mensaje;
        notificacion.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #22d3ee;
            color: #000;
            padding: 15px 25px;
            border-radius: 8px;
            font-weight: bold;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        `;
        
        document.body.appendChild(notificacion);
        
        setTimeout(() => {
            notificacion.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notificacion.remove(), 300);
        }, 2000);
    }
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

window.carrito = new CarritoManager();

function abrirCarrito() {
    window.location.href = 'carrito.html';
}

function cerrarCarrito() {
    const overlay = document.querySelector('.carrito-overlay');
    const panel = document.querySelector('.carrito-panel');
    
    if (overlay && panel) {
        panel.style.animation = 'slideOut 0.3s ease-out';
        overlay.style.animation = 'fadeOut 0.3s ease-out';
        
        setTimeout(() => {
            overlay.remove();
            panel.remove();
        }, 300);
    }
}