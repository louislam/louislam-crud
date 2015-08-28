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
     * @param callable $nameClosure
     */
    public function __construct($tableName, callable $nameClosure = null) {
        $beans = R::findAll($tableName);

        $options = [];

        foreach ($beans as $bean) {
            if ($nameClosure != null) {
                $options[$bean->id] = $nameClosure($bean);
            } else {
                $options[$bean->id] = $bean->name;
            }
        }

        parent::__construct($tableName, $options);

    }


}