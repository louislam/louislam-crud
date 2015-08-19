<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class RadioButton extends FieldType
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
    public function render($echo = true)
    {
        $name = $this->field->getName();
        $display = $this->field->getDisplayName();
        $bean = $this->field->getBean();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();



        $html = "<label>$display</label>";

        foreach ($this->options as $v =>$optionName) {

            if ($value == $v) {
                $selected  = "checked";
            } else {
                $selected = "";
            }


            $html  .= <<< EOF
        <div class="radio">
        <label><input type="radio" name="$name" value="$v" $readOnly $required $selected /> $optionName</label>
        </div>
EOF;
        }

        if ($echo)
            echo $html;

        return $html;
    }

    public function renderCell($value) {
        return $this->options[$value];
    }


}

?>

