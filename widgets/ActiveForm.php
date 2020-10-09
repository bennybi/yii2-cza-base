<?php

/**
 * ActiveForm is a widget that builds an interactive HTML form for one or multiple data models.
 * 
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\widgets;

use Yii;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * ActiveForm is a widget that builds an interactive HTML form for one or multiple data models.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ActiveForm extends \yii\bootstrap\ActiveForm {

    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     * @see fieldConfig
     */
    public $fieldClass = 'cza\base\widgets\ActiveField';
    public $model;
    
    public function juiField($model, $attribute, $options = [])
    {
        $this->fieldClass = 'cza\base\widgets\ui\jui\ActiveField';
        return parent::field($model, $attribute, $options);
    }
    
    /**
     * Generates a form field by multiple model attributes.
     * 
     * @param Model $model the data model.
     * @param array $attribute the attribute name or expression. See [[Html::getAttributeName()]] for the format
     * about attribute expression.
     * @param array $options the additional configurations for the field object. These are properties of [[ActiveField]]
     * or a subclass, depending on the value of [[fieldClass]].
     * @return ActiveField the created ActiveField object.
     * @see fieldConfig
     */
    public function fields($model, $attributes, $options = [])
    {
        $config = $this->fieldConfig;
        if ($config instanceof \Closure) {
            $config = call_user_func($config, $model, $attributes);
        }
        if (!isset($config['class'])) {
            $config['class'] = $this->fieldClass;
        }
        return Yii::createObject(ArrayHelper::merge($config, $options, [
            'model' => $model,
            'attributes' => $attributes,
            'enableLabel' => false,
            'form' => $this,
        ]));
    }

    public function getAjaxJs() {
        $js = "jQuery('form#{$this->options['id']}').on('beforeSubmit', function(e) {
                   var vForm = jQuery(this);
                   jQuery.ajax({
                            url: vForm.attr('action'),
                            type: 'post',
                            data: vForm.serialize(),
                            success: function(data) {
                                jQuery.msgGrowl ({
                                        type: data._meta.type, 
                                        title: '" . Yii::t('cza', 'Tips') . "',
                                        text: data._meta.message
                                });
                            },
                            error :function(data){alert(data._meta.message);}
                    });
                    return false;
                });";
        return $js;
    }

}
