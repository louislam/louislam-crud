<?php
use LouisLam\CRUD\LouisCRUD;

/** @var LouisCRUD $crud */
/** @var \Stolz\Assets\Manager $assets */
/** @var \DebugBar\JavascriptRenderer $debugbarRenderer */

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

    <?=@$crud->getData("head") ?>
    <?php echo $debugbarRenderer->renderHead() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
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

<?=@$crud->getData("bodyBeforeEnd") ?>

<?php
    echo $assets->js();
    echo $debugbarRenderer->render();
?>


</body>
</html>
