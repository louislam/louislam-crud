<?php

namespace LouisLam\CRUD;

use LouisLam\CRUD\FieldType\CustomField;
use LouisLam\CRUD\FieldType\FieldType;
use LouisLam\CRUD\FieldType\IntegerType;
use LouisLam\CRUD\FieldType\Password;
use LouisLam\CRUD\FieldType\PasswordWithConfirm;
use LouisLam\CRUD\FieldType\TextArea;
use LouisLam\CRUD\FieldType\TextField;
use LouisLam\LouisString;
use LouisLam\Util;

/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 3:42 PM
 */
class Field
{

    /**
     * A normal single value for the bean.
     * Or Many to One.
     * @var int
     */
    const NORMAL = 1;

    /**
     * Many to many
     * @var int
     */
    const MANY_TO_MANY = 2;

    /**
     * One to many
     * @var int
     */
    const ONE_TO_MANY = 3;

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
     * @var bool
     */
    protected $storable = true;

    /**
     * The highest priority value
     * @var null
     */
    private $value = null;
    private $overwriteValue = false;

    /**
     * @var callable
     */
    private $cellClosure = null;

    private $isStorable = true;

    /**
     * Validator Closure
     * @var callable[]
     */
    private $validatorList = [];

    /**
     *
     */
    private $isUnique = false;

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

        if (LouisString::contains($dataType, "varchar")) {
            $this->fieldType = new TextField();
        } else if (LouisString::contains($dataType, "int")) {
            $this->fieldType = new IntegerType();
        } else if (LouisString::contains($dataType, "text")) {
                $this->fieldType = new TextArea();
        } else {
            $this->fieldType = new TextField();
        }

        $this->fieldType->setField($this);
    }

    /**
     * Get Field Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

    public function hide()
    {
        $this->hide = true;
        return $this;
    }

    public function show()
    {
        $this->hide = false;
        return $this;
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

    public function setDisplayName($name) {
        $this->displayName = $name;
        return $this;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function setRequired($bool)
    {
        $this->required = $bool;
        return $this;
    }

    public function required() {
        $this->required = true;
        return $this;
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
        return $this;
    }

    /**
     * @param bool|true $echo
     * @return string
     */
    public function render($echo = true)
    {
        return $this->fieldType->render($echo);
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
     * @param FieldType|string $fieldType
     * @return Field
     * @throws \ErrorException
     */
    public function setFieldType($fieldType)
    {

        // Overloading
        if ($fieldType instanceof FieldType) {
            // If FieldType

            $this->fieldType = $fieldType;


        } else {
            // If String

            switch ($fieldType) {
                case "password":
                    $this->fieldType = new Password();
                    break;
                case "confirm_password":
                    $this->fieldType = new PasswordWithConfirm();
                    break;
                default:
                    throw new \ErrorException("Unsupported field type.");
            }

        }

        if ($this->fieldType != null)
            $this->fieldType->setField($this);

        return $this;
    }

    /**
     * @param $html
     * @return string
     */
    public function customHTML($html)
    {
        $this->fieldType = new CustomField($this);
        $this->fieldType->setHtml($html);
        return $this;
    }

    public function setReadOnly($yes)
    {
        $this->readOnly = $yes;
        return $this;
    }

    /**
     * The value can be stored into the current bean if it is false.
     * @return bool
     */
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

        try {

            if ($this->fieldType->getFieldRelation() == Field::NORMAL) {

                if (isset($bean->{$this->getName()})) {
                    $value = $this->fieldType->renderCell($bean->{$this->getName()});
                } else {
                    $value = "";
                }


            } else {

                //TODO: Show a few items
                $value = "Click 'Edit' to view select item(s).";
            }

        } catch (\Exception $ex) {
            $value = "N/A";
        }

        if ($this->cellClosure != null) {
            $c = $this->cellClosure;
            return $c($value, $bean);
        } else {
            return $value;
        }

    }

    /**
     * Get the value that will be inserted into the database
     * @param array $data Most likely refer to $_POST
     * @return null
     */
    public function getStoreValue($data = null)
    {

        // If Overrite Value
        if (isset($this->value) && $this->overwriteValue) {
            return $this->value;
        }

        // If user have user input value
        if ($data != null && isset($data[$this->getName()])) {

            // Process the value by FieldType
            // For example, HTML5's datetime-local is unable insert into the database directly. So the DateTimeLocal have to convert it to the proper format.
            return $this->fieldType->beforeStoreValue($data[$this->getName()]);
        }

        return $this->value;
    }


    /**
     * GEt the value that will be rendered on HTML page
     * @return array|mixed|null|string
     */
    public function getRenderValue()
    {
        $name = $this->getName();
        $defaultValue = $this->getDefaultValue();
        $bean = $this->getBean();
        $value = "";

        if ($this->isCreate()) {

            // Create Page
            // Use Default Value if not null
            if ($this->value !== null) {
                $value = $this->value;
            } else if ($defaultValue !== null) {
                $value = $defaultValue;
            }

        } else {

            // Edit Page
            if ($this->getFieldRelation() == Field::MANY_TO_MANY) {
                // Many to many, Value list
                $keyName = "shared". ucfirst($name) . "List";

                $relatedBeans = $bean->{$keyName};
                $value = [];

                foreach ($relatedBeans as $relatedBean) {
                    $value[$relatedBean->id] = $relatedBean->id;
                }

            } else {

                // Single Value
                if ($this->isOverwriteValue() && $this->value !== null) {
                    // Use the value set by user.
                    $value = $this->value;
                } else {
                    // Use the value from Database
                    $value = $this->getFieldType()->beforeRenderValue($bean->{$name});
                }
            }

        }

        return $value;
    }

    /**
     * @param int|string $value
     * @param bool $force
     */
    public function setValue($value, $force = false)
    {
        $this->value = $value;
        $this->overwriteValue = $force;

        return $this;
    }

    /**
     * @return mixed
     */
    public function isOverwriteValue()
    {
        return $this->overwriteValue;
    }

    /**
     * @return LouisCRUD
     */
    public function getCRUD()
    {
        return $this->crud;
    }

    /**
     * @param callable $cellClosure
     */
    public function setCellHTML($cellClosure)
    {
        $this->cellClosure = $cellClosure;
        return $this;
    }


    public function getFieldRelation()
    {
        return $this->getFieldType()->getFieldRelation();
    }



    /**
     * Is the field storable to the current table.
     *
     * 1.  Is not read only.
     * 2. Is not hidden field (Clarify: This is not equals to HTML's hidden, it's hide() ).
     * 3. Is no special relation.
     * @return bool
     */
    public function isStorable()
    {
        return
            ! $this->isReadOnly() &&
            ! $this->isHidden() &&
            $this->getFieldRelation() == Field::NORMAL &&
            $this->isStorable;
    }

    public function setStorable($bool) {
        $this->isStorable = $bool;
        return $this;
    }

    /**
     * @param $closure
     */
    public function addValidator($closure) {
        $this->validatorList[] = $closure;
    }


    /**
     * Validate Before INSERT/UPDATE
     * @param $value
     * @param $postData
     * @return bool
     */
    public function validate($value, $postData) {
        foreach ($this->validatorList as $validator) {
            $result = $validator($value, $postData);

            if ($result !== true) {
                return $result;
            }
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function isUnique()
    {
        return $this->isUnique;
    }

    /**
     * @param mixed $isUnique
     */
    public function setUnique($isUnique)
    {
        $this->isUnique = $isUnique;
    }



}