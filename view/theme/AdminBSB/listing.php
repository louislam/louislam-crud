<?php
use LouisLam\CRUD\LouisCRUD;
use LouisLam\CRUD\Field;

/** @var Field[] $fields */
/** @var array $list */
/** @var LouisCRUD $crud */
/** @var string $layoutName*/

$isAjax = ($crud->isAjaxListView()) ? "true" : "false";
$jsonLink = $crud->getListViewJSONLink();
$enableSearch = $crud->isEnabledSearch() ? "true" : "false";
$enableSorting = $crud->isEnabledSorting() ? "true" : "false";

$crud->addBodyEndHTML(<<< JS
<script>
    var isAjax = $isAjax;
    var ajaxUrl = "$jsonLink";
    var enableSearch = $enableSearch;
    var enableSorting = $enableSorting;
    crud.initListView(isAjax, ajaxUrl, enableSearch, enableSorting);
</script>
JS
);

$this->layout($layoutName);

?>


<?=$crud->getData("header") ?>


<div class="row">
    <div class="col-xs-12">
        <div class="card">

            <div class="header">

                    <?=$crud->getTableDisplayName() ?>

                <div class="btn-group">
                    <?php if ($crud->isEnabledCreate()) : ?>
                        <!-- Create Button -->
                        <a class="btn btn-primary btn-lg" href="<?=$crud->getCreateLink() ?>"><?=$crud->getCreateName(); ?></a>
                    <?php endif; ?>

                    <!-- Filter -->
                    <div class="dropdown column-filter" style="display: inline-block">
                        <button class="btn btn-default dropdown-toggle  btn-lg" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" >
                            Column Filter
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu column-selector">

                            <?php
                            $i =1;
                            ?>

                            <?php foreach ($fields as $field) : ?>
                                <li>
                                    <a href="#">
                                        <label>
                                            <input data-column="<?=$i++ ?>" type="checkbox" checked />
                                            <?=$field->getDisplayName() ?>
                                        </label>
                                    </a>
                                </li>
                            <?php endforeach; ?>


                        </ul>
                    </div>

                    <!-- Export Button -->
                    <a class="btn btn-default  btn-lg" href="<?=$crud->getExportLink() ?>">Export Excel</a>
                </div>
            </div>

            <div class="body" style="overflow-x: auto;">
                <table id="louis-crud-table" class="table table-bordered table-hover dataTable display"  cellspacing="0" >
                    <thead>
                    <tr>
                        <!-- colspan="2"-->
                        <th>Actions</th>

                         <!-- Column Header -->
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

                     <!--       <td>
                                <label><input type="checkbox" value="<?/*=$bean->id */?>" /> </label>
                            </td>-->

                            <!-- Action TD -->
                            <td>
                                <?php if ($crud->isEnabledEdit()) : ?>
                                    <a href="<?=$crud->getEditLink($bean->id) ?>" class="btn btn-default"><?=$crud->getEditName() ?></a>
                                <?php endif; ?>


                                <?php if ($crud->isEnabledDelete()) : ?>
                                    <a class="btn-delete btn btn-danger" href="javascript:void(0)" data-id="<?=$bean->id ?>" data-url="<?=$this->e($crud->getDeleteLink($bean->id)) ?>"><?=$crud->getDeleteName() ?></a>
                                <?php endif; ?>

                                <!-- Action Closure -->
                                <?php if ($crud->getRowAction() != null) : ?>
                                    <?php
                                    $c = $crud->getRowAction();
                                    echo $c($bean);
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



