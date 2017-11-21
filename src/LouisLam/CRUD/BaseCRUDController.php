<?php
/**
 * Created by PhpStorm.
 * User: User5
 * Date: 11/17/2017
 * Time: 11:25 AM
 */

namespace LouisLam\CRUD;


abstract class BaseCRUDController
{

    protected $params = [];

    /**
     * @var LouisCRUD
     */
    protected $crud;

    public abstract function main(LouisCRUD $crud);
    public abstract function listView(LouisCRUD $crud);
    public abstract function create(LouisCRUD $crud);
    public abstract function edit(LouisCRUD $crud);

    /**
     * @return LouisCRUD
     */
    public function getCRUD()
    {
        return $this->crud;
    }

    public function setCRUD(LouisCRUD $crud)
    {
        $this->crud = $crud;
    }

    public function setParam($i, $value) {
        $this->params[$i] = $value;
    }

}