<?php
/**
 * Created by PhpStorm.
 * User: Louis
 * Date: 6/3/2016
 * Time: 下午 5:20
 */
namespace LouisLam\CRUD;

class FieldTest extends \PHPUnit\Framework\TestCase {
    public function testGetName() {
        $crud = new LouisCRUD();
        $field = new Field($crud, "product_name", "varchar(255)");
        $this->assertEquals("product_name", $field->getName());
    }

    public function testGetFieldType() {
        $crud = new LouisCRUD();
        $field = new Field($crud, "product_name", "varchar(255)");
        $this->assertEquals("LouisLam\\CRUD\\FieldType\\TextField", get_class($field->getFieldType()));
    }

    public function testShowHide() {
        $crud = new LouisCRUD();
        $field = new Field($crud, "product_name", "varchar(255)");

        // Assert Default
        $this->assertEquals(false, $field->isHidden());

        $field->hide();
        $this->assertEquals(true, $field->isHidden());

        $field->show();
        $this->assertEquals(false, $field->isHidden());
    }

    public function testDisplayName() {
        $crud = new LouisCRUD();
        $field = new Field($crud, "product_name", "varchar(255)");
        $field->setDisplayName("!@#$%^&*()_");
        $this->assertEquals("!@#$%^&*()_", $field->getDisplayName());
    }

    public function testRequired() {
        $crud = new LouisCRUD();
        $field = new Field($crud, "product_name", "varchar(255)");

        $this->assertEquals(false, $field->isRequired());

        $field->setRequired(true);
        $this->assertEquals(true, $field->isRequired());

        $field->setRequired(false);
        $this->assertEquals(false, $field->isRequired());
    }

    public function testDefaultValue() {
        $crud = new LouisCRUD();
        $field = new Field($crud, "product_name", "varchar(255)");

        $this->assertEquals(null, $field->getDefaultValue());

        $field->setDefaultValue(true);
        $this->assertEquals(true, $field->getDefaultValue());

        $field->setDefaultValue(null);
        $this->assertEquals(null, $field->getDefaultValue());

        $field->setDefaultValue(1999);
        $this->assertEquals(1999, $field->getDefaultValue());

        $field->setDefaultValue(19.99);
        $this->assertEquals(19.99, $field->getDefaultValue());

        $field->setDefaultValue("1999abc");
        $this->assertEquals("1999abc", $field->getDefaultValue());

        $field->setDefaultValue("中文字");
        $this->assertEquals("中文字", $field->getDefaultValue());
    }

}
