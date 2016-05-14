<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\Exception\DirectoryPermissionException;
use LouisLam\Util;

class Camera extends FieldType
{

    private $uploadPath;

    public function __construct($uploadPath = "upload/")
    {
        $this->type = "file";
        $this->setUploadPath($uploadPath);

        // Create a directory for Upload Path
        if (! file_exists($this->getUploadPath())) {
            mkdir($this->getUploadPath(), 0777);
        } else {
            //chmod($this->getUploadPath(), 0777);
        }

        // Check the directory permission
        if (! is_writable($this->getUploadPath())) {
            throw new DirectoryPermissionException($this->getUploadPath());
        }
    }


    public function render($echo = false)
    {
        $name = $this->field->getName();
        $display = $this->field->getDisplayName();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();
        $crud = $this->field->getCRUD();

        $uploadURL = Util::url("louislam-crud/upload/json?fullpath=no");

        if ($value != "" && $value != null) {
            $imgURL = Util::res($value);
            $imgTag = <<< HTML
<img src="$imgURL" alt="" />
HTML;
            $hideRemoveButton = "";
        } else {
            $imgTag = "";
            $hideRemoveButton = 'style="display: none"';
        }

        $html = <<< HTML
<div class="form-group">
    <label for="upload-$name">$display</label>

    <input id="upload-$name" class="form-control" type="file" $readOnly $required data-required="$required"  accept="image/*;capture=camera" />
    <input id="field-$name" type="hidden" name="$name" value="$value"  />

    <div id="image-preview-$name" class="image-preview">
                $imgTag
        </div>

        <a id="image-remove-$name" href="javascript:void(0)" class="btn btn-danger" $hideRemoveButton>Remove Image</a>
   <br/>   <br/>
HTML;

        $crud->addScript(<<< HTML

        <!-- Remove Required for the upload field -->
    <script>
     $("#upload-$name").removeAttr("required");
    </script>

    <script>

        $("#image-remove-$name").click(function () {
                $("#image-preview-$name").html("");
                $("#field-$name").val("");

                var required = $("#upload-$name").data("required");

                if (required == "required") {
                         $("#upload-$name").attr("required", true);
                }

                $(this).hide();
        });

       $("#upload-$name").change(function () {

            if ($(this).val() == "") {
                return;
            }

           var data = new FormData();

            jQuery.each($(this)[0].files, function(i, file) {
                data.append("upload", file);
            });

             $.ajax({
                url: '$uploadURL',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(data){
                        var img = $("<img alt=\"\" />");
                        img.attr("src", RES_URL + data.url);

                        $("#image-preview-$name").html(img);
                        $("#field-$name").val(data.url);
                        $("#image-remove-$name").show();

                         $("#upload-$name").removeAttr("required");
                }
            });
       });
    </script>

HTML
        );

        if ($echo)
            echo $html;

        return $html;
    }

    /**
     * @return string
     */
    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    /**
     * @param string $uploadPath
     */
    public function setUploadPath($uploadPath)
    {
        // TODO: Append slash if no end slash
        $this->uploadPath = $uploadPath;
    }
    
    public function renderCell($value)
    {
        $imgURL = Util::res($value);

        if ($value != null && $value != "") {
            return <<< HTML
<a target="_blank" href="$imgURL"><img src="$imgURL" alt="" style="max-width: 200px; max-height:70px;"></a>
HTML;
        } else {
            return "";
        }


    }
    
    
}