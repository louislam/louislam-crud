<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/15/2015
 * Time: 11:01 PM
 */

namespace LouisLam\CRUD\Exception;


class NoFieldException extends \Exception
{
    
    /**
     * NoFieldException constructor.
     */
    public function __construct()
    {
        parent::__construct("There is no field for the table. Please add a field in your database or you can use showFields().");
    }
}