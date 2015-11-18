<?php

/**
 * ActiveForm is a widget that builds an interactive HTML form for one or multiple data models.
 * 
 * @author Ben Bi <ben@cciza.com>
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
class ActiveForm extends \yii\widgets\ActiveForm {

    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     * @see fieldConfig
     */
    public $fieldClass = 'cza\base\widgets\ActiveField';
    public $model;

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
