<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 9/8/2015
 * Time: 10:30 AM
 */

namespace LouisLam\CRUD\Route;


use LouisLam\CRUD\FieldType\Email;
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

        // Set Layout
        $crud->setLayout("register");

        $crud->showFields([
            "name",
            "email",
            "password"
        ]);

        $crud->requiredFields([
            "name",
            "email",
            "password"
        ]);

        $crud->hideFields([
           "active"
        ]);

        field("password")->setFieldType("confirm_password");
        field("email")->setFieldType(new Email());
    }

}