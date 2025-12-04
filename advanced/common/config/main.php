<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'container' => [
        'definitions' => [
            // Evita que o Select2 tente usar Bootstrap 3
            'yii\bootstrap\BootstrapAsset' => [
                'class' => 'yii\bootstrap5\BootstrapAsset',
            ],
            'yii\bootstrap\BootstrapPluginAsset' => [
                'class' => 'yii\bootstrap5\BootstrapPluginAsset',
            ],
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
    ],
];
