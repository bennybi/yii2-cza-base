<?php

namespace cza\base\components\actions\common;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\HttpException;
use cza\base\behaviors\CmsMediaBehavior;

/**
 * Descriptions
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class EnvSettingsAction extends \yii\base\Action {

    /**
     * @var callable a PHP callable that will be called when running an action to determine
     * if the current user has the permission to execute the action. If not set, the access
     * check will not be performed. The signature of the callable should be as follows,
     *
     * ```php
     * function ($action, $model = null) {
     *     // $model is the requested model instance.
     *     // If null, it means no specific model (e.g. IndexAction)
     * }
     * ```
     */
    public $checkAccess;

    public function run() {

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
//        $params = Yii::$app->request->getParams();
        $params = $_REQUEST;

        if (!isset($params['language'])) {
            throw new HttpException(404, Yii::t('cza', 'Language ({s1}) Not Found!', ['s1' => $params['language']]));
        }

        $enabledLangs = array_keys(Yii::$app->czaHelper->getEnabledLangs());
        if(!in_array($params['language'], $enabledLangs)){
            throw new HttpException(404, Yii::t('cza', '({s1}) was not found in enabled languages!', ['s1' => $params['language']]));
        }
        
        Yii::$app->session->set('user.language',$params['language']);
        $this->controller->redirect(isset($params['returnUrl']) ? $params['returnUrl'] : Yii::$app->getRequest()->getReferrer());
    }

}
