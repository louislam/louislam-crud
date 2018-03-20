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
    protected $attributeList = [];
    protected $classList = [];

    private $fieldRelation = Field::NORMAL;
    protected $customSaveBeanClosure = null;

    public abstract function render($echo = false);

    /**
     * @param Field $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    public function renderCell($value) {
       return htmlspecialchars($value);
    }

    protected function getReadOnlyString() {
        if ($this->field->isReadOnly()) {
            return "readonly";
        } else {
            return "";
        }
    }

    protected function getDisabledString() {
        if ($this->field->isDisabled()) {
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

    protected function getRequiredStar()
    {
        if ($this->field->isRequired()) {
            return "<strong style='color: red;'>*</strong>";
        } else {
            return "";
        }
    }

    /**
     * @return array|string
     */
    public function getValue()
    {
        return $this->field->getRenderValue();
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

    public function addAttribute($key, $value) {
        $this->attributeList[$key] = $value;
    }

    public function removeAttribute($key) {
        unset($this->attributeList[$key]);
    }

    public function addClass($key, $value) {
        $this->classList[$key] = $value;
    }

    public function removeClass($key) {
        unset($this->classList[$key]);
    }

    public function beforeStoreValue($valueFromUser) {
        return $valueFromUser;
    }

    public function beforeRenderValue($valueFromDatabase) {
        return $valueFromDatabase;
    }

    /**
     * @return callable
     */
    public function getCustomSaveBeanClosure()
    {
        return $this->customSaveBeanClosure;
    }

    /**
     * @param callable $customSaveBeanClosure
     */
    public function setCustomSaveBeanClosure($customSaveBeanClosure)
    {
        $this->customSaveBeanClosure = $customSaveBeanClosure;
    }
  
}