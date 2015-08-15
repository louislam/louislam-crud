<?php
use LouisLam\CRUD\LouisCRUD;
use LouisLam\CRUD\Field;

/** @var Field[] $fields */
/** @var array $list */
/** @var LouisCRUD $crud */

$this->layout('raw::layout', ['title' => '']);
?>

<form action="<?=$crud->getEditSubmitLink($crud->getBean()->id) ?>" data-method="put" class="ajax">

    <?php foreach($fields as $field) : ?>
        <?php $field->render() ?>
        <br />
    <?php endforeach; ?>


    <input type="submit" value="Edit" />

</form>