<?php
use LouisLam\CRUD\Field;
use LouisLam\CRUD\LouisCRUD;
use LouisLam\CRUD\Middleware\CSRFGuard;

/**
 * TODO
 */

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
                var box = $(' <div id="msg-callout" class="callout">' + result.msg + '</div>').addClass(result.class);
                $("#msg").html(box)
        }

    });
</script>
HTML
);

$this->layout($layoutName, [
    "crud" => $crud
]);

$fieldGroupList = $crud->getFieldGroupList();

?>

<form id="louis-form" action="<?= $crud->getEditSubmitLink($crud->getBean()->id) ?>" data-method="<?=$crud->getEditSubmitMethod() ?>" class="ajax">
    <?=CSRFGuard::inputTag() ?>

    <?=$crud->getData("header") ?>

    <div class="row">

        <!-- 客戶資料表 -->
        <?php foreach($fieldGroupList as $fieldGroup) : ?>
            <div class="col-md-10 col-xs-12 col-lg-8">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?=$fieldGroup->getGroupName(); ?></h3>
                    </div>

                    <div class="box-body">

                        <div class="row">
                            <?php foreach($fieldGroup->getFieldList() as $field) : ?>
                                <div class="col-xs-<?=$fieldGroup->getWidth($field->getName()) ?>">
                                    <?=$field->render(false) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>


                </div>




            </div>
        <?php endforeach; ?>



    </div>

    <div class="row">
        <div class="col-xs-12 col-xs-12 col-lg-8">

            <div class="box">

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



