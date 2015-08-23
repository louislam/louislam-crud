<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class FileType extends FieldType
{

    public function __construct()
    {
        $this->type = "file";

    }

    public function renderCell($value) {
        echo $value;
    }

    public function render($echo = false)
    {
        $name = $this->field->getName();
        $display = $this->field->getDisplayName();
        $value = $this->getValue();
        $readOnly = $this->getReadOnlyString();
        $required = $this->getRequiredString();
        $type = $this->type;

        $html = <<< HTML
<div class="form-group">
    <label for="field-$name">$display</label>
    <input id="field-$name" class="form-control"  type="$type" name="$name" value="$value" $readOnly $required />

        <div id="filename-$name"></div>
<div class="progress">
                    <div class="progress-bar progress-bar-aqua" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only"></span>
                    </div>
                  </div>

    <script>
        $('#field-$name').fileupload({
         progressall: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('.progress .progress-bar').css(
            'width',
            progress + '%'
        );
    },
            dataType: 'json',
            done: function (e, data) {
            console.log(data);
                $.each(data.result.files, function (index, file) {
                    $('#filename-$name').text(file.name);
                });
            }
        });
    </script>
</div>
HTML;

        if ($echo)
            echo $html;


        return $html;
    }


}