<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 9/8/2015
 * Time: 10:30 AM
 */

namespace LouisLam\CRUD\Route;


use LouisLam\CRUD\Route;
use LouisLam\CRUD\SlimLouisCRUD;

class RegisterRoute extends Route
{
    
    /**
     * @param $crud SlimLouisCRUD
     */
    public function route($crud)
    {
        $this->routeName = "register";
        $this->tableName = "member";

    }

}