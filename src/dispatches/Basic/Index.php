<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 engine-core
 * @license BSD 3-Clause License
 */

namespace EngineCore\modules\extension\dispatches\Basic;

use EngineCore\enums\AppEnum;
use EngineCore\dispatch\Dispatch;
use EngineCore\Ec;
use EngineCore\modules\extension\controllers\ConfigController;
use EngineCore\modules\extension\controllers\ControllerController;
use EngineCore\modules\extension\controllers\ModuleController;
use EngineCore\modules\extension\controllers\ThemeController;
use yii\data\ArrayDataProvider;

/**
 * Class Index
 */
class Index extends Dispatch
{

    /**
     * @var ConfigController|ModuleController|ControllerController|ThemeController
     */
    public $controller;

    /**
     * @param string $app 应用ID
     *
     * @return string
     */
    public function run($app = AppEnum::BACKEND)
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => $this->controller->repository->getConfigurationByApp(false, $app),
            'pagination' => [
                'pageSize' => -1, //不使用分页
            ],
        ]);

        return $this->response->render(null, [
            'dataProvider' => $dataProvider,
            'app' => $app,
            'runList' => Ec::$service->getExtension()->getRunModeList(),
        ]);
    }

}
