<?php
require "vendor/autoload.php";

// Copy the view folder to vendor
$source = "view";
$dest= "vendor/louislam/louislam-crud/view";

rrmdir("vendor/louislam/louislam-crud/view");
mkdir($dest, 0777, true);
foreach (
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::SELF_FIRST) as $item
) {
    if ($item->isDir()) {
        mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName(), 0777, true);
    } else {
        copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
    }
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir."/".$object))
                    rrmdir($dir."/".$object);
                else
                    unlink($dir."/".$object);
            }
        }
        rmdir($dir);
    }
}