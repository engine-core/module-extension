<?php
/**
 * @link      https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 E-Kevin
 * @license   BSD 3-Clause License
 */

namespace EngineCore\modules\extension\controllers;

use EngineCore\Ec;
use EngineCore\modules\extension\Module;
use EngineCore\services\extension\ConfigRepository;
use EngineCore\web\Controller;
use yii\filters\VerbFilter;

class ConfigController extends Controller
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
            'response' => [
                'viewFile' => '@EngineCore/modules/extension/views/index.php'
            ]
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
     * @var ConfigRepository
     */
    public $repository;
    
    /**
     * @inheritdoc
     */
    public function __construct($id, Module $module, array $config = [])
    {
        $this->repository = Ec::$service->getExtension()->getConfigRepository();
        parent::__construct($id, $module, $config);
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'uninstall' => ['post'],
                ],
            ],
        ];
    }
    
}