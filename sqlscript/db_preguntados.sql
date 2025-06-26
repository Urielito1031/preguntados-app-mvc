-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-06-2025 a las 17:05:44
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
create database db_preguntados;
use db_preguntados;
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
                                        (2, 'Ecuador'),
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
-- Estructura de tabla para la tabla 'pregunta-sugerida'
CREATE TABLE `pregunta_sugerida` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `id_categoria` int(11) NOT NULL,
                                     `enunciado` varchar(255) NOT NULL,
                                     PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- --------------------------------------------------------
-- Estructura de tabla para la tabla 'respuesta-sugerida'

CREATE TABLE `respuesta_sugerida` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `respuesta` varchar(255) NOT NULL,
                                      `id_pregunta` int(11) NOT NULL,
                                      `es_correcta` TINYINT(1) NOT NULL,
                                      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE `pregunta` (
                            `id` int(11) NOT NULL,
                            `id_categoria` int(11) NOT NULL,
                            `enunciado` varchar(255) NOT NULL,
                            `cantidad_jugada` int(11) DEFAULT 0,
                            `cantidad_aciertos` int(11) DEFAULT 0,
                            `cantidad_reportes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`id`,`id_categoria`, `enunciado`, `cantidad_jugada`, `cantidad_aciertos`, `cantidad_reportes`) VALUES
                                                                                                                           (1, 1, '¿Quién fue el primer presidente de los Estados Unidos?', 0, 0, 0),
                                                                                                                           (2, 1, '¿En qué año comenzó la Segunda Guerra Mundial?', 0, 0, 0),
                                                                                                                           (3, 1, '¿Qué civilización construyó las pirámides de Giza?', 0, 0, 0),
                                                                                                                           (4, 2, '¿Cuál es el deporte más popular del mundo?', 0, 0, 0),
                                                                                                                           (5, 2, '¿Quién es considerado el mejor jugador de baloncesto de la historia?', 0, 0, 0),
                                                                                                                           (6, 2, '¿Qué torneo de tenis se juega en hierba?', 0, 0, 0),
                                                                                                                           (7, 3, '¿Quién pintó la Mona Lisa?', 0, 0, 0),
                                                                                                                           (8, 3, '¿Qué movimiento artístico está asociado con Claude Monet?', 0, 0, 0),
                                                                                                                           (9, 3, '¿Cuál es el nombre de la obra famosa de Vincent van Gogh?', 0, 0, 0),
                                                                                                                           (10, 4, '¿Quién formuló las leyes del movimiento?', 0, 0, 0),
                                                                                                                           (11, 4, '¿Qué elemento es el más abundante en la atmósfera terrestre?', 0, 0, 0),
                                                                                                                           (12, 4, '¿Qué molécula porta la información genética?', 0, 0, 0),
                                                                                                                           (13, 5, '¿Cuál es la capital de Francia?', 0, 0, 0),
                                                                                                                           (14, 5, '¿Qué río es el más largo de Sudamérica?', 0, 0, 0),
                                                                                                                           (15, 5, '¿En qué continente está Egipto?', 0, 0, 0),
                                                                                                                           (16, 6, '¿Qué actor ganó un Oscar por "El Renacido"?', 0, 0, 0),
                                                                                                                           (17, 6, '¿Qué banda escribió "Hey Jude"?', 0, 0, 0),
                                                                                                                           (18, 6, '¿Qué saga incluye a un personaje llamado Harry Potter?', 0, 0, 0),
                                                                                                                           (21, 1, '¿En qué año cayó el Muro de Berlín?', 0, 0, 0),
                                                                                                                           (22, 1, '¿Quién fue el líder de la Revolución Rusa de 1917?', 0, 0, 0),
                                                                                                                           (23, 1, '¿Qué emperador romano construyó el Coliseo?', 0, 0, 0),
                                                                                                                           (24, 2, '¿En qué deporte se utiliza un bate y una pelota?', 0, 0, 0),
                                                                                                                           (25, 2, '¿Qué país ha ganado más Copas Mundiales de Fútbol?', 0, 0, 0),
                                                                                                                           (26, 2, '¿Qué jugador de la NBA combinó su dominio con el "skyhook" y un récord de 6 MVP?', 0, 0, 0),
                                                                                                                           (27, 3, '¿Quién pintó "La última cena"?', 0, 0, 0),
                                                                                                                           (28, 3, '¿Qué artista es conocido por sus pinturas de latas de sopa Campbell?', 0, 0, 0),
                                                                                                                           (29, 3, '¿Qué arquitecto diseñó el Museo Guggenheim de Bilbao?', 0, 0, 0),
                                                                                                                           (30, 4, '¿Qué planeta es conocido como el planeta rojo?', 0, 0, 0),
                                                                                                                           (31, 4, '¿Qué científico propuso la teoría de la relatividad?', 0, 0, 0),
                                                                                                                           (32, 4, '¿Qué partícula subatómica tiene carga positiva?', 0, 0, 0),
                                                                                                                           (33, 5, '¿Cuál es el país más grande del mundo por área?', 0, 0, 0),
                                                                                                                           (34, 5, '¿Qué montaña es la más alta del mundo?', 0, 0, 0),
                                                                                                                           (35, 5, '¿Qué país tiene la mayor cantidad de husos horarios?', 0, 0, 0),
                                                                                                                           (36, 6, '¿Qué actor interpretó a Iron Man en el Universo Cinematográfico de Marvel?', 0, 0, 0),
                                                                                                                           (37, 6, '¿Qué serie de TV es conocida por el lema "Winter is Coming"?', 0, 0, 0),
                                                                                                                           (38, 6, '¿Qué director de cine es conocido por películas como "Pulp Fiction" y "Kill Bill"?', 0, 0, 0),
                                                                                                                           (39, 1, '¿Qué presidente argentino organizó una fiesta en la quinta de Olivos?', 0, 0, 0),
                                                                                                                           (40, 1, '¿Qué país sudamericano fue el último en independizarse de España?', 0, 0, 0),
                                                                                                                           (41, 2, '¿Qué país inventó el bádminton?', 0, 0, 0),
                                                                                                                           (42, 4, '¿Qué animal tiene el cerebro más grande en proporción a su cuerpo?', 0, 0, 0),
                                                                                                                           (43, 4, '¿Qué planeta del sistema solar tiene vientos más rápidos (2,100 km/h)?', 0, 0, 0),
                                                                                                                           (44, 4, '¿Qué científico argentino ganó el Nobel por descubrir cómo las células usan el azúcar?', 0, 0, 0),
                                                                                                                           (45, 5, '¿Qué país tiene la capital más alta del mundo?', 0, 0, 0),
                                                                                                                           (46, 5, '¿Qué provincia Argentina tiene frontera con Chile y Bolivia?', 0, 0, 0),
                                                                                                                           (47, 7, '¿Qué patrón de diseño sugiere crear objetos sin exponer la lógica de creación?', 0, 0, 0),
                                                                                                                           (48, 7, '¿Qué principio SOLID indica que una clase debe tener una única responsabilidad?', 0, 0, 0),
                                                                                                                           (49, 7, '¿Qué comando de Docker se usa para construir una imagen desde un Dockerfile?', 0, 0, 0),
                                                                                                                           (50, 4, '¿Qué protocolo asegura la comunicación encriptada en la web?', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_incorrecta`
--

CREATE TABLE `respuesta` (
                             `id` int(11) NOT NULL,
                             `respuesta` varchar(255) NOT NULL,
                             `id_pregunta` int(11) NOT NULL,
                             `es_correcta` TINYINT(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta_incorrecta`
--

INSERT INTO `respuesta` (`id`, `respuesta`, `id_pregunta`, `es_correcta`) VALUES
                                                                              (1, 'Thomas Jefferson', 1, 0),
                                                                              (2, 'Abraham Lincoln', 1, 0),
                                                                              (3, 'John Adams', 1, 0),
                                                                              (4, '1941', 2, 0),
                                                                              (5, '1914', 2, 0),
                                                                              (6, '1945', 2, 0),
                                                                              (7, 'Maya', 3, 0),
                                                                              (8, 'Azteca', 3, 0),
                                                                              (9, 'Inca', 3, 0),
                                                                              (10, 'Baloncesto', 4, 0),
                                                                              (11, 'Tenis', 4, 0),
                                                                              (12, 'Golf', 4, 0),
                                                                              (13, 'LeBron James', 5, 0),
                                                                              (14, 'Kobe Bryant', 5, 0),
                                                                              (15, 'Magic Johnson', 5, 0),
                                                                              (16, 'Roland Garros', 6, 0),
                                                                              (17, 'US Open', 6, 0),
                                                                              (18, 'Australian Open', 6, 0),
                                                                              (19, 'Michelangelo', 7, 0),
                                                                              (20, 'Raphael', 7, 0),
                                                                              (21, 'Donatello', 7, 0),
                                                                              (22, 'Cubismo', 8, 0),
                                                                              (23, 'Surrealismo', 8, 0),
                                                                              (24, 'Expresionismo', 8, 0),
                                                                              (25, 'La Persistencia de la Memoria', 9, 0),
                                                                              (26, 'El Grito', 9, 0),
                                                                              (27, 'Guernica', 9, 0),
                                                                              (28, 'Albert Einstein', 10, 0),
                                                                              (29, 'Galileo Galilei', 10, 0),
                                                                              (30, 'Nikola Tesla', 10, 0),
                                                                              (31, 'Hidrógeno', 11, 0),
                                                                              (32, 'Carbono', 11, 0),
                                                                              (33, 'Nitrógeno', 11, 0),
                                                                              (34, 'ARN', 12, 0),
                                                                              (35, 'Proteína', 12, 0),
                                                                              (36, 'Enzima', 12, 0),
                                                                              (37, 'Londres', 13, 0),
                                                                              (38, 'Berlín', 13, 0),
                                                                              (39, 'Madrid', 13, 0),
                                                                              (40, 'Nilo', 14, 0),
                                                                              (41, 'Misisipi', 14, 0),
                                                                              (42, 'Yangtsé', 14, 0),
                                                                              (43, 'Asia', 15, 0),
                                                                              (44, 'Europa', 15, 0),
                                                                              (45, 'América', 15, 0),
                                                                              (46, 'Brad Pitt', 16, 0),
                                                                              (47, 'Tom Cruise', 16, 0),
                                                                              (48, 'Johnny Depp', 16, 0),
                                                                              (49, 'Rolling Stones', 17, 0),
                                                                              (50, 'Led Zeppelin', 17, 0),
                                                                              (51, 'Queen', 17, 0),
                                                                              (52, 'El Señor de los Anillos', 18, 0),
                                                                              (53, 'Star Wars', 18, 0),
                                                                              (54, 'Juego de Tronos', 18, 0),
                                                                              (60, '1975', 21, 0),
                                                                              (61, '1991', 21, 0),
                                                                              (62, '1961', 21, 0),
                                                                              (63, 'Joseph Stalin', 22, 0),
                                                                              (64, 'Leon Trotsky', 22, 0),
                                                                              (65, 'Mikhail Gorbachev', 22, 0),
                                                                              (66, 'Julio César', 23, 0),
                                                                              (67, 'Nerón', 23, 0),
                                                                              (68, 'Augusto', 23, 0),
                                                                              (69, 'Fútbol', 24, 0),
                                                                              (70, 'Baloncesto', 24, 0),
                                                                              (71, 'Tenis', 24, 0),
                                                                              (72, 'Alemania', 25, 0),
                                                                              (73, 'Italia', 25, 0),
                                                                              (74, 'Argentina', 25, 0),
                                                                              (75, 'LeBron James', 26, 0),
                                                                              (76, 'Michael Jordan', 26, 0),
                                                                              (77, 'Kobe Bryant', 26, 0),
                                                                              (78, 'Miguel Ángel', 27, 0),
                                                                              (79, 'Pablo Picasso', 27, 0),
                                                                              (80, 'Vincent van Gogh', 27, 0),
                                                                              (81, 'Salvador Dalí', 28, 0),
                                                                              (82, 'Jackson Pollock', 28, 0),
                                                                              (83, 'Roy Lichtenstein', 28, 0),
                                                                              (84, 'Zaha Hadid', 29, 0),
                                                                              (85, 'I.M. Pei', 29, 0),
                                                                              (86, 'Santiago Calatrava', 29, 0),
                                                                              (87, 'Venus', 30, 0),
                                                                              (88, 'Júpiter', 30, 0),
                                                                              (89, 'Saturno', 30, 0),
                                                                              (90, 'Isaac Newton', 31, 0),
                                                                              (91, 'Stephen Hawking', 31, 0),
                                                                              (92, 'Galileo Galilei', 31, 0),
                                                                              (93, 'Electrón', 32, 0),
                                                                              (94, 'Neutrón', 32, 0),
                                                                              (95, 'Positrón', 32, 0),
                                                                              (96, 'Canadá', 33, 0),
                                                                              (97, 'China', 33, 0),
                                                                              (98, 'Estados Unidos', 33, 0),
                                                                              (99, 'K2', 34, 0),
                                                                              (100, 'Kangchenjunga', 34, 0),
                                                                              (101, 'Makalu', 34, 0),
                                                                              (102, 'Rusia', 35, 0),
                                                                              (103, 'Estados Unidos', 35, 0),
                                                                              (104, 'Reino Unido', 35, 0),
                                                                              (105, 'Chris Evans', 36, 0),
                                                                              (106, 'Chris Hemsworth', 36, 0),
                                                                              (107, 'Mark Ruffalo', 36, 0),
                                                                              (108, 'The Walking Dead', 37, 0),
                                                                              (109, 'Stranger Things', 37, 0),
                                                                              (110, 'Breaking Bad', 37, 0),
                                                                              (111, 'Martin Scorsese', 38, 0),
                                                                              (112, 'Steven Spielberg', 38, 0),
                                                                              (113, 'Christopher Nolan', 38, 0),
                                                                              (114, 'Hipólito Yrigoyen', 39, 0),
                                                                              (115, 'Arturo Frondizi', 39, 0),
                                                                              (116, 'Raúl Alfonsín', 39, 0),
                                                                              (117, 'Argentina', 40, 0),
                                                                              (118, 'Colombia', 40, 0),
                                                                              (119, 'Venezuela', 40, 0),
                                                                              (120, 'China', 41, 0),
                                                                              (121, 'Inglaterra', 41, 0),
                                                                              (122, 'Japón', 41, 0),
                                                                              (123, 'Delfín', 42, 0),
                                                                              (124, 'Elefante', 42, 0),
                                                                              (125, 'Chimpancé', 42, 0),
                                                                              (126, 'Júpiter', 43, 0),
                                                                              (127, 'Saturno', 43, 0),
                                                                              (128, 'Venus', 43, 0),
                                                                              (129, 'César Milstein', 44, 0),
                                                                              (130, 'Bernardo Houssay', 44, 0),
                                                                              (131, 'Juan Maldacena', 44, 0),
                                                                              (132, 'Nepal', 45, 0),
                                                                              (133, 'Perú', 45, 0),
                                                                              (134, 'Bután', 45, 0),
                                                                              (135, 'Salta', 46, 0),
                                                                              (136, 'Formosa', 46, 0),
                                                                              (137, 'Misiones', 46, 0),
                                                                              (138, 'Singleton', 47, 0),
                                                                              (139, 'Observer', 47, 0),
                                                                              (140, 'Decorator', 47, 0),
                                                                              (141, 'Principio de Abierto/Cerrado', 48, 0),
                                                                              (142, 'Principio de Sustitución de Liskov', 48, 0),
                                                                              (143, 'Principio de Segregación de Interfaces', 48, 0),
                                                                              (144, 'docker create', 49, 0),
                                                                              (145, 'docker run', 49, 0),
                                                                              (146, 'docker compose', 49, 0),
                                                                              (147, 'HTTP', 50, 0),
                                                                              (148, 'FTP', 50, 0),
                                                                              (149, 'SMTP', 50, 0),
                                                                              (150, 'George Washington', 1, 1),
                                                                              (151, '1939', 2, 1),
                                                                              (152, 'Egipcia', 3, 1),
                                                                              (153, 'Fútbol', 4, 1),
                                                                              (154, 'Michael Jordan', 5, 1),
                                                                              (155, 'Wimbledon', 6, 1),
                                                                              (156, 'Leonardo da Vinci', 7, 1),
                                                                              (157, 'Impresionismo', 8, 1),
                                                                              (158, 'La Noche Estrellada', 9, 1),
                                                                              (159, 'Isaac Newton', 10, 1),
                                                                              (160, 'Oxígeno', 11, 1),
                                                                              (161, 'ADN', 12, 1),
                                                                              (162, 'París', 13, 1),
                                                                              (163, 'Amazonas', 14, 1),
                                                                              (164, 'África', 15, 1),
                                                                              (165, 'Leonardo DiCaprio', 16, 1),
                                                                              (166, 'The Beatles', 17, 1),
                                                                              (167, 'Harry Potter', 18, 1),
                                                                              (168, '1989', 21, 1),
                                                                              (169, 'Vladimir Lenin', 22, 1),
                                                                              (170, 'Vespasiano', 23, 1),
                                                                              (171, 'Béisbol', 24, 1),
                                                                              (172, 'Brasil', 25, 1),
                                                                              (173, 'Kareem Abdul-Jabbar', 26, 1),
                                                                              (174, 'Leonardo da Vinci', 27, 1),
                                                                              (175, 'Andy Warhol', 28, 1),
                                                                              (176, 'Frank Gehry', 29, 1),
                                                                              (177, 'Marte', 30, 1),
                                                                              (178, 'Albert Einstein', 31, 1),
                                                                              (179, 'Protón', 32, 1),
                                                                              (180, 'Rusia', 33, 1),
                                                                              (181, 'Monte Everest', 34, 1),
                                                                              (182, 'Francia', 35, 1),
                                                                              (183, 'Robert Downey Jr.', 36, 1),
                                                                              (184, 'Game of Thrones', 37, 1),
                                                                              (185, 'Quentin Tarantino', 38, 1),
                                                                              (186, 'Alberto Fernández', 39, 1),
                                                                              (187, 'Bolivia', 40, 1),
                                                                              (188, 'India', 41, 1),
                                                                              (189, 'Cachalote', 42, 1),
                                                                              (190, 'Neptuno', 43, 1),
                                                                              (191, 'Luis Federico Leloir', 44, 1),
                                                                              (192, 'Bolivia', 45, 1),
                                                                              (193, 'Jujuy', 46, 1),
                                                                              (194, 'Factory Method', 47, 1),
                                                                              (195, 'Principio de Responsabilidad Única', 48, 1),
                                                                              (196, 'docker build', 49, 1),
                                                                              (197, 'HTTPS', 50, 1);

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
                           `cuenta_validada` tinyint(1) NOT NULL DEFAULT 0,
                           `preguntas_entregadas` int(11) DEFAULT 0,
                           `respondidas_correctamente` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `fecha_nacimiento`, `sexo`, `correo`, `contrasenia`, `nombre_usuario`, `url_foto_perfil`, `url_qr`, `id_rol`, `id_ciudad`, `id_nivel`, `puntaje_total`, `cuenta_validada`, `preguntas_entregadas`, `respondidas_correctamente`) VALUES
                                                                                                                                                                                                                                                                                               (9, NULL, NULL, NULL, NULL, 'admin123@gmail.com', '$2y$10$OCA/OjkHJQa2uOoF/aNMKeyoEgqeNh.a9S08XTE4hZl4j.c3A/GOW', 'usuarioAdmin123', 'public/img/photo-admin.jpg', '/qr/usuarioAdmin123.png', 1, 1, 1, 0, 1, 0, 0),
                                                                                                                                                                                                                                                                                               (10, NULL, NULL, NULL, NULL, 'editor123@gmail.com', '$2y$10$oX6fGeN2dnaI0En7EWuhAubVO6gFgSJuC0uG9qZY3uJEAM9pQDJsy', 'usuarioEditor123', 'public/img/photo-editor.jpg', '/qr/usuarioEditor123.png', 2, 2, 1, 0, 1, 0, 0);

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
ALTER TABLE `respuesta`
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
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `respuesta_incorrecta`
--
ALTER TABLE `respuesta`
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
ALTER TABLE `respuesta`
    ADD CONSTRAINT `respuesta_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `pregunta` (`id`)
        ON DELETE CASCADE;

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
    ADD CONSTRAINT `usuario_pregunta_ibfk_2` FOREIGN KEY (`id_pregunta`) REFERENCES `pregunta` (`id`)
        ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
