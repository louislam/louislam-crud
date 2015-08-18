<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class Hidden extends TextField
{
    /**
     * Email constructor.
     */
    public function __construct()
    {
        $this->type = "hidden";
    }

}