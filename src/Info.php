<?php
/**
 * @link      https://github.com/EngineCore/module-extension
 * @copyright Copyright (c) 2020 E-Kevin
 * @license   BSD 3-Clause License
 */

declare(strict_types=1);

namespace EngineCore\modules\extension;

use EngineCore\Ec;
use EngineCore\enums\AppEnum;
use EngineCore\extension\installation\ExtensionInterface;
use EngineCore\extension\repository\info\ModularityInfo;
use EngineCore\extension\repository\models\Config;
use EngineCore\extension\repository\models\Controller;
use EngineCore\extension\repository\models\Module;
use EngineCore\extension\repository\models\Theme;
use EngineCore\helpers\ArrayHelper;
use Yii;
use yii\web\Application;

class Info extends ModularityInfo implements ExtensionInterface
{
    
    const EXT_RAND_CODE = 'CBuZ9M_';
    
    protected
        $id = 'extension',
        $name = '扩展管理',
        $category = self::CATEGORY_EXTENSION;
    
    /**
     * @inheritdoc
     */
    public function getMenus(): array
    {
        return [
            AppEnum::BACKEND => [
                // 扩展中心
                'extension' => [
                    'label' => Yii::t('ec/modules/extension', 'Extension Center'),
                    'icon'  => 'cube',
                    'visible'  => true,
                    'items' => [
                        // 扩展管理
                        'manage'   => [
                            'label' => Yii::t('ec/modules/extension', 'Extension Manage'),
                            'icon'  => 'cubes',
                            'visible'  => true,
                            'items' => [
                                // 模块扩展管理
                                'module'     => [
                                    'label'       => Yii::t('ec/modules/extension', 'Module Manage'),
                                    'url'         => "/{$this->id}/module/index",
                                    'description' => Yii::t('ec/modules/extension', 'Module Manage'),
                                    'visible'        => true,
                                    'items'       => [
                                        ['label' => Yii::t('ec/modules/extension', 'List'), 'url' => "/{$this->id}/module/index"],
                                        ['label' => Yii::t('ec/modules/extension', 'Uninstall'), 'url' => "/{$this->id}/module/uninstall"],
                                        ['label' => Yii::t('ec/modules/extension', 'Install'), 'url' => "/{$this->id}/module/install"],
                                        ['label' => Yii::t('ec/modules/extension', 'Manage'), 'url' => "/{$this->id}/module/update"],
                                    ],
                                ],
                                // 控制器扩展管理
                                'controller' => [
                                    'label'       => Yii::t('ec/modules/extension', 'Controller Manage'),
                                    'url'         => "/{$this->id}/controller/index",
                                    'description' => Yii::t('ec/modules/extension', 'Controller Manage'),
                                    'visible'        => true,
                                    'items'       => [
                                        ['label' => Yii::t('ec/modules/extension', 'List'), 'url' => "/{$this->id}/controller/index"],
                                        ['label' => Yii::t('ec/modules/extension', 'Uninstall'), 'url' => "/{$this->id}/controller/uninstall"],
                                        ['label' => Yii::t('ec/modules/extension', 'Install'), 'url' => "/{$this->id}/controller/install"],
                                        ['label' => Yii::t('ec/modules/extension', 'Manage'), 'url' => "/{$this->id}/controller/update"],
                                    ],
                                ],
                                // 主题扩展管理
                                'theme'      => [
                                    'label'       => Yii::t('ec/modules/extension', 'Theme Manage'),
                                    'url'         => "/{$this->id}/theme/index",
                                    'description' => Yii::t('ec/modules/extension', 'Theme Manage'),
                                    'visible'        => true,
                                    'items'       => [
                                        ['label' => Yii::t('ec/modules/extension', 'List'), 'url' => "/{$this->id}/theme/index"],
                                        ['label' => Yii::t('ec/modules/extension', 'Uninstall'), 'url' => "/{$this->id}/theme/uninstall"],
                                        ['label' => Yii::t('ec/modules/extension', 'Install'), 'url' => "/{$this->id}/theme/install"],
                                        ['label' => Yii::t('ec/modules/extension', 'Manage'), 'url' => "/{$this->id}/theme/update"],
                                        ['label' => Yii::t('ec/modules/extension', 'Enable'), 'url' => "/{$this->id}/theme/enable"],
                                    ],
                                ],
                                // 配置扩展管理
                                'config'     => [
                                    'label'       => Yii::t('ec/modules/extension', 'Config Manage'),
                                    'url'         => "/{$this->id}/config/index",
                                    'description' => Yii::t('ec/modules/extension', 'Config Manage'),
                                    'visible'        => true,
                                    'items'       => [
                                        ['label' => Yii::t('ec/modules/extension', 'List'), 'url' => "/{$this->id}/config/index"],
                                        ['label' => Yii::t('ec/modules/extension', 'Uninstall'), 'url' => "/{$this->id}/config/uninstall"],
                                        ['label' => Yii::t('ec/modules/extension', 'Install'), 'url' => "/{$this->id}/config/install"],
                                        ['label' => Yii::t('ec/modules/extension', 'Manage'), 'url' => "/{$this->id}/config/update"],
                                    ],
                                ],
                            ],
                        ],
                        // 扩展功能
                        'function' => [
                            'label' => Yii::t('ec/modules/extension', 'Extension Function'),
                            'icon'  => 'cogs',
                            'visible'  => true,
                            'items' => [
                                'sync'        => [
                                    'label'       => Yii::t('ec/modules/extension', 'Synchronization'),
                                    'alias'       => Yii::t('ec/modules/extension', 'Sync Menu'),
                                    'url'         => "/{$this->id}/functions/sync-menu",
                                    'description' => Yii::t('ec/modules/extension', 'Sync extension menu'),
                                    'visible'        => true,
                                    'config'      => [
                                        'linkOptions' => [
                                            'data-method' => 'post',
                                            'data-pjax'   => 1,
                                        ],
                                    ],
                                ],
                                'clear-cache' => [
                                    'label'       =>  Yii::t('ec/modules/extension', 'Clear'),
                                    'alias'       =>  Yii::t('ec/modules/extension', 'Clear Cache'),
                                    'url'         => "/{$this->id}/functions/clear-cache",
                                    'description' =>  Yii::t('ec/modules/extension', 'Clear extension cache'),
                                    'visible'        => true,
                                    'config'      => [
                                        'linkOptions' => [
                                            'data-method' => 'post',
                                            'data-pjax'   => 1,
                                        ],
                                    ],
                                ],
                                'refresh-config' => [
                                    'label'       =>  Yii::t('ec/modules/extension', 'Refresh'),
                                    'alias'       =>  Yii::t('ec/modules/extension', 'Refresh Configuration'),
                                    'url'         => "/{$this->id}/functions/refresh-config",
                                    'description' =>  Yii::t('ec/modules/extension', 'Refresh extension configuration'),
                                    'visible'        => true,
                                    'config'      => [
                                        'linkOptions' => [
                                            'data-method' => 'post',
                                            'data-pjax'   => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        $common = [
            'container'  => [
                'definitions' => [
                    'EngineCore\services\ServiceLocator' => [
                        // 链式配置，让数据看起来简洁美观
                        ':locators.extension.services' => [
                            ':controller.model.class' => 'EngineCore\extension\repository\models\Controller',
                            ':modularity.model.class' => 'EngineCore\extension\repository\models\Module',
                            ':theme.model.class'      => 'EngineCore\extension\repository\models\Theme',
                            ':config.model.class'     => 'EngineCore\extension\repository\models\Config',
                        ],
                    ],
                ],
            ],
            'components' => [
                'i18n' => [
                    'translations' => [
                        'ec/modules/extension' => [
                            'class'          => 'yii\\i18n\\PhpMessageSource',
                            'sourceLanguage' => 'en-US',
                            'basePath'       => '@EngineCore/modules/extension/messages',
                            'fileMap'        => [
                                'ec/modules/extension' => 'app.php',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $backend = [
            'modules' => [
                $this->getId() => [
                    'class' => 'EngineCore\modules\extension\Module',
                ],
                'gridview'     => [
                    'class' => '\kartik\grid\Module'
                    // enter optional module parameters below - only if you need to
                    // use your own export download action or custom translation
                    // message source
                    // 'downloadAction' => 'gridview/export/download',
                    // 'i18n' => []
                ],
            ],
        ];
        $console = [];
        
        return [
            AppEnum::BACKEND => ArrayHelper::merge($common, $backend),
            AppEnum::CONSOLE => ArrayHelper::merge($common, $console),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function getMigrationTable(): string
    {
        return '{{%' . static::EXT_RAND_CODE . 'migration}}';
    }
    
    /**
     * @inheritdoc
     */
    public function getMigrationPath(): array
    {
        return ['@EngineCore/extension/repository/migrations'];
    }
    
    /**
     * {@inheritdoc}
     */
    public function setRepositoryModel()
    {
        $extension = Ec::$service->getExtension();
        if (!$extension->getControllerRepository()->hasModel()) {
            $extension->getControllerRepository()->setModel(Controller::class);
        }
        if (!$extension->getModularityRepository()->hasModel()) {
            $extension->getModularityRepository()->setModel(Module::class);
        }
        if (!$extension->getThemeRepository()->hasModel()) {
            $extension->getThemeRepository()->setModel(Theme::class);
        }
        if (!$extension->getConfigRepository()->hasModel()) {
            $extension->getConfigRepository()->setModel(Config::class);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function initialize(): bool
    {
        return Ec::$service->getMigration()->table($this->getMigrationTable())
                           ->interactive(false)
                           ->path($this->getMigrationPath())
                           ->compact(Yii::$app instanceof Application)
                           ->up(0);
    }
    
}