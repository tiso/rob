<?php

declare(strict_types = 1);

namespace App;

use App\Db;
use App\Domain\Employee;
use App\Domain\Position;

/**
 * Read Model
 */
class Model
{

    const TABLE_POSITION = 'position';
    const TABLE_EMPLOYEE = 'employee';

    /** @var \App\Db $db */
    protected $db;

    /**
     * Static constructor factory
     * 
     * @return self
     */
    public static function construct()
    {
        $db = Db::getInstance();
        return new static($db);
    }

    final public function __construct(Db $db)
    {
        $this->db = $db;
    }

    /** @return null|\App\Position */
    public function getPositionById(int $id)
    {
        $result = $this->db->select(self::TABLE_POSITION, "`id`=$id");
        $row = $result->fetch();
        return $row ? Position::fromDb($row) : null;
    }

    /** @return \App\Position[] */
    public function getPositions()
    {
        $positions = [];
        $result = $this->db->select(self::TABLE_POSITION);
        foreach ($result as $row) {
            $positions[$row['id']] = Position::fromDb($row);
        }
        return $positions;
    }

    /** @return null|\App\Employee */
    public function getEmployeeById(int $id)
    {
        $result = $this->db->query($this->getEmployeeEntityQuery() . " WHERE e.`id`=$id");
        $row = $result->fetch();
        return $row ? Employee::fromDb($row) : null;
    }

    /** @return \App\Employee[] */
    public function getEmployees()
    {
        $employees = [];
        $result = $this->db->query($this->getEmployeeEntityQuery());
        foreach ($result as $row) {
            $employees[$row['id']] = Employee::fromDb($row);
        }
        return $employees;
    }

    //--------------------------------------------------------------------------
    private function getEmployeeEntityQuery()
    {
        return "
            SELECT e.`id`, e.`position_id`, p.`name` AS position, e.`firstname`, e.`lastname`, e.`titles`, e.`email`, e.`phone`, e.`salary`, p.`salary` AS position_salary
            FROM " . self::TABLE_EMPLOYEE . " AS e
            JOIN " . self::TABLE_POSITION . " AS p ON e.`position_id` = p.`id`";
    }

}
