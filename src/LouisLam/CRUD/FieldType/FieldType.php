<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 3:41 PM
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\Field;

abstract class FieldType
{

    /**
     * @var Field
     */
    protected $field;


    public abstract function render($echo = true);

    /**
     * @param Field $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    public function renderCell($value) {
        echo $value;
    }

    protected function getReadOnlyString() {
        if ($this->field->isReadOnly()) {
            return "readonly";
        } else {
            return "";
        }
    }

    protected function getRequiredString()
    {
        if ($this->field->isRequired()) {
            return "required";
        } else {
            return "";
        }
    }

}