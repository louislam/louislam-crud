<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class FileType extends TextField
{

    public function __construct()
    {
        $this->type = "file";
    }

    public function renderCell($value) {
        echo $value;
    }

    public function render($echo = false)
    {
        $name = $this->field->getName();
        $html = parent::render($echo);
        $html .= <<< HTML
    <script>
        $('#field-$name').fileupload({
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('<p/>').text(file.name).appendTo(document.body);
                });
            }
        });
    </script>
HTML;

        return $html;
    }


}