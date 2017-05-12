<?php

namespace cza\base\widgets\ui\adminlte2;

use yii\base\Widget;

class Box extends Widget {

    use BoxTrait;

    public $options = [];
    public $config = [];

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        ob_start();
        ob_implicit_flush(false);
    }

    public function run() {
        $content = ob_get_clean();
        echo self::boxBegin($this->config, $this->options);
        echo $content;
        echo self::boxEnd();
    }

}
