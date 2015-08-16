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

<form action="<?=$crud->getEditSubmitLink($crud->getBean()->id) ?>" data-method="put" class="ajax">

    <?php foreach($fields as $field) : ?>
        <?php $field->render() ?>
        <br />
    <?php endforeach; ?>

    <div id="msg" style="color:red"></div>

    <input type="submit" value="Save" />


</form>

<button onclick="history.back()">Back</button>

<script>
    crud.setAjaxFormCallback(function (result) {
        $("#msg").html(result.msg);
    });
</script>