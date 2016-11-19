<?php
namespace cza\base\vendor\widgets\imperavi;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
/**
 * Warpper of \yii\imperavi\Widget
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class Widget extends \yii\imperavi\Widget
{
    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        if (!isset($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = $this->getId();
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $this->selector = '#' . $this->htmlOptions['id'];
        if (!is_null($this->model)) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->htmlOptions);
        } else {
            echo Html::textarea($this->attribute, $this->value, $this->htmlOptions);
        }

        ImperaviRedactorAsset::register($this->getView())->setLang($this->options['lang']);
        $this->registerClientScript();
    }

    /**
     * Registers Imperavi Redactor JS
     */
    protected function registerClientScript()
    {
        $view = $this->getView();


        /*
         * Language fix
         * @author <https://github.com/sim2github>
         */
        if (!isset($this->options['lang']) || empty($this->options['lang'])) {
            $this->options['lang'] = str_replace("-", "_", strtolower(Yii::$app->language));
        }

        // Insert plugins in options
        if (!empty($this->plugins)) {
            $this->options['plugins'] = $this->plugins;

            foreach ($this->options['plugins'] as $plugin) {
                $this->registerPlugin($plugin);
            }
        }

        $options = empty($this->options) ? '' : Json::encode($this->options);
        $js = "jQuery('" . $this->selector . "').redactor($options);";
        $view->registerJs($js);
    }

}

