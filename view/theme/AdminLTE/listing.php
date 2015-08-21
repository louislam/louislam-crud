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
    <div class="row">
    	<div class="col-xs-12">
            <a class="btn btn-primary" href="<?=$crud->getCreateLink() ?>">New</a>
            <br />  <br />
        </div>

    </div>

<?php endif; ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <div class="box-header">
                <h3 class="box-title"></h3>
            </div>

            <div class="box-body" style="overflow-x: auto;">
                <table id="table" class="table table-bordered table-hover dataTable display"  cellspacing="0" >
                    <thead>
                    <tr>
                        <th>Actions</th>
                        <?php foreach ($fields as $field) : ?>
                            <th><?=$field->getDisplayName() ?></th>
                        <?php endforeach; ?>


                    </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th></th>
                            <?php foreach ($fields as $field) : ?>
                                <th></th>
                            <?php endforeach; ?>
                        </tr>
                    </tfoot>
                    <tbody>
                    <?php foreach ($list as $bean) : ?>
                        <tr id="row-<?=$bean->id ?>">
                            <td>

                                <?php if ($crud->isEnabledEdit()) : ?>
                                    <a href="<?=$crud->getEditLink($bean->id) ?>" class="btn btn-default">Edit</a>
                                <?php endif; ?>


                                <?php if ($crud->isEnabledDelete()) : ?>
                                    <a class="btn-delete btn btn-danger" href="javascript:void(0)" data-id="<?=$bean->id ?>" data-url="<?=$this->e($crud->getDeleteLink($bean->id)) ?>">Delete</a>
                                <?php endif; ?>

                                <!-- Action Closure -->
                                <?php if ($crud->getRowAction() != null) : ?>
                                    <?php
                                    $c = $crud->getRowAction();
                                    $c($bean);
                                    ?>
                                <?php endif; ?>

                            </td>

                            <!-- Cell -->
                            <?php foreach ($fields as $field) : ?>
                                <td><?=$field->cellValue($bean); ?></td>
                            <?php endforeach; ?>
                        </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>



