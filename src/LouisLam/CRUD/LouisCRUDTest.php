<?php

/**
 * Created by PhpStorm.
 * User: louis_000
 * Date: 2/3/2016
 * Time: 4:07 PM
 */
class LouisCRUDTest extends  \PHPUnit\Framework\TestCase {

    public function testGetBean() {
        $crud = new \LouisLam\CRUD\LouisCRUD();
        $this->assertEquals(null, $crud->getBean());
    }

    public function testGetSQL() {
        $crud = new \LouisLam\CRUD\LouisCRUD();
        $this->assertEquals(null, $crud->getSQL());
    }

    public function testGetSQL2() {
        $crud = new \LouisLam\CRUD\LouisCRUD();
        $crud->setSQL("SELECT * FROM `user`");
        $this->assertEquals("SELECT * FROM `user`", $crud->getSQL());
    }

}
