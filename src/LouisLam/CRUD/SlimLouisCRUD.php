<?php
namespace LouisLam\CRUD;

use LouisLam\Util;
use Slim\Slim;

/**
 * Class SlimLouisCRUD
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 10:37 AM
 * @package LouisLam\CRUD
 */
class SlimLouisCRUD extends LouisCRUD
{
    private $groupName;
    private $apiGroupName;

    /** @var Slim */
    private $slim;

    /** @var callable */
    private $configFunction;

    /** @var callable */
    protected $listviewFunction = null;

    /** @var callable */
    protected $createFunction = null;

    /** @var callable */
    protected $editFunction = null;

    /** @var callable */
    private $deleteFunction = null;

    /** @var string[] */
    private $tableList = [];
    private $routeNameList = [];

    private $currentRouteName = "";


    /**
     * SlimCRUD constructor.
     * @param string $groupName
     * @param string $apiGroupName
     * @param Slim $slim
     */
    public function __construct($groupName = "crud", $apiGroupName = "api", $slim = null)
    {
        parent::__construct();
        $this->groupName = $groupName;
        $this->apiGroupName = $apiGroupName;

        if ($slim == null) {
            $this->slim = new Slim();
        } else {
            $this->slim = $slim;
        }

    }

    private function init($tableName, $routeName) {
        // Table Name set this time ONLY.
        $this->setTable($tableName);

        $this->currentRouteName = $routeName;

        // Link
        $this->setListViewLink(Util::url($this->groupName . "/" . $routeName));
        $this->setCreateLink(Util::url($this->groupName . "/" . $routeName . "/create"));
        $this->setCreateSubmitLink(Util::url($this->apiGroupName . "/" . $routeName));
        $this->setEditLink(Util::url($this->groupName . "/" . $routeName . "/edit/:id"));
        $this->setEditSubmitLink(Util::url($this->apiGroupName . "/" . $routeName . "/:id"));
        $this->setDeleteLink(Util::url($this->apiGroupName . "/" . $routeName . "/:id"));
    }

    /**
     * @param string $tableName
     * @param callable $customCRUDFunction
     * @param string $routeName
     */
    public function add($tableName, $customCRUDFunction = null, $routeName = null)
    {
        if ($routeName == null) {
            $routeName = $tableName;
        }

        $this->tableList[$routeName] = $tableName;
        $this->routeNameList[] = $routeName;

        /*
         * Page Group (ListView, CreateView, EditView)
         */
        $this->slim->group("/" . $this->groupName . "/" . $routeName, function () use ($routeName, $customCRUDFunction, $tableName) {

            $this->slim->get("/", function () use ($routeName)  {
                $this->slim->redirectTo("_louisCRUD_" . $routeName);
            });

            // ListView
            $this->slim->get("/list(/:p1/(:p2/(:p3/(:p4/(:p5/)))))", function ($p1 = null, $p2 = null, $p3 = null, $p4 = null, $p5 = null) use ($routeName, $customCRUDFunction, $tableName) {

                // MUST INIT FIRST
                $this->init($tableName, $routeName);

                if ($this->configFunction != null) {
                    $function = $this->configFunction;
                    $result = $function();

                    if ($result === false) {
                        return;
                    }
                }

                if ($customCRUDFunction != null) {
                    $result = $customCRUDFunction();

                    if ($result === false) {
                        return;
                    }
                }

                if ($this->listviewFunction != null) {
                    $listviewFunction = $this->listviewFunction;
                    $result = $listviewFunction($p1, $p2, $p3, $p4, $p5);

                    if ($result === false) {
                        return;
                    }
                }

                $this->renderListView();
                return;
            })->name("_louisCRUD_" . $routeName);

            // Create
            $this->slim->get("/create", function () use ($routeName, $customCRUDFunction, $tableName) {

                // MUST INIT FIRST
                $this->init($tableName, $routeName);

                if ($this->configFunction != null) {
                    $function = $this->configFunction;
                    $result = $function();

                    if ($result === false) {
                        return;
                    }
                }

                if ($customCRUDFunction != null) {
                    $result = $customCRUDFunction();

                    if ($result === false) {
                        return;
                    }
                }

                if ($this->createFunction != null) {
                    $createFunction = $this->createFunction;
                    $result = $createFunction();

                    if ($result === false) {
                        return;
                    }
                }

                // Force Hide ID field
                $this->field("id")->hide();

                $this->renderCreateView();
            });

            // Edit
            $this->slim->get("/edit/:id", function ($id) use ($routeName, $customCRUDFunction, $tableName) {

                // MUST INIT FIRST
                $this->init($tableName, $routeName);

                // Load Bean first
                $this->loadBean($id);

                // ID must be hidden
                $this->field("id")->hide();

                if ($this->configFunction != null) {
                    $function = $this->configFunction;
                    $result = $function();

                    if ($result === false) {
                        return;
                    }
                }

                if ($customCRUDFunction != null) {
                    $result = $customCRUDFunction();

                    if ($result === false) {
                        return;
                    }
                }

                if ($this->editFunction != null) {
                    $editFunction = $this->editFunction;
                    $result = $editFunction($id);

                    if ($result === false) {
                        return;
                    }
                }

                // If user show the ID field, force set it to readonly
                $this->field("id")->setReadOnly(true);

                $this->renderEditView();
            });

        });

        /*
         * API Group, RESTful style.
         */
        $this->slim->group("/" . $this->apiGroupName . "/" . $routeName, function () use ($routeName, $customCRUDFunction, $tableName) {

            /*
             * Insert a bean
             * POST /crud/{tableName}
             */
            $this->slim->post("/", function () use ($routeName, $customCRUDFunction, $tableName) {

                // MUST INIT FIRST
                $this->init($tableName, $routeName);

                if ($this->configFunction != null) {
                    $function = $this->configFunction;
                    $result = $function();

                    if ($result === false) {
                        return;
                    }
                }

                if ($customCRUDFunction != null) {
                    $result = $customCRUDFunction();

                    if ($result === false) {
                        return;
                    }
                }

                // Custom Global Function
                $result = $customCRUDFunction();

                if ($result === false) {
                    return;
                }

                // Custom Create Function
                if ($this->createFunction != null) {
                    $createFunction = $this->createFunction;
                    $result = $createFunction();
                }

                if ($result === false) {
                    return;
                }

                // Force hide ID
                $this->field("id")->hide();

                // Insert into database
                $this->insertBean($_POST);

            });

            /*
             * Update a bean
             * PUT /crud/{tableName}/{id}
             */
            $this->slim->put("/:id", function ($id) use ($routeName, $customCRUDFunction, $tableName) {

                // MUST INIT FIRST
                $this->init($tableName, $routeName);

                // Load Bean
                $this->loadBean($id);

                if ($this->configFunction != null) {
                    $function = $this->configFunction;
                    $result = $function();

                    if ($result === false) {
                        return;
                    }
                }

                // Custom Global Function
                if ($customCRUDFunction != null) {
                    $result = $customCRUDFunction();

                    if ($result === false) {
                        return;
                    }
                }

                // Custom Create Function
                if ($this->editFunction != null) {
                    $editFunction = $this->editFunction;
                    $result = $editFunction($id);

                    if ($result === false) {
                        return;
                    }
                }

                // Force hide ID
                $this->field("id")->hide();

                // Insert into database

                $jsonObject = $this->updateBean($this->slim->request()->params());

                $this->enableJSONResponse();
                echo json_encode($jsonObject);
            });

            /*
             * Delete a bean
             * DELETE /crud/{tableName}/{id}
             */
            $this->slim->delete("/:id", function ($id) use ($routeName, $customCRUDFunction, $tableName) {

                // MUST INIT FIRST
                $this->init($tableName, $routeName);

                $this->enableJSONResponse();

                $this->loadBean($id);

                if ($this->configFunction != null) {
                    $function = $this->configFunction;
                    $result = $function();

                    if ($result === false) {
                        return;
                    }
                }

                // Custom Global Function
                if ($customCRUDFunction != null) {
                    $result = $customCRUDFunction();

                    if ($result === false) {
                        return;
                    }
                }

                // Custom Delete Function
                if ($this->deleteFunction != null) {
                    $deleteFunction = $this->deleteFunction;
                    $result =  $deleteFunction($id);

                    if ($result === false) {
                        return;
                    }
                }

                $this->deleteBean();

                $result = new \stdClass();
                $result->status = "succ";

                echo json_encode($result);
            });

        });

    }

    /**
     * @param callable $func
     */
    public function config($func) {
        $this->configFunction = $func;
    }

    /**
     * @param callable $func
     */
    public function listView($func)
    {
        $this->listviewFunction = $func;
    }

    /**
     * @param callable $func
     */
    public function create($func)
    {
        $this->createFunction = $func;
    }

    /**
     * @param callable $func
     */
    public function edit($func)
    {
        $this->editFunction = $func;
    }

    /**
     * @param callable $func
     */
    public function delete($func)
    {
        $this->deleteFunction = $func;
    }

    /**
     * @return Slim
     */
    public function getSlim()
    {
        return $this->slim;
    }

    public function run()
    {
        $this->slim->run();
    }

    public function enableJSONResponse()
    {
        $this->slim->response->header('Content-Type', 'application/json');
    }

    /**
     * @return string
     */
    public function generateMenu() {
        $temp = "<nav><ul>";

        foreach ($this->routeNameList as $routeName) {
            $name = $this->tableList[$routeName];
            $url = $this->slim->urlFor("_louisCRUD_" . $name);
            $temp .= "<li><a href='$url'>$routeName</a></li>";
        }

        $temp .= "</ul></nav>";
        return $temp;
    }



}