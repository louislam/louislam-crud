<?php
/**
 * Created by PhpStorm.
 * User: User5
 * Date: 10/13/2017
 * Time: 3:34 PM
 */

namespace LouisLam;

use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

class LouisAuth extends AuthBasic {
    /**
     * @var OODBBean
     */
    private $user = null;


    public function login($username, $password) {
        $row = R::getRow("SELECT * FROM `user` WHERE username = ? ", [
            $username
        ]);

        if ($row != null && password_verify($password, $row["password"])) {
            $_SESSION["username"] = $username;
            $_SESSION["password"] = $row["password"];
            return true;
        } else {
            unset($_SESSION["username"]);
            unset($_SESSION["password"]);
            return false;
        }
    }

    public function getUser($force = false) {
        if (!isset($_SESSION["username"]) || !isset($_SESSION["password"])) {
            return null;
        }

        if ($force || $this->user == null) {
            $bean = R::findOne("user", " username = ? ", [
                $_SESSION["username"]
            ]);

            if ($bean != null && $_SESSION["password"] === $bean->password) {
                $this->user = $bean;
                return $this->user;
            } else {
                return null;
            }
        } else {
            return $this->user;
        }
    }

    public function isLoggedIn() {
        if (!isset($_SESSION["username"]) || !isset($_SESSION["password"])) {
            return false;
        }

        $row = $this->getUser(true);

        if ($row != null) {
            return true;
        } else {
            unset($_SESSION["username"]);
            unset($_SESSION["password"]);
            return false;
        }
    }

    public function checkLogin($callback) {
        if (! $this->isLoggedIn()) {
            $callback();
        }
    }

    public function logout() {
        unset($_SESSION["username"]);
        unset($_SESSION["password"]);
    }
}
