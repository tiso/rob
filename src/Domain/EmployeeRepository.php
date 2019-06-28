<?php

declare(strict_types = 1);

namespace App\Domain;

use App\Domain\Employee;
use App\Domain\Repository;

class EmployeeRepository extends Repository
{

    protected $table = 'employee';

    /**
     * CRUD Create method
     * 
     * @return int
     */
    public function create(Employee $Position)
    {
        return $this->insert($Position);
    }

}
