<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use RedBeanPHP\R;

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
        $this->field->getCRUD()->addBodyEndHTML(<<< HTML
            <script type="text/javascript">
                $(function () {
                    $('#field-$name').daterangepicker({
                        singleDatePicker: true,
                        timePicker: true,
                        timePickerIncrement: 30,
                        locale: {
                            format: 'YYYY-MM-DD hh:mm A'
                        }
                    });
                });
            </script>
HTML
);
        return parent::render($echo);
    }


    public function beforeRenderValue($valueFromDatabase)
    {
        return date("Y-m-d h:i A", strtotime($valueFromDatabase));
    }

    public function beforeStoreValue($valueFromUser)
    {
        return R::isoDateTime(strtotime($valueFromUser));
    }


}