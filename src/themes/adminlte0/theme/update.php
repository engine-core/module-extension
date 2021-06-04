<?php
/**
 * @link https://github.com/EngineCore/module-extension
 * @copyright Copyright (c) 2020 E-Kevin
 * @license BSD 3-Clause License
 */

use wocenter\Wc;

/* @var $this yii\web\View */
/* @var $model \extensions\wocenter\backend\modules\extension\models\Theme */
/* @var $id string */
/* @var $dependList array */
/* @var $runList array */
/* @var $composerList array */
/* @var $app string */

$this->title = '管理 ' . $id . ' 主题扩展';
$page = Wc::$service->getMenu()->getPage();
if ($breadcrumbs = $page->setUrl('/extension/theme/index')
    ->setLevel(3)->setBreadcrumbsUrlParams([
        'app' => $app
    ])
    ->generateBreadcrumbs()
) {
    $this->params['breadcrumbs'][] = $breadcrumbs;
}
$this->params['breadcrumbs'][] = $this->title;
?>
<?=

$this->render('_form', [
    'model' => $model,
    'dependList' => $dependList,
    'runList' => $runList,
    'composerList' => $composerList,
])
?>