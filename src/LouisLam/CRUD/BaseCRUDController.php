<?php
/**
 * Created by PhpStorm.
 * User: User5
 * Date: 11/17/2017
 * Time: 11:25 AM
 */

namespace LouisLam\CRUD;

abstract class BaseCRUDController {
    protected $params = [];

    /**
     * @var LouisCRUD
     */
    protected $crud;

    /**
     * @param LouisCRUD $crud
     */
    abstract public function main($crud);

    /**
     * @param LouisCRUD $crud
     */
    abstract public function listView($crud);

    /**
     * @param LouisCRUD $crud
     */
    abstract public function create($crud);

    /**
     * @param LouisCRUD $crud
     */
    abstract public function edit($crud);

    /**
     * @return LouisCRUD
     */
    public function getCRUD() {
        return $this->crud;
    }

    public function setCRUD(LouisCRUD $crud) {
        $this->crud = $crud;
    }

    public function setParam($i, $value) {
        $this->params[$i] = $value;
    }
}
