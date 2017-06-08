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
    protected $prefix = null;
    protected $postfix = null;

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
        $type = $this->type;


        if ($this->prefix == null && $this->postfix == null) {
            $inputGroupOpenTag = "";
            $inputGroupEndTag = "";
        } else {
            $inputGroupOpenTag = "<div class=\"input-group\">";
            $inputGroupEndTag = "</div>";
        }


        if ($this->prefix != null) {
            $prefixHTML = " <span class=\"input-group-addon\" >$this->prefix</span>";
        } else {
            $prefixHTML = "";
        }

        if ($this->postfix != null) {
            $postfixHTML = " <span class=\"input-group-addon\" >$this->postfix</span>";
        } else {
            $postfixHTML = "";
        }

        $html  = <<< EOF
        <div class="form-group">
            <label for="field-$name">$display</label> 
             $inputGroupOpenTag
                $prefixHTML
                <input id="field-$name" class="form-control"  type="$type" name="$name" value="$value" $readOnly $required />
                $postfixHTML
            $inputGroupEndTag
        </div>
EOF;

        if ($echo)
            echo $html;

        return $html;
    }

    /**
     * @param null $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @param null $postfix
     */
    public function setPostfix($postfix)
    {
        $this->postfix = $postfix;
        return $this;
    }



}