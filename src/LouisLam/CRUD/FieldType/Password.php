<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class Password extends TextField
{

    public function __construct()
    {
        $this->type = "password";
    }

    public function renderCell($value) {
        return "***";
    }
}