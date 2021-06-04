<?php
/**
 * @link https://github.com/EngineCore/module-extension
 * @copyright Copyright (c) 2020 E-Kevin
 * @license BSD 3-Clause License
 */

use wocenter\Wc;

/* @var $this yii\web\View */
/* @var $model \extensions\wocenter\backend\modules\extension\models\Module */
/* @var $runList array */
/* @var $id string */
/* @var $dependList array */
/* @var $composerList array */

$this->title = '安装 ' . $id . ' 模块';
$this->params['breadcrumbs'][] = ['label' => '模块管理', 'url' => ['/extension/module/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = Wc::$service->getExtension()->getUrlManager()->getUrl('module/index');
?>
<?=

$this->render('_form', [
    'model' => $model,
    'runList' => $runList,
    'dependList' => $dependList,
    'composerList' => $composerList,
])
?>