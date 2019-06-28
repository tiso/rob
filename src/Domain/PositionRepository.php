<?php

declare(strict_types = 1);

namespace App\Domain;

use App\Domain\Position;
use App\Domain\Repository;

class PositionRepository extends Repository
{

    protected $table = 'position';

    /**
     * CRUD Create method
     * 
     * @return int
     */
    public function create(Position $Position)
    {
        return $this->insert($Position);
    }

}
