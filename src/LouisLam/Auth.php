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

        if (Auth::$encryptPasswordFunction != null) {
            $encrypt = Auth::$encryptPasswordFunction;
            $password = $encrypt($password);
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT);
        }

        $row = R::getRow("SELECT * FROM `user` WHERE username = ? AND password = ?", array(
            $username, $password
        ));

        if ($row != null) {
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
            return R::findOne("user", " username = ? AND password = ? ", array(
                $_SESSION["username"],
                $_SESSION["password"]
            ));
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