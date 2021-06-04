<?php
/**
 * @link      https://github.com/engine-core/module-extension
 * @copyright Copyright (c) 2021 E-Kevin
 * @license   BSD 3-Clause License
 */

namespace EngineCore\modules\extension\controllers;

use EngineCore\modules\extension\Module;
use EngineCore\web\Controller;
use yii\filters\VerbFilter;

class FunctionsController extends Controller
{
    
    /**
     * @var Module
     */
    public $module;
    
    protected $defaultDispatchMap = ['sync-menu', 'clear-cache', 'refresh-config'];
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'sync-menu'   => ['post'],
                    'clear-cache' => ['post'],
                    'refresh-config' => ['post'],
                ],
            ],
        ];
    }
    
}