-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-11-2025 a las 00:40:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

CREATE DATABASE IF NOT EXISTS draftosaurus;
USE draftosaurus;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `draftosaurus`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores_local`
--

CREATE TABLE `jugadores_local` (
  `partida_id` int(11) NOT NULL,
  `jugador1` varchar(50) DEFAULT NULL,
  `jugador2` varchar(50) DEFAULT NULL,
  `jugador3` varchar(50) DEFAULT NULL,
  `jugador4` varchar(50) DEFAULT NULL,
  `jugador5` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores_partida`
--

CREATE TABLE `jugadores_partida` (
  `id` int(11) NOT NULL,
  `partida_id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `fecha_union` timestamp NOT NULL DEFAULT current_timestamp(),
  `dado_inicial` tinyint(4) DEFAULT NULL,
  `coloco_en_ronda` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores_partida`
--

INSERT INTO `jugadores_partida` (`id`, `partida_id`, `usuario`, `fecha_union`, `dado_inicial`, `coloco_en_ronda`) VALUES
(40, 55, 'santi_moder', '2025-10-27 11:50:31', NULL, 0),
(41, 55, 'ADMIN', '2025-10-27 11:51:11', NULL, 0),
(42, 56, 'santi_moder', '2025-10-27 12:51:14', NULL, 0),
(43, 56, 'ADMIN', '2025-10-27 12:51:21', NULL, 0),
(44, 57, 'santi_moder', '2025-10-27 12:57:26', NULL, 0),
(45, 57, 'ADMIN', '2025-10-27 12:57:34', NULL, 0),
(46, 58, 'santi_moder', '2025-10-27 13:00:52', NULL, 0),
(47, 58, 'ADMIN', '2025-10-27 13:00:58', NULL, 0),
(48, 59, 'santi_moder', '2025-10-27 13:01:41', NULL, 0),
(49, 59, 'ADMIN', '2025-10-27 13:01:54', NULL, 0),
(50, 60, 'santi_moder', '2025-10-27 13:08:01', NULL, 0),
(51, 60, 'ADMIN', '2025-10-27 13:08:18', NULL, 0),
(52, 61, 'santi_moder', '2025-10-27 13:17:53', 5, 0),
(53, 61, 'ADMIN', '2025-10-27 13:18:11', 5, 0),
(54, 62, 'santi_moder', '2025-10-27 13:21:25', 6, 0),
(55, 62, 'ADMIN', '2025-10-27 13:21:35', 4, 0),
(56, 63, 'santi_moder', '2025-10-27 13:25:20', 6, 0),
(57, 63, 'ADMIN', '2025-10-27 13:25:27', 4, 0),
(58, 64, 'santi_moder', '2025-10-27 13:28:12', 3, 0),
(59, 64, 'ADMIN', '2025-10-27 13:28:24', 1, 0),
(60, 65, 'santi_moder', '2025-10-27 13:38:49', NULL, 0),
(61, 65, 'ADMIN', '2025-10-27 13:38:58', NULL, 0),
(62, 66, 'santi_moder', '2025-10-27 13:40:48', 6, 0),
(63, 66, 'ADMIN', '2025-10-27 13:40:58', 6, 0),
(64, 67, 'santi_moder', '2025-10-27 13:52:07', 4, 0),
(65, 67, 'ADMIN', '2025-10-27 13:52:21', 5, 0),
(66, 68, 'santi_moder', '2025-10-27 14:01:32', 2, 0),
(67, 68, 'ADMIN', '2025-10-27 14:01:44', 2, 0),
(68, 69, 'santi_moder', '2025-10-27 14:39:48', 5, 0),
(69, 69, 'ADMIN', '2025-10-27 14:40:09', 1, 0),
(70, 70, 'santi_moder', '2025-10-27 14:41:37', 6, 0),
(71, 70, 'ADMIN', '2025-10-27 14:41:46', 3, 0),
(72, 71, 'santi_moder', '2025-10-27 14:56:45', NULL, 0),
(73, 71, 'ADMIN', '2025-10-27 14:56:53', NULL, 0),
(74, 72, 'santi_moder', '2025-10-27 15:04:41', 6, 0),
(75, 72, 'ADMIN', '2025-10-27 15:04:46', 6, 0),
(76, 73, 'santi_moder', '2025-10-27 15:18:03', 3, 0),
(77, 73, 'ADMIN', '2025-10-27 15:18:13', 3, 0),
(78, 74, 'santi_moder', '2025-10-27 15:45:42', 6, 0),
(79, 74, 'ADMIN', '2025-10-27 15:45:50', 1, 0),
(80, 75, 'santi_moder', '2025-10-27 15:50:55', 3, 0),
(81, 75, 'ADMIN', '2025-10-27 15:51:02', 1, 0),
(82, 76, 'santi_moder', '2025-10-27 16:55:50', 6, 0),
(83, 76, 'ADMIN', '2025-10-27 16:56:02', 3, 0),
(84, 77, 'santi_moder', '2025-10-27 17:35:41', 6, 0),
(85, 77, 'ADMIN', '2025-10-27 17:35:48', 6, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `id_partida` int(11) NOT NULL,
  `jugador` varchar(50) NOT NULL,
  `color` varchar(20) DEFAULT NULL,
  `zona` varchar(50) DEFAULT NULL,
  `turno` int(11) DEFAULT NULL,
  `puntos` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidas`
--

CREATE TABLE `partidas` (
  `id` int(11) NOT NULL,
  `creador` varchar(50) NOT NULL,
  `jugadores` int(11) NOT NULL,
  `tipo` enum('seguimiento','online') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','en_curso','finalizada') DEFAULT 'pendiente',
  `contraseña` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `dado_zona` varchar(50) DEFAULT NULL,
  `turno_actual` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partidas`
--

INSERT INTO `partidas` (`id`, `creador`, `jugadores`, `tipo`, `fecha_creacion`, `estado`, `contraseña`, `nombre`, `dado_zona`, `turno_actual`) VALUES
(55, 'santi_moder', 5, 'online', '2025-10-27 11:50:31', 'pendiente', '123', 'Prueba1', NULL, NULL),
(56, 'santi_moder', 0, 'online', '2025-10-27 12:51:14', 'en_curso', '123', 'Santiago', NULL, NULL),
(57, 'santi_moder', 5, 'online', '2025-10-27 12:57:26', 'pendiente', '123', 'agua', NULL, NULL),
(58, 'santi_moder', 5, 'online', '2025-10-27 13:00:52', 'pendiente', '123', 'prueba2', NULL, NULL),
(59, 'santi_moder', 2, 'online', '2025-10-27 13:01:41', 'en_curso', '123', 'prueba3', NULL, NULL),
(60, 'santi_moder', 2, 'online', '2025-10-27 13:08:01', 'en_curso', '123', 'prueba4', NULL, NULL),
(61, 'santi_moder', 2, 'online', '2025-10-27 13:17:53', 'en_curso', '123', 'pruebadado', NULL, NULL),
(62, 'santi_moder', 2, 'online', '2025-10-27 13:21:25', 'en_curso', '123', 'pruebaa', NULL, NULL),
(63, 'santi_moder', 2, 'online', '2025-10-27 13:25:20', 'en_curso', '123', 'prueba11', NULL, NULL),
(64, 'santi_moder', 2, 'online', '2025-10-27 13:28:12', 'en_curso', '123', 'prueba12', NULL, NULL),
(65, 'santi_moder', 2, 'online', '2025-10-27 13:38:49', 'en_curso', '123', 'prueba13', NULL, NULL),
(66, 'santi_moder', 2, 'online', '2025-10-27 13:40:48', 'en_curso', '123', 'prueba14', NULL, NULL),
(67, 'santi_moder', 2, 'online', '2025-10-27 13:52:07', 'en_curso', '123', 'prueba15', 'BOSQUE', NULL),
(68, 'santi_moder', 2, 'online', '2025-10-27 14:01:32', 'en_curso', '123', '16', 'NO_TREX', 'santi_moder'),
(69, 'santi_moder', 2, 'online', '2025-10-27 14:39:48', 'en_curso', '123', '17', 'CAFETERIA', 'ADMIN'),
(70, 'santi_moder', 2, 'online', '2025-10-27 14:41:37', 'en_curso', '123', '18', 'BOSQUE', 'santi_moder'),
(71, 'santi_moder', 2, 'online', '2025-10-27 14:56:45', 'en_curso', '123', '19', NULL, NULL),
(72, 'santi_moder', 2, 'online', '2025-10-27 15:04:41', 'en_curso', '123', '20', 'CAFETERIA', 'santi_moder'),
(73, 'santi_moder', 2, 'online', '2025-10-27 15:18:03', 'en_curso', '123', '21', NULL, 'ADMIN'),
(74, 'santi_moder', 2, 'online', '2025-10-27 15:45:42', 'en_curso', '123', '22', 'VACIO', 'ADMIN'),
(75, 'santi_moder', 2, 'online', '2025-10-27 15:50:55', 'en_curso', '123', '23', 'LLANURA', 'santi_moder'),
(76, 'santi_moder', 2, 'online', '2025-10-27 16:55:50', 'en_curso', '123', '24', 'NO_TREX', 'santi_moder'),
(77, 'santi_moder', 2, 'online', '2025-10-27 17:35:41', 'en_curso', '123', '25', NULL, 'santi_moder');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `Nivel` int(3) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `telefono`, `pais`, `usuario`, `password`, `Nivel`) VALUES
(7, 'Santiago', 'Modernell', 'santimodernellsanti@gmail.com', '095656919', 'Uruguay', 'santi_moder', '$2y$10$NeTI4e6nC7otRuKEFLmmYusVb9KWb.raonzGtKhJsGQ4nRpd17aSC', 15),
(8, 'Admin', 'Admin', 'Admin@gmail.com', '000000000', 'Uruguay', 'ADMIN', '$2y$10$3EhnJLnlxk3vMF7ZWoR/l.shjEG5BIVm/WKnSVj3mx38vZnfY3AsC', 1),
(11, NULL, NULL, 'cappuccioproyecto@gmail.com', NULL, NULL, 'Facu', '$2y$10$FIj/ZaFddA1fB9bRvjHlE.0nJYOcqcFUk8rfR7OwIYuTisdbjw1aq', 1),
(12, NULL, NULL, 'cappuccioxx@gmail.com', NULL, NULL, 'Sexo', '$2y$10$VgtG594SHPbtwYvHhXX.P.9TAfnUpqydcx.am1Sl7e0/7UUDTn42C', 1),
(13, NULL, NULL, 'prueba2@gmail.com', NULL, NULL, 'prueba2', '$2y$10$G4dltyKxPJMjVsbxq4RGde1wtVfQb34eJ4VxpkUvmG0Ac0wIUpsm6', 1),
(14, NULL, NULL, 'hola@gmail.com', NULL, NULL, 'Facundo', '$2y$10$5zFHfxgTAjb3TFRR3HF.1OhKbN0xWIUHbgncC.02ewgzfH9z.uBcO', 1),
(15, NULL, NULL, 'hola1@gmail.com', NULL, NULL, 'Mogolico', '$2y$10$Fc1TWq9tnHsFv3HK94XxmO/FdsT3LutXo8e3qZnJFrFnMIbpyQBbi', 1),
(16, NULL, NULL, 'adadasj@gmail.com', NULL, NULL, 'Mielda', '$2y$10$bwvF8DvqjdN1O7vDFANFLOnu2OyEDxgNI93E0StH6YoyxzVRvLjCS', 1),
(17, NULL, NULL, 'hola3@gmail.com', NULL, NULL, 'Hola', '$2y$10$fvQhHk0moZv2UrZ/7/NljOzsmgspZIh.LK1SEQk1Dk23DO3nU3vke', 1),
(18, NULL, NULL, 'putarro@gmail.com', NULL, NULL, 'Putarro', '$2y$10$0wQ/6vvHowbqhh7X9pMt5O5bmCm8.NgbLGZ6cSr3rQsaIdP7COzOK', 1),
(19, NULL, NULL, 'downy@gmail.com', NULL, NULL, 'Downy', '$2y$10$595flCBuZluC4sTLwZz85.jVlUyPOETCzfSm309rc.jx0Y38wLi/C', 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vistas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vistas` (
`id` int(11)
,`id_partida` int(11)
,`jugador` varchar(50)
,`color` varchar(20)
,`zona` varchar(50)
,`turno` int(11)
,`puntos` int(11)
,`fecha` datetime
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vistas`
--
DROP TABLE IF EXISTS `vistas`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `vistas` AS
SELECT id, id_partida, jugador, color, zona, turno, puntos, fecha
FROM movimientos;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `jugadores_local`
--
ALTER TABLE `jugadores_local`
  ADD PRIMARY KEY (`partida_id`);

--
-- Indices de la tabla `jugadores_partida`
--
ALTER TABLE `jugadores_partida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partida_id` (`partida_id`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `jugadores_partida`
--
ALTER TABLE `jugadores_partida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `partidas`
--
ALTER TABLE `partidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `jugadores_local`
--
ALTER TABLE `jugadores_local`
  ADD CONSTRAINT `jugadores_local_ibfk_1` FOREIGN KEY (`partida_id`) REFERENCES `partidas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `jugadores_partida`
--
ALTER TABLE `jugadores_partida`
  ADD CONSTRAINT `jugadores_partida_ibfk_1` FOREIGN KEY (`partida_id`) REFERENCES `partidas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
