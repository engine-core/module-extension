<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 E-Kevin
 * @license BSD 3-Clause License
 */

namespace EngineCore\modules\extension\dispatches\Basic\Functions;

use EngineCore\dispatch\Dispatch;
use EngineCore\Ec;
use EngineCore\modules\extension\controllers\FunctionsController;
use Yii;

/**
 * Class ClearCache
 */
class ClearCache extends Dispatch
{
    
    /**
     * @var FunctionsController
     */
    public $controller;
    
    public function run()
    {
        Ec::$service->getExtension()->clearCache();
        
        $this->response->success(Yii::t('ec/modules/extension', 'Clean up succeeded.'), Yii::$app->getRequest()->getReferrer());
    }
    
}