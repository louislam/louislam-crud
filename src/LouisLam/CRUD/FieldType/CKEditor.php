<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class CKEditor extends FieldType
{
    // TODO: Check 777 for uploading images

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
        $crud = $this->field->getCRUD();

        $uploadURL = \LouisLam\Util::url("louislam-crud/upload");

        $html  = <<< HTML
        <label for="field-$name">$display </label>
        <textarea id="field-$name" class="editor" name="$name" $readOnly $required style="width:100%">$value</textarea>
HTML;

        $crud->addScript(<<< HTML
        <script>
             var element = $( 'textarea.editor[name=$name]' );

            element.ckeditor( {
                height: 600,
                width: "100%",
                extraPlugins: 'uploadimage',
                imageUploadUrl: '$uploadURL/json',
                filebrowserImageUploadUrl: '$uploadURL/js'
            } );

            element.ckeditor().resize("100%");
        </script>
HTML
);

        if ($echo)
            echo $html;

        return $html;
    }

    public static function upload() {

        $output = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction(' . $callback . ', "' . $image_url . '","' . $msg . '");</script>';
        echo $output;
    }


    public function renderCell($value)
    {
        $value = trim(strip_tags($value));
        return mb_strimwidth($value, 0, 60, "...", "UTF-8");
    }

}