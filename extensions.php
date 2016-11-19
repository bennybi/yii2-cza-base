<?php

$vendorDir = dirname(dirname(__DIR__));

return array_merge(
        require($vendorDir . '/yiisoft/extensions.php'), [
            'cza/yii2-base' => [
                'name' => 'cza/yii2-base',
                'version' => '0.0.1.0',
                'alias' => [
                    '@cza' => '@vendor/cza',
                    '@cza/base' => '@vendor/cza/yii2-base',
                    '@cza/base/modules' => '@vendor/cza/yii2-base/modules',
                    '@uploads' => '@app/web/uploads',
                    '@themes' => '@app/themes',
                ],
                'bootstrap' => 'cza\\base\\Bootstrap',
            ],
            'cza/yii2-gii' => [
                'name' => 'cza/yii2-gii',
                'version' => '0.0.1.0',
                'alias' => [
                    '@cza/gii' => '@vendor/cza/yii2-gii',
                ],
            ],
        ]
);
