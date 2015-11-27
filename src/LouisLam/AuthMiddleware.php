<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 11/27/2015
 * Time: 1:47 PM
 */

namespace LouisLam;


class AuthMiddleware extends \Slim\Middleware
{
    public function call()
    {
        $app = $this->app;
        $env = $app->environment;
        $req = $app->request;
        $res = $app->response;

        echo 123213123;
    }
}