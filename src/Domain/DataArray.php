<?php

declare(strict_types = 1);

namespace App\Domain;

interface DataArray
{

    /**
     * @return array [column => value]
     */
    public function getData();
}
