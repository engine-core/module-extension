<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 E-Kevin
 * @license BSD 3-Clause License
 */

namespace EngineCore\modules\extension\dispatches\Basic\Theme;

use EngineCore\dispatch\Dispatch;
use EngineCore\Ec;
use EngineCore\enums\AppEnum;
use EngineCore\enums\EnableEnum;
use EngineCore\extension\repository\models\Theme;
use EngineCore\modules\extension\controllers\ThemeController;
use yii\web\NotFoundHttpException;

/**
 * Class Enable
 */
class Enable extends Dispatch
{
    
    /**
     * @var ThemeController
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
        /** @var Theme $model */
        $model = $this->controller->repository->findOne($id, $app);
        if (null === $model) {
            throw new NotFoundHttpException("{$id}: 扩展不存在");
        }
        $res = Ec::transaction(function () use ($model, $app) {
            $model->on($model::EVENT_BEFORE_UPDATE, function ($event) use ($app) {
                /** @var $event \yii\base\ModelEvent */
                if (!$event->sender::updateAll(['status' => EnableEnum::DISABLE], [
                    'status' => EnableEnum::ENABLE,
                    'app'    => $app,
                ])) {
                    $event->isValid = false;
                }
            });
            $model->status = EnableEnum::ENABLE;
            
            return $model->save(false, ['status']);
        });
        if ($res) {
            $this->response->success('操作成功！', ['index', 'app' => $app]);
        } else {
            $this->response->error('操作失败！', ['index', 'app' => $app]);
        }
    }
    
}