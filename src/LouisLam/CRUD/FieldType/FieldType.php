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


    public abstract function render($echo = false);

    /**
     * @param Field $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    public function renderCell($value) {
       return $value;
    }

    protected function getReadOnlyString() {
        if ($this->field->isReadOnly()) {
            return "readonly";
        } else {
            return "";
        }
    }

    protected function getDisabledString() {
        if ($this->field->isReadOnly()) {
            return "disabled";
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

    public function getValue() {
        $name = $this->field->getName();
        $defaultValue = $this->field->getDefaultValue();
        $bean = $this->field->getBean();
        $value = "";

        if ($this->field->isCreate()) {

            // Create Page
            // Use Default Value if not null
            if ($this->field->getValue() !== null) {
                $value = $this->field->getValue();
            } else if ($defaultValue !== null) {
                $value = $defaultValue;
            }

        } else {

            // Edit Page
            // Use the value from Database
            if ($this->field->isOverwriteValue() && $this->field->getValue() !== null) {
                $value = $this->field->getValue();
            } else {
                $value = $bean->{$name};
            }
        }

        return $value;
    }

}