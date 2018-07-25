<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class IPType extends FieldType
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
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();

        $html  = <<< HTML

        <div class="form-group">
            <label  for="field-$name">$display</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fa fa-laptop"></i>
              </div>
              <input id="field-$name" type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask="" name="$name" value="$value" $readOnly $required >
            </div>
        </div>

HTML;

        if ($echo)
            echo $html;

        return $html;
    }


}