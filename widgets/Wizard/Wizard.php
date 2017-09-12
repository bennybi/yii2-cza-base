<?php

namespace cza\base\widgets\Wizard;

use Yii;
use cza\base\widgets\Widget;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use cza\base\models\statics\OperationEvent;
use yii\helpers\Url;
use yii\web\View;
use cza\base\models\statics\OperationResult;
use yii\helpers\Json;

/**
 *  Wizard Widget
 * @author Ben Bi <bennybi@qq.com>
 * @license
 */
class Wizard extends Widget {

    const THEME_DEFAULT = 'default';
    const THEME_ARROWS = 'arrows';
    const THEME_CIRCLES = 'circles';
    const THEME_DOTS = 'dots';

    public $model = null;
    public $theme = 'default';
    public $pluginOptions = [];  // for wizard plugins

    /**
     * array[
     *   'title'=> '',
     *   'summary'=> '',
     *   'content'=> '',
     *   'active'=> false,
     * ]
     * @var type 
     */
    public $steps = [];

    public function init() {
        parent::init();
        $defaults = [];
        $this->options = ArrayHelper::merge($defaults, $this->options);

        if (empty($this->model)) {
            throw new \yii\base\Exception('model parameter is required!');
        }

        $wizardAsset = WizardAsset::register($this->view);
        $wizardAsset->applyTheme($this->theme);

        $defaults = [
            'selected' => 0,
            'theme' => $this->theme,
            'transitionEffect' => 'fade',
            'showStepURLhash' => false,
            'lang' => [
                'next' => Yii::t('app.c2', 'Next Step'),
                'previous' => Yii::t('app.c2', 'Prev Step'),
            ],
            'toolbarSettings' => [
                'toolbarPosition' => 'bottom', // none, top, bottom, both
                'toolbarButtonPosition' => 'right', // left, right
                'showNextButton' => true, // show/hide a Next button
                'showPreviousButton' => true, // show/hide a Previous button
            ],
            'anchorSettings' => [
                'anchorClickable' => true, // Enable/Disable anchor navigation
                'enableAllAnchors' => false, // Activates all anchors clickable all times
                'markDoneStep' => true, // add done css
                'enableAnchorOnDoneStep' => true // Enable/Disable the done steps navigation
            ],
            'contentURL' => null, // content url, Enables Ajax content loading. can set as data data-content-url on anchor
            'disabledSteps' => [], // Array Steps disabled
            'errorSteps' => [], // Highlight step with errors
            'transitionEffect' => 'fade', // Effect on navigation, none/slide/fade
            'transitionSpeed' => '400'
        ];
        $this->pluginOptions = ArrayHelper::merge($defaults, $this->pluginOptions);

        ob_start();
        ob_implicit_flush(false);
    }

    public function run() {
        $formId = $this->model->getBaseFormName();
        $this->clientEvents = [
            "leaveStep" => "function(e, anchorObject, stepNumber, stepDirection) {
                console.log('leaveStep:' + stepNumber + ':' + stepDirection);
                if (stepDirection === 'forward' && stepNumber === 0 && wizardAjaxInvoke ==false) {
                    wizardAjaxInvoke = true;
                    var vForm = $('#{$formId}');
                    $.ajax({
                      method: 'POST',
                      url: vForm.attr('action'),
                      data: vForm.serialize(),
                      success: function(data) {
                                console.log(data._meta);
                                if(data._meta.result == '" . OperationResult::ERROR . "'){
                                   $('#{$this->model->getBaseFormName()}').replaceWith(data._data.content);
                                }
                                else{
                                   $('#{$this->model->getBaseFormName()}').replaceWith(data._data.content);
                                   $('#{$this->options['id']}').smartWizard('next');
                                }
                      },
                      error :function(data){alert(data._meta.message);}
                    });
                   return false;
                }
            } ",
            "showStep" => "function(e, anchorObject, stepNumber, stepDirection) {
                console.log('showStep');
            } ",
            "beginReset" => "function(e) {
                console.log('beginReset');
            } ",
            "endReset" => "function(e) {
                console.log('endReset');
            } ",
            "themeChanged" => "function(e) {
                console.log('themeChanged');
            } ",
        ];

        $this->registerJs();
        $this->registerClientEvents();
        $content = ob_get_clean();
        return $this->render($this->template, ['content' => $content]);
    }

    public function registerJs() {
        $js = "";
        $js .= "var wizardAjaxInvoke = false;";
        $js .= "$('#{$this->options['id']}').smartWizard(" . Json::encode($this->pluginOptions) . ");\n";
       $this->getView()->registerJs($js, View::POS_READY);
    }

}
