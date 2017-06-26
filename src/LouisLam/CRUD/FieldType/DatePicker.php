<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 * Credit: KKson
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\LouisCRUD;

class DatePicker extends TextField
{

    public function __construct($crud = null)
    {

    }

    public function render($echo = false)
    {
        $name = $this->field->getName();
        $html = parent::render($echo);

        $this->field->getCRUD()->addBodyEndHTML(<<< HTML
           <script type="text/javascript">
            $(function () {
                $('#field-$name').addClass("datepicker").datepicker({
                    format: "yyyy-mm-dd"
                });
            });
        </script>
HTML
);
        return $html;
    }


    public function beforeRenderValue($valueFromDatabase)
    {
        return date("Y-m-d", strtotime($valueFromDatabase));
    }

}