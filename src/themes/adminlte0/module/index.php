<?php
/**
 * @link      https://github.com/EngineCore/module-extension
 * @copyright Copyright (c) 2020 E-Kevin
 * @license   BSD 3-Clause License
 */

use EngineCore\Ec;
use EngineCore\extension\repository\info\ExtensionInfo;
use wonail\adminlte\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider ArrayDataProvider */
/* @var $runList array */
/* @var $app string */

$headerToolbar = '';
$this->params['breadcrumbs'] = Ec::$service->getMenu()->getPage()->setLevel(3)->getBreadcrumbs();
$this->title = Ec::$service->getMenu()->getPage()->getTitle();
//$this->params['navSelectPage'] = Ec::$service->getExtension()->getUrlManager()->getUrl('module/index');
?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <?php
        foreach (\EngineCore\enums\AppEnum::list() as $k => $name) {
            if ($k == $app) {
                echo Html::tag('li', Html::a($name, "#{$k}", ['data-toggle' => 'tab']), [
                    'class' => 'active',
                ]);
            } else {
                echo Html::tag('li', Html::a($name, ['index', 'app' => $k], ['data-pjax' => 1]));
            }
        }
        ?>
        <li class="pull-right">
            <?= Html::a('清理缓存', ['clear-cache', 'app' => $app], [
                'class'       => 'text-yellow',
                'data-method' => 'post',
            ]) ?>
        </li>
    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="avatar">
            <?php
            $column = [
                [
                    'label'  => '扩展名',
                    'format' => 'raw',
                    'value'  => function ($infoInstance, $key) use ($app) {
                        /** @var ExtensionInfo $infoInstance */
                        return $infoInstance->getCanInstall()
                            ? $key
                            : Html::a($key, ['update', 'id' => $key, 'app' => $app], ['data-pjax' => 1]);
                    },
                ],
                [
                    'attribute' => 'name',
                    'format'    => 'html',
                    'value'     => function ($infoInstance) {
                        /** @var ExtensionInfo $infoInstance */
                        if ($infoInstance->getIsSystem()) {
                            return '<span class="text-danger">' . $infoInstance->getName() . '</span>';
                        } else {
                            if ($infoInstance->getCanInstall()) {
                                return '<span class="text-success">' . $infoInstance->getName() . '</span>';
                            } else {
                                return '<span class="text-warning">' . $infoInstance->getName() . '</span>';
                            }
                        }
                    },
                    'label'     => '名称',
                ],
                [
                    'value' => function ($infoInstance) {
                        /** @var ExtensionInfo $infoInstance */
                        return $infoInstance->getConfiguration()->getDescription();
                    },
                    'label' => Yii::t('ec/modules/extension', 'Description'),
                ],
                [
                    'class'     => 'kartik\grid\BooleanColumn',
                    'label'     => Yii::t('ec/modules/extension', 'Is System'),
                    'attribute' => 'isSystem',
                ],
                [
                    'class'     => 'kartik\grid\BooleanColumn',
                    'label'     => Yii::t('ec/modules/extension', 'Status'),
                    'attribute' => 'isEnable',
                ],
                [
                    'format' => 'html',
                    'label'  => Yii::t('ec/modules/extension', 'Run Mode'),
                    'value'  => function ($infoInstance) use ($runList) {
                        /** @var ExtensionInfo $infoInstance */
                        $run = !$infoInstance->getCanInstall() ? $infoInstance->getRunMode() : -1;
                        switch ($run) {
                            case ExtensionInfo::RUN_MODULE_EXTENSION:
                                return Html::tag('span', $runList[$run], ['class' => 'text-danger']);
                                break;
                            case ExtensionInfo::RUN_MODULE_DEVELOPER:
                                return Html::tag('span', $runList[$run], ['class' => 'text-warning']);
                                break;
                            default:
                                return Yii::t('ec/modules/extension', 'Not installed');
                        }
                    },
                ],
                [
                    'value' => function ($infoInstance) {
                        /** @var ExtensionInfo $infoInstance */
                        return $infoInstance->getConfiguration()->getVersion();
                    },
                    'label' => Yii::t('ec/modules/extension', 'Version'),
                ],
                [
                    'class'          => \wonail\adminlte\grid\ActionColumn::class,
                    'template'       => '{install} {uninstall} {tips}',
                    'visibleButtons' => [
                        'install'   => function ($infoInstance, $key, $index) {
                            /** @var ExtensionInfo $infoInstance */
                            return $infoInstance->getCanInstall();
                        },
                        'uninstall' => function ($infoInstance, $key, $index) {
                            /** @var ExtensionInfo $infoInstance */
                            return $infoInstance->getCanUninstall();
                        },
                        'tips'      => function ($infoInstance, $key, $index) {
                            /** @var ExtensionInfo $infoInstance */
                            return !$infoInstance->getCanInstall() && !$infoInstance->getCanUninstall();
                        },
                    ],
                    'buttons'        => [
                        'install'   => function ($url, $model, $key) use ($app) {
                            return Html::a(Yii::t('ec/modules/extension', 'Install'), ['install', 'id' => $key, 'app' => $app], ['data-pjax' => 1]);
                        },
                        'uninstall' => function ($url, $model, $key) use ($app) {
                            return Html::a(Yii::t('ec/modules/extension', 'Uninstall'), ['uninstall', 'id' => $key, 'app' => $app], [
                                'data-method'  => 'post',
                                'data-confirm' => Yii::t('ec/modules/extension', 'Are you sure you want to uninstall the selected extension?'),
                            ]);
                        },
                        'tips'      => function ($url, $model, $key) {
                            return 'N/A';
                        },
                    ],
                ],
            ];
            
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns'      => $column,
                'layout'       => "{items}\n{pager}",
            ]);
            ?>
        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>