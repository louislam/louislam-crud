<?php

namespace LouisLam;

class Auth {
    /**
     * @var AuthBasic
     */
    private static $authLogic = null;

    /**
     * @return AuthBasic
     */
    public static function getAuthLogic() {
        if (self::$authLogic == null) {
            self::$authLogic = new LouisAuth();
        }

        return self::$authLogic;
    }

    /**
     * @param AuthBasic $authLogic
     */
    public static function setAuthLogic($authLogic) {
        self::$authLogic = $authLogic;
    }

    /**
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public static function login($username, $password) {
        return self::getAuthLogic()->login($username, $password);
    }

    public static function setEncryptPasswordFunction($func) {
        self::getAuthLogic()->setEncryptPasswordFunction($func);
    }

    /**
     * @param bool $force
     * @return mixed
     */
    public static function getUser($force = false) {
        return self::getAuthLogic()->getUser($force);
    }

    public static function isLoggedIn() {
        return self::getAuthLogic()->isLoggedIn();
    }

    public static function checkLogin($callback) {
        self::getAuthLogic()->checkLogin($callback);
    }

    public static function logout() {
        self::getAuthLogic()->logout();
    }
}
