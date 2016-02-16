<?php

namespace LouisLam;

use RedBeanPHP\R;

class Auth
{

    /**
     * @var
     */
    private static $user = null;

    /**
     * @var callable
     */
    private static $encryptPasswordFunction = null;

    public static function login($username, $password) {

        $row = R::getRow("SELECT * FROM `user` WHERE username = ? ", array(
            $username
        ));

        if ($row != null && password_verify($password, $row["password"]) ) {

            $_SESSION["username"] = $username;
            $_SESSION["password"] = $password;
            return true;
        } else {
            unset( $_SESSION["username"]);
            unset($_SESSION["password"] );
            return false;
        }
    }

    public static function setEncryptPasswordFunction($func) {
        Auth::$encryptPasswordFunction = $func;
    }

    public static function getUser($force = false) {

        if (!isset($_SESSION["username"]) || !isset($_SESSION["password"])) {
            return null;
        }

        if ($force || Auth::$user == null) {

            $bean = R::findOne("user", " username = ? ", array(
                $_SESSION["username"]
            ));


            if ($bean != null && password_verify($_SESSION["password"], $bean->password) ) {
                return true;
            } else {
                return false;
            }

        } else {
            return Auth::$user;
        }
    }


    public static function isLoggedIn() {

        if (!isset($_SESSION["username"]) || !isset($_SESSION["password"])) {
            return false;
        }

        $row = Auth::getUser(true);

        if ($row != null) {
            return true;
        } else {
            unset( $_SESSION["username"]);
            unset($_SESSION["password"] );
            return false;
        }
    }

    public static function checkLogin($callback) {
        if (! Auth::isLoggedIn()) {
            $callback();
        }
    }

    public static function logout() {
        unset( $_SESSION["username"]);
        unset($_SESSION["password"] );
    }
}