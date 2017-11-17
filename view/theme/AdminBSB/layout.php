<?php
use LouisLam\CRUD\LouisCRUD;
use LouisLam\Util;
/** @var LouisCRUD $crud */
$adminLTESetting = \LouisLam\CRUD\AdminLTESetting::getInstance();
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=$crud->getData("title") ?></title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <link href="/node_modules/adminbsb-materialdesign/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= Util::res("vendor/fortawesome/font-awesome/css/font-awesome.min.css") ?>">

    <link href="/node_modules/adminbsb-materialdesign/plugins/node-waves/waves.css" rel="stylesheet" />
    <link href="/node_modules/adminbsb-materialdesign/plugins/animate-css/animate.css" rel="stylesheet" />
    <link href="/node_modules/adminbsb-materialdesign/css/style.css" rel="stylesheet">
    <link href="/node_modules/adminbsb-materialdesign/css/themes/all-themes.css" rel="stylesheet" />

    <link rel="stylesheet" href="<?= Util::res("vendor/datatables/datatables/media/css/dataTables.bootstrap.min.css") ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/louislam/louislam-crud/css/louis-crud.css"); ?>">
    <link rel="stylesheet" type="text/css" href="<?=Util::res("vendor/almasaeed2010/adminlte/plugins/select2/select2.min.css") ?>" />
    <link rel="stylesheet" type="text/css" href="<?=Util::res("vendor/bootstrap-select/bootstrap-select/dist/css/bootstrap-select.min.css") ?>" />

    <script src="<?= Util::res("vendor/louislam/louislam-crud/node_modules/sweetalert/dist/sweetalert.min.js") ?>"></script>
    <link rel="stylesheet" href="<?= Util::res("vendor/louislam/louislam-crud/node_modules/sweetalert/dist/sweetalert.css") ?>">

    <link rel="stylesheet" href="<?= Util::res("vendor/almasaeed2010/adminlte/plugins/daterangepicker/daterangepicker.css"); ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/almasaeed2010/adminlte/plugins/datepicker/datepicker3.css"); ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/almasaeed2010/adminlte/plugins/timepicker/bootstrap-timepicker.min.css"); ?>">

    <style>
        .sidebar .user-info {
           /* background: #607d8b;*/
            height: auto;
        }

        .sidebar .user-info .info-container {
            top: 0;
        }
    </style>

    <?=$crud->getHeadHTML(); ?>
    <?=@$crud->getData("head") ?>
</head>
<body  class="theme-red">

<!-- Page Loader -->
<div class="page-loader-wrapper">
    <div class="loader">
        <div class="preloader">
            <div class="spinner-layer pl-red">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
        <p>Please wait...</p>
    </div>
</div>

<div class="overlay"></div>
<!-- #END# Overlay For Sidebars -->
<!-- Search Bar -->
<div class="search-bar">
    <div class="search-icon">
        <i class="material-icons">search</i>
    </div>
    <input type="text" placeholder="START TYPING...">
    <div class="close-search">
        <i class="material-icons">close</i>
    </div>
</div>
<!-- #END# Search Bar -->
<!-- Top Bar -->
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
            <a class="navbar-brand" href="widgets//node_modules/adminbsb-materialdesign//node_modules/adminbsb-materialdesign/index.html"><?=$crud->getData("title") ?></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <!-- Call Search -->
                <li><a href="javascript:void(0);" class="js-search" data-close="true"><i class="material-icons">search</i></a></li>
                <!-- #END# Call Search -->
                <!-- Notifications -->
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        <i class="material-icons">notifications</i>
                        <span class="label-count">7</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">NOTIFICATIONS</li>
                        <li class="body">
                            <ul class="menu">
                                <li>
                                    <a href="javascript:void(0);">
                                        <div class="icon-circle bg-light-green">
                                            <i class="material-icons">person_add</i>
                                        </div>
                                        <div class="menu-info">
                                            <h4>12 new members joined</h4>
                                            <p>
                                                <i class="material-icons">access_time</i> 14 mins ago
                                            </p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <div class="icon-circle bg-cyan">
                                            <i class="material-icons">add_shopping_cart</i>
                                        </div>
                                        <div class="menu-info">
                                            <h4>4 sales made</h4>
                                            <p>
                                                <i class="material-icons">access_time</i> 22 mins ago
                                            </p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <div class="icon-circle bg-red">
                                            <i class="material-icons">delete_forever</i>
                                        </div>
                                        <div class="menu-info">
                                            <h4><b>Nancy Doe</b> deleted account</h4>
                                            <p>
                                                <i class="material-icons">access_time</i> 3 hours ago
                                            </p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <div class="icon-circle bg-orange">
                                            <i class="material-icons">mode_edit</i>
                                        </div>
                                        <div class="menu-info">
                                            <h4><b>Nancy</b> changed name</h4>
                                            <p>
                                                <i class="material-icons">access_time</i> 2 hours ago
                                            </p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <div class="icon-circle bg-blue-grey">
                                            <i class="material-icons">comment</i>
                                        </div>
                                        <div class="menu-info">
                                            <h4><b>John</b> commented your post</h4>
                                            <p>
                                                <i class="material-icons">access_time</i> 4 hours ago
                                            </p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <div class="icon-circle bg-light-green">
                                            <i class="material-icons">cached</i>
                                        </div>
                                        <div class="menu-info">
                                            <h4><b>John</b> updated status</h4>
                                            <p>
                                                <i class="material-icons">access_time</i> 3 hours ago
                                            </p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <div class="icon-circle bg-purple">
                                            <i class="material-icons">settings</i>
                                        </div>
                                        <div class="menu-info">
                                            <h4>Settings updated</h4>
                                            <p>
                                                <i class="material-icons">access_time</i> Yesterday
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="javascript:void(0);">View All Notifications</a>
                        </li>
                    </ul>
                </li>
                <!-- #END# Notifications -->
                <!-- Tasks -->
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        <i class="material-icons">flag</i>
                        <span class="label-count">9</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">TASKS</li>
                        <li class="body">
                            <ul class="menu tasks">
                                <li>
                                    <a href="javascript:void(0);">
                                        <h4>
                                            Footer display issue
                                            <small>32%</small>
                                        </h4>
                                        <div class="progress">
                                            <div class="progress-bar bg-pink" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 32%">
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <h4>
                                            Make new buttons
                                            <small>45%</small>
                                        </h4>
                                        <div class="progress">
                                            <div class="progress-bar bg-cyan" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <h4>
                                            Create new dashboard
                                            <small>54%</small>
                                        </h4>
                                        <div class="progress">
                                            <div class="progress-bar bg-teal" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 54%">
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <h4>
                                            Solve transition issue
                                            <small>65%</small>
                                        </h4>
                                        <div class="progress">
                                            <div class="progress-bar bg-orange" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 65%">
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <h4>
                                            Answer GitHub questions
                                            <small>92%</small>
                                        </h4>
                                        <div class="progress">
                                            <div class="progress-bar bg-purple" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 92%">
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="javascript:void(0);">View All Tasks</a>
                        </li>
                    </ul>
                </li>
                <!-- #END# Tasks -->
                <li class="pull-right"><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i class="material-icons">more_vert</i></a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- #Top Bar -->
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar main-sidebar">
        <!-- User Info -->
        <div class="user-info">

            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">John Doe</div>
                <div class="email">john.doe@example.com</div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void(0);"><i class="material-icons">person</i>Profile</a></li>
                        <li role="seperator" class="divider"></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">input</i>Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>

                <?=@$crud->getData("menu") ?>
            </ul>
        </div>

        <div class="legal">
            <?=@$crud->getData("footer") ?>
            <div class="copyright">

            </div>
            <div class="version">

            </div>
        </div>
    </aside>
</section>

<section class="content">
    <div class="container-fluid">
        <?=$this->section('content')?>
    </div>
</section>

<script src="<?=Util::res("vendor/components/jquery/jquery.min.js") ?>"></script>
<script src="<?=Util::res("vendor/components/jqueryui/jquery-ui.min.js") ?>"></script>

<script src="<?= Util::res("vendor/moment/moment/min/moment.min.js") ?>"></script>
<script src="<?= Util::res("vendor/almasaeed2010/adminlte/") ?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?=Util::res("vendor/datatables/datatables/media/js/jquery.dataTables.min.js") ?>"></script>
<script src="<?=Util::res("vendor/datatables/datatables/media/js/dataTables.bootstrap.min.js") ?>"></script>

<script src="/node_modules/adminbsb-materialdesign/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<script src="/node_modules/adminbsb-materialdesign/plugins/node-waves/waves.js"></script>


<script src="<?=Util::res("vendor/louislam/louislam-crud/js/LouisCRUD.js") ?>?v=2"></script>
<script src="<?=Util::res("vendor/louislam/louislam-utilities/js/L.js") ?>"></script>

<script src="<?=Util::res("vendor/ckeditor/ckeditor/ckeditor.js") ?>"></script>
<script src="<?=Util::res("vendor/ckeditor/ckeditor/adapters/jquery.js") ?>"></script>
<script src="<?=Util::res("vendor/bootstrap-select/bootstrap-select/dist/js/bootstrap-select.min.js") ?>"></script>
<script src="<?=Util::res("vendor/almasaeed2010/adminlte/plugins/select2/select2.min.js") ?>"></script>
<script src="<?= Util::res("vendor/almasaeed2010/adminlte/") ?>plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?= Util::res("vendor/almasaeed2010/adminlte/") ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?= Util::res("vendor/almasaeed2010/adminlte/") ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>


<script>
    var crud = new LouisCRUD();
    var BASE_URL = "<?="http://" . $_SERVER['SERVER_NAME'] ?>";
    var RES_URL = "<?=Util::res("") ?>";

    $(document).ready(function () {
    	if ($(".main-sidebar ul li.active").size() == 0) {
            $.AdminBSB.options.leftSideBar.scrollActiveItemWhenPageLoad = false;
        }
    });
</script>



<script src="/node_modules/adminbsb-materialdesign/js/admin.js"></script>

<?=$crud->getBodyEndHTML(); ?>

<?=@$crud->getData("bodyBeforeEnd") ?>

</body>
</html>
