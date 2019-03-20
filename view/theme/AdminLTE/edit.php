<?php
use LouisLam\CRUD\Field;
use LouisLam\CRUD\LouisCRUD;
use LouisLam\CRUD\Middleware\CSRFGuard;

/** @var Field[] $fields */
/** @var array $list */
/** @var LouisCRUD $crud */
/** @var string $layoutName */

$crud->addScript(<<< HTML
<script>
    crud.setAjaxFormCallback(function (result) {

        $("#msg").html("");

        if (result.class == "callout-danger") {
                alertError(result.msg);
        } else {
                var box = $(' <div id="msg-callout" class="callout">'+result.msg+'</div>').addClass(result.class);
                $("#msg").html(box)
        }

    });
</script>
HTML
);

$this->layout($layoutName, [
    "crud" => $crud
]);
?>

<form id="louis-form" action="<?= $crud->getEditSubmitLink($crud->getBean()->id) ?>" data-method="<?=$crud->getEditSubmitMethod() ?>" class="ajax">
    <?=CSRFGuard::inputTag() ?>
    <?=$crud->getData("header") ?>

    <div class="row">
        <!-- left column -->
        <div class="col-md-10">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->

                <div class="box-body">
                    <?php foreach ($fields as $field) : ?>
                        <?= $field->render(false) ?>
                    <?php endforeach; ?>
                </div>

                <div class="box-footer">
                    <input type="submit" value="Save" class="btn btn-primary"/>

                    <?php if ($crud->isEnabledListView()) : ?>
                        <a  href="<?=$crud->getListViewLink() ?>" class="btn btn-default">Back</a>
                    <?php endif; ?>

                </div>


            </div>


                <div id="msg" >

                </div>

        </div>
    </div>

</form>



