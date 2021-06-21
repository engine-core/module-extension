<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 engine-core
 * @license BSD 3-Clause License
 */

use EngineCore\Ec;
use EngineCore\extension\repository\models\Config;
use EngineCore\extension\repository\models\Controller;
use EngineCore\extension\repository\models\Module;
use EngineCore\extension\repository\models\Theme;

/* @var $this yii\web\View */
/* @var $model Config|Module|Theme|Controller */
/* @var $runList array */
/* @var $id string */
/* @var $dependList array */
/* @var $composerList array */
/* @var $app string */

$this->title = Yii::t('ec/modules/extension', 'Manage Extension', ['extension' => $id]);
$this->params['breadcrumbs'] = Ec::$service->getMenu()->getPage()->setConditions([
    'level' => 4,
])->setBreadcrumbsUrlParams([
    'app' => $app,
])->getBreadcrumbs();
?>

<div class="update">
    
    <?= $this->render('_form', [
        'model'        => $model,
        'runList'      => $runList,
        'dependList'   => $dependList,
        'composerList' => $composerList,
    ]); ?>

</div>
