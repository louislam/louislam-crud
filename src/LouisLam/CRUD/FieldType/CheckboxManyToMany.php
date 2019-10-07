<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\Field;
use RedBeanPHP\R;

class CheckboxManyToMany extends CheckboxList
{
    
    /**
     * @param string $tableName
     * @param callable $nameClosure function ($bean) {}
     * @param string $valueField The field name that used to be value. The default field is "id".
     * @param string $relationTable
     */
    public function __construct($tableName, callable $nameClosure = null, $valueField = "id") {
        $beans = R::findAll($tableName);

        $options = [];

        foreach ($beans as $bean) {

            if ($nameClosure != null) {
                $options[$bean->{$valueField}] = $nameClosure($bean);
            } else {
                $options[$bean->{$valueField}] = $bean->name;
            }
        }

        parent::__construct($tableName, $options);

    }


}