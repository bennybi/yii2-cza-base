<?php

$vendorDir = dirname(dirname(__DIR__));

return array_merge(
        require($vendorDir . '/yiisoft/extensions.php'), [
            'cza' => [
                'name' => 'cza',
                'version' => '2.0.0',
                'alias' => [
                    '@cza' => '@vendor/cza',
                    '@cza/base' => '@vendor/cza/yii2-cza-base',
                    '@cza/base/modules' => '@vendor/cza/yii2-cza-base/modules',
                    '@uploads' => '@web/uploads',
                    '@themes' => '@app/themes',
                ],
            ],
        ]
);
