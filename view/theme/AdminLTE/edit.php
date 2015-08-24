<?php
use LouisLam\CRUD\Field;
use LouisLam\CRUD\LouisCRUD;

/** @var Field[] $fields */
/** @var array $list */
/** @var LouisCRUD $crud */
/** @var string $layoutName */

$this->layout($layoutName, [
    "crud" => $crud
]);
?>

<form action="<?= $crud->getEditSubmitLink($crud->getBean()->id) ?>" data-method="put" class="ajax">

    <div class="row">
        <!-- left column -->
        <div class="col-md-6">

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

            <div id="msg-callout" class="callout callout-info" style="display:none">
                <p id="msg">
                </p>
            </div>
        </div>
    </div>

</form>



<script>
    crud.setAjaxFormCallback(function (result) {
        $("#msg-callout").show();
        $("#msg").html(result.msg);
    });
</script>