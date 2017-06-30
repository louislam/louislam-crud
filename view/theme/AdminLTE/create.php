<?php
use LouisLam\CRUD\LouisCRUD;
use LouisLam\CRUD\Field;

/** @var Field[] $fields */
/** @var array $list */
/** @var LouisCRUD $crud */
/** @var string $layoutName*/
$listViewLink = $crud->getListViewLink();
$crud->addScript(<<< HTML
<script>
    crud.setAjaxFormCallback(function (result) {
        if (result.class == "callout-danger") {
                alertError(result.msg);
        } else {
               location.href = "$listViewLink";
        }
    });
</script>
HTML
);

$this->layout($layoutName, [
    "crud" => $crud
]);
?>

<form id="louis-form" action="<?=$crud->getCreateSubmitLink() ?>" data-method="post" class="ajax">

    <div class="row">

        <div class="col-md-10">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Create</h3>
                </div>

                    <div class="box-body">
                        <?php foreach($fields as $field) : ?>
                            <?= $field->render(false) ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="box-footer">
                        <input type="submit" value="Create" class="btn btn-primary" />

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


