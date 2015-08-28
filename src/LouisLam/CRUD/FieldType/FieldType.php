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

    private $fieldRelation = Field::NORMAL;

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

    /**
     * @return array|mixed|string
     */
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

            if ($this->fieldRelation == Field::MANY_TO_MANY) {
                // Many to many, Value list
                $keyName = "shared". ucfirst($name) . "List";

                $relatedBeans = $bean->{$keyName};
                $value = [];

                foreach ($relatedBeans as $relatedBean) {
                    $value[$relatedBean->id] = $relatedBean->id;
                }


            } else {
                // Single Value

                if ($this->field->isOverwriteValue() && $this->field->getValue() !== null) {
                    // Use the value set by user.
                    $value = $this->field->getValue();
                } else {
                    // Use the value from Database
                    $value = $bean->{$name};
                }
            }


        }

        return $value;
    }

    /**
     * @return int
     */
    public function getFieldRelation()
    {
        return $this->fieldRelation;
    }

    /**
     * @param int $fieldRelation
     */
    public function setFieldRelation($fieldRelation)
    {
        $this->fieldRelation = $fieldRelation;
    }


}