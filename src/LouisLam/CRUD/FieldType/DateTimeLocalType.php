<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use RedBeanPHP\R;

class DateTimeLocalType extends TextField
{
    /**
     * Email constructor.
     */
    public function __construct()
    {
        $this->type = "datetime-local";
    }

    /**
     * Generate DateTime String for datetime-local
     * @param int $timestamp
     * @return string
     */
    public static function getHTMLDateTime($timestamp = null)
    {
        return date("Y-m-d\TH:i", $timestamp);
    }

    public function beforeStoreValue($valueFromUser)
    {
        return R::isoDateTime(strtotime($valueFromUser));
    }

    public function renderCell($value)
    {
        return date("Y-m-d h:i A", strtotime($value));
    }


    public function beforeRenderValue($valueFromDatabase)
    {
        return DateTimeLocalType::getHTMLDateTime(strtotime($valueFromDatabase));
    }

}