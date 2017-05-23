<?php

namespace cza\base\components\actions\backend;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\HttpException;
use cza\base\behaviors\CmsMediaBehavior;
use cza\base\models\statics\ResponseDatum;

/**
 * Descriptions
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class ProfileSaveAction extends \yii\rest\Action {

    public $entityIdAttribute = 'entity_id';
    public $view = 'edit';

    public function run() {

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $params = Yii::$app->request->post();
//Yii::info($params);
//Yii::info($_FILES);
        if (isset($params[$this->entityIdAttribute])) {
            $model = $this->controller->retrieveModel($params[$this->entityIdAttribute]);
        } else {
            throw new HttpException(404, Yii::t('cza', 'Associated entity not found!'));
        }

        $profileModel = $model->profile;

        // handle cms media fields
        if ($this->controller instanceof \cza\base\components\controllers\backend\CmsController) {
            $cmsFields = $this->controller->getCmsFields();
            $profileModel->attachBehavior('CmsMediaBehavior', [
                'class' => CmsMediaBehavior::className(),
                'fields' => $cmsFields,
                'options' => ['isTranslation' => true],
            ]);
        }

        if ($profileModel->load($params)) {
            if ($profileModel->save()) {
                Yii::$app->session->setFlash($profileModel->getMessageName(), [Yii::t('app.c2', 'Saved successful.')]);
            } else {
                Yii::$app->session->setFlash($profileModel->getMessageName(), $profileModel->errors);
            }
        }
        return (Yii::$app->request->isAjax) ? $this->controller->renderAjax($this->view, [ 'model' => $model,]) : $this->controller->render($this->view, [ 'model' => $model,]);
    }

}
