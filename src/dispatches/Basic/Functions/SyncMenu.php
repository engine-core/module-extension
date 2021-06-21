<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 engine-core
 * @license BSD 3-Clause License
 */

namespace EngineCore\modules\extension\dispatches\Basic\Functions;

use EngineCore\dispatch\Dispatch;
use EngineCore\Ec;
use EngineCore\modules\extension\controllers\FunctionsController;
use Yii;

/**
 * Class SyncMenu
 */
class SyncMenu extends Dispatch
{

    /**
     * @var FunctionsController
     */
    public $controller;

    public function run()
    {
        if (Ec::$service->getMenu()->getConfig()->sync()) {
            $this->response->success(Yii::t('ec/modules/extension', 'Synchronization succeeded.'), Yii::$app->getRequest()->getReferrer());
        } else {
            $this->response->error(Yii::t('ec/modules/extension', 'Synchronization failed.'), Yii::$app->getRequest()->getReferrer());
        }
    }

}