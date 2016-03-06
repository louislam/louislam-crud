<?php
/**
 * Created by PhpStorm.
 * User: Louis
 * Date: 6/3/2016
 * Time: 下午 5:20
 */

namespace LouisLam\CRUD;


class FieldTest extends \PHPUnit_Framework_TestCase
{

    
    public function testGetName()
    {
        $crud = new LouisCRUD();
        $field = new Field($crud, "product_name", "varchar(255)");
        $this->assertEquals("product_name", $field->getName());
    }

    public function testGetFieldType()
    {
        $crud = new LouisCRUD();
        $field = new Field($crud, "product_name", "varchar(255)");
        $this->assertEquals("LouisLam\\CRUD\\FieldType\\TextField", get_class($field->getFieldType()));
    }

    public function testHideType() {

    }


}
