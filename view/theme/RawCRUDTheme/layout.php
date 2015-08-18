<?php
/** @var LouisCRUD $crud */
use LouisLam\CRUD\LouisCRUD;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Louis CRUD</title>
    <?=$this->insert("raw::css") ?>
    <?=$this->insert('raw::scripts')?>
    <?=$crud->getHTMLHead() ?>
</head>
<body>
<?=$crud->getLayoutHeader() ?>
<?=$this->section('content')?>
<?=$crud->getLayoutFooter() ?>
</body>
</html>