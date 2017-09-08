<?php

namespace cza\base\widgets;

use Yii;
use ArrayAccess;
use ArrayObject;
use ArrayIterator;
use ReflectionClass;
use yii\base\InvalidCallException;
use yii\base\Model;

/**
 * Cza Widget
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class Widget extends \yii\base\Widget {

    const UI_BOOTSTRAP = 'bootstrap';
    const UI_JQUERYUI = 'jui';

    private $_data = [];
    protected $_controller = null;

    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var array the options for the underlying Bootstrap JS plugin.
     * Please refer to the corresponding Bootstrap plugin Web page for possible options.
     * For example, [this page](http://getbootstrap.com/javascript/#modals) shows
     * how to use the "Modal" plugin and the supported options (e.g. "remote").
     */
    public $clientOptions = [];

    /**
     * @var array the event handlers for the underlying Bootstrap JS plugin.
     * Please refer to the corresponding Bootstrap plugin Web page for possible events.
     * For example, [this page](http://getbootstrap.com/javascript/#modals) shows
     * how to use the "Modal" plugin and the supported events (e.g. "shown").
     */
    public $clientEvents = [];
    public $ui = SELF::UI_BOOTSTRAP;
    public $template;

    public function init() {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (empty($this->template)) {
            $this->template = $this->shortClassName();
        }
    }

    public function setController($controller) {
        $this->_controller = $controller;
    }

    /**
     * 
     * @return Controller
     */
    public function getController() {
        if (is_null($this->_controller)) {
            $this->_controller = Yii::$app->controller;
        }
        return $this->_controller;
    }

    public function run() {
        return $this->render($this->template);
    }

    /**
     * Returns the form name that this widget class should use.
     *
     * The form name is mainly used by [[\yii\widgets\ActiveForm]] to determine how to name
     * the input fields for the attributes in a model. If the form name is "A" and an attribute
     * name is "b", then the corresponding input name would be "A[b]". If the form name is
     * an empty string, then the input name would be "b".
     *
     * By default, this method returns the model class name (without the namespace part)
     * as the form name. You may override it when the model is used in different forms.
     *
     * @return string the form name of this model class.
     */
    public function shortClassName() {
        if (!isset($this->_data['shortClassName'])) {
            $this->_data['shortClassName'] = \Yii::$app->czaHelper->naming->shortClassName($this);
        }
        return $this->_data['shortClassName'];
    }

    /**
     * convert Upper case string into split sting, e.g.: CzaClassName => cza-class-name
     * @param type $name
     * @return type string
     */
    public function htmlId($name = '', $splitor = '-') {
        if (!isset($this->_data['htmlId'])) {
            $this->_data['htmlId'] = empty($name) ? strtolower(preg_replace("/(.)([A-Z])/", "$1{$splitor}$2", $this->shortClassName())) : strtolower(preg_replace("/(.)([A-Z])/", "$1{$splitor}$2", $name));
        }
        return $this->_data['htmlId'];
    }

    public function getTabWidget($config = []) {
        switch ($this->ui) {
            case Widget::UI_BOOTSTRAP:
                return \cza\base\widgets\common\form\Tabs::widget($config);
                break;
            case Widget::UI_JQUERYUI:
                return \yii\jui\Tabs::widget($config);
                break;
            default:
                return \cza\base\widgets\common\form\Tabs::widget($config);
                break;
        }
    }

    /**
     * Registers JS event handlers that are listed in [[clientEvents]].
     * @since 2.0.2
     */
    protected function registerClientEvents() {
        if (!empty($this->clientEvents)) {
            $id = $this->options['id'];
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('#{$id}').off('{$event}').on('{$event}', {$handler});";
            }
            $this->getView()->registerJs(implode("\n", $js));
        }
    }

}
