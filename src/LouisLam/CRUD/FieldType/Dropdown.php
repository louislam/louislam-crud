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

    protected $class = "";

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
     * @param bool $val No Use
     */
    public function enableSelect2($val = null) {
        $this->class = "select2";
    }

    /**
     * @param bool $val
     */
    public function enableBootstrapSelect() {
        $this->class = "selectpicker";
    }

    public function disableLibrary() {
        $this->class = "";
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
        $class = $this->class;

        $html = <<<TAG
<label for="field-$name" >$display</label>
TAG;

        // data-live-search="true"   Search box for bootstrap-select

        $html .= <<<TAG
<select id="field-$name" name="$name" $required $readOnly $disabled class="form-control $class" data-live-search="true">
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
