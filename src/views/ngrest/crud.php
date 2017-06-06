<?php
use luya\admin\ngrest\render\RenderCrud;
use luya\admin\Module;

/* @var $config \luya\admin\ngrest\ConfigInterface */
/* @var $this \luya\admin\ngrest\render\RenderCrudView */
/* @var $isInline boolean Whether current window mode is inline or not */
/* @var $relationCall boolena */
$this->beginPage();
$this->beginBody();
?>
<?php $this->registerAngularControllerScript(); ?>
<div ng-controller="<?= $config->hash; ?>" ng-init="init()" class="crud">
    <!-- This fake ui-view is used to render the detail item, which actuals uses the parent scope in the ui router controller. -->
    <div style="visibility:hidden;" ui-view></div>
    <?php if (!$relationCall): ?>
    <div class="crud-header">
        <h1 class="crud-title">TITEL</h1>
        <div class="crud-toolbar">
            <div class="btn-group" ng-class="{'show': toggleSettings}">
                <button class="btn btn-sm btn-link btn-icon" type="button" ng-click="toggleSettings=!toggleSettings">
                    <i class="material-icons">more_vert</i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" ng-show="!exportDownloadButton && !exportLoading" ng-click="exportData()">
                        <i class="material-icons">get_app</i> <span><?= Module::t('ngrest_crud_csv_export_btn'); ?></span>
                    </a>
                    <a class="dropdown-item" ng-show="exportLoading">
                        <i class="material-icons spin">cached</i>
                    </a>
                    <a class="dropdown-item" ng-show="exportDownloadButton" ng-click="exportDownload()">
                       <i class="material-icons">get_app</i><span> <?= Module::t('ngrest_crud_csv_export_btn_dl'); ?></span>
                    </a>
                    <?php // foreach buttons from external buttons list ?>
                </div>
            </div>
        </div>
    </div>
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link" ng-class="{'active':crudSwitchType==0}" ng-click="switchTo(0, true)">
                <i class="material-icons">list</i>
                <span><?= Module::t('ngrest_crud_btn_list'); ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" ng-class="{'active':crudSwitchType==1}" ng-click="switchTo(1)">
                <i class="material-icons">add_box</i>
                <span><?= Module::t('ngrest_crud_btn_add'); ?></span>
            </a>
        </li>
        <li class="nav-item" ng-show="crudSwitchType==2">
            <a class="nav-link" ng-class="{'active' : crudSwitchType==2}" ng-click="switchTo(0, true)">
                <i class="material-icons">cancel</i>
                <span><?= Module::t('ngrest_crud_btn_close'); ?></span>
            </a>
        </li>
        <li class="nav-item" ng-repeat="(index,btn) in tabService.tabs">
            <a class="nav-link" ng-class="{'active' : btn.active}">
                <i class="material-icons" ng-click="closeTab(btn, index)">cancel</i>
                <span ng-click="switchToTab(btn)">{{btn.name}} #{{btn.id}}</span>
            </a>
        </li>
        <li class="nav-item nav-item-border-only" ng-repeat="lang in AdminLangService.data" ng-class="{'ml-auto' : $first}">
            <a class="nav-link" ng-click="AdminLangService.toggleSelection(lang)" ng-class="{'active' : AdminLangService.isInSelection(lang.short_code)}" role="tab">
                <span class="flag flag--{{lang.short_code}}">
                    <span class="flag__fallback">{{lang.name}}</span>
                </span>
            </a>
        </li>
    </ul>
    <?php endif; ?>
    <div class="tab-content" ng-if="service">
        <?php if (!$relationCall): ?>
        <div class="tab-pane" ng-repeat="btn in tabService.tabs" ng-class="{'active' : btn.active}" ng-if="btn.active">
            <crud-relation-loader api="{{btn.api}}" array-index="{{btn.arrayIndex}}" model-class="{{btn.modelClass}}" id="{{btn.id}}"></crud-relation-loader>
        </div>
        <?php endif; ?>
        <div class="tab-pane" ng-if="crudSwitchType==0" ng-class="{'active' : crudSwitchType==0}">
            <div class="tab-padded">
                <div class="row mt-2">
                    <div class="col-md-4 col-lg-6 col-xl-6 col-xxxl-8">
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon">
                                <i class="material-icons">search</i>
                            </div>
                            <input class="form-control" ng-model="config.searchQuery" type="text" placeholder="<?= Module::t('ngrest_crud_search_text'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 col-xl-3 col-xxxl-2">
                        <select class="form-control" ng-change="changeGroupByField()" ng-model="config.groupByField">
                            <option value="0"><?= Module::t('ngrest_crud_group_prompt'); ?></option>
                            <?php foreach ($config->getPointer('list') as $item): ?>
                                <option value="<?= $item['name']; ?>"><?= $item['alias']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (!empty($config->getFilters())): ?>
                    <div class="col-md-4 col-lg-3 col-xl-3 col-xxxl-2">
                        <select class="form-control" ng-model="config.filter">
                            <option value="0"><?= Module::t('ngrest_crud_filter_prompt'); ?></option>
                            <?php foreach (array_keys($config->getFilters()) as $name): ?>
                                <option value="<?= $name; ?>"><?= $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <table class="table table-hover table-striped table-align-middle mt-4">
                <thead class="thead-default">
                    <tr>
                        <?php foreach ($config->getPointer('list') as $item): ?>
                        <th>
                            <span><?= $item['alias']; ?></span>
                            <div class="table-sorter table-sorter-up is-active">
                                <i class="material-icons">keyboard_arrow_up</i>
                            </div>
                            <div class="table-sorter table-sorter-down">
                                <i class="material-icons">keyboard_arrow_down</i>
                            </div>
                        </th>
                        <?php endforeach; ?>
                        <th class="tab-padding-right text-right">
                            <span class="crud-counter">{{data.listArray.length}} <?= Module::t('ngrest_crud_rows_count'); ?></span>
                        </th>
                    </tr>
                </thead>
                <tbody ng-repeat="(key, items) in data.listArray | groupBy: config.groupByField" ng-init="viewToggler[key]=true">
                    <tr ng-repeat="(k, item) in items | srcbox:config.searchString" ng-show="viewToggler[key]">
                        <?php foreach ($config->getPointer('list') as $item): ?>
                            <?php foreach ($this->context->createElements($item, RenderCrud::TYPE_LIST) as $element): ?>
                                 <td><?= $element['html']; ?></td>
                             <?php endforeach; ?>
                         <?php endforeach; ?>
                         <?php if (count($this->context->getButtons()) > 0): ?>
                        <td class="text-right">
                            <?php foreach ($this->context->getButtons() as $item): ?>
                            <button type="button" class="btn btn-sm btn-link btn-icon" ng-click="<?= $item['ngClick']; ?>"><i class="material-icons"><?= $item['icon']; ?></i></button>
                            <?php endforeach; ?>
                            <!-- 
                            <div class="btn-group">
                                <button class="btn btn-sm btn-link btn-icon" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#"><i class="material-icons">build</i> <span>Build</span></a>
                                    <a class="dropdown-item" href="#"><i class="material-icons">done</i> <span>Mark as done</span></a>
                                    <a class="dropdown-item" href="#"><i class="material-icons">report</i> <span>Report</span></a>
                                </div>
                            </div>
                             -->
                        </td>
                        <?php endif; ?>
                    </tr>
                </tbody>
            </table>
            <ul class="pagination" ng-if="pager && !config.pagerHiddenByAjaxSearch">
                <li class="page-item page-item-icon disabled" ng-class="{'disabled' : pager.currentPage == 1}" >
                    <a class="page-link" ng-click="pagerPrevClick()" tabindex="-1"><i class="material-icons">keyboard_arrow_left</i></a>
                </li>
                <li class="page-item active" ng-repeat="pageId in pager.pages" ng-class="{'active': pageId == pager.currentPage}">
                    <a class="page-link" ng-click="realoadCrudList(pageId)">{{pageId}}</a>
                </li>
                <li class="page-item page-item-icon" ng-class="{'disabled' : pager.currentPage == pager.pageCount}">
                    <a class="page-link"  ng-click="pagerNextClick()"><i class="material-icons">keyboard_arrow_right</i></a>
                </li>
            </ul>
        </div>
        <?= $this->render('_crudform', ['type' => '1', 'renderer' => RenderCrud::TYPE_CREATE, 'isInline' => $isInline]); ?>
        <?= $this->render('_crudform', ['type' => '2', 'renderer' => RenderCrud::TYPE_UPDATE, 'isInline' => $isInline]); ?>
        <?= $this->render('_awform'); ?>
    </div>
</div>
<?php $this->endBody(); ?>
<?php $this->endPage(); ?>