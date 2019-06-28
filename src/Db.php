<?php

declare(strict_types = 1);

namespace App;

use App\Singleton;

/**
 * PDO Db Class
 */
class Db extends Singleton
{

    /** @var \PDO $handler */
    private $handler;

    protected function __construct()
    {
        $this->handler = new \PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DBNAME, SQL_USERNAME, SQL_PASSWORD);
        $this->handler->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->handler->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
        $this->handler->exec("SET NAMES UTF8");
    }

    public function getInsertId()
    {
        return $this->handler->lastInsertId();
    }

    public function sanitize($value)
    {
        if ((is_bool($value) || is_numeric($value)) && !is_string($value)) {
            return $value + 0;
        } elseif (is_null($value)) {
            return 'NULL';
        } else {
            return $this->handler->quote($string);;
        }
    }

    public function query(string $query, $fetch = \PDO::FETCH_ASSOC)
    {
        if (is_null($query)) {
            return null;
        }
        return $this->handler->query($query, $fetch);
    }

//simple CRUD:
    public function insert(string $table, array $data)
    {
        if (empty($data)) {
            return null;
        }
        $q = [];
        foreach ($data as $column => $value) {
            $value = $this->sanitize($value);
            $q[] = "`$column`=$value";
        }
        $query = "INSERT INTO `$table` SET " . implode(', ', $q);
        $this->handler->exec($query);
        return $this->handler->lastInsertId();
    }

    public function select(string $table, $condition = null)
    {
        $query = "SELECT * FROM `$table`";
        if (!empty($condition)) {
            $query .= " WHERE $condition";
        }
        $result = $this->query($query, \PDO::FETCH_ASSOC);
        return $result;
    }

    public function update(string $table, array $data, $condition = null)
    {
        if (empty($data)) {
            return null;
        }
        $q = [];
        foreach ($data as $column => $value) {
            $value = $this->sanitize($value);
            $q[] = "`$column`=$value";
        }
        $query = "UPDATE `$table` SET " . implode(', ', $q);
        if (!empty($condition)) {
            $query .= " WHERE $condition";
        }
        return $this->handler->exec($query);
    }

    public function delete(string $table, $condition = null)
    {
        $query = "DELETE FROM `$table`";
        if (!empty($condition)) {
            $query .= " WHERE $condition";
        }
        return $this->handler->exec($query);
    }

}
