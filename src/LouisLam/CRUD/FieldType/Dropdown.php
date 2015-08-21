<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


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
    public function render($echo = true)
    {
        $name = $this->field->getName();
        $display = $this->field->getDisplayName();
        $bean = $this->field->getBean();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();

        $html = <<<TAG
<label for="field-$name" name="$name" $required $readOnly>$display</label>
TAG;

        $html .= <<<TAG
<select name>
TAG;

        foreach ($this->options as $v =>$optionName) {

            if ($value == $v) {
                $selected  = "selected";
            } else {
                $selected = "";
            }

            $html  .= <<< EOF
     <option value="$v"   $selected /> $optionName</option>
EOF;
        }

        $html .= "</select>";

        if ($echo)
            echo $html;

        return $html;
    }

    public function renderCell($value) {
        return $this->options[$value];
    }

}

?>

