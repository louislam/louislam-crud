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
     * @var callback
     */
    private $encryptionClosure = null;

    public function __construct()
    {

        // Use MD5 by default
        $this->encryptionClosure = function ($v) {
            return password_hash($v, PASSWORD_DEFAULT);
        };

    }

    /**
     * Render Field for Create/Edit
     * @param bool|true $echo
     * @return string
     */
    public function render($echo = false)
    {
        $name = $this->field->getName();
        $display = $this->field->getDisplayName();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();
        $type = $this->type;
        $crud = $this->field->getCRUD();

        $html = <<< HTML
        <div class="form-group">
            <label for="field-$name">$display</label> <input id="field-$name" class="form-control"  type="$type" name="$name" value="" $readOnly $required />
        </div>

         <div class="form-group">
            <label for="field-$name-confirm">Confirm $display</label> <input id="field-$name-confirm" class="form-control"  type="$type" value="" $readOnly $required />
        </div>

HTML;

        $crud->addScript(<<< HTML
<script>
    $(document).ready(function () {
        crud.addValidator(function (data) {
            if ($("#field-$name-confirm").val() != $("#field-$name").val()) {
                crud.addErrorMsg("Passwords are not matched.");
                return false;
            } else {
                return true;
            }
        });
    });
</script>
HTML
        );

        if ($echo) {
            echo $html;
        }

        return $html;
    }

    /**
     * @param callback $c function ($value) { return md5($value); }
     */
    public function setEncryptionClosure($c)
    {
        $this->encryptionClosure = $c;
    }

    public function beforeStoreValue($valueFromUser)
    {
        if ($valueFromUser == "") {
            return $this->field->getBean()->{$this->field->getName()};
        }

        $c = $this->encryptionClosure;
        return $c($valueFromUser);
    }


}