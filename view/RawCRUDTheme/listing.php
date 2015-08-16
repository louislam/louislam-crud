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

<?php if ($crud->isEnabledCreate()) : ?>
    <a href="<?=$crud->getCreateLink() ?>">New</a>
<?php endif; ?>


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

                <?php if ($crud->isEnabledEdit()) : ?>
                    <a href="<?=$crud->getEditLink($bean->id) ?>">Edit</a>
                <?php endif; ?>


               <?php if ($crud->isEnabledDelete()) : ?>
                   <a class="btn-delete" href="javascript:void(0)" data-id="<?=$bean->id ?>" data-url="<?=$this->e($crud->getDeleteLink($bean->id)) ?>">Delete</a>
               <?php endif; ?>

                <?=$crud->getRowActionHTML(); ?>
            </td>

            <?php foreach ($fields as $field) : ?>
                    <td><?=$field->cellValue($bean); ?></td>
            <?php endforeach; ?>
        </tr>

    <?php endforeach; ?>
    </tbody>
</table>

