<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 engine-core
 * @license BSD 3-Clause License
 */

namespace EngineCore\modules\extension\dispatches\Basic;

use EngineCore\dispatch\Dispatch;
use EngineCore\enums\AppEnum;
use EngineCore\extension\repository\models\BaseExtensionModel;
use EngineCore\modules\extension\controllers\ConfigController;
use EngineCore\modules\extension\controllers\ControllerController;
use EngineCore\modules\extension\controllers\ModuleController;
use EngineCore\modules\extension\controllers\ThemeController;
use yii\web\NotFoundHttpException;

/**
 * Class Uninstall
 */
class Uninstall extends Dispatch
{
    
    /**
     * @var ConfigController|ModuleController|ControllerController|ThemeController
     */
    public $controller;
    
    /**
     * @param string $id  扩展名称
     * @param string $app 应用ID
     *
     * @throws NotFoundHttpException
     */
    public function run($id, $app = AppEnum::BACKEND)
    {
        /** @var BaseExtensionModel $model */
        $model = $this->controller->repository->findOne($id, $app);
        if (null === $model) {
            throw new NotFoundHttpException("{$id}: 扩展不存在");
        }
        if ($model->delete()) {
            $this->response->success('卸载成功！', ['index', 'app' => $app]);
        } else {
            $this->response->error('卸载失败！', ['index', 'app' => $app]);
        }
    }
    
}