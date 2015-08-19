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

<form action="<?=$crud->getCreateSubmitLink() ?>" data-method="post" class="ajax">

    <div class="row">
        <!-- left column -->
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Quick Example</h3>
                </div><!-- /.box-header -->
                <!-- form start -->

                    <div class="box-body">
                        <?php foreach($fields as $field) : ?>
                            <?php $field->render() ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="box-footer">
                        <input type="submit" value="Create" class="btn btn-primary" />
                    </div>

            </div>
        </div>
    </div>







</form>

<button onclick="history.back()">Back</button>

<script>
    crud.setAjaxFormCallback(function (result) {
        location.href = "<?=$crud->getListViewLink() ?>";
    });
</script>