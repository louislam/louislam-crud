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

<a href="<?=$crud->getCreateLink() ?>">New</a>

<table id="table" class="display">
    <thead>
    <tr>
        <th>Actions</th>
        <?php foreach ($fields as $field) : ?>
                <th><?=$field->getDisplayName() ?></th>
        <?php endforeach; ?>


    </tr>
    </thead>
    <tbody>
    <?php foreach ($list as $bean) : ?>
        <tr id="row-<?=$bean->id ?>">
            <td>
                <a href="<?=$crud->getEditLink($bean->id) ?>">Edit</a>
                <a class="btn-delete" href="javascript:void(0)" data-id="<?=$bean->id ?>" data-url="<?=$this->e($crud->getDeleteLink($bean->id)) ?>">Delete</a>
                <?=$crud->getRowActionHTML(); ?>
            </td>

            <?php foreach ($fields as $field) : ?>
                    <td><?=$bean->{$field->getName()} ?></td>
            <?php endforeach; ?>
        </tr>

    <?php endforeach; ?>
    </tbody>
</table>

