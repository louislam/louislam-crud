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

    <link rel="stylesheet" href="<?= Util::res("vendor/almasaeed2010/adminlte/bootstrap/css/bootstrap.min.css") ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/fortawesome/font-awesome/css/font-awesome.min.css") ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/almasaeed2010/adminlte/dist/css/AdminLTE.min.css") ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/almasaeed2010/adminlte/dist/css/skins/_all-skins.min.css") ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/datatables/datatables/media/css/dataTables.bootstrap.min.css") ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/louislam/louislam-crud/css/louis-crud.css"); ?>">
    <link rel="stylesheet" type="text/css" href="<?=Util::res("vendor/select2/select2/dist/css/select2.min.css") ?>" />
    <link rel="stylesheet" type="text/css" href="<?=Util::res("vendor/bootstrap-select/bootstrap-select/dist/css/bootstrap-select.min.css") ?>" />

    <script src="<?= Util::res("vendor/louislam/louislam-crud/node_modules/sweetalert/dist/sweetalert.min.js") ?>"></script>
    <link rel="stylesheet" href="<?= Util::res("vendor/louislam/louislam-crud/node_modules/sweetalert/dist/sweetalert.css") ?>">

    <link rel="stylesheet" href="<?= Util::res("vendor/almasaeed2010/adminlte/plugins/daterangepicker/daterangepicker.css"); ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/almasaeed2010/adminlte/plugins/datepicker/datepicker3.css"); ?>">
    <link rel="stylesheet" href="<?= Util::res("vendor/almasaeed2010/adminlte/plugins/timepicker/bootstrap-timepicker.min.css"); ?>">

    <?=$crud->getHeadHTML(); ?>
    <?=@$crud->getData("head") ?>
</head>
<body class="hold-transition sidebar-mini <?=$adminLTESetting->getSkin() ?>">
<div class="wrapper">

    <header class="main-header">
        <a href="#" class="logo">

            <span class="logo-mini"></span>
            <span class="logo-lg"><?=$crud->getData("title") ?></span>
        </a>

        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle</span>
            </a>
            <div class="navbar-custom-menu">

            </div>
        </nav>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">

            <div class="user-panel">
                <?=@$crud->getData("userPanel") ?>
            </div>

            <ul class="sidebar-menu">
                <?=@$crud->getData("menu") ?>
            </ul>
        </section>
    </aside>

    <div class="content-wrapper">
        <section class="content-header"> </section>

        <section class="content">
            <?=$this->section('content')?>
        </section>
    </div>

    <footer class="main-footer">
        <?=@$crud->getData("footer") ?>
    </footer>
</div>

<script src="<?=Util::res("vendor/components/jquery/jquery.min.js") ?>"></script>
<script src="<?=Util::res("vendor/components/jqueryui/jquery-ui.min.js") ?>"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>

<script src="<?= Util::res("vendor/almasaeed2010/adminlte/") ?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?=Util::res("vendor/datatables/datatables/media/js/jquery.dataTables.min.js") ?>"></script>
<script src="<?=Util::res("vendor/datatables/datatables/media/js/dataTables.bootstrap.min.js") ?>"></script>
<script src="<?=Util::res("vendor/louislam/louislam-crud/js/LouisCRUD.js") ?>?v=2"></script>
<script src="<?=Util::res("vendor/louislam/louislam-utilities/js/L.js") ?>"></script>
<script src="<?=Util::res("vendor/ckeditor/ckeditor/ckeditor.js") ?>"></script>
<script src="<?=Util::res("vendor/ckeditor/ckeditor/adapters/jquery.js") ?>"></script>
<script src="<?=Util::res("vendor/select2/select2/dist/js/select2.min.js") ?>"></script>
<script src="<?=Util::res("vendor/bootstrap-select/bootstrap-select/dist/js/bootstrap-select.min.js") ?>"></script>
<script src="<?=Util::res("vendor/louislam/louislam-crud/js/moment.min.js") ?>"></script>
<script src="<?= Util::res("vendor/almasaeed2010/adminlte/") ?>plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?= Util::res("vendor/almasaeed2010/adminlte/") ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?= Util::res("vendor/almasaeed2010/adminlte/") ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

<script src="<?= Util::res("vendor/almasaeed2010/adminlte/") ?>dist/js/app.min.js"></script>

<script>
    var crud = new LouisCRUD();
    var BASE_URL = "<?="http://" . $_SERVER['SERVER_NAME'] ?>";
    var RES_URL = "<?=Util::res("") ?>";
</script>

<?=$crud->getBodyEndHTML(); ?>

<?=@$crud->getData("bodyBeforeEnd") ?>

</body>
</html>
