<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\Field;
use RedBeanPHP\R;

class CheckboxList extends FieldType
{

    /**
     * @var string[]
     */
    protected $options;

    protected $tableName;

    /**
     * @param string $tableName
     * @param string[] $options
     * @param callable $nameClosure
     */
    public function __construct($tableName, array $options = null) {
        $this->tableName = $tableName;
        $this->options = $options;
        $this->setFieldRelation(Field::MANY_TO_MANY);
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
        $valueList = $this->getValue();

        $readOnly = $this->getDisabledString();
        $required = $this->getRequiredString();

        $html = <<<TAG
<label for="field-$name" >$display</label>
TAG;

        $html .= <<<TAG
       <div class="form-group checkboxes-group">
TAG;

        foreach ($this->options as $v =>$optionName) {

            if (isset($valueList[$v])) {
                $selected  = "checked";
            } else {
                $selected = "";
            }

            $nameAttr = 'name="'. $name .'[]"';

            $html  .= <<< HTML
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="$v" $nameAttr $required $readOnly $selected /> $optionName
                    </label>
                </div>
HTML;
        }

        $html .= " </div><br />";

        if ($echo)
            echo $html;

        return $html;
    }

    public function renderCell($value) {

        print_r($value);
        try {
            return $this->options[$value];
        } catch (\ErrorException $ex) {
            return $value;
        }
    }



}