<?php

namespace LouisLam\CRUD;

use Exception;
use FileUpload\FileUpload;
use League\Plates\Engine;
use LouisLam\CRUD\Exception\BeanNotNullException;
use LouisLam\CRUD\Exception\NoBeanException;
use LouisLam\CRUD\Exception\NoFieldException;
use LouisLam\CRUD\Exception\TableNameException;
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
     * For those who would like to fork my project and re-publish on Packagist, please update the composer name.
     * TODO: Read from composer.json?
     * @var string Package Name
     */
    private $packageName = "louislam/louislam-crud";

    /**
     * @var string Table Name
     */
    private $tableName = null;

    private $fieldsInfoFromDatabase = null;

    /*
     * URLs
     */
    private $listViewLink = "";
    private $createLink = "";
    private $createSubmitLink = "";
    private $editLink = "";
    private $editSubmitLink = "";
    private $deleteLink = "";
    private $exportLink = "";
    private $listViewJSONLink = "";

    /** @var Field[] */
    private $fieldList = [];

    /**
     * @var callable
     */
    private $actionClosure = null;

    private $findClause = null;
    private $findAllClause = null;
    private $bindingData = [];

    /** @var string This will highest priority to use */
    private $sql = null;

    /**
     * Current Bean for edit or delete
     * @var
     */
    private $currentBean = null;

    private $enableListView = true;
    private $enableEdit = true;
    private $enableDelete = true;
    private $enableCreate = true;

    /** @var Engine */
    private $template;
    private $theme;

    /*
     *  Template (please use getter to get the template name)
     */
    private $listViewTemplate = null;
    private $editTemplate = null;
    private $createTemplate = null;

    private $tableDisplayName = null;

    /** @var array Data for layout */
    private $data = "";

    private $ajaxListView = true;

    private $exportFilename = null;


    public function __construct($tableName = null, $viewDir = "view")
    {

        R::ext('xdispense', function ($type) {
            return R::getRedBean()->dispense($type);
        });

        if ($tableName != null) {
            $this->setTable($tableName);
        }

        try {
            $this->template = new Engine($viewDir);
        } catch(\LogicException $ex) {
            $this->template = new Engine();
        }

        $this->addTheme("adminlte", "vendor/$this->packageName/view/theme/AdminLTE");
        $this->addTheme("raw", "vendor/$this->packageName/view/theme/RawCRUDTheme");
        $this->setCurrentTheme("adminlte");
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

        // Check if the name whether is satisfied
        if (ctype_upper($name[0])) {
            throw new Exception("Field name cannot start with upper-case.");
        }

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
        $fieldNames = func_get_args();

        $newOrderList = [];

        // For each parameters (field name)
        for ($i = 0; $i < $numargs; $i++) {
            $field = $this->field($fieldNames[$i]);
            $field->show();
            $newOrderList[$fieldNames[$i]] = $field;

            // Unset the field from the field list
            unset($this->fieldList[$fieldNames[$i]]);
        }

        // now $this->fieldList remains fields that user do not input.
        // Use user's order and append remaining fields to the back.
        $this->fieldList = array_merge($newOrderList, $this->fieldList);


    }

    public function hideFields()
    {
        $numargs = func_num_args();
        $fieldNames = func_get_args();

        for ($i = 0; $i < $numargs; $i++) {
            $this->field($fieldNames[$i])->hide();
        }
    }

    public function requiredFields()
    {
        $numargs = func_num_args();
        $fieldNames = func_get_args();

        for ($i = 0; $i < $numargs; $i++) {
            $this->field($fieldNames[$i])->required();
        }
    }

    public function readOnlyFields()
    {
        $numargs = func_num_args();
        $fieldNames = func_get_args();

        for ($i = 0; $i < $numargs; $i++) {
            $this->field($fieldNames[$i])->setReadOnly(true);
        }
    }

    public function allReadOnly()
    {
        foreach($this->fieldList as $field) {
            $field->setReadOnly(true);
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

    /**
     * @return string
     */
    public function getTableName() {
        return $this->tableName;
    }

    private function initFields()
    {
        $this->loadFieldsInfoFromDatabase();

        foreach ($this->fieldsInfoFromDatabase as $showField => $dataType)
        {
            if (!isset($this->fieldList[$showField])) {
                $this->addField($showField, $dataType);
            }
        }
    }

    public function find($clause, $data = [])
    {
        $this->findAllClause = null;

        $this->findClause = $clause;
        $this->bindingData = $data;
    }

    public function findAll($clause, $data = [])
    {
        $this->findClause = null;

        $this->findAllClause = $clause;
        $this->bindingData = $data;
    }

    private function loadFieldsInfoFromDatabase()
    {
        try {
            $this->fieldsInfoFromDatabase = R::inspect($this->tableName);
        } catch (Exception $ex) {
            $this->createTable();

            // Load again
            $this->loadFieldsInfoFromDatabase();
        }
    }

    public function createTable()
    {
        $bean = R::xdispense($this->tableName);
        R::store($bean);
        R::trash($bean);
    }

    protected function getAction($bean)
    {
        $html = "";

        if ($this->isEnabledEdit()) {
            $url = $this->getEditLink($bean->id);
            $html .= <<< HTML
<a href="$url" class="btn btn-default">Edit</a>
HTML;

        }

        if ($this->isEnabledDelete()) {
            $url = $this->getDeleteLink($bean->id);
            $html .= <<< HTML
 <a class="btn-delete btn btn-danger" href="javascript:void(0)" data-id="$bean->id" data-url="$url">Delete</a>
HTML;
        }

        if ($this->getRowAction() != null) {
            $c = $this->getRowAction();
            $html .= $c($bean);
        }

        return $html;
    }

    protected function beforeRender()
    {
        // if there is a ID field only, no other fields, then throw an Exception
        if (count($this->fieldList) <= 1) {
            throw new NoFieldException();
        }
    }

    /**
     * Get List view data
     * @param null $start
     * @param null $rowPerPage
     * @return array List of beans
     */
    protected function getListViewData($start = null, $rowPerPage = null)
    {
        try {

            if ($start != null && $rowPerPage != null) {
                $limit = " LIMIT $start,$rowPerPage";
            } else {
                $limit = "";
            }


            if ($this->sql != null) {
            // Custom SQL

                $list = [];
                $tempList = R::getAll($this->sql . $limit, $this->bindingData);

                // Array convert to object
                foreach ($tempList as $row) {
                    $list[] = (object) $row;
                }

            } elseif ($this->findClause != null) {
            // Find Clause

                $list = R::find($this->tableName, $this->findClause . $limit, $this->bindingData);
            } else {
            // Find All Clause

                if ($this->findAllClause != null) {
                    $clause = $this->findAllClause .  $limit;
                } else {
                    $clause = $limit;
                }
                $list = R::findAll($this->tableName, $clause, $this->bindingData);

            }
        } catch(\RedBeanPHP\RedException\SQL $ex) {
            // If the table is not existing create one, create the table and run this function again.
            $this->createTable();
            return $this->getListViewData($start, $rowPerPage);
        }

        return $list;
    }

    public function renderExcel()
    {
        $this->beforeRender();
        $list = $this->getListViewData();

        (new ExcelHelper())->genExcel($this, $list, $this->getExportFilename());
    }

    public function renderListView($echo = true)
    {
        $this->beforeRender();

        if ($this->ajaxListView) {
            $list = [];
        } else {
            $list = $this->getListViewData();
        }

        $html = $this->template->render($this->getListViewTemplate(), [
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


    public function getListViewJSONString($echo = true) {
        $this->beforeRender();

        if (isset($_POST["start"])) {
            $start = $_POST["start"];
        } else {
            $start = 0;
        }

        if (isset($_POST["length"])) {
            $rowPerPage = $_POST["length"];
        } else {
            $rowPerPage = 15;
        }

       $list = $this->getListViewData($start, $rowPerPage);

        $obj = new AjaxResult();

        if (isset($_POST["draw"])) {
            $obj->draw = $_POST["draw"];
        }

        foreach ($list as $bean) {
            $row = [];

            // Action
            $row[] = $this->getAction($bean);

            $fields = $this->getShowFields();

            foreach ($fields as $field) {
                $row[] = $field->cellValue($bean);
            }

            $obj->data[] = $row;
        }

        $json = json_encode($obj);

        if ($echo) {
            echo $json;
        }

        return $json;
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
    public function getListViewLink()
    {
        return $this->listViewLink;
    }

    /**
     * @param string $listViewLink
     */
    public function setListViewLink($listViewLink)
    {
        $this->listViewLink = $listViewLink;
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
     * TODO: Validate Before INSERT/UPDATE
     * @param $bean
     */
    public function validate($bean) {

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

        $result = new Result();

        // Store
        $result->id = R::store($bean);

        $result->msg = "The record has been created successfully.";

        return $result;
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

        $result = new Result();
        // Store
        $result->id = R::store($this->currentBean);
        $result->msg = "Saved.";

        return $result;
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

    public function getBean() {
        return $this->currentBean;
    }

    /**
     * Get Current Layout Name in Plates Template Engine style
     * If user have created a layout.php in the default folder, use their layout.php.
     * Or else use the default layout.
     *
     * @return string Layout Name
     */
    private function getLayoutName()
    {
        try {
            return $this->template->exists("layout") ? "layout" : $this->theme . "::layout";
        } catch (\LogicException $ex) {
            return $this->theme . "::layout";
        }
    }

    /**
     * @return mixed
     */
    public function enableListView($bool)
    {
        $this->enableListView = $bool;
    }

    /**
     * @return boolean
     */
    public function isEnabledListView()
    {
        return $this->enableListView;
    }


    /**
     * @return mixed
     */
    public function enableEdit($bool)
    {
        $this->enableEdit = $bool;
    }

    /**
     * @return boolean
     */
    public function isEnabledEdit()
    {
        return $this->enableEdit;
    }

    /**
     * @return boolean
     */
    public function isEnabledDelete()
    {
        return $this->enableDelete;
    }

    /**
     * @return boolean
     */
    public function isEnabledCreate()
    {
        return $this->enableCreate;
    }

    /**
     * @param boolean $showDelete
     */
    public function enableDelete($showDelete)
    {
        $this->enableDelete = $showDelete;
    }

    /**
     * @param boolean $showCreate
     */
    public function enableCreate($showCreate)
    {
        $this->enableCreate = $showCreate;
    }

    /**
     * @return string
     */
    public function getListViewTemplate()
    {
        if ($this->listViewTemplate != null)
            return $this->listViewTemplate;

        return $this->theme . "::listing";
    }

    /**
     * @param null $listViewTemplate
     */
    public function setListViewTemplate($listViewTemplate)
    {
        $this->listViewTemplate = $listViewTemplate;
    }

    /**
     * @return null
     */
    public function getEditTemplate()
    {
        if ($this->editTemplate != null)
            return $this->editTemplate;

        return $this->theme . "::edit";
    }

    /**
     * @param null $editTemplate
     */
    public function setEditTemplate($editTemplate)
    {
        $this->editTemplate = $editTemplate;
    }

    /**
     * @return null
     */
    public function getCreateTemplate()
    {
        if ($this->createTemplate != null)
            return $this->createTemplate;

        return $this->theme . "::create";
    }

    /**
     * @param null $createTemplate
     */
    public function setCreateTemplate($createTemplate)
    {
        $this->createTemplate = $createTemplate;
    }

    /**
     * @return callable
     */
    public function getRowAction()
    {
        return $this->actionClosure;
    }

    /**
     * @param callable $actionClosure
     */
    public function rowAction($actionClosure)
    {
        $this->actionClosure = $actionClosure;
    }

    public function upload($fieldName) {
        $fileUpload = new FileUpload($_FILES[$fieldName], $_SERVER);
        $fileUpload->processAll();
    }

    public function getTemplateEngine() {
        return $this->template;
    }

    public function render($name, $data = [], $echo = true) {
        $data["layoutName"] = $this->getLayoutName();
        $data["crud"] = $this;

        $html = $this->template->render($name, $data);
        if ($echo) {
            echo $html;
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getTableDisplayName()
    {
        if (($this->tableDisplayName == null)) {
            return "";
        } else {
            return $this->tableDisplayName;
        }
    }

    /**
     * @param string $tableDisplayName
     */
    public function setTableDisplayName($tableDisplayName)
    {
        $this->tableDisplayName = $tableDisplayName;
    }


    public function setData($key, $value = null)
    {
        $this->data[$key] = $value;
    }

    public function getData($key) {
        if (!isset($this->data[$key])) {
            return "";
        }
        return $this->data[$key];
    }

    public function loadView($dataName, $viewName = null, $data = [])
    {
        if ($viewName == null) {
            $viewName = $dataName;
        }

        $this->setData($dataName, $this->render($viewName, $data, false));
    }

    public function isAjaxListView()
    {
        return $this->ajaxListView;
    }

    /**
     * @return mixed
     */
    public function getListViewJSONLink()
    {
        return $this->listViewJSONLink;
    }

    /**
     * @param mixed $listViewJSONLink
     */
    public function setListViewJSONLink($listViewJSONLink)
    {
        $this->listViewJSONLink = $listViewJSONLink;
    }

    public function enableAjaxListView($bool = true) {
        $this->ajaxListView = $bool;
    }

    public function msg($msg, $title = null) {

        $title = ($title == null) ? "Message" : $title;

        $this->render($this->theme . "::msg", [
            "msg" => $msg,
            "title" => $title
        ]);
    }

    public function setSQL($sql, $data = []) {
        $this->sql = $sql;
        $this->bindingData = $data;
    }

    public function getSQL() {
        return $this->sql;
    }

    /**
     * @return string
     */
    public function getExportLink()
    {
        return $this->exportLink;
    }

    /**
     * @param string $exportLink
     */
    public function setExportLink($exportLink)
    {
        $this->exportLink = $exportLink;
    }

    /**
     * @return null
     */
    public function getExportFilename()
    {
        return $this->exportFilename;
    }

    /**
     * @param null $exportFilename
     */
    public function setExportFilename($exportFilename)
    {
        $this->exportFilename = $exportFilename;
    }



}