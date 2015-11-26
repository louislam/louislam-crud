<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use RedBeanPHP\R;

class ManyToOne extends Dropdown
{

    /**
     * ManyToOne constructor.
     * @param string $tableName
     * @param string $clause
     * @param array $data
     * @param callable $nameClosure
     * @param string $valueField The field name that used to be value. The default field is "id".
     * @param bool $nullOption
     */
    public function __construct( $tableName,  $clause = null,  $data = [],  $nameClosure = null,  $valueField = "id", $nullOption = true) {
        $beans = R::find($tableName, $clause, $data);

        $options = [];

        if ($nullOption) {
            $options["--louislam-crud-null"] = "--";
        }

        foreach ($beans as $bean) {

            if ($nameClosure != null) {
                $options[$bean->{$valueField}] = $nameClosure($bean);
            } else {
                $options[$bean->{$valueField}] = $bean->name;
            }


        }

        parent::__construct($options);
    }

}