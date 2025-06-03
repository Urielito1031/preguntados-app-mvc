-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-06-2025 a las 23:31:38
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
-- Base de datos: `db_preguntados`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `color` varchar(7) NOT NULL,
  `url_imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `descripcion`, `color`, `url_imagen`) VALUES
(1, 'Historia', '#f6e047', NULL),
(2, 'Deportes', '#f49533', NULL),
(3, 'Arte', '#ea2a33', NULL),
(4, 'Ciencia', '#45ca6d', NULL),
(5, 'Geografía', '#3a77c5', NULL),
(6, 'Entretenimiento', '#ec4bad', NULL),
(7, 'Aleatorio', '#8a41b7', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudad`
--

CREATE TABLE `ciudad` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_pais` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciudad`
--

INSERT INTO `ciudad` (`id`, `nombre`, `id_pais`) VALUES
(1, 'Ciudad Madero', 1),
(2, 'Quito', 2),
(3, 'New York', 3),
(4, 'Puente Alto', 4),
(5, 'Manchester', 5),
(6, '', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nivel`
--

CREATE TABLE `nivel` (
  `id_nivel` int(11) NOT NULL,
  `nombre_nivel` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `nivel`
--

INSERT INTO `nivel` (`id_nivel`, `nombre_nivel`) VALUES
(1, 'Aspirante'),
(2, 'Novato'),
(3, 'Estrella'),
(4, 'Heroe'),
(5, 'Legendario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pais`
--

CREATE TABLE `pais` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pais`
--

INSERT INTO `pais` (`id`, `nombre`) VALUES
(1, 'Argentina'),
(2, 'Peru'),
(3, 'Estados Unidos'),
(4, 'Chile'),
(5, 'Inglaterra'),
(6, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partida`
--

CREATE TABLE `partida` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `puntaje` int(11) DEFAULT 0,
  `estado` enum('GANADA','PERDIDA') DEFAULT NULL,
  `preguntas_correctas` int(11) DEFAULT 0,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE `pregunta` (
  `id` int(11) NOT NULL,
  `respuesta_correcta` varchar(255) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `enunciado` varchar(255) NOT NULL,
  `cantidad_jugada` int(11) DEFAULT 0,
  `cantidad_aciertos` int(11) DEFAULT 0,
  `cantidad_reportes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`id`, `respuesta_correcta`, `id_categoria`, `enunciado`, `cantidad_jugada`, `cantidad_aciertos`, `cantidad_reportes`) VALUES
(1, 'George Washington', 1, '¿Quién fue el primer presidente de los Estados Unidos?', 0, 0, 0),
(2, '1939', 1, '¿En qué año comenzó la Segunda Guerra Mundial?', 0, 0, 0),
(3, 'Egipcia', 1, '¿Qué civilización construyó las pirámides de Giza?', 0, 0, 0),
(4, 'Fútbol', 2, '¿Cuál es el deporte más popular del mundo?', 0, 0, 0),
(5, 'Michael Jordan', 2, '¿Quién es considerado el mejor jugador de baloncesto de la historia?', 0, 0, 0),
(6, 'Wimbledon', 2, '¿Qué torneo de tenis se juega en hierba?', 0, 0, 0),
(7, 'Leonardo da Vinci', 3, '¿Quién pintó la Mona Lisa?', 0, 0, 0),
(8, 'Impresionismo', 3, '¿Qué movimiento artístico está asociado con Claude Monet?', 0, 0, 0),
(9, 'La Noche Estrellada', 3, '¿Cuál es el nombre de la obra famosa de Vincent van Gogh?', 0, 0, 0),
(10, 'Isaac Newton', 4, '¿Quién formuló las leyes del movimiento?', 0, 0, 0),
(11, 'Oxígeno', 4, '¿Qué elemento es el más abundante en la atmósfera terrestre?', 0, 0, 0),
(12, 'ADN', 4, '¿Qué molécula porta la información genética?', 0, 0, 0),
(13, 'París', 5, '¿Cuál es la capital de Francia?', 0, 0, 0),
(14, 'Amazonas', 5, '¿Qué río es el más largo de Sudamérica?', 0, 0, 0),
(15, 'África', 5, '¿En qué continente está Egipto?', 0, 0, 0),
(16, 'Leonardo DiCaprio', 6, '¿Qué actor ganó un Oscar por \"El Renacido\"?', 0, 0, 0),
(17, 'The Beatles', 6, '¿Qué banda escribió \"Hey Jude\"?', 0, 0, 0),
(18, 'Harry Potter', 6, '¿Qué saga incluye a un personaje llamado Harry Potter?', 0, 0, 0),
(21, '1989', 1, '¿En qué año cayó el Muro de Berlín?', 0, 0, 0),
(22, 'Vladimir Lenin', 1, '¿Quién fue el líder de la Revolución Rusa de 1917?', 0, 0, 0),
(23, 'Vespasiano', 1, '¿Qué emperador romano construyó el Coliseo?', 0, 0, 0),
(24, 'Béisbol', 2, '¿En qué deporte se utiliza un bate y una pelota?', 0, 0, 0),
(25, 'Brasil', 2, '¿Qué país ha ganado más Copas Mundiales de Fútbol?', 0, 0, 0),
(26, 'Kareem Abdul-Jabbar', 2, '¿Qué jugador de la NBA combinó su dominio con el \"skyhook\" y un récord de 6 MVP?', 0, 0, 0),
(27, 'Leonardo da Vinci', 3, '¿Quién pintó \"La última cena\"?', 0, 0, 0),
(28, 'Andy Warhol', 3, '¿Qué artista es conocido por sus pinturas de latas de sopa Campbell?', 0, 0, 0),
(29, 'Frank Gehry', 3, '¿Qué arquitecto diseñó el Museo Guggenheim de Bilbao?', 0, 0, 0),
(30, 'Marte', 4, '¿Qué planeta es conocido como el planeta rojo?', 0, 0, 0),
(31, 'Albert Einstein', 4, '¿Qué científico propuso la teoría de la relatividad?', 0, 0, 0),
(32, 'Protón', 4, '¿Qué partícula subatómica tiene carga positiva?', 0, 0, 0),
(33, 'Rusia', 5, '¿Cuál es el país más grande del mundo por área?', 0, 0, 0),
(34, 'Monte Everest', 5, '¿Qué montaña es la más alta del mundo?', 0, 0, 0),
(35, 'Francia', 5, '¿Qué país tiene la mayor cantidad de husos horarios?', 0, 0, 0),
(36, 'Robert Downey Jr.', 6, '¿Qué actor interpretó a Iron Man en el Universo Cinematográfico de Marvel?', 0, 0, 0),
(37, 'Game of Thrones', 6, '¿Qué serie de TV es conocida por el lema \"Winter is Coming\"?', 0, 0, 0),
(38, 'Quentin Tarantino', 6, '¿Qué director de cine es conocido por películas como \"Pulp Fiction\" y \"Kill Bill\"?', 0, 0, 0),
(39, 'Alberto Fernánez', 1, '¿Qué presidente argentino organizó una fiesta en la quinta de Olivos?', 0, 0, 0),
(40, 'Bolivia', 1, '¿Qué país sudamericano fue el último en independizarse de España?', 0, 0, 0),
(41, 'India', 2, '¿Qué país inventó el bádminton?', 0, 0, 0),
(42, 'Cachalote', 4, '¿Qué animal tiene el cerebro más grande en proporción a su cuerpo?', 0, 0, 0),
(43, 'Neptuno', 4, '¿Qué planeta del sistema solar tiene vientos más rápidos (2,100 km/h)?', 0, 0, 0),
(44, 'Luis Federico Leloir', 4, '¿Qué científico argentino ganó el Nobel por descubrir cómo las células usan el azúcar?', 0, 0, 0),
(45, 'Bolivia', 5, '¿Qué país tiene la capital más alta del mundo?', 0, 0, 0),
(46, 'Jujuy', 5, '¿Qué provincia Argentina tiene frontera con Chile y Bolivia?', 0, 0, 0),
(47, 'Factory Method', 7, '¿Qué patrón de diseño sugiere crear objetos sin exponer la lógica de creación?', 0, 0, 0),
(48, 'Principio de Responsabilidad Única', 7, '¿Qué principio SOLID indica que una clase debe tener una única responsabilidad?', 0, 0, 0),
(49, 'docker build', 7, '¿Qué comando de Docker se usa para construir una imagen desde un Dockerfile?', 0, 0, 0),
(50, 'HTTPS', 4, '¿Qué protocolo asegura la comunicación encriptada en la web?', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_incorrecta`
--

CREATE TABLE `respuesta_incorrecta` (
  `id` int(11) NOT NULL,
  `respuesta` varchar(255) NOT NULL,
  `id_pregunta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta_incorrecta`
--

INSERT INTO `respuesta_incorrecta` (`id`, `respuesta`, `id_pregunta`) VALUES
(1, 'Thomas Jefferson', 1),
(2, 'Abraham Lincoln', 1),
(3, 'John Adams', 1),
(4, '1941', 2),
(5, '1914', 2),
(6, '1945', 2),
(7, 'Maya', 3),
(8, 'Azteca', 3),
(9, 'Inca', 3),
(10, 'Baloncesto', 4),
(11, 'Tenis', 4),
(12, 'Golf', 4),
(13, 'LeBron James', 5),
(14, 'Kobe Bryant', 5),
(15, 'Magic Johnson', 5),
(16, 'Roland Garros', 6),
(17, 'US Open', 6),
(18, 'Australian Open', 6),
(19, 'Michelangelo', 7),
(20, 'Raphael', 7),
(21, 'Donatello', 7),
(22, 'Cubismo', 8),
(23, 'Surrealismo', 8),
(24, 'Expresionismo', 8),
(25, 'La Persistencia de la Memoria', 9),
(26, 'El Grito', 9),
(27, 'Guernica', 9),
(28, 'Albert Einstein', 10),
(29, 'Galileo Galilei', 10),
(30, 'Nikola Tesla', 10),
(31, 'Hidrógeno', 11),
(32, 'Carbono', 11),
(33, 'Nitrógeno', 11),
(34, 'ARN', 12),
(35, 'Proteína', 12),
(36, 'Enzima', 12),
(37, 'Londres', 13),
(38, 'Berlín', 13),
(39, 'Madrid', 13),
(40, 'Nilo', 14),
(41, 'Misisipi', 14),
(42, 'Yangtsé', 14),
(43, 'Asia', 15),
(44, 'Europa', 15),
(45, 'América', 15),
(46, 'Brad Pitt', 16),
(47, 'Tom Cruise', 16),
(48, 'Johnny Depp', 16),
(49, 'Rolling Stones', 17),
(50, 'Led Zeppelin', 17),
(51, 'Queen', 17),
(52, 'El Señor de los Anillos', 18),
(53, 'Star Wars', 18),
(54, 'Juego de Tronos', 18),
(60, '1975', 21),
(61, '1991', 21),
(62, '1961', 21),
(63, 'Joseph Stalin', 22),
(64, 'Leon Trotsky', 22),
(65, 'Mikhail Gorbachev', 22),
(66, 'Julio César', 23),
(67, 'Nerón', 23),
(68, 'Augusto', 23),
(69, 'Fútbol', 24),
(70, 'Baloncesto', 24),
(71, 'Tenis', 24),
(72, 'Alemania', 25),
(73, 'Italia', 25),
(74, 'Argentina', 25),
(75, 'LeBron James', 26),
(76, 'Michael Jordan', 26),
(77, 'Kobe Bryant', 26),
(78, 'Miguel Ángel', 27),
(79, 'Pablo Picasso', 27),
(80, 'Vincent van Gogh', 27),
(81, 'Salvador Dalí', 28),
(82, 'Jackson Pollock', 28),
(83, 'Roy Lichtenstein', 28),
(84, 'Zaha Hadid', 29),
(85, 'I.M. Pei', 29),
(86, 'Santiago Calatrava', 29),
(87, 'Venus', 30),
(88, 'Júpiter', 30),
(89, 'Saturno', 30),
(90, 'Isaac Newton', 31),
(91, 'Stephen Hawking', 31),
(92, 'Galileo Galilei', 31),
(93, 'Electrón', 32),
(94, 'Neutrón', 32),
(95, 'Positrón', 32),
(96, 'Canadá', 33),
(97, 'China', 33),
(98, 'Estados Unidos', 33),
(99, 'K2', 34),
(100, 'Kangchenjunga', 34),
(101, 'Makalu', 34),
(102, 'Rusia', 35),
(103, 'Estados Unidos', 35),
(104, 'Reino Unido', 35),
(105, 'Chris Evans', 36),
(106, 'Chris Hemsworth', 36),
(107, 'Mark Ruffalo', 36),
(108, 'The Walking Dead', 37),
(109, 'Stranger Things', 37),
(110, 'Breaking Bad', 37),
(111, 'Martin Scorsese', 38),
(112, 'Steven Spielberg', 38),
(113, 'Christopher Nolan', 38),
(114, 'Hipólito Yrigoyen', 39),
(115, 'Arturo Frondizi', 39),
(116, 'Raúl Alfonsín', 39),
(117, 'Argentina', 40),
(118, 'Colombia', 40),
(119, 'Venezuela', 40),
(120, 'China', 41),
(121, 'Inglaterra', 41),
(122, 'Japón', 41),
(123, 'Delfín', 42),
(124, 'Elefante', 42),
(125, 'Chimpancé', 42),
(126, 'Júpiter', 43),
(127, 'Saturno', 43),
(128, 'Venus', 43),
(129, 'César Milstein', 44),
(130, 'Bernardo Houssay', 44),
(131, 'Juan Maldacena', 44),
(132, 'Nepal', 45),
(133, 'Perú', 45),
(134, 'Bután', 45),
(135, 'Salta', 46),
(136, 'Formosa', 46),
(137, 'Misiones', 46),
(138, 'Singleton', 47),
(139, 'Observer', 47),
(140, 'Decorator', 47),
(141, 'Principio de Abierto/Cerrado', 48),
(142, 'Principio de Sustitución de Liskov', 48),
(143, 'Principio de Segregación de Interfaces', 48),
(144, 'docker create', 49),
(145, 'docker run', 49),
(146, 'docker compose', 49),
(147, 'HTTP', 50),
(148, 'FTP', 50),
(149, 'SMTP', 50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Editor'),
(3, 'Jugador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('Masculino','Femenino','Prefiero no cargarlo') DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `contrasenia` varchar(255) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `url_foto_perfil` varchar(255) DEFAULT NULL,
  `url_qr` varchar(255) DEFAULT NULL,
  `id_rol` int(11) NOT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `id_nivel` int(11) NOT NULL DEFAULT 1,
  `puntaje_total` int(11) NOT NULL DEFAULT 0,
  `cuenta_validada` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `fecha_nacimiento`, `sexo`, `correo`, `contrasenia`, `nombre_usuario`, `url_foto_perfil`, `url_qr`, `id_rol`, `id_ciudad`, `id_nivel`, `puntaje_total`, `cuenta_validada`) VALUES
(9, NULL, NULL, NULL, NULL, 'admin123@gmail.com', '$2y$10$OCA/OjkHJQa2uOoF/aNMKeyoEgqeNh.a9S08XTE4hZl4j.c3A/GOW', 'usuarioAdmin123', 'public/img/photo-admin.jpg', '/qr/usuarioAdmin123.png', 1, NULL, 1, 0, 1),
(10, NULL, NULL, NULL, NULL, 'editor123@gmail.com', '$2y$10$oX6fGeN2dnaI0En7EWuhAubVO6gFgSJuC0uG9qZY3uJEAM9pQDJsy', 'usuarioEditor123', 'public/img/photo-editor.jpg', '/qr/usuarioEditor123.png', 2, NULL, 1, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_pregunta`
--

CREATE TABLE `usuario_pregunta` (
  `id_usuario` int(11) NOT NULL,
  `id_pregunta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pais` (`id_pais`);

--
-- Indices de la tabla `nivel`
--
ALTER TABLE `nivel`
  ADD PRIMARY KEY (`id_nivel`);

--
-- Indices de la tabla `pais`
--
ALTER TABLE `pais`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partida`
--
ALTER TABLE `partida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `respuesta_incorrecta`
--
ALTER TABLE `respuesta_incorrecta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pregunta` (`id_pregunta`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_ciudad` (`id_ciudad`),
  ADD KEY `id_nivel` (`id_nivel`);

--
-- Indices de la tabla `usuario_pregunta`
--
ALTER TABLE `usuario_pregunta`
  ADD PRIMARY KEY (`id_pregunta`,`id_usuario`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `nivel`
--
ALTER TABLE `nivel`
  MODIFY `id_nivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pais`
--
ALTER TABLE `pais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `partida`
--
ALTER TABLE `partida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `respuesta_incorrecta`
--
ALTER TABLE `respuesta_incorrecta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD CONSTRAINT `ciudad_ibfk_1` FOREIGN KEY (`id_pais`) REFERENCES `pais` (`id`);

--
-- Filtros para la tabla `partida`
--
ALTER TABLE `partida`
  ADD CONSTRAINT `partida_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD CONSTRAINT `pregunta_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`);

--
-- Filtros para la tabla `respuesta_incorrecta`
--
ALTER TABLE `respuesta_incorrecta`
  ADD CONSTRAINT `respuesta_incorrecta_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `pregunta` (`id`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id`),
  ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudad` (`id`),
  ADD CONSTRAINT `usuario_ibfk_3` FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id_nivel`);

--
-- Filtros para la tabla `usuario_pregunta`
--
ALTER TABLE `usuario_pregunta`
  ADD CONSTRAINT `usuario_pregunta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `usuario_pregunta_ibfk_2` FOREIGN KEY (`id_pregunta`) REFERENCES `pregunta` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
