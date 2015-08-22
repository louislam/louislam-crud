<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/22/2015
 * Time: 11:40 AM
 */

namespace LouisLam\CRUD;


class AjaxResult
{
    public $draw = 1;
    public $recordsTotal = 1;
    public $recordsFiltered = 1;
    public $data = [];
}