<?php

namespace LouisLam\CRUD;

use League\Plates\Engine;
use LouisLam\CRUD\Exception\BeanNotNullException;
use LouisLam\CRUD\Exception\NoFieldException;
use LouisLam\CRUD\Exception\TableNameException;
use LouisLam\CRUD\Exception\NoBeanException;
use RedBeanPHP\R;

/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 AM
 */
class LouisCRUD
{
    /**
     * @var string Table Name
     */
    private $tableName = null;

    /** @var Engine */
    private $template;

    private $theme;

    private $fieldsInfoFromDatabase = null;


    private $deleteLink = "";
    private $createLink = "";
    private $createSubmitLink = "";
    private $editLink = "";
    private $editSubmitLink = "";



    /** @var Field[] */
    private $fieldList = [];

    /**
     * @var string
     */
    private $rowActionHTML = "";

    private $findClause = null;
    private $findAllClause = null;
    private $bindingData = [];

    /**
     * Current Bean for edit or delete
     * @var
     */
    private $currentBean = null;

    public function __construct($tableName = null, $viewDir = "view")
    {

        R::ext('xdispense', function ($type) {
            return R::getRedBean()->dispense($type);
        });

        if ($tableName != null) {
            $this->setTable($tableName);
        }

        $this->template = new Engine($viewDir);
        $this->addTheme("raw", "vendor/louislam/louislam-crud/view/RawCRUDTheme");
        $this->setCurrentTheme("raw");
    }

    public function setViewDirectory($viewDir)
    {
        $this->template->setDirectory($viewDir);
    }

    public function field($name)
    {
        if (!isset($this->fieldList[$name])) {
            $this->addField($name);
        }

        return $this->fieldList[$name];
    }

    /**
     * @param $name
     * @param string $dataType
     */
    public function addField($name, $dataType = "varchar(255)")
    {
        $this->fieldList[$name] = new Field($this, $name, $dataType);
    }

    /**
     * @return Field[]
     */
    public function getShowFields()
    {
        $fields = [];

        foreach ($this->fieldList as $field) {
            if (! $field->isHidden()) {
                $fields[] =  $field;
            }
        }

        return $fields;
    }

    public function showFields()
    {
        $numargs = func_num_args();
        $this->showFields = [];

        $fieldNames = func_get_args();

        for ($i = 0; $i < $numargs; $i++) {
            $this->field($fieldNames[$i])->show();
        }
    }

    public function hideFields() {
        $numargs = func_num_args();

        $fieldNames = func_get_args();

        for ($i = 0; $i < $numargs; $i++) {
            $this->field($fieldNames[$i])->hide();
        }
    }

    public function setCurrentTheme($theme)
    {
        $this->theme = $theme;
    }

    public function addTheme($themeName, $path)
    {
        $this->template->addFolder($themeName, $path);
    }

    protected function setTable($tableName)
    {

        if ($this->tableName != null) {
            throw new TableNameException();
        }

        $this->tableName = $tableName;
        $this->initFields();
    }

    private function initFields()
    {
        $this->loadFieldsInfoFromDatabase();

        foreach ($this->fieldsInfoFromDatabase as $showField => $dataType) {
            if (!isset($this->fieldList[$showField])) {
                $this->addField($showField, $dataType);
            }
        }
    }

    public function find($clause, $data = []) {
        $this->findAllClause = null;

        $this->findClause = $clause;
        $this->bindingData = $data;
    }

    public function findAll($clause, $data = []) {
        $this->findClause = null;

        $this->findAllClause = $clause;
        $this->bindingData = $data;
    }

    private function loadFieldsInfoFromDatabase()
    {
        $this->fieldsInfoFromDatabase = R::inspect($this->tableName);
    }

    public function createTable() {
        $bean = R::xdispense($this->tableName);
        R::store($bean);
        R::trash($bean);
    }

    private function beforeRender() {

        // if there is a ID field only, no other fields, then throw an Exception
        if (count($this->fieldList) <= 1) {
            throw new NoFieldException();
        }
    }

    public function renderListView($echo = true)
    {
        $this->beforeRender();

        try {
            if ($this->findClause != null) {
                $list = R::find($this->tableName, $this->findClause, $this->bindingData);
            } else {
                $list = R::findAll($this->tableName, $this->findAllClause, $this->bindingData);
            }
        } catch(\RedBeanPHP\RedException\SQL $ex) {
            // If the table is not existing create one, create the table and run this function again.
            $this->createTable();
            $this->renderListView($echo);
            return null;
        }

        $html = $this->template->render($this->theme . "::listing", [
            "fields" => $this->getShowFields(),
            "list" => $list,
            "crud" => $this,
            "layoutName" => $this->getLayoutName()
        ]);

        if ($echo) {
            echo $html;
        }

        return $html;
    }

    /**
     * @param bool|true $echo
     * @return string
     */
    public function renderCreateView($echo = true)
    {
        $this->beforeRender();

        $html = $this->template->render($this->theme . "::create", [
            "fields" => $this->getShowFields(),
            "crud" => $this,
            "layoutName" => $this->getLayoutName()
        ]);

        if ($echo) {
            echo $html;
        }
        return $html;
    }

    public function renderEditView($echo = true)
    {
        $this->beforeRender();

        if ($this->currentBean == null) {
            throw new NoBeanException();
        }

        $html = $this->template->render($this->theme . "::edit", [
            "fields" => $this->getShowFields(),
            "crud" => $this,
            "layoutName" => $this->getLayoutName()
        ]);

        if ($echo) {
            echo $html;
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getEditLink($id)
    {
        return str_replace(":id", $id, $this->editLink);
    }

    /**
     * Example: http://localhost/user/edit/:id
     * @param string $editLink
     */
    public function setEditLink($editLink)
    {
        $this->editLink = $editLink;
    }

    /**
     * @param $id
     * @return string
     */
    public function getEditSubmitLink($id)
    {
        return str_replace(":id", $id, $this->editSubmitLink);
    }

    /**
     * @param string $editSubmitLink
     */
    public function setEditSubmitLink($editSubmitLink)
    {
        $this->editSubmitLink = $editSubmitLink;
    }

    /**
     * @return string
     */
    public function getDeleteLink($id)
    {
        return str_replace(":id", $id, $this->deleteLink);
    }

    /**
     * Example: http://localhost/user/delete/:id
     * @param string $deleteLInk
     */
    public function setDeleteLink($deleteLInk)
    {
        $this->deleteLink = $deleteLInk;
    }

    /**
     * @return string
     */
    public function getCreateLink()
    {
        return $this->createLink;
    }

    /**
     * @param string $createLink
     */
    public function setCreateLink($createLink)
    {
        $this->createLink = $createLink;
    }

    /**
     * @return string
     */
    public function getCreateSubmitLink()
    {
        return $this->createSubmitLink;
    }

    /**
     * @param string $createSubmitLink
     */
    public function setCreateSubmitLink($createSubmitLink)
    {
        $this->createSubmitLink = $createSubmitLink;
    }

    /**
     * Load a bean.
     * For Edit and Create only.
     * Before rendering the edit or Create page, you have to load a bean first.
     * @param $id
     * @throws BeanNotNullException You can load one time only.
     */
    public function loadBean($id)
    {

        if ($this->currentBean != null) {
            throw new BeanNotNullException();
        }

        $this->currentBean = R::load($this->tableName, $id);
    }

    /**
     * Store Data into Database
     * @param $data
     * @return int|string
     */
    public function insertBean($data)
    {
        $bean = R::xdispense($this->tableName);

        $fields = $this->getShowFields();

        $fieldNameList = [];

        foreach ($fields as $field) {
            $fieldNameList[] = $field->getName();
        }

        // http://www.redbeanphp.com/import_and_export
        $fieldsString = implode(",", $fieldNameList);
        $bean->import($data, $fieldsString);

        return R::store($bean);
    }

    public function updateBean($data)
    {

        if ($this->currentBean ==null) {
            throw new NoBeanException();
        }

        $fields = $this->getShowFields();
        $fieldNameList = [];

        foreach ($fields as $field) {
            $fieldNameList[] = $field->getName();
        }

        $fieldsString = implode(",", $fieldNameList);
        $this->currentBean->import($data, $fieldsString);
        return R::store($this->currentBean);
    }

    /**
     * Delete the loaded bean
     * @throws NoBeanException
     */
    public function deleteBean() {

        if ($this->currentBean == null) {
            throw new NoBeanException();
        }

        R::trash($this->currentBean);
    }

    public function getRowActionHTML()
    {
        return $this->rowActionHTML;
    }

    public function setRowActionHTML($html)
    {
        $this->rowActionHTML = $html;
    }

    public function getBean() {
        return $this->currentBean;
    }

    /**
     * Get Current Layout Name in Plates Template Engine style
     * If user have created a layout.php in the default folder, use their layout.php.
     * Or else use the raw one.
     *
     * @return string Layout Name
     */
    private function getLayoutName()
    {
        return $this->template->exists("layout") ? "layout" : "raw::layout";
    }

}