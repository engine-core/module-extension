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
 * Class RefreshConfig
 */
class RefreshConfig extends Dispatch
{

    /**
     * @var FunctionsController
     */
    public $controller;

    public function run()
    {
        // 清理缓存
        Ec::$service->getExtension()->getRepository()->clearCache();
        Ec::$service->getMenu()->getConfig()->clearCache();
        Ec::$service->getSystem()->getSetting()->clearCache();
        // 刷新配置
        Ec::$service->getExtension()->getEnvironment()->flushConfigFiles();

        $this->response->success(Yii::t('ec/modules/extension', 'Configuration refresh succeeded.'), Yii::$app->getRequest()->getReferrer());
    }

}