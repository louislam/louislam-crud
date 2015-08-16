<?php
use LouisLam\CRUD\LouisCRUD;
use LouisLam\CRUD\Field;

/** @var Field[] $fields */
/** @var array $list */
/** @var LouisCRUD $crud */
/** @var string $layoutName*/

$this->layout($layoutName);
?>

<form action="<?=$crud->getCreateSubmitLink() ?>" data-method="post" class="ajax">

    <?php foreach($fields as $field) : ?>
        <?php $field->render() ?>
        <br />
    <?php endforeach; ?>


    <input type="submit" value="Create" />


</form>

<button onclick="history.back()">Back</button>

<script>
    crud.setAjaxFormCallback(function (result) {
        location.href = "<?=$crud->getListViewLink() ?>";
    });
</script>