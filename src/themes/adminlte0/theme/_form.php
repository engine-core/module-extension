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
/* @var $model \extensions\wocenter\backend\modules\extension\models\Theme */
/* @var $form ActiveForm */
/* @var $dependList array */
/* @var $runList array */
/* @var $composerList array */
?>

<?php $form = ActiveForm::begin([
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

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

// 系统模块
if (!$model->getInfoInstance()->isSystem) {
    echo $form->field($model, 'is_system')->radioList(\wocenter\enums\YesEnum::list());
}

// 运行模式列表
echo $form->field($model, 'run')->radioList($runList);

$btn[] = Html::submitButton(Yii::t('wocenter/app',
    ($this->context->action->id == 'install' && $model->getInfoInstance()->canInstall)
        ? 'Install'
        : 'Save'
), ['class' => 'btn btn-success']);
$btn[] = Html::resetButton(Yii::t('wocenter/app', 'Reset'), ['class' => 'btn btn-default']);
echo Html::tag('div', implode("\n", $btn), [
    'class' => 'text-center',
]);
?>
    <hr>
<?php
$footer = '<blockquote class="help-block">';
if (Yii::$app->controller->action->id == 'install' && $model->getInfoInstance()->canInstall) {
    $footer .= '<p>安装后，系统将自动加载所需菜单、路由规则、权限配置等信息</p>';
} else {
    $footer .= '<p>更改设置后，系统将自动同步更新所需菜单、路由规则、权限配置等信息</p>';
}
$footer .= '</blockquote>';
echo $footer;
?>
    <hr>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $dependList,
    'pagination' => [
        'pageSize' => -1, //不使用分页
    ],
]);
$extensionDepends = GridView::widget([
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
                    && !Wc::$service->getExtension()->getVersion()->validateVersion($model['localVersion'], $model['requireVersion'])
                ) {
                    return Html::tag('div', Html::tag('span',
                        $model['localVersion'] . ' 版本冲突',
                        ['class' => 'text-danger']), [
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
$dataProvider = new ArrayDataProvider([
    'allModels' => $composerList,
    'pagination' => [
        'pageSize' => -1, //不使用分页
    ],
]);
$composerDepends = GridView::widget([
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
                    && !Wc::$service->getExtension()->getVersion()->validateVersion($model['localVersion'], $model['requireVersion'])
                ) {
                    return Html::tag('div', Html::tag('span',
                        $model['localVersion'] . ' 版本冲突',
                        ['class' => 'text-danger']), [
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

    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#extensionDepends" aria-expanded="false"
             aria-controls="extensionDepends">
            <h3 class="panel-title">
                扩展依赖
            </h3>
        </div>
        <div class="collapse" id="extensionDepends">
            <div class="panel-body">
                <?= $extensionDepends ?>
            </div>
            <div class="panel-footer text-muted">使用该扩展前必须首先解决扩展依赖，只有满足依赖关系才能确保正常使用该扩展功能。</div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#composerDepends" aria-expanded="false"
             aria-controls="composerDepends">
            <h3 class="panel-title">
                Composer依赖
            </h3>
        </div>
        <div class="collapse" id="composerDepends">
            <div class="panel-body">
                <?= $composerDepends ?>
            </div>
            <div class="panel-footer text-muted">使用该扩展前必须首先解决composer依赖，只有满足依赖关系才能确保正常使用该扩展功能。</div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#extensionConfig" aria-expanded="false"
             aria-controls="extensionConfig">
            <h3 class="panel-title">
                扩展配置
            </h3>
        </div>
        <div class="collapse" id="extensionConfig">
            <div class="panel-body">
                <?= \yii\helpers\VarDumper::dumpAsString($model->getInfoInstance()->getConfig(),10, true); ?>
            </div>
            <div class="panel-footer text-muted">该配置数据仅在【应用|安装】当前主题时自动保存在 `<?= '@'.$model->getInfoInstance()->app.'/config/extension.php'; ?>` 文件里。</div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#extensionCommonConfig" aria-expanded="false"
             aria-controls="extensionCommonConfig">
            <h3 class="panel-title">
                扩展公共配置
            </h3>
        </div>
        <div class="collapse" id="extensionCommonConfig">
            <div class="panel-body">
                <?= \yii\helpers\VarDumper::dumpAsString($model->getInfoInstance()->getCommonConfig(),10, true); ?>
            </div>
            <div class="panel-footer text-muted">该配置数据仅在【应用|安装】当前主题时自动保存在 `<?= '@common/config/extension.php'; ?>` 文件里。</div>
        </div>
    </div>

<?php ActiveForm::end(); ?>