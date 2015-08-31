<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/31/2015
 * Time: 10:17 AM
 */

use LouisLam\CRUD\LouisCRUD;

if (defined("ENABLE_CRUD_HELPER") && ENABLE_CRUD_HELPER) {

    /**
     * @var $globalCRUD LouisLam\CRUD\LouisCRUD
     */
    $globalCRUD = null;

    /**
     * @param $crud LouisLam\CRUD\LouisCRUD
     */
    function setGlobalCRUD($crud) {
        global $globalCRUD;
        $globalCRUD = $crud;
    }

    /**
     * @param $fieldName
     * @return \LouisLam\CRUD\Field
     */
    function f($fieldName) {
        global $globalCRUD;
        return $globalCRUD->field($fieldName);
    }

}