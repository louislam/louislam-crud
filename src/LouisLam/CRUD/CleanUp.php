<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/18/2015
 * Time: 10:47 AM
 */

namespace LouisLam\CRUD;


use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CleanUp
{

    public static function cleanUp() {
        $dir = 'vendor/almasaeed2010/adminlte/plugins';
        deleteDir($dir);
    }

    public static function deleteDir($dir) {

        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

}