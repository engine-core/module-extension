<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 engine-core
 * @license BSD 3-Clause License
 */

use EngineCore\Ec;
use EngineCore\enums\AppEnum;
use EngineCore\enums\YesEnum;
use EngineCore\extension\repository\info\ExtensionInfo;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use EngineCore\extension\repository\models\Config;
use EngineCore\extension\repository\models\Controller;
use EngineCore\extension\repository\models\Module;
use EngineCore\extension\repository\models\Theme;

/* @var $this yii\web\View */
/* @var $model Config|Module|Theme|Controller */
/* @var $form ActiveForm */
/* @var $runList array */
/* @var $dependList array */
/* @var $composerList array */
?>

<?php $form = ActiveForm::begin([
    'type'       => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<div class="jumbotron text-center">
    <h1><?= $model->getInfoInstance()->getName() ?></h1>
    <p class="lead">
        <?= $model->getInfoInstance()->getConfiguration()->getDescription() ?>
        <small class="text-danger">
            <?php
            if ($model->getInfoInstance()->getIsSystem()) {
                Yii::t('ec/extension', 'Is System');
            }
            ?>
        </small>
    </p>
</div>

<?php
echo $form->field($model, 'unique_name')->staticInput();

echo $form->field($model, 'version')->staticInput();

switch ($model->getInfoInstance()->getType()) {
    case ExtensionInfo::TYPE_CONTROLLER:
        echo $form->field($model, 'module_id')->textInput();
        echo $form->field($model, 'controller_id')->textInput();
        break;
    case ExtensionInfo::TYPE_MODULE:
        echo $form->field($model, 'module_id')->textInput();
        echo $form->field($model, 'bootstrap')->radioList(YesEnum::list(), ['inline' => true]);
        break;
    case ExtensionInfo::TYPE_THEME:
//        echo $form->field($model, 'theme_id')->textInput();
        break;
}

// 系统模块
echo $form->field($model, 'is_system')->radioList(YesEnum::list(), ['inline' => true]);

// 运行模块列表
echo $form->field($model, 'run')->radioList($runList, ['inline' => true]);

switch ($model->getInfoInstance()->getType()) {
    case ExtensionInfo::TYPE_MODULE:
    case ExtensionInfo::TYPE_CONTROLLER:
    case ExtensionInfo::TYPE_CONFIG:
        // 扩展状态
        echo $form->field($model, 'status')->radioList(YesEnum::list(), ['inline' => true]);
        break;
}

$btn[] = Html::submitButton(Yii::t('ec/app',
    ($this->context->action->id == 'install' && $model->getInfoInstance()->getCanInstall())
        ? 'Install'
        : 'Save'
), ['class' => 'btn btn-success']);
$btn[] = Html::resetButton(Yii::t('ec/app', 'Reset'), ['class' => 'btn btn-default']);
echo Html::tag('div', implode("\n", $btn), [
    'class' => 'text-center',
]);
?>
<hr>
<?php
$footer = '<blockquote class="help-block">';
if (Yii::$app->controller->action->id == 'install' && $model->getInfoInstance()->getCanInstall()) {
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
    'allModels'  => $dependList,
    'pagination' => [
        'pageSize' => -1, //不使用分页
    ],
]);
$extensionDepends = GridView::widget([
    'dataProvider'     => $dataProvider,
    'layout'           => "{items}",
    'bordered'         => false,
    'hover'            => true,
    'emptyText'        => Yii::t('ec/modules/extension', 'There is no dependency'),
    'emptyTextOptions' => ['class' => 'text-center text-muted'],
    'columns'          => [
        [
            'label'     => Yii::t('ec/modules/extension', 'Extension Name'),
            'attribute' => 'name',
        ],
        [
            'label'     => Yii::t('ec/modules/extension', 'Description'),
            'attribute' => 'description',
        ],
        [
            'label'  => '当前版本',
            'format' => 'raw',
            'value'  => function ($model) {
                if (
                    ($model['installed'] || $model['downloaded'])
                    && !Ec::$service->getSystem()->getVersion()->compare($model['localVersion'], $model['requireVersion'])
                ) {
                    return Html::tag('div', Html::tag('span',
                        $model['localVersion'] . ' 版本冲突',
                        ['class' => 'text-danger']), [
                        'data-toggle' => 'tooltip',
                        'title'       => nl2br('当前版本' . $model['localVersion']
                                . '不符合所需的依赖版本要求 ' . $model['requireVersion'])
                            . '。在解决冲突前，扩展功能可能存在不兼容或无法使用的情况。',
                        'data-html'   => 'true',
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
            'class'     => 'kartik\grid\BooleanColumn',
            'attribute' => 'downloaded',
            'label'     => '已下载',
        ],
        [
            'class'     => 'kartik\grid\BooleanColumn',
            'attribute' => 'installed',
            'label'     => '已安装',
        ],
    ],
]);

$dataProvider = new ArrayDataProvider([
    'allModels'  => $composerList,
    'pagination' => [
        'pageSize' => -1, //不使用分页
    ],
]);
$composerDepends = GridView::widget([
    'dataProvider'     => $dataProvider,
    'layout'           => "{items}",
    'bordered'         => false,
    'hover'            => true,
    'emptyText'        => Yii::t('ec/modules/extension', 'There is no dependency'),
    'emptyTextOptions' => ['class' => 'text-center text-muted'],
    'columns'          => [
        [
            'label' => Yii::t('ec/modules/extension', 'Extension Name'),
            'value' => 'name',
        ],
        [
            'label'     => Yii::t('ec/modules/extension', 'Description'),
            'attribute' => 'description',
        ],
        [
            'label'  => '当前版本',
            'format' => 'raw',
            'value'  => function ($model) {
                if (
                    $model['installed']
                    && !Ec::$service->getSystem()->getVersion()->compare($model['localVersion'], $model['requireVersion'])
                ) {
                    return Html::tag('div', Html::tag('span',
                        $model['localVersion'] . ' 版本冲突',
                        ['class' => 'text-danger']), [
                        'data-toggle' => 'tooltip',
                        'title'       => nl2br('当前版本' . $model['localVersion']
                                . '不符合所需的依赖版本要求 ' . $model['requireVersion'])
                            . '。在解决冲突前，扩展功能可能存在不兼容或无法使用的情况。',
                        'data-html'   => 'true',
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
            'class'     => 'kartik\grid\BooleanColumn',
            'attribute' => 'installed',
            'label'     => '已安装',
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
            <?= VarDumper::dumpAsString(Ec::$service->getExtension()->getEnvironment()->getConfig($model->getInfoInstance())[$model->getInfoInstance()->getApp()] ?? [], 10, true); ?>
        </div>
        <div class="panel-footer text-muted">该配置数据将自动保存在
            `<?= '@' . $model->getInfoInstance()->getApp() . '/config/extension.php'; ?>` 文件里。
        </div>
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
            <?= VarDumper::dumpAsString(Ec::$service->getExtension()->getEnvironment()->getConfig($model->getInfoInstance())[AppEnum::COMMON] ?? [], 10, true); ?>
        </div>
        <div class="panel-footer text-muted">该配置数据将自动保存在 `<?= '@common/config/extension.php'; ?>` 文件里。</div>
    </div>
</div>

<?php ActiveForm::end(); ?>
