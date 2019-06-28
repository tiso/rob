# Rob

Simple Demo application for managing job positions and employees. Application is in slovak language.

## Instalation

- clone this repo to your local machine
- run `composer install`
- set correct constants in `config/config.php`
- create database and schema: run queries from `resources/db/schema.sql`
- insert sample data: run queries from `resources/db/data.sql`


 ## Architecture
- PHP / MySQL (using PDO extension)
- MVC pattern
- DDD building blocks - Value Object, Entity, Repository
- Separated Read Model and Write Model