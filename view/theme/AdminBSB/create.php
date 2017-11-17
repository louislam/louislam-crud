<?php

use LouisLam\CRUD\FieldType\IntegerType;
use LouisLam\CRUD\FieldType\TextField;
use LouisLam\CRUD\LouisCRUD;
use LouisLam\CRUD\Field;

/** @var Field[] $fields */
/** @var array $list */
/** @var LouisCRUD $crud */
/** @var string $layoutName*/

$crud->addScript(<<< HTML
<script>
    crud.setAjaxFormCallback(function (result) {
        if (result.class == "callout-danger") {
            alertError(result.msg);
        } else {
            location.href = result.redirect_url;   
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

        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6">

            <div class="card">
                <div class="header bg-amber">
                    <h3 class="box-title">Create</h3>
                </div>

                    <div class="body">
                        <?php foreach($fields as $field) : ?>
                            <?php
                                $html = $field->render(false);

                                if (
                                    $field->getFieldType() instanceof TextField ||
                                    $field->getFieldType() instanceof IntegerType
                                ) {
                                    phpQuery::newDocumentHTML("<div id='main'>$html</div>");
                                    $element = pq(".form-group");
                                    $innerHTML = $element->html();
                                    echo "<div class='form-group'><div class='form-line'>$innerHTML</div></div>";
                                }
                            ?>
                        <?php endforeach; ?>

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


