<?php
use LouisLam\CRUD\LouisCRUD;
use LouisLam\CRUD\Field;

/** @var Field[] $fields */
/** @var array $list */
/** @var LouisCRUD $crud */
/** @var string $layoutName*/

$this->layout($layoutName, [
    "crud" => $crud
]);
?>

    <div class="row">

        <div class="col-md-6">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?=$title ?></h3>
                </div>


                    <div class="box-body">
                        <?=$msg ?>
                    </div>

                    <div class="box-footer">

                    </div>

            </div>
        </div>
    </div>
