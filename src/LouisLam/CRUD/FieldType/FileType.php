<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


use LouisLam\CRUD\Exception\DirectoryPermissionException;

class FileType extends FieldType
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
            chmod($this->getUploadPath(), 0777);
        }

        // Check the directory permission
        if (! is_writable($this->getUploadPath())) {
            throw new DirectoryPermissionException($this->getUploadPath());
        }
    }

    public function renderCell($value) {
        $url = \LouisLam\Util::res($this->getUploadPath() . $value);

        return <<< HTML
    <a href="$url" target="_blank">Open File</a>
HTML;
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
    <input id="field-$name" class="form-control file"  type="$type" name="$name" value="$value" $readOnly $required />

HTML;

        if ($echo)
            echo $html;

        return $html;
    }



    public function beforeStoreValue($valueFromUser)
    {
        $targetFilename = $this->uploadPath . $valueFromUser;
        move_uploaded_file($_FILES[$this->field->getName()]["tmp_name"], $targetFilename);

        return $valueFromUser;
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


}