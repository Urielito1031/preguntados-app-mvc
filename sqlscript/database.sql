CREATE DATABASE labanda;
USE labanda;

DROP TABLE IF EXISTS `canciones`;
CREATE TABLE `canciones` (
  `idCancion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCancion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `canciones` VALUES (1,'cancion1',10),(2,'cancion2',12),(3,'cancion3',15);

DROP TABLE IF EXISTS `presentaciones`;
CREATE TABLE `presentaciones` (
  `idPresentacion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `precio` int(11) DEFAULT NULL,
  PRIMARY KEY (`idPresentacion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `presentaciones` VALUES (1,'Presentacion 1','2020-06-02 22:02:14',10),(2,'Presentacion 2','2020-06-02 22:02:19',10),(3,'Presentacion 3','2020-06-02 22:02:21',10);

create table integrantes
(
    nombre      text null,
    instrumento text null,
    id          int auto_increment
        primary key
);

INSERT INTO integrantes(nombre, instrumento) VALUE ('facu', 'ukelele')