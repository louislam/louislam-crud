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

        $html  = <<< EOF
        <label>$display <textarea class="editor" name="$name" $readOnly $required>$value</textarea></label>
        <script>
                $( 'textarea.editor[name=$name]' ).ckeditor( {
                    extraPlugins: 'uploadimage',
                    imageUploadUrl: '/uploader/upload.php?type=Images'
                } );
        </script>
EOF;

        if ($echo)
            echo $html;

        return $html;
    }

    public static function upload() {

        $output = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction(' . $callback . ', "' . $image_url . '","' . $msg . '");</script>';
        echo $output;
    }

}