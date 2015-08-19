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
        $defaultValue = $this->field->getDefaultValue();
        $bean = $this->field->getBean();
        $value = "";
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();
        $type = $this->type;

        if ($this->field->isCreate()) {

            // Create Page
            // Use Default Value if not null
            if ($defaultValue !== null) {
                $value = $defaultValue;
            }

        } else {

            // Edit Page
            // Use the value from Database
            $value = $bean->{$name};

        }

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