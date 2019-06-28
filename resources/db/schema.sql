-- Db schema

CREATE DATABASE `rob` COLLATE 'utf8mb4_general_ci';

USE `rob`;

CREATE TABLE `position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `salary` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;

CREATE TABLE `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL, 
  `titles` varchar(255) NOT NULL DEFAULT '', 
  `email` varchar(255) NOT NULL, 
  `phone` varchar(255) NOT NULL, 
  `salary` int,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  FOREIGN KEY (`position_id`) 
    REFERENCES `position`(`id`)
    ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB;
