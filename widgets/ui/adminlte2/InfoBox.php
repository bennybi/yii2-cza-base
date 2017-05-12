<?php

namespace cza\base\widgets\ui\adminlte2;

use Yii;

class InfoBox extends Box {

    const TYPE_INFO = 'info';
    const TYPE_DANGER = 'danger';
    const TYPE_WARNING = 'warning';
    const TYPE_SUCCESS = 'success';

    public $messages = [];
    public $defaultMessageType = self::TYPE_INFO;
    public $wrapperConfig = [];
    public $blockTpl = '<div class="{class}"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4>{icon}{title}</h4>{content}</div>';
    public $blockTypes = [];
    public $withWrapper = true; // with outside wrapper or not

    public function init() {
        parent::init();

        $defaults = [
            self::TYPE_INFO => ['{class}' => 'alert alert-info alert-dismissible', '{icon}' => '<i class="icon fa fa-info-circle"></i>', '{title}' => Yii::t('cza', 'Tips'), '{content}' => ''],
            self::TYPE_DANGER => ['{class}' => 'alert alert-danger alert-dismissible', '{icon}' => '<i class="icon fa fa-ban"></i>', '{title}' => Yii::t('cza', 'Alert'), '{content}' => ''],
            self::TYPE_WARNING => ['{class}' => 'alert alert-warning alert-dismissible', '{icon}' => '<i class="icon fa fa-warning"></i>', '{title}' => Yii::t('cza', 'Warning'), '{content}' => ''],
            self::TYPE_SUCCESS => ['{class}' => 'alert alert-success alert-dismissible', '{icon}' => '<i class="icon fa fa-info"></i>', '{title}' => Yii::t('cza', 'Alert'), '{content}' => ''],
        ];
        $this->blockTypes = array_replace_recursive($defaults, $this->blockTypes);
    }

    public function run() {
        $content = $this->getMessageBlocks();
        if ($this->withWrapper) {
            echo self::boxBegin($this->getWrapperConfig());
            echo $content;
            echo self::boxEnd();
        } else {
            echo $content;
        }
    }

    public function getWrapperConfig() {
        $defaults = [
            'type' => 'box-info', // box-info, box-primary, box-danger, box-success
            'noPadding' => false,
            'header' => [
                'title' => Yii::t('cza', 'Tips'),
                'class' => 'with-border',
                'tools' => '{collapse}{remove}',
                'icon' => '<i class="fa fa-warning"></i>',
            ],
            'body' => [
                'class' => ''
            ],
            'footer' => '',
        ];
        $this->wrapperConfig = array_replace_recursive($defaults, $this->wrapperConfig);
        return $this->wrapperConfig;
    }

    public function getMessageBlocks() {
        $content = ob_get_clean();
        if (!empty($content)) {
            return $content;
        }
        foreach ($this->messages as $val) {
            $message = is_array($val) ? $val[0] : $val;
            if (is_string($message)) {
                $content.= $this->block($message);
            } elseif (is_array($message) && isset($message['content']) && isset($message['type']) && isset($message['options'])) {
                $content.= $this->block($message['content'], $message['type'], $message['options']);
            } elseif (is_array($message) && isset($message['content']) && isset($message['type'])) {
                $content.= $this->block($message['content'], $message['type']);
            }
        }
        return $content;
    }

    public function block($message, $type = '', $options = []) {
        if (!empty($options)) {
            $blockOptions = $options;
        } else {
            if (empty($type)) {
                $type = $this->defaultMessageType;
            }
            $blockOptions = $this->blockTypes[$type];
        }
        $blockOptions['{content}'] = $message;
        return strtr($this->blockTpl, $blockOptions);
    }

}
