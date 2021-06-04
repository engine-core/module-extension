<?php
/**
 * @link https://github.com/EngineCore/module-extension
 * @copyright Copyright (c) 2020 E-Kevin
 * @license BSD 3-Clause License
 */

use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use wocenter\Wc;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \extensions\wocenter\backend\modules\extension\models\Controller */
/* @var $form ActiveForm */
/* @var $dependList array */
/* @var $runList array */
/* @var $composerList array */

$footer = '<blockquote class="help-block">';
if (Yii::$app->controller->action->id == 'install' && $model->getInfoInstance()->canInstall) {
    $footer .= '<p>安装后，系统将自动加载所需菜单、路由规则、权限配置等信息</p>';
} else {
    $footer .= '<p>更改设置后，系统将自动同步更新所需菜单、路由规则、权限配置等信息</p>';
}
$footer .= '</blockquote>';
?>

<?php $form = ActiveForm::begin(); ?>

    <div class="jumbotron text-center">
        <h1><?= $model->getInfoInstance()->name ?>
            <small class="text-danger">
                <?php if ($model->is_system) : ?>核心扩展<?php endif; ?>
            </small>
        </h1>
        <p class="lead"><?= $model->getInfoInstance()->description ?></p>
    </div>

<?php
echo $form->field($model, 'id')->textInput(['disabled' => true]);

echo $form->field($model, 'module_id')->textInput();

echo $form->field($model, 'controller_id')->textInput();

// 系统模块
if (!$model->getInfoInstance()->isSystem) {
    echo $form->field($model, 'is_system')->radioList(['否', '是']);
}

// 运行模式列表
echo $form->field($model, 'run')->radioList($runList);

$btn[] = Html::submitButton(Yii::t('wocenter/app',
    ($this->context->action->id == 'install' && $model->getInfoInstance()->canInstall)
        ? 'Install'
        : 'Save'
), ['class' => 'btn btn-success width-200']);
$btn[] = Html::resetButton(Yii::t('wocenter/app', 'Reset'), ['class' => 'btn btn-default']);
$btn[] = Html::button(Yii::t('wocenter/app', 'Go back'), ['class' => 'btn btn-default', 'data-widget' => 'goback',]);
echo Html::tag('div', implode("\n", $btn), [
    'class' => 'text-center',
]);
?>
    <hr>

    <div class="row-fluid">
        <div class="col-lg-10">
            <?php
            $dataProvider = new ArrayDataProvider([
                'allModels' => $dependList,
                'pagination' => [
                    'pageSize' => -1, //不使用分页
                ],
            ]);
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{items}",
                'bordered' => false,
                'hover' => true,
                'emptyText' => '不存在任何依赖关系',
                'emptyTextOptions' => ['class' => 'text-center text-muted'],
                'columns' => [
                    [
                        'label' => '扩展名称',
                        'value' => function ($model, $key) {
                            return $key;
                        },
                    ],
                    [
                        'label' => '名称',
                        'attribute' => 'name',
                    ],
                    [
                        'label' => '描述',
                        'attribute' => 'description',
                    ],
                    [
                        'label' => '当前版本',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if (
                                ($model['installed'] || $model['downloaded'])
                                && !Wc::$service->getExtension()->getVersion()->validateVersion($model['currentVersion'], $model['needVersion'])
                            ) {
                                return Html::tag('div', Html::tag('span',
                                    $model['currentVersion'] . ' 版本冲突',
                                    ['class' => 'text-red']), [
                                    'data-toggle' => 'tooltip',
                                    'title' => nl2br('当前版本' . $model['currentVersion']
                                            . '不符合所需的依赖版本要求 ' . $model['needVersion'])
                                        . '。在解决冲突前，扩展功能可能存在不兼容或无法使用的情况。',
                                    'data-html' => 'true',
                                ]);
                            }
            
                            return $model['currentVersion'];
                        },
                    ],
                    [
                        'label' => '依赖版本',
                        'value' => function ($model) {
                            return $model['needVersion'];
                        },
                    ],
                    [
                        'class' => 'kartik\grid\BooleanColumn',
                        'attribute' => 'downloaded',
                        'label' => '已下载',
                    ],
                    [
                        'class' => 'kartik\grid\BooleanColumn',
                        'attribute' => 'installed',
                        'label' => '已安装',
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="col-lg-2 text-muted">
            <h4>扩展依赖</h4>
            <p>
                使用该扩展前必须首先解决扩展依赖，只有满足依赖关系才能确保正常使用该扩展功能。
            </p>
        </div>
    </div>

    <hr>

    <div class="row-fluid">
        <div class="col-lg-10">
            <?php
            $dataProvider = new ArrayDataProvider([
                'allModels' => $composerList,
                'pagination' => [
                    'pageSize' => -1, //不使用分页
                ],
            ]);
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{items}",
                'bordered' => false,
                'hover' => true,
                'emptyText' => '不存在任何依赖关系',
                'emptyTextOptions' => ['class' => 'text-center text-muted'],
                'columns' => [
                    [
                        'label' => '扩展名称',
                        'value' => function ($model, $key) {
                            return $key;
                        },
                    ],
                    [
                        'label' => '当前版本',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if (
                                $model['installed']
                                && !Wc::$service->getExtension()->getVersion()->validateVersion($model['currentVersion'], $model['needVersion'])
                            ) {
                                return Html::tag('div', Html::tag('span',
                                    $model['currentVersion'] . ' 版本冲突',
                                    ['class' => 'text-red']), [
                                    'data-toggle' => 'tooltip',
                                    'title' => nl2br('当前版本' . $model['currentVersion']
                                            . '不符合所需的依赖版本要求 ' . $model['needVersion'])
                                        . '。在解决冲突前，扩展功能可能存在不兼容或无法使用的情况。',
                                    'data-html' => 'true',
                                ]);
                            }
            
                            return $model['currentVersion'];
                        },
                    ],
                    [
                        'label' => '依赖版本',
                        'value' => function ($model) {
                            return $model['needVersion'];
                        },
                    ],
                    [
                        'class' => 'kartik\grid\BooleanColumn',
                        'attribute' => 'installed',
                        'label' => '已安装',
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="col-lg-2 text-muted">
            <h4>Composer依赖</h4>
            <p>
                使用该扩展前必须首先解决composer依赖，只有满足依赖关系才能确保正常使用该扩展功能。
            </p>
        </div>
    </div>

<?php ActiveForm::end(); ?>