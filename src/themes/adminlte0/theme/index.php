<?php
/**
 * @link https://github.com/EngineCore/module-extension
 * @copyright Copyright (c) 2020 E-Kevin
 * @license BSD 3-Clause License
 */

use EngineCore\enums\AppEnum;
use kartik\grid\GridView;
use wocenter\enums\EnableEnum;
use wocenter\interfaces\ExtensionInfoInterface;
use wocenter\Wc;
use yii\bootstrap\Tabs;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider ArrayDataProvider */
/* @var $runList array */
/* @var $app string */

$this->title = Wc::$service->getMenu()->getPage()->setLevel(3)->getTitle('主题管理');
$this->params['breadcrumbs'][] = $this->title;

$column = [
    [
        'label' => '扩展名',
        'format' => 'raw',
        'value' => function ($model, $key) use ($app) {
            return $model['infoInstance']->canInstall
                ? $key
                : Html::a($key, ['update', 'id' => $key, 'app' => $app], ['data-pjax' => 1]);
        },
    ],
    [
        'attribute' => 'name',
        'format' => 'html',
        'value' => function ($model) {
            return $model['infoInstance']->name;
        },
        'label' => '名称',
    ],
    [
        'attribute' => 'description',
        'value' => function ($model) {
            return $model['infoInstance']->description;
        },
        'label' => '描述',
    ],
    [
        'class' => 'kartik\grid\BooleanColumn',
        'value' => function ($model) {
            $is_system = $model['infoInstance']->isSystem;
            if (!$is_system) {
                if (!$model['infoInstance']->canInstall) {
                    $is_system = $model['data']['is_system'];
                }
            }
            
            return $is_system;
        },
        'label' => '核心扩展',
    ],
    [
        'format' => 'html',
        'label' => '运行模式',
        'value' => function ($model) use ($runList) {
            $run = !$model['infoInstance']->canInstall ? $model['data']['run'] : -1;
            switch ($run) {
                case ExtensionInfoInterface::RUN_MODULE_EXTENSION:
                    return Html::tag('span', $runList[$run], ['class' => 'text-danger']);
                    break;
                case ExtensionInfoInterface::RUN_MODULE_DEVELOPER:
                    return Html::tag('span', $runList[$run], ['class' => 'text-warning']);
                    break;
                default:
                    return '未安装';
            }
        },
    ],
    [
        'attribute' => 'author',
        'value' => function ($model) {
            return $model['infoInstance']->developer;
        },
        'label' => '开发者',
    ],
    [
        'attribute' => 'version',
        'value' => function ($model) {
            return $model['infoInstance']->version;
        },
        'label' => '版本',
    ],
    [
        'class' => \kartik\grid\ActionColumn::class,
        'template' => '{install} {uninstall} {enable} {tips}',
        'visibleButtons' => [
            'install' => function ($model, $key, $index) {
                return $model['infoInstance']->canInstall;
            },
            'uninstall' => function ($model, $key, $index) {
                return $model['infoInstance']->canUninstall;
            },
            'enable' => function ($model, $key, $index) {
                $status = !$model['infoInstance']->canInstall ? $model['data']['status'] : EnableEnum::DISABLE;
                
                return !$model['infoInstance']->canInstall && ($status == EnableEnum::DISABLE);
            },
            'tips' => function ($model, $key, $index) {
                $status = !$model['infoInstance']->canInstall ? $model['data']['status'] : EnableEnum::DISABLE;
                
                return $status == EnableEnum::ENABLE;
            },
        ],
        'buttons' => [
            'install' => function ($url, $model, $key) use ($app) {
                return Html::a('安装', ['install', 'id' => $key, 'app' => $app], ['data-pjax' => 1]);
            },
            'uninstall' => function ($url, $model, $key) use ($app) {
                return Html::a('卸载', ['uninstall', 'id' => $key, 'app' => $app], [
                    'data-method' => 'post',
                    'data-confirm' => '确定要卸载所选主题吗？',
                ]);
            },
            'enable' => function ($url, $model, $key) use ($app) {
                return Html::a('应用', ['enable', 'id' => $key, 'app' => $app], [
                    'data-method' => 'post',
                    'data-confirm' => '确定使用当前主题吗？',
                ]);
            },
            'tips' => function ($url, $model, $key) {
                return '当前主题';
            },
        ],
    ],
];
$content = GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $column,
    'bordered' => false,
    'hover' => true,
    'emptyTextOptions' => ['class' => 'text-center text-muted'],
]);

foreach (AppEnum::list() as $k => $name) {
    $items[] = [
        'label' => $name,
        'url' => ['index', 'app' => $k],
        'active' => $k == $app,
        'content' => $k == $app ? $content : '',
        'linkOptions' => [
            'data-pjax' => 1,
        ],
    ];
}

$items[] = [
    'label' => '清理缓存',
    'url' => ['functions/clear-cache'],
    'headerOptions' => [
        'class' => 'pull-right',
    ],
    'linkOptions' => [
        'data-method' => 'post',
        'data-pjax' => 1,
    ],
];

echo Tabs::widget([
    'items' => $items,
]);
