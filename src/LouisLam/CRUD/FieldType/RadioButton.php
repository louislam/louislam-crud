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
        $defaultValue = $this->field->getDefaultValue();
        $bean = $this->field->getBean();
        $value = "";
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();

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

        $html = $display;

        foreach ($this->options as $value =>$optionName) {
            $html  = <<< EOF
        <label><input type="radio" name="$name" value="$value" $readOnly $required /> $optionName</label>
EOF;
        }

        if ($echo)
            echo $html;

        return $html;
    }


}

?>

