<?php
/**
 * @link      https://github.com/EngineCore/module-extension
 * @copyright Copyright (c) 2020 E-Kevin
 * @license   BSD 3-Clause License
 */

namespace EngineCore\modules\extension\controllers;

use EngineCore\Ec;
use EngineCore\modules\extension\Module;
use EngineCore\services\extension\ModularityRepository;
use EngineCore\web\Controller;
use yii\filters\VerbFilter;

class ModuleController extends Controller
{
    
    /**
     * @var Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    protected $defaultDispatchMap = [
        'index'           => [
            'class' => 'EngineCore\modules\extension\dispatches\Basic\Index',
        ],
        'update'          => [
            'response' => [
                'viewFile' => '@EngineCore/modules/extension/views/update.php',
            ],
        ],
        'install'         => [
            'class'    => 'EngineCore\modules\extension\dispatches\Basic\Install',
            'response' => [
                'viewFile' => '@EngineCore/modules/extension/views/install.php',
            ],
        ],
        'uninstall'       => [
            'class' => 'EngineCore\modules\extension\dispatches\Basic\Uninstall',
        ],
    ];
    
    /**
     * @var ModularityRepository
     */
    public $repository;
    
    /**
     * @inheritdoc
     */
    public function __construct($id, Module $module, array $config = [])
    {
        $this->repository = Ec::$service->getExtension()->getModularityRepository();
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