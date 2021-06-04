<?php
/**
 * @link https://github.com/EngineCore/module-extension
 * @copyright Copyright (c) 2020 E-Kevin
 * @license BSD 3-Clause License
 */

use wocenter\interfaces\ExtensionInfoInterface;
use wocenter\Wc;
use wonail\adminlte\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider ArrayDataProvider */
/* @var $app string */
/* @var $runList array */

$headerToolbar = '';
$this->title = '控制器扩展';
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = Wc::$service->getExtension()->getUrlManager()->getUrl('controller/index');
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
                'class' => 'text-yellow',
                'data-method' => 'post',
            ]) ?>
        </li>
    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="avatar">
            <?php
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
                        if ($model['infoInstance']->isSystem) {
                            return '<span class="text-danger">' . $model['infoInstance']->name . '</span>';
                        } else {
                            if ($model['infoInstance']->canInstall) {
                                return '<span class="text-success">' . $model['infoInstance']->name . '</span>';
                            } else {
                                return '<span class="text-warning">' . $model['infoInstance']->name . '</span>';
                            }
                        }
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
                    'label' => '访问路由',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $route = $model['infoInstance']->moduleId
                            ? '/' . $model['infoInstance']->moduleId . '/' . $model['infoInstance']->id
                            : '/' . $model['infoInstance']->id;
                        
                        return $model['infoInstance']->canUninstall
                            ? Html::a($route, $route)
                            : $route;
                    },
                ],
                [
                    'class' => 'kartik\grid\BooleanColumn',
                    'value' => function ($model) {
                        return $model['infoInstance']->isSystem;
                    },
                    'label' => '核心扩展',
                ],
                [
                    'format' => 'html',
                    'label' => '运行模式',
                    'value' => function ($model) use ($runList) {
                        switch ($model['run']) {
                            case ExtensionInfoInterface::RUN_MODULE_EXTENSION:
                                return '<span class="text-danger">' . $runList[$model['run']] . '</span>';
                                break;
                            case ExtensionInfoInterface::RUN_MODULE_DEVELOPER:
                                return '<span class="text-warning">' . $runList[$model['run']] . '</span>';
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
                    'class' => \wonail\adminlte\grid\ActionColumn::class,
                    'template' => '{install} {uninstall} {tips}',
                    'visibleButtons' => [
                        'install' => function ($model, $key, $index) {
                            return $model['infoInstance']->canInstall;
                        },
                        'uninstall' => function ($model, $key, $index) {
                            return $model['infoInstance']->canUninstall;
                        },
                        'tips' => function ($model, $key, $index) {
                            return !$model['infoInstance']->canInstall && !$model['infoInstance']->canUninstall;
                        },
                    ],
                    'buttons' => [
                        'install' => function ($url, $model, $key) use ($app) {
                            return Html::a('安装', ['install', 'id' => $key, 'app' => $app], ['data-pjax' => 1]);
                        },
                        'uninstall' => function ($url, $model, $key) use ($app) {
                            return Html::a('卸载', ['uninstall', 'id' => $key, 'app' => $app], [
                                'data-method' => 'post',
                                'data-confirm' => '确定要卸载所选模块吗？',
                            ]);
                        },
                        'tips' => function ($url, $model, $key) {
                            return 'N/A';
                        },
                    ],
                ],
            ];
            
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => $column,
                'layout' => "{items}\n{pager}",
            ]);
            ?>
        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>