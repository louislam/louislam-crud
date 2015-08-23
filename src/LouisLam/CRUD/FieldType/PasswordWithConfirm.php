<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class PasswordWithConfirm extends FieldType
{

    protected $type = "password";

    /**
     * Render Field for Create/Edit
     * @param bool|true $echo
     * @return string
     */
    public function render($echo = false)
    {
        $name = $this->field->getName();
        $display = $this->field->getDisplayName();
        $bean = $this->field->getBean();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();
        $type = $this->type;


        $html  = <<< HTML
        <div class="form-group">
            <label for="field-$name">$display</label> <input id="field-$name" class="form-control"  type="$type" name="$name" value="$value" $readOnly $required />
        </div>

         <div class="form-group">
            <label for="field-$name-confirm">Confirm $display</label> <input id="field-$name-confirm" class="form-control"  type="$type" value="" $readOnly $required />
        </div>

        <script>
                crud.addValidateFunction(function () {
                        if ($("#field-$name-confirm").val() != $("#field-$name").val()) {
                                crud.addErrorMsg("Passwords are not matched.");
                                return false;
                        } else {
                                return true;
                        }
                });
        </script>
HTML;

        if ($echo)
            echo $html;

        return $html;
    }


}