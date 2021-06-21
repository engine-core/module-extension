<?php
/**
 * @link https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 engine-core
 * @license BSD 3-Clause License
 */

namespace EngineCore\modules\extension\controllers;

use EngineCore\Ec;
use EngineCore\modules\extension\Module;
use EngineCore\services\extension\ControllerRepository;
use EngineCore\web\Controller;
use yii\filters\VerbFilter;

class ControllerController extends Controller
{

    /**
     * @var Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    protected $defaultDispatchMap = [
        'index' => [
            'class' => 'EngineCore\modules\extension\dispatches\Basic\Index',
        ],
        'update' => [
            'class' => 'EngineCore\modules\extension\dispatches\Basic\Update',
            'response' => [
                'viewFile' => '@EngineCore/modules/extension/views/update.php'
            ]
        ],
        'install' => [
            'class' => 'EngineCore\modules\extension\dispatches\Basic\Install',
            'response' => [
                'viewFile' => '@EngineCore/modules/extension/views/install.php'
            ]
        ],
        'uninstall' => [
            'class' => 'EngineCore\modules\extension\dispatches\Basic\Uninstall',
        ],
    ];

    /**
     * @var ControllerRepository
     */
    public $repository;

    /**
     * @inheritdoc
     */
    public function __construct($id, Module $module, array $config = [])
    {
        $this->repository = Ec::$service->getExtension()->getControllerRepository();
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'uninstall' => ['post'],
                ],
            ],
        ];
    }

}