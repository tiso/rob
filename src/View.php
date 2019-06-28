<?php

declare(strict_types = 1);

namespace App;

use App\Escape;

class View
{

    private $currentPath;
    private $data = [];

    public function __construct(string $currentPath)
    {
        $this->currentPath = $currentPath;
    }

    public function render()
    {
        return $this->renderLayout($this->renderMenu(), $this->renderContent());
    }

    public function renderEmployeeForm(string $action, array $options, array $data = [])
    {
        $selected = isset($data['position_id']) ? $data['position_id'] : '';
        return '
<form method="POST">
  <fieldset>
    <legend>Zamestnanec</legend>
    <div><label for="firstname">meno</label><input name="firstname" id="firstname" value="' . $this->renderFormValue($data, 'firstname') . '"><span class="required" title="required">*</span>' . $this->renderValidationError('firstname') . '</div>
    <div><label for="lastname">priezvisko</label><input name="lastname" id="lastname" value="' . $this->renderFormValue($data, 'lastname') . '"><span class="required" title="required">*</span>' . $this->renderValidationError('lastname') . '</div>
    <div><label for="titles">tituly</label><input name="titles" id="titles" value="' . $this->renderFormValue($data, 'titles') . '"><span class="required" title="required">*</span>' . $this->renderValidationError('titles') . '</div>
    <div><label for="email">email</label><input name="email" id="email" value="' . $this->renderFormValue($data, 'email') . '"><span class="required" title="required">*</span>' . $this->renderValidationError('email') . '</div>
    <div><label for="phone">telefón</label><input name="phone" id="phone" value="' . $this->renderFormValue($data, 'phone') . '"><span class="required" title="required">*</span>' . $this->renderValidationError('phone') . '</div>
    <div><label for="position_id">pracovná pozícia</label><select id="position_id" name="position_id">' . $this->renderOptions($options, $selected) . '</select><span class="required" title="required">*</span></div>
    <div><label for="salary">plat (EUR)</label><input name="salary" id="salary" value="' . $this->renderFormValue($data, 'salary') . '"><span class="required" title="required">*</span></div>
    <div><input name="submit" type="submit" value="' . $action . '"></div>
  </fieldset>
</form>';
    }

    public function renderEmployeesList(array $employees)
    {
        $out = '
<a href="' . BASE_URL . $this->currentPath . '/add">pridať nového zamestnanca</a>
<table>
  <caption>Zamestnanci</caption>
  <tr><th>#</th><th>meno</th><th>email</th><th>telefón</th><th>pozícia</th><th>plat</th><th></th></tr>';
        foreach ($employees as $employee) {
            $out .= '
  <tr>
    <td>' . $employee->id . '</td>
    <td>' . $employee->getFullname() . '</td>
    <td>' . $employee->email . '</td>
    <td>' . $employee->phone . '</td>
    <td>' . $employee->position->name . '</td>
    <td>' . $employee->salary . '</td>
    <td>
      <a href="' . BASE_URL . $this->currentPath . '/edit?id=' . (string) $employee->id . '">upraviť</a> 
      <a href="' . BASE_URL . $this->currentPath . '/delete?id=' . (string) $employee->id . '">zmazať</a>
    </td>
  </tr>';
        }
        $out .= '</table>';
        return $out;
    }

    public function renderPositionForm(string $action, array $data = [])
    {
        return '
<form method="POST">
  <fieldset>
    <legend>Pracovná pozícia</legend>
    <div><label for="name">názov</label><input name="name" id="name" value="' . $this->renderFormValue($data, 'name') . '"><span class="required" title="required">*</span>' . $this->renderValidationError('name') . '</div>
    <div><label for="salary">štandartný plat (EUR)</label><input name="salary" id="salary" value="' . $this->renderFormValue($data, 'salary') . '"><span class="required" title="required">*</span>' . $this->renderValidationError('salary') . '</div>
    <div><input name="submit" type="submit" value="' . $action . '"></div>
  </fieldset>
</form>';
    }

    public function renderPositionList(array $positions)
    {
        $out = '
<div>' . $this->renderValidationError('deletePosition') . '</div>
<a href="' . BASE_URL . $this->currentPath . '/add">pridať novú pracovnú pozíciu</a>
<table>
  <caption>Pracovné pozície</caption>
  <tr><th>#</th><th>názov</th><th>štandartný plat</th><th></th></tr>';
        foreach ($positions as $position) {
            $out .= '
  <tr>
    <td>' . $position->id . '</td>
    <td>' . $position->name . '</td>
    <td>' . $position->salary . '</td>
    <td>
      <a href="' . BASE_URL . $this->currentPath . '/edit?id=' . (string) $position->id . '">upraviť</a> 
      <a href="' . BASE_URL . $this->currentPath . '/delete?id=' . (string) $position->id . '">zmazať</a>
    </td>
  </tr>';
        }
        $out .= '</table>';
        return $out;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    //--------------------------------------------------------------------------
    private function renderContent()
    {
        return $this->data['content'];
    }

    private function renderFormValue(array $data, string $key)
    {
        if (isset($data[$key])) {
            return Escape::escapeHTML($data[$key]);
        }
        return '';
    }

    private function renderLayout(string $menu, string $content)
    {
        return '<!doctype html>
<html lang="sk">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>Rob</title>
        <link rel="shortcut icon" type="image/x-icon" href="' . BASE_URL . '/favicon.ico">
        <link rel="stylesheet" type="text/css" href="' . BASE_URL . '/app.css" media="screen">
        <style></style>
    </head>
    <body>
        <div id="all">
            <div id="header"><a href="' . BASE_URL . '" title="home">Rob</a>...header...</div><hr class="hide">
            <div id="menu">' . $menu . '<div class="clear"></div></div><hr class="hide">
            <div id="main">' . $content . '</div><hr class="hide">
            <div id="footer">...footer...</div>
        </div>
    </body>
</html>';
    }

    private function renderMenu()
    {
        return '
<ul class="rst">
    <li><a href="' . BASE_URL . '/position">Pracovné pozície</a></li>
    <li><a href="' . BASE_URL . '/employee">Zamestnanci</a></li>
</ul>';
    }

    private function renderOptions(array $options, $selected = null)
    {
        $out = '';
        foreach ($options as $id => $text) {
            $out .= '<option value="' . Escape::escapeHTML($id) . '"' . ($id === $selected ? ' selected="selected"' : '') . '>' . Escape::escapeHTML($text) . '</option>';
        }
        return $out;
    }

    private function renderValidationError(string $key)
    {
        $out = '';
        if (isset($_SESSION['errors']) && isset($_SESSION['errors'][$key])) {
            $out = ' <span class="error">' . \implode(' ', (array) $_SESSION['errors'][$key]) . '</span>';
            unset($_SESSION['errors'][$key]);
        }
        return $out;
    }

}
