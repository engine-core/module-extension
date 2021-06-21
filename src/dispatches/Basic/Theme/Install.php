<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 engine-core
 * @license BSD 3-Clause License
 */

namespace EngineCore\modules\extension\dispatches\Basic\Theme;

use EngineCore\dispatch\Dispatch;
use EngineCore\Ec;
use EngineCore\enums\AppEnum;
use EngineCore\enums\EnableEnum;
use EngineCore\extension\repository\models\Theme;
use EngineCore\modules\extension\controllers\ThemeController;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class Install
 */
class Install extends Dispatch
{

    /**
     * @var ThemeController
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
        /** @var Theme $model */
        $model = $this->controller->repository->findOne($id, $app);
        if (null === $model) {
            throw new NotFoundHttpException("{$id}: 扩展不存在");
        }
        // 如果已经安装则跳转到编辑页面
        if (!$model->getIsNewRecord()) {
            return $this->controller->redirect(['update', 'app' => $app, 'id' => $id]);
        }

        if ($model->load(Yii::$app->getRequest()->getBodyParams())) {
            $res = Ec::transaction(function () use ($model, $app) {
                $model->on($model::EVENT_BEFORE_INSERT, function ($event) use ($app) {
                    // 安装前把指定应用的主题状态禁用，确保每个应用中只有一个激活的主题
                    /** @var $event \yii\base\ModelEvent */
                    if (!$event->sender::updateAll(['status' => EnableEnum::DISABLE], [
                        'status' => EnableEnum::ENABLE,
                        'app' => $app,
                    ])) {
                        $event->isValid = false;
                    }
                });
                $model->status = EnableEnum::ENABLE;

                return $model->save();
            });
            if ($res) {
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
