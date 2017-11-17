<?php
use LouisLam\CRUD\Field;
use LouisLam\CRUD\FieldType\IntegerType;
use LouisLam\CRUD\FieldType\TextField;
use LouisLam\CRUD\LouisCRUD;

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

    <?=$crud->getData("header") ?>

    <div class="row">

        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6">

            <div class="card">
                <div class="header bg-amber">
                    <h3 class="box-title">Edit</h3>
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
                            $html =  "<div class='form-group'><div class='form-line'>$innerHTML</div></div>";
                        }

                        echo $html;
                        ?>
                    <?php endforeach; ?>

                    <div class="btn-group">
                        <input type="submit" value="Save" class="btn btn-primary  btn-lg"/>

                        <?php if ($crud->isEnabledListView()) : ?>
                            <a  href="<?=$crud->getListViewLink() ?>" class="btn btn-default btn-lg">Back</a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>


                <div id="msg" >

                </div>

        </div>
    </div>

</form>



