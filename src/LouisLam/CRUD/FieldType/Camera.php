<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\Exception\DirectoryPermissionException;
use LouisLam\Util;

class Camera extends Image
{
    protected $additionalAttr = "accept=\"image/*;capture=camera\"";
}