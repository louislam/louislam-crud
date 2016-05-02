<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class Password extends TextField
{

    /**
     * @var callback
     */
    private $encryptionClosure = null;

    public function __construct()
    {
        $this->type = "password";

        // Use MD5 by default
        $this->encryptionClosure = function ($v) {
            return password_hash($v, PASSWORD_DEFAULT);
        };
    }

    public function renderCell($value)
    {
        return "***";
    }

    public function beforeStoreValue($valueFromUser)
    {
        $c = $this->encryptionClosure;

        return $c($valueFromUser);
    }
}