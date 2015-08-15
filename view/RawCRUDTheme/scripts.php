<?php
use LouisLam\CRUD\LouisCRUD;
use LouisLam\CRUD\Field;
use LouisLam\Util;

/** @var Field[] $fields */
/** @var array $list */
/** @var LouisCRUD $crud */
?>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
<script src="<?=Util::url("vendor/louislam/louislam-crud-library/js/LouisCRUD.js") ?>"></script>
<script>
    var crud = new LouisCRUD();
</script>