<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\Exception\DirectoryPermissionException;
use LouisLam\Util;

class Image extends FileType
{
    public function getPreviewHTMLTemplate() {
        return '<img src="{fileURL}" alt="" />';
    }

    public function renderCell($value)
    {
        $imgURL = htmlspecialchars(Util::res($value));

        if ($value != null && $value != "") {
            return <<< HTML
<a target="_blank" href="$imgURL"><img src="$imgURL" alt="" style="max-width: 200px; max-height:70px;"></a>
HTML;
        } else {
            return "";
        }


    }

}
