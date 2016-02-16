<?php

namespace LouisLam\CRUD;

use DebugBar\StandardDebugBar;
use Exception;
use League\Plates\Engine;
use LouisLam\CRUD\Exception\BeanNotNullException;
use LouisLam\CRUD\Exception\NoBeanException;
use LouisLam\CRUD\Exception\NoFieldException;
use LouisLam\CRUD\Exception\TableNameException;
use LouisLam\CRUD\FieldType\CheckboxManyToMany;
use LouisLam\CRUD\FieldType\DropdownManyToOne;
use LouisLam\Util;
use PHPSQL\Creator;
use PHPSQL\Parser;
use RedBeanPHP\OODBBean;
use RedBeanPHP\R;


/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 AM
 */
class LouisCRUD
{
    const NULL = "--louislam-crud-null";

    /**
     * For those who would like to fork my project, please update the composer name.
     * TODO: Read from composer.json?
     * @var string Package Name
     */
    private $packageName = "louislam/louislam-crud";

    /**`
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
    private $createSuccURL = "";

    /*
     * Submit Methods
     */
    private $editSubmitMethod = "put";

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
     * @var OODBBean
     */
    private $currentBean = null;

    private $enableListView = true;
    private $enableEdit = true;
    private $enableDelete = true;
    private $enableCreate = true;

    /** @var Engine */
    private $template;
    protected $theme;

    /**
     * @var string $layout
     */
    private $layout = null;

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

    private $debugbar;

    private $cacheVersion = 2;

    /** @var \Stolz\Assets\Manager Assets Manager  */
    private $headAssets;

    /** @var \Stolz\Assets\Manager Assets Manager  */
    private $bodyEndAssets;

    private $script = "";

    /**
     * @param string $tableName Table Name
     * @param string $viewDir User Template directory
     * @throws TableNameException
     * @throws \RedBeanPHP\RedException
     */
    public function __construct($tableName = null, $viewDir = "view")
    {
        R::ext('xdispense', function ($type) {
            return R::getRedBean()->dispense($type);
        });

        if ($tableName != null) {
            $this->setTable($tableName);
        }

        // Init Assets Manager
        $this->headAssets = new \Stolz\Assets\Manager([
            'css_dir' => '',
            'js_dir' => '',
        ]);

        $this->bodyEndAssets = new \Stolz\Assets\Manager([
            'css_dir' => '',
            'js_dir' => '',
        ]);


        try {
            $this->template = new Engine($viewDir);
        } catch(\LogicException $ex) {
            throw new Exception("The view folder is not existing.");
        }

        // Debug Bar
        $this->debugbar = new StandardDebugBar();
        $debugbarRenderer = $this->debugbar->getJavascriptRenderer(Util::res("vendor/maximebf/debugbar/src/DebugBar/Resources"));

        // Template Default Data
        $this->template->addData([
            "crud" => $this,
            "cacheVersion" => $this->cacheVersion,
            "debugbar" => $this->debugbar,
            "debugbarRenderer" => $debugbarRenderer,
            "headAssets" => $this->headAssets,
            "bodyEndAssets" => $this->bodyEndAssets
        ]);

        $this->addTheme("adminlte", "vendor/$this->packageName/view/theme/AdminLTE");
        $this->setCurrentTheme("adminlte");

        // Enable helper?
        if (defined("ENABLE_CRUD_HELPER") && ENABLE_CRUD_HELPER) {
            //setGlobalCRUD($this);
        }

    }

    /**
     * @param $msg
     */
    public function log($msg) {
        $this->debugbar["messages"]->addMessage($msg);
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
     * @throws Exception
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

    /**
     * @param $fieldNameList
     */
    public function showFields($fieldNameList)
    {
        $nameList = [];
        $newOrderList = [];

        if (is_array($fieldNameList)) {
            // Array Style
            $nameList = $fieldNameList;

        } else {
            // Grocery CRUD style
            $numargs = func_num_args();
            $fieldNames = func_get_args();

            // For each parameters (field name)
            for ($i = 0; $i < $numargs; $i++) {
              $nameList[] = $fieldNames[$i];
            }
        }

        foreach ($nameList as $name) {
            $field = $this->field($name);
            $field->show();
            $newOrderList[$name] = $field;

            // Unset the field from the field list
            unset($this->fieldList[$name]);
        }

        // now $this->fieldList remains fields that user do not input.
        // Use user's order and append remaining fields to the back.
        $this->fieldList = array_merge($newOrderList, $this->fieldList);


    }

    public function hideFields($fieldNameList)
    {

        if (is_array($fieldNameList)) {
            foreach ($fieldNameList as $name) {
                $this->field($name)->hide();
            }
        } else {
            $numargs = func_num_args();
            $fieldNames = func_get_args();

            for ($i = 0; $i < $numargs; $i++) {
                $this->field($fieldNames[$i])->hide();
            }
        }
    }

    public function hideAllFields() {
        foreach ($this->fieldList as $field) {
            $field->hide();
        }
    }

    /**
     * @param string[]|string $fieldNameList
     */
    public function requiredFields($fieldNameList)
    {
        if (is_array($fieldNameList)) {
            foreach ($fieldNameList as $name) {
                $this->field($name)->required();
            }
        } else {
            $numArgs = func_num_args();
            $fieldNames = func_get_args();

            for ($i = 0; $i < $numArgs; $i++) {
                $this->field($fieldNames[$i])->required();
            }
        }
    }

    public function readOnlyFields()
    {
        $numArgs = func_num_args();
        $fieldNames = func_get_args();

        for ($i = 0; $i < $numArgs; $i++) {
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

    public function getThemeName() {
        return $this->theme;
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
     * @throws \RedBeanPHP\RedException\SQL
     */
    protected function getListViewData($start = null, $rowPerPage = null, $keyword = null, $sortField = null, $sortOrder = null)
    {
        try {

            // Paging
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
                    $list[] = (object)$row;
                }

            } else {

                $bindingData = $this->bindingData;

                // For Find All Clause
                if ($this->findAllClause != null) {
                    $findClause = " 1 = 1 " . $this->findAllClause;
                } else if ($this->findClause != null) {
                    $findClause = $this->findClause;
                } else {
                    $findClause = " 1 = 1 ";
                }


                // Build a searching clause
                if ($keyword != null) {
                    $searchClause = $this->buildSearchingClause();
                    $searchData = $this->buildSearchingData($keyword);

                    $findClause = $searchClause . $findClause;

                    // Merge Array
                    $bindingData = $searchData + $bindingData;
                }

                // Sorting
                if ($sortField != null) {
                    $fakeSelect = "SELECT * FROM louislamcrud_fake_table WHERE ";

                    $parser = new Parser($fakeSelect . $findClause);

                    $sqlArray = $parser->parsed;

                    $sqlArray["ORDER"][0]["expr_type"] = "colref";
                    $sqlArray["ORDER"][0]["base_expr"] = $sortField;
                    $sqlArray["ORDER"][0]["sub_tree"] = null;
                    $sqlArray["ORDER"][0]["direction"] = $sortOrder;

                    $findClause = str_replace($fakeSelect, "", (new Creator($sqlArray))->created);
                }


                $list = R::find($this->tableName, $findClause . $limit, $bindingData);
            }
        } catch (\RedBeanPHP\RedException\SQL $ex) {

                throw $ex;
        } catch(\Exception $ex) {
            // TODO: This should be for not existing test only, not other exceptions.

            // If the table is not existing create one, create the table and run this function again.
            $this->createTable();
            return $this->getListViewData($start, $rowPerPage);
        }

        return $list;
    }

    protected function buildSearchingClause() {
        $searchClause = " ( ";

        $searchFields = $this->getShowFields();
        $isFirstSearchField = true;
        foreach ($searchFields as $searchField) {

            if ($isFirstSearchField) {
                $isFirstSearchField = false;
            } else {
                $searchClause .= " OR ";
            }

            $searchClause .= $searchField->getName() . " LIKE ? ";
        }

        $searchClause .= " ) AND ";

        return $searchClause;
    }

    protected function buildSearchingData($keyword) {
        $searchData = [];

        $searchFields = $this->getShowFields();

        foreach ($searchFields as $searchField) {
            $searchData[] = "%$keyword%";
        }

        return $searchData;
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
            "layoutName" => $this->getLayoutName()
        ]);

        if ($echo) {
            echo $html;
        }

        return $html;
    }


    /**
     * For Ajax ListView (DataTables)
     *
     * @param bool|true $echo
     * @return string
     * @throws NoFieldException
     * @throws \RedBeanPHP\RedException\SQL
     */
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
            $rowPerPage = 25;
        }

        if (isset($_POST["search"]["value"])) {
            $keyword = $_POST["search"]["value"];
        } else {
            $keyword = null;
        }

        if (isset($_POST["order"][0]["column"])) {
           $fieldIndex = $_POST["order"][0]["column"] - 1;

            if ($fieldIndex >= 0) {
                $orderField = $this->getShowFields()[$fieldIndex]->getName();
                $order = $_POST["order"][0]["dir"];
            } else {
                $orderField = null;
                $order = null;
            }


        } else {
            $orderField = null;
            $order = null;
        }

        $list = $this->getListViewData($start, $rowPerPage, $keyword, $orderField, $order);


        $obj = new AjaxResult();

        // Get the total number of record
        // TODO: Can improve performance?
        $obj->recordsTotal = count($this->getListViewData());
        $obj->recordsFiltered = $obj->recordsTotal;

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
     * For API
     *
     * @param bool|true $echo
     * @return mixed
     * @throws NoFieldException
     * @throws \RedBeanPHP\RedException\SQL
     */
    public function getJSONList($echo = true) {
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
        $obj = [];

        foreach ($list as $bean) {
            $row = [];
            // Action
            //$row[] = $this->getAction($bean);
            $fields = $this->getShowFields();

            foreach ($fields as $field) {
                $row[$field->getName()] = $field->cellValue($bean);
            }
            $obj[] = $row;
        }

        $json = Util::prettyJSONPrint(json_encode($obj));

        if ($echo) {
            echo $json;
        }

        return $json;
    }


    /**
     * @param bool echo?
     */
    public function getJSON($echo = true) {
        $bean = $this->getBean();

        $fields = $this->getShowFields();

        $output = "";
        $array = [];

        foreach ($fields as $field) {
            $array[$field->getName()] = $field->cellValue($bean);
        }

        $output = Util::prettyJSONPrint(json_encode($array));

        if ($echo) {
            echo $output;
        }


        return $output;
    }

    /**
     * @param bool|true $echo
     * @return string
     */
    public function renderCreateView($echo = true)
    {
        $this->beforeRender();

        $html = $this->template->render($this->getCreateTemplate(), [
            "fields" => $this->getShowFields(),
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

        $html = $this->template->render($this->getEditTemplate(), [
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
     * @param string $method
     */
    public function setEditSubmitLink($editSubmitLink, $method = "put")
    {
        $this->editSubmitLink = $editSubmitLink;
        $this->editSubmitMethod = $method;
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
     * TODO: Update to similar to updateBean
     * Store Data into Database
     * @param $data
     * @return int|string
     */
    public function insertBean($data)
    {
        $bean = R::xdispense($this->tableName);
        $result =  $this->saveBean($bean, $data);

        if (!isset($result->msg)) {
            $result->msg = "The record has been created successfully.";
            $result->class = "callout-info";
            $result->ok = true;
        } else {
            $result->ok = false;
        }

        return $result;
    }

    /**
     * Update a bean.
     * @param $data
     * @return Result
     * @throws NoBeanException
     */
    public function updateBean($data)
    {
        if ($this->currentBean ==null) {
            throw new NoBeanException();
        }

        $result = $this->saveBean($this->currentBean, $data);

        // Return result
        if (!isset($result->msg)) {
            $result->msg = "Saved.";
            $result->class = "callout-info";
        }

        return $result;
    }

    /**
     * Insert or Update a bean
     *
     * @param OODBBean $bean
     * @param $data array
     * @return Result
     */
    private function saveBean($bean, $data)
    {

        // Handle File Field that may not in the $data, because Filename always go into $_FILES.
        foreach ($_FILES as $fieldName => $file) {
            $data[$fieldName] = $file["name"];
        }


        // Store Showing fields only
        $fields = $this->getShowFields();

        foreach ($fields as $field) {


            // Check is unique
            if ($field->isUnique()) {

                // Try to find duplicate beans
                $fieldName = $field->getName();
                $duplicateBeans = R::find($bean->getMeta('type'), " $fieldName = ? ", [$data[$field->getName()]]);

                if (count($duplicateBeans) > 0) {
                    $validateResult = "Email 已存在！";
                }
            }

            if ($field->getFieldRelation() == Field::MANY_TO_MANY) {
                // 1. Many to many

                // http://www.redbeanphp.com/many_to_many
                $keyName = "shared" . ucfirst($field->getName()) . "List";

                // Clear the current list (tableB_tableA)
                try {
                    $tableName = $this->getTableName() . "_" . $field->getName();
                    $idName = $this->getTableName() . "_id";
                    R::exec("DELETE FROM $tableName WHERE $idName = ?", [$bean->id]);
                } catch (\Exception $ex) {
                }

                // Clear the current list (tableA_tableB)
                try {
                    $tableName = $field->getName() . "_" . $this->getTableName();
                    $idName = $this->getTableName() . "_id";
                    R::exec("DELETE FROM $tableName WHERE $idName = ?", [$bean->id]);
                } catch (\Exception $ex) {
                }

                // If User have checked a value in checkbox
                if (isset($data[$field->getName()])) {
                    $valueList = $data[$field->getName()];
                    $slots = R::genSlots($valueList);
                    $relatedBeans = R::find($field->getName(), " id IN ($slots)", $valueList);

                    foreach ($relatedBeans as $relatedBean) {
                        $bean->{$keyName}[] = $relatedBean;
                    }
                }

            } else if ($field->getFieldRelation() == Field::ONE_TO_MANY) {
                // TODO One to many

            } else if (! $field->isStorable()) {

                // 2. If not storable, skip
                continue;

            } elseif ($field->getFieldRelation() == Field::NORMAL) {
                // 3.Normal data field

                $value = $field->getStoreValue($data);

                if ($value == LouisCRUD::NULL) {
                    $value = null;
                }

                // Validate the value
                if ($field->isStorable())
                    $validateResult = $field->validate($value, $data);
                else {
                    // TODO: check non-storable?
                    $validateResult = true;
                }

                // If validate failed, return result object.
                if ($validateResult !== true) {
                    $result = new Result();
                    $result->id = @$bean->id;
                    $result->msg = $validateResult;
                    $result->fieldName = $field->getName();
                    $result->class = "callout-danger";
                    return $result;
                }

                // Set the value to the current bean directly
                $bean->{$field->getName()} = $value;

            }
        }

        // Store
        // TODO: Return result object
        $id = R::store($bean);
        $result = new Result();
        $result->id = $id;
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
        if ($this->layout != null) {
            return $this->layout;
        }

        try {
            return $this->template->exists("backend_layout") ? "layout" : $this->theme . "::layout";
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
     * Row Action
     * @param callable $actionClosure Closure Format: function($bean) { }
     *
     */
    public function rowAction($actionClosure)
    {
        $this->actionClosure = $actionClosure;
    }

    public function upload($fieldName = "upload", $folder = "upload/") {

        if (isset($_FILES[$fieldName])) {

            $filename = dechex(rand(1, 99999999999999)) . "-" . urlencode($_FILES[$fieldName]["name"]);

            $relativePath = $folder . $filename;
            move_uploaded_file($_FILES[$fieldName]["tmp_name"], $relativePath);

            $output = [
                "fileName" =>$filename,
                "uploaded" => 1,
                "url" => $relativePath,
                "status" => "SUCC"
            ];
        } else {
            $output = [
                "fileName" => "",
                "uploaded" => 0,
                "url" => "",
                "status" => "FAIL",
                "msg" => "The file size was too big."
            ];
        }


        return $output;
    }

    public function getTemplateEngine() {
        return $this->template;
    }

    public function render($name, $data = [], $echo = false) {
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

    public function getFullViewName($viewName) {
        return $this->theme . "::" . $viewName;
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

    public function manyToMany($tableName, $nameFormatClosure)
    {
        $field = $this->field($tableName);
        $field->setFieldType(new CheckboxManyToMany($tableName, $nameFormatClosure));
        return $field;
    }

    public function manyToOne($tableName) {
        $field = $this->field($tableName . "_id");
        $field->setFieldType(new DropdownManyToOne($tableName));
        return $field;
    }

    public function getEditSubmitMethod()
    {
        return $this->editSubmitMethod;
    }


    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return string
     */
    public function getCreateSuccURL()
    {
        return $this->createSuccURL;
    }

    /**
     * @param string $createSuccURL
     */
    public function setCreateSuccURL($createSuccURL)
    {
        $this->createSuccURL = $createSuccURL;
    }

    /**
     * @return \Stolz\Assets\Manager
     */
    public function getBodyEndAssets()
    {
        return $this->bodyEndAssets;
    }

    /**
     * @return \Stolz\Assets\Manager
     */
    public function getHeadAssets()
    {
        return $this->headAssets;
    }

    public function addScript($script) {
        $this->script .= $script;
    }

    public function getScript() {
        return $this->script;
    }



}