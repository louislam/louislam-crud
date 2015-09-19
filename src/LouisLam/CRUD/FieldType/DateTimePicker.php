<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class DateTimePicker extends TextField
{
    /**
     * Email constructor.
     */
    public function __construct()
    {

    }

    public function render($echo = false)
    {
        $name = $this->field->getName();
        $html = parent::render($echo);

        $html .= <<< HTML
           <script type="text/javascript">
            $(function () {
                $('#field-$name').datetimepicker({
                        format: "YYYY-MM-DD hh:mm A"
                });
            });
        </script>
HTML;

        return $html;
    }


    public function beforeRenderValue($valueFromDatabase)
    {
        return date("Y-m-d h:i A", strtotime($valueFromDatabase));
    }

}