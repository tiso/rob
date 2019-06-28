<?php

declare(strict_types = 1);

namespace App\Domain;

use App\Domain\DataArray;
use App\Domain\Entity;
use App\Domain\Position;
use App\Domain\Salary;

/**
 * @property-read int $id
 * @property-read string $firstname
 * @property-read string $lastname
 * @property-read string $titles
 * @property-read string $email
 * @property-read string $phone
 * @property-read App\Domain\Position $position
 * @property-read App\Domain\Salary $salary
 */
class Employee extends Entity implements DataArray
{

    const TYPE = 'Employee';

    protected $id = null;
    protected $firstname;
    protected $lastname;
    protected $titles = '';
    protected $email;
    protected $phone;
    protected $position;
    protected $salary;

    /** @return self */
    public static function fromDb(array $row)
    {
        $position = Position::fromDb([
                    'id' => $row['position_id'],
                    'name' => $row['position'],
                    'salary' => $row['position_salary'],
        ]);
        $salary = is_null($row['salary']) ? $position->salary : Salary::fromDb($row['salary']);
        $entity = new self();
        $entity->id = (int) $row['id'];
        $entity->firstname = $row['firstname'];
        $entity->lastname = $row['lastname'];
        $entity->titles = $row['titles'];
        $entity->email = $row['email'];
        $entity->phone = $row['phone'];
        $entity->position = $position;
        $entity->salary = $salary;
        return $entity;
    }

    /** @return self */
    public static function fromForm(array $formData)
    {
        $position = Position::fromForm([
                    'id' => $formData['position_id'],
                    'name' => $formData['position'],
                    'salary' => $formData['position_salary'],
        ]);
        $salary = is_null($formData['salary']) ? $position->salary : new Salary((float) $formData['salary']);
        ;
        $entity = new self();
        if (!empty($formData['id'])) {
            $entity->id = (int) $formData['id'];
        }
        $entity->firstname = $formData['firstname'];
        $entity->lastname = $formData['lastname'];
        $entity->titles = $formData['titles'];
        $entity->email = $formData['email'];
        $entity->phone = $formData['phone'];
        $entity->position = $position;
        $entity->salary = $salary;
        return $entity;
    }

    public function getFullname()
    {
        return $this->firstname . ' ' . $this->lastname . (empty($this->titles) ? '' : ', ' . $this->titles);
    }

    public function getData()
    {
        //var_dump($this->position->salary, $this->salary);
        return [
            'position_id' => $this->position->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'titles' => $this->titles,
            'email' => $this->email,
            'phone' => $this->phone,
            'salary' => $this->position->salary == $this->salary ? null : $this->salary->getInternalAmount(),
        ];
    }

}
