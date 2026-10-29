-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-10-2025 a las 04:18:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `almoy_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_agregar_stock` (IN `p_id_producto` INT, IN `p_cantidad` INT, IN `p_motivo` VARCHAR(200))   BEGIN
    DECLARE v_stock_anterior INT;
    DECLARE v_stock_nuevo INT;
    
    -- Obtener stock actual
    SELECT stock_actual INTO v_stock_anterior FROM productos WHERE id_producto = p_id_producto;
    
    -- Calcular nuevo stock
    SET v_stock_nuevo = v_stock_anterior + p_cantidad;
    
    -- Actualizar stock
    UPDATE productos SET stock_actual = v_stock_nuevo WHERE id_producto = p_id_producto;
    
    -- Registrar movimiento
    INSERT INTO movimientos_stock (id_producto, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo)
    VALUES (p_id_producto, 'entrada', p_cantidad, v_stock_anterior, v_stock_nuevo, p_motivo);
    
    SELECT v_stock_nuevo AS stock_actual, 'Stock agregado correctamente' AS mensaje;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_consultar_productos` (IN `p_id_producto` INT, IN `p_id_categoria` INT, IN `p_nombre` VARCHAR(200), IN `p_estado` VARCHAR(20))   BEGIN
    SELECT 
        p.id_producto,
        p.nombre,
        p.descripcion,
        p.precio,
        c.nombre AS categoria,
        p.imagen_url,
        p.stock_actual,
        p.stock_minimo,
        p.estado,
        p.fecha_creacion,
        p.fecha_actualizacion,
        CASE 
            WHEN p.stock_actual <= p.stock_minimo THEN 'BAJO STOCK'
            WHEN p.stock_actual = 0 THEN 'SIN STOCK'
            ELSE 'STOCK OK'
        END AS alerta_stock
    FROM productos p
    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE 
        (p_id_producto IS NULL OR p.id_producto = p_id_producto)
        AND (p_id_categoria IS NULL OR p.id_categoria = p_id_categoria)
        AND (p_nombre IS NULL OR p.nombre LIKE CONCAT('%', p_nombre, '%'))
        AND (p_estado IS NULL OR p.estado = p_estado)
    ORDER BY p.fecha_actualizacion DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_producto` (IN `p_id_producto` INT, IN `p_eliminar_fisico` BOOLEAN)   BEGIN
    IF p_eliminar_fisico THEN
        -- Eliminar físicamente
        DELETE FROM productos WHERE id_producto = p_id_producto;
        SELECT 'Producto eliminado físicamente' AS mensaje;
    ELSE
        -- Baja lógica
        UPDATE productos SET estado = 'descontinuado' WHERE id_producto = p_id_producto;
        SELECT 'Producto dado de baja (descontinuado)' AS mensaje;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_historial_movimientos` (IN `p_id_producto` INT, IN `p_fecha_desde` DATE, IN `p_fecha_hasta` DATE)   BEGIN
    SELECT 
        m.id_movimiento,
        p.nombre AS producto,
        m.tipo_movimiento,
        m.cantidad,
        m.stock_anterior,
        m.stock_nuevo,
        m.motivo,
        m.fecha_movimiento
    FROM movimientos_stock m
    INNER JOIN productos p ON m.id_producto = p.id_producto
    WHERE 
        (p_id_producto IS NULL OR m.id_producto = p_id_producto)
        AND (p_fecha_desde IS NULL OR DATE(m.fecha_movimiento) >= p_fecha_desde)
        AND (p_fecha_hasta IS NULL OR DATE(m.fecha_movimiento) <= p_fecha_hasta)
    ORDER BY m.fecha_movimiento DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_producto` (IN `p_nombre` VARCHAR(200), IN `p_descripcion` TEXT, IN `p_precio` DECIMAL(10,2), IN `p_id_categoria` INT, IN `p_imagen_url` VARCHAR(500), IN `p_stock_inicial` INT, IN `p_stock_minimo` INT)   BEGIN
    DECLARE v_id_producto INT;
    
    -- Insertar el producto
    INSERT INTO productos (nombre, descripcion, precio, id_categoria, imagen_url, stock_actual, stock_minimo)
    VALUES (p_nombre, p_descripcion, p_precio, p_id_categoria, p_imagen_url, p_stock_inicial, p_stock_minimo);
    
    SET v_id_producto = LAST_INSERT_ID();
    
    -- Registrar movimiento de stock inicial
    IF p_stock_inicial > 0 THEN
        INSERT INTO movimientos_stock (id_producto, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo)
        VALUES (v_id_producto, 'entrada', p_stock_inicial, 0, p_stock_inicial, 'Stock inicial');
    END IF;
    
    SELECT v_id_producto AS id_producto, 'Producto insertado correctamente' AS mensaje;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_modificar_producto` (IN `p_id_producto` INT, IN `p_nombre` VARCHAR(200), IN `p_descripcion` TEXT, IN `p_precio` DECIMAL(10,2), IN `p_id_categoria` INT, IN `p_imagen_url` VARCHAR(500), IN `p_stock_minimo` INT, IN `p_estado` VARCHAR(20))   BEGIN
    UPDATE productos 
    SET 
        nombre = COALESCE(p_nombre, nombre),
        descripcion = COALESCE(p_descripcion, descripcion),
        precio = COALESCE(p_precio, precio),
        id_categoria = COALESCE(p_id_categoria, id_categoria),
        imagen_url = COALESCE(p_imagen_url, imagen_url),
        stock_minimo = COALESCE(p_stock_minimo, stock_minimo),
        estado = COALESCE(p_estado, estado)
    WHERE id_producto = p_id_producto;
    
    SELECT ROW_COUNT() AS filas_afectadas, 'Producto modificado correctamente' AS mensaje;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_productos_bajo_stock` ()   BEGIN
    SELECT 
        p.id_producto,
        p.nombre,
        p.stock_actual,
        p.stock_minimo,
        c.nombre AS categoria,
        (p.stock_minimo - p.stock_actual) AS unidades_faltantes
    FROM productos p
    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE p.stock_actual <= p.stock_minimo
    AND p.estado = 'activo'
    ORDER BY p.stock_actual ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reducir_stock` (IN `p_id_producto` INT, IN `p_cantidad` INT, IN `p_motivo` VARCHAR(200))   BEGIN
    DECLARE v_stock_anterior INT;
    DECLARE v_stock_nuevo INT;
    
    -- Obtener stock actual
    SELECT stock_actual INTO v_stock_anterior FROM productos WHERE id_producto = p_id_producto;
    
    -- Verificar que hay suficiente stock
    IF v_stock_anterior < p_cantidad THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente';
    END IF;
    
    -- Calcular nuevo stock
    SET v_stock_nuevo = v_stock_anterior - p_cantidad;
    
    -- Actualizar stock
    UPDATE productos SET stock_actual = v_stock_nuevo WHERE id_producto = p_id_producto;
    
    -- Registrar movimiento
    INSERT INTO movimientos_stock (id_producto, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo)
    VALUES (p_id_producto, 'salida', p_cantidad, v_stock_anterior, v_stock_nuevo, p_motivo);
    
    SELECT v_stock_nuevo AS stock_actual, 'Stock reducido correctamente' AS mensaje;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Smartphones', 'Teléfonos inteligentes de última generación', '2025-10-15 19:07:44', '2025-10-15 19:07:44'),
(2, 'Audio', 'Auriculares, parlantes y accesorios de audio', '2025-10-15 19:07:44', '2025-10-15 19:07:44'),
(3, 'Relojes', 'Smartwatches y relojes inteligentes', '2025-10-15 19:07:44', '2025-10-15 19:07:44'),
(4, 'Accesorios Móviles', 'Fundas, cargadores y accesorios para celulares', '2025-10-15 19:07:44', '2025-10-15 19:07:44'),
(5, 'Gaming', 'Periféricos y accesorios para gaming', '2025-10-15 19:07:44', '2025-10-15 19:07:44'),
(6, 'Electrónica', 'Productos electrónicos diversos', '2025-10-15 19:07:44', '2025-10-15 19:07:44'),
(7, 'Hogar', 'Artículos para el hogar, casuales', '2025-10-22 22:52:26', '2025-10-22 22:52:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal_producto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id_detalle`, `id_venta`, `id_producto`, `nombre_producto`, `cantidad`, `precio_unitario`, `subtotal_producto`) VALUES
(12, 8, 2, 'auris', 1, 426.00, 426.00),
(13, 9, 1, 'telefono', 1, 2352.00, 2352.00),
(14, 10, 3, 'dron', 1, 2556.00, 2556.00),
(15, 11, 4, 'reloj chino', 1, 342.00, 342.00),
(16, 12, 5, 'cargador', 1, 626.00, 626.00),
(17, 13, 6, 'PC gamer', 1, 62436.00, 62436.00),
(18, 14, 1, 'telefono', 1, 2352.00, 2352.00),
(19, 15, 1, 'telefono', 1, 2352.00, 2352.00),
(20, 16, 5, 'cargador', 1, 626.00, 626.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

CREATE TABLE `stock` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `id_categoria` int(11) NOT NULL,
  `imagen_url` varchar(500) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `estado` enum('activo','inactivo','descontinuado') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `stock`
--

INSERT INTO `stock` (`id_producto`, `nombre`, `descripcion`, `precio`, `id_categoria`, `imagen_url`, `stock`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'telefono', 'telefono', 2352.00, 1, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSMILJRHoKgYrqRpLPhe8738HpRW1twyX430Q&s', 0, 'activo', '2025-10-22 22:58:57', '2025-10-23 21:11:35'),
(2, 'auris', 'auriculares', 426.00, 2, 'https://http2.mlstatic.com/D_NQ_NP_604401-MLU78460149264_082024-O.webp', 10, 'activo', '2025-10-22 22:59:28', '2025-10-23 20:41:01'),
(3, 'dron', 'dron', 2556.00, 6, 'https://http2.mlstatic.com/D_NQ_NP_835455-MLA86674128067_062025-O.webp', 4, 'activo', '2025-10-22 23:00:17', '2025-10-23 21:11:04'),
(4, 'reloj chino', ' chino', 342.00, 3, 'https://images.fravega.com/f500/dbb3c5e7424e3281d3f382b593690a3a.png', 14, 'activo', '2025-10-22 23:01:12', '2025-10-23 20:37:46'),
(5, 'cargador', 'carga', 626.00, 4, 'https://lh5.googleusercontent.com/proxy/tsXmh0mxaqZ-aFiPM81hXyBAlucYBG7ERWBahZv4Z50fvRK_9Hl9iPGqMRzNqzW86Y6JwYXuJ2ZK8gMnPHNSKucHa4MSf3TOzHGQRanyLLo_6SJ8ueYWqQuIZlN97EI', 11, 'activo', '2025-10-22 23:01:51', '2025-10-23 21:11:16'),
(6, 'PC gamer', 'como un pc pero gamer', 62436.00, 5, 'https://eventop.org/wp-content/uploads/gama-pro-pc.png', 3, 'activo', '2025-10-22 23:02:39', '2025-10-23 20:37:59'),
(7, 'lavarropa', ' lava ropa', 3556.00, 7, 'https://whirlpoolarg.vtexassets.com/arquivos/ids/164510-800-auto?v=638769644056570000&width=800&height=auto&aspect=true', 0, 'activo', '2025-10-22 23:08:05', '2025-10-23 14:21:25');

--
-- Disparadores `stock`
--
DELIMITER $$
CREATE TRIGGER `stock_actualizacion` BEFORE UPDATE ON `stock` FOR EACH ROW BEGIN
    SET NEW.fecha_actualizacion = NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `password`, `fecha_registro`) VALUES
(13, 'Leonardo Moy', 'Leonardo123@gmail.com', '$2y$10$SkEk52U4.qBaIc4DG8Ac.eCuI.AQguDTuXgqLUSTKqgR8XO6YUWwu', '2025-10-21 23:23:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `fecha_venta` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `interes` decimal(10,2) DEFAULT 0.00,
  `metodo_pago` varchar(50) NOT NULL,
  `estado` varchar(20) DEFAULT 'completada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `fecha_venta`, `total`, `subtotal`, `interes`, `metodo_pago`, `estado`) VALUES
(8, '2025-10-23 17:37:19', 426.00, 426.00, 0.00, 'Efectivo', 'completada'),
(9, '2025-10-23 17:37:29', 2352.00, 2352.00, 0.00, 'Efectivo', 'completada'),
(10, '2025-10-23 17:37:38', 2556.00, 2556.00, 0.00, 'Efectivo', 'completada'),
(11, '2025-10-23 17:37:46', 342.00, 342.00, 0.00, 'Efectivo', 'completada'),
(12, '2025-10-23 17:37:52', 626.00, 626.00, 0.00, 'Efectivo', 'completada'),
(13, '2025-10-23 17:37:59', 62436.00, 62436.00, 0.00, 'Efectivo', 'completada'),
(14, '2025-10-23 17:38:33', 2352.00, 2352.00, 0.00, 'Efectivo', 'completada'),
(15, '2025-10-23 17:55:44', 2352.00, 2352.00, 0.00, 'Efectivo', 'completada'),
(16, '2025-10-23 17:55:51', 626.00, 626.00, 0.00, 'Efectivo', 'completada');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_alertas_stock`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_alertas_stock` (
`id_producto` int(11)
,`producto` varchar(200)
,`categoria` varchar(100)
,`stock` int(11)
,`prioridad` varchar(19)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_catalogo_productos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_catalogo_productos` (
`id_producto` int(11)
,`nombre` varchar(200)
,`descripcion` text
,`precio` decimal(10,2)
,`categoria` varchar(100)
,`imagen_url` varchar(500)
,`stock` int(11)
,`disponibilidad` varchar(16)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_alertas_stock`
--
DROP TABLE IF EXISTS `v_alertas_stock`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_alertas_stock`  AS SELECT `s`.`id_producto` AS `id_producto`, `s`.`nombre` AS `producto`, `c`.`nombre` AS `categoria`, `s`.`stock` AS `stock`, CASE WHEN `s`.`stock` = 0 THEN 'CRÍTICO - SIN STOCK' WHEN `s`.`stock` <= 5 THEN 'URGENTE' ELSE 'OK' END AS `prioridad` FROM (`stock` `s` join `categorias` `c` on(`s`.`id_categoria` = `c`.`id_categoria`)) WHERE `s`.`estado` = 'activo' ORDER BY `s`.`stock` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_catalogo_productos`
--
DROP TABLE IF EXISTS `v_catalogo_productos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_catalogo_productos`  AS SELECT `s`.`id_producto` AS `id_producto`, `s`.`nombre` AS `nombre`, `s`.`descripcion` AS `descripcion`, `s`.`precio` AS `precio`, `c`.`nombre` AS `categoria`, `s`.`imagen_url` AS `imagen_url`, `s`.`stock` AS `stock`, CASE WHEN `s`.`stock` > 5 THEN 'Disponible' WHEN `s`.`stock` > 0 THEN 'Últimas unidades' ELSE 'Sin stock' END AS `disponibilidad` FROM (`stock` `s` join `categorias` `c` on(`s`.`id_categoria` = `c`.`id_categoria`)) WHERE `s`.`estado` = 'activo' ORDER BY `c`.`nombre` ASC, `s`.`nombre` ASC ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_categoria` (`id_categoria`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `stock`
--
ALTER TABLE `stock`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `stock` (`id_producto`);

--
-- Filtros para la tabla `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
