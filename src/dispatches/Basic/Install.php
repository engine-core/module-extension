<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 engine-core
 * @license BSD 3-Clause License
 */

namespace EngineCore\modules\extension\dispatches\Basic;

use EngineCore\dispatch\Dispatch;
use EngineCore\Ec;
use EngineCore\enums\AppEnum;
use EngineCore\extension\repository\models\Config;
use EngineCore\extension\repository\models\Controller;
use EngineCore\extension\repository\models\Module;
use EngineCore\extension\repository\models\Theme;
use EngineCore\modules\extension\controllers\ConfigController;
use EngineCore\modules\extension\controllers\ControllerController;
use EngineCore\modules\extension\controllers\ModuleController;
use EngineCore\modules\extension\controllers\ThemeController;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class Install
 */
class Install extends Dispatch
{

    /**
     * @var ConfigController|ModuleController|ControllerController|ThemeController
     */
    public $controller;

    /**
     * @param string $id 扩展名称
     * @param string $app 应用ID
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function run($id, $app = AppEnum::BACKEND)
    {
        /** @var Config|Module|Controller|Theme $model */
        $model = $this->controller->repository->findOne($id, $app);
        if (null === $model) {
            throw new NotFoundHttpException("{$id}: 扩展不存在");
        }
        // 如果已经安装则跳转到编辑页面
        if (!$model->getIsNewRecord()) {
            return $this->controller->redirect(['update', 'app' => $app, 'id' => $id]);
        }

        if ($model->load(Yii::$app->getRequest()->getBodyParams())) {
            if ($model->save()) {
                $this->response->success('安装成功！', ['index', 'app' => $app]);
            } else {
                $this->response->error('安装失败！');
            }
        }

        return $this->response->setAssign([
            'model' => $model,
            'id' => $id,
            'app' => $app,
            'runList' => Ec::$service->getExtension()->getRunModeList(),
            'dependList' => Ec::$service->getExtension()->getDependent()->getDefinitions()[$id]['extensionDependencies'][$app] ?? [],
            'composerList' => Ec::$service->getExtension()->getDependent()->getDefinitions()[$id]['composerDependencies'] ?? [],
        ])->render();
    }

}