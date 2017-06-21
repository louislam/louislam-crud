<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class TimePicker extends TextField
{
    /**
     * Email constructor.
     */
    public function __construct()
    {

    }

    public function render($echo = false)
    {
        $this->setPostfix("<i class=\"glyphicon glyphicon-time\"></i>");

        $name = $this->field->getName();

        return parent::render($echo);
    }


    public function beforeRenderValue($valueFromDatabase)
    {
        return date("Y-m-d h:i A", strtotime($valueFromDatabase));
    }

}