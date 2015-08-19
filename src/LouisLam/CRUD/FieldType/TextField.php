<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class TextField extends FieldType
{

    protected $type = "text";

    /**
     * Render Field for Create/Edit
     * @param bool|true $echo
     * @return string
     */
    public function render($echo = true)
    {
        $name = $this->field->getName();
        $display = $this->field->getDisplayName();
        $bean = $this->field->getBean();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();
        $type = $this->type;


        $html  = <<< EOF
        <div class="form-group">
            <label for="field-$name">$display</label> <input id="field-$name" class="form-control"  type="$type" name="$name" value="$value" $readOnly $required />
        </div>
EOF;

        if ($echo)
            echo $html;

        return $html;
    }


}