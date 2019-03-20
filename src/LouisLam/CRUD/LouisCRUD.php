<?php

namespace LouisLam\CRUD;

use Exception;
use League\Plates\Engine;
use LouisLam\CRUD\Exception\BeanNotNullException;
use LouisLam\CRUD\Exception\NoBeanException;
use LouisLam\CRUD\Exception\NoFieldException;
use LouisLam\CRUD\Exception\TableNameException;
use LouisLam\CRUD\FieldType\CheckboxManyToMany;
use LouisLam\CRUD\FieldType\DropdownManyToOne;
use LouisLam\Util;
use PHPSQLParser\PHPSQLCreator;
use PHPSQLParser\PHPSQLParser;
use RedBeanPHP\OODBBean;
use RedBeanPHP\R;
use Stringy\Stringy;

/**
 * @param $str
 * @return Stringy
 */
function s($str) {
    return Stringy::create($str);
}


/**
 * LouisCRUD
 *
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
    protected $packageName = "louislam/louislam-crud";

    /**
     * @var string Table Name
     */
    protected $tableName = null;
    
    protected $fieldsInfoFromDatabase = null;



    /*
     * URLs
     */
    protected $listViewLink = "";
    protected $createLink = "";
    protected $createSubmitLink = "";
    protected $editLink = "";
    protected $editSubmitLink = "";
    protected $deleteLink = "";
    protected $exportLink = "";
    protected $listViewJSONLink = "";

    /**
     * @var string If it is null, the getter will return getListViewLink() instead of ths var.
     */
    protected $createSuccURL = null;

    /*
     * Submit Methods
     */
    protected $editSubmitMethod = "put";

    /** @var Field[] */
    protected $fieldList = [];

    /**
     * @var callable
     */
    protected $actionClosure = null;
    
    protected $findClause = null;
    protected $findAllClause = null;
    protected $bindingData = [];

    /** @var string This will highest priority to use */
    protected $sql = null;

    /**
     * Current Bean for edit or delete
     * @var OODBBean
     */
    protected $currentBean = null;

    /*
     * Flag for controlling the function enable/disable
     */
    protected $enableListView = true;
    protected $enableEdit = true;
    protected $enableDelete = true;
    protected $enableCreate = true;
    protected $enableSearch = true;
    protected $enableSorting = true;

    /** @var Engine */
    protected $template;
    protected $theme;

    /**
     * @var string $layout
     */
    protected $layout = null;

    /*
     *  Template (please use getter to get the template name)
     */
    protected $listViewTemplate = null;
    protected $editTemplate = null;
    protected $createTemplate = null;
    
    private $tableDisplayName = null;

    /** @var array Data for layout */
    protected $data = [];
    
    protected $ajaxListView = true;
    
    protected $exportFilename = null;
    
    protected $cacheVersion = 2;
    
    protected $bodyEndHTML = "";
    protected $headHTML = "";

    /**
     * @var callable
     */
    protected $afterUpdateBean = null;

    /**
     * @var callable
     */
    protected $afterInsertBean = null;

    /**
     * @var callable
     */
    protected $beforeUpdateBean = null;

    /**
     * @var callable
     */
    protected $beforeInsertBean = null;

    /**
     * @var callable
     */
    protected $beforeStoreEachField = null;

    /**
     * @var string
     */
    protected $duplicateEntryErrorMsg = null;

    /**
     * @var bool
     */
    protected $enableFieldGroup = false;

    /**
     * @var FieldGroup[]
     */
    protected $fieldGroupList = [];

    protected $editName = "Edit";
    protected $deleteName = "Delete";
    protected $createName = "New";


    /**
     * First Priority Closure for getListViewData()
     * @var callable
     */
    protected $listViewDataClosure = null;

    /**
     * @var callable
     */
    protected $countListViewDataClosure = null;

    /**
     * @var callable
     */
    protected $searchClosure = null;

    /**
     * @var callable
     */
    protected $searchResultCountClosure = null;

    /**
     * @var \Closure This will be called if error.
     */
    private $onInsertError;

    /**
     * @var \Closure This will be called if error.
     */
    protected $onUpdateError;
    
    protected $allowExt = [
        "jpg", "jpeg", "gif", "png", "apng", "svg", "pdf", "doc", "docx", "ppt", "pptx", "xls", "xlsx", "mp4"
    ];

    /**
     * @return string
     */
    public function getDuplicateEntryErrorMsg()
    {
        return $this->duplicateEntryErrorMsg;
    }

    /**
     * @param string $duplicateEntryErrorMsg
     */
    public function setDuplicateEntryErrorMsg($duplicateEntryErrorMsg)
    {
        $this->duplicateEntryErrorMsg = $duplicateEntryErrorMsg;
    }

    /**
     * @param string $tableName Table Name
     * @param string $viewDir User Template directory
     * @throws Exception
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

        try {
            $this->template = new Engine($viewDir);
        } catch(\LogicException $ex) {
            mkdir("view");

            try {
                $this->template = new Engine($viewDir);
            } catch (\LogicException $ex) {
                throw new Exception("The \"view\" folder is not existing.");
            }
        }

        // Template Default Data
        $this->template->addData([
            "crud" => $this,
            "cacheVersion" => $this->cacheVersion
        ]);

        // Keep old name
        $this->addTheme("adminlte", "vendor/$this->packageName/view/theme/AdminLTE");
        $this->addTheme("AdminLTE", "vendor/$this->packageName/view/theme/AdminLTE");

        $this->setCurrentTheme("AdminLTE");

        // Enable helper?
        if (defined("ENABLE_CRUD_HELPER") && ENABLE_CRUD_HELPER) {
            //setGlobalCRUD($this);
        }

        // Default on error callback
        $this->onUpdateError = $this->onInsertError = function ($internalErrorMsg) {
            return $internalErrorMsg;
        };
    }

    /**
     * @param $msg
     */
    public function log($msg) {

    }

    public function setViewDirectory($viewDir)
    {
        $this->template->setDirectory($viewDir);
    }

    /**
     * Get or Create a field with a name
     * @param string $name The Field Name
     * @return Field The field object
     * @throws Exception
     */
    public function field($name)
    {
        if (!isset($this->fieldList[$name])) {
            $this->addField($name);
        }

        return $this->fieldList[$name];
    }

    /**
     * Create a field with a name
     * @param $name
     * @param string $dataType it can be varchar/int/text
     * @throws Exception
     */
    public function addField($name, $dataType = "varchar(255)")
    {
        if ($name == "") {
            throw new Exception("Field name cannot be empty.");
        }

        // Check if the name whether is satisfied
        if (ctype_upper($name[0])) {
            throw new Exception("Field name cannot start with upper-case.");
        }

        $this->fieldList[$name] = new Field($this, $name, $dataType);
    }

    /**
     * Get an array of Field(s) which is/are going to be shown.
     * @return Field[]
     */
    public function getShowFields()
    {
        $fields = [];

        foreach ($this->fieldList as $field) {
            if (! $field->isHidden()) {

                // Must be number index
                $fields[] =  $field;
            }
        }

        return $fields;
    }

    /**
     * Show and order field(s)
     * @param string[] $fieldNameList An array of field name(s).
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

    /**
     * Hide fields, useful if you want to keep the field in the database but not show on the curd page.
     * @param string[] $fieldNameList An array of field name(s).
     */
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

    /**
     * Hide all fields.
     */
    public function hideAllFields() {
        foreach ($this->fieldList as $field) {
            $field->hide();
        }
    }

    /**
     *
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

    /**
     * Create Table
     * Problem: The first ID is always start from 2.
     * TODO: Create table with pure SQL, but be careful it may not support for all databases.
     */
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
            $editName = $this->editName;
            $html .= <<< HTML
<a href="$url" class="btn btn-default">$editName</a>
HTML;

        }

        if ($this->isEnabledDelete()) {
            $url = $this->getDeleteLink($bean->id);
            $deleteName = $this->deleteName;
            $html .= <<< HTML
 <a class="btn-delete btn btn-danger" href="javascript:void(0)" data-id="$bean->id" data-url="$url">$deleteName</a>
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
     * Count Total List view data
     * @param string $keyword
     * @return int
     */
    protected function countTotalListViewData($keyword = null) {
        $count = 0;

        if ($this->listViewDataClosure != null) {

            if ($this->countListViewDataClosure != null) {
                $c = $this->countListViewDataClosure;
                return  $c($keyword);
            } else {
                return 100000;
            }

        } elseif ($this->searchResultCountClosure != null) {            // For Custom Searching
            $c = $this->searchResultCountClosure;
            return $c($keyword);
            
        } else {
            $this->beforeGetListViewData(function ($tableName, $findClause, $limit, $bindingData) use (&$count) {

                // For RedBean Case
                $count = R::getCell("SELECT COUNT(*) FROM `$tableName` WHERE $findClause $limit", $bindingData);

            }, function ($sql, $limit, $bindingData) use (&$count) {

                // For SQL Case
                $count = R::getRow("SELECT COUNT(*) AS `count` FROM (" . $sql . $limit . ") AS user_defined_query", $bindingData)["count"];

            }, null, null, $keyword);

        }



        return $count;
    }

    /**
     * Get List view data
     *
     * @param int $start
     * @param int $rowPerPage
     * @param string $keyword
     * @param string $sortField
     * @param string $sortOrder ASC/DESC
     * @return array List of beans
     * @throws \RedBeanPHP\RedException\SQL
     */
    protected function getListViewData($start = null, $rowPerPage = null, $keyword = null, $sortField = null, $sortOrder = null) {
        $list = [];

        if ($this->listViewDataClosure != null) {
            $c = $this->listViewDataClosure;
            $list = $c($start, $rowPerPage, $keyword, $sortField, $sortOrder);

        } elseif ($keyword != null && trim($keyword) != "" && $this->searchClosure != null) {         // For Custom Searching
            $c = $this->searchClosure;
            $list = $c($start, $rowPerPage, $keyword, $sortField, $sortOrder);

        } else {
            $this->beforeGetListViewData(function ($tableName, $findClause, $limit, $bindingData) use (&$list) {

                // For RedBean Case
                $list = R::find($tableName, $findClause . $limit, $bindingData);

            }, function ($sql, $limit, $bindingData) use (&$list) {

                // For SQL Case
                $list = R::getAll($sql . $limit, $bindingData);

                try {
                    $list = R::convertToBeans($this->tableName, $list);
                } catch (\Exception $ex) {

                }

            }, $start, $rowPerPage, $keyword, $sortField, $sortOrder);
        }

        return $list;
    }

    /**
     * Prepare the SQL or parameter for RedBean
     *
     * TODO: Sort by multiple fields?
     *
     * @param int $start
     * @param int $rowPerPage
     * @param string $keyword
     * @param string $sortField
     * @param null $sortOrder ASC/DESC
     * @param callable $callbackRedBean
     * @param callable $callbackSQL
     * @throws \RedBeanPHP\RedException\SQL
     */
    protected function beforeGetListViewData($callbackRedBean, $callbackSQL, $start = null, $rowPerPage = null, $keyword = null, $sortField = null, $sortOrder = null)
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
                $callbackSQL($this->sql, $limit, $this->bindingData);
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
                    $bindingData = array_merge($searchData, $bindingData);
                }

                // Sorting
                if ($sortField != null) {
                    $fakeSelect = "SELECT * FROM louislamcrud_fake_table WHERE ";

                    $parser = new PHPSQLParser($fakeSelect . $findClause);

                    $sqlArray = $parser->parsed;

                    $sqlArray["ORDER"][0]["expr_type"] = "colref";
                    $sqlArray["ORDER"][0]["base_expr"] = $sortField;
                    $sqlArray["ORDER"][0]["sub_tree"] = null;
                    $sqlArray["ORDER"][0]["direction"] = $sortOrder;

                    $findClause = str_replace($fakeSelect, "", (new PHPSQLCreator($sqlArray))->created);
                }

                $callbackRedBean($this->tableName, $findClause, $limit, $bindingData);
            }
        } catch (\RedBeanPHP\RedException\SQL $ex) {

                throw $ex;
        } catch(\Exception $ex) {
            // TODO: This should be for not existing table only, not other exceptions.

            // If the table is not existing create one, create the table and run this function again.
            $this->createTable();
            $this->beforeGetListViewData($callbackRedBean, $callbackSQL, $start, $rowPerPage, $keyword, $sortField, $sortOrder);
        }

    }

    protected function buildSearchingClause() {
        $searchClause = " ( ";

        $searchFields = $this->getShowFields();
        $isFirstSearchField = true;
        foreach ($searchFields as $searchField) {
            if ($searchField->isSearchable()) {
                if ($isFirstSearchField) {
                    $isFirstSearchField = false;
                } else {
                    $searchClause .= " OR ";
                }

                $fieldSearhingClosure = $searchField->getSearchingClosure();
                if($fieldSearhingClosure != null) {
                    $searchClause .= $fieldSearhingClosure($searchField) . ' ';
                } else {
                    $searchClause .= "UPPER(`" . $searchField->getName() . "`)" . " LIKE BINARY UPPER(?) ";
                }
            }
        }

        $searchClause .= " ) AND ";

        return $searchClause;
    }

    protected function buildSearchingData($keyword) {
        $keyword = trim($keyword);
        $searchData = [];

        $searchFields = $this->getShowFields();

        foreach ($searchFields as $searchField) {
            if ($searchField->isSearchable()) {
                $fieldSearhingDataClosure = $searchField->getSearchingDataClosure();
                if($fieldSearhingDataClosure != null) {
                    $searchData[] = $fieldSearhingDataClosure($searchField, $keyword);
                } else {
                    $searchData[] = "%$keyword%";
                }
            }
        }

        return R::flat($searchData);
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
            $keyword = trim($_POST["search"]["value"]);
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
        $obj->recordsTotal = $this->countTotalListViewData($keyword);
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
     * @param bool $echo echo?
     * @return string
     */
    public function getJSON($echo = true) {
        $bean = $this->getBean();

        $fields = $this->getShowFields();
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

    public function addFieldGroup($groupName, $fieldNameList) {
        $this->enableFieldGroup = true;
        $fieldGroup = new FieldGroup();
        $fieldGroup->setGroupName($groupName);

        foreach ($fieldNameList as $fieldName => $width) {
            $fieldGroup->addField($this->field($fieldName), $width);
        }

        $this->fieldGroupList[] = $fieldGroup;
    }

    public function getFieldGroupList() {
        return $this->fieldGroupList;
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
     * Store Data into Database
     * @param $data
     * @return int|string
     */
    public function insertBean($data)
    {
        $bean = R::xdispense($this->tableName);



        $result =  $this->saveBean($bean, $data);

        if (empty($result->msg)) {
            $result->msg = "The record has been created successfully.";
            $result->class = "callout-info";
            $result->ok = true;
        } else {
            $result->ok = false;
            $result->class = "callout-danger";

        }

        if ($this->afterInsertBean != null) {
            $callable = $this->afterInsertBean;
            $callable($bean, $result);
        }

        return $result;
    }

    /**
     * @param callable $afterUpdateBean
     */
    public function afterUpdate($afterUpdateBean)
    {
        $this->afterUpdateBean = $afterUpdateBean;
    }

    /**
     * @param callable $afterInsertBean
     */
    public function afterInsert($afterInsertBean)
    {
        $this->afterInsertBean = $afterInsertBean;
    }

    /**
     */
    public function beforeUpdate($beforeUpdateBean)
    {
        $this->beforeUpdateBean = $beforeUpdateBean;
    }


    /**
     * @param $beforeInsertBean
     */
    public function beforeInsert($beforeInsertBean)
    {
        $this->beforeInsertBean = $beforeInsertBean;
    }


    public function beforeStoreEachField($closure)
    {
        $this->beforeStoreEachField = $closure;
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
        if (empty($result->msg)) {
            $result->msg = "Saved.";
            $result->class = "callout-info";
        } else {
            $result->class = "callout-danger";
        }

        if ($this->afterUpdateBean != null) {
            $callable = $this->afterUpdateBean;
            $callable($this->currentBean, $result);
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
    protected function saveBean($bean, $data)
    {
        // Handle File Field that may not in the $data, because Filename always go into $_FILES.
        foreach ($_FILES as $fieldName => $file) {
            $data[$fieldName] = $file["name"];
        }

        // Store Showing fields only
        $fields = $this->getShowFields();

        foreach ($fields as $field) {
            $fieldType = $field->getFieldType();

            // Check is unique
            if ($field->isUnique()) {

                // Try to find duplicate beans
                $fieldName = $field->getName();
                $duplicateBeans = R::find($bean->getMeta('type'), " $fieldName = ? ", [$data[$field->getName()]]);

                if (count($duplicateBeans) > 0) {
                    // TODO
                }
            }

            if($fieldType->getBeforeSaveBeanClosure() != null) {
                $beforeSaveBeanClosure = $fieldType->getBeforeSaveBeanClosure();
                $beforeSaveBeanClosure($bean, $field->getStoreValue($data));
            } else if ($field->getFieldRelation() == Field::MANY_TO_MANY) {
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
        // Return result object
        $result = new Result();

        try {

            if ($bean->id > 0) {

                if ($this->beforeUpdateBean != null) {
                    $callable = $this->beforeUpdateBean;
                    $callable($this->currentBean);
                }

            } else {

                if ($this->beforeInsertBean != null) {
                    $callable = $this->beforeInsertBean;
                    $callable($bean);
                }

            }

            $id = R::store($bean);
            $needStore = false;
            foreach ($fields as $field) {
                $fieldType = $field->getFieldType();
                if($fieldType->getAfterSaveBeanClosure() != null) {
                    $afterSaveBeanClosure = $fieldType->getAfterSaveBeanClosure();
                    $needStore |= !!$afterSaveBeanClosure($bean, $field->getStoreValue($data));
                }
            }
            if($needStore) {
                $id = R::store($bean);
            }
            $result->id = $id;
            $result->redirect_url = Stringy::create($this->getCreateSuccURL())->replace("{id}", $id)->__toString();
        } catch (\Exception $ex) {
            $result->ok = false;
            $result->class = "callout-danger";


            if ($bean->id > 0) {
                $callback = $this->onUpdateError;
                $result->msg = $callback($ex->getMessage());
            } else {
                $callback = $this->onInsertError;
                $result->msg = $callback($ex->getMessage());
            }

        }

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
            return $this->template->exists("backend_layout") ? "backend_layout" : $this->theme . "::layout";
        } catch (\LogicException $ex) {
            return $this->theme . "::layout";
        }
    }

    /**
     * @param boolean $bool
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
     * @param boolean $bool
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

        if ($this->enableFieldGroup) {
            return $this->theme . "::edit2";
        } else {
            return $this->theme . "::edit";
        }
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

    public function upload($fieldName = "upload", $folder = null) {

        if ($folder == null) {
            $folder = "upload" . DIRECTORY_SEPARATOR;
        }

        if (isset($_FILES[$fieldName])) { 

            $filenameArray = explode(".", $_FILES[$fieldName]["name"]);

            if (count($filenameArray) >=2) {
                $ext = $filenameArray[count($filenameArray) - 1];
            } else {
                $ext = "";
            }
            
            if (! in_array($ext, $this->allowExt)) {
                return  [
                    "fileName" => "",
                    "uploaded" => 0,
                    "url" => "",
                    "status" => "FAIL",
                    "msg" => "Format is not allowed."
                ];
            }

            $filename = dechex(rand(1, 99999999)) . "-" . time() . "." . $ext;

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

    /**
     * TODO
     * @param string $fieldName
     * @param string $folder
     * @param null $width
     * @param null $height
     * @return null
     */
    public function uploadImage($fieldName = "upload", $folder = "upload/", $width = null, $height = null) {
        return null;
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
        if (! empty($_SERVER["QUERY_STRING"])) {
            return $this->listViewJSONLink . "?" . $_SERVER["QUERY_STRING"];
        } else {
            return $this->listViewJSONLink;
        }
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
        if ($this->createSuccURL != null)
            return $this->createSuccURL;
        else
            return $this->getListViewLink();
    }

    /**
     * @param string $createSuccURL
     */
    public function setCreateSuccURL($createSuccURL)
    {
        $this->createSuccURL = $createSuccURL;
    }

    /**
     * @deprecated
     * @return null
     */
    public function getBodyEndAssets()
    {
        return null;
    }

    /**
     * @deprecated
     * @return null
     */
    public function getHeadAssets()
    {
        return null;
    }

    /**
     * @param $script
     */
    public function addScript($script) {
        $this->bodyEndHTML .= $script;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getScript() {
        return $this->bodyEndHTML;
    }

    public function addBodyEndHTML($html) {
        $this->bodyEndHTML .= $html;
    }

    public function getBodyEndHTML() {
        return $this->bodyEndHTML;
    }

    /**
     * @param $html
     */
    public function addHeadHTML($html) {
        $this->headHTML .= $html;
    }

    /**
     * @return string
     */
    public function getHeadHTML() {
        return $this->headHTML;
    }

    /**
     * @return string
     */
    public function getEditName()
    {
        return $this->editName;
    }

    /**
     * @param string $editName
     */
    public function setEditName($editName)
    {
        $this->editName = $editName;
    }

    /**
     * @return string
     */
    public function getDeleteName()
    {
        return $this->deleteName;
    }

    /**
     * @param string $deleteName
     */
    public function setDeleteName($deleteName)
    {
        $this->deleteName = $deleteName;
    }

    /**
     * @return string
     */
    public function getCreateName()
    {
        return $this->createName;
    }

    /**
     * @param string $createName
     */
    public function setCreateName($createName)
    {
        $this->createName = $createName;
    }

    /**
     * @return \Closure
     */
    public function getOnInsertError()
    {
        return $this->onInsertError;
    }

    /**
     * @param \Closure $onInsertError
     */
    public function setOnInsertError($onInsertError)
    {
        $this->onInsertError = $onInsertError;
    }

    /**
     * @return \Closure
     */
    public function getOnUpdateError()
    {
        return $this->onUpdateError;
    }

    /**
     * @param \Closure $onUpdateError
     */
    public function setOnUpdateError($onUpdateError)
    {
        $this->onUpdateError = $onUpdateError;
    }

    /**
     * @return bool
     */
    public function isEnabledSearch()
    {
        return $this->enableSearch;
    }

    /**
     * @param bool $enableSearch
     */
    public function enableSearch($enableSearch)
    {
        $this->enableSearch = $enableSearch;
    }

    /**
     * @return bool
     */
    public function isEnabledSorting()
    {
        return $this->enableSorting;
    }

    /**
     * @param bool $enableSorting
     */
    public function enableSorting($enableSorting)
    {
        $this->enableSorting = $enableSorting;
    }

    /**
     * @return callable
     */
    public function getSearchClosure()
    {
        return $this->searchClosure;
    }

    /**
     * @param callable $searchClosure  function ()
     */
    public function setSearchClosure($searchClosure)
    {
        $this->searchClosure = $searchClosure;
    }

    /**
     * @return callable
     */
    public function getSearchResultCountClosure()
    {
        return $this->searchResultCountClosure;
    }

    /**
     * @param callable $searchResultCountClosure
     */
    public function setSearchResultCountClosure($searchResultCountClosure)
    {
        $this->searchResultCountClosure = $searchResultCountClosure;
    }

    /**
     * @param callable $listViewDataClosure
     */
    public function setListViewDataClosure($listViewDataClosure)
    {
        $this->listViewDataClosure = $listViewDataClosure;
    }

    /**
     * @param callable $countListViewDataClosure
     */
    public function setCountListViewDataClosure($countListViewDataClosure)
    {
        $this->countListViewDataClosure = $countListViewDataClosure;
    }



}