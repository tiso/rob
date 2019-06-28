<?php

declare(strict_types = 1);

namespace App\Domain;

use App\Db;
use App\Domain\DataArray;

/**
 * Write Model 
 */
abstract class Repository
{

    /** @var App\Db $db */
    protected $db;

    /** @var string $table */
    protected $table;

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

    /** @return int */
    final protected function insert(DataArray $entity)
    {
        return $this->db->insert($this->table, $entity->getData());
    }

    /** @return int */
    final public function update(DataArray $entity)
    {
        $this->db->update($this->table, $entity->getData(), "id=$entity->id");
    }

    /** @return int */
    final public function delete(DataArray $entity)
    {
        return $this->db->delete($this->table, "id=$entity->id");
    }

}
