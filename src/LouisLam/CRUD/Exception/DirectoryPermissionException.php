<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/14/2015
 * Time: 4:50 PM
 */

namespace LouisLam\CRUD\Exception;


class DirectoryPermissionException extends \Exception
{
    
    /**
     * BeanNotNullException constructor.
     */
    public function __construct($dir)
    {
        parent::__construct("You have no permission to write file in the directory '$dir'.");
    }
}