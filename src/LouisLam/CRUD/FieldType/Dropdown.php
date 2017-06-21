<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\LouisCRUD;

class Dropdown extends FieldType
{

    /**
     * @var string[]
     */
    private $options;

    /**
     * RadioButton constructor.
     * @param string[] $options
     */
    public function __construct($options) {
        $this->options = $options;
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
        $bean = $this->field->getBean();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $disabled = $this->getDisabledString();
        $required = $this->getRequiredString();

        $html = <<<TAG
<label for="field-$name" >$display</label>
TAG;

        $html .= <<<TAG
<select id="field-$name" name="$name" $required $readOnly $disabled class="form-control">
TAG;

        foreach ($this->options as $v =>$optionName) {

            if ($value == $v) {
                $selected  = "selected";
            } else {
                $selected = "";
            }

            if ($this->field->isRequired() && $v == LouisCRUD::NULL) {
                $v = "";
            }

            $html  .= <<< EOF
     <option value="$v" $selected> $optionName</option>
EOF;
        }

        $html .= "</select><br />";

        if ($echo)
            echo $html;

        return $html;
    }

    public function renderCell($value) {
        try {
            return $this->options[$value];
        } catch (\ErrorException $ex) {
            return $value;
        }
    }

}
