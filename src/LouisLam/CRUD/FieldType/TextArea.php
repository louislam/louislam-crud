<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class TextArea extends FieldType
{

    /**
     * Render Field for Create/Edit
     * @param bool|true $echo
     * @return string
     */
    public function render($echo = false)
    {
        $name = $this->field->getName();
        $display = $this->field->getDisplayName();
        $defaultValue = $this->field->getDefaultValue();
        $bean = $this->field->getBean();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();


        $html  = <<< EOF
        <label for="field-$name">$display</label><textarea class="form-control" id="field-$name" name="$name" $readOnly $required>$value</textarea>
EOF;

        if ($echo)
            echo $html;

        return $html;
    }


}