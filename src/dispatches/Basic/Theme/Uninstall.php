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
use EngineCore\extension\setting\SettingProviderInterface;
use EngineCore\modules\extension\controllers\ThemeController;
use yii\web\NotFoundHttpException;

/**
 * Class Uninstall
 */
class Uninstall extends Dispatch
{

    /**
     * @var ThemeController
     */
    public $controller;

    /**
     * @param string $id 扩展名称
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
            $defaultTheme = Ec::$service->getSystem()->getSetting()->get(SettingProviderInterface::DEFAULT_THEME);
            // 当前激活的主题不是默认主题
            if ($this->controller->repository->getModel()->getActiveTheme($app) !== $defaultTheme) {
                $model->on($model::EVENT_BEFORE_DELETE, function ($event) use ($app, $defaultTheme) {
                    // 主题扩展卸载后激活默认主题
                    /** @var $event \yii\base\ModelEvent */
                    if (!$event->sender::updateAll(['status' => EnableEnum::ENABLE], [
                        'unique_name' => $defaultTheme,
                        'app' => $app,
                    ])) {
                        $event->isValid = false;
                    }
                });
            }

            return $model->delete();
        });
        if ($res) {
            $this->response->success('卸载成功！', ['index', 'app' => $app]);
        } else {
            $this->response->error('卸载失败！', ['index', 'app' => $app]);
        }
    }

}