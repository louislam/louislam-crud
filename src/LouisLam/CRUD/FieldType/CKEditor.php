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


    private $fullFeatures = false;

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

        $fullFeatures = ($this->fullFeatures) ? "true" : "false";

        $crud->addScript(<<< HTML

        <script>
        $(document).ready(function () {
                var element = $( 'textarea.editor[name=$name]' );
                var fullFeatures = $fullFeatures;

              var ckConfig = {
                    height: 600,
                    width: "100%",
                    extraPlugins: 'uploadimage',
                    imageUploadUrl: '$uploadURL/json',
                    filebrowserImageUploadUrl: '$uploadURL/js',
                    allowedContent: true
                };

                if (fullFeatures) {
                        ckConfig.plugins = "dialogui,dialog,a11yhelp,about,basicstyles,bidi,blockquote,clipboard," +
"button,panelbutton,panel,floatpanel,colorbutton,colordialog,menu," +
"contextmenu,dialogadvtab,div,elementspath,enterkey,entities,popup," +
"filebrowser,find,fakeobjects,flash,floatingspace,listblock,richcombo," +
"font,format,forms,horizontalrule,htmlwriter,iframe,image,indent," +
"indentblock,indentlist,justify,link,list,liststyle,magicline," +
"maximize,newpage,pagebreak,pastefromword,pastetext,preview,print," +
"removeformat,resize,save,menubutton,scayt,selectall,showblocks," +
"showborders,smiley,sourcearea,specialchar,stylescombo,tab,table," +
"tabletools,templates,toolbar,undo,wsc,wysiwygarea";
                        ckConfig.toolbar = 'Full';

                        ckConfig.toolbar_Full = [
                            { name: 'document', items : [ 'Source','-', 'NewPage','DocProps','Preview','Print','-','Templates' ] },
                            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
                            { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
                            { name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton',
                            'HiddenField' ] },
                            '/',
                            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
                            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
                            '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
                            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
                            { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
                            '/',
                            { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
                            { name: 'colors', items : [ 'TextColor','BGColor' ] },
                            { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
                    ];
                }

            element.ckeditor(ckConfig);

            element.ckeditor().resize("100%");
        });

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

    public function fullFeatures() {
        $this->fullFeatures = true;
    }
}