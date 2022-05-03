<?php
/**
 * Created by PhpStorm.
 * User: User5
 * Date: 10/13/2017
 * Time: 3:34 PM
 */

namespace LouisLam;

abstract class AuthBasic {
    /**
     * @var callable
     */
    protected $encryptPasswordFunction = null;

    /**
     * @param string $username
     * @param string $password
     * @return mixed
     */
    abstract public function login($username, $password);

    /**
     * @param bool $force
     * @return mixed
     */
    abstract public function getUser($force = false);

    /**
     * @return boolean
     */
    abstract public function isLoggedIn();

    /**
     * @param callable $callback
     * @return void
     */
    abstract public function checkLogin($callback);

    /**
     * @return void
     */
    abstract public function logout();

    public function setEncryptPasswordFunction($func) {
        $this->encryptPasswordFunction = $func;
    }
}
