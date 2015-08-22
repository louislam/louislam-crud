<?php
/** @var LouisCRUD $crud */
use LouisLam\CRUD\LouisCRUD;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=$crud->getData("title") ?></title>

    <?=$this->insert("adminlte::css") ?>
    <?=$this->insert('adminlte::scripts')?>
    <?=$crud->getHTMLHead() ?>

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="index2.html" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"></span>
            <span class="logo-lg"><?=$crud->getData("title") ?></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
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

        <section class="content-header">



        </section>

        <!-- Main content -->
        <section class="content">
            <?=$this->section('content')?>

        </section>
    </div>
    <footer class="main-footer">
        <?=$crud->getLayoutFooter() ?>
    </footer>


</div>

</body>
</html>
