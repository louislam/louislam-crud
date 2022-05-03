<?php
/*
 * http://localhost:3000/example.php/crud/book
 */
require "vendor/autoload.php";

use LouisLam\CRUD\SlimLouisCRUD;
use RedBeanPHP\R;

SlimLouisCRUD::$isDev = true;

R::setup("sqlite:tmp/dbfile.db");

$crud = new SlimLouisCRUD();

$crud->add("user", function () use ($crud) {
    $crud->showFields(["id", "name", "password", "email"]);
});

$crud->add("book", function () use ($crud) {
    $crud->showFields(["id", "title", "date"]);
});

$crud->getSlim()->get("/test(/:p1)(/:p2)", function ($p1 = null, $p2 = null) {
    echo $p1;
    echo $p2;
});

/*
 * 4. Run the application
 */
$crud->run();
