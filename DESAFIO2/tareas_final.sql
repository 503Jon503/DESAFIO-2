-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-04-2026 a las 07:47:06
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
-- Base de datos: `tareas_final`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'profesor'),
(2, 'estudiante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_limite` datetime DEFAULT NULL,
  `profesor_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id`, `titulo`, `descripcion`, `fecha_limite`, `profesor_id`, `imagen`) VALUES
(1, 'HOLA', 'MAÑANA', '2026-04-16 19:15:00', 1, NULL),
(2, 'EVA 2', 'VIDEO DE 10 MIN', '2026-04-16 23:59:00', 1, NULL),
(3, 'POR FAVOR', 'FUNCIONAAAAAAAAAA', '2026-04-17 11:58:00', 1, NULL),
(4, 'PRUEBA FINAL', 'PARA MAÑANA', '2026-04-16 11:11:00', 1, 'tarea_69e0743b21e343.41433479.jpg'),
(5, 'TALLER 3', 'CORDENADAS POLARES', '2026-04-17 12:55:00', 1, 'tarea_69e076001afb59.07314396.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas_estudiantes`
--

CREATE TABLE `tareas_estudiantes` (
  `id` int(11) NOT NULL,
  `tarea_id` int(11) DEFAULT NULL,
  `estudiante_id` int(11) DEFAULT NULL,
  `estado_id` int(11) DEFAULT 1,
  `entrega` text DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `fecha_entrega_real` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas_estudiantes`
--

INSERT INTO `tareas_estudiantes` (`id`, `tarea_id`, `estudiante_id`, `estado_id`, `entrega`, `archivo`, `fecha_entrega_real`) VALUES
(1, 1, 2, 2, NULL, NULL, NULL),
(2, 1, 3, 1, 'JE', NULL, '2026-04-15 23:15:59'),
(3, 1, 4, 1, NULL, NULL, NULL),
(4, 1, 5, 2, NULL, NULL, NULL),
(5, 1, 6, 1, NULL, NULL, NULL),
(6, 2, 2, 2, NULL, NULL, NULL),
(7, 2, 3, 1, NULL, NULL, NULL),
(8, 2, 4, 1, NULL, NULL, NULL),
(9, 2, 5, 1, NULL, NULL, NULL),
(10, 2, 6, 1, NULL, NULL, NULL),
(11, 3, 2, 1, 'POR FAVOR DIOS', 'entrega_69e0700458f010.65582978.pdf', '2026-04-15 23:13:40'),
(12, 3, 3, 1, 'JE', NULL, '2026-04-15 23:16:06'),
(13, 3, 4, 1, NULL, NULL, NULL),
(14, 3, 5, 1, 'ME ESCUHAS?', NULL, '2026-04-15 22:57:48'),
(15, 3, 6, 1, NULL, NULL, NULL),
(16, 4, 2, 2, 'FUNCIONA', 'entrega_69e0749fd7dd47.27836273.pdf', '2026-04-15 23:33:19'),
(17, 4, 3, 1, NULL, NULL, NULL),
(18, 4, 4, 1, NULL, NULL, NULL),
(19, 4, 5, 1, NULL, NULL, NULL),
(20, 4, 6, 1, NULL, NULL, NULL),
(21, 5, 2, 1, NULL, NULL, NULL),
(22, 5, 3, 1, NULL, NULL, NULL),
(23, 5, 4, 1, NULL, NULL, NULL),
(24, 5, 5, 1, NULL, NULL, NULL),
(25, 5, 6, 2, 'aja', 'entrega_69e076ade4ea86.90116814.jpg', '2026-04-15 23:42:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `carnet` varchar(20) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `rol_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `carnet`, `password`, `rol_id`) VALUES
(1, 'Profesor General', 'PROF-001', 'PROFESOR123', 1),
(2, 'Jonathan Isaías Rosales Elías', 'RE253008', 'RE253008', 2),
(3, 'Carlos Isaac Reyes Hernández', 'RH230274', 'RH230274', 2),
(4, 'Carlos Roberto Luna Diaz', 'LD252724', 'LD252724', 2),
(5, 'Marcelo Augusto Zelaya Colocho', 'ZC253009', 'ZC253009', 2),
(6, 'Manuel de Jesús Hernández Escamilla', 'HE252995', 'HE252995', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indices de la tabla `tareas_estudiantes`
--
ALTER TABLE `tareas_estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarea_id` (`tarea_id`),
  ADD KEY `estudiante_id` (`estudiante_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `carnet` (`carnet`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tareas_estudiantes`
--
ALTER TABLE `tareas_estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tareas_estudiantes`
--
ALTER TABLE `tareas_estudiantes`
  ADD CONSTRAINT `tareas_estudiantes_ibfk_1` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`),
  ADD CONSTRAINT `tareas_estudiantes_ibfk_2` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
