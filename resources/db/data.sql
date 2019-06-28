-- data

USE `rob`;

SET NAMES utf8mb4;

INSERT INTO `position` (`id`, `name`, `salary`) VALUES
(1,	'PHP Developer',	1500000);

INSERT INTO `employee` (`id`, `position_id`, `firstname`, `lastname`, `titles`, `email`, `phone`, `salary`) VALUES
(1,	1,	'John',	'Doe',	'',	'john.doe@example.com',	'+123 456 789 012',	NULL);

