<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 E-Kevin
 * @license BSD 3-Clause License
 */

namespace EngineCore\modules\extension\dispatches\Basic\Module;

use EngineCore\dispatch\Dispatch;
use EngineCore\Ec;
use EngineCore\enums\AppEnum;
use EngineCore\extension\repository\models\Module;
use EngineCore\modules\extension\controllers\ModuleController;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class Update
 */
class Update extends Dispatch
{
    
    /**
     * @var ModuleController
     */
    public $controller;
    
    /**
     * @param string $id  扩展名称
     * @param string $app 应用ID
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function run($id, $app = AppEnum::BACKEND)
    {
        /** @var Module $model */
        $model = $this->controller->repository->findOne($id, $app);
        if (null === $model) {
            throw new NotFoundHttpException("{$id}: 扩展不存在");
        }
        // 如果没有安装则跳转到安装页面
        if ($model->getIsNewRecord()) {
            return $this->controller->redirect(['install', 'app' => $app, 'id' => $id]);
        }
        $oldModuleId = $model->module_id;
        
        if ($model->load(Yii::$app->getRequest()->getBodyParams())) {
            if ($model->save()) {
                $this->response->success('更新成功！', $oldModuleId != $model->module_id ? ["/$model->module_id"] : null);
            } else {
                $this->response->error('更新失败！');
            }
        }
        
        return $this->response->setAssign([
            'model'        => $model,
            'id'           => $id,
            'app'          => $app,
            'runList'      => Ec::$service->getExtension()->getRunModeList(),
            'dependList'   => Ec::$service->getExtension()->getDependent()->getDefinitions()[$id]['extensionDependencies'][$app] ?? [],
            'composerList' => Ec::$service->getExtension()->getDependent()->getDefinitions()[$id]['composerDependencies'] ?? [],
        ])->render();
    }
    
}