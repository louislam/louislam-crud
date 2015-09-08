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

class Register extends Route
{

    /**
     * RegisterRoute constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->routeName = "register";
        $this->tableName = "member";
    }


    /**
     * @param $crud SlimLouisCRUD
     */
    public function route($crud)
    {

        // Enable Create View only
        $crud->enableCreate(true);
        $crud->enableDelete(false);
        $crud->enableListView(false);
        $crud->enableEdit(false);

        // Remove Layout
        $crud->setLayout("register");

        $crud->showFields([
           "username",
            "password",
            "email"
        ]);

        field("password")->setFieldType("password");

    }

}