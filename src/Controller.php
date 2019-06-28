<?php

declare(strict_types = 1);

namespace App;

use App\App;
use App\Model;
use App\View;
use App\Domain\Employee;
use App\Domain\EmployeeRepository;
use App\Domain\Position;
use App\Domain\PositionRepository;

class Controller
{

    const ERROR_DUPLICATE_ENTRY = '1062 Duplicate entry';
    const ERROR_FOREIGN_KEY = '1451 Cannot delete or update a parent row';
    const MESSAGE_REQUIRED = 'Hodnota je povinná.';
    const PATH_EMPLOYEE = '/employee';
    const PATH_POSITION = '/position';

    /** @var App\App $context */
    private $context;

    /** @var App\View $renderer */
    private $view;
    private $data = [];

    public function __construct(App $context, View $view)
    {
        $this->context = $context;
        $this->view = $view;
        $this->data['path'] = $this->context->path;
        $this->data['content'] = $this->context->content;
    }

    public function indexAction()
    {
        $this->render();
    }

    public function notFoundAction()
    {
        $this->render();
    }

    public function listPositionsAction()
    {
        $model = Model::construct();
        $positions = $model->getPositions();
        $this->data['content'] .= $this->view->renderPositionList($positions);
        $this->render();
    }

    public function addPositionAction()
    {
        $data = $this->context->formData;
        if ($this->validatePositionData($data)) {
            $entity = Position::fromForm($data);
            $repository = PositionRepository::construct();
            try {
                $repository->create($entity);
                \header('Location: ' . BASE_URL . self::PATH_POSITION, true, 302);
                die();
            } catch (\PDOException $ex) {
                if (false !== strpos($ex->getMessage(), self::ERROR_DUPLICATE_ENTRY)) {
                    $_SESSION['errors']['name'] = 'Pozícia s týmto názvom už existuje, zvoľte iný.';
                }
            }
        }
        $this->data['content'] .= $this->view->renderPositionForm('Pridať', $data);
        $this->render();
    }

    public function editPositionAction()
    {
        $data = $this->context->formData;
        $id = (int) $this->context->params['id'];
        if ($this->validatePositionData($data)) {
            $data = $data + ['id' => $id];
            $entity = Position::fromForm($data);
            $repository = PositionRepository::construct();
            try {
                $repository->update($entity);
                \header('Location: ' . BASE_URL . self::PATH_POSITION, true, 302);
                die();
            } catch (\PDOException $ex) {
                if (false !== strpos($ex->getMessage(), self::ERROR_DUPLICATE_ENTRY)) {
                    $_SESSION['errors']['name'] = 'Pozícia s týmto názvom už existuje, zvoľte iný.';
                }
            }
        }
        if (!empty($id)) {
            $model = Model::construct();
            $dbEntity = $model->getPositionById($id);
            if ($dbEntity) {
                $data = $data + [
                    'name' => $dbEntity->name,
                    'salary' => $dbEntity->salary->amount,
                ];
            }
        }
        $this->data['content'] .= $this->view->renderPositionForm('Upraviť', $data);
        $this->render();
    }

    public function deletePositionAction()
    {
        $id = (int) $this->context->params['id'];
        $model = Model::construct();
        $dbEntity = $model->getPositionById($id);
        if ($dbEntity) {
            $repository = PositionRepository::construct();
            try {
                $repository->delete($dbEntity);
            } catch (\PDOException $ex) {
                if (false !== strpos($ex->getMessage(), self::ERROR_FOREIGN_KEY)) {
                    $_SESSION['errors']['deletePosition'] = 'Pozíciu nemožno vymazať, je priradená k zamestnancom.';
                }
            }
        }
        \header('Location: ' . BASE_URL . self::PATH_POSITION, true, 302);
        die();
    }

    public function listEmployeesAction()
    {
        $model = Model::construct();
        $employees = $model->getEmployees();
        $this->data['content'] .= $this->view->renderEmployeesList($employees);
        $this->render();
    }

    public function addEmployeeAction()
    {
        $data = $this->context->formData;
        $model = Model::construct();
        if ($this->validateEmployeeData($data) && !is_null($position = $model->getPositionById((int) $data['position_id']))) {
            $data = $data + [
                'position' => $position->name,
                'position_salary' => $position->salary->amount,
            ];
            if (empty($data['salary'])) {
                $data['salary'] = $data['position_salary'];
            }
            $entity = Employee::fromForm($data);
            $repository = EmployeeRepository::construct();
            try {
                $repository->create($entity);
                \header('Location: ' . BASE_URL . self::PATH_EMPLOYEE, true, 302);
                die();
            } catch (\PDOException $ex) {
                if (false !== strpos($ex->getMessage(), self::ERROR_DUPLICATE_ENTRY)) {
                    $_SESSION['errors']['email'] = 'Zamestnanec s týmto emailom už existuje, zvoľte iný.';
                }
            }
        }
        $options = $this->getPositionOptions($model);
        $this->data['content'] .= $this->view->renderEmployeeForm('Pridať', $options, $data);
        $this->render();
    }

    public function editEmployeeAction()
    {
        $data = $this->context->formData;
        $id = (int) $this->context->params['id'];
        $model = Model::construct();
        $options = $this->getPositionOptions($model);
        if ($this->validateEmployeeData($data) && !empty($options[$data['position_id']])) {
            $position = $model->getPositionById((int) $data['position_id']);
            $data = $data + [
                'id' => $id,
                'position' => $position->name,
                'position_salary' => $position->salary->amount,
            ];
            if (empty($data['salary'])) {
                $data['salary'] = $data['position_salary'];
            }
            $entity = Employee::fromForm($data);
            $repository = EmployeeRepository::construct();
            try {
                $repository->update($entity);
                \header('Location: ' . BASE_URL . self::PATH_EMPLOYEE, true, 302);
                die();
            } catch (\PDOException $ex) {
                if (false !== strpos($ex->getMessage(), self::ERROR_DUPLICATE_ENTRY)) {
                    $_SESSION['errors']['email'] = 'Zamestnanec s týmto emailom už existuje, zvoľte iný.';
                }
            }
        }
        if (!empty($id)) {
            $dbEntity = $model->getEmployeeById($id);
            if ($dbEntity) {
                $data = $data + [
                    'firstname' => $dbEntity->firstname,
                    'lastname' => $dbEntity->lastname,
                    'titles' => $dbEntity->titles,
                    'email' => $dbEntity->email,
                    'phone' => $dbEntity->phone,
                    'position_id' => $dbEntity->position->id,
                    'salary' => $dbEntity->salary->amount
                ];
            }
        }
        $this->data['content'] .= $this->view->renderEmployeeForm('Upraviť', $options, $data);
        $this->render();
    }

    public function deleteEmployeeAction()
    {
        $id = (int) $this->context->params['id'];
        $model = Model::construct();
        $dbEntity = $model->getEmployeeById($id);
        if ($dbEntity) {
            $repository = EmployeeRepository::construct();
            $repository->delete($dbEntity);
        }
        \header('Location: ' . BASE_URL . self::PATH_EMPLOYEE, true, 302);
        die();
    }

    //--------------------------------------------------------------------------
    private function getPositionOptions(Model $model)
    {
        return array_map(function($value) {
            return $value->name;
        }, $model->getPositions());
    }

    private function render()
    {
        $this->view->setData($this->data);
        echo $this->view->render();
    }

    private function validatePositionData(array $data)
    {
        if (empty($data))
        {
            return false;
        }
        $return = true;
        if (empty($data['name'])) {
            $_SESSION['errors']['name'] = self::MESSAGE_REQUIRED;
            $return &= false;
        }
        if (empty($data['salary'])) {
            $_SESSION['errors']['salary'] = self::MESSAGE_REQUIRED;
            $return &= false;
        }
        return $return;
    }

    private function validateEmployeeData(array $data)
    {
        if (empty($data))
        {
            return false;
        }
        $return = true;
        if (empty($data['firstname'])) {
            $_SESSION['errors']['firstname'] = self::MESSAGE_REQUIRED;
            $return &= false;
        }
        if (empty($data['lastname'])) {
            $_SESSION['errors']['lastname'] = self::MESSAGE_REQUIRED;
            $return &= false;
        }
        if (empty($data['titles'])) {
            $_SESSION['errors']['titles'] = self::MESSAGE_REQUIRED;
            $return &= false;
        }
        if (empty($data['email'])) {
            $_SESSION['errors']['email'] = self::MESSAGE_REQUIRED;
            $return &= false;
        }
        if (empty($data['phone'])) {
            $_SESSION['errors']['phone'] = self::MESSAGE_REQUIRED;
            $return &= false;
        }
        return $return;
    }
}
