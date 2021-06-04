<?php
/**
 * @link https://github.com/EngineCore/module-extension
 * @copyright Copyright (c) 2020 E-Kevin
 * @license BSD 3-Clause License
 */

use wocenter\Wc;

/* @var $this yii\web\View */
/* @var $model \extensions\wocenter\backend\modules\extension\models\Controller */
/* @var $id string */
/* @var $dependList array */
/* @var $runList array */
/* @var $composerList array */

$this->title = '安装 ' . $id . ' 控制器扩展';
$this->params['breadcrumbs'][] = ['label' => '控制器扩展', 'url' => ['/extension/controller/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = Wc::$service->getExtension()->getUrlManager()->getUrl('controller/index');
?>
<?=

$this->render('_form', [
    'model' => $model,
    'dependList' => $dependList,
    'runList' => $runList,
    'composerList' => $composerList,
])
?>