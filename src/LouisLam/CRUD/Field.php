<?php

namespace LouisLam\CRUD;

use LouisLam\CRUD\FieldType\CustomField;
use LouisLam\CRUD\FieldType\FieldType;
use LouisLam\CRUD\FieldType\TextField;
use LouisLam\String;
use LouisLam\Util;

/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 3:42 PM
 */
class Field
{

    /** @var LouisCRUD */
    private $crud;
    private $displayName = null;

    /** @var string */
    private $name;

    /** @var string */
    private $dataType = null;

    private $hide = false;

    private $required = false;

    /** @var mixed */
    private $defaultValue = null;

    /**
     * @var FieldType
     */
    private $fieldType = null;

    /**
     * @var bool
     */
    protected $readOnly = false;

    /**
     * @param LouisCRUD $crud
     * @param string $name
     * @param string $dataType
     */
    public function __construct($crud, $name, $dataType)
    {
        $this->crud = $crud;
        $this->name = $name;
        $this->displayName = Util::displayName($name);

        $this->dataType = $dataType;

        if (String::contains($dataType, "varchar")) {
            $this->fieldType = new TextField();
        } else {
            $this->fieldType = new TextField();
        }

        $this->fieldType->setField($this);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

    public function hide()
    {
        $this->hide = true;
    }

    public function show()
    {
        $this->hide = false;
    }

    public function isHidden()
    {
        return $this->hide;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function setRequired($bool)
    {
        $this->required = $bool;
    }

    public function required() {
        $this->required = true;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @param bool|true $echo
     * @return string
     */
    public function render($echo = true)
    {
        return $this->fieldType->render(true);
    }

    public function getBean()
    {
        return $this->crud->getBean();
    }

    public function isEdit()
    {
        return $this->getBean() != null;
    }

    public function isCreate()
    {
        return $this->getBean() == null;
    }

    /**
     * @return FieldType
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * @param FieldType $fieldType
     */
    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;
        $this->fieldType->setField($this);
    }

    /**
     * @param $html
     */
    public function customHTML($html)
    {
        $this->fieldType = new CustomField($this);
        $this->fieldType->setHtml($html);
    }

    public function setReadOnly($yes)
    {
        $this->readOnly = $yes;
    }

    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @param $bean
     * @return string
     */
    public function cellValue($bean)
    {
        return $this->fieldType->renderCell($bean->{$this->getName()});
    }

}