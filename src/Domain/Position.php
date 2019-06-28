<?php

declare(strict_types = 1);

namespace App\Domain;

use App\Domain\DataArray;
use App\Domain\Entity;
use App\Domain\Salary;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read App\Domain\Salary $salary
 */
class Position extends Entity implements DataArray
{

    const TYPE = 'Position';

    protected $id = null;
    protected $name;

    /** @var App\Domain\Salary $salary */
    protected $salary;

    /** @return self */
    public static function fromDb(array $row)
    {
        $salary = Salary::fromDb((string) $row['salary']);
        $entity = new self();
        $entity->id = (int) $row['id'];
        $entity->name = $row['name'];
        $entity->salary = $salary;
        return $entity;
    }

    /** @return self */
    public static function fromForm(array $formData)
    {
        $salary = new Salary((float) $formData['salary']);
        $entity = new self();
        if (!empty($formData['id'])) {
            $entity->id = (int) $formData['id'];
        }
        $entity->name = $formData['name'];
        $entity->salary = $salary;
        return $entity;
    }

    public function getData()
    {
        return [
            'name' => $this->name,
            'salary' => $this->salary->getInternalAmount(),
        ];
    }

}
