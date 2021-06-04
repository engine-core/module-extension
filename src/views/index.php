<?php
/**
 * @link      https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 E-Kevin
 * @license   BSD 3-Clause License
 */

use EngineCore\Ec;
use EngineCore\enums\AppEnum;
use EngineCore\extension\repository\info\ExtensionInfo;
use kartik\grid\GridView;
use yii\bootstrap\Tabs;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider ArrayDataProvider */
/* @var $runList array */
/* @var $app string */

$this->params['breadcrumbs'] = Ec::$service->getMenu()->getPage()->setLevel(3)->getBreadcrumbs();
$this->title = Ec::$service->getMenu()->getPage()->getTitle();

$column = [
    [
        'label'  => Yii::t('ec/modules/extension', 'Extension Name'),
        'format' => 'raw',
        'value'  => function ($infoInstance, $key) use ($app) {
            /** @var ExtensionInfo $infoInstance */
            return $infoInstance->getCanInstall()
                ? $key
                : Html::a($key, ['update', 'id' => $key, 'app' => $app]);
        },
    ],
    [
        'attribute' => 'name',
        'label'     => Yii::t('ec/modules/extension', 'Name'),
    ],
    [
        'value'     => function ($infoInstance) {
            /** @var ExtensionInfo $infoInstance */
            return $infoInstance->getConfiguration()->getDescription();
        },
        'label'     => Yii::t('ec/modules/extension', 'Description'),
    ],
    [
        'value'     => function ($infoInstance) {
            /** @var ExtensionInfo $infoInstance */
            return $infoInstance->getConfiguration()->getVersion();
        },
        'label'     => Yii::t('ec/modules/extension', 'Version'),
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
        'class'          => \kartik\grid\ActionColumn::class,
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
                return Html::a(Yii::t('ec/modules/extension', 'Install'), ['install', 'id' => $key, 'app' => $app]);
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
$content = GridView::widget([
    'dataProvider'     => $dataProvider,
    'columns'          => $column,
    'bordered'         => false,
    'hover'            => true,
    'emptyTextOptions' => ['class' => 'text-center text-muted'],
]);

foreach (AppEnum::list() as $k => $name) {
    $items[] = [
        'label'       => $name,
        'url'         => ['index', 'app' => $k],
        'active'      => $k == $app,
        'content'     => $k == $app ? $content : '',
        'linkOptions' => [
            'data-pjax' => 1,
        ],
    ];
}

$items[] = [
    'label'         => Yii::t('ec/modules/extension', 'Clear Cache'),
    'url'           => ['functions/clear-cache'],
    'headerOptions' => [
        'class' => 'pull-right',
    ],
    'linkOptions'   => [
        'data-method' => 'post',
        'data-pjax'   => 1,
    ],
];
?>

<div class="index">
    
    <?= Tabs::widget([
        'items' => $items,
    ]); ?>

</div>
