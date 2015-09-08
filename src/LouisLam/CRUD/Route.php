<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 9/8/2015
 * Time: 9:58 AM
 */

namespace LouisLam\CRUD;


use ReflectionClass;

abstract class Route
{

    /**
     * @var SlimLouisCRUD
     */
    protected $crud;

    protected $isEnabled = true;
    protected $routeName;
    protected $tableName = null;
    protected $displayName = null;

    /**
     * Route constructor.
     */
    public function __construct()
    {
        $r = new \ReflectionClass($this);
        $this->routeName = strtolower($r->getShortName());

    }

    /**
     * @param $crud SlimLouisCRUD
     */
    public abstract function route($crud);


    public function addToCRUD() {
        $this->crud->add($this->routeName, function ()  {
                $this->route($this->crud);
        }, $this->tableName, $this->displayName);
    }

    /**
     * @return SlimLouisCRUD
     */
    public function getCRUD()
    {
        return $this->crud;
    }

    /**
     * @param SlimLouisCRUD $crud
     */
    public function setCRUD($crud)
    {
        $this->crud = $crud;
    }



}