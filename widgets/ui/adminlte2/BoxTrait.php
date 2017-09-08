<?php

namespace cza\base\widgets\ui\adminlte2;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

trait BoxTrait {

    public $box = false;
    public static $boxConfig = [];
    private static $defaultConfig = [
        'type' => 'box-default', // box-info, box-primary, box-danger, box-success
        'noPadding' => false,
        'header' => [
            'title' => ' ',
            'class' => 'with-border',
            'tools' => '{collapse}{remove}',
            'icon' => false,
        ],
        'body' => [
            'class' => ''
        ],
        'footer' => '',
        'icons' => [
            'remove' => 'times', // fa icon name
            'collapse' => 'minus', // fa icon name
        ]
    ];

    public static function boxBegin($boxConfig = [], $boxOptions = []) {
        $str = "";

        self::$boxConfig = ArrayHelper::merge(self::$defaultConfig, $boxConfig);

        $options = ArrayHelper::merge(['class' => 'box ' . self::$boxConfig['type']], $boxOptions);
        $str .= Html::beginTag('div', $options);

        if (!empty(self::$boxConfig['header'])) {
            $header = self::$boxConfig['header'];
            $str .= Html::beginTag('div', ['class' => 'box-header ' . $header['class']]);

            if ($header['icon']) {
                $str .= $header['icon'];
            }

            if (!empty($header['title'])) {
                $str .= Html::tag('h3', Html::encode($header['title']), ['class' => 'box-title', 'data-widget'=>"collapse"]);
            }

            if (trim($header['tools'])) {
                $str .= Html::beginTag('div', ['class' => 'box-tools pull-right']);

                foreach (['collapse' => self::$boxConfig['icons']['collapse'], 'remove' => self::$boxConfig['icons']['remove']] as $tool => $icon) {
                    $header['tools'] = str_replace('{' . $tool . '}', self::boxTool($tool, $icon), $header['tools']);
                }

                $str .= $header['tools'];

                $str .= Html::endTag('div');
            }

            $str .= Html::endTag('div');
        }

        $class = 'box-body ';
        $class .= self::$boxConfig['body']['class'];
        if (self::$boxConfig['noPadding']) {
            $class .= ' no-padding';
        }
        $str .= Html::beginTag('div', ['class' => $class]);
        return $str;
    }

    static $footerUsed = false;

    public static function footer() {
        $str = "";

        self::$footerUsed = true;
        $str .= Html::endTag('div');
        $str .= Html::beginTag('div', ['class' => 'box-footer']);
        if (!empty(self::$boxConfig['footer'])) {
            $str .= self::$boxConfig['footer'];
            $str .= Html::endTag('div');
        }
        return $str;
    }

    public static function boxEnd() {
        $str = "";

        if (!self::$footerUsed) {
            $str .= Html::endTag('div');
            if (!empty(self::$boxConfig['footer'])) {
                $str .= Html::beginTag('div', ['class' => 'box-footer']);
                $str .= self::$boxConfig['footer'];
                $str .= Html::endTag('div');
            }
        } elseif (empty(self::$boxConfig['footer'])) {
            $str .= Html::endTag('div');
        }
        $str .= Html::endTag('div');
        return $str;
    }

    private static function boxTool($widget, $icon) {
        return Html::tag(
                        'a', Html::tag('i', null, ['class' => 'fa fa-' . $icon]), ['data-widget' => $widget, 'class' => 'btn btn-box-tool']
        );
    }

}
