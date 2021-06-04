<?php

use EngineCore\Ec;
use EngineCore\extension\repository\models\Config;
use EngineCore\extension\repository\models\Controller;
use EngineCore\extension\repository\models\Module;
use EngineCore\extension\repository\models\Theme;
use kartik\form\ActiveForm;
use wonail\adminlte\grid\GridView;
//use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Box;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Config|Module|Theme|Controller */
/* @var $form ActiveForm */
/* @var $runList array */
/* @var $dependList array */
/* @var $composerList array */

$footer = '<blockquote class="help-block">';
if (Yii::$app->controller->action->id == 'install' && $model->infoInstance->canInstall) {
    $footer .= '<p>安装后，系统将自动加载所需菜单、路由规则、权限配置等信息</p>';
} else {
    $footer .= '<p>更改设置后，系统将自动同步更新所需菜单、路由规则、权限配置等信息</p>';
}
$footer .= '</blockquote>';
?>

<?php
Box::begin();
$form = ActiveForm::begin();
?>

<div class="jumbotron text-center">
    <h1><?= $model->infoInstance->name ?>
        <small class="text-danger">
            <?php if ($model->is_system) : ?>核心扩展<?php endif; ?>
        </small>
    </h1>
    <p class="lead"><?= $model->infoInstance->getConfiguration()->getDescription() ?></p>
</div>

<?php
echo $form->field($model, 'module_id')->textInput();

// 系统模块
if (!$model->infoInstance->isSystem) {
    echo $form->field($model, 'is_system')->radioList(['否', '是']);
}
// 运行模块列表
echo $form->field($model, 'run')->radioList($runList);
//echo $form->field($model, 'status')->radioList(\wocenter\libs\Constants::getStatusList());

$btn[] = Html::submitButton(Yii::t('wocenter/app',
    ($this->context->action->id == 'install' && $model->infoInstance->canInstall)
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
                            && !Ec::$service->getSystem()->getVersion()->compare($model['localVersion'], $model['requireVersion'])
                        ) {
                            return Html::tag('div', Html::tag('span',
                                $model['localVersion'] . ' 版本冲突',
                                ['class' => 'text-red']), [
                                'data-toggle' => 'tooltip',
                                'title' => nl2br('当前版本' . $model['localVersion']
                                        . '不符合所需的依赖版本要求 ' . $model['requireVersion'])
                                    . '。在解决冲突前，扩展功能可能存在不兼容或无法使用的情况。',
                                'data-html' => 'true',
                            ]);
                        }
                        
                        return $model['localVersion'];
                    },
                ],
                [
                    'label' => '依赖版本',
                    'value' => function ($model) {
                        return $model['requireVersion'];
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
                            && !Ec::$service->getSystem()->getVersion()->compare($model['localVersion'], $model['requireVersion'])
                        ) {
                            return Html::tag('div', Html::tag('span',
                                $model['localVersion'] . ' 版本冲突',
                                ['class' => 'text-red']), [
                                'data-toggle' => 'tooltip',
                                'title' => nl2br('当前版本' . $model['localVersion']
                                        . '不符合所需的依赖版本要求 ' . $model['requireVersion'])
                                    . '。在解决冲突前，扩展功能可能存在不兼容或无法使用的情况。',
                                'data-html' => 'true',
                            ]);
                        }
                        
                        return $model['localVersion'];
                    },
                ],
                [
                    'label' => '依赖版本',
                    'value' => function ($model) {
                        return $model['requireVersion'];
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

<?php
ActiveForm::end();
Box::end();
?>
